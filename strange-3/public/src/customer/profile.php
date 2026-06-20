<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile - Universal Sambal</title>
  <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
  <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
  <div id="app">
    <main class="app-shell">
      <?php
        $root_path = "../../";
        $active_page = "profile";
        include '../../includes/customer_header.php';
      ?>

      <section class="page section profile-module">
        <div class="section-head">
          <div>
            <p class="eyebrow">Customer profile</p>
            <h2>My Profile</h2>
          </div>
          <p>Manage your username, phone number, address, and account password.</p>
        </div>

        <div v-if="!currentUser" class="empty-panel">
          <h1>Login Required</h1>
          <p>Login first to view and update your profile.</p>
          <a class="cta" href="../auth/login.php">Login</a>
        </div>

        <div v-else-if="profileLoading" class="empty-panel">
          <h1>Loading Profile...</h1>
          <p>Fetching your saved account details.</p>
        </div>

        <div v-else class="profile-layout">
          <aside class="profile-summary">
            <div class="profile-avatar">{{ profileInitials }}</div>
            <h3>{{ currentUser.name }}</h3>
            <p>{{ currentUser.phone }}</p>
            <p class="muted-text">{{ currentUser.address || 'No address saved' }}</p>
            <a class="small-action" href="my_orders.php">View Orders</a>
          </aside>

          <div class="profile-stack">
            <form class="profile-form" @submit.prevent="saveProfile">
              <p v-if="profileMessage" class="vendor-alert">{{ profileMessage }}</p>
              <p v-if="profileError" class="form-error">{{ profileError }}</p>

              <label class="form-field">
                <span>Username</span>
                <input v-model="profileForm.name" :disabled="!isEditingProfile" required>
              </label>

              <label class="form-field">
                <span>Phone</span>
                <input v-model="profileForm.phone" :disabled="!isEditingProfile" required>
              </label>

              <label class="form-field wide">
                <span>Address</span>
                <textarea class="form-control" v-model="profileForm.address" :disabled="!isEditingProfile" required></textarea>
              </label>

              <div class="inline-actions wide">
                <button v-if="!isEditingProfile" class="small-action primary" type="button" @click="startEditingProfile">
                  Edit Profile
                </button>
                <button v-if="isEditingProfile" class="small-action primary" type="submit" :disabled="profileSaving">
                  {{ profileSaving ? 'Saving...' : 'Save Profile' }}
                </button>
                <button v-if="isEditingProfile" class="small-action" type="button" @click="cancelEditingProfile" :disabled="profileSaving">
                  Cancel
                </button>
                <button class="small-action" type="button" @click="showPasswordForm = !showPasswordForm">
                  {{ showPasswordForm ? 'Cancel Password Change' : 'Change Password' }}
                </button>
              </div>
            </form>

            <form v-if="showPasswordForm" class="profile-form" @submit.prevent="changePassword">
              <p v-if="passwordMessage" class="vendor-alert">{{ passwordMessage }}</p>
              <p v-if="passwordError" class="form-error">{{ passwordError }}</p>

              <label class="form-field wide">
                <span>Old password</span>
                <input v-model="passwordForm.old_password" type="password" autocomplete="current-password" required>
              </label>

              <label class="form-field">
                <span>New password</span>
                <input v-model="passwordForm.new_password" type="password" autocomplete="new-password" required>
              </label>

              <label class="form-field">
                <span>Confirm new password</span>
                <input v-model="passwordForm.confirm_password" type="password" autocomplete="new-password" required>
              </label>

              <div class="inline-actions wide">
                <button class="small-action primary" type="submit" :disabled="passwordSaving">
                  {{ passwordSaving ? 'Updating...' : 'Update Password' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </section>

      <footer class="footer">
        Universal Sambal Profile.
      </footer>
    </main>
  </div>

  <script>
    const { createApp, computed, onMounted, ref } = Vue;

    createApp({
      setup() {
        const currentUser = ref(null);
        const profileLoading = ref(false);
        const profileSaving = ref(false);
        const passwordSaving = ref(false);
        const showPasswordForm = ref(false);
        const isEditingProfile = ref(false);
        const profileMessage = ref('');
        const profileError = ref('');
        const passwordMessage = ref('');
        const passwordError = ref('');
        const profileForm = ref({
          name: '',
          phone: '',
          address: ''
        });
        const passwordForm = ref({
          old_password: '',
          new_password: '',
          confirm_password: ''
        });

        const syncProfileForm = (user) => {
          profileForm.value = {
            name: user?.name || '',
            phone: user?.phone || '',
            address: user?.address || ''
          };
        };

        const loadCurrentUser = () => {
          try {
            const savedUser = localStorage.getItem('currentUser');
            currentUser.value = savedUser ? JSON.parse(savedUser) : null;
            syncProfileForm(currentUser.value);
          } catch (e) {
            currentUser.value = null;
            syncProfileForm(null);
          }
        };

        const apiRequest = async (path, options = {}) => {
          const response = await fetch(`../../api${path}`, {
            headers: {
              'Content-Type': 'application/json',
              ...(options.headers || {})
            },
            ...options
          });
          const data = await response.json().catch(() => ({}));
          if (!response.ok || data.status === 'error') {
            throw new Error(data.errors ? data.errors.join(' ') : (data.message || 'Request failed.'));
          }
          return data;
        };

        const loadProfile = async () => {
          if (!currentUser.value?.user_id) return;
          profileLoading.value = true;
          profileError.value = '';

          try {
            const data = await apiRequest(`/profile/${currentUser.value.user_id}`);
            currentUser.value = data.user;
            localStorage.setItem('currentUser', JSON.stringify(data.user));
            syncProfileForm(data.user);
          } catch (error) {
            profileError.value = error.message || 'Unable to load profile.';
          } finally {
            profileLoading.value = false;
          }
        };

        const saveProfile = async () => {
          if (!currentUser.value?.user_id) return;
          profileSaving.value = true;
          profileMessage.value = '';
          profileError.value = '';

          try {
            const data = await apiRequest(`/profile/${currentUser.value.user_id}`, {
              method: 'PUT',
              body: JSON.stringify(profileForm.value)
            });
            currentUser.value = data.user;
            localStorage.setItem('currentUser', JSON.stringify(data.user));
            syncProfileForm(data.user);
            isEditingProfile.value = false;
            profileMessage.value = 'Profile updated.';
          } catch (error) {
            profileError.value = error.message || 'Unable to save profile.';
          } finally {
            profileSaving.value = false;
          }
        };

        const startEditingProfile = () => {
          profileMessage.value = '';
          profileError.value = '';
          syncProfileForm(currentUser.value);
          isEditingProfile.value = true;
        };

        const cancelEditingProfile = () => {
          syncProfileForm(currentUser.value);
          profileError.value = '';
          isEditingProfile.value = false;
        };

        const changePassword = async () => {
          if (!currentUser.value?.user_id) return;
          passwordSaving.value = true;
          passwordMessage.value = '';
          passwordError.value = '';

          try {
            await apiRequest(`/profile/${currentUser.value.user_id}/password`, {
              method: 'PATCH',
              body: JSON.stringify(passwordForm.value)
            });
            passwordForm.value = {
              old_password: '',
              new_password: '',
              confirm_password: ''
            };
            passwordMessage.value = 'Password updated.';
          } catch (error) {
            passwordError.value = error.message || 'Unable to update password.';
          } finally {
            passwordSaving.value = false;
          }
        };

        const profileInitials = computed(() => {
          const name = currentUser.value?.name || 'US';
          return name
            .split(/\s|_/)
            .filter(Boolean)
            .slice(0, 2)
            .map((part) => part[0])
            .join('')
            .toUpperCase();
        });

        onMounted(() => {
          loadCurrentUser();
          loadProfile();
        });

        return {
          changePassword,
          cancelEditingProfile,
          currentUser,
          isEditingProfile,
          passwordError,
          passwordForm,
          passwordMessage,
          passwordSaving,
          profileError,
          profileForm,
          profileInitials,
          profileLoading,
          profileMessage,
          profileSaving,
          saveProfile,
          showPasswordForm,
          startEditingProfile
        };
      }
    }).mount('#app');
  </script>
</body>

</html>
