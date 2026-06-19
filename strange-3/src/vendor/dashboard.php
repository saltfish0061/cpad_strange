<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vendor Dashboard - Universal Sambal</title>
  <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
  <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
  <div id="app">
        <main class="app-shell">
          <?php
            $root_path = '../../';
            $active_page = 'vendor';
            include '../../includes/customer_header.php';
          ?>

          <section class="page vendor-command">
            <div class="section-head vendor-command-head">
              <div>
                <p class="eyebrow">Vendor Dashboard</p>
                <h2>Vendor Command Center</h2>
              </div>
              <div class="vendor-head-actions">
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

                <div v-if="vendorTab === 'dashboard'">
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
                      </div>
                    </div>
                  </div>
                </div>

                <div v-if="vendorTab === 'orders'">
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
                </div>

                <div v-if="vendorTab === 'menu'">
                  <div class="vendor-panel">
                    <div class="vendor-panel-head">
                      <div>
                        <h2>{{ editingItemId ? 'Update Menu Item' : 'Add Menu Item' }}</h2>
                        <span class="vendor-panel-subtitle">{{ availableCount }} available - {{ soldOutCount }} sold out</span>
                      </div>
                      <button v-if="editingItemId" class="small-action" type="button" @click="resetMenuForm">Cancel Edit</button>
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
                    <table class="vendor-table">
                      <thead>
                        <tr><th>Item</th><th>Category</th><th>Price</th><th>Availability</th><th>Actions</th></tr>
                      </thead>
                      <tbody>
                        <tr v-for="item in vendorMenu" :key="item.item_id">
                          <td><strong>{{ item.name }}</strong><div class="muted-text">{{ item.item_id }}</div></td>
                          <td>{{ item.category }}</td>
                          <td>{{ formatMoney(item.price) }}</td>
                          <td><span class="status-pill" :class="Number(item.is_available) ? 'available' : 'sold-out'">{{ Number(item.is_available) ? 'available' : 'sold out' }}</span></td>
                          <td>
                            <div class="inline-actions">
                              <button class="small-action" type="button" @click="editMenuItem(item)">Edit</button>
                              <button class="small-action" type="button" @click="toggleAvailability(item)">{{ Number(item.is_available) ? 'Sold Out' : 'Available' }}</button>
                              <button class="danger-action" type="button" @click="deleteMenuItem(item)">Delete</button>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div v-if="vendorTab === 'sales'">
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

          <footer class="footer">Universal Sambal Vendor Module.</footer>
        </main>
  </div>

  <script>
    const { createApp, computed, onMounted, ref } = Vue;
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

        const formatMoney = (value) => `RM ${Number(value || 0).toFixed(2)}`;

        const setVendorMessage = (message) => {
          vendorMessage.value = message;
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
          if (!window.confirm(`Delete ${item.name}? Items used in orders cannot be deleted.`)) return;
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

        onMounted(() => {
          if (canAccessVendor) loadVendorData();
        });

        return {
          availableCount,
          deleteMenuItem,
          editMenuItem,
          editingItemId,
          formatMoney,
          loadVendorData,
          menuForm,
          orderCountByStatus,
          pendingOrders,
          popularBarWidth,
          resetMenuForm,
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
      }
    }).mount('#app');
  </script>
</body>

</html>
