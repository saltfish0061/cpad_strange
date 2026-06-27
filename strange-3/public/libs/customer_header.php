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

  <div class="mobile-header-actions">
    <a class="pill-button cart-button mobile-cart-button <?php echo ($active_page === 'cart') ? 'active' : ''; ?>" href="<?php echo $root_path; ?>src/customer/cart.php" aria-label="Cart">
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
      <span class="header-cart-count"><script>document.write(AppUtils.cart.count(AppUtils.cart.load()))</script></span>
    </a>

    <button class="mobile-nav-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false" aria-controls="topbar-menu">
      <span class="toggle-bar"></span>
      <span class="toggle-bar"></span>
      <span class="toggle-bar"></span>
    </button>
  </div>

  <div id="topbar-menu" class="topbar-menu">
    <nav class="nav" aria-label="Main navigation">
      <a class="nav-link <?php echo ($active_page === 'home') ? 'active' : ''; ?>" data-page="home" href="<?php echo $root_path; ?>index.php">Home</a>
      <a class="nav-link <?php echo ($active_page === 'menu') ? 'active' : ''; ?>" data-page="menu" href="<?php echo $root_path; ?>src/customer/menu.php">Menu</a>
      <a class="nav-link <?php echo ($active_page === 'orders') ? 'active' : ''; ?>" data-page="orders" href="<?php echo $root_path; ?>src/customer/my_orders.php">Orders</a>
      <a class="nav-link <?php echo ($active_page === 'profile') ? 'active' : ''; ?>" data-page="profile" href="<?php echo $root_path; ?>src/customer/profile.php">Profile</a>
      <a id="header-vendor-link" class="nav-link <?php echo ($active_page === 'vendor') ? 'active' : ''; ?>" data-page="vendor" href="<?php echo $root_path; ?>src/vendor/dashboard.php" hidden>Dashboard</a>
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
        <span id="header-cart-count" class="header-cart-count"><script>document.write(AppUtils.cart.count(AppUtils.cart.load()))</script></span>
      </a>
      <a id="header-login-link" class="pill-button primary" href="<?php echo $root_path; ?>src/auth/login.php">Login</a>
      <button id="header-logout-button" class="pill-button primary" type="button" hidden>Logout</button>
    </div>
  </div>
</header>

<?php if ($active_page !== 'vendor') : ?>
<nav class="apk-bottom-nav" aria-label="App navigation">
  <a class="apk-tab <?php echo ($active_page === 'home') ? 'active' : ''; ?>" href="<?php echo $root_path; ?>index.php">
    <svg viewBox="0 0 24 24" aria-hidden="true">
      <path d="M3 10.5 12 3l9 7.5"></path>
      <path d="M5 9.5V21h5v-6h4v6h5V9.5"></path>
    </svg>
    <span>Home</span>
  </a>
  <a class="apk-tab <?php echo ($active_page === 'orders') ? 'active' : ''; ?>" href="<?php echo $root_path; ?>src/customer/my_orders.php">
    <svg viewBox="0 0 24 24" aria-hidden="true">
      <path d="M7 3h10a2 2 0 0 1 2 2v16l-3-2-2 2-2-2-2 2-2-2-3 2V5a2 2 0 0 1 2-2Z"></path>
      <path d="M9 8h6"></path>
      <path d="M9 12h6"></path>
    </svg>
    <span>Orders</span>
  </a>
  <a class="apk-tab apk-tab-primary <?php echo ($active_page === 'menu') ? 'active' : ''; ?>" href="<?php echo $root_path; ?>src/customer/menu.php" aria-label="Order">
    <span class="apk-tab-primary-icon">
      <svg viewBox="0 0 24 24" aria-hidden="true">
        <path d="M4 12h16"></path>
        <path d="M12 4v16"></path>
      </svg>
    </span>
    <span>Order</span>
  </a>
  <a class="apk-tab <?php echo ($active_page === 'cart') ? 'active' : ''; ?>" href="<?php echo $root_path; ?>src/customer/cart.php">
    <span class="apk-tab-badge-wrap">
      <svg viewBox="0 0 24 24" aria-hidden="true">
        <circle cx="9" cy="21" r="1"></circle>
        <circle cx="20" cy="21" r="1"></circle>
        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
      </svg>
      <span class="apk-cart-badge header-cart-count"><script>document.write(AppUtils.cart.count(AppUtils.cart.load()))</script></span>
    </span>
    <span>Cart</span>
  </a>
  <a class="apk-tab <?php echo ($active_page === 'profile') ? 'active' : ''; ?>" href="<?php echo $root_path; ?>src/customer/profile.php">
    <svg viewBox="0 0 24 24" aria-hidden="true">
      <circle cx="12" cy="8" r="4"></circle>
      <path d="M4 21a8 8 0 0 1 16 0"></path>
    </svg>
    <span>Profile</span>
  </a>
</nav>
<?php endif; ?>

<script>
  // Dynamically sync cart count badge from localStorage
  const syncHeaderCartCount = () => {
    AppUtils.header.syncCartCount();
  };

  const syncHeaderVendorLink = () => {
    AppUtils.header.syncVendorLink();
  };

  const syncHeaderAuth = () => {
    AppUtils.header.syncAuth();
  };

  const setupHeaderLogout = () => {
    const logoutButton = document.getElementById('header-logout-button');
    if (!logoutButton) return;

    logoutButton.addEventListener('click', () => {
      AppUtils.session.clear();
      syncHeaderAuth();
      syncHeaderVendorLink();
      syncHeaderCartCount();
      window.location.href = '<?php echo $root_path; ?>index.php';
    });
  };

  const closeHeaderMenu = () => {
    const topbar = document.querySelector('.topbar');
    const toggle = document.querySelector('.mobile-nav-toggle');
    if (!topbar || !toggle) return;

    topbar.classList.remove('menu-open');
    toggle.setAttribute('aria-expanded', 'false');
  };

  const setupHeaderMobileMenu = () => {
    const topbar = document.querySelector('.topbar');
    const toggle = document.querySelector('.mobile-nav-toggle');
    const menu = document.getElementById('topbar-menu');
    if (!topbar || !toggle || !menu) return;

    toggle.addEventListener('click', () => {
      const willOpen = !topbar.classList.contains('menu-open');
      topbar.classList.toggle('menu-open', willOpen);
      toggle.setAttribute('aria-expanded', String(willOpen));
    });

    menu.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', closeHeaderMenu);
    });

    window.addEventListener('resize', () => {
      if (window.innerWidth > 760) {
        closeHeaderMenu();
      }
    });
  };
  
  syncHeaderCartCount();
  syncHeaderVendorLink();
  syncHeaderAuth();

  // Set up listeners
  document.addEventListener('DOMContentLoaded', () => {
    syncHeaderCartCount();
    syncHeaderVendorLink();
    syncHeaderAuth();
    setupHeaderLogout();
    setupHeaderMobileMenu();
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
    AppUtils.header.animateCart();
  };

  const showToast = (message, type = 'success') => {
    AppUtils.toast.show(message, type);
  };

  window.showToast = showToast;
</script>
