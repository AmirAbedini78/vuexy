<script setup>
import { useGenerateImageVariant } from '@core/composable/useGenerateImageVariant'
import authV2ForgotPasswordIllustrationDark from '@images/pages/auth-v2-forgot-password-illustration-dark.png'
import authV2ForgotPasswordIllustrationLight from '@images/pages/auth-v2-forgot-password-illustration-light.png'
import authV2MaskDark from '@images/pages/misc-mask-dark.png'
import authV2MaskLight from '@images/pages/misc-mask-light.png'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'

const email = ref('')
const authThemeImg = useGenerateImageVariant(authV2ForgotPasswordIllustrationLight, authV2ForgotPasswordIllustrationDark)
const authThemeMask = useGenerateImageVariant(authV2MaskLight, authV2MaskDark)
const isLoading = ref(false)
const message = ref('')
const status = ref('')
const errors = ref({})

const onSubmit = async () => {
  isLoading.value = true
  message.value = ''
  status.value = ''
  errors.value = {}
  try {
    const response = await $api('/auth/forgot-password', {
      method: 'POST',
      body: { email: email.value },
    })
    status.value = 'success'
    message.value = response.message
  } catch (err) {
    status.value = 'error'
    errors.value = err.data?.errors || { email: ['An error occurred'] }
    message.value = err.data?.message || 'Failed to send reset link'
  } finally {
    isLoading.value = false
  }
}

definePage({
  meta: {
    layout: 'blank',
    unauthenticatedOnly: true,
  },
})
</script>

<template>


  






        




  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <div class="position-relative my-sm-16">

      

      <!-- 👉 Auth card -->
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
                <!-- <h1 class="app-logo-title">
                  {{ themeConfig.app.title }}
                </h1> -->
              </div>
            </RouterLink>
          </VCardTitle>
        </VCardItem>

        <VCardText>
          <h4 class="text-h4 mb-1 text-center">
            Forgot Your Passwoard?
          </h4>
          <p class="mb-0 text-center form-header">
            Enter your email and we'll send you instructions to
            reset your password       
          </p>
          <VAlert v-if="message" :type="status" variant="tonal" class="mb-4">
            <VAlertTitle>{{ status === 'success' ? 'Success' : 'Error' }}</VAlertTitle>
            {{ message }}
          </VAlert>
        </VCardText>

        <VCardText>
          <VForm @submit.prevent="onSubmit">
            <VRow>
              <!-- email -->
              <VCol cols="12">
                <AppTextField
                  v-model="email"
                  autofocus
                  label="Email"
                  type="email"
                  placeholder="Enter your email"
                  :error-messages="errors.email"
                  :disabled="isLoading"
                />
              </VCol>

              <!-- Reset link -->
              <VCol cols="12">
                <VBtn
                  block
                  type="submit"
                  :loading="isLoading"
                  :disabled="isLoading"
                >
                  {{ isLoading ? 'Sending...' : 'Send Reset Link' }}
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
