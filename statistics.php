<?php
require_once 'db.php';
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$stats = $pdo->query("SELECT c.name, SUM(t.amount) as total FROM transactions t JOIN categories c ON t.category_id=c.id WHERE t.type='expense' GROUP BY c.id ORDER BY total DESC")->fetchAll();
$total = array_sum(array_column($stats, 'total'));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Статистика</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-3">
    <a href="index.php" class="btn btn-sm btn-secondary mb-2">← Назад</a>
    <div class="card">
        <div class="card-header">📊 Статистика</div>
        <div class="card-body">
            <?php foreach($stats as $s): ?>
                <?php $percent = round($s['total'] / $total * 100); ?>
                <b><?= $s['name'] ?></b> - <?= $s['total'] ?> ₽ (<?= $percent ?>%)
                <div class="progress mb-2"><div class="progress-bar" style="width:<?= $percent ?>%; background:#2c7da0"></div></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</body>
</html>