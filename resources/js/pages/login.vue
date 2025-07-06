<template>
  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <div class="position-relative my-sm-16">
      <!-- 👉 Auth Card -->
      <VCard
        class="auth-card"
        max-width="400"
        :class="$vuetify.display.smAndUp ? 'pa-6' : 'pa-0'"
      >
        <VCardItem class="justify-center">
          <VCardTitle>
            <RouterLink to="/">
              <div class="app-logo">
                <VNodeRenderer :nodes="themeConfig.app.logo" />
              </div>
            </RouterLink>
          </VCardTitle>
        </VCardItem>

        <VCardText>
          <h4 class="text-h4 mb-1 text-center">
            Unlock Your Limitless Potential!
          </h4>
          <p class="mb-0 text-center form-header">
            Log in to your account and start the adventure
          </p>
        </VCardText>

        <!-- Success message for newly registered users -->
        <VCardText v-if="isNewlyRegistered">
          <VAlert type="success" variant="tonal" class="mb-4">
            <template #prepend>
              <VIcon icon="tabler-check-circle" />
            </template>
            <VAlertTitle>Registration Successful!</VAlertTitle>
            Your account has been created successfully. Please log in with your
            credentials.
          </VAlert>
        </VCardText>

        <!-- Success message for email verification -->
        <VCardText v-if="emailVerified">
          <VAlert type="success" variant="tonal" class="mb-4">
            <template #prepend>
              <VIcon icon="tabler-check-circle" />
            </template>
            <VAlertTitle>Email Verified!</VAlertTitle>
            Your email has been successfully verified. You can now log in.
          </VAlert>
        </VCardText>

        <VCardText>
          <VForm ref="refVForm" @submit.prevent="onSubmit">
            <VRow>
              <!-- email -->
              <VCol cols="12">
                <AppTextField
                  v-model="credentials.email"
                  label="Email"
                  placeholder="Enter your email"
                  type="email"
                  autofocus
                  :rules="[requiredValidator, emailValidator]"
                  :error-messages="errors.email"
                  :disabled="isLoading"
                />
              </VCol>

              <!-- password -->
              <VCol cols="12">
                <AppTextField
                  v-model="credentials.password"
                  label="Password"
                  placeholder="············"
                  :rules="[requiredValidator]"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  autocomplete="password"
                  :error-messages="errors.password"
                  :append-inner-icon="
                    isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'
                  "
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                  :disabled="isLoading"
                />

                <div
                  class="d-flex align-center flex-wrap justify-space-between my-6"
                >
                  <VCheckbox
                    v-model="rememberMe"
                    label="Remember me"
                    :disabled="isLoading"
                    style="font-size: 5px !important"
                  />
                  <RouterLink
                    class="text-primary ms-2 mb-1"
                    :to="{ name: 'forgot-password' }"
                  >
                    Forgot Password?
                  </RouterLink>
                </div>

                <VBtn
                  block
                  type="submit"
                  :loading="isLoading"
                  :disabled="isLoading"
                >
                  {{ isLoading ? "Logging in..." : "Login" }}
                </VBtn>
              </VCol>

              <!-- create account -->
              <VCol cols="12" class="text-center">
                <span>New on our platform?</span>
                <RouterLink
                  class="text-primary ms-1"
                  :to="{ name: 'register' }"
                >
                  Create an account
                </RouterLink>
              </VCol>
              <VCol cols="12" class="d-flex align-center">
                <VDivider />
                <span class="mx-4">or</span>
                <VDivider />
              </VCol>

              <!-- auth providers -->
              <VCol cols="12" class="text-center">
                <div class="d-flex justify-center flex-wrap gap-1">
                  <button
                    style="
                      background: none;
                      border: none;
                      padding: 0;
                      margin: 0 4px;
                      width: 32px;
                      height: 32px;
                      display: inline-flex;
                      align-items: center;
                      justify-content: center;
                    "
                  >
                    <img
                      src="/images/4svg/wired-outline-2557-logo-google-hover-pinch.png"
                      width="20"
                      height="20"
                      alt="Google"
                    />
                  </button>
                  <button
                    style="
                      background: none;
                      border: none;
                      padding: 0;
                      margin: 0 4px;
                      width: 32px;
                      height: 32px;
                      display: inline-flex;
                      align-items: center;
                      justify-content: center;
                    "
                  >
                    <img
                      src="/images/4svg/wired-outline-2714-logo-x-hover-pinch.png"
                      width="20"
                      height="20"
                      alt="X"
                    />
                  </button>
                  <button
                    style="
                      background: none;
                      border: none;
                      padding: 0;
                      margin: 0 4px;
                      width: 32px;
                      height: 32px;
                      display: inline-flex;
                      align-items: center;
                      justify-content: center;
                    "
                  >
                    <img
                      src="/images/4svg/wired-outline-2540-logo-facebook-hover-pinch.png"
                      width="20"
                      height="20"
                      alt="Facebook"
                    />
                  </button>
                  <button
                    style="
                      background: none;
                      border: none;
                      padding: 0;
                      margin: 0 4px;
                      width: 32px;
                      height: 32px;
                      display: inline-flex;
                      align-items: center;
                      justify-content: center;
                    "
                  >
                    <img
                      src="/images/4svg/wired-outline-2549-logo-linkedin-hover-pinch.png"
                      width="20"
                      height="20"
                      alt="LinkedIn"
                    />
                  </button>
                </div>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </div>
  </div>
