<template>
  <VCard>
    <VCardTitle>Admin Navigation Test</VCardTitle>
    <VCardText>
      <div class="mb-4">
        <h3>User Information:</h3>
        <pre>{{ JSON.stringify(userData, null, 2) }}</pre>
      </div>

      <div class="mb-4">
        <h3>Is Admin: {{ isAdmin }}</h3>
      </div>

      <div class="mb-4">
        <h3>Navigation Items Count: {{ navigationItemsCount }}</h3>
      </div>

      <div class="mb-4">
        <h3>Navigation Items:</h3>
        <pre>{{ JSON.stringify(navigationItems, null, 2) }}</pre>
      </div>

      <VBtn color="primary" @click="refreshData"> Refresh Data </VBtn>

      <VBtn color="success" class="ml-2" @click="forceAdmin">
        Force Admin Mode
      </VBtn>
    </VCardText>
  </VCard>
</template>

<script setup>
definePage({
  meta: {
    layout: "default",
    requiresAuth: true,
    requiresAdmin: true,
  },
});

const userDataCookie = useCookie("userData");
const userData = ref(null);
const isAdmin = ref(false);
const navigationItems = ref([]);
const navigationItemsCount = ref(0);

const refreshData = () => {
  userData.value = userDataCookie.value;
  isAdmin.value = userData.value?.role === "admin";

  // Get navigation items from the layout
  const { navigationItems: navItems } = useAdminNavigation();
  navigationItems.value = navItems.value;
  navigationItemsCount.value = navItems.value?.length || 0;

  console.log("Test page - refreshed data:", {
    userData: userData.value,
    isAdmin: isAdmin.value,
    navigationItems: navigationItems.value,
    count: navigationItemsCount.value,
  });
};

const forceAdmin = () => {
  // Force set admin role for testing
  userDataCookie.value = {
    ...userDataCookie.value,
    role: "admin",
  };
  refreshData();
};

onMounted(() => {
  refreshData();
});
</script>
