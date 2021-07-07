<?php

define('USER', 'poker-user');
define('PASSWORD', '<password>');
define('HOST', 'localhost');
define('DATABASE', 'poker');

try {
	$db = new PDO("mysql:host=".HOST.";dbname=".DATABASE, USER, PASSWORD);
} catch (PDOException $e) {
	die("Error: ".$e->getMessage());
}
