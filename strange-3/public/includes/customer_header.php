<?php
// Shared customer topbar header layout
// Expected variables:
// $root_path: e.g. "../../" or ""
// $customer_path: e.g. "" or "src/customer/"
// $active_page: e.g. "menu", "orders", "cart", etc.
?>
<header class="topbar">
  <a class="brand" href="<?php echo $root_path; ?>index.php" aria-label="Universal Sambal home">
    <img class="logo" src="<?php echo $root_path; ?>images/assets/logo.png" alt="Universal Sambal logo">
    <span>Universal Sambal</span>
  </a>

  <nav class="nav" aria-label="Main navigation">
    <a class="nav-link <?php echo ($active_page === 'home') ? 'active' : ''; ?>" data-page="home" href="<?php echo $root_path; ?>index.php">Home</a>
    <a class="nav-link <?php echo ($active_page === 'menu') ? 'active' : ''; ?>" data-page="menu" href="<?php echo $root_path; ?>src/customer/menu.php">Menu</a>
    <a class="nav-link <?php echo ($active_page === 'orders') ? 'active' : ''; ?>" data-page="orders" href="<?php echo $root_path; ?>src/customer/my_orders.php">Orders</a>
    <a class="nav-link <?php echo ($active_page === 'profile') ? 'active' : ''; ?>" data-page="profile" href="<?php echo $root_path; ?>src/customer/profile.php">Profile</a>
    <a id="header-vendor-link" class="nav-link <?php echo ($active_page === 'vendor') ? 'active' : ''; ?>" data-page="vendor" href="<?php echo $root_path; ?>src/vendor/dashboard.php" hidden>Vendor</a>
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
    <a id="header-login-link" class="pill-button primary" href="<?php echo $root_path; ?>src/auth/login.php">Login</a>
    <button id="header-logout-button" class="pill-button primary" type="button" hidden>Logout</button>
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

  const syncHeaderVendorLink = () => {
    const vendorLink = document.getElementById('header-vendor-link');
    if (!vendorLink) return;

    try {
      const savedUser = localStorage.getItem('currentUser');
      const currentUser = savedUser ? JSON.parse(savedUser) : null;
      vendorLink.hidden = currentUser?.role !== 'admin';
    } catch (e) {
      vendorLink.hidden = true;
    }
  };

  const syncHeaderAuth = () => {
    const loginLink = document.getElementById('header-login-link');
    const logoutButton = document.getElementById('header-logout-button');
    if (!loginLink || !logoutButton) return;

    try {
      const savedUser = localStorage.getItem('currentUser');
      const currentUser = savedUser ? JSON.parse(savedUser) : null;
      loginLink.hidden = Boolean(currentUser);
      logoutButton.hidden = !currentUser;
    } catch (e) {
      loginLink.hidden = false;
      logoutButton.hidden = true;
    }
  };

  const setupHeaderLogout = () => {
    const logoutButton = document.getElementById('header-logout-button');
    if (!logoutButton) return;

    logoutButton.addEventListener('click', () => {
      localStorage.removeItem('currentUser');
      syncHeaderAuth();
      syncHeaderVendorLink();
      window.location.href = '<?php echo $root_path; ?>index.php';
    });
  };
  
  // Set up listeners
  document.addEventListener('DOMContentLoaded', () => {
    syncHeaderCartCount();
    syncHeaderVendorLink();
    syncHeaderAuth();
    setupHeaderLogout();
  });
  window.addEventListener('storage', () => {
    syncHeaderCartCount();
    syncHeaderVendorLink();
    syncHeaderAuth();
  });
  // Periodic fallback check in case state is updated in the same window
  setInterval(() => {
    syncHeaderCartCount();
    syncHeaderVendorLink();
    syncHeaderAuth();
  }, 1000);

  const animateHeaderCartWiggle = () => {
    const cartButton = document.querySelector('.cart-button');
    if (!cartButton) return;

    cartButton.classList.remove('cart-wiggle');
    void cartButton.offsetWidth;
    cartButton.classList.add('cart-wiggle');

    window.setTimeout(() => {
      cartButton.classList.remove('cart-wiggle');
    }, 450);
  };
</script>
