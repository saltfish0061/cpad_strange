<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart - Universal Sambal</title>
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
            <p class="eyebrow">Your Selection</p>
            <h2>Your Shopping Cart</h2>
          </div>
        </div>

        <div v-if="cartIsEmpty" class="empty-panel">
          <h1>Your Cart is Empty</h1>
          <p>Add some delicious meals or refreshing drinks to your cart to get started.</p>
          <a class="cta" href="menu.php" style="width: auto;">Explore Menu</a>
        </div>

        <div v-else class="cart-layout">
          <div class="cart-items-list">
            <div class="cart-item-card" v-for="item in cartItemsList" :key="item.item_id">
              <img :src="getItemImage(item.item_id)" :alt="item.name" class="cart-item-img">
              <div class="cart-item-info">
                <h4>{{ item.name }}</h4>
                <p>RM {{ parseFloat(item.price).toFixed(2) }}</p>
              </div>
              <div class="qty-controls">
                <button class="qty-btn" @click="decreaseQty(item.item_id)">-</button>
                <span class="qty-val">{{ item.quantity }}</span>
                <button class="qty-btn" @click="increaseQty(item.item_id)">+</button>
              </div>
              <div class="cart-item-price" style="min-width: 80px; text-align: right;">
                RM {{ item.subtotal.toFixed(2) }}
              </div>
              <button class="delete-item-btn" @click="removeItem(item.item_id)">&times; Remove</button>
            </div>
          </div>

          <div class="cart-summary-card">
            <h3>Order Summary</h3>
            <div class="form-group">
              <label for="order-note">Order Note</label>
              <textarea
                id="order-note"
                v-model="orderNote"
                class="form-control"
                placeholder="Optional note for the vendor..."
                @input="saveOrderNote"
              ></textarea>
            </div>
            <div class="summary-row">
              <span>Subtotal</span>
              <span>RM {{ cartTotal.toFixed(2) }}</span>
            </div>
            <div class="summary-row">
              <span>Delivery / Service</span>
              <span>FREE</span>
            </div>
            <div class="summary-row total">
              <span>Total</span>
              <span>RM {{ cartTotal.toFixed(2) }}</span>
            </div>
          </div>
        </div>

        <div v-if="!cartIsEmpty" class="floating-checkout-bar">
          <div>
            <span>{{ cartCount }} {{ cartCount === 1 ? 'item' : 'items' }}</span>
            <strong>RM {{ cartTotal.toFixed(2) }}</strong>
          </div>
          <a class="cta" href="checkout.php">Proceed to Checkout</a>
        </div>
      </section>

      <footer class="footer">
        Universal Sambal Cart.
      </footer>
    </main>
  </div>

  <script>
    const { createApp, computed, onMounted, ref } = Vue;

    createApp({
      setup() {
        const cart = ref({});
        const menuItems = ref([]);
        const orderNote = ref('');

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

        const loadOrderNote = () => {
          orderNote.value = localStorage.getItem('orderNote') || '';
        };

        const saveOrderNote = () => {
          localStorage.setItem('orderNote', orderNote.value);
        };

        const cartIsEmpty = computed(() => {
          return Object.keys(cart.value).length === 0;
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

        const getItemImage = (itemId) => {
          const images = {
            'F001': '../../images/food/ayam_merah.png',
            'F002': '../../images/food/ayam_hijau.png',
            'F003': '../../images/food/brownsugar.png',
            'F004': '../../images/food/harimau.png',
            'F005': '../../images/food/bawean.png',
            'F006': '../../images/food/2rasa.png',
            'F007': '../../images/food/3rasa.png',
            'D001': '../../images/drink/orange.png',
            'D002': '../../images/drink/carrot.png',
            'D003': '../../images/drink/carrot_susu.png',
            'D004': '../../images/drink/tembikai.png',
            'D005': '../../images/drink/tembikai_susu.png'
          };
          return images[itemId] || '../../images/food/test.png';
        };

        const increaseQty = (itemId) => {
          if (cart.value[itemId]) {
            cart.value[itemId]++;
          }
          saveCart();
          if (typeof animateHeaderCartWiggle === 'function') {
            animateHeaderCartWiggle();
          }
        };

        const decreaseQty = (itemId) => {
          if (cart.value[itemId]) {
            cart.value[itemId]--;
            if (cart.value[itemId] <= 0) {
              delete cart.value[itemId];
            }
            saveCart();
            if (typeof animateHeaderCartWiggle === 'function') {
            animateHeaderCartWiggle();
          }
          }
        };

        const removeItem = (itemId) => {
          delete cart.value[itemId];
          saveCart();
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

        const cartCount = computed(() => {
          return Object.values(cart.value).reduce((sum, qty) => sum + qty, 0);
        });

        onMounted(() => {
          loadCart();
          loadOrderNote();
          fetchMenu();
        });

        return {
          cartIsEmpty,
          cartItemsList,
          cartCount,
          cartTotal,
          increaseQty,
          decreaseQty,
          removeItem,
          getItemImage,
          orderNote,
          saveOrderNote
        };
      }
    }).mount('#app');
  </script>
</body>

</html>