</template>

<script setup>
import { useGenerateImageVariant } from "@core/composable/useGenerateImageVariant";
import authV2LoginIllustrationBorderedDark from "@images/pages/auth-v2-login-illustration-bordered-dark.png";
import authV2LoginIllustrationBorderedLight from "@images/pages/auth-v2-login-illustration-bordered-light.png";
import authV2LoginIllustrationDark from "@images/pages/auth-v2-login-illustration-dark.png";
import authV2LoginIllustrationLight from "@images/pages/auth-v2-login-illustration-light.png";
import authV2MaskDark from "@images/pages/misc-mask-dark.png";
import authV2MaskLight from "@images/pages/misc-mask-light.png";
import { VNodeRenderer } from "@layouts/components/VNodeRenderer";
import { themeConfig } from "@themeConfig";
import { computed, ref } from "vue";
import { useAbility, useRoute, useRouter } from "vue-router";
import { VForm } from "vuetify/components/VForm";

const authThemeImg = useGenerateImageVariant(
  authV2LoginIllustrationLight,
  authV2LoginIllustrationDark,
  authV2LoginIllustrationBorderedLight,
  authV2LoginIllustrationBorderedDark,
  true
);
const authThemeMask = useGenerateImageVariant(authV2MaskLight, authV2MaskDark);

definePage({
  meta: {
    layout: "blank",
    unauthenticatedOnly: true,
  },
});

const isPasswordVisible = ref(false);
const route = useRoute();
const router = useRouter();
const ability = useAbility();

// Check if user just registered
const isNewlyRegistered = computed(() => route.query.registered === "true");

// Check if email was just verified
const emailVerified = computed(() => route.query.verified === "true");

const registeredEmail = computed(() => route.query.email || "");

const errors = ref({
  email: undefined,
  password: undefined,
});

const refVForm = ref();

const credentials = ref({
  email: registeredEmail.value,
  password: "",
});

const rememberMe = ref(false);
const isLoading = ref(false);

const login = async () => {
  isLoading.value = true;
  errors.value = { email: undefined, password: undefined };

  try {
    console.log("Attempting login with:", credentials.value.email);

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
        body: JSON.stringify({
          email: credentials.value.email,
          password: credentials.value.password,
        }),
      }
    );

    const data = await response.json();
    console.log("Login response:", { status: response.status, data });

    if (!response.ok) {
      throw new Error(data.message || "Login failed");
    }

    const { access_token, user } = data;

    // Store user data and token with proper cookie options
    const userDataCookie = useCookie("userData", {
      maxAge: 60 * 60 * 24 * 7, // 7 days
      secure: import.meta.env.PROD, // Secure in production
      sameSite: "lax",
    });
    const accessTokenCookie = useCookie("accessToken", {
      maxAge: 60 * 60 * 24 * 7, // 7 days
      secure: import.meta.env.PROD, // Secure in production
      sameSite: "lax",
    });

    userDataCookie.value = user;
    accessTokenCookie.value = access_token;

    console.log("Cookies set:", {
      userData: userDataCookie.value,
      accessToken: accessTokenCookie.value,
    });

    // Set default ability rules
    const userAbilityRules = [
      {
        action: "read",
        subject: "all",
      },
    ];

    const userAbilityRulesCookie = useCookie("userAbilityRules", {
      maxAge: 60 * 60 * 24 * 7, // 7 days
      secure: import.meta.env.PROD, // Secure in production
      sameSite: "lax",
    });

    userAbilityRulesCookie.value = userAbilityRules;
    ability.update(userAbilityRules);

    console.log("About to redirect to dashboard...");
    await nextTick();
    router.push("/");
  } catch (err) {
    console.error("Login error:", {
      message: err.message,
      status: err.status,
      data: err.data,
    });
    if (err.data && err.data.errors) {
      errors.value = err.data.errors;
    } else if (err.message) {
      errors.value = { email: [err.message] };
    } else {
      errors.value = { email: ["An error occurred during login"] };
    }
  } finally {
    isLoading.value = false;
  }
};

const onSubmit = () => {
  refVForm.value?.validate().then(({ valid: isValid }) => {
    if (isValid) login();
  });
};
</script>

<style lang="scss">
@use "@core-scss/template/pages/page-auth";
</style>
