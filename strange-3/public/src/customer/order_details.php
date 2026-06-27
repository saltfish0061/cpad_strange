<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Order Details - Universal Sambal</title>
  <script src="../../js/vue.global.prod.js"></script>
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
        <div style="margin-bottom: 20px;">
          <a class="pill-button" href="my_orders.php" style="display: inline-flex; align-items: center; gap: 8px; border-color: #000000; color: #000000;">
            &larr; Back to Orders
          </a>
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

        <div v-else-if="!currentOrder" class="empty-panel">
          <h1>Order Not Found</h1>
          <p>We couldn't find the requested order, or it doesn't belong to this account.</p>
        </div>

        <div v-else>
          <div class="section-head">
            <div>
              <p class="eyebrow">Tracking ID: {{ currentOrder.order_id }}</p>
              <h2>Order Status</h2>
            </div>
            <p>Placed on: {{ currentOrder.order_date }}</p>
          </div>

          <!-- Progress Tracker -->
          <div class="progress-tracker">
            <div class="progress-line" :style="{ width: progressPercent + '%', '--mobile-progress': progressPercent }"></div>
            <div 
              v-for="(step, idx) in trackerSteps" 
              :key="step.status"
              class="progress-step"
              :class="{ 
                active: currentOrder.status === step.status || (currentOrder.status === 'completed' && step.status === handoffStatus),
                completed: isStepCompleted(step.status)
              }"
            >
              <div class="step-icon">
                <span v-if="isStepCompleted(step.status)">&checkmark;</span>
                <span v-else>{{ idx + 1 }}</span>
              </div>
              <div class="step-label">{{ step.label }}</div>
            </div>
          </div>

          <div class="cart-layout" style="margin-top: 40px;">
            <div class="cart-summary-card">
              <h3>Order Details</h3>
              <div class="summary-row" style="border-bottom: 1px solid var(--line); padding-bottom: 10px; margin-bottom: 10px; font-weight: bold;">
                <span>Item</span>
                <span style="display: grid; grid-template-columns: 80px 100px; text-align: right;">
                  <span>Qty</span>
                  <span>Subtotal</span>
                </span>
              </div>
              <div 
                v-for="item in currentOrderItems" 
                :key="item.order_item_id" 
                class="summary-row" 
                style="font-size: 15px; margin-bottom: 10px;"
              >
                <span>{{ item.item_name }}</span>
                <span style="display: grid; grid-template-columns: 80px 100px; text-align: right; color: var(--muted);">
                  <span>x{{ item.quantity }}</span>
                  <span style="font-weight: 800; color: var(--ink);">RM {{ parseFloat(item.subtotal).toFixed(2) }}</span>
                </span>
              </div>
              <div class="summary-row total">
                <span>Grand Total</span>
                <span>RM {{ parseFloat(currentOrder.total_amount).toFixed(2) }}</span>
              </div>
            </div>

            <div class="cart-summary-card">
              <h3>Delivery / Pickup Info</h3>
              <p><strong>Method:</strong> {{ formatDeliveryMethod(currentOrder.delivery_method) }}</p>
              <p><strong>Name:</strong> {{ currentUser?.name || currentOrder.user_id }}</p>
              <p><strong>Phone:</strong> {{ currentUser?.phone || '-' }}</p>
              <p><strong>Address:</strong> {{ currentUser?.address || '-' }}</p>
              <p v-if="currentOrder.order_note"><strong>Note:</strong> {{ currentOrder.order_note }}</p>
              <div style="margin-top: 24px; padding: 16px; border-radius: 12px; background: var(--green-100); text-align: center;">
                <p style="margin: 0; font-weight: bold; color: var(--sambal-dark);">
                  Status: <span class="status-badge" :class="currentOrder.status">{{ formatOrderStatus(currentOrder.status) }}</span>
                </p>
                <p v-if="currentOrder.status === 'pending'" style="margin: 8px 0 0; font-size: 13px; color: var(--muted);">
                  Waiting for vendor approval.
                </p>
                <p v-else-if="currentOrder.status === 'preparing'" style="margin: 8px 0 0; font-size: 13px; color: var(--muted);">
                  Vendor is preparing your delicious meal!
                </p>
                <p v-else-if="currentOrder.status === 'ready'" style="margin: 8px 0 0; font-size: 13px; color: var(--muted);">
                  Your order is ready! Please pick it up.
                </p>
                <p v-else-if="currentOrder.status === 'on_the_way'" style="margin: 8px 0 0; font-size: 13px; color: var(--muted);">
                  Your order is on the way.
                </p>
                <p v-else-if="currentOrder.status === 'completed'" style="margin: 8px 0 0; font-size: 13px; color: var(--muted);">
                  Thank you for dining with us!
                </p>
                <p v-else-if="currentOrder.status === 'cancelled'" style="margin: 8px 0 0; font-size: 13px; color: var(--muted);">
                  This order was cancelled.
                </p>
              </div>
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
        const currentOrder = ref(null);
        const currentOrderItems = ref([]);
        const loading = ref(false);

        const loadCurrentUser = () => {
          currentUser.value = AppUtils.session.loadUser();
        };

        const loadCartCount = () => {
          cartCount.value = AppUtils.cart.count(AppUtils.cart.load());
        };

        const formatDeliveryMethod = (method) => {
          return method === 'delivery' ? 'Delivery' : 'Self-Pickup';
        };

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

        const fetchOrderDetails = async (id) => {
          if (!currentUser.value?.user_id) {
            currentOrder.value = null;
            currentOrderItems.value = [];
            return;
          }

          loading.value = true;
          try {
            const res = await fetch(`../../api/orders/${id}?user_id=${encodeURIComponent(currentUser.value.user_id)}`);
            if (res.status === 404) {
              currentOrder.value = null;
              currentOrderItems.value = [];
              return;
            }
            const data = await res.json();
            if (data.status === 'success') {
              currentOrder.value = data.order;
              currentOrderItems.value = data.items;
            }
          } catch (e) {
            console.error('Error fetching order details:', e);
          } finally {
            loading.value = false;
          }
        };

        const handoffStatus = computed(() => currentOrder.value?.delivery_method === 'delivery' ? 'on_the_way' : 'ready');
        const trackerSteps = computed(() => [
          { status: 'pending', label: 'Order Placed' },
          { status: 'preparing', label: 'Preparing' },
          {
            status: handoffStatus.value,
            label: currentOrder.value?.delivery_method === 'delivery' ? 'On the way' : 'Ready for Pickup'
          },
          { status: 'completed', label: 'Completed' }
        ]);

        const isStepCompleted = (stepStatus) => {
          if (!currentOrder.value) return false;
          const statusOrder = ['pending', 'preparing', handoffStatus.value, 'completed'];
          const currentIdx = statusOrder.indexOf(currentOrder.value.status);
          const stepIdx = statusOrder.indexOf(stepStatus);
          
          if (currentOrder.value.status === 'cancelled') {
            return stepStatus === 'pending';
          }
          return stepIdx <= currentIdx;
        };

        const progressPercent = computed(() => {
          if (!currentOrder.value) return 0;
          if (currentOrder.value.status === 'cancelled') return 0;
          const statusOrder = ['pending', 'preparing', handoffStatus.value, 'completed'];
          const idx = statusOrder.indexOf(currentOrder.value.status);
          if (idx === -1) return 0;
          return (idx / (statusOrder.length - 1)) * 76; // Match progress bar boundaries
        });

        onMounted(() => {
          loadCurrentUser();
          loadCartCount();
          const params = new URLSearchParams(window.location.search);
          const id = params.get('id');
          if (id) {
            fetchOrderDetails(id);
          } else {
            currentOrder.value = null;
          }
        });

        return {
          cartCount,
          currentUser,
          currentOrder,
          currentOrderItems,
          formatDeliveryMethod,
          formatOrderStatus,
          handoffStatus,
          loading,
          trackerSteps,
          isStepCompleted,
          progressPercent
        };
      }
    }).mount('#app');
  </script>
</body>

</html>
