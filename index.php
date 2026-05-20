<?php
require_once 'db.php';
require_once 'models.php';

if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

if($_POST) {
    if(isset($_POST['add_income'])) {
        addTransaction($pdo, $_POST['user_id'], 'income', $_POST['amount'], $_POST['date'], 0, $_POST['source_id']);
        header("Location: index.php");
        exit;
    }
    if(isset($_POST['add_expense'])) {
        addTransaction($pdo, $_POST['user_id'], 'expense', $_POST['amount'], $_POST['date'], $_POST['category_id'], 0);
        header("Location: index.php");
        exit;
    }
    if(isset($_POST['add_goal'])) {
        addGoal($pdo, $_POST['user_id'], $_POST['goal_name'], $_POST['target_amount']);
        header("Location: index.php");
        exit;
    }
    if(isset($_POST['complete_goal'])) {
        $stmt = $pdo->query("SELECT id FROM categories WHERE name = 'Цель'");
        $goal_category = $stmt->fetch();
        $category_id = $goal_category ? $goal_category['id'] : 1;
        
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, category_id, description, transaction_date) VALUES (?, 'expense', ?, ?, 'Оплачена цель', CURDATE())");
        $stmt->execute([$_POST['user_id'], $_POST['amount'], $category_id]);
        
        $stmt = $pdo->prepare("DELETE FROM goals WHERE id = ?");
        $stmt->execute([$_POST['goal_id']]);
        
        $_SESSION['congrats'] = "🎉  Поздравляем! Цель достигнута! Списано " . number_format($_POST['amount'], 0) . " ₽.";
        header("Location: index.php");
        exit;
    }
}

$users = allUsers($pdo);
$categories = allCategories($pdo);
$sources = allSources($pdo);
$incomes = lastIncomes($pdo);
$expenses = lastExpenses($pdo);
$goals = allGoals($pdo);
$income_total = totalIncome($pdo);
$expense_total = totalExpense($pdo);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Бюджет</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background: #f5f5f5; }
        .container-fluid { max-width: 1400px; margin: 0 auto; }
        .card { margin-bottom: 20px; border-radius: 10px; }
        table { font-size: 14px; }
        .nav-buttons { margin-bottom: 20px; }
        .nav-buttons .btn { margin-right: 10px; }
    </style>
</head>
<body>
<div class="container-fluid">
    <!-- Навигационные кнопки -->
    <div class="nav-buttons">
        
        <a href="history.php" class="btn btn-info">  История</a>
        <a href="statistics.php" class="btn btn-success">  Статистика</a>
        <a href="tips.php" class="btn btn-info">  Советы</a>
        <a href="logout.php" class="btn btn-danger">  Выход</a>
    </div>

    <?php if(isset($_SESSION['congrats'])): ?>
        <div class="alert alert-success text-center"><?= $_SESSION['congrats'] ?></div>
        <?php unset($_SESSION['congrats']); ?>
    <?php endif; ?>
    
    <div class="row mb-3">
        <div class="col-md-3 alert alert-success"> Доходы: <?= number_format($income_total, 0) ?> ₽</div>
        <div class="col-md-3 alert alert-danger">  Расходы: <?= number_format($expense_total, 0) ?> ₽</div>
        <div class="col-md-3 alert alert-info"> Остаток: <?= number_format($income_total - $expense_total, 0) ?> ₽</div>
        <div class="col-md-3 text-end"></div>
    </div>
    
    <div class="row">
        <!-- Доходы -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">Доход</div>
                <div class="card-body">
                    <form method="POST" class="row g-2">
                        <div class="col-6"><select name="user_id" class="form-select"><?php foreach($users as $u) echo "<option value='{$u['id']}'>{$u['name']}</option>"; ?></select></div>
<div class="col-6"><select name="source_id" class="form-select"><?php foreach($sources as $s) echo "<option value='{$s['id']}'>{$s['name']}</option>"; ?></select></div>
                        <div class="col-4"><input type="number" name="amount" class="form-control" placeholder="Сумма" required></div>
                        <div class="col-4"><input type="date" name="date" class="form-control" required></div>
                        <div class="col-4"><button type="submit" name="add_income" class="btn btn-success w-100">+</button></div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-secondary text-white">Доходы</div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light"><tr><th>Дата</th><th>Кто</th><th>Источник</th><th>Сумма</th></tr></thead>
                        <tbody>
                            <?php foreach($incomes as $i): ?>
                            <tr><td><?= $i['transaction_date'] ?></td><td><?= $i['user'] ?></td><td><?= $i['source'] ?></td><td class="text-success">+<?= number_format($i['amount'], 0) ?></td></tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Расходы -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-danger text-white">Расход</div>
                <div class="card-body">
                    <form method="POST" class="row g-2">
                        <div class="col-6"><select name="user_id" class="form-select"><?php foreach($users as $u) echo "<option value='{$u['id']}'>{$u['name']}</option>"; ?></select></div>
                        <div class="col-6"><select name="category_id" class="form-select"><?php foreach($categories as $c) echo "<option value='{$c['id']}'>{$c['name']}</option>"; ?></select></div>
                        <div class="col-4"><input type="number" name="amount" class="form-control" placeholder="Сумма" required></div>
                        <div class="col-4"><input type="date" name="date" class="form-control" required></div>
                        <div class="col-4"><button type="submit" name="add_expense" class="btn btn-danger w-100">-</button></div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-secondary text-white">Расходы</div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light"><tr><th>Дата</th><th>Кто</th><th>Категория</th><th>Сумма</th></tr></thead>
                        <tbody>
                            <?php foreach($expenses as $e): ?>
                            <td><td><?= $e['transaction_date'] ?></td><td><?= $e['user'] ?></td><td><?= $e['category'] ?></td><td class="text-danger">-<?= number_format($e['amount'], 0) ?></td></tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Цели -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-warning">Новая цель</div>
                <div class="card-body">
                    <form method="POST" class="row g-2">
                        <div class="col-12"><select name="user_id" class="form-select"><?php foreach($users as $u) echo "<option value='{$u['id']}'>{$u['name']}</option>"; ?></select></div>
                        <div class="col-7"><input type="text" name="goal_name" class="form-control" placeholder="Что хотим?" required></div>
<div class="col-3"><input type="number" name="target_amount" class="form-control" placeholder="Сколько?" required></div>
                        <div class="col-2"><button type="submit" name="add_goal" class="btn btn-primary w-100">+</button></div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-secondary text-white">Цели</div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light"><tr><th>Кто</th><th>Цель</th><th>Нужно</th><th></th></tr></thead>
                        <tbody>
                            <?php foreach($goals as $g): ?>
                            <tr>
                                <td><?= $g['user'] ?></td><td><?= $g['name'] ?></td><td><?= number_format($g['target_amount'], 0) ?> ₽</td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="goal_id" value="<?= $g['id'] ?>">
                                        <input type="hidden" name="user_id" value="<?= $g['user_id'] ?>">
                                        <input type="hidden" name="amount" value="<?= $g['target_amount'] ?>">
                                        <button type="submit" name="complete_goal" class="btn btn-sm btn-success" onclick="return confirm('Списать <?= number_format($g['target_amount'], 0) ?> ₽?')">✅</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>