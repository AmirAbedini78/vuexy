<script setup>
import authV1BottomShape from '@images/svg/auth-v1-bottom-shape.svg?raw'
import authV1TopShape from '@images/svg/auth-v1-top-shape.svg?raw'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const otp = ref('')
const isOtpInserted = ref(false)
const message = ref('')
const status = ref('')
const errors = ref({})

const onFinish = async () => {
  isOtpInserted.value = true
  message.value = ''
  status.value = ''
  errors.value = {}
  try {
    const response = await $api('/auth/verify-2fa', {
      method: 'POST',
      body: { one_time_password: otp.value },
      headers: { Authorization: `Bearer ${useCookie('accessToken').value}` },
    })
    status.value = 'success'
    message.value = response.message
    useCookie('accessToken').value = response.access_token
    setTimeout(() => router.push('/'), 2000)
  } catch (err) {
    status.value = 'error'
    errors.value = err.data?.errors || { one_time_password: ['Invalid code'] }
    message.value = err.data?.message || 'Failed to verify code'
  } finally {
    isOtpInserted.value = false
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
      <!-- 👉 Auth card -->
      <VCard
        class="auth-card"
        max-width="460"
        :class="$vuetify.display.smAndUp ? 'pa-6' : 'pa-0'"
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
            Two Step Verification 💬
          </h4>
          <p class="mb-1 form-header">
            Enter the 6-digit code from your authenticator app below.
          </p>
          <VAlert v-if="message" :type="status" variant="tonal" class="mb-4">
            <VAlertTitle>{{ status === 'success' ? 'Success' : 'Error' }}</VAlertTitle>
            {{ message }}
          </VAlert>
        </VCardText>
        <VCardText>
          <VForm @submit.prevent="onFinish">
            <VRow>
              <VCol cols="12">
                <h6 class="text-body-1">
                  Type your 6 digit security code
                </h6>
                <VOtpInput
                  v-model="otp"
                  :disabled="isOtpInserted"
                  type="number"
                  class="pa-0"
                  @finish="onFinish"
                  :error-messages="errors.one_time_password"
                />
              </VCol>
              <VCol cols="12">
                <VBtn
                  :loading="isOtpInserted"
                  :disabled="isOtpInserted"
                  block
                  type="submit"
                >
                  Verify my account
                </VBtn>
              </VCol>
              <VCol cols="12">
                <div class="d-flex justify-center align-center flex-wrap">
                  <span class="me-1">Didn't get the code?</span>
                  <a href="#">Resend</a>
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

.v-otp-input {
  .v-otp-input__content {
    padding-inline: 0;
  }
}
</style> 
