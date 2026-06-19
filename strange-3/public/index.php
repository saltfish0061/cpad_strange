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
              <a v-if="isVendor" href="src/vendor/dashboard.php" class="float-tile tile-order">
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

        const loadCurrentUser = () => {
          try {
            const savedUser = localStorage.getItem('currentUser');
            currentUser.value = savedUser ? JSON.parse(savedUser) : null;
          } catch (e) {
            currentUser.value = null;
          }
        };

        onMounted(() => {
          loadCurrentUser();
          window.addEventListener('storage', loadCurrentUser);
        });

        const isVendor = computed(() => currentUser.value?.role === 'admin');

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

        return {
          currentUser,
          isVendor,
          previewItems
        };
      }
    }).mount('#app');
  </script>
</body>

</html>
