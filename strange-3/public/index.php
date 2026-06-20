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
              <h1>Ayam Gepuk? Nah, Try Ours. Constipation Guaranteed</h1>
              <p class="hero-text">
                Order ayam geprek, rice meals, and cold drinks from Universal Sambal. Pick up at the cafe
                or choose delivery when the craving cannot wait.
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
                    <p>Skip the rush by placing your order early, then track the kitchen status from your phone.</p>
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
                    <h3>Rice meals, sambal, and drinks</h3>
                    <p>Explore our signature spicy dishes, authentic Indonesian rice meals, and fresh beverages.</p>
                    <a class="cta" href="src/customer/menu.php">View Menu</a>
                  </div>
                  <img class="cutout promo-img-menu" src="images/food/separate.png" alt="Ayam geprek sambal hijau">
                </article>
              </div>

              <article class="promo red">
                <div>
                  <p class="eyebrow">Fresh & Cold</p>
                  <h3>Cool down after the heat</h3>
                  <p>Pair the sambal kick with chilled fruit juice, milk drinks, or something sweet from the drinks menu.</p>
                  <a class="cta" href="src/customer/menu.php?category=drink">Explore Drinks</a>
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

        <section class="spice-slider-band">
          <div class="page section spice-layout">
            <div class="section-head center-text">
              <div>
                <h2>Find Your Perfect Pair</h2>
                <p>Slide to match your heat level with the perfect cooling drink</p>
              </div>
            </div>

            <div class="spice-interactive">
              <div class="spice-slider-container">
                <input type="range" min="1" max="5" v-model="spiceLevel" class="spice-slider" :class="'level-' + spiceLevel">
                <div class="spice-labels">
                  <span>Mild</span>
                  <span>Sweet Heat</span>
                  <span>Classic Kick</span>
                  <span>Fire Warning</span>
                  <span>Call Ambulance</span>
                </div>
              </div>

              <transition name="combo-swap" mode="out-in">
                <div class="combo-showcase" v-if="suggestedCombo" :key="spiceLevel">
                  <article class="combo-card food-combo" :class="{ 'sold-out-card': !Number(suggestedCombo.food.is_available) }">
                    <img :src="getItemImage(suggestedCombo.food.item_id)" :alt="suggestedCombo.food.name">
                    <div class="combo-card-body">
                      <span class="combo-badge hot">🌶️ Heat Level {{ spiceLevel }}</span>
                      <span v-if="!Number(suggestedCombo.food.is_available)" class="sold-out-ribbon">Sold Out</span>
                      <h3>{{ suggestedCombo.food.name }}</h3>
                      <p class="meta">{{ suggestedCombo.food.description }}</p>
                      <div class="price-row">
                        <span>RM {{ parseFloat(suggestedCombo.food.price).toFixed(2) }}</span>
                      </div>
                    </div>
                  </article>

                  <div class="combo-plus">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                  </div>

                  <article class="combo-card drink-combo" :class="{ 'sold-out-card': !Number(suggestedCombo.drink.is_available) }">
                    <img :src="getItemImage(suggestedCombo.drink.item_id)" :alt="suggestedCombo.drink.name">
                    <div class="combo-card-body">
                      <span class="combo-badge cool">❄️ Cool Down</span>
                      <span v-if="!Number(suggestedCombo.drink.is_available)" class="sold-out-ribbon">Sold Out</span>
                      <h3>{{ suggestedCombo.drink.name }}</h3>
                      <p class="meta">{{ suggestedCombo.drink.description }}</p>
                      <div class="price-row">
                        <span>RM {{ parseFloat(suggestedCombo.drink.price).toFixed(2) }}</span>
                      </div>
                    </div>
                  </article>
                </div>
              </transition>

              <div class="combo-actions" v-if="suggestedCombo">
                <button type="button" class="cta cta-combo" :disabled="comboUnavailable" @click="addComboToCart">
                  {{ comboUnavailable ? 'Combo Currently Sold Out' : 'Add Perfect Combo to Cart' }}
                </button>
              </div>
            </div>
          </div>
        </section>
      </div>

      <footer class="footer">
        Universal Sambal. Fresh orders, spicy plates, and cold drinks.
      </footer>
    </main>
  </div>

  <script>
    const { createApp, computed, onMounted, ref, watch } = Vue;

    createApp({
      setup() {
        const currentUser = ref(null);
        const cart = ref({});
        const menuItems = ref([]);
        const topPickIds = ['F001', 'F002', 'D005'];
        const savedSpiceLevel = Number(localStorage.getItem('spiceLevel'));
        const spiceLevel = ref(savedSpiceLevel >= 1 && savedSpiceLevel <= 5 ? savedSpiceLevel : 3);

        const comboMap = {
          1: { food: 'F003', drink: 'D006' },
          2: { food: 'F002', drink: 'D001' },
          3: { food: 'F005', drink: 'D003' },
          4: { food: 'F001', drink: 'D004' },
          5: { food: 'F004', drink: 'D005' },
        };

        const suggestedCombo = computed(() => {
          if (!menuItems.value.length) return null;
          const map = comboMap[spiceLevel.value];
          if (!map) return null;
          const food = menuItems.value.find(item => item.item_id === map.food);
          const drink = menuItems.value.find(item => item.item_id === map.drink);
          if (food && drink) {
            return { food, drink };
          }
          return null;
        });

        const comboUnavailable = computed(() => {
          return !suggestedCombo.value ||
            !Number(suggestedCombo.value.food.is_available) ||
            !Number(suggestedCombo.value.drink.is_available);
        });

        const addComboToCart = () => {
          if (suggestedCombo.value && !comboUnavailable.value) {
            increaseQty(suggestedCombo.value.food.item_id);
            increaseQty(suggestedCombo.value.drink.item_id);
          }
        };

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

        const fetchMenu = async () => {
          try {
            const res = await fetch('api/menu?include_unavailable=1');
            const data = await res.json();
            if (data.status === 'success') {
              menuItems.value = data.items;
              removeUnavailableCartItems();
            }
          } catch (e) {
            console.error('Error fetching top picks:', e);
          }
        };

        const isVendor = computed(() => currentUser.value?.role === 'admin');

        const topPickItems = computed(() => {
          const availableItems = menuItems.value.filter((item) => Number(item.is_available));
          const selectedItems = topPickIds
            .map((itemId) => availableItems.find((item) => item.item_id === itemId))
            .filter(Boolean);

          return selectedItems.length === topPickIds.length
            ? selectedItems
            : availableItems.slice(0, 3);
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

        watch(spiceLevel, (level) => {
          localStorage.setItem('spiceLevel', String(level));
        });

        return {
          cart,
          currentUser,
          decreaseQty,
          getItemImage,
          increaseQty,
          isVendor,
          topPickItems,
          spiceLevel,
          suggestedCombo,
          comboUnavailable,
          addComboToCart
        };
      }
    }).mount('#app');
  </script>
</body>

</html>
