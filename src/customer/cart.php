<?php
session_start();
require_once '../../includes/db.php';

// Delete Item
if (isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    if (isset($_SESSION['cart'][$id_to_delete])) {
        unset($_SESSION['cart'][$id_to_delete]);
    }
    header("Location: cart.php");
    exit();
}

// Update Quantities
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qty'])) {
    foreach ($_POST['qty'] as $item_id => $quantity) {
        if ($quantity < 1) {
            unset($_SESSION['cart'][$item_id]);
        } else {
            $_SESSION['cart'][$item_id] = $quantity;
        }
    }
    header("Location: cart.php");
    exit();
}

$cart_items = [];
$total_price = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item_id => $quantity) {
        $stmt = $db_conn->prepare("SELECT * FROM menus WHERE item_id = ?");
        $stmt->execute([$item_id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($item) {
            $subtotal = $item['price'] * $quantity;
            $total_price += $subtotal;
            $cart_items[] = [
                'item_id' => $item_id,
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Cart - Online Food Ordering</title>
    <style>
        body { width: 50%; margin: 0 auto; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; text-align: left; }
        th, td { border: 1px solid #000; padding: 10px; }
        .fit { width: 1%; white-space: nowrap; }
        .price { text-align: right; }
        .total-row { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Your Shopping Cart</h1>
    
    <?php include '../../includes/customer_nav.php'; ?>

    <?php if (empty($cart_items)): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <form id="cartForm" action="cart.php" method="POST">
            <table>
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th class="fit">Price (RM)</th>
                        <th class="fit">Qty</th>
                        <th class="fit">Subtotal (RM)</th>
                        <th class="fit">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><?php echo $item['name']; ?></td>
                            <td class="price fit"><?php echo number_format($item['price'], 2); ?></td>
                            <td class="fit">
                                <input type="number" name="qty[<?php echo $item['item_id']; ?>]" 
                                       value="<?php echo $item['quantity']; ?>" min="1" 
                                       style="width: 50px;" 
                                       onchange="this.form.submit()">
                            </td>
                            <td class="price fit"><?php echo number_format($item['subtotal'], 2); ?></td>
                            <td class="fit"><a href="cart.php?delete=<?php echo $item['item_id']; ?>" onclick="return confirm('Remove this item?')">Delete</a></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="3" style="text-align: right;">Total Price:</td>
                        <td class="price fit">RM <?php echo number_format($total_price, 2); ?></td>
                        <td class="fit"></td>
                    </tr>
                </tbody>
            </table>
            
            <div style="margin-top: 20px; margin-bottom: 30px;">
                <a href="checkout.php"><button type="button" style="padding: 10px 20px;">Checkout</button></a>
            </div>
        </form>
    <?php endif; ?>
</body>
</html>
