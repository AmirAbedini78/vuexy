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
            <h4 class="text-h4 mb-1">Verifying your email...</h4>
            <VAlert type="info" variant="tonal" class="mb-4">
              <VAlertTitle>Verifying...</VAlertTitle>
              Please wait while we verify your email address.
            </VAlert>
          </template>
          <template v-else-if="status === 'success'">
            <h4 class="text-h4 mb-1">Email Verified ✉️</h4>
            <VAlert type="success" variant="tonal" class="mb-4">
              <VAlertTitle>Email Verified!</VAlertTitle>
              Your email has been successfully verified. You can now <RouterLink :to="{ name: 'login' }">log in</RouterLink>.
            </VAlert>
          </template>
          <template v-else>
            <h4 class="text-h4 mb-1">Verification Failed</h4>
            <VAlert type="error" variant="tonal" class="mb-4">
              <VAlertTitle>Verification Failed</VAlertTitle>
              {{ errorMessage }}
            </VAlert>
            <div class="d-flex align-center justify-center">
              <span class="me-1">Didn't get the mail?</span>
              <RouterLink :to="{ name: 'register' }">Resend</RouterLink>
            </div>
          </template>
        </VCardText>
      </VCard>
    </div>
  </div>
</template>

<script setup>
import authV1BottomShape from '@images/svg/auth-v1-bottom-shape.svg?raw'
import authV1TopShape from '@images/svg/auth-v1-top-shape.svg?raw'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const status = ref('verifying') // verifying, success, error
const errorMessage = ref('')

onMounted(async () => {
  const { token } = route.params
  let url = `/api/verify/${token}`
  try {
    await $fetch(url, { method: 'GET' })
    status.value = 'success'
    setTimeout(() => router.push({ name: 'login', query: { verified: 'true' } }), 2000)
  } catch (err) {
    status.value = 'error'
    errorMessage.value = err?.data?.message || 'Verification link is invalid or expired.'
  }
})

definePage({
  meta: {
    layout: 'blank',
    public: true,
  },
})
</script>

<style lang="scss">
@use "@core-scss/template/pages/page-auth";
</style> 
