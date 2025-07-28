<script setup>
import navItems from "@/navigation/horizontal";

// Components
import Footer from "@/layouts/components/Footer.vue";
import NavBarNotifications from "@/layouts/components/NavBarNotifications.vue";
import NavbarThemeSwitcher from "@/layouts/components/NavbarThemeSwitcher.vue";
import UserProfile from "@/layouts/components/UserProfile.vue";
import { HorizontalNavLayout } from "@layouts";

// Navigation composable
const { goToDashboard } = useNavigation();
</script>

<template>
  <HorizontalNavLayout :nav-items="navItems">
    <!-- 👉 navbar -->
    <template #navbar>
      <!-- App Logo - Removed as requested -->
      <!-- <RouterLink to="/" class="app-logo d-flex align-center gap-x-3">
        <VNodeRenderer :nodes="themeConfig.app.logo" />
      </RouterLink> -->
      <VSpacer />

      <!-- Theme Switcher (Light/Dark mode) -->
      <NavbarThemeSwitcher />

      <!-- Home icon for navigation to dashboard -->
      <IconBtn class="me-2" @click="goToDashboard" title="Go to Dashboard">
        <VIcon size="24" icon="tabler-home" />
      </IconBtn>

      <!-- Notifications -->
      <NavBarNotifications class="me-2" />

      <!-- User Profile -->
      <UserProfile />
    </template>

    <!-- 👉 Pages -->
    <slot />

    <!-- 👉 Footer -->
    <template #footer>
      <Footer />
    </template>

    <!-- 👉 Customizer - Removed as requested -->
    <!-- <TheCustomizer /> -->
  </HorizontalNavLayout>
</template>
