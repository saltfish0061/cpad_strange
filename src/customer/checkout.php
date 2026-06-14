<?php
session_start();
require_once '../../includes/db.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: menu.php");
    exit();
}

$cart_items = [];
$total_price = 0;

foreach ($_SESSION['cart'] as $item_id => $quantity) {
    $stmt = $db_conn->prepare("SELECT * FROM menus WHERE item_id = ?");
    $stmt->execute([$item_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($item) {
        $subtotal = $item['price'] * $quantity;
        $total_price += $subtotal;
        $cart_items[] = [
            'name' => $item['name'],
            'price' => $item['price'],
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Online Food Ordering</title>
    <style>
        body { width: 50%; margin: 0 auto; text-align: center; }
        .summary-box { border: 1px solid #000; padding: 20px; margin-top: 20px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; text-align: center; }
        th, td { border: 1px solid #000; padding: 10px; }
        .item-name { text-align: left; }
        .price { text-align: right; }
    </style>
</head>
<body>
    <h1>Order Checkout</h1>
    
    <?php include '../../includes/customer_nav.php'; ?>

    <div class="summary-box">
        <h3>Order Summary</h3>
        <table>
            <thead>
                <tr>
                    <th class="item-name">Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td class="item-name"><?php echo $item['name']; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td class="price">RM <?php echo number_format($item['subtotal'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="2" style="text-align: right; font-weight: bold;">Grand Total:</td>
                    <td class="price" style="font-weight: bold;">RM <?php echo number_format($total_price, 2); ?></td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 30px; text-align: center; margin-bottom: 20px;">
            <form action="confirm_order.php" method="POST">
                <button type="submit" style="padding: 10px 20px; font-size: 1.1em;">Confirm Order</button>
            </form>
        </div>
    </div>
</body>
</html>
