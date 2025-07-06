<template>
  <div class="auth-wrapper d-flex align-center justify-center pa-4">
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
        <h4 class="text-h4 mb-1">Login</h4>
        <VForm @submit.prevent="login">
          <VTextField
            v-model="form.email"
            label="Email"
            type="email"
            :error-messages="errors.email"
            class="mb-4"
          />
          <VTextField
            v-model="form.password"
            label="Password"
            type="password"
            :error-messages="errors.password"
            class="mb-4"
          />
          <VBtn type="submit" block :loading="isLoading" :disabled="isLoading">
            {{ isLoading ? "Logging in..." : "Login" }}
          </VBtn>
        </VForm>
        <div v-if="errorMessage" class="text-error mt-4">
          {{ errorMessage }}
        </div>
      </VCardText>
    </VCard>
  </div>
</template>

<script setup>
import { VNodeRenderer } from "@layouts/components/VNodeRenderer";
import { themeConfig } from "@themeConfig";
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const form = ref({
  email: "",
  password: "",
});
const errors = ref({
  email: [],
  password: [],
});
const errorMessage = ref("");
const isLoading = ref(false);

const login = async () => {
  isLoading.value = true;
  errors.value = { email: [], password: [] };
  errorMessage.value = "";

  console.log("Attempting login with:", form.value.email);

  try {
    const response = await fetch(
      `${import.meta.env.VITE_API_BASE_URL}/auth/login`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        credentials: "include",
        body: JSON.stringify(form.value),
      }
    );

    console.log("Response status:", response.status);
    console.log("Response headers:", response.headers);

    const data = await response.json();
    console.log("Login response:", { status: response.status, data });

    if (!response.ok) {
      if (data.errors) {
        errors.value = data.errors;
        errorMessage.value = data.message || "Validation failed";
      } else {
        errorMessage.value = data.message || "Login failed";
      }
      return;
    }

    // Store user data and token
    const userDataCookie = useCookie("userData", {
      maxAge: 60 * 60 * 24 * 7, // 7 days
      secure: false,
      sameSite: "lax",
    });
    const accessTokenCookie = useCookie("accessToken", {
      maxAge: 60 * 60 * 24 * 7, // 7 days
      secure: false,
      sameSite: "lax",
    });

    userDataCookie.value = data.user;
    accessTokenCookie.value = data.access_token;

    console.log("Login successful, redirecting to dashboard...");
    router.push({ name: "dashboard" });
  } catch (err) {
    console.error("Login error:", {
      message: err.message,
      status: err.status,
      data: err.data,
    });
    errorMessage.value = err.message || "An error occurred during login";
    if (err.data?.errors) {
      errors.value = err.data.errors;
    }
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
