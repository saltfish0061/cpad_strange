(function () {
  const MENU_IMAGE_MAP = {
    F001: 'images/food/ayam_merah.png',
    F002: 'images/food/ayam_hijau.png',
    F003: 'images/food/brownsugar.png',
    F004: 'images/food/harimau.png',
    F005: 'images/food/bawean.png',
    F006: 'images/food/2rasa.png',
    F007: 'images/food/3rasa.png',
    D001: 'images/drink/orange.png',
    D002: 'images/drink/carrot.png',
    D003: 'images/drink/carrot_susu.png',
    D004: 'images/drink/tembikai.png',
    D005: 'images/drink/tembikai_susu.png',
    D006: 'images/drink/apple.png'
  };

  const readJson = (key, fallback) => {
    try {
      const saved = localStorage.getItem(key);
      return saved ? JSON.parse(saved) : fallback;
    } catch (error) {
      console.error(`Failed to read ${key}:`, error);
      return fallback;
    }
  };

  const writeJson = (key, value) => {
    localStorage.setItem(key, JSON.stringify(value));
  };

  const cart = {
    load() {
      return readJson('cart', {});
    },
    save(nextCart) {
      writeJson('cart', nextCart || {});
      window.syncHeaderCartCount?.();
    },
    count(nextCart) {
      return Object.values(nextCart || {}).reduce((sum, qty) => sum + Number(qty || 0), 0);
    },
    removeUnavailable(nextCart, menuItems) {
      const availableIds = new Set(
        (menuItems || [])
          .filter((item) => Number(item.is_available))
          .map((item) => item.item_id)
      );
      const cleaned = { ...(nextCart || {}) };
      let changed = false;

      Object.keys(cleaned).forEach((itemId) => {
        if (!availableIds.has(itemId)) {
          delete cleaned[itemId];
          changed = true;
        }
      });

      return { cart: cleaned, changed };
    }
  };

  const session = {
    loadUser() {
      return readJson('currentUser', null);
    },
    saveUser(user) {
      if (user) {
        writeJson('currentUser', user);
      } else {
        localStorage.removeItem('currentUser');
      }
    },
    clear() {
      localStorage.removeItem('currentUser');
      localStorage.removeItem('cart');
      localStorage.removeItem('orderNote');
    }
  };

  const orderNote = {
    load() {
      return localStorage.getItem('orderNote') || '';
    },
    save(note) {
      localStorage.setItem('orderNote', note || '');
    },
    clear() {
      localStorage.removeItem('orderNote');
    }
  };

  const images = {
    item(itemId, rootPath = '') {
      return `${rootPath}${MENU_IMAGE_MAP[itemId] || 'images/food/test.png'}`;
    }
  };

  const toast = {
    show(message, type = 'success') {
      if (!message) return;

      let stack = document.querySelector('.toast-stack');
      if (!stack) {
        stack = document.createElement('div');
        stack.className = 'toast-stack';
        stack.setAttribute('aria-live', 'polite');
        stack.setAttribute('aria-atomic', 'true');
        document.body.appendChild(stack);
      }

      const toastEl = document.createElement('div');
      toastEl.className = `toast-message ${type}`;
      toastEl.textContent = message;
      stack.appendChild(toastEl);

      window.setTimeout(() => {
        toastEl.classList.add('leaving');
        window.setTimeout(() => toastEl.remove(), 180);
      }, 2600);
    }
  };

  const header = {
    syncCartCount() {
      const countEls = document.querySelectorAll('.header-cart-count');
      const count = cart.count(cart.load());
      countEls.forEach((countEl) => {
        countEl.innerText = count;
      });
    },
    syncVendorLink() {
      const vendorLink = document.getElementById('header-vendor-link');
      if (!vendorLink) return;
      const currentUser = session.loadUser();
      vendorLink.hidden = currentUser?.role !== 'admin';
    },
    syncAuth() {
      const loginLink = document.getElementById('header-login-link');
      const logoutButton = document.getElementById('header-logout-button');
      if (!loginLink || !logoutButton) return;

      const currentUser = session.loadUser();
      loginLink.hidden = Boolean(currentUser);
      logoutButton.hidden = !currentUser;
    },
    syncAll() {
      header.syncCartCount();
      header.syncVendorLink();
      header.syncAuth();
    },
    animateCart() {
      const cartButtons = document.querySelectorAll('.cart-button');
      if (!cartButtons.length) return;

      cartButtons.forEach((cartButton) => {
        cartButton.classList.remove('cart-wiggle');
        void cartButton.offsetWidth;
        cartButton.classList.add('cart-wiggle');
      });

      window.setTimeout(() => {
        cartButtons.forEach((cartButton) => {
          cartButton.classList.remove('cart-wiggle');
        });
      }, 450);
    }
  };

  window.AppUtils = {
    storage: { readJson, writeJson },
    cart,
    session,
    orderNote,
    images,
    toast,
    header
  };
})();
