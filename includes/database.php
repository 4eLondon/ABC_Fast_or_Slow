<?php

function database_connection(): PDO
{
    // AI assisted
    static $connection = null;

    if ($connection instanceof PDO) {
        return $connection;
    }

    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '3306';
    $databaseName = getenv('DB_NAME') ?: 'abc_fast_or_slow';
    $databaseUser = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASS');
    $password = $password === false ? '123' : $password;


    $dsn = "mysql:host={$host};port={$port};dbname={$databaseName};charset=utf8mb4";
    $connection = new PDO($dsn, $databaseUser, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $connection;
}
//PDO is a class that represents a connection between PHP and a database server. 