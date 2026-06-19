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
      }
    };

    createApp({
      setup() {
        const route = ref('home');
        const cartCount = ref(0);
        const apiBase = 'api';
        const vendorTab = ref('dashboard');
        const vendorLoading = ref(false);
        const vendorMessage = ref('');
        const vendorMenu = ref([]);
        const vendorOrders = ref([]);
        const vendorSales = ref({
          summary: { completed_orders: 0, total_sales: 0 },
          popular_items: [],
          status_counts: []
        });
        const selectedOrder = ref(null);
        const selectedOrderItems = ref([]);
        const editingItemId = ref('');
        const menuForm = ref({
          name: '',
          description: '',
          price: '',
          category: 'food'
        });

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
          if (route.value === 'vendor') {
            loadVendorData();
          }
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

        const navItems = computed(() => ['home', 'menu', 'orders', 'profile', 'vendor']);

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

        const formatMoney = (value) => {
          return `RM ${Number(value || 0).toFixed(2)}`;
        };

        const setVendorMessage = (message) => {
          vendorMessage.value = message;
          if (message) {
            window.setTimeout(() => {
              if (vendorMessage.value === message) {
                vendorMessage.value = '';
              }
            }, 3600);
          }
        };

        const apiRequest = async (path, options = {}) => {
          const response = await fetch(`${apiBase}${path}`, {
            headers: {
              'Content-Type': 'application/json',
              ...(options.headers || {})
            },
            ...options
          });
          const data = await response.json().catch(() => ({}));

          if (!response.ok || data.status === 'error') {
            const message = data.errors ? data.errors.join(' ') : (data.message || 'Request failed.');
            throw new Error(message);
          }

          return data;
        };

        const loadVendorMenu = async () => {
          const data = await apiRequest('/vendor/menu');
          vendorMenu.value = data.items || [];
        };

        const loadVendorOrders = async () => {
          const data = await apiRequest('/vendor/orders');
          vendorOrders.value = data.orders || [];
        };

        const loadVendorSales = async () => {
          vendorSales.value = await apiRequest('/vendor/sales');
        };

        const loadVendorData = async () => {
          vendorLoading.value = true;
          try {
            await Promise.all([
              loadVendorMenu(),
              loadVendorOrders(),
              loadVendorSales()
            ]);
          } catch (error) {
            setVendorMessage(error.message || 'Could not load vendor data.');
          } finally {
            vendorLoading.value = false;
          }
        };

        const resetMenuForm = () => {
          editingItemId.value = '';
          menuForm.value = {
            name: '',
            description: '',
            price: '',
            category: 'food'
          };
        };

        const editMenuItem = (item) => {
          editingItemId.value = item.item_id;
          menuForm.value = {
            name: item.name,
            description: item.description || '',
            price: item.price,
            category: item.category
          };
          vendorTab.value = 'menu';
        };

        const saveMenuItem = async () => {
          const payload = {
            ...menuForm.value,
            price: Number(menuForm.value.price)
          };

          try {
            if (editingItemId.value) {
              await apiRequest(`/vendor/menu/${editingItemId.value}`, {
                method: 'PUT',
                body: JSON.stringify(payload)
              });
              setVendorMessage('Menu item updated.');
            } else {
              await apiRequest('/vendor/menu', {
                method: 'POST',
                body: JSON.stringify(payload)
              });
              setVendorMessage('Menu item added.');
            }

            resetMenuForm();
            await Promise.all([loadVendorMenu(), loadVendorSales()]);
          } catch (error) {
            setVendorMessage(error.message);
          }
        };

        const toggleAvailability = async (item) => {
          try {
            await apiRequest(`/vendor/menu/${item.item_id}/availability`, {
              method: 'PATCH',
              body: JSON.stringify({ is_available: !Number(item.is_available) })
            });
            await loadVendorMenu();
          } catch (error) {
            setVendorMessage(error.message);
          }
        };

        const deleteMenuItem = async (item) => {
          if (!window.confirm(`Delete ${item.name}? Items used in orders cannot be deleted.`)) {
            return;
          }

          try {
            await apiRequest(`/vendor/menu/${item.item_id}`, { method: 'DELETE' });
            setVendorMessage('Menu item deleted.');
            await loadVendorMenu();
          } catch (error) {
            setVendorMessage(error.message);
          }
        };

        const viewOrder = async (order) => {
          try {
            const data = await apiRequest(`/vendor/orders/${order.order_id}`);
            selectedOrder.value = data.order;
            selectedOrderItems.value = data.items || [];
            vendorTab.value = 'orders';
          } catch (error) {
            setVendorMessage(error.message);
          }
        };

        const updateOrderStatus = async (order, status) => {
          try {
            await apiRequest(`/vendor/orders/${order.order_id}/status`, {
              method: 'PATCH',
              body: JSON.stringify({ status })
            });
            setVendorMessage('Order status updated.');
            await Promise.all([loadVendorOrders(), loadVendorSales()]);
            if (selectedOrder.value && selectedOrder.value.order_id === order.order_id) {
              selectedOrder.value.status = status;
            }
          } catch (error) {
            setVendorMessage(error.message);
          }
        };

        const pendingOrders = computed(() => vendorOrders.value.filter((order) => order.status !== 'completed' && order.status !== 'cancelled'));
        const availableCount = computed(() => vendorMenu.value.filter((item) => Number(item.is_available)).length);
        const soldOutCount = computed(() => vendorMenu.value.filter((item) => !Number(item.is_available)).length);
        const orderCountByStatus = (status) => vendorOrders.value.filter((order) => order.status === status).length;
        const popularBarWidth = (item) => {
          const quantities = vendorSales.value.popular_items.map((popularItem) => Number(popularItem.total_quantity || 0));
          const maxQuantity = Math.max(1, ...quantities);
          return `${Math.max(10, (Number(item.total_quantity || 0) / maxQuantity) * 100)}%`;
        };

        return {
          addPreviewItem,
          availableCount,
          cartCount,
          currentRoute,
          deleteMenuItem,
          editMenuItem,
          editingItemId,
          formatMoney,
          loadVendorData,
          menuForm,
          navItems,
          orderCountByStatus,
          pendingOrders,
          popularBarWidth,
          previewItems,
          resetMenuForm,
          route,
          routes,
          saveMenuItem,
          selectedOrder,
          selectedOrderItems,
          soldOutCount,
          toggleAvailability,
          updateOrderStatus,
          vendorLoading,
          vendorMenu,
          vendorMessage,
          vendorOrders,
          vendorSales,
          vendorTab,
          viewOrder
        };
      },
      template: `
        <main class="app-shell">
          <header class="topbar">
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
              <a class="pill-button primary" href="#/login">Login</a>
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

          <section v-else-if="route === 'vendor'" class="page vendor-command">
            <div class="vendor-hero-panel">
              <div class="vendor-hero-copy">
                <p class="eyebrow">Vendor module</p>
                <h1>Vendor Command Center</h1>
                <p>Track the queue, switch menu items on or off, and keep completed sales visible without leaving the counter.</p>
              </div>
              <div class="vendor-hero-actions">
                <span class="vendor-live-pill">{{ pendingOrders.length }} active orders</span>
                <button class="vendor-refresh" type="button" @click="loadVendorData">
                  {{ vendorLoading ? 'Refreshing...' : 'Refresh Data' }}
                </button>
              </div>
            </div>

            <div class="vendor-workspace">
              <aside class="vendor-rail" aria-label="Vendor sections">
                <button class="vendor-tab" :class="{ active: vendorTab === 'dashboard' }" type="button" @click="vendorTab = 'dashboard'">
                  <span class="vendor-tab-label">Dashboard</span>
                  <span class="vendor-tab-note">Live queue</span>
                </button>
                <button class="vendor-tab" :class="{ active: vendorTab === 'orders' }" type="button" @click="vendorTab = 'orders'">
                  <span class="vendor-tab-label">Orders</span>
                  <span class="vendor-tab-note">{{ vendorOrders.length }} records</span>
                </button>
                <button class="vendor-tab" :class="{ active: vendorTab === 'menu' }" type="button" @click="vendorTab = 'menu'">
                  <span class="vendor-tab-label">Menu Items</span>
                  <span class="vendor-tab-note">{{ availableCount }} available</span>
                </button>
                <button class="vendor-tab" :class="{ active: vendorTab === 'sales' }" type="button" @click="vendorTab = 'sales'">
                  <span class="vendor-tab-label">Sales Records</span>
                  <span class="vendor-tab-note">{{ formatMoney(vendorSales.summary.total_sales) }}</span>
                </button>
              </aside>

              <div class="vendor-main">
                <p v-if="vendorMessage" class="vendor-alert">{{ vendorMessage }}</p>

                <template v-if="vendorTab === 'dashboard'">
                  <div class="vendor-grid">
                    <article class="metric-card hot">
                      <span>Open orders</span>
                      <strong>{{ pendingOrders.length }}</strong>
                      <em class="metric-mini">Needs attention</em>
                    </article>
                    <article class="metric-card">
                      <span>Completed orders</span>
                      <strong>{{ vendorSales.summary.completed_orders || 0 }}</strong>
                      <em class="metric-mini">Closed tickets</em>
                    </article>
                    <article class="metric-card green">
                      <span>Total sales</span>
                      <strong>{{ formatMoney(vendorSales.summary.total_sales) }}</strong>
                      <em class="metric-mini">Completed only</em>
                    </article>
                    <article class="metric-card">
                      <span>Menu health</span>
                      <strong>{{ availableCount }}</strong>
                      <em class="metric-mini">{{ soldOutCount }} sold out</em>
                    </article>
                  </div>

                  <div class="vendor-split">
                    <div class="vendor-panel">
                      <div class="vendor-panel-head">
                        <div>
                          <h2>Incoming Orders</h2>
                          <span class="vendor-panel-subtitle">Latest active tickets</span>
                        </div>
                        <button class="small-action primary" type="button" @click="vendorTab = 'orders'">View All</button>
                      </div>
                      <table class="vendor-table">
                        <thead>
                          <tr>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="order in pendingOrders.slice(0, 6)" :key="order.order_id">
                            <td><strong>{{ order.order_id }}</strong></td>
                            <td>{{ order.customer_name }}</td>
                            <td>{{ formatMoney(order.total_amount) }}</td>
                            <td><span class="status-pill" :class="order.status">{{ order.status }}</span></td>
                            <td><button class="small-action" type="button" @click="viewOrder(order)">Details</button></td>
                          </tr>
                          <tr v-if="pendingOrders.length === 0">
                            <td colspan="5" class="muted-text">No active orders right now.</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>

                    <div class="vendor-panel">
                      <div class="vendor-panel-head">
                        <div>
                          <h2>Status Queue</h2>
                          <span class="vendor-panel-subtitle">Kitchen flow</span>
                        </div>
                      </div>
                      <div class="queue-list">
                        <div class="queue-row">
                          <div>
                            <strong>Pending</strong>
                            <span>Waiting to prepare</span>
                          </div>
                          <b class="queue-count">{{ orderCountByStatus('pending') }}</b>
                        </div>
                        <div class="queue-row">
                          <div>
                            <strong>Preparing</strong>
                            <span>Currently cooking</span>
                          </div>
                          <b class="queue-count">{{ orderCountByStatus('preparing') }}</b>
                        </div>
                        <div class="queue-row">
                          <div>
                            <strong>Ready</strong>
                            <span>Ready for pickup</span>
                          </div>
                          <b class="queue-count">{{ orderCountByStatus('ready') }}</b>
                        </div>
                      </div>
                    </div>
                  </div>
                </template>

                <template v-if="vendorTab === 'orders'">
                  <div v-if="selectedOrder" class="order-detail">
                    <h3>Order {{ selectedOrder.order_id }} - {{ selectedOrder.customer_name }}</h3>
                    <p class="muted-text">{{ selectedOrder.customer_phone }} - {{ formatMoney(selectedOrder.total_amount) }}</p>
                    <ul class="order-detail-list">
                      <li v-for="item in selectedOrderItems" :key="item.order_item_id">
                        <span>{{ item.quantity }}x {{ item.name }}</span>
                        <strong>{{ formatMoney(item.subtotal) }}</strong>
                      </li>
                    </ul>
                  </div>

                  <div class="vendor-panel">
                    <div class="vendor-panel-head">
                      <div>
                        <h2>Order Dashboard</h2>
                        <span class="vendor-panel-subtitle">{{ vendorOrders.length }} total orders</span>
                      </div>
                    </div>
                    <table class="vendor-table">
                      <thead>
                        <tr>
                          <th>Order</th>
                          <th>Customer</th>
                          <th>Phone</th>
                          <th>Items</th>
                          <th>Total</th>
                          <th>Status</th>
                          <th>Update</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="order in vendorOrders" :key="order.order_id">
                          <td><button class="small-action" type="button" @click="viewOrder(order)">{{ order.order_id }}</button></td>
                          <td>{{ order.customer_name }}</td>
                          <td>{{ order.customer_phone }}</td>
                          <td>{{ order.item_count }}</td>
                          <td>{{ formatMoney(order.total_amount) }}</td>
                          <td><span class="status-pill" :class="order.status">{{ order.status }}</span></td>
                          <td>
                            <select class="vendor-select" :value="order.status" @change="updateOrderStatus(order, $event.target.value)">
                              <option value="pending">pending</option>
                              <option value="preparing">preparing</option>
                              <option value="ready">ready</option>
                              <option value="completed">completed</option>
                              <option value="cancelled">cancelled</option>
                            </select>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </template>

                <template v-if="vendorTab === 'menu'">
                  <div class="vendor-panel">
                    <div class="vendor-panel-head">
                      <div>
                        <h2>{{ editingItemId ? 'Update Menu Item' : 'Add Menu Item' }}</h2>
                        <span class="vendor-panel-subtitle">{{ availableCount }} available - {{ soldOutCount }} sold out</span>
                      </div>
                      <button v-if="editingItemId" class="small-action" type="button" @click="resetMenuForm">Cancel Edit</button>
                    </div>
                    <form class="vendor-form" @submit.prevent="saveMenuItem">
                      <label>
                        Name
                        <input class="vendor-input" v-model="menuForm.name" required>
                      </label>
                      <label>
                        Price
                        <input class="vendor-input" v-model="menuForm.price" type="number" min="0.01" step="0.01" required>
                      </label>
                      <label>
                        Category
                        <select class="vendor-select" v-model="menuForm.category" required>
                          <option value="food">food</option>
                          <option value="drink">drink</option>
                        </select>
                      </label>
                      <label class="wide">
                        Description
                        <textarea class="vendor-textarea" v-model="menuForm.description"></textarea>
                      </label>
                      <div class="inline-actions wide">
                        <button class="small-action primary" type="submit">{{ editingItemId ? 'Save Changes' : 'Add Item' }}</button>
                        <button class="small-action" type="button" @click="resetMenuForm">Clear</button>
                      </div>
                    </form>

                    <table class="vendor-table">
                      <thead>
                        <tr>
                          <th>Item</th>
                          <th>Category</th>
                          <th>Price</th>
                          <th>Availability</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="item in vendorMenu" :key="item.item_id">
                          <td>
                            <strong>{{ item.name }}</strong>
                            <div class="muted-text">{{ item.item_id }}</div>
                          </td>
                          <td>{{ item.category }}</td>
                          <td>{{ formatMoney(item.price) }}</td>
                          <td>
                            <span class="status-pill" :class="Number(item.is_available) ? 'available' : 'sold-out'">
                              {{ Number(item.is_available) ? 'available' : 'sold out' }}
                            </span>
                          </td>
                          <td>
                            <div class="inline-actions">
                              <button class="small-action" type="button" @click="editMenuItem(item)">Edit</button>
                              <button class="small-action" type="button" @click="toggleAvailability(item)">
                                {{ Number(item.is_available) ? 'Sold Out' : 'Available' }}
                              </button>
                              <button class="danger-action" type="button" @click="deleteMenuItem(item)">Delete</button>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </template>

                <template v-if="vendorTab === 'sales'">
                  <div class="vendor-grid">
                    <article class="metric-card green">
                      <span>Completed orders</span>
                      <strong>{{ vendorSales.summary.completed_orders || 0 }}</strong>
                      <em class="metric-mini">Recorded sales</em>
                    </article>
                    <article class="metric-card hot">
                      <span>Total completed sales</span>
                      <strong>{{ formatMoney(vendorSales.summary.total_sales) }}</strong>
                      <em class="metric-mini">Revenue snapshot</em>
                    </article>
                  </div>

                  <div class="vendor-split">
                    <div class="vendor-panel">
                      <div class="vendor-panel-head">
                        <div>
                          <h2>Popular Items</h2>
                          <span class="vendor-panel-subtitle">Completed orders only</span>
                        </div>
                      </div>
                      <div class="popular-list">
                        <div class="popular-row" v-for="item in vendorSales.popular_items" :key="item.item_id">
                          <div>
                            <strong>{{ item.name }}</strong>
                            <span>{{ item.category }} - {{ item.total_quantity }} sold - {{ formatMoney(item.total_sales) }}</span>
                            <div class="popular-bar"><span :style="{ width: popularBarWidth(item) }"></span></div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="vendor-panel">
                      <div class="vendor-panel-head">
                        <div>
                          <h2>Status Mix</h2>
                          <span class="vendor-panel-subtitle">All order records</span>
                        </div>
                      </div>
                      <div class="queue-list">
                        <div class="queue-row" v-for="status in vendorSales.status_counts" :key="status.status">
                          <div>
                            <strong>{{ status.status }}</strong>
                            <span>orders</span>
                          </div>
                          <b class="queue-count">{{ status.total }}</b>
                        </div>
                      </div>
                    </div>
                  </div>
                </template>
              </div>
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

          <footer class="footer">
            Universal Sambal base UI. Vue frontend linked to individual customer PHP pages.
          </footer>
        </main>
      `
    }).mount('#app');
  </script>
</body>

</html>
