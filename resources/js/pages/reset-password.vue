<script setup>
import authV1BottomShape from '@images/svg/auth-v1-bottom-shape.svg?raw'
import authV1TopShape from '@images/svg/auth-v1-top-shape.svg?raw'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const form = ref({
  email: route.query.email || '',
  password: '',
  password_confirmation: '',
  token: route.params.token || '',
})

const isPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)
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
    const response = await $api('/auth/reset-password', {
      method: 'POST',
      body: form.value,
    })
    status.value = 'success'
    message.value = response.message
    setTimeout(() => router.push({ name: 'login' }), 2000)
  } catch (err) {
    status.value = 'error'
    errors.value = err.data?.errors || { email: ['An error occurred'] }
    message.value = err.data?.message || 'Failed to reset password'
  } finally {
    isLoading.value = false
  }
}

definePage({
  meta: {
    layout: 'blank',
    public: true,
  },
})
</script>

<template>
  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <div class="position-relative my-sm-16">
      <!-- 👉 Top shape -->
      <VNodeRenderer
        :nodes="h('div', { innerHTML: authV1TopShape })"
        class="text-primary auth-v1-top-shape d-none d-sm-block"
      />
      <!-- 👉 Bottom shape -->
      <VNodeRenderer
        :nodes="h('div', { innerHTML: authV1BottomShape })"
        class="text-primary auth-v1-bottom-shape d-none d-sm-block"
      />
      <!-- 👉 Auth Card -->
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
          <h4 class="text-h4 mb-1">
            Reset Password 🔒
          </h4>
          <p class="mb-0 form-header">
            Your new password must be different from previously used passwords
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
                  v-model="form.email"
                  label="Email"
                  type="email"
                  placeholder="Enter your email"
                  :error-messages="errors.email"
                  :disabled="isLoading"
                />
              </VCol>
              <!-- password -->
              <VCol cols="12">
                <AppTextField
                  v-model="form.password"
                  autofocus
                  label="New Password"
                  placeholder="············"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  autocomplete="new-password"
                  :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
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
                  :append-inner-icon="isConfirmPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
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
                  {{ isLoading ? 'Resetting...' : 'Set New Password' }}
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
@use "@core-scss/template/pages/page-auth";
</style> 
