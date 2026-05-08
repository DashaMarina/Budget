<?php
require_once 'db.php';
if($_POST){
    $stmt=$pdo->prepare("INSERT INTO users (name,email,password) VALUES (?,?,MD5(?))");
    $stmt->execute([$_POST['name'],$_POST['email'],$_POST['password']]);
    header("Location: login.php");
}
?>
<!DOCTYPE html>
<html>
<head><title>Регистрация</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-5" style="max-width:400px">
    <div class="card">
        <div class="card-body">
            <h3 class="text-center">📝  Регистрация</h3>
            <form method="POST">
                <input type="text" name="name" class="form-control mb-2" placeholder="Имя" required>
                <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
                <input type="password" name="password" class="form-control mb-2" placeholder="Пароль" required>
                <button class="btn btn-primary w-100">Зарегистрироваться</button>
            </form>
            <div class="text-center mt-2">
                <a href="login.php">Вход</a></div>
        </div>
    </div>
</div>
</body>
</html>