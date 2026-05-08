<?php
require_once 'db.php';

if($_POST) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? AND password=MD5(?)");
    $stmt->execute([$_POST['email'], $_POST['password']]);
    $user = $stmt->fetch();
    if($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Вход</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width:400px">
    <div class="card">
        <div class="card-body">
            <h3 class="text-center">💰 Вход</h3>
            <form method="POST">
                <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
                <input type="password" name="password" class="form-control mb-2" placeholder="Пароль" required>
                <button class="btn btn-primary w-100">Войти</button>
            </form>
            <div class="text-center mt-2"><a href="register.php">Регистрация</a></div>
        </div>
    </div>
</div>
</body>
</html>