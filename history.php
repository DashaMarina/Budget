<?php
require_once 'db.php';
require_once 'models.php';
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
// Обработка удаления
if(isset($_GET['delete'])) {
    deleteTransaction($pdo, $_GET['delete']);
    header("Location: history.php");
    exit;
}
// Обработка редактирования
if($_POST && isset($_POST['edit_id'])) {
    updateTransaction($pdo, $_POST['edit_id'], $_POST['amount'], $_POST['date']);
    header("Location: history.php");
    exit;
}
$trans = $pdo->query("
    SELECT t.*, u.name
as user, IF(t.type='income', s.name
, c.name
) as cat 
    FROM transactions t 
    LEFT JOIN users u ON t.user_id = u.id

    LEFT JOIN sources s ON t.source_id = s.id

    LEFT JOIN categories c ON t.category_id = c.id

    ORDER BY t.transaction_date DESC
")->fetchAll();
$editItem = null;
if(isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editItem = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>История</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
rel="stylesheet">
</head>
<body>
<div class="container mt-3">
    <a href="index.php" class="btn btn-secondary mb-2">← Назад</a>
    <div class="card">
        <div class="card-header"> История</div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead class="table-light">
                    <tr><th>Дата</th><th>Кто</th><th>Тип</th><th>Категория</th><th>Сумма</th><th>Действия</th></tr>
                </thead>
                <tbody>
                    <?php foreach($trans as $t): ?>
                    <tr>
                        <td><?= $t['transaction_date'] ?></td>
                        <td><?= $t['user'] ?></td>
                        <td><?= $t['type'] == 'income' ? ' Доход' : ' Расход' ?></td>
                        <td><?= $t['cat'] ?></td>
                        <td><?= number_format($t['amount'], 0) ?> ₽</td>
                        <td>
                            <a href="?edit=<?= $t['id'] ?>" class="btn btn-sm btn-warning">✏️</a>
                            <a href="?delete=<?= $t['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить?')">🗑️</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php if($editItem): ?>
<div class="modal show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5>Редактировать</h5>
                    <a href="history.php" class="btn-close"></a>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="edit_id" value="<?= $editItem['id'] ?>">
                    <input type="number" name="amount" class="form-control mb-2" value="<?= $editItem['amount'] ?>" required>
                    <input type="date" name="date" class="form-control mb-2" value="<?= $editItem['transaction_date'] ?>" required>
                </div>
                <div class="modal-footer">
                    <a href="history.php" class="btn btn-secondary">Отмена</a>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
</body>
</html>