<script setup>
definePage({
  meta: {
    layout: "default",
    action: "read",
    subject: "Settings",
  },
});

const currentPassword = ref("");
const newPassword = ref("");
const confirmPassword = ref("");
const email = ref("john.doe@example.com");
const loading = ref(false);

const accountSettings = ref({
  twoFactorAuth: true,
  sessionTimeout: 30,
  language: "en",
  timezone: "Europe/Zurich",
  currency: "EUR",
});

const passwordRules = {
  currentPassword: [(v) => !!v || "Current password is required"],
  newPassword: [
    (v) => !!v || "New password is required",
    (v) => v.length >= 8 || "Password must be at least 8 characters",
    (v) =>
      /[A-Z]/.test(v) || "Password must contain at least one uppercase letter",
    (v) =>
      /[a-z]/.test(v) || "Password must contain at least one lowercase letter",
    (v) => /[0-9]/.test(v) || "Password must contain at least one number",
  ],
  confirmPassword: [
    (v) => !!v || "Please confirm your password",
    (v) => v === newPassword.value || "Passwords do not match",
  ],
};

const refPasswordForm = ref();

const languageOptions = [
  { title: "English", value: "en" },
  { title: "German", value: "de" },
  { title: "French", value: "fr" },
  { title: "Italian", value: "it" },
  { title: "Spanish", value: "es" },
];

const timezoneOptions = [
  { title: "Europe/Zurich", value: "Europe/Zurich" },
  { title: "Europe/London", value: "Europe/London" },
  { title: "Europe/Paris", value: "Europe/Paris" },
  { title: "America/New_York", value: "America/New_York" },
  { title: "Asia/Tokyo", value: "Asia/Tokyo" },
];

const currencyOptions = [
  { title: "Euro (€)", value: "EUR" },
  { title: "US Dollar ($)", value: "USD" },
  { title: "British Pound (£)", value: "GBP" },
  { title: "Swiss Franc (CHF)", value: "CHF" },
];

const sessionTimeoutOptions = [
  { title: "15 minutes", value: 15 },
  { title: "30 minutes", value: 30 },
  { title: "1 hour", value: 60 },
  { title: "2 hours", value: 120 },
  { title: "Never", value: 0 },
];

const changePassword = async () => {
  const { valid } = await refPasswordForm.value.validate();

  if (!valid) return;

  loading.value = true;

  try {
    // Here you would typically send the password change request to your API
    console.log("Changing password...");

    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Reset form
    currentPassword.value = "";
    newPassword.value = "";
    confirmPassword.value = "";

    // Show success message
    // You can use a toast notification here
  } catch (error) {
    console.error("Error changing password:", error);
  } finally {
    loading.value = false;
  }
};

