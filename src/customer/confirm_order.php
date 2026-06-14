<?php
session_start();
require_once '../../includes/db.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: menu.php");
    exit();
}

$user_id = 'C001'; // Need to update after implement auth module

try {
    $db_conn->beginTransaction();

    // 1. Calculate Total
    $total_amount = 0;
    $items_to_save = [];
    foreach ($_SESSION['cart'] as $item_id => $quantity) {
        $stmt = $db_conn->prepare("SELECT price FROM menus WHERE item_id = ?");
        $stmt->execute([$item_id]);
        $price = $stmt->fetchColumn();
        
        $subtotal = $price * $quantity;
        $total_amount += $subtotal;
        $items_to_save[] = [
            'item_id' => $item_id,
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }

    // 2. Generate Order ID (incresing not random)
    $stmt = $db_conn->query("SELECT order_id FROM orders WHERE order_id LIKE 'O%' ORDER BY CAST(SUBSTRING(order_id, 2) AS UNSIGNED) DESC LIMIT 1");
    $last_id = $stmt->fetchColumn();
    $order_id = $last_id ? sprintf("O%03d", (int)substr($last_id, 1) + 1) : "O001";

    // 3. Insert Main Order
    $stmt = $db_conn->prepare("INSERT INTO orders (order_id, user_id, total_amount, status) VALUES (?, ?, ?, 'pending')");
    $stmt->execute([$order_id, $user_id, $total_amount]);

    // 4. Insert Order Items 
    $stmt = $db_conn->query("SELECT order_item_id FROM order_items WHERE order_item_id LIKE 'OI%' ORDER BY CAST(SUBSTRING(order_item_id, 3) AS UNSIGNED) DESC LIMIT 1");
    $last_oi_id = $stmt->fetchColumn();
    $oi_num = $last_oi_id ? (int)substr($last_oi_id, 2) : 0;

    $stmt_item = $db_conn->prepare("INSERT INTO order_items (order_item_id, order_id, item_id, quantity, subtotal) VALUES (?, ?, ?, ?, ?)");
    foreach ($items_to_save as $item) {
        $oi_num++;
        $oi_id = sprintf("OI%03d", $oi_num);
        $stmt_item->execute([$oi_id, $order_id, $item['item_id'], $item['quantity'], $item['subtotal']]);
    }

    $db_conn->commit();
    unset($_SESSION['cart']);

} catch (PDOException $e) {
    $db_conn->rollBack();
    die("Error placing order: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Successful - Online Food Ordering</title>
    <style>
        body { width: 50%; margin: 0 auto; text-align: center; }
        .success-box { border: 2px solid green; padding: 30px; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="success-box">
        <h1 style="color: green;">Order Placed Successfully!</h1>
        
        <?php include '../../includes/customer_nav.php'; ?>

        <p>Thank you for your order. Your Order ID is: <b><?php echo $order_id; ?></b></p>
        <p>You can track your order in the "Order Status" page.</p>
    </div>
</body>
</html>
