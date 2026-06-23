<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Orders - Universal Sambal</title>
  <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
  <script src="../../js/app-utils.js"></script>
  <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
  <div id="app">
    <main class="app-shell">
      <?php
        $root_path = "../../";
        $active_page = "orders";
        include '../../libs/customer_header.php';
      ?>

      <section class="page section">
        <div class="section-head">
          <div>
            <p class="eyebrow">My Activity</p>
            <h2>Your Orders</h2>
          </div>
        </div>

        <div class="tabs">
          <div 
            class="tab-item" 
            :class="{ active: orderTab === 'active' }"
            @click="orderTab = 'active'"
          >
            Active Orders
          </div>
          <div 
            class="tab-item" 
            :class="{ active: orderTab === 'history' }"
            @click="orderTab = 'history'"
          >
            Order History
          </div>
        </div>

        <div v-if="loading" class="loading-surface">
          <div class="loading-card" role="status" aria-live="polite">
            <span class="loading-spinner" aria-hidden="true"></span>
            <strong>Loading</strong>
          </div>
          <div class="loading-skeleton-list" aria-hidden="true">
            <span></span><span></span><span></span>
          </div>
        </div>

        <div v-else-if="filteredOrders.length === 0" class="empty-panel">
          <h1>No Orders Found</h1>
          <p>You don't have any orders in this section yet.</p>
          <a class="cta" href="menu.php">Order Now</a>
        </div>

        <div v-else>
          <div class="order-card" v-for="order in filteredOrders" :key="order.order_id">
            <div class="order-info">
              <h4>Order #{{ order.order_id }}</h4>
              <p>Placed on: {{ order.order_date }}</p>
              <p style="font-weight: 800; color: var(--green-900); margin-top: 8px;">
                Total: RM {{ parseFloat(order.total_amount).toFixed(2) }}
              </p>
            </div>
            <div style="display: flex; align-items: center; gap: 20px;">
              <span class="status-badge" :class="order.status">{{ formatOrderStatus(order.status) }}</span>
              <a class="cta" :href="'order_details.php?id=' + order.order_id" style="padding: 6px 16px; font-size: 13px; min-height: 34px;">View Details</a>
            </div>
          </div>
        </div>
      </section>

      <?php include '../../libs/footer.php'; ?>
    </main>
  </div>

  <script>
    const { createApp, computed, onMounted, ref } = Vue;

    createApp({
      setup() {
        const cartCount = ref(0);
        const currentUser = ref(null);
        const orders = ref([]);
        const loading = ref(false);
        const orderTab = ref('active');

        const loadCurrentUser = () => {
          currentUser.value = AppUtils.session.loadUser();
        };

        const loadCartCount = () => {
          cartCount.value = AppUtils.cart.count(AppUtils.cart.load());
        };

        const fetchOrders = async () => {
          if (!currentUser.value?.user_id) {
            orders.value = [];
            return;
          }

          loading.value = true;
          try {
            const res = await fetch(`../../api/orders?user_id=${encodeURIComponent(currentUser.value.user_id)}`);
            const data = await res.json();
            if (data.status === 'success') {
              orders.value = data.orders;
            }
          } catch (e) {
            console.error('Error fetching orders:', e);
          } finally {
            loading.value = false;
          }
        };

        const filteredOrders = computed(() => {
          return orders.value.filter(order => {
            const isActive = ['pending', 'preparing', 'ready', 'on_the_way'].includes(order.status);
            return orderTab.value === 'active' ? isActive : !isActive;
          });
        });

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

        onMounted(() => {
          loadCurrentUser();
          loadCartCount();
          fetchOrders();
        });

        return {
          cartCount,
          currentUser,
          filteredOrders,
          formatOrderStatus,
          loading,
          orderTab
        };
      }
    }).mount('#app');
  </script>
</body>

</html>
