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

// Composables
import { useProviderStatus } from "@/composables/useProviderStatus";

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

  // Start polling until status becomes active
  if (typeof window !== "undefined") {
    if (!window.__providerStatusPoll) {
      window.__providerStatusPoll = setInterval(async () => {
        try {
          await fetchProviderStatus();
          if (shouldShowFullSidebar.value && window.__providerStatusPoll) {
            clearInterval(window.__providerStatusPoll);
            window.__providerStatusPoll = null;
            console.log("Provider status is active - polling stopped");
          }
        } catch (e) {
          // ignore
        }
      }, 10000);
      console.log("Started provider status polling");
    }

    // Refresh on window focus
    const handleFocus = async () => {
      try {
        await fetchProviderStatus();
      } catch (e) {}
    };
    window.addEventListener("focus", handleFocus);
    // Save handler to remove later
    window.__providerStatusFocusHandler = handleFocus;
  }
});

// Cleanup event listener on unmount
onUnmounted(() => {
  window.removeEventListener("providerStatusChanged", () => {});
  if (typeof window !== "undefined") {
    if (window.__providerStatusPoll) {
      clearInterval(window.__providerStatusPoll);
      window.__providerStatusPoll = null;
    }
    if (window.__providerStatusFocusHandler) {
      window.removeEventListener("focus", window.__providerStatusFocusHandler);
      window.__providerStatusFocusHandler = null;
    }
  }
});

// Helper to return only Welcome item
const getWelcomeOnly = () => {
  const welcome = userNavItems.find((i) => i.title === "Welcome");
  return welcome ? [welcome] : [];
};

// Track nav rerenders when status changes
const navVersion = ref(0);
watch(providerStatus, () => {
  navVersion.value++;
  console.log(
    "Sidebar nav rerender due to providerStatus:",
    providerStatus.value,
    "version:",
    navVersion.value
  );
  // Show rejection notice when status is rejected
  if (providerStatus.value === "rejected") {
    rejectionNotice.value = true;
  }
});

// Rejection popup state (non-intrusive)
const rejectionNotice = ref(false);

// Navigation logic based on provider status
const navigationItems = computed(() => {
  if (typeof window !== "undefined") {
    const userData = userDataCookie.value;

    // Admin users always see admin navigation
    if (userData?.role === "admin") return adminNavItems;

    // While loading or unknown provider status, show only Welcome
    if (!providerStatus.value) return getWelcomeOnly();

    // For regular users, check provider status
    if (shouldShowFullSidebar.value) {
      // Provider status is 'active' - show all menu items
      return userNavItems;
    } else if (shouldShowLimitedSidebar.value) {
      // Provider status is 'approved' or 'rejected' - show only Welcome
      return getWelcomeOnly();
    } else {
      // No provider profile or status is 'not_found' - show only Welcome
      return getWelcomeOnly();
    }
  }
  // SSR or safety fallback
  return getWelcomeOnly();
});
</script>

<template>
  <VerticalNavLayout :key="navVersion" :nav-items="navigationItems">
    <!-- Rejection toast (shows on each refresh/login if status is rejected) -->
    <VSnackbar
      v-model="rejectionNotice"
      color="error"
      :timeout="5000"
      location="top right"
      elevation="2"
    >
      <div class="d-flex align-center" style="gap: 8px">
        <VIcon icon="tabler-alert-triangle" size="20" />
        Your provider profile was rejected. Please contact admin support.
      </div>
    </VSnackbar>

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
