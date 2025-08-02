<template>
  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <div class="position-relative my-sm-16">
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
          <h4 class="text-h4 mb-1">Verify your email</h4>
          <p class="mb-4 text-body-1">
            Account activation link sent to your email address
            <strong>{{ userEmail }}</strong
            >, Please follow the link inside to continue.
          </p>

          <VBtn block color="primary" @click="skipVerification" class="mb-4">
            Skip For Now
          </VBtn>

          <div class="text-center">
            <span class="text-body-2">Didn't get the email?</span>
            <VBtn
              variant="text"
              color="primary"
              size="small"
              @click="resendEmail"
              :loading="isResending"
              class="ms-1"
            >
              Resend
            </VBtn>
          </div>
        </VCardText>
      </VCard>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";

console.log("Verify-email component is loading...");

definePage({
  meta: {
    layout: "blank",
    public: true,
  },
});

const route = useRoute();
const router = useRouter();
const isResending = ref(false);

// Get email from route params or localStorage
const userEmail = ref(
  route.query.email || localStorage.getItem("registeredEmail") || "your email"
);

console.log("User email:", userEmail.value);

const skipVerification = async () => {
  // Try auto-login if credentials exist
  const email = localStorage.getItem("registerEmail");
  const password = localStorage.getItem("registerPassword");
  if (email && password) {
    try {
      const res = await fetch(`${import.meta.env.VITE_API_BASE_URL}/login`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify({ email, password }),
      });
      const data = await res.json();
      if (res.ok && data.token) {
        localStorage.setItem("accessToken", data.token);
        localStorage.setItem("userData", JSON.stringify(data.user));
        localStorage.removeItem("registerEmail");
        localStorage.removeItem("registerPassword");
        localStorage.removeItem("registeredEmail");

        // Always redirect to timeline after email verification
        router.push(
          `/registration/timeline/${
            data.user.role === "user" ? "individual" : data.user.role
          }/${data.user.id}`
        );
        return;
      }
    } catch (e) {
      // fall through to login
    }
  }
  // fallback: just clear registeredEmail and go to login
  localStorage.removeItem("registeredEmail");
  router.push({
    name: "login",
    query: { registered: "true", email: userEmail.value },
  });
};

const resendEmail = async () => {
  isResending.value = true;

  try {
    // Call resend verification email API
    const response = await fetch(
      `${import.meta.env.VITE_API_BASE_URL}/email/resend-verification`,
      {
        method: "POST",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({
          email: userEmail.value,
        }),
      }
    );

    if (response.ok) {
      // Show success message (you can add a toast notification here)
      console.log("Verification email resent successfully");
    } else {
      console.error("Failed to resend verification email");
    }
  } catch (error) {
    console.error("Error resending verification email:", error);
  } finally {
    isResending.value = false;
  }
};
</script>

<style lang="scss">
@use "@core-scss/template/pages/page-auth";
</style>
