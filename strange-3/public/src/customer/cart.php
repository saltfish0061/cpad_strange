<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart - Universal Sambal</title>
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
            <p class="eyebrow">Your Selection</p>
            <h2>Your Shopping Cart</h2>
          </div>
        </div>

        <div v-if="cartChecking" class="loading-surface">
          <div class="loading-card" role="status" aria-live="polite">
            <span class="loading-spinner" aria-hidden="true"></span>
            <strong>Loading</strong>
          </div>
          <div class="loading-skeleton-cart" aria-hidden="true">
            <span class="cart-skeleton-item"></span>
            <span class="cart-skeleton-summary"></span>
            <span class="cart-skeleton-item"></span>
            <span class="cart-skeleton-item"></span>
          </div>
        </div>

        <div v-else-if="cartIsEmpty" class="empty-panel">
          <h1>Your Cart is Empty</h1>
          <p>Add some delicious meals or refreshing drinks to your cart to get started.</p>
          <a class="cta" href="menu.php" style="width: auto;">Explore Menu</a>
        </div>

        <div v-else class="cart-layout">
          <div class="cart-items-list">
            <div
              class="cart-item-shell"
              :class="{
                revealed: revealedCartItem === item.item_id,
                dragging: dragState?.itemId === item.item_id,
                'release-ready': deleteReadyCartItem === item.item_id
              }"
              v-for="item in cartItemsList"
              :key="item.item_id"
            >
              <button class="cart-delete-reveal" @click="removeItem(item.item_id)" aria-label="Remove item">
                <svg class="trash-icon" viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M3 6h18"></path>
                  <path d="M8 6V4h8v2"></path>
                  <path d="M6 6l1 15h10l1-15"></path>
                  <path d="M10 11v6"></path>
                  <path d="M14 11v6"></path>
                </svg>
              </button>

              <div
                class="cart-item-card"
                :style="{ transform: `translateX(${getCartItemOffset(item.item_id)}px)` }"
                @pointerdown="startCartItemDrag($event, item.item_id)"
                @pointermove="moveCartItemDrag"
                @pointerup="endCartItemDrag"
                @pointercancel="endCartItemDrag"
              >
                <img :src="getItemImage(item.item_id)" :alt="item.name" class="cart-item-img">
                <div class="cart-item-info">
                  <h4>{{ item.name }}</h4>
                  <p>RM {{ parseFloat(item.price).toFixed(2) }}</p>
                  <div class="qty-controls cart-item-qty">
                    <button class="qty-btn" @click="decreaseQty(item.item_id)">-</button>
                    <span class="qty-val">{{ item.quantity }}</span>
                    <button class="qty-btn" @click="increaseQty(item.item_id)">+</button>
                  </div>
                </div>
                <div class="cart-item-price">
                  RM {{ item.subtotal.toFixed(2) }}
                </div>
              </div>
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

      <?php include '../../libs/footer.php'; ?>
    </main>
  </div>

  <script>
    const { createApp, computed, onMounted, ref } = Vue;

    createApp({
      setup() {
        const cart = ref({});
        const menuItems = ref([]);
        const cartChecking = ref(true);
        const orderNote = ref('');
        const revealedCartItem = ref(null);
        const deleteReadyCartItem = ref(null);
        const dragState = ref(null);
        const dragOffsets = ref({});
        const cartRevealOffset = -72;
        const cartMaxDragOffset = -112;
        const cartReleaseDeleteOffset = -96;

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

        const loadOrderNote = () => {
          orderNote.value = AppUtils.orderNote.load();
        };

        const saveOrderNote = () => {
          AppUtils.orderNote.save(orderNote.value);
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
          } finally {
            cartChecking.value = false;
          }
        };

        const getItemImage = (itemId) => {
          return AppUtils.images.item(itemId, '../../');
        };

        const increaseQty = (itemId) => {
          if (cart.value[itemId]) {
            cart.value[itemId]++;
          }
          saveCart();
          if (typeof animateHeaderCartWiggle === 'function') {
            animateHeaderCartWiggle();
          }
          if (typeof showToast === 'function') {
            showToast('Cart quantity updated.');
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
            if (typeof showToast === 'function') {
              showToast('Cart quantity updated.');
            }
          }
        };

        const removeItem = (itemId) => {
          delete cart.value[itemId];
          revealedCartItem.value = null;
          deleteReadyCartItem.value = null;
          delete dragOffsets.value[itemId];
          saveCart();
          if (typeof showToast === 'function') {
            showToast('Item removed from cart.');
          }
        };

        const getCartItemOffset = (itemId) => {
          if (dragOffsets.value[itemId] !== undefined) {
            return dragOffsets.value[itemId];
          }
          return revealedCartItem.value === itemId ? cartRevealOffset : 0;
        };

        const startCartItemDrag = (event, itemId) => {
          if (event.target.closest('button, a, input, textarea, select')) return;

          const wasRevealed = revealedCartItem.value === itemId;
          revealedCartItem.value = wasRevealed ? itemId : null;
          deleteReadyCartItem.value = null;
          dragOffsets.value = wasRevealed ? { [itemId]: cartRevealOffset } : {};

          dragState.value = {
            itemId,
            startX: event.clientX,
            baseOffset: wasRevealed ? cartRevealOffset : 0
          };
          event.currentTarget.setPointerCapture?.(event.pointerId);
        };

        const moveCartItemDrag = (event) => {
          if (!dragState.value) return;

          const delta = event.clientX - dragState.value.startX;
          const nextOffset = Math.max(cartMaxDragOffset, Math.min(0, dragState.value.baseOffset + delta));
          const itemId = dragState.value.itemId;
          dragOffsets.value = {
            ...dragOffsets.value,
            [itemId]: nextOffset
          };
          deleteReadyCartItem.value = nextOffset <= cartReleaseDeleteOffset ? itemId : null;
        };

        const endCartItemDrag = () => {
          if (!dragState.value) return;

          const itemId = dragState.value.itemId;
          const offset = dragOffsets.value[itemId] || 0;
          const shouldDelete = offset <= cartReleaseDeleteOffset;

          dragState.value = null;
          deleteReadyCartItem.value = null;

          if (shouldDelete) {
            removeItem(itemId);
            return;
          }

          revealedCartItem.value = offset < -36 ? itemId : null;
          dragOffsets.value = {
            ...dragOffsets.value,
            [itemId]: revealedCartItem.value === itemId ? cartRevealOffset : 0
          };
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
          return AppUtils.cart.count(cart.value);
        });

        onMounted(() => {
          loadCart();
          loadOrderNote();
          if (Object.keys(cart.value).length) {
            fetchMenu();
          } else {
            cartChecking.value = false;
          }
        });

        return {
          cartChecking,
          cartIsEmpty,
          cartItemsList,
          cartCount,
          cartTotal,
          increaseQty,
          decreaseQty,
          removeItem,
          revealedCartItem,
          deleteReadyCartItem,
          dragState,
          getCartItemOffset,
          startCartItemDrag,
          moveCartItemDrag,
          endCartItemDrag,
          getItemImage,
          orderNote,
          saveOrderNote
        };
      }
    }).mount('#app');
  </script>
</body>

</html>
