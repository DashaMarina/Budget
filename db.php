<?php
$pdo = new PDO("mysql:host=localhost;dbname=budget_family", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
session_start();
?>