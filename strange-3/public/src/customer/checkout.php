<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout - Universal Sambal</title>
  <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
  <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
  <div id="app">
    <main class="app-shell">
      <?php
        $root_path = "../../";
        $active_page = "cart";
        include '../../includes/customer_header.php';
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
                    Pickup
                  </div>
                  <div
                    class="payment-method-card"
                    :class="{ active: checkoutForm.deliveryMethod === 'delivery' }"
                    @click="checkoutForm.deliveryMethod = 'delivery'"
                  >
                    Delivery
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
                    Cash on Delivery
                  </div>
                  <div 
                    class="payment-method-card"
                    :class="{ active: checkoutForm.paymentMethod === 'card' }"
                    @click="checkoutForm.paymentMethod = 'card'"
                  >
                    Card Payment
                  </div>
                  <div 
                    class="payment-method-card"
                    :class="{ active: checkoutForm.paymentMethod === 'qr' }"
                    @click="checkoutForm.paymentMethod = 'qr'"
                  >
                    DuitNow QR
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

      <footer class="footer">
        Universal Sambal Checkout.
      </footer>
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
          try {
            const savedCart = localStorage.getItem('cart');
            if (savedCart) {
              cart.value = JSON.parse(savedCart);
            }
          } catch (e) {
            console.error('Failed to load cart:', e);
          }
        };

        const saveCart = () => {
          localStorage.setItem('cart', JSON.stringify(cart.value));
          if (typeof syncHeaderCartCount === 'function') {
            syncHeaderCartCount();
          }
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
          localStorage.setItem('orderNote', checkoutForm.value.orderNote);
        };

        const loadCheckoutProfile = async () => {
          try {
            const savedUser = localStorage.getItem('currentUser');
            currentUser.value = savedUser ? JSON.parse(savedUser) : null;
            if (!currentUser.value?.user_id) return;

            const res = await fetch(`../../api/profile/${currentUser.value.user_id}`);
            const data = await res.json();
            if (data.status === 'success') {
              currentUser.value = data.user;
              localStorage.setItem('currentUser', JSON.stringify(data.user));
            }
          } catch (e) {
            console.error('Error loading checkout profile:', e);
          } finally {
            checkoutForm.value.name = currentUser.value?.name || '';
            checkoutForm.value.phone = currentUser.value?.phone || '';
            checkoutForm.value.address = currentUser.value?.address || '';
            checkoutForm.value.orderNote = localStorage.getItem('orderNote') || '';
          }
        };

        const cartCount = computed(() => {
          return Object.values(cart.value).reduce((sum, qty) => sum + qty, 0);
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
            alert('Please login before placing an order.');
            return;
          }

          if (!checkoutForm.value.name || !checkoutForm.value.phone) {
            alert('Please enter your username and phone number.');
            return;
          }

          if (checkoutForm.value.deliveryMethod === 'delivery' && !checkoutForm.value.address) {
            alert('Please enter your delivery address.');
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
            localStorage.setItem('currentUser', JSON.stringify(profileData.user));

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
              localStorage.removeItem('orderNote');
              window.location.href = 'confirm_order.php?order_id=' + data.order_id;
            } else {
              alert('Error: ' + data.message);
            }
          } catch (e) {
            console.error('Error placing order:', e);
            alert('Failed to place order. Please try again.');
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
