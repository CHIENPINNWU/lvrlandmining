<?php
function getDbConnection($dbname)
{
    $dsn = 'mysql:dbname='.$dbname.';host=127.0.0.1';
    $user = 'root';
    $password = 'password';
    $dbh = new PDO($dsn, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

    try {
        $dbh = new PDO($dsn, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    } catch (PDOException $e) {
        die('Connection failed: ' . $e->getMessage());
    }

    return $dbh;
}