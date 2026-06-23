<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout - Universal Sambal</title>
  <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
  <script src="../../js/app-utils.js"></script>
  <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
  <div id="app">
    <main class="app-shell">
      <?php
        $root_path = "../../";
        $active_page = "cart";
        include '../../libs/customer_header.php';
      ?>

      <section class="page section">
        <div class="section-head">
          <div>
            <p class="eyebrow">Almost Done!</p>
            <h2>Checkout Details</h2>
          </div>
        </div>

        <div v-if="cartCount === 0" class="empty-panel">
          <h1>No Items to Checkout</h1>
          <p>Your cart is empty. Please add items before checking out.</p>
          <a class="cta" href="menu.php" style="width: auto;">Explore Menu</a>
        </div>

        <div v-else class="cart-layout">
          <div class="cart-summary-card">
            <h3>Customer & Payment Info</h3>
            <form @submit.prevent="submitOrder" class="checkout-form">
              <div class="form-group">
                <label for="name">Username</label>
                <input type="text" id="name" v-model="checkoutForm.name" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" v-model="checkoutForm.phone" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" v-model="checkoutForm.address" class="form-control" required></textarea>
              </div>
              <div class="form-group">
                <label for="order-note">Order Note</label>
                <textarea id="order-note" v-model="checkoutForm.orderNote" class="form-control" @input="saveOrderNote"></textarea>
              </div>
              <div class="form-group">
                <label>Delivery Method</label>
                <div class="payment-methods">
                  <div
                    class="payment-method-card"
                    :class="{ active: checkoutForm.deliveryMethod === 'pickup' }"
                    @click="checkoutForm.deliveryMethod = 'pickup'"
                  >
                    <img src="../../images/assets/checkout/fulfillment/food-pickup.png" alt="Pickup">
                    <span class="payment-option-label">Pickup</span>
                  </div>
                  <div
                    class="payment-method-card"
                    :class="{ active: checkoutForm.deliveryMethod === 'delivery' }"
                    @click="checkoutForm.deliveryMethod = 'delivery'"
                  >
                    <img src="../../images/assets/checkout/fulfillment/food-delivery.png" alt="Delivery">
                    <span class="payment-option-label">Delivery</span>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Payment Method</label>
                <div class="payment-methods">
                  <div 
                    class="payment-method-card"
                    :class="{ active: checkoutForm.paymentMethod === 'cod' }"
                    @click="checkoutForm.paymentMethod = 'cod'"
                  >
                    <img src="../../images/assets/checkout/payment/dollar.png" alt="Cash">
                    <span class="payment-option-label">Cash on Delivery</span>
                  </div>
                  <div 
                    class="payment-method-card"
                    :class="{ active: checkoutForm.paymentMethod === 'card' }"
                    @click="checkoutForm.paymentMethod = 'card'"
                  >
                    <img src="../../images/assets/checkout/payment/card.png" alt="Card">
                    <span class="payment-option-label">Card Payment</span>
                  </div>
                  <div 
                    class="payment-method-card"
                    :class="{ active: checkoutForm.paymentMethod === 'qr' }"
                    @click="checkoutForm.paymentMethod = 'qr'"
                  >
                    <img src="../../images/assets/checkout/payment/qr icon.png" alt="Qr">
                    <span class="payment-option-label">DuitNow QR</span>
                  </div>
                </div>
              </div>
              <button type="submit" class="cta" :disabled="submitting">
                {{ submitting ? 'Placing Order...' : 'Confirm & Place Order' }}
              </button>
            </form>
          </div>

          <div class="cart-summary-card">
            <h3>Order Items</h3>
            <div v-for="item in cartItemsList" :key="item.item_id" class="summary-row" style="font-size: 14px;">
              <span>{{ item.name }} x{{ item.quantity }}</span>
              <span>RM {{ item.subtotal.toFixed(2) }}</span>
            </div>
            <div class="summary-row total">
              <span>Grand Total</span>
              <span>RM {{ cartTotal.toFixed(2) }}</span>
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
        const cart = ref({});
        const menuItems = ref([]);
        const currentUser = ref(null);
        const checkoutForm = ref({
          name: '',
          phone: '',
          address: '',
          orderNote: '',
          deliveryMethod: 'pickup',
          paymentMethod: 'cod'
        });
        const submitting = ref(false);

        const loadCart = () => {
          cart.value = AppUtils.cart.load();
        };

        const saveCart = () => {
          AppUtils.cart.save(cart.value);
        };

        const removeUnavailableCartItems = () => {
          const availableIds = new Set(
            menuItems.value
              .filter((item) => Number(item.is_available))
              .map((item) => item.item_id)
          );
          let changed = false;

          for (const itemId of Object.keys(cart.value)) {
            if (!availableIds.has(itemId)) {
              delete cart.value[itemId];
              changed = true;
            }
          }

          if (changed) {
            saveCart();
          }
        };

        const saveOrderNote = () => {
          AppUtils.orderNote.save(checkoutForm.value.orderNote);
        };

        const loadCheckoutProfile = async () => {
          try {
            currentUser.value = AppUtils.session.loadUser();
            if (!currentUser.value?.user_id) return;

            const res = await fetch(`../../api/profile/${currentUser.value.user_id}`);
            const data = await res.json();
            if (data.status === 'success') {
              currentUser.value = data.user;
              AppUtils.session.saveUser(data.user);
            }
          } catch (e) {
            console.error('Error loading checkout profile:', e);
          } finally {
            checkoutForm.value.name = currentUser.value?.name || '';
            checkoutForm.value.phone = currentUser.value?.phone || '';
            checkoutForm.value.address = currentUser.value?.address || '';
            checkoutForm.value.orderNote = AppUtils.orderNote.load();
          }
        };

        const cartCount = computed(() => {
          return AppUtils.cart.count(cart.value);
        });

        const fetchMenu = async () => {
          try {
            const res = await fetch('../../api/menu?include_unavailable=1');
            const data = await res.json();
            if (data.status === 'success') {
              menuItems.value = data.items;
              removeUnavailableCartItems();
            }
          } catch (e) {
            console.error('Error fetching menu:', e);
          }
        };

        const cartItemsList = computed(() => {
          const list = [];
          for (const [itemId, qty] of Object.entries(cart.value)) {
            const item = menuItems.value.find(m => m.item_id === itemId);
            if (item) {
              list.push({
                ...item,
                quantity: qty,
                subtotal: parseFloat(item.price) * qty
              });
            } else {
              list.push({
                item_id: itemId,
                name: 'Item ' + itemId,
                price: 0,
                quantity: qty,
                subtotal: 0
              });
            }
          }
          return list;
        });

        const cartTotal = computed(() => {
          return cartItemsList.value.reduce((sum, item) => sum + item.subtotal, 0);
        });

        const submitOrder = async () => {
          if (!currentUser.value?.user_id) {
            if (typeof showToast === 'function') showToast('Please login before placing an order.', 'error');
            return;
          }

          if (!checkoutForm.value.name || !checkoutForm.value.phone) {
            if (typeof showToast === 'function') showToast('Please enter your username and phone number.', 'error');
            return;
          }

          if (checkoutForm.value.deliveryMethod === 'delivery' && !checkoutForm.value.address) {
            if (typeof showToast === 'function') showToast('Please enter your delivery address.', 'error');
            return;
          }

          submitting.value = true;
          try {
            const profileRes = await fetch(`../../api/profile/${currentUser.value.user_id}`, {
              method: 'PUT',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({
                name: checkoutForm.value.name,
                phone: checkoutForm.value.phone,
                address: checkoutForm.value.address
              })
            });
            const profileData = await profileRes.json();
            if (!profileRes.ok || profileData.status === 'error') {
              const message = profileData.errors ? profileData.errors.join(' ') : (profileData.message || 'Unable to update checkout info.');
              throw new Error(message);
            }
            currentUser.value = profileData.user;
            AppUtils.session.saveUser(profileData.user);

            const res = await fetch('../../api/orders', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({
                user_id: currentUser.value.user_id,
                items: cart.value,
                order_note: checkoutForm.value.orderNote,
                delivery_method: checkoutForm.value.deliveryMethod
              })
            });
            const data = await res.json();
            if (data.status === 'success') {
              cart.value = {};
              saveCart();
              AppUtils.orderNote.clear();
              if (typeof showToast === 'function') showToast('Order placed successfully.');
              window.location.href = 'confirm_order.php?order_id=' + data.order_id;
            } else {
              if (typeof showToast === 'function') showToast(data.message || 'Unable to place order.', 'error');
            }
          } catch (e) {
            console.error('Error placing order:', e);
            if (typeof showToast === 'function') showToast(e.message || 'Failed to place order. Please try again.', 'error');
          } finally {
            submitting.value = false;
          }
        };

        onMounted(() => {
          loadCart();
          loadCheckoutProfile();
          fetchMenu();
        });

        return {
          cartCount,
          cartItemsList,
          cartTotal,
          checkoutForm,
          saveOrderNote,
          submitOrder,
          submitting
        };
      }
    }).mount('#app');
  </script>
</body>

</html>
