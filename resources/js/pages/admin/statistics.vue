<template>
  <VCard>
    <VCardTitle class="d-flex align-center justify-space-between">
      <span>System Statistics</span>
      <VBtn
        color="primary"
        prepend-icon="tabler-refresh"
        @click="loadStatistics"
        :loading="loading"
      >
        Refresh
      </VBtn>
    </VCardTitle>

    <VCardText>
      <VRow>
        <!-- Statistics Cards -->
        <VCol cols="12" md="6">
          <VCard>
            <VCardTitle>User Registration Trends</VCardTitle>
            <VCardText>
              <div v-if="stats.users_by_month?.length">
                <div
                  v-for="item in stats.users_by_month"
                  :key="item.month"
                  class="d-flex align-center justify-space-between py-2"
                >
                  <span class="text-body-2">{{
                    getMonthName(item.month)
                  }}</span>
                  <VChip color="primary" size="small"
                    >{{ item.count }} users</VChip
                  >
                </div>
              </div>
              <div v-else class="text-center py-4">
                <p class="text-medium-emphasis">No data available</p>
              </div>
            </VCardText>
          </VCard>
        </VCol>

        <VCol cols="12" md="6">
          <VCard>
            <VCardTitle>Listing Creation Trends</VCardTitle>
            <VCardText>
              <div v-if="stats.listings_by_month?.length">
                <div
                  v-for="item in stats.listings_by_month"
                  :key="item.month"
                  class="d-flex align-center justify-space-between py-2"
                >
                  <span class="text-body-2">{{
                    getMonthName(item.month)
                  }}</span>
                  <VChip color="info" size="small"
                    >{{ item.count }} listings</VChip
                  >
                </div>
              </div>
              <div v-else class="text-center py-4">
                <p class="text-medium-emphasis">No data available</p>
              </div>
            </VCardText>
          </VCard>
        </VCol>

        <!-- System Overview -->
        <VCol cols="12">
          <VCard>
            <VCardTitle>System Overview</VCardTitle>
            <VCardText>
              <VRow>
                <VCol cols="12" sm="6" md="3">
                  <div class="text-center">
                    <h3 class="text-h4 font-weight-bold text-primary">
                      {{ systemStats.total_users || 0 }}
                    </h3>
                    <p class="text-body-2 text-medium-emphasis">Total Users</p>
                  </div>
                </VCol>
                <VCol cols="12" sm="6" md="3">
                  <div class="text-center">
                    <h3 class="text-h4 font-weight-bold text-success">
                      {{ systemStats.total_admins || 0 }}
                    </h3>
                    <p class="text-body-2 text-medium-emphasis">Total Admins</p>
                  </div>
                </VCol>
                <VCol cols="12" sm="6" md="3">
                  <div class="text-center">
                    <h3 class="text-h4 font-weight-bold text-info">
                      {{ systemStats.total_listings || 0 }}
                    </h3>
                    <p class="text-body-2 text-medium-emphasis">
                      Total Listings
                    </p>
                  </div>
                </VCol>
                <VCol cols="12" sm="6" md="3">
                  <div class="text-center">
                    <h3 class="text-h4 font-weight-bold text-warning">
                      {{ systemStats.total_company_users || 0 }}
                    </h3>
                    <p class="text-body-2 text-medium-emphasis">
                      Company Users
                    </p>
                  </div>
                </VCol>
              </VRow>
            </VCardText>
          </VCard>
        </VCol>
      </VRow>
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

const loading = ref(false);
const stats = ref({});
const systemStats = ref({});

const loadStatistics = async () => {
  loading.value = true;
  try {
    // Load detailed statistics
    const statsResponse = await $api("/admin/statistics", {
      method: "GET",
    });
    stats.value = statsResponse;

    // Load system overview
    const dashboardResponse = await $api("/admin/dashboard", {
      method: "GET",
    });
    systemStats.value = dashboardResponse;
  } catch (error) {
    console.error("Error loading statistics:", error);
  } finally {
    loading.value = false;
  }
};

const getMonthName = (monthNumber) => {
  const months = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
  ];
  return months[monthNumber - 1] || "Unknown";
};

// Load statistics on mount
onMounted(() => {
  // Check if user is admin
  const userDataCookie = useCookie("userData");
  const userData = userDataCookie.value;

  if (!userData || userData.role !== "admin") {
    console.log("Non-admin user detected, redirecting...");
    router.push("/");
    return;
  }

  loadStatistics();
});
</script>

<style scoped>
.v-card {
  border-radius: 8px;
}
</style>
