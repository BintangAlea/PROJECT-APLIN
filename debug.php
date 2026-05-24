<?php
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = parse_url($_SERVER['SCRIPT_NAME'], PHP_URL_PATH);

$basePath = dirname($scriptName) !== '/' ? dirname($scriptName) . '/api.php' : '/api.php';
$path = str_replace($basePath, '', $requestUri);

echo json_encode([
    'REQUEST_URI' => $_SERVER['REQUEST_URI'],
    'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'],
    'scriptName_parsed' => $scriptName,
    'basePath' => $basePath,
    'path' => $path,
    'dirname_result' => dirname($scriptName),
    'dirname_is_root' => dirname($scriptName) === '/'
], JSON_PRETTY_PRINT);
