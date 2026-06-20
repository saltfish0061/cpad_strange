<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Our Menu - Universal Sambal</title>
  <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
  <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
  <div id="app">
    <main class="app-shell">
      <?php
      $root_path = "../../";
      $active_page = "menu";
      include '../../includes/customer_header.php';
      ?>

      <section class="page section">
        <div class="section-head">
          <div>
            <p class="eyebrow">Fresh & Delicious</p>
            <h2>Our Menu</h2>
          </div>
          <p>Browse our selection of authentic dishes and refreshing beverages.</p>
        </div>

        <div class="menu-controls">
          <div class="categories">
            <button v-for="cat in ['all', 'food', 'drink']" :key="cat" class="category-btn"
              :class="{ active: selectedCategory === cat }" @click="setCategory(cat)">
              {{ cat.toUpperCase() }}
            </button>
          </div>
          <input type="text" v-model="searchQuery" @input="syncMenuUrl" placeholder="Search for food or drinks..." class="search-input">
        </div>

        <div v-if="loading" class="empty-panel">
          <h1>Loading...</h1>
          <p>Fetching our fresh menus from the kitchen...</p>
        </div>

        <div v-else-if="availableMenu.length === 0 && soldOutMenu.length === 0" class="empty-panel">
          <h1>No Items Found</h1>
          <p>We couldn't find any items matching your criteria. Try adjusting your filters.</p>
        </div>

        <div v-else-if="availableMenu.length > 0" class="card-grid">
          <article class="food-card" v-for="item in availableMenu" :key="item.item_id">
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

        <div v-if="soldOutMenu.length > 0" class="sold-out-section">
          <div class="section-head compact">
            <div>
              <p class="eyebrow">Unavailable</p>
              <h2>Sold Out Today</h2>
            </div>
          </div>
          <div class="card-grid">
            <article class="food-card sold-out-card" v-for="item in soldOutMenu" :key="item.item_id">
              <div class="sold-out-media">
                <img :src="getItemImage(item.item_id)" :alt="item.name">
                <span class="sold-out-ribbon">Sold Out</span>
              </div>
              <div class="food-card-body">
                <h3>{{ item.name }}</h3>
                <p class="meta">{{ item.description }}</p>
                <div class="price-row">
                  <span>RM {{ parseFloat(item.price).toFixed(2) }}</span>
                  <button class="qty-btn" type="button" disabled aria-label="Sold out">+</button>
                </div>
              </div>
            </article>
          </div>
        </div>

        <div class="menu-checkout-container"
          style="display: flex; justify-content: center; margin-top: 40px; margin-bottom: 20px;">
          <button v-if="pairSuggestion" class="cta-float cta-pair" type="button" @click="showSuggestedCategory">
            {{ pairSuggestion.label }}
          </button>
          <button class="cta-float" :disabled="cartIsEmpty" @click="goToCheckout"
            style="width: 100%; max-width: 320px; text-align: center; border: 0;">
            Proceed to Checkout ({{ cartCount }} {{ cartCount === 1 ? 'item' : 'items' }})
          </button>
        </div>
      </section>

      <footer class="footer">
        Universal Sambal Menu. Refined Customer flow.
      </footer>
    </main>
  </div>

  <script>
    const { createApp, computed, onMounted, ref } = Vue;

    createApp({
      setup() {
        const cart = ref({});
        const menuItems = ref([]);
        const loading = ref(false);
        const selectedCategory = ref('all');
        const searchQuery = ref('');

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

        const loadMenuFilters = () => {
          const params = new URLSearchParams(window.location.search);
          const category = params.get('category');
          const search = params.get('search');

          if (['all', 'food', 'drink'].includes(category)) {
            selectedCategory.value = category;
          }

          if (search) {
            searchQuery.value = search;
          }
        };

        const syncMenuUrl = () => {
          const params = new URLSearchParams();

          if (selectedCategory.value !== 'all') {
            params.set('category', selectedCategory.value);
          }

          if (searchQuery.value.trim()) {
            params.set('search', searchQuery.value.trim());
          }

          const query = params.toString();
          const nextUrl = query ? `${window.location.pathname}?${query}` : window.location.pathname;
          window.history.replaceState({}, '', nextUrl);
        };

        const setCategory = (category) => {
          selectedCategory.value = category;
          syncMenuUrl();
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
          loading.value = true;
          try {
            const res = await fetch('../../api/menu?include_unavailable=1');
            const data = await res.json();
            if (data.status === 'success') {
              menuItems.value = data.items;
              removeUnavailableCartItems();
            }
          } catch (e) {
            console.error('Error fetching menu:', e);
          } finally {
            loading.value = false;
          }
        };

        const filteredMenu = computed(() => {
          return menuItems.value.filter(item => {
            const matchesCat = selectedCategory.value === 'all' || item.category === selectedCategory.value;
            const matchesSearch = item.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
              (item.description && item.description.toLowerCase().includes(searchQuery.value.toLowerCase()));
            return matchesCat && matchesSearch;
          });
        });

        const availableMenu = computed(() => filteredMenu.value.filter((item) => Number(item.is_available)));
        const soldOutMenu = computed(() => filteredMenu.value.filter((item) => !Number(item.is_available)));

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
            'D005': '../../images/drink/tembikai_susu.png',
            'D006': '../../images/drink/apple.png'
          };
          return images[itemId] || '../../images/food/test.png';
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

        const cartCount = computed(() => {
          return Object.values(cart.value).reduce((sum, qty) => sum + qty, 0);
        });

        const cartIsEmpty = computed(() => {
          return cartCount.value === 0;
        });

        const cartCategories = computed(() => {
          const categories = new Set();

          for (const itemId of Object.keys(cart.value)) {
            const item = menuItems.value.find((menuItem) => menuItem.item_id === itemId);
            if (item) {
              categories.add(item.category);
            }
          }

          return categories;
        });

        const pairSuggestion = computed(() => {
          if (cartIsEmpty.value) return null;
          const hasFood = cartCategories.value.has('food');
          const hasDrink = cartCategories.value.has('drink');

          if (hasFood && !hasDrink) {
            return { category: 'drink', label: 'Add a Drink' };
          }

          if (hasDrink && !hasFood) {
            return { category: 'food', label: 'Add a Meal' };
          }

          return null;
        });

        const showSuggestedCategory = () => {
          if (!pairSuggestion.value) return;
          searchQuery.value = '';
          selectedCategory.value = pairSuggestion.value.category;
          syncMenuUrl();
          window.scrollTo({ top: 0, behavior: 'smooth' });
        };

        const goToCheckout = () => {
          if (!cartIsEmpty.value) {
            window.location.href = 'checkout.php';
          }
        };

        onMounted(() => {
          loadCart();
          loadMenuFilters();
          fetchMenu();
        });

        return {
          cart,
          availableMenu,
          soldOutMenu,
          getItemImage,
          increaseQty,
          decreaseQty,
          loading,
          selectedCategory,
          searchQuery,
          setCategory,
          syncMenuUrl,
          cartCount,
          cartIsEmpty,
          pairSuggestion,
          showSuggestedCategory,
          goToCheckout
        };
      }
    }).mount('#app');
  </script>
</body>

</html>
