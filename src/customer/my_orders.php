<?php
session_start();
require_once '../../includes/db.php';

// Test User
$user_id = 'C001'; 

$view = isset($_GET['view']) ? $_GET['view'] : 'current';

try {
    if ($view === 'history') {
        $stmt = $db_conn->prepare("SELECT * FROM orders WHERE user_id = ? AND status IN ('completed', 'cancelled') ORDER BY order_date DESC");
        $title = "Order History";
    } else {
        $stmt = $db_conn->prepare("SELECT * FROM orders WHERE user_id = ? AND status IN ('pending', 'preparing', 'ready') ORDER BY order_date DESC");
        $title = "Current Order Status";
    }
    
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching orders: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?> - Online Food Ordering</title>
    <style>
        body { width: 50%; margin: 0 auto; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; text-align: left; }
        th, td { border: 1px solid #000; padding: 10px; }
        .fit { width: 1%; white-space: nowrap; }
        .status-pending { color: orange; font-weight: bold; }
        .status-preparing { color: blue; font-weight: bold; }
        .status-ready { color: green; font-weight: bold; }
        .status-completed { color: gray; }
        .status-cancelled { color: red; }
    </style>
</head>
<body>
    <h1>My Orders</h1>
    
    <?php include '../../includes/customer_nav.php'; ?>

    <h3><?php echo $title; ?></h3>

    <?php if (empty($orders)): ?>
        <p>No orders found in this section.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th class="fit">Order ID</th>
                    <th>Date</th>
                    <th class="fit">Total (RM)</th>
                    <th class="fit">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td class="fit">
                            <a href="order_details.php?id=<?php echo $order['order_id']; ?>">
                                <?php echo $order['order_id']; ?>
                            </a>
                        </td>
                        <td><?php echo $order['order_date']; ?></td>
                        <td class="fit" style="text-align: right;"><?php echo number_format($order['total_amount'], 2); ?></td>
                        <td class="fit status-<?php echo $order['status']; ?>">
                            <?php echo strtoupper($order['status']); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
