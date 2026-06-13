<?php
require 'app/Core/Database.php';
$db = \App\Core\Database::getConnection();
var_dump($db->query("SHOW TABLES FROM db_merish_cafe")->fetchAll(PDO::FETCH_COLUMN));
