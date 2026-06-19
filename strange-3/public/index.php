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
  <div id="app">
    <main class="app-shell">
      <?php
        $root_path = "";
        $active_page = "home";
        include 'includes/customer_header.php';
      ?>

      <div>
        <section class="landing-top">
          <div class="page hero">
            <div class="hero-copy">
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
                <article class="hero-rating-card rating-top">
                  <div class="rating-stars" aria-label="5 out of 5 stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                  <p>"I swear on my life, this is the best sambal I have ever tasted, even the delivery was quick."</p>
                  <strong>TungTungSahere03</strong>
                </article>
                <article class="hero-rating-card rating-bottom">
                  <div class="rating-stars" aria-label="5 out of 5 stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                  <p>"I can't lie, the chicken is actually crispy, the batter is good too, not too salty, thought it's all hype LOL, drinks were cold, and checkout was simple."</p>
                  <strong>saranghaeminji69</strong>
                </article>
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

        <section class="page section">
          <div class="section-head top-picks">
            <div>
              <h2>Our Top Picks</h2>
            </div>
          </div>

          <div class="card-grid">
            <article class="food-card" v-for="item in topPickItems" :key="item.item_id">
              <img :src="getItemImage(item.item_id)" :alt="item.name">
              <div class="food-card-body">
                <h3>{{ item.name }}</h3>
                <p class="meta">{{ item.description }}</p>
                <div class="price-row">
                  <span>RM {{ parseFloat(item.price).toFixed(2) }}</span>
                  <div class="cart-stepper" :class="{ active: cart[item.item_id] > 0 }">
                    <button class="qty-btn dec" type="button" @click="decreaseQty(item.item_id)" aria-label="Remove item">-</button>
                    <span class="qty-val">{{ cart[item.item_id] || '' }}</span>
                    <button class="qty-btn inc" type="button" @click="increaseQty(item.item_id)" aria-label="Add item">+</button>
                  </div>
                </div>
              </div>
            </article>
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
              <a v-if="isVendor" href="src/vendor/dashboard.php" class="float-tile tile-order">
                <strong>Vendor</strong>
                <span>Manage records</span>
              </a>
            </div>
          </div>
        </section>
      </div>

      <footer class="footer">
        Universal Sambal base UI. Vue frontend linked to individual customer PHP pages.
      </footer>
    </main>
  </div>

  <script>
    const { createApp, computed, onMounted, ref } = Vue;

    createApp({
      setup() {
        const currentUser = ref(null);
        const cart = ref({});
        const menuItems = ref([]);
        const topPickIds = ['F001', 'F002', 'D005'];

        const loadCurrentUser = () => {
          try {
            const savedUser = localStorage.getItem('currentUser');
            currentUser.value = savedUser ? JSON.parse(savedUser) : null;
          } catch (e) {
            currentUser.value = null;
          }
        };

        const loadCart = () => {
          try {
            const savedCart = localStorage.getItem('cart');
            cart.value = savedCart ? JSON.parse(savedCart) : {};
          } catch (e) {
            cart.value = {};
          }
        };

        const saveCart = () => {
          localStorage.setItem('cart', JSON.stringify(cart.value));
          if (typeof syncHeaderCartCount === 'function') {
            syncHeaderCartCount();
          }
        };

        const fetchMenu = async () => {
          try {
            const res = await fetch('api/menu');
            const data = await res.json();
            if (data.status === 'success') {
              menuItems.value = data.items;
            }
          } catch (e) {
            console.error('Error fetching top picks:', e);
          }
        };

        const isVendor = computed(() => currentUser.value?.role === 'admin');

        const topPickItems = computed(() => {
          const selectedItems = topPickIds
            .map((itemId) => menuItems.value.find((item) => item.item_id === itemId))
            .filter(Boolean);

          return selectedItems.length === topPickIds.length
            ? selectedItems
            : menuItems.value.slice(0, 3);
        });

        const getItemImage = (itemId) => {
          const images = {
            'F001': 'images/food/ayam_merah.png',
            'F002': 'images/food/ayam_hijau.png',
            'F003': 'images/food/brownsugar.png',
            'F004': 'images/food/harimau.png',
            'F005': 'images/food/bawean.png',
            'F006': 'images/food/2rasa.png',
            'F007': 'images/food/3rasa.png',
            'D001': 'images/drink/orange.png',
            'D002': 'images/drink/carrot.png',
            'D003': 'images/drink/carrot_susu.png',
            'D004': 'images/drink/tembikai.png',
            'D005': 'images/drink/tembikai_susu.png',
            'D006': 'images/drink/apple.png'
          };
          return images[itemId] || 'images/food/test.png';
        };

        const increaseQty = (itemId) => {
          if (cart.value[itemId]) {
            cart.value[itemId]++;
          } else {
            cart.value[itemId] = 1;
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

        onMounted(() => {
          loadCurrentUser();
          loadCart();
          fetchMenu();
          window.addEventListener('storage', () => {
            loadCurrentUser();
            loadCart();
          });
        });

        return {
          cart,
          currentUser,
          decreaseQty,
          getItemImage,
          increaseQty,
          isVendor,
          topPickItems
        };
      }
    }).mount('#app');
  </script>
</body>

</html>