const saveAccountSettings = async () => {
  loading.value = true;

  try {
    // Here you would typically send the settings to your API
    console.log("Saving account settings:", accountSettings.value);

    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Show success message
    // You can use a toast notification here
  } catch (error) {
    console.error("Error saving settings:", error);
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div>
    <!-- Password Change Section -->
    <VCard class="mb-6">
      <VCardTitle class="pa-6">
        <h3 class="text-h5 font-weight-bold">Change Password</h3>
      </VCardTitle>

      <VDivider />

      <VCardText class="pa-6">
        <VForm ref="refPasswordForm" @submit.prevent="changePassword">
          <VRow>
            <VCol cols="12" md="6">
              <VTextField
                v-model="currentPassword"
                label="Current Password"
                type="password"
                placeholder="Enter your current password"
                :rules="passwordRules.currentPassword"
                variant="outlined"
                required
              />
            </VCol>

            <VCol cols="12" md="6">
              <VTextField
                v-model="newPassword"
                label="New Password"
                type="password"
                placeholder="Enter your new password"
                :rules="passwordRules.newPassword"
                variant="outlined"
                required
              />
            </VCol>

            <VCol cols="12" md="6">
              <VTextField
                v-model="confirmPassword"
                label="Confirm New Password"
                type="password"
                placeholder="Confirm your new password"
                :rules="passwordRules.confirmPassword"
                variant="outlined"
                required
              />
            </VCol>

            <VCol cols="12" md="6" class="d-flex align-end">
              <VBtn
                type="submit"
                color="primary"
                :loading="loading"
                prepend-icon="tabler-lock"
              >
                Change Password
              </VBtn>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </VCard>

    <!-- Account Settings Section -->
    <VCard class="mb-6">
      <VCardTitle class="pa-6">
        <h3 class="text-h5 font-weight-bold">Account Settings</h3>
      </VCardTitle>

      <VDivider />

      <VCardText class="pa-6">
        <VRow>
          <VCol cols="12" md="6">
            <VTextField
              v-model="email"
              label="Email Address"
              placeholder="Enter your email address"
              variant="outlined"
              disabled
            />
            <p class="text-caption text-medium-emphasis mt-1">
              Contact support to change your email address
            </p>
          </VCol>

          <VCol cols="12" md="6">
            <VSelect
              v-model="accountSettings.language"
              :items="languageOptions"
              label="Language"
              variant="outlined"
            />
          </VCol>

          <VCol cols="12" md="6">
            <VSelect
              v-model="accountSettings.timezone"
              :items="timezoneOptions"
              label="Timezone"
              variant="outlined"
            />
          </VCol>

          <VCol cols="12" md="6">
            <VSelect
              v-model="accountSettings.currency"
              :items="currencyOptions"
              label="Currency"
              variant="outlined"
            />
          </VCol>

          <VCol cols="12" md="6">
            <VSelect
              v-model="accountSettings.sessionTimeout"
              :items="sessionTimeoutOptions"
              label="Session Timeout"
              variant="outlined"
            />
          </VCol>

          <VCol cols="12" md="6" class="d-flex align-end">
            <VBtn
              color="primary"
              :loading="loading"
              prepend-icon="tabler-settings"
              @click="saveAccountSettings"
            >
              Save Settings
            </VBtn>
          </VCol>
        </VRow>
      </VCardText>
    </VCard>

    <!-- Security Settings Section -->
    <VCard class="mb-6">
      <VCardTitle class="pa-6">
        <h3 class="text-h5 font-weight-bold">Security Settings</h3>
      </VCardTitle>

      <VDivider />

      <VCardText class="pa-6">
        <VRow>
          <VCol cols="12">
            <VCard variant="outlined" class="pa-4">
              <div class="d-flex align-center justify-space-between">
                <div>
                  <h4 class="text-h6 font-weight-medium">
                    Two-Factor Authentication
                  </h4>
                  <p class="text-body-2 text-medium-emphasis mt-1">
                    Add an extra layer of security to your account
                  </p>
                </div>
                <VSwitch
                  v-model="accountSettings.twoFactorAuth"
                  color="primary"
                />
              </div>
            </VCard>
          </VCol>

          <VCol cols="12">
            <VCard variant="outlined" class="pa-4">
              <div class="d-flex align-center justify-space-between">
                <div>
                  <h4 class="text-h6 font-weight-medium">
                    Login Notifications
                  </h4>
                  <p class="text-body-2 text-medium-emphasis mt-1">
                    Get notified when someone logs into your account
                  </p>
                </div>
                <VSwitch
                  v-model="accountSettings.loginNotifications"
                  color="primary"
                />
              </div>
            </VCard>
          </VCol>

          <VCol cols="12">
            <VCard variant="outlined" class="pa-4">
              <div class="d-flex align-center justify-space-between">
                <div>
                  <h4 class="text-h6 font-weight-medium">Device Management</h4>
                  <p class="text-body-2 text-medium-emphasis mt-1">
                    Manage devices that have access to your account
                  </p>
                </div>
                <VBtn
                  variant="outlined"
                  size="small"
                  @click="() => $router.push('/device-management')"
                >
                  Manage Devices
                </VBtn>
              </div>
            </VCard>
          </VCol>
        </VRow>
      </VCardText>
    </VCard>

    <!-- Danger Zone Section -->
    <VCard>
      <VCardTitle class="pa-6">
        <h3 class="text-h5 font-weight-bold text-error">Danger Zone</h3>
      </VCardTitle>

      <VDivider />

      <VCardText class="pa-6">
        <VRow>
          <VCol cols="12">
            <VCard variant="outlined" class="pa-4 border-error">
              <div class="d-flex align-center justify-space-between">
                <div>
                  <h4 class="text-h6 font-weight-medium text-error">
                    Delete Account
                  </h4>
                  <p class="text-body-2 text-medium-emphasis mt-1">
                    Permanently delete your account and all associated data
                  </p>
                </div>
                <VBtn
                  color="error"
                  variant="outlined"
                  size="small"
                  @click="() => $router.push('/delete-account')"
                >
                  Delete Account
                </VBtn>
              </div>
            </VCard>
          </VCol>

          <VCol cols="12">
            <VCard variant="outlined" class="pa-4 border-warning">
              <div class="d-flex align-center justify-space-between">
                <div>
                  <h4 class="text-h6 font-weight-medium text-warning">
                    Export Data
                  </h4>
                  <p class="text-body-2 text-medium-emphasis mt-1">
                    Download a copy of your data
                  </p>
                </div>
                <VBtn
                  color="warning"
                  variant="outlined"
                  size="small"
                  @click="() => $router.push('/export-data')"
                >
                  Export Data
                </VBtn>
              </div>
            </VCard>
          </VCol>
        </VRow>
      </VCardText>
    </VCard>
  </div>
</template>

<style scoped>
.v-card {
  border-radius: 8px;
}

.border-error {
  border-color: rgb(var(--v-theme-error)) !important;
}

.border-warning {
  border-color: rgb(var(--v-theme-warning)) !important;
}
</style>
