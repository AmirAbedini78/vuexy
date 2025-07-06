<template>
  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <div class="position-relative my-sm-16">
      <VNodeRenderer
        :nodes="h('div', { innerHTML: authV1TopShape })"
        class="text-primary auth-v1-top-shape d-none d-sm-block"
      />
      <VNodeRenderer
        :nodes="h('div', { innerHTML: authV1BottomShape })"
        class="text-primary auth-v1-bottom-shape d-none d-sm-block"
      />
      <VCard
        class="auth-card"
        max-width="460"
        :class="$vuetify.display.smAndUp ? 'pa-6' : 'pa-2'"
      >
        <VCardItem class="justify-center">
          <VCardTitle>
            <RouterLink to="/">
              <div class="app-logo">
                <VNodeRenderer :nodes="themeConfig.app.logo" />
                <h1 class="app-logo-title">
                  {{ themeConfig.app.title }}
                </h1>
              </div>
            </RouterLink>
          </VCardTitle>
        </VCardItem>
        <VCardText>
          <template v-if="status === 'verifying'">
            <h4 class="text-h4 mb-1">تأیید ایمیل</h4>
            <VAlert type="info" variant="tonal" class="mb-4">
              <VAlertTitle>آماده تأیید</VAlertTitle>
              لطفاً برای تأیید آدرس ایمیل خود روی دکمه زیر کلیک کنید.
            </VAlert>
            <VBtn
              block
              :loading="isLoading"
              :disabled="isLoading"
              @click="verifyEmail"
            >
              {{ isLoading ? "در حال تأیید..." : "تأیید ایمیل" }}
            </VBtn>
          </template>
          <template v-else-if="status === 'success'">
            <h4 class="text-h4 mb-1">ایمیل تأیید شد ✉️</h4>
            <VAlert type="success" variant="tonal" class="mb-4">
              <VAlertTitle>ایمیل تأیید شد!</VAlertTitle>
              ایمیل شما با موفقیت تأیید شد. به صفحه
              <RouterLink :to="{ name: 'login' }">ورود</RouterLink> هدایت خواهید
              شد.
            </VAlert>
          </template>
          <template v-else>
            <h4 class="text-h4 mb-1">تأیید ناموفق</h4>
            <VAlert type="error" variant="tonal" class="mb-4">
              <VAlertTitle>خطا در تأیید</VAlertTitle>
              {{ errorMessage }}
            </VAlert>
            <div class="d-flex align-center justify-center">
              <span class="me-1">ایمیل دریافت نکردید؟</span>
              <RouterLink :to="{ name: 'register' }">ارسال مجدد</RouterLink>
            </div>
          </template>
        </VCardText>
      </VCard>
    </div>
  </div>
</template>

<script setup>
import authV1BottomShape from "@images/svg/auth-v1-bottom-shape.svg?raw";
import authV1TopShape from "@images/svg/auth-v1-top-shape.svg?raw";
import { VNodeRenderer } from "@layouts/components/VNodeRenderer";
import { themeConfig } from "@themeConfig";
import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();
const status = ref("verifying");
const errorMessage = ref("");
const isLoading = ref(false);

const verifyEmail = async () => {
  isLoading.value = true;
  const { token } = route.params;
  console.log("Attempting to verify token:", token);
  const url = `${import.meta.env.VITE_API_BASE_URL}/verify/${token}`;
  try {
    const response = await $fetch(url, {
      method: "GET",
      credentials: "include",
      headers: {
        Accept: "application/json",
      },
    });
    console.log("Verification response:", response);
    status.value = "success";
    setTimeout(
      () => router.push({ name: "login", query: { verified: "true" } }),
      2000
    );
  } catch (err) {
    console.error("Verification error:", err);
    status.value = "error";
    errorMessage.value =
      err?.data?.message || "لینک تأیید نامعتبر است یا منقضی شده است.";
  } finally {
    isLoading.value = false;
  }
};

definePage({
  meta: {
    layout: "blank",
    public: true,
  },
});
</script>

<style lang="scss">
@use "@core-scss/template/pages/page-auth";
</style>
