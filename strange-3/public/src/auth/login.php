<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Login - Universal Sambal</title>
  <script src="../../js/vue.global.prod.js"></script>
  <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
  <div id="app">
    <main class="app-shell auth-only-shell">
      <section class="auth-page">
        <div class="auth-shell">
          <div class="auth-copy">
            <h1>{{ authMode === 'login' ? 'Welcome back to the sambal counter' : 'Join the sambal counter' }}</h1>
          </div>

          <form v-if="authMode === 'login'" class="auth-card" @submit.prevent="login">
              <div class="auth-switch" aria-label="Authentication pages">
                <button class="active" type="button" @click="setAuthMode('login')">Login</button>
                <button type="button" @click="setAuthMode('register')">Register</button>
              </div>
              <div class="auth-card-head">
                <h2>Enter your account</h2>
                <span>Use your exact username and password.</span>
              </div>

              <label class="form-field">
                <span>Username</span>
                <input v-model="loginForm.username" placeholder="Your username" autocomplete="username">
              </label>

              <label class="form-field">
                <span>Password</span>
                <input v-model="loginForm.password" type="password" placeholder="Your password"
                  autocomplete="current-password">
              </label>

              <p v-if="loginError" class="form-error">{{ loginError }}</p>

              <button class="auth-submit" type="submit">Login</button>
              <div class="auth-link-row">
                <a href="../../index.php">Back home</a>
              </div>
          </form>

          <form v-else class="auth-card" @submit.prevent="register">
              <div class="auth-switch" aria-label="Authentication pages">
                <button type="button" @click="setAuthMode('login')">Login</button>
                <button class="active" type="button" @click="setAuthMode('register')">Register</button>
              </div>
              <div class="auth-card-head">
                <h2>Create your account</h2>
                <span>Your username must be unique and case-sensitive.</span>
              </div>

              <label class="form-field">
                <span>Username</span>
                <input v-model="registerForm.username" placeholder="Choose a username" autocomplete="username">
              </label>

              <label class="form-field">
                <span>Phone</span>
                <input v-model="registerForm.phone" placeholder="Your phone number" autocomplete="tel">
              </label>

              <label class="form-field">
                <span>Address</span>
                <textarea class="form-control" v-model="registerForm.address" placeholder="Your delivery address" autocomplete="street-address"></textarea>
              </label>

              <label class="form-field">
                <span>Password</span>
                <input v-model="registerForm.password" type="password" placeholder="Create password" autocomplete="new-password">
              </label>

              <label class="form-field">
                <span>Confirm Password</span>
                <input v-model="registerForm.confirm_password" type="password" placeholder="Repeat password" autocomplete="new-password">
              </label>

              <p v-if="registerError" class="form-error">{{ registerError }}</p>

              <button class="auth-submit" type="submit" :disabled="registering">
                {{ registering ? 'Creating...' : 'Create Account' }}
              </button>
              <div class="auth-link-row">
                <a href="../../index.php">Back home</a>
              </div>
          </form>
        </div>
      </section>
    </main>
  </div>

  <script>
    const { createApp, ref } = Vue;

    createApp({
      setup() {
        const params = new URLSearchParams(window.location.search);
        const authMode = ref(params.get('mode') === 'register' ? 'register' : 'login');
        const loginError = ref('');
        const registering = ref(false);
        const registerError = ref('');
        const loginForm = ref({
          username: '',
          password: ''
        });
        const registerForm = ref({
          username: '',
          phone: '',
          address: '',
          password: '',
          confirm_password: ''
        });

        const setAuthMode = (mode) => {
          authMode.value = mode;
          loginError.value = '';
          registerError.value = '';
          const nextUrl = mode === 'register' ? 'login.php?mode=register' : 'login.php';
          window.history.replaceState({}, '', nextUrl);
        };

        const login = async () => {
          loginError.value = '';
          const username = loginForm.value.username.trim();
          const password = loginForm.value.password.trim();

          if (!username || !password) {
            loginError.value = 'Please enter your username and password.';
            return;
          }

          try {
            const response = await fetch('../../api/login', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({
                username,
                password
              })
            });
            const data = await response.json().catch(() => ({}));

            if (!response.ok || !data.user) {
              throw new Error(data.error || 'Username or password is wrong.');
            }

            localStorage.setItem('currentUser', JSON.stringify(data.user));
            localStorage.removeItem('cart');
            localStorage.removeItem('orderNote');
            window.location.href = data.user.role === 'admin' ? '../vendor/dashboard.php' : '../customer/profile.php';
          } catch (error) {
            loginError.value = error.message || 'Unable to login right now.';
          }
        };

        const register = async () => {
          registerError.value = '';
          registering.value = true;

          try {
            const response = await fetch('../../api/register', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify(registerForm.value)
            });
            const data = await response.json().catch(() => ({}));

            if (!response.ok || data.status === 'error' || !data.user) {
              throw new Error(data.errors ? data.errors.join(' ') : (data.message || 'Unable to create account.'));
            }

            localStorage.setItem('currentUser', JSON.stringify(data.user));
            localStorage.removeItem('cart');
            localStorage.removeItem('orderNote');
            window.location.href = '../customer/profile.php';
          } catch (error) {
            registerError.value = error.message || 'Unable to register right now.';
          } finally {
            registering.value = false;
          }
        };

        return {
          authMode,
          login,
          loginError,
          loginForm,
          register,
          registerError,
          registerForm,
          registering,
          setAuthMode
        };
      }
    }).mount('#app');
  </script>
</body>

</html>
