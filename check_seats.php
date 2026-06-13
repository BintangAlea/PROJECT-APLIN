<?php
require 'bootstrap.php';
$db = \App\Core\Database::getConnection();
$res = $db->query('SELECT * FROM seats');
var_dump($res->fetchAll());
