<?php

set_time_limit (0);
$settings_dir = "../settings";
include "$settings_dir/database.php";

try {
	$stmt = $pdo->query("SHOW TABLES");
	$tables = $stmt->fetchAll(PDO::FETCH_NUM);
} catch (PDOException $e) {
	echo "DB Error, could not list tables\n";
	die();
}

$old = 0;
foreach ($tables as $row) {
	if ($row[0] == $mysql_table_prefix."link_keyword") {
		$old = 1;
		break;
	}
}

if ($old == 0) {
	echo "The database seems to have been upgraded.\n";
	die();
}
$error = 0;
for ($i=0;$i<=15; $i++) {
	$char = dechex($i);
	try {
		$pdo->exec("create table `".$mysql_table_prefix."link_keyword$char` (
			link_id int not null,
			keyword_id int not null,
			weight int(3),
			domain int(4),
			key linkid(link_id),
			key keyid(keyword_id))");
	} catch (PDOException $e) {
		print "Error: ";
		print htmlspecialchars($e->getMessage());
		print "<br>\n";
		$error++;
	}
}

try {
	$pdo->exec("create table `".$mysql_table_prefix."domains` (
		domain_id int auto_increment primary key not null,	
		domain varchar(255))");
} catch (PDOException $e) {
	print "Error: ";
	print htmlspecialchars($e->getMessage());
	print "<br>\n";
	$error++;
}


try {
	$pdo->exec("alter table `".$mysql_table_prefix."links` add key md5key(md5sum(16))");
} catch (PDOException $e) {
	print "Error: ";
	print htmlspecialchars($e->getMessage());
	print "<br>\n";
	$error++;
}

try {
	$pdo->exec("alter table `".$mysql_table_prefix."query_log` add key querykey(query)");
} catch (PDOException $e) {
	print "Error: ";
	print htmlspecialchars($e->getMessage());
	print "<br>\n";
	$error++;
}

if ($error >0) {
	print "<b>Creating tables failed. Consult the above error messages.</b>";
	die();
} 


$query = "select link_id, keyword_id, weight from ".$mysql_table_prefix."link_keyword";
try {
	$stmt = $pdo->query($query);
	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	foreach ($results as $row) {
		$link=$row['link_id'];
		$word_id=$row['keyword_id'];
		$weight=$row['weight'];

		$query = "select keyword from ".$mysql_table_prefix."keywords where keyword_id = ?";

		try {
			$stmt2 = $pdo->prepare($query);
			$stmt2->execute([$word_id]);
			$row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
			
			if ($row2) {
				$word = $row2['keyword'];
				$wordmd5 = substr(md5($word), 0, 1);
				$query = "insert into ".$mysql_table_prefix."link_keyword$wordmd5 (link_id, keyword_id, weight) values(?, ?, ?)";
				$stmt3 = $pdo->prepare($query);
				$stmt3->execute([$link, $word_id, $weight]);
			}
		} catch (PDOException $e) {
			echo htmlspecialchars($e->getMessage());
		}
	}
} catch (PDOException $e) {
	echo htmlspecialchars($e->getMessage());
}


$query = "select link_id, url from ".$mysql_table_prefix."links";
try {
	$stmt = $pdo->query($query);
	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$found_domains = array();
	foreach ($results as $row) {
		$link_id=$row['link_id'];
		$url=$row['url'];
		$parsed = parse_url($url);
		$domain = $parsed['host'];
		
		if (isset($found_domains[$domain]) && $found_domains[$domain]!="") {
			$domain_id = $found_domains[$domain];
		} else {
			$query = "insert into ".$mysql_table_prefix."domains (domain) values(?)"; 
			try {
				$stmt2 = $pdo->prepare($query);
				$stmt2->execute([$domain]);
				$domain_id = $pdo->lastInsertId();
				$found_domains[$domain] = $domain_id;
			} catch (PDOException $e) {
				echo htmlspecialchars($e->getMessage());
				continue;
			}
		}
		

		for ($i=0;$i<=15; $i++) {
			$char = dechex($i);
			try {
				$query = "update ".$mysql_table_prefix."link_keyword$char set domain=? where link_id = ?";
				$stmt3 = $pdo->prepare($query);
				$stmt3->execute([$domain_id, $link_id]);
			} catch (PDOException $e) {
				echo htmlspecialchars($e->getMessage());
			}
		}
	}
} catch (PDOException $e) {
	echo htmlspecialchars($e->getMessage());
}

try {
	$pdo->exec("drop table link_keyword");
} catch (PDOException $e) {
	echo htmlspecialchars($e->getMessage());
}

print "Database upgraded.";
?>
