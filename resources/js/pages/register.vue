<script setup>
import { useGenerateImageVariant } from '@core/composable/useGenerateImageVariant'
import authV2RegisterIllustrationBorderedDark from '@images/pages/auth-v2-register-illustration-bordered-dark.png'
import authV2RegisterIllustrationBorderedLight from '@images/pages/auth-v2-register-illustration-bordered-light.png'
import authV2RegisterIllustrationDark from '@images/pages/auth-v2-register-illustration-dark.png'
import authV2RegisterIllustrationLight from '@images/pages/auth-v2-register-illustration-light.png'
import authV2MaskDark from '@images/pages/misc-mask-dark.png'
import authV2MaskLight from '@images/pages/misc-mask-light.png'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'
import { VForm } from 'vuetify/components/VForm'

const authThemeImg = useGenerateImageVariant(authV2RegisterIllustrationLight, authV2RegisterIllustrationDark, authV2RegisterIllustrationBorderedLight, authV2RegisterIllustrationBorderedDark, true)
const authThemeMask = useGenerateImageVariant(authV2MaskLight, authV2MaskDark)

definePage({
  meta: {
    layout: 'blank',
    unauthenticatedOnly: true,
  },
})

const isPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)
const route = useRoute()
const router = useRouter()
const ability = useAbility()

const errors = ref({
  name: undefined,
  email: undefined,
  password: undefined,
  password_confirmation: undefined,
})

const refVForm = ref()

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  privacyPolicies: false,
})

const isLoading = ref(false)
const registrationDone = ref(false)

// Computed properties for password validation
const passwordRules = computed(() => [
  requiredValidator,
  (value) => value.length >= 8 || 'Password must be at least 8 characters'
])

const confirmPasswordRules = computed(() => [
  requiredValidator,
  (value) => value === form.value.password || 'Passwords do not match'
])

const register = async () => {
  isLoading.value = true
  errors.value = { name: undefined, email: undefined, password: undefined, password_confirmation: undefined }
  
  try {
    const res = await $api('/auth/register', {
      method: 'POST',
      body: {
        name: form.value.name,
        email: form.value.email,
        password: form.value.password,
        password_confirmation: form.value.password_confirmation,
      },
    })

    const { access_token, user } = res

    // Don't store user data and token after registration
    // User should login separately
    
    registrationDone.value = true
    
  } catch (err) {
    console.error('Registration error:', err)
    if (err.data && err.data.errors) {
      errors.value = err.data.errors
    } else {
      errors.value = { email: ['An error occurred during registration'] }
    }
  } finally {
    isLoading.value = false
  }
}

const onSubmit = () => {
  refVForm.value?.validate().then(({ valid: isValid }) => {
    if (isValid && form.value.privacyPolicies)
      register()
  })
}
</script>

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
          Welcom to Explorer Elite!
          </h4>
          <p class="mb-0 text-center form-header">
            Sing up in your Adventure management Platform!
          </p>
        </VCardText>
        
        <VCardText>
          <!-- Alert for successful registration -->
          <VAlert
            v-if="registrationDone"
            type="success"
            variant="tonal"
            class="mb-4"
          >
            <VAlertTitle>Registration Successful!</VAlertTitle>
            We have sent an email to <strong>{{ form.email }}</strong> with a link to verify your account.
            Please check your inbox (and spam folder).
          </VAlert>

          <VForm
            v-if="!registrationDone"
            ref="refVForm"
            @submit.prevent="onSubmit"
          >
            <VRow>
              <!-- Username -->
              <VCol cols="12">
                <AppTextField
                  v-model="form.name"
                  autofocus
                  label="Full Name"
                  placeholder="John Doe"
                  :rules="[requiredValidator]"
                  :error-messages="errors.name"
                  :disabled="isLoading"
                />
              </VCol>

              <!-- email -->
              <VCol cols="12">
                <AppTextField
                  v-model="form.email"
                  label="Email"
                  type="email"
                  placeholder="johndoe@email.com"
                  :rules="[requiredValidator, emailValidator]"
                  :error-messages="errors.email"
                  :disabled="isLoading"
                />
              </VCol>

              <!-- password -->
              <VCol cols="12">
                <AppTextField
                  v-model="form.password"
                  label="Password"
                  placeholder="············"
                  :rules="passwordRules"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  autocomplete="new-password"
                  :error-messages="errors.password"
                  :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                  :disabled="isLoading"
                />
              </VCol>

              <!-- confirm password -->
              <VCol cols="12">
                <AppTextField
                  v-model="form.password_confirmation"
                  label="Confirm Password"
                  placeholder="············"
                  :rules="confirmPasswordRules"
                  :type="isConfirmPasswordVisible ? 'text' : 'password'"
                  autocomplete="new-password"
                  :error-messages="errors.password_confirmation"
                  :append-inner-icon="isConfirmPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
                  :disabled="isLoading"
                />
              </VCol>

              <!-- privacy policies -->
              <VCol cols="12">
                <VCheckbox
                  v-model="form.privacyPolicies"
                  label="I agree to the privacy policy & terms"
                  :rules="[v => !!v || 'You must agree to continue!']"
                  :disabled="isLoading"
                />
              </VCol>

              <VCol cols="12">
                <VBtn
                  block
                  type="submit"
                  :loading="isLoading"
                  :disabled="isLoading || !form.privacyPolicies"
                >
                  {{ isLoading ? 'Creating account...' : 'Sign up' }}
                </VBtn>
              </VCol>

              <!-- login instead -->
              <VCol
                cols="12"
                class="text-center"
              >
                <span>Already have an account?</span>
                <RouterLink
                  class="text-primary ms-1"
                  :to="{ name: 'login' }"
                >
                  Sign in instead
                </RouterLink>
              </VCol>
              <VCol
                cols="12"
                class="d-flex align-center"
              >
                <VDivider />
                <span class="mx-4">or</span>
                <VDivider />
              </VCol>

              <!-- auth providers -->
              <VCol cols="12" class="text-center">
  <div class="d-flex justify-center flex-wrap gap-1">
    <button style="background: none; border: none; padding: 0; margin: 0 4px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
      <img src="images/svg/4svg/wired-outline-2557-logo-google-hover-pinch.png" width="20" height="20" alt="Google" />
    </button>
    <button style="background: none; border: none; padding: 0; margin: 0 4px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
      <img src="images/svg/4svg/wired-outline-2714-logo-x-hover-pinch.png" width="20" height="20" alt="X" />
    </button>
    <button style="background: none; border: none; padding: 0; margin: 0 4px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
      <img src="images/svg/4svg/wired-outline-2540-logo-facebook-hover-pinch.png" width="20" height="20" alt="Facebook" />
    </button>
    <button style="background: none; border: none; padding: 0; margin: 0 4px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
      <img src="images/svg/4svg/wired-outline-2549-logo-linkedin-hover-pinch.png" width="20" height="20" alt="LinkedIn" />
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

<style lang="scss">
@use "@core-scss/template/pages/page-auth";
</style>
