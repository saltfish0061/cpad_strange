const { createApp, computed, onMounted, onUnmounted, ref } = Vue;

const routes = {
  home: {
    label: 'Home',
    title: 'Universal Sambal',
    note: 'Welcome page and main discovery area.'
  },
  menu: {
    label: 'Menu',
    title: 'Menu',
    note: 'Browse available food and drinks from Universal Sambal.'
  },
  cart: {
    label: 'Cart',
    title: 'Cart',
    note: 'Review your selected items before checkout.'
  },
  orders: {
    label: 'Orders',
    title: 'Orders',
    note: 'Track active orders and view your order history.'
  },
  profile: {
    label: 'Profile',
    title: 'Profile',
    note: 'Manage your account and contact details.'
  },
  vendor: {
    label: 'Vendor',
    title: 'Vendor Dashboard',
    note: 'Manage menu availability, incoming orders, and sales records.'
  },
  login: {
    label: 'Login',
    title: 'Login',
    note: 'Sign in to continue your Universal Sambal experience.'
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

    const updateRoute = () => {
      const nextRoute = window.location.hash.replace('#/', '') || 'home';
      route.value = routes[nextRoute] ? nextRoute : 'home';
    };

    onMounted(() => {
      updateRoute();
      window.addEventListener('hashchange', updateRoute);
    });

    onUnmounted(() => {
      window.removeEventListener('hashchange', updateRoute);
    });

    const currentRoute = computed(() => routes[route.value]);
    const navItems = computed(() => ['home', 'menu', 'orders', 'profile', 'vendor']);

    const previewItems = [
      {
        name: 'Ayam Geprek Sambal Merah',
        category: 'Food',
        price: 'RM 11.20',
        image: 'images/top picks ayam merah.png'
      },
      {
        name: 'Ayam Geprek Sambal Hijau',
        category: 'Food',
        price: 'RM 11.20',
        image: 'images/top picks ayam hijau.png'
      },
      {
        name: 'Jus Tembikai Susu',
        category: 'Drink',
        price: 'RM 6.40',
        image: 'images/tembikai susu.png'
      }
    ];

    const addPreviewItem = () => {
      cartCount.value += 1;
    };

    const login = async () => {
      loginError.value = '';
      loginLoading.value = true;

      try {
        const response = await fetch('src/main.php/api/login', {
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
        window.location.hash = payload.user.role === 'admin' ? '#/vendor' : '#/menu';
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
        <a class="brand" href="#/home" aria-label="Universal Sambal home">
          <span class="brand-mark">US</span>
          <span>Universal Sambal</span>
        </a>

        <nav class="nav" aria-label="Main navigation">
          <a
            v-for="item in navItems"
            :key="item"
            class="nav-link"
            :class="{ active: route === item }"
            :href="'#/' + item"
          >
            {{ routes[item].label }}
          </a>
        </nav>

        <div class="top-actions">
          <a class="pill-button cart-button" href="#/cart">
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
              <h1>Fresh sambal favorites, ready when you are</h1>
              <p class="hero-text">
                Browse signature ayam geprek, refreshing drinks, and place your order before
                the lunch rush begins.
              </p>
              <div class="hero-actions">
                <a class="cta" href="#/menu">Explore Menu</a>
              </div>
            </div>

            <div class="hero-media">
              <div class="food-stage">
                <div class="food-backdrop"></div>
                <img class="hero-food" src="images/test.png" alt="Ayam geprek sambal merah">
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
                    <p>Skip the counter queue and collect your meal when it is ready.</p>
                    <a class="cta" href="#/orders">Track Order</a>
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
                    <p class="eyebrow">Customer menu</p>
                    <h3>Browse food and drinks</h3>
                    <p>Explore spicy mains, rice sets, and cold drinks in one place.</p>
                    <a class="cta" href="#/menu">View Menu</a>
                  </div>
                  <img class="cutout promo-img-menu" src="images/separate.png" alt="Ayam geprek sambal hijau">
                </article>
              </div>

              <article class="promo red">
                <div>
                  <p class="eyebrow">Juices for you</p>
                  <h3>Incoming orders and menu status</h3>
                  <p>Pair every sambal meal with fresh fruit drinks made to order.</p>
                  <a class="cta" href="#/menu">I want it</a>
                </div>
                <img class="cutout promo-img-splash" src="images/splash.png" alt="Fruit drinks splash">
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
              <img src="images/top view.png" alt="Rice meal">
              <div class="float-tile tile-food">
                <strong>Menu</strong>
                <span>Browse food and drinks</span>
              </div>
              <div class="float-tile tile-drink">
                <strong>Cart</strong>
                <span>Adjust quantities</span>
              </div>
              <div class="float-tile tile-cart">
                <strong>Orders</strong>
                <span>Status and history</span>
              </div>
              <div class="float-tile tile-order">
                <strong>Vendor</strong>
                <span>Manage records</span>
              </div>
            </div>
          </div>
        </section>

        <section class="page section">
          <div class="section-head">
            <div>
              <p class="eyebrow">Preview cards</p>
              <h2>Top picks shell</h2>
            </div>
            <p>Popular picks from the Universal Sambal kitchen.</p>
          </div>

          <div class="card-grid">
            <article class="food-card" v-for="item in previewItems" :key="item.name">
              <img :src="item.image" :alt="item.name">
              <div class="food-card-body">
                <h3>{{ item.name }}</h3>
                <p class="meta">{{ item.category }} preview item</p>
                <div class="price-row">
                  <span>{{ item.price }}</span>
                  <button class="add-button" type="button" @click="addPreviewItem" aria-label="Add preview item">+</button>
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
              <img src="images/top picks ayam merah.png" alt="Ayam geprek sambal merah">
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
              {{ loginLoading ? 'Logging in...' : 'Login' }}
            </button>
          </form>
        </div>
      </section>

      <section v-else class="page route-panel">
        <div class="empty-panel">
          <div>
            <p class="eyebrow">Universal Sambal</p>
            <h1>{{ currentRoute.title }}</h1>
            <p>{{ currentRoute.note }}</p>
            <div class="link-list">
              <a class="pill-button primary" href="#/home">Back Home</a>
              <a class="pill-button" href="#/menu">Menu</a>
              <a class="pill-button" href="#/cart">Cart</a>
              <a class="pill-button" href="#/orders">Orders</a>
            </div>
          </div>
        </div>
      </section>

      <footer v-if="route !== 'login'" class="footer">
        Universal Sambal base UI. Vue frontend now, Slim API routes later.
      </footer>
    </main>
  `
}).mount('#app');
