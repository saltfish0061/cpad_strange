<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Order Confirmed - Universal Sambal</title>
  <script src="../../js/vue.global.prod.js"></script>
  <script src="../../js/lottie-player.js"></script>
  <script src="../../js/app-utils.js"></script>
  <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
  <div id="app">
    <main class="app-shell">
      <?php
        $root_path = "../../";
        $active_page = "";
        include '../../libs/customer_header.php';
      ?>

      <section class="page section order-success-section">
        <div class="success-box order-success-card">
          <div class="success-icon" aria-hidden="true">
            <lottie-player
              src="../../animations/success.json"
              background="transparent"
              speed="0.7"
              loop
              autoplay>
            </lottie-player>
          </div>
          <p class="success-eyebrow">Order received</p>
          <h1>You're all set.</h1>
          <p class="success-copy">Your order has been registered and is now waiting for vendor confirmation.</p>
          <div class="success-summary">
            <span>Order ID</span>
            <strong>{{ orderId }}</strong>
          </div>
          <div class="success-next">
            <span class="success-next-dot" aria-hidden="true"></span>
            <div>
              <strong>Awaiting confirmation</strong>
              <span>We will update the order status once the kitchen accepts it.</span>
            </div>
          </div>
          <div class="cta-row">
            <a class="cta" :href="trackOrderLink">Track Order Status</a>
            <a class="cta secondary" href="menu.php">Back to Menu</a>
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
        const orderId = ref('');
        const cartCount = ref(0);
        const trackOrderLink = computed(() => {
          return orderId.value && orderId.value !== 'N/A'
            ? `order_details.php?id=${encodeURIComponent(orderId.value)}`
            : 'my_orders.php';
        });

        const loadCartCount = () => {
          cartCount.value = AppUtils.cart.count(AppUtils.cart.load());
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
