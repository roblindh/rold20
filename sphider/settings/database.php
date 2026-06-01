<?php
	$database="rold20sphider";
	$mysql_user = "root";
	$mysql_password = "admin"; 
	$mysql_host = "localhost";
	$mysql_table_prefix = "";

	try {
		$dsn = "mysql:host={$mysql_host};dbname={$database};charset=utf8mb4";
		$pdo = new PDO($dsn, $mysql_user, $mysql_password, [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		]);
	} catch (PDOException $e) {
		die ("<b>Cannot connect to database, check if username, password and host are correct.</b><br>" . htmlspecialchars($e->getMessage()));
	}
?>

