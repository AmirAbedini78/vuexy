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

// Provider status composable
const {
  providerStatus,
  shouldShowFullSidebar,
  shouldShowLimitedSidebar,
  fetchProviderStatus,
  initializeFromStorage,
} = useProviderStatus();

// Get user data to check if admin
const userDataCookie = useCookie("userData");

// Initialize provider status on mount
onMounted(async () => {
  initializeFromStorage();
  await fetchProviderStatus();

  // Listen for provider status changes from admin dashboard
  window.addEventListener("providerStatusChanged", async (event) => {
    console.log("Provider status changed event received:", event.detail);
    await fetchProviderStatus();
  });
});

// Cleanup event listener on unmount
onUnmounted(() => {
  window.removeEventListener("providerStatusChanged", () => {});
});

// Navigation logic based on provider status
const navigationItems = computed(() => {
  if (typeof window !== "undefined") {
    const userData = userDataCookie.value;

    // Admin users always see admin navigation
    if (userData?.role === "admin") return adminNavItems;

    // For regular users, check provider status
    if (shouldShowFullSidebar.value) {
      // Provider status is 'active' - show all menu items
      return userNavItems;
    } else if (shouldShowLimitedSidebar.value) {
      // Provider status is 'approved' or 'rejected' - show only Welcome
      const welcome = userNavItems.find((i) => i.title === "Welcome");
      return welcome ? [welcome] : [];
    } else {
      // No provider profile or status is 'not_found' - show only Welcome
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
