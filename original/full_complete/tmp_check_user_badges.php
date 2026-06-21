<?php
require_once 'control.php';
try {
    $dbh = db_connect();
    $stmt = $dbh->query("SHOW COLUMNS FROM user_badges");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($cols, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage();
}
