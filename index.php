<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Universal Sambal</title>
  <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
  <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <div id="app"></div>

  <script>
    const { createApp, computed, onMounted, onUnmounted, ref } = Vue;

    const routes = {
      home: {
        label: 'Home',
        title: 'Universal Sambal',
        path: 'index.php'
      },
      menu: {
        label: 'Menu',
        title: 'Menu',
        path: 'src/customer/menu.php'
      },
      orders: {
        label: 'Orders',
        title: 'Orders',
        path: 'src/customer/my_orders.php'
      },
      profile: {
        label: 'Profile',
        title: 'Profile',
        path: '#/profile'
      },
      vendor: {
        label: 'Vendor',
        title: 'Vendor Dashboard',
        path: '#/vendor'
      },
      login: {
        label: 'Login',
        title: 'Login',
        path: '#/login'
      }
    };

    createApp({
      setup() {
        const route = ref('home');
        const cartCount = ref(0);
        const currentUser = ref(JSON.parse(localStorage.getItem('universalSambalUser') || 'null'));
        const loginForm = ref({
          identifier: '',
          password: ''
        });
        const loginError = ref('');
        const loginLoading = ref(false);

        // Load cart count from localStorage
        const updateCartCount = () => {
          try {
            const savedCart = localStorage.getItem('cart');
            if (savedCart) {
              const cart = JSON.parse(savedCart);
              cartCount.value = Object.values(cart).reduce((sum, qty) => sum + qty, 0);
            } else {
              cartCount.value = 0;
            }
          } catch (e) {
            console.error(e);
          }
        };

        const updateRoute = () => {
          const nextRoute = window.location.hash.replace('#/', '') || 'home';
          route.value = ['profile', 'vendor', 'login'].includes(nextRoute) ? nextRoute : 'home';
        };

        onMounted(() => {
          updateRoute();
          updateCartCount();
          window.addEventListener('hashchange', updateRoute);
          window.addEventListener('storage', updateCartCount);
          // Check storage changes periodically
          setInterval(updateCartCount, 1000);
        });

        onUnmounted(() => {
          window.removeEventListener('hashchange', updateRoute);
          window.removeEventListener('storage', updateCartCount);
        });

        const currentRoute = computed(() => {
          const r = route.value;
          if (r === 'home') return routes.home;
          return {
            label: r.charAt(0).toUpperCase() + r.slice(1),
            title: r.charAt(0).toUpperCase() + r.slice(1),
            note: 'Empty placeholder page.'
          };
        });

        const navItems = computed(() => ['home', 'menu', 'orders', 'profile']);

        const previewItems = [
          {
            item_id: 'F001',
            name: 'Ayam Geprek Sambal Merah',
            category: 'Food',
            price: 'RM 11.20',
            image: 'images/food/ayam_merah.png'
          },
          {
            item_id: 'F002',
            name: 'Ayam Geprek Sambal Hijau',
            category: 'Food',
            price: 'RM 11.20',
            image: 'images/food/ayam_hijau.png'
          },
          {
            item_id: 'D005',
            name: 'Jus Tembikai Susu Ice',
            category: 'Drink',
            price: 'RM 6.40',
            image: 'images/drink/tembikai_susu.png'
          }
        ];

        const addPreviewItem = (itemId) => {
          try {
            const savedCart = localStorage.getItem('cart');
            let cart = savedCart ? JSON.parse(savedCart) : {};
            cart[itemId] = (cart[itemId] || 0) + 1;
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
          } catch (e) {
            console.error(e);
          }
        };

        const login = async () => {
          loginError.value = '';
          loginLoading.value = true;

          try {
            const response = await fetch('api/login', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({
                name: loginForm.value.identifier,
                user_id: loginForm.value.identifier,
                password: loginForm.value.password
              })
            });
            const payload = await response.json();

            if (!response.ok) {
              throw new Error(payload.error || 'Login failed.');
            }

            currentUser.value = payload.user;
            localStorage.setItem('universalSambalUser', JSON.stringify(payload.user));
            loginForm.value.password = '';
            window.location.hash = payload.user.role === 'admin' ? '#/vendor' : '#/profile';
          } catch (error) {
            loginError.value = error.message || 'Login failed.';
          } finally {
            loginLoading.value = false;
          }
        };

        const logout = () => {
          currentUser.value = null;
          localStorage.removeItem('universalSambalUser');
          window.location.hash = '#/login';
        };

        return {
          addPreviewItem,
          cartCount,
          currentRoute,
          currentUser,
          login,
          loginError,
          loginForm,
          loginLoading,
          logout,
          navItems,
          previewItems,
          route,
          routes
        };
      },
      template: `
        <main class="app-shell" :class="{ 'auth-only-shell': route === 'login' }">
          <header v-if="route !== 'login'" class="topbar">
            <a class="brand" href="index.php" aria-label="Universal Sambal home">
              <span class="brand-mark">US</span>
              <span>Universal Sambal</span>
            </a>

            <nav class="nav" aria-label="Main navigation">
              <a
                v-for="item in navItems"
                :key="item"
                class="nav-link"
                :class="{ active: route === item || (item === 'home' && route === 'home') }"
                :href="routes[item] ? routes[item].path : '#/' + item"
              >
                {{ routes[item] ? routes[item].label : item }}
              </a>
            </nav>

            <div class="top-actions">
              <a class="pill-button cart-button" href="src/customer/cart.php">
                <svg 
                  xmlns="http://w3.org" 
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
                <span>{{ cartCount }}</span>
              </a>
              <button v-if="currentUser" class="pill-button" type="button" @click="logout">Logout</button>
              <a v-else class="pill-button primary" href="#/login">Login</a>
            </div>
          </header>

          <template v-if="route === 'home'">
            <section class="landing-top">
              <div class="page hero">
                <div class="hero-copy">
                  <p class="eyebrow">Online food and drink ordering</p>
                  <h1>Want to get constipated? Try ours</h1>
                  <p class="hero-text">
                    A base customer interface for Universal Sambal. Browse the menu, add items to cart,
                    track orders, and leave the real links ready for the backend module.
                  </p>
                  <div class="hero-actions">
                    <a class="cta" href="src/customer/menu.php">Explore Menu</a>
                  </div>
                </div>

                <div class="hero-media">
                  <div class="food-stage">
                    <div class="food-backdrop"></div>
                    <img class="hero-food" src="images/food/test.png" alt="Ayam geprek sambal merah">
                  </div>
                </div>
              </div>
            </section>

            <section class="promo-section">
              <div class="page section">
                <div class="promo-grid">
                  <div class="promo-stack">
                    <article class="promo">
                      <div>
                        <p class="eyebrow">Made fresh</p>
                        <h3>Order ahead during peak hours</h3>
                        <p>Placeholder campaign panel for future offers or announcements.</p>
                        <a class="cta" href="src/customer/my_orders.php">Track Order</a>
                      </div>
                      <lottie-player
                        class="promo-visual-rider"
                        src="animations/rider.json"
                        background="transparent"
                        speed="1"
                        loop
                        autoplay>
                      </lottie-player>
                    </article>

                    <article class="promo green">
                      <div>
                        <p class="eyebrow">Customer Menu</p>
                        <h3>Browse Food & Drinks</h3>
                        <p>Explore our signature spicy dishes, authentic Indonesian rice meals, and fresh beverages.</p>
                        <a class="cta" href="src/customer/menu.php">View Menu</a>
                      </div>
                      <img class="cutout promo-img-menu" src="images/food/separate.png" alt="Ayam geprek sambal hijau">
                    </article>
                  </div>

                  <article class="promo red">
                    <div>
                      <p class="eyebrow">Fresh & Cold</p>
                      <h3>Quench Your Thirst</h3>
                      <p>Discover our refreshing selection of hand-crafted fruit juices, milkshakes, and cold teas to cool the heat.</p>
                      <a class="cta" href="src/customer/menu.php">Explore Drinks</a>
                    </div>
                    <img class="cutout promo-img-splash" src="images/assets/splash.png" alt="Fruit drinks splash">
                  </article>
                </div>
              </div>
            </section>

            <section class="category-band">
              <div class="page section category-layout">
                <div class="section-head">
                  <div>
                    <p class="eyebrow">System modules</p>
                    <h2>Base navigation for customer and vendor flow</h2>
                  </div>
                </div>

                <div class="category-art">
                  <img src="images/assets/top_view.png" alt="Rice meal">
                  <a href="src/customer/menu.php" class="float-tile tile-food">
                    <strong>Menu</strong>
                    <span>Browse food and drinks</span>
                  </a>
                  <a href="src/customer/cart.php" class="float-tile tile-drink">
                    <strong>Cart</strong>
                    <span>Adjust quantities</span>
                  </a>
                  <a href="src/customer/my_orders.php" class="float-tile tile-cart">
                    <strong>Orders</strong>
                    <span>Status and history</span>
                  </a>
                  <a href="#/vendor" class="float-tile tile-order">
                    <strong>Vendor</strong>
                    <span>Manage records</span>
                  </a>
                </div>
              </div>
            </section>

            <section class="page section">
              <div class="section-head">
                <div>
                  <p class="eyebrow">Preview cards</p>
                  <h2>Top Picks</h2>
                </div>
                <p>Add these top-selling meals and drinks straight to your cart.</p>
              </div>

              <div class="card-grid">
                <article class="food-card" v-for="item in previewItems" :key="item.item_id">
                  <img :src="item.image" :alt="item.name">
                  <div class="food-card-body">
                    <h3>{{ item.name }}</h3>
                    <p class="meta">{{ item.category }} preview item</p>
                    <div class="price-row">
                      <span>{{ item.price }}</span>
                      <a href="src/customer/menu.php" class="cta" style="min-height: 36px; padding: 0 16px; font-size: 13px; width: auto; margin-top: 0;">Order Now</a>
                    </div>
                  </div>
                </article>
              </div>
            </section>
          </template>

          <section v-else-if="route === 'login'" class="page route-panel auth-page">
            <div class="auth-shell">
              <div class="auth-copy">
                <div class="auth-brand">
                  <span class="brand-mark">US</span>
                  <span>Universal Sambal</span>
                </div>
                <h1>Welcome back</h1>

                <div class="auth-food-stage">
                  <img src="images/food/ayam_merah.png" alt="Ayam geprek sambal merah">
                </div>
              </div>

              <form class="auth-card" @submit.prevent="login">
                <div class="auth-card-head">
                  <h2>Sign in</h2>
                </div>
                <div class="form-field">
                  <label for="login-identifier">Name or User ID</label>
                  <input
                    id="login-identifier"
                    v-model.trim="loginForm.identifier"
                    type="text"
                    autocomplete="username"
                    placeholder="Enter your name or user ID"
                    required
                  >
                </div>

                <div class="form-field">
                  <label for="login-password">Password</label>
                  <input
                    id="login-password"
                    v-model="loginForm.password"
                    type="password"
                    autocomplete="current-password"
                    placeholder="Enter your password"
                    required
                  >
                </div>

                <p v-if="loginError" class="form-error">{{ loginError }}</p>

                <button class="auth-submit" type="submit" :disabled="loginLoading">
                  {{ loginLoading ? 'Signing in...' : 'Sign in' }}
                </button>
              </form>
            </div>
          </section>

          <section v-else class="page route-panel">
            <div class="empty-panel">
              <div>
                <p class="eyebrow">Placeholder route</p>
                <h1>{{ currentRoute.title }}</h1>
                <p>{{ currentRoute.note }}</p>
                <div class="link-list">
                  <a class="pill-button primary" href="index.php">Back Home</a>
                  <a class="pill-button" href="src/customer/menu.php">Menu</a>
                  <a class="pill-button" href="src/customer/cart.php">Cart</a>
                  <a class="pill-button" href="src/customer/my_orders.php">Orders</a>
                </div>
              </div>
            </div>
          </section>

          <footer v-if="route !== 'login'" class="footer">
            Universal Sambal base UI. Vue frontend linked to individual customer PHP pages.
          </footer>
        </main>
      `
    }).mount('#app');
  </script>
</body>

</html>


