<template>
  <VCard>
    <VCardTitle>Simple Admin Dashboard</VCardTitle>
    <VCardText>
      <h2>Welcome to Admin Dashboard!</h2>
      <p>This is a simple test page to verify routing works correctly.</p>

      <div class="mt-4">
        <h3>User Information:</h3>
        <pre>{{ JSON.stringify(userData, null, 2) }}</pre>
      </div>

      <div class="mt-4">
        <VBtn color="primary" @click="goToComplexDashboard">
          Go to Complex Dashboard
        </VBtn>
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

const router = useRouter();
const userDataCookie = useCookie("userData");
const userData = ref(null);

const goToComplexDashboard = () => {
  router.push("/admin/dashboard");
};

onMounted(() => {
  userData.value = userDataCookie.value;
  console.log("Simple dashboard mounted");
});
</script>
