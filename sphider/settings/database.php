<?php
	$mysql_host = getenv('DB_HOST') ?: 'localhost';
	$mysql_user = getenv('DB_USER') ?: 'root';
	$mysql_password = getenv('DB_PASSWORD') ?: 'admin';
	$database = getenv('DB_NAME') ?: 'rold20sphider';
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

	// Load Sphider Database and legacy compatibility layers
	include_once dirname(__DIR__) . "/include/database.php";
	include_once dirname(__DIR__) . "/include/mysql_compat.php";
?>

