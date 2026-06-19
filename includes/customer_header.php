<?php
// Shared customer topbar header layout
// Expected variables:
// $root_path: e.g. "../../" or ""
// $customer_path: e.g. "" or "src/customer/"
// $active_page: e.g. "menu", "orders", "cart", etc.
?>
<header class="topbar">
  <a class="brand" href="<?php echo $root_path; ?>index.php" aria-label="Universal Sambal home">
    <span class="brand-mark">US</span>
    <span>Universal Sambal</span>
  </a>

  <nav class="nav" aria-label="Main navigation">
    <a class="nav-link <?php echo ($active_page === 'home') ? 'active' : ''; ?>" href="<?php echo $root_path; ?>index.php">Home</a>
    <a class="nav-link <?php echo ($active_page === 'menu') ? 'active' : ''; ?>" href="<?php echo $root_path; ?>src/customer/menu.php">Menu</a>
    <a class="nav-link <?php echo ($active_page === 'orders') ? 'active' : ''; ?>" href="<?php echo $root_path; ?>src/customer/my_orders.php">Orders</a>
    <a class="nav-link <?php echo ($active_page === 'profile') ? 'active' : ''; ?>" href="<?php echo $root_path; ?>index.php#/profile">Profile</a>
  </nav>

  <div class="top-actions">
    <a class="pill-button cart-button <?php echo ($active_page === 'cart') ? 'active' : ''; ?>" href="<?php echo $root_path; ?>src/customer/cart.php">
      <svg 
        xmlns="http://www.w3.org/2000/svg" 
        viewBox="0 0 24 24" 
        fill="none" 
        stroke="currentColor" 
        stroke-width="2" 
        stroke-linecap="round" 
        stroke-linejoin="round" 
        class="cart-icon"
      >
        <circle cx="9" cy="21" r="1"></circle>
        <circle cx="20" cy="21" r="1"></circle>
        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
      </svg>
      <span id="header-cart-count">0</span>
    </a>
    <a class="pill-button primary" href="<?php echo $root_path; ?>index.php#/login">Login</a>
  </div>
</header>

<script>
  // Dynamically sync cart count badge from localStorage
  const syncHeaderCartCount = () => {
    try {
      const savedCart = localStorage.getItem('cart');
      const countEl = document.getElementById('header-cart-count');
      if (countEl) {
        if (savedCart) {
          const cart = JSON.parse(savedCart);
          countEl.innerText = Object.values(cart).reduce((sum, qty) => sum + qty, 0);
        } else {
          countEl.innerText = '0';
        }
      }
    } catch (e) {
      console.error('Failed to sync cart count:', e);
    }
  };
  
  // Set up listeners
  document.addEventListener('DOMContentLoaded', syncHeaderCartCount);
  window.addEventListener('storage', syncHeaderCartCount);
  // Periodic fallback check in case state is updated in the same window
  setInterval(syncHeaderCartCount, 1000);
</script>
