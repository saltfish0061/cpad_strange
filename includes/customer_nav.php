<!-- Unified Navigation Style -->
<style>
    .navbar { 
        display: flex; 
        justify-content: center; 
        border: 1px solid #000; 
        padding: 10px; 
        margin: 20px 0; 
        background-color: #fff;
    }
    .navbar a { 
        text-decoration: none; 
        color: #000; 
        padding: 0 15px; 
        font-weight: bold; 
        font-size: 0.95em;
    }
    .navbar a:hover { text-decoration: underline; }
    
    /* Small separator line between menu and order history */
    .nav-sep { border-left: 1px solid #000; height: 15px; margin: 0 5px; align-self: center; }
</style>

<!-- Unified Navigation Bar -->
<nav class="navbar">
    <a href="menu.php">Menu</a>
    <a href="cart.php">Cart (<?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>)</a>
    
    <div class="nav-sep"></div>
    
    <a href="my_orders.php?view=current">Current Orders</a>
    <a href="my_orders.php?view=history">Order History</a>
</nav>
