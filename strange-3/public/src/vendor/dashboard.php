<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Vendor Dashboard - Universal Sambal</title>
  <script src="../../js/vue.global.prod.js"></script>
  <script src="../../js/app-utils.js"></script>
  <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
  <div id="app">
        <main class="app-shell">
          <?php
            $root_path = '../../';
            $active_page = 'vendor';
            include '../../libs/customer_header.php';
          ?>

          <section class="page vendor-command">
            <div class="section-head vendor-command-head">
              <div>
                <p class="eyebrow">Vendor Dashboard</p>
                <h2>Vendor Command Center</h2>
              </div>
              <div class="vendor-head-actions">
                <a class="apk-page-shortcut" href="../customer/profile.php" aria-label="Open profile">
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="8" r="4"></circle>
                    <path d="M4 21a8 8 0 0 1 16 0"></path>
                  </svg>
                  <span>Profile</span>
                </a>
                <span class="vendor-live-pill">{{ pendingOrders.length }} active orders</span>
                <button class="vendor-refresh" type="button" @click="loadVendorData" :disabled="vendorLoading" :aria-label="vendorLoading ? 'Refreshing data' : 'Refresh data'" :title="vendorLoading ? 'Refreshing...' : 'Refresh data'">
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M20 6v5h-5"></path>
                    <path d="M4 18v-5h5"></path>
                    <path d="M18.4 9A7 7 0 0 0 6.2 6.8L4 9"></path>
                    <path d="M5.6 15A7 7 0 0 0 17.8 17.2L20 15"></path>
                  </svg>
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
                  <span class="vendor-tab-note">{{ availableCount }} on stock</span>
                </button>
                <button class="vendor-tab" :class="{ active: vendorTab === 'sales' }" type="button" @click="vendorTab = 'sales'">
                  <span class="vendor-tab-label">Sales Records</span>
                  <span class="vendor-tab-note">{{ formatMoney(vendorSales.summary.total_sales) }}</span>
                </button>
              </aside>

              <div class="vendor-main">
                <p v-if="vendorMessage" class="vendor-alert">{{ vendorMessage }}</p>

                <div v-if="showVendorInitialLoading" class="loading-surface">
                  <div class="loading-card" role="status" aria-live="polite">
                    <span class="loading-spinner" aria-hidden="true"></span>
                    <strong>Loading</strong>
                  </div>
                  <div class="loading-skeleton-grid" aria-hidden="true">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                  </div>
                </div>

                <div v-else-if="vendorTab === 'dashboard'">
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
                          <div><strong>Pending</strong><span>Waiting to prepare</span></div>
                          <b class="queue-count">{{ orderCountByStatus('pending') }}</b>
                        </div>
                        <div class="queue-row">
                          <div><strong>Preparing</strong><span>Currently cooking</span></div>
                          <b class="queue-count">{{ orderCountByStatus('preparing') }}</b>
                        </div>
                        <div class="queue-row">
                          <div><strong>Ready</strong><span>Ready for pickup</span></div>
                          <b class="queue-count">{{ orderCountByStatus('ready') }}</b>
                        </div>
                        <div class="queue-row">
                          <div><strong>On the way</strong><span>Out for delivery</span></div>
                          <b class="queue-count">{{ orderCountByStatus('on_the_way') }}</b>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-else-if="vendorTab === 'orders'">
                  <div v-if="selectedOrder" class="order-detail">
                    <h3>Order {{ selectedOrder.order_id }} - {{ selectedOrder.customer_name }}</h3>
                    <p class="muted-text">
                      {{ selectedOrder.customer_phone }} - {{ formatDeliveryMethod(selectedOrder.delivery_method) }} - {{ formatMoney(selectedOrder.total_amount) }}
                    </p>
                    <p v-if="selectedOrder.customer_address" class="muted-text">
                      <strong>Address:</strong> {{ selectedOrder.customer_address }}
                    </p>
                    <p v-if="selectedOrder.order_note" class="muted-text">
                      <strong>Note:</strong> {{ selectedOrder.order_note }}
                    </p>
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
                        <span class="vendor-panel-subtitle">{{ filteredVendorOrders.length }} of {{ vendorOrders.length }} orders</span>
                      </div>
                      <button class="vendor-filter-toggle" :class="{ active: orderFiltersOpen }" type="button" @click="orderFiltersOpen = !orderFiltersOpen" aria-label="Order filters" title="Filter and sort orders">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                          <path d="M4 7h16"></path>
                          <path d="M7 12h10"></path>
                          <path d="M10 17h4"></path>
                        </svg>
                      </button>
                    </div>
                    <div v-if="orderFiltersOpen" class="vendor-filter-bar">
                      <label>
                        Status
                        <select class="vendor-select" v-model="orderStatusFilter">
                          <option value="all">All orders</option>
                          <option value="pending">Pending</option>
                          <option value="preparing">Preparing</option>
                          <option value="ready">Ready</option>
                          <option value="on_the_way">On the way</option>
                          <option value="completed">Completed</option>
                          <option value="cancelled">Cancelled</option>
                        </select>
                      </label>
                      <label>
                        Sort
                        <select class="vendor-select" v-model="orderSort">
                          <option value="newest">Newest first</option>
                          <option value="oldest">Oldest first</option>
                          <option value="highest">Highest total</option>
                          <option value="lowest">Lowest total</option>
                        </select>
                      </label>
                    </div>
                    <table class="vendor-table">
                      <thead>
                        <tr>
                          <th>Order</th>
                          <th>Customer</th>
                          <th>Phone</th>
                          <th>Method</th>
                          <th>Items</th>
                          <th>Total</th>
                          <th>Status</th>
                          <th>Update</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="order in filteredVendorOrders" :key="order.order_id">
                          <td><button class="small-action" type="button" @click="viewOrder(order)">{{ order.order_id }}</button></td>
                          <td>{{ order.customer_name }}</td>
                          <td>{{ order.customer_phone }}</td>
                          <td>{{ formatDeliveryMethod(order.delivery_method) }}</td>
                          <td>{{ order.item_count }}</td>
                          <td>{{ formatMoney(order.total_amount) }}</td>
                          <td><span class="status-pill" :class="order.status">{{ formatOrderStatus(order.status) }}</span></td>
                          <td>
                            <select class="vendor-select" :value="order.status" @change="updateOrderStatus(order, $event.target.value)">
                              <option v-for="status in statusOptionsForOrder(order)" :key="status" :value="status">
                                {{ formatOrderStatus(status) }}
                              </option>
                            </select>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div v-else-if="vendorTab === 'menu'">
                  <div class="vendor-panel">
                    <div class="vendor-panel-head">
                      <div>
                        <h2>{{ editingItemId ? 'Update Menu Item' : 'Add Menu Item' }}</h2>
                        <span class="vendor-panel-subtitle">{{ availableCount }} on stock - {{ soldOutCount }} sold out</span>
                      </div>
                      <div class="inline-actions">
                        <button class="vendor-filter-toggle" :class="{ active: menuFiltersOpen }" type="button" @click="menuFiltersOpen = !menuFiltersOpen" aria-label="Menu filters" title="Filter and sort menu items">
                          <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 7h16"></path>
                            <path d="M7 12h10"></path>
                            <path d="M10 17h4"></path>
                          </svg>
                        </button>
                        <button v-if="editingItemId" class="small-action" type="button" @click="resetMenuForm">Cancel Edit</button>
                      </div>
                    </div>
                    <form class="vendor-form" @submit.prevent="saveMenuItem">
                      <label>Name<input class="vendor-input" v-model="menuForm.name" required></label>
                      <label>Price<input class="vendor-input" v-model="menuForm.price" type="number" min="0.01" step="0.01" required></label>
                      <label>
                        Category
                        <select class="vendor-select" v-model="menuForm.category" required>
                          <option value="food">food</option>
                          <option value="drink">drink</option>
                        </select>
                      </label>
                      <label class="wide">Description<textarea class="vendor-textarea" v-model="menuForm.description"></textarea></label>
                      <div class="inline-actions wide">
                        <button class="small-action primary" type="submit">{{ editingItemId ? 'Save Changes' : 'Add Item' }}</button>
                        <button class="small-action" type="button" @click="resetMenuForm">Clear</button>
                      </div>
                    </form>
                    <div v-if="menuFiltersOpen" class="vendor-filter-bar">
                      <label>
                        Stock
                        <select class="vendor-select" v-model="menuStockFilter">
                          <option value="all">All stock</option>
                          <option value="available">On stock</option>
                          <option value="sold-out">Sold out</option>
                        </select>
                      </label>
                      <label>
                        Category
                        <select class="vendor-select" v-model="menuCategoryFilter">
                          <option value="all">All categories</option>
                          <option value="food">Food</option>
                          <option value="drink">Drink</option>
                        </select>
                      </label>
                      <label>
                        Sort
                        <select class="vendor-select" v-model="menuSort">
                          <option value="name">Name A-Z</option>
                          <option value="price-high">Price high-low</option>
                          <option value="price-low">Price low-high</option>
                        </select>
                      </label>
                    </div>
                    <table class="vendor-table menu-items-table">
                      <thead>
                        <tr><th>Item</th><th>Category</th><th>Price</th><th>Availability</th><th>Actions</th></tr>
                      </thead>
                      <tbody>
                        <tr v-for="item in filteredVendorMenu" :key="item.item_id">
                          <td><strong>{{ item.name }}</strong><div class="muted-text">{{ item.item_id }}</div></td>
                          <td>{{ item.category }}</td>
                          <td>{{ formatMoney(item.price) }}</td>
                          <td><span class="status-pill" :class="Number(item.is_available) ? 'available' : 'sold-out'">{{ Number(item.is_available) ? 'on stock' : 'sold out' }}</span></td>
                          <td>
                            <div class="inline-actions">
                              <button class="small-action" type="button" @click="editMenuItem(item)">Edit</button>
                              <button class="small-action" type="button" @click="toggleAvailability(item)">{{ Number(item.is_available) ? 'Sold Out' : 'On Stock' }}</button>
                              <button class="danger-action" type="button" @click="deleteMenuItem(item)">Delete</button>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div v-else-if="vendorTab === 'sales'">
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

                  <div class="vendor-panel" style="margin-top: 18px;">
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
                </div>
              </div>
            </div>
          </section>

          <?php include '../../libs/footer.php'; ?>
        </main>
  </div>

  <script>
    const { createApp, computed, onMounted, ref, watch } = Vue;
    let currentUser = null;

    try {
      const savedUser = localStorage.getItem('currentUser');
      currentUser = savedUser ? JSON.parse(savedUser) : null;
    } catch (e) {
      currentUser = null;
    }

    const canAccessVendor = currentUser?.role === 'admin';

    if (!canAccessVendor) {
      window.location.replace('../auth/login.php');
    }

    createApp({
      setup() {
        const apiBase = '../../api';
        const savedVendorTab = localStorage.getItem('vendorTab');
        const vendorTab = ref(['dashboard', 'orders', 'menu', 'sales'].includes(savedVendorTab) ? savedVendorTab : 'dashboard');
        const vendorLoading = ref(false);
        const vendorLoaded = ref(false);
        const vendorMessage = ref('');
        const vendorMenu = ref([]);
        const vendorOrders = ref([]);
        const vendorSales = ref({
          summary: { completed_orders: 0, total_sales: 0 },
          popular_items: [],
          status_counts: []
        });
        const orderStatusFilter = ref(localStorage.getItem('vendorOrderStatusFilter') || 'all');
        const orderSort = ref(localStorage.getItem('vendorOrderSort') || 'newest');
        const menuStockFilter = ref(localStorage.getItem('vendorMenuStockFilter') || 'all');
        const menuCategoryFilter = ref(localStorage.getItem('vendorMenuCategoryFilter') || 'all');
        const menuSort = ref(localStorage.getItem('vendorMenuSort') || 'name');
        const orderFiltersOpen = ref(false);
        const menuFiltersOpen = ref(false);
        const selectedOrder = ref(null);
        const selectedOrderItems = ref([]);
        const editingItemId = ref('');
        const menuForm = ref({
          name: '',
          description: '',
          price: '',
          category: 'food'
        });

        const formatMoney = (value) => `RM ${Number(value || 0).toFixed(2)}`;
        const formatDeliveryMethod = (method) => method === 'delivery' ? 'Delivery' : 'Pickup';
        const formatOrderStatus = (status) => {
          const labels = {
            pending: 'Pending',
            preparing: 'Preparing',
            ready: 'Ready',
            on_the_way: 'On the way',
            completed: 'Completed',
            cancelled: 'Cancelled'
          };
          return labels[status] || status;
        };
        const statusOptionsForOrder = (order) => [
          'pending',
          'preparing',
          order.delivery_method === 'delivery' ? 'on_the_way' : 'ready',
          'completed',
          'cancelled'
        ];

        const setVendorMessage = (message, type = 'success') => {
          vendorMessage.value = message;
          if (typeof showToast === 'function') {
            showToast(message, type);
          }
          if (message) {
            window.setTimeout(() => {
              if (vendorMessage.value === message) vendorMessage.value = '';
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
            throw new Error(data.errors ? data.errors.join(' ') : (data.message || 'Request failed.'));
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
            await Promise.all([loadVendorMenu(), loadVendorOrders(), loadVendorSales()]);
          } catch (error) {
            setVendorMessage(error.message || 'Could not load vendor data.', 'error');
          } finally {
            vendorLoaded.value = true;
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
            setVendorMessage(error.message, 'error');
          }
        };

        const toggleAvailability = async (item) => {
          try {
            await apiRequest(`/vendor/menu/${item.item_id}/availability`, {
              method: 'PATCH',
              body: JSON.stringify({ is_available: !Number(item.is_available) })
            });
            await loadVendorMenu();
            setVendorMessage(Number(item.is_available) ? 'Item marked sold out.' : 'Item marked on stock.');
          } catch (error) {
            setVendorMessage(error.message, 'error');
          }
        };

        const deleteMenuItem = async (item) => {
          if (!window.confirm(`Delete ${item.name}? Items used in orders cannot be deleted.`)) return;
          try {
            await apiRequest(`/vendor/menu/${item.item_id}`, { method: 'DELETE' });
            setVendorMessage('Menu item deleted.');
            await loadVendorMenu();
          } catch (error) {
            setVendorMessage(error.message, 'error');
          }
        };

        const viewOrder = async (order) => {
          try {
            const data = await apiRequest(`/vendor/orders/${order.order_id}`);
            selectedOrder.value = data.order;
            selectedOrderItems.value = data.items || [];
            vendorTab.value = 'orders';
          } catch (error) {
            setVendorMessage(error.message, 'error');
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
            setVendorMessage(error.message, 'error');
          }
        };

        const pendingOrders = computed(() => vendorOrders.value.filter((order) => order.status !== 'completed' && order.status !== 'cancelled'));
        const filteredVendorOrders = computed(() => {
          const filtered = vendorOrders.value.filter((order) => {
            return orderStatusFilter.value === 'all' || order.status === orderStatusFilter.value;
          });

          return [...filtered].sort((a, b) => {
            if (orderSort.value === 'oldest') return String(a.order_id).localeCompare(String(b.order_id));
            if (orderSort.value === 'highest') return Number(b.total_amount || 0) - Number(a.total_amount || 0);
            if (orderSort.value === 'lowest') return Number(a.total_amount || 0) - Number(b.total_amount || 0);
            return String(b.order_id).localeCompare(String(a.order_id));
          });
        });
        const filteredVendorMenu = computed(() => {
          const filtered = vendorMenu.value.filter((item) => {
            const stockMatches =
              menuStockFilter.value === 'all' ||
              (menuStockFilter.value === 'available' && Number(item.is_available)) ||
              (menuStockFilter.value === 'sold-out' && !Number(item.is_available));
            const categoryMatches = menuCategoryFilter.value === 'all' || item.category === menuCategoryFilter.value;
            return stockMatches && categoryMatches;
          });

          return [...filtered].sort((a, b) => {
            if (menuSort.value === 'price-high') return Number(b.price || 0) - Number(a.price || 0);
            if (menuSort.value === 'price-low') return Number(a.price || 0) - Number(b.price || 0);
            return String(a.name || '').localeCompare(String(b.name || ''));
          });
        });
        const showVendorInitialLoading = computed(() => vendorLoading.value && !vendorLoaded.value);
        const availableCount = computed(() => vendorMenu.value.filter((item) => Number(item.is_available)).length);
        const soldOutCount = computed(() => vendorMenu.value.filter((item) => !Number(item.is_available)).length);
        const orderCountByStatus = (status) => vendorOrders.value.filter((order) => order.status === status).length;
        const popularBarWidth = (item) => {
          const quantities = vendorSales.value.popular_items.map((popularItem) => Number(popularItem.total_quantity || 0));
          const maxQuantity = Math.max(1, ...quantities);
          return `${Math.max(10, (Number(item.total_quantity || 0) / maxQuantity) * 100)}%`;
        };

        watch(vendorTab, (tab) => {
          localStorage.setItem('vendorTab', tab);
        });
        watch(orderStatusFilter, (value) => localStorage.setItem('vendorOrderStatusFilter', value));
        watch(orderSort, (value) => localStorage.setItem('vendorOrderSort', value));
        watch(menuStockFilter, (value) => localStorage.setItem('vendorMenuStockFilter', value));
        watch(menuCategoryFilter, (value) => localStorage.setItem('vendorMenuCategoryFilter', value));
        watch(menuSort, (value) => localStorage.setItem('vendorMenuSort', value));

        onMounted(() => {
          if (canAccessVendor) loadVendorData();
        });

        return {
          availableCount,
          deleteMenuItem,
          editMenuItem,
          editingItemId,
          filteredVendorMenu,
          filteredVendorOrders,
          formatDeliveryMethod,
          formatMoney,
          formatOrderStatus,
          loadVendorData,
          menuCategoryFilter,
          menuForm,
          menuSort,
          menuStockFilter,
          menuFiltersOpen,
          orderCountByStatus,
          orderFiltersOpen,
          orderSort,
          orderStatusFilter,
          pendingOrders,
          popularBarWidth,
          resetMenuForm,
          saveMenuItem,
          selectedOrder,
          selectedOrderItems,
          showVendorInitialLoading,
          soldOutCount,
          statusOptionsForOrder,
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
      }
    }).mount('#app');
  </script>
</body>

</html>
