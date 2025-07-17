<script setup>
import { useGenerateImageVariant } from "@core/composable/useGenerateImageVariant";
import authV2ForgotPasswordIllustrationDark from "@images/pages/auth-v2-forgot-password-illustration-dark.png";
import authV2ForgotPasswordIllustrationLight from "@images/pages/auth-v2-forgot-password-illustration-light.png";
import authV2MaskDark from "@images/pages/misc-mask-dark.png";
import authV2MaskLight from "@images/pages/misc-mask-light.png";
import { VNodeRenderer } from "@layouts/components/VNodeRenderer";
import { themeConfig } from "@themeConfig";
import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();

const form = ref({
  email: route.query.email || "",
  password: "",
  password_confirmation: "",
  token: route.params.token || "",
});

const isPasswordVisible = ref(false);
const isConfirmPasswordVisible = ref(false);
const isLoading = ref(false);
const message = ref("");
const status = ref("");
const errors = ref({});

const authThemeImg = useGenerateImageVariant(
  authV2ForgotPasswordIllustrationLight,
  authV2ForgotPasswordIllustrationDark
);
const authThemeMask = useGenerateImageVariant(authV2MaskLight, authV2MaskDark);

const onSubmit = async () => {
  isLoading.value = true;
  message.value = "";
  status.value = "";
  errors.value = {};
  try {
    const response = await $api("/auth/reset-password", {
      method: "POST",
      body: form.value,
    });
    status.value = "success";
    message.value = response.message;
    // Redirect to login with success message after 2 seconds
    setTimeout(() => {
      router.push({
        name: "login",
        query: {
          message:
            "Password reset successfully! You can now log in with your new password.",
          type: "success",
        },
      });
    }, 2000);
  } catch (err) {
    status.value = "error";
    errors.value = err.data?.errors || { email: ["An error occurred"] };
    message.value = err.data?.message || "Failed to reset password";
  } finally {
    isLoading.value = false;
  }
};

definePage({
  meta: {
    layout: "blank",
    unauthenticatedOnly: true,
  },
});
</script>

<template>
  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <div class="position-relative my-sm-16">
      <!-- 👉 Auth card -->
      <VCard
        class="auth-card"
        max-width="450"
        :class="$vuetify.display.smAndUp ? 'pa-6' : 'pa-0'"
      >
        <VCardItem class="justify-center">
          <VCardTitle>
            <RouterLink to="/">
              <div class="app-logo">
                <VNodeRenderer :nodes="themeConfig.app.logo" />
                <!-- <h1 class="app-logo-title">
                  {{ themeConfig.app.title }}
                </h1> -->
              </div>
            </RouterLink>
          </VCardTitle>
        </VCardItem>

        <VCardText>
          <h4 class="text-h4 mb-1 text-center">Reset Your Password</h4>
          <p class="mb-0 text-center form-header">
            Your new password must be different from previously used passwords
          </p>
          <VAlert v-if="message" :type="status" variant="tonal" class="mb-4">
            <VAlertTitle>{{
              status === "success" ? "Success" : "Error"
            }}</VAlertTitle>
            {{ message }}
          </VAlert>
        </VCardText>

        <VCardText>
          <VForm @submit.prevent="onSubmit">
            <VRow>
              <!-- password -->
              <VCol cols="12">
                <AppTextField
                  v-model="form.password"
                  autofocus
                  label="New Password"
                  placeholder="············"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  autocomplete="new-password"
                  :append-inner-icon="
                    isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'
                  "
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                  :error-messages="errors.password"
                  :disabled="isLoading"
                />
              </VCol>
              <!-- Confirm Password -->
              <VCol cols="12">
                <AppTextField
                  v-model="form.password_confirmation"
                  label="Confirm Password"
                  autocomplete="new-password"
                  placeholder="············"
                  :type="isConfirmPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="
                    isConfirmPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'
                  "
                  @click:append-inner="
                    isConfirmPasswordVisible = !isConfirmPasswordVisible
                  "
                  :error-messages="errors.password_confirmation"
                  :disabled="isLoading"
                />
              </VCol>
              <!-- reset password -->
              <VCol cols="12">
                <VBtn
                  block
                  type="submit"
                  :loading="isLoading"
                  :disabled="isLoading"
                >
                  {{ isLoading ? "Resetting..." : "Set New Password" }}
                </VBtn>
              </VCol>
              <!-- back to login -->
              <VCol cols="12">
                <RouterLink
                  class="d-flex align-center justify-center"
                  :to="{ name: 'login' }"
                >
                  <VIcon
                    icon="tabler-chevron-left"
                    size="20"
                    class="me-1 flip-in-rtl"
                  />
                  <span>Back to login</span>
                </RouterLink>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </div>
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>
