<script setup>
import adminNavItems from "@/navigation/vertical/admin";
import userNavItems from "@/navigation/vertical/user";

// Components
import Footer from "@/layouts/components/Footer.vue";
import NavBarNotifications from "@/layouts/components/NavBarNotifications.vue";
import NavbarThemeSwitcher from "@/layouts/components/NavbarThemeSwitcher.vue";
import UserProfile from "@/layouts/components/UserProfile.vue";

// @layouts plugin
import { VerticalNavLayout } from "@layouts";

// Navigation composable
const { goToDashboard } = useNavigation();

// Get user data to check if admin
const userDataCookie = useCookie("userData");

// Simple navigation logic (gated by verification)
const navigationItems = computed(() => {
  if (typeof window !== "undefined") {
    const userData = userDataCookie.value;
    if (userData?.role === "admin") return adminNavItems;

    // Read verification flag set by Timeline
    const verifiedCookie = useCookie("userVerified");
    let isVerified = verifiedCookie.value === "true";
    if (!isVerified) {
      try {
        isVerified = localStorage.getItem("userVerified") === "true";
      } catch (e) {}
    }

    if (!isVerified) {
      const welcome = userNavItems.find((i) => i.title === "Welcome");
      return welcome ? [welcome] : [];
    }
  }
  return userNavItems;
});
</script>

<template>
  <VerticalNavLayout :nav-items="navigationItems">
    <!-- 👉 navbar -->
    <template #navbar="{ toggleVerticalOverlayNavActive }">
      <div class="d-flex h-100 align-center">
        <!-- Toggle button for mobile only (to open) -->
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
