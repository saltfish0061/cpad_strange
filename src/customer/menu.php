<?php
session_start();
require_once '../../includes/db.php';

$message = "";

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['items'])) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $added_count = 0;
    foreach ($_POST['items'] as $item_id => $quantity) {
        if ($quantity > 0) {
            if (isset($_SESSION['cart'][$item_id])) {
                $_SESSION['cart'][$item_id] += $quantity;
            } else {
                $_SESSION['cart'][$item_id] = $quantity;
            }
            $added_count++;
        }
    }
    
    if ($added_count > 0) {
        $message = "Your items have been added into cart!";
    }
}

// Fetch all available menu items
try {
    $sql = "SELECT * FROM menus WHERE is_available = 1 ORDER BY category, name";
    $stmt = $db_conn->prepare($sql);
    $stmt->execute();
    $menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching menu: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Menu - Online Food Ordering</title>
    <style>
        body { width: 50%; margin: 0 auto; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; text-align: left; }
        th, td { border: 1px solid #000; padding: 10px; }
        .fit { width: 1%; white-space: nowrap; }
        .category-header { font-weight: bold; text-decoration: underline; text-align: center; }
        .price { text-align: right; }
        .message { color: green; font-weight: bold; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Our Menu</h1>
    
    <?php include '../../includes/customer_nav.php'; ?>

    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    
    <form action="menu.php" method="POST">
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Description</th>
                    <th class="fit">Price (RM)</th>
                    <th class="fit">Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $current_category = "";
                foreach ($menu_items as $item): 
                    if ($current_category != $item['category']): 
                        $current_category = $item['category'];
                ?>
                    <tr class="category-header">
                        <td colspan="4"><?php echo strtoupper($current_category); ?></td>
                    </tr>
                <?php endif; ?>
                    <tr>
                        <td><?php echo $item['name']; ?></td>
                        <td><?php echo $item['description']; ?></td>
                        <td class="price fit"><?php echo number_format($item['price'], 2); ?></td>
                        <td class="fit">
                            <input type="number" name="items[<?php echo $item['item_id']; ?>]" min="0" value="0" style="width: 50px;">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div style="margin-top: 20px; margin-bottom: 30px; text-align: center;">
            <button type="submit">Add to Cart</button>
            <button type="reset">Clear Selection</button>
        </div>
    </form>
</body>
</html>
