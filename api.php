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
    $userId = $_GET['user_id'] ?? 'guest';
    $stmt = $pdo->prepare('SELECT * FROM cart WHERE user_id = ?');
    $stmt->execute([$userId]);
    $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($cart);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'cart') {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $userId = $_POST['user_id'] ?? 'guest';
    $price = $_POST['price'] ?? 0;
    $stmt = $pdo->prepare('INSERT INTO cart (product_id, quantity, user_id, price) VALUES (?, ?, ?, ?)');
    $stmt->execute([$productId, $quantity, $userId, $price]);
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
    $ratingFilter = $_GET['rating'] ?? 'all';
    $sort = $_GET['sort'] ?? 'newest';
    $query = 'SELECT * FROM comments WHERE product_id = ?';
    if ($ratingFilter !== 'all') {
        $query .= ' AND rating = ?';
    }
    $query .= $sort === 'newest' ? ' ORDER BY created_at DESC' : ' ORDER BY created_at ASC';
    $stmt = $pdo->prepare($query);
    $params = [$productId];
    if ($ratingFilter !== 'all') {
        $params[] = $ratingFilter;
    }
    $stmt->execute($params);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($comments);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'comments') {
    $productId = $_POST['product_id'];
    $user = $_POST['user'];
    $text = $_POST['text'];
    $rating = $_POST['rating'];
    $stmt = $pdo->prepare('INSERT INTO comments (product_id, user, text, rating, created_at) VALUES (?, ?, ?, ?, NOW())');
    $stmt->execute([$productId, $user, $text, $rating]);
    echo json_encode(['message' => 'نظر ثبت شد']);
}

// API برای جستجو
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'search') {
    $query = $_GET['q'];
    $results = array_filter($GLOBALS['products'], fn($p) => stripos($p['name'], $query) !== false || stripos($p['desc'], $query) !== false);
    echo json_encode(array_values($results));
}

// API برای محصولات مرتبط
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'related') {
    $category = $_GET['category'];
    $exclude = $_GET['exclude'];
    $results = array_filter($GLOBALS['products'], fn($p) => $p['category'] === $category && $p['id'] != $exclude);
    echo json_encode(array_values($results));
}

// API برای داشبورد فروشنده
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add_product') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];
    $stmt = $pdo->prepare('INSERT INTO products (name, price, stock, category) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $price, $stock, $category]);
    echo json_encode(['message' => 'محصول اضافه شد']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'reply_comment') {
    $commentId = $_POST['comment_id'];
    $reply = $_POST['reply'];
    $stmt = $pdo->prepare('UPDATE comments SET reply = ? WHERE id = ?');
    $stmt->execute([$reply, $commentId]);
    echo json_encode(['message' => 'پاسخ ثبت شد']);
}
?>
