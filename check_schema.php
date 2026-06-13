<?php
require 'bootstrap.php';
$db = \App\Core\Database::getConnection();
$res = $db->query('SHOW COLUMNS FROM services');
var_dump($res->fetchAll(PDO::FETCH_COLUMN));
