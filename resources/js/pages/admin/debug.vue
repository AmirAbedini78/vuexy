<template>
  <VCard>
    <VCardTitle>Debug Page - Admin Navigation</VCardTitle>
    <VCardText>
      <div class="mb-4">
        <h3>Current Route:</h3>
        <p><strong>Path:</strong> {{ $route.path }}</p>
        <p><strong>Name:</strong> {{ $route.name }}</p>
      </div>

      <div class="mb-4">
        <h3>User Data:</h3>
        <pre>{{ JSON.stringify(userData, null, 2) }}</pre>
      </div>

      <div class="mb-4">
        <h3>Navigation Test:</h3>
        <VBtn color="primary" @click="testNavigation" class="mr-2">
          Test Navigation
        </VBtn>
        <VBtn color="success" @click="goToAdminDashboard" class="mr-2">
          Go to Admin Dashboard
        </VBtn>
        <VBtn color="warning" @click="goToSimpleDashboard" class="mr-2">
          Go to Simple Dashboard
        </VBtn>
        <VBtn color="error" @click="goToHome" class="mr-2"> Go to Home </VBtn>
      </div>

      <div class="mb-4">
        <h3>Console Logs:</h3>
        <div v-for="(log, index) in logs" :key="index" class="log-item">
          <code>{{ log }}</code>
        </div>
      </div>
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

const route = useRoute();
const router = useRouter();
const userDataCookie = useCookie("userData");
const userData = ref(null);
const logs = ref([]);

const addLog = (message) => {
  logs.value.push(`${new Date().toLocaleTimeString()}: ${message}`);
  console.log(message);
};

const testNavigation = () => {
  addLog("Testing navigation...");
  const { goToDashboard } = useNavigation();
  goToDashboard();
};

const goToAdminDashboard = () => {
  addLog("Manually navigating to /admin/dashboard");
  router.push("/admin/dashboard");
};

const goToSimpleDashboard = () => {
  addLog("Manually navigating to /admin/simple-dashboard");
  router.push("/admin/simple-dashboard");
};

const goToHome = () => {
  addLog("Manually navigating to /");
  router.push("/");
};

onMounted(() => {
  userData.value = userDataCookie.value;
  addLog("Debug page mounted");
  addLog(`Current route: ${route.path}`);
  addLog(`User data: ${JSON.stringify(userData.value)}`);
});
</script>

<style scoped>
.log-item {
  background: #f5f5f5;
  padding: 8px;
  margin: 4px 0;
  border-radius: 4px;
  font-family: monospace;
  font-size: 12px;
}
</style>
