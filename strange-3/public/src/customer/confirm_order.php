<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Confirmed - Universal Sambal</title>
  <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
  <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
  <div id="app">
    <main class="app-shell">
      <?php
        $root_path = "../../";
        $active_page = "";
        include '../../includes/customer_header.php';
      ?>

      <section class="page section">
        <div class="success-box">
          <h1>Order Successful!</h1>
          <p>Thank you for dining with us! Your order has been registered and is awaiting vendor confirmation.</p>
          <div>
            <span class="order-id-highlight">Order ID: {{ orderId }}</span>
          </div>
          <div class="cta-row">
            <a class="cta" :href="trackOrderLink">Track Order Status</a>
            <a class="cta secondary" href="menu.php">Return</a>
          </div>
        </div>
      </section>

      <footer class="footer">
        Universal Sambal Order Success. Refined Customer flow.
      </footer>
    </main>
  </div>

  <script>
    const { createApp, computed, onMounted, ref } = Vue;

    createApp({
      setup() {
        const orderId = ref('');
        const cartCount = ref(0);
        const trackOrderLink = computed(() => {
          return orderId.value && orderId.value !== 'N/A'
            ? `order_details.php?id=${encodeURIComponent(orderId.value)}`
            : 'my_orders.php';
        });

        const loadCartCount = () => {
          try {
            const savedCart = localStorage.getItem('cart');
            if (savedCart) {
              const cart = JSON.parse(savedCart);
              cartCount.value = Object.values(cart).reduce((sum, qty) => sum + qty, 0);
            }
          } catch (e) {
            console.error('Failed to load cart:', e);
          }
        };

        onMounted(() => {
          loadCartCount();
          const params = new URLSearchParams(window.location.search);
          orderId.value = params.get('order_id') || 'N/A';
        });

        return {
          orderId,
          cartCount,
          trackOrderLink
        };
      }
    }).mount('#app');
  </script>
</body>

</html>
