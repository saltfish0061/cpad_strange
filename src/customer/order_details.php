<?php
session_start();
require_once '../../includes/db.php';

// Check if Order ID is provided
if (!isset($_GET['id'])) {
    header("Location: my_orders.php");
    exit();
}

$order_id = $_GET['id'];
$user_id = 'C001'; // Need to update after implement auth module

try {
    // 1. Fetch Order based on user_id 
    $stmt = $db_conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("Order not found"); // Cannot see others order from the link like(order_details.php?id=O001)
    }

    // 2. Fetch Order Items (Joined oder_items and menus)
    $stmt = $db_conn->prepare("
        SELECT oi.*, m.name as item_name, m.price as unit_price 
        FROM order_items oi
        JOIN menus m ON oi.item_id = m.item_id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error fetching order details: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details - <?php echo $order_id; ?></title>
    <style>
        body { width: 50%; margin: 0 auto; text-align: center; }
        .details-box { border: 1px solid #000; padding: 20px; margin-top: 20px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; text-align: left; }
        th, td { border: 1px solid #000; padding: 10px; }
        .fit { width: 1%; white-space: nowrap; }
        .price { text-align: right; }
        .status-badge { font-weight: bold; text-transform: uppercase; border: 1px solid #000; padding: 2px 8px; }
    </style>
</head>
<body>
    <h1>Order Details: <?php echo $order_id; ?></h1>
    
    <?php include '../../includes/customer_nav.php'; ?>

    <div class="details-box">
        <p><b>Order Date:</b> <?php echo $order['order_date']; ?></p>
        <p><b>Status:</b> <span class="status-badge"><?php echo $order['status']; ?></span></p>

        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th class="fit">Unit Price</th>
                    <th class="fit">Quantity</th>
                    <th class="fit">Subtotal (RM)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo $item['item_name']; ?></td>
                        <td class="price fit"><?php echo number_format($item['unit_price'], 2); ?></td>
                        <td class="fit" style="text-align: center;"><?php echo $item['quantity']; ?></td>
                        <td class="price fit"><?php echo number_format($item['subtotal'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr style="font-weight: bold;">
                    <td colspan="3" style="text-align: right;">Grand Total:</td>
                    <td class="price">RM <?php echo number_format($order['total_amount'], 2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
