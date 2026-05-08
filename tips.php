<?php
require_once 'db.php';
if(!isset($_SESSION['user_id'])){header("Location: login.php");exit;}
$tips=[
    ['50/30/20','50% на необходимое, 30% на желания, 20% в копилку'],
    ['Отслеживайте мелкие траты','Кофе и перекусы незаметно съедают бюджет'],
    ['Правило 24 часов','Перед крупной покупкой подождите сутки'],
    ['Откладывайте первым делом','Сразу после зарплаты положите часть в копилку']
];
?>
<!DOCTYPE html>
<html>
<head><title>Советы</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<div class="container mt-3">
    <a href="index.php" class="btn btn-sm btn-secondary mb-2">← Назад</a>
    <div class="card">
        <div class="card-header">💡  Советы по бюджету</div>
        <div class="card-body">
            <?foreach($tips as $t){echo"<div class='alert alert-info'><b>{$t[0]}</b><br>{$t[1]}</div>";}?>
        </div>
    </div>
</div>
</body>
</html>