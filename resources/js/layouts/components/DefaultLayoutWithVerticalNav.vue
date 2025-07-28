<script setup>
import navItems from "@/navigation/vertical";

// Components
import Footer from "@/layouts/components/Footer.vue";
import NavBarNotifications from "@/layouts/components/NavBarNotifications.vue";
import NavbarThemeSwitcher from "@/layouts/components/NavbarThemeSwitcher.vue";
import UserProfile from "@/layouts/components/UserProfile.vue";

// @layouts plugin
import { VerticalNavLayout } from "@layouts";

// Navigation composable
const { goToDashboard } = useNavigation();
</script>

<template>
  <VerticalNavLayout :nav-items="navItems">
    <!-- 👉 navbar -->
    <template #navbar="{ toggleVerticalOverlayNavActive }">
      <div class="d-flex h-100 align-center">
        <IconBtn
          id="vertical-nav-toggle-btn"
          class="ms-n3 d-lg-none"
          @click="toggleVerticalOverlayNavActive(true)"
        >
          <VIcon size="26" icon="tabler-menu-2" />
        </IconBtn>

        <!-- App Logo and Title - Removed as requested -->
        <!-- <RouterLink to="/" class="app-logo d-flex align-center gap-x-3 me-4">
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
        <NavBarNotifications class="me-1" />

        <!-- User Profile -->
        <UserProfile />
      </div>
    </template>

    <!-- 👉 Pages -->
    <slot />

    <!-- 👉 Footer -->
    <template #footer>
      <Footer />
    </template>

    <!-- 👉 Customizer - Removed as requested -->
    <!-- <TheCustomizer /> -->
  </VerticalNavLayout>
</template>
