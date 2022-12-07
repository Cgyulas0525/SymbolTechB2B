<?php

try {
    $opt  = array(
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => FALSE,
    );
    $dsn = 'mysql:host=localhost;dbname=b2b0000;charset=utf8';
    $pdo = new PDO($dsn, 'root', 'password', $opt);
} catch(PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

