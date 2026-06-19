<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Universal Sambal</title>
  <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
  <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
  <div id="app">
    <main class="app-shell auth-only-shell">
      <section class="auth-page">
        <div class="auth-shell">
          <div class="auth-copy">
            <a class="auth-brand" href="../../index.php" aria-label="Universal Sambal home">
              <span class="brand-mark">US</span>
              <span>Universal Sambal</span>
            </a>
            <h1>Welcome back to the sambal counter</h1>
          </div>

          <form class="auth-card" @submit.prevent="login">
            <div class="auth-card-head">
              <p class="eyebrow">Login</p>
              <h2>Enter your account</h2>
            </div>

            <label class="form-field">
              <span>User ID</span>
              <input v-model="loginForm.user_id" placeholder="Example: C001 or A001" autocomplete="username">
            </label>

            <label class="form-field">
              <span>Password</span>
              <input v-model="loginForm.password" type="password" placeholder="Your password" autocomplete="current-password">
            </label>

            <p v-if="loginError" class="form-error">{{ loginError }}</p>

            <button class="auth-submit" type="submit">Login</button>
            <a class="pill-button" href="../../index.php">Back Home</a>
          </form>
        </div>
      </section>
    </main>
  </div>

  <script>
    const { createApp, ref } = Vue;

    createApp({
      setup() {
        const loginError = ref('');
        const loginForm = ref({
          user_id: '',
          password: ''
        });

        const login = async () => {
          loginError.value = '';
          const userId = loginForm.value.user_id.trim();
          const password = loginForm.value.password.trim();

          if (!userId || !password) {
            loginError.value = 'Please enter your user ID and password.';
            return;
          }

          try {
            const response = await fetch('../../api/login', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({
                user_id: userId,
                password
              })
            });
            const data = await response.json().catch(() => ({}));

            if (!response.ok || !data.user) {
              throw new Error(data.error || 'Invalid login credentials.');
            }

            localStorage.setItem('currentUser', JSON.stringify(data.user));
            window.location.href = data.user.role === 'admin' ? '../vendor/dashboard.php' : '../customer/profile.php';
          } catch (error) {
            loginError.value = error.message || 'Unable to login right now.';
          }
        };

        return {
          login,
          loginError,
          loginForm
        };
      }
    }).mount('#app');
  </script>
</body>

</html>
