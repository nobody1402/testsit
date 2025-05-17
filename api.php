<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$host = 'localhost';
$dbname = 'your_database_name';
$username = 'your_database_user';
$password = 'your_database_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Database connection failed']));
}

// API برای سبد خرید
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'cart') {
    $stmt = $pdo->query('SELECT * FROM cart');
    $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($cart);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'cart') {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $stmt = $pdo->prepare('INSERT INTO cart (product_id, quantity) VALUES (?, ?)');
    $stmt->execute([$productId, $quantity]);
    echo json_encode(['message' => 'محصول به سبد اضافه شد']);
}

// API برای مقایسه
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'compare') {
    $stmt = $pdo->query('SELECT * FROM compare');
    $compare = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($compare);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'compare') {
    $productId = $_POST['product_id'];
    $stmt = $pdo->prepare('INSERT INTO compare (product_id) VALUES (?)');
    $stmt->execute([$productId]);
    echo json_encode(['message' => 'محصول به مقایسه اضافه شد']);
}

// API برای نظرات
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'comments') {
    $productId = $_GET['product_id'];
    $stmt = $pdo->prepare('SELECT * FROM comments WHERE product_id = ?');
    $stmt->execute([$productId]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($comments);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'comments') {
    $productId = $_POST['product_id'];
    $user = $_POST['user'];
    $text = $_POST['text'];
    $rating = $_POST['rating'];
    $stmt = $pdo->prepare('INSERT INTO comments (product_id, user, text, rating) VALUES (?, ?, ?, ?)');
    $stmt->execute([$productId, $user, $text, $rating]);
    echo json_encode(['message' => 'نظر ثبت شد']);
}
?>
