<?php
function allUsers($pdo) {
    return $pdo->query("SELECT * FROM users")->fetchAll();
}

function allCategories($pdo) {
    return $pdo->query("SELECT * FROM categories")->fetchAll();
}

function allSources($pdo) {
    return $pdo->query("SELECT * FROM sources")->fetchAll();
}

function addTransaction($pdo, $user, $type, $amount, $date, $cat, $src) {
    if($type == 'income') {
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, source_id, transaction_date) VALUES (?, 'income', ?, ?, ?)");
        $stmt->execute([$user, $amount, $src, $date]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, category_id, transaction_date) VALUES (?, 'expense', ?, ?, ?)");
        $stmt->execute([$user, $amount, $cat, $date]);
    }
}

function lastIncomes($pdo) {
    return $pdo->query("SELECT t.*, s.name as source, u.name as user FROM transactions t LEFT JOIN sources s ON t.source_id=s.id LEFT JOIN users u ON t.user_id=u.id WHERE t.type='income' ORDER BY t.transaction_date DESC LIMIT 5")->fetchAll();
}

function lastExpenses($pdo) {
    return $pdo->query("SELECT t.*, c.name as category, u.name as user FROM transactions t LEFT JOIN categories c ON t.category_id=c.id LEFT JOIN users u ON t.user_id=u.id WHERE t.type='expense' ORDER BY t.transaction_date DESC LIMIT 5")->fetchAll();
}

function allGoals($pdo) {
    return $pdo->query("SELECT g.*, u.name as user FROM goals g JOIN users u ON g.user_id=u.id")->fetchAll();
}

function addGoal($pdo, $user, $name, $amount) {
    $stmt = $pdo->prepare("INSERT INTO goals (user_id, name, target_amount) VALUES (?, ?, ?)");
    $stmt->execute([$user, $name, $amount]);
}

function totalIncome($pdo) {
    return $pdo->query("SELECT SUM(amount) FROM transactions WHERE type='income'")->fetchColumn();
}

function totalExpense($pdo) {
    return $pdo->query("SELECT SUM(amount) FROM transactions WHERE type='expense'")->fetchColumn();
}

function deleteRow($pdo, $table, $id) {
    $stmt = $pdo->prepare("DELETE FROM $table WHERE id = ?");
    $stmt->execute([$id]);
}
function completeGoal($pdo, $goal_id, $user_id, $amount) {
    // 1. Удаляем цель
    $stmt = $pdo->prepare("DELETE FROM goals WHERE id = ?");
    $stmt->execute([$goal_id]);
    
    // 2. Добавляем расход на сумму цели
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, category_id, description, transaction_date) VALUES (?, 'expense', ?, 1, 'Оплачена цель', CURDATE())");
    $stmt->execute([$user_id, $amount]);
    
    return true;
}
//УДАЛЕНИЕ 
function deleteTransaction($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = ?");
    return $stmt->execute([$id]);
}

//РЕДАКТИРОВАНИЕ
function updateTransaction($pdo, $id, $amount, $date) {
    $stmt = $pdo->prepare("UPDATE transactions SET amount = ?, transaction_date = ? WHERE id = ?");
    return $stmt->execute([$amount, $date, $id]);
}

?>