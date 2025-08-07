<template>
  <VCard>
    <VCardText>
      <VRow>
        <!-- Welcome Section -->
        <VCol cols="12">
          <div class="d-flex align-center justify-space-between mb-6">
            <div>
              <h2 class="text-h4 font-weight-bold">
                Welcome back, {{ userData?.name || "Admin" }}! 👋
              </h2>
              <p class="text-body-1 text-medium-emphasis">
                Here's what's happening with your platform today.
              </p>
            </div>
            <VBtn
              color="primary"
              prepend-icon="tabler-refresh"
              @click="loadDashboardData"
              :loading="loading"
            >
              Refresh Data
            </VBtn>
          </div>
        </VCol>

        <!-- Statistics Cards -->
        <VCol cols="12" sm="6" md="3">
          <VCard class="stat-card">
            <VCardText class="text-center">
              <VIcon
                icon="tabler-users"
                size="48"
                color="primary"
                class="mb-3"
              />
              <h3 class="text-h4 font-weight-bold text-primary">
                {{ stats.total_users || 0 }}
              </h3>
              <p class="text-body-2 text-medium-emphasis">Total Users</p>
            </VCardText>
          </VCard>
        </VCol>

        <VCol cols="12" sm="6" md="3">
          <VCard class="stat-card">
            <VCardText class="text-center">
              <VIcon
                icon="tabler-shield-check"
                size="48"
                color="success"
                class="mb-3"
              />
              <h3 class="text-h4 font-weight-bold text-success">
                {{ stats.total_admins || 0 }}
              </h3>
              <p class="text-body-2 text-medium-emphasis">Total Admins</p>
            </VCardText>
          </VCard>
        </VCol>

        <VCol cols="12" sm="6" md="3">
          <VCard class="stat-card">
            <VCardText class="text-center">
              <VIcon
                icon="tabler-map-pin"
                size="48"
                color="info"
                class="mb-3"
              />
              <h3 class="text-h4 font-weight-bold text-info">
                {{ stats.total_listings || 0 }}
              </h3>
              <p class="text-body-2 text-medium-emphasis">Total Listings</p>
            </VCardText>
          </VCard>
        </VCol>

        <VCol cols="12" sm="6" md="3">
          <VCard class="stat-card">
            <VCardText class="text-center">
              <VIcon
                icon="tabler-building"
                size="48"
                color="warning"
                class="mb-3"
              />
              <h3 class="text-h4 font-weight-bold text-warning">
                {{ stats.total_company_users || 0 }}
              </h3>
              <p class="text-body-2 text-medium-emphasis">Company Users</p>
            </VCardText>
          </VCard>
        </VCol>

        <!-- Recent Activity Section -->
        <VCol cols="12" md="6">
          <VCard>
            <VCardTitle class="d-flex align-center justify-space-between">
              <span>Recent User Registrations</span>
              <VBtn variant="text" size="small" @click="navigateToUsers">
                View All
              </VBtn>
            </VCardTitle>
            <VCardText>
              <div v-if="stats.recent_registrations?.length">
                <div
                  v-for="user in stats.recent_registrations"
                  :key="user.id"
                  class="d-flex align-center justify-space-between py-3 border-bottom"
                >
                  <div>
                    <p class="font-weight-medium mb-1">{{ user.name }}</p>
                    <p class="text-body-2 text-medium-emphasis">
                      {{ user.email }}
                    </p>
                  </div>
                  <div class="text-right">
                    <p class="text-body-2 text-medium-emphasis">
                      {{ formatDate(user.created_at) }}
                    </p>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-4">
                <p class="text-medium-emphasis">No recent registrations</p>
              </div>
            </VCardText>
          </VCard>
        </VCol>

        <VCol cols="12" md="6">
          <VCard>
            <VCardTitle class="d-flex align-center justify-space-between">
              <span>Recent Listings</span>
              <VBtn variant="text" size="small" @click="navigateToListings">
                View All
              </VBtn>
            </VCardTitle>
            <VCardText>
              <div v-if="stats.recent_listings?.length">
                <div
                  v-for="listing in stats.recent_listings"
                  :key="listing.id"
                  class="d-flex align-center justify-space-between py-3 border-bottom"
                >
                  <div>
                    <p class="font-weight-medium mb-1">{{ listing.title }}</p>
                    <p class="text-body-2 text-medium-emphasis">
                      ID: {{ listing.id }}
                    </p>
                  </div>
                  <div class="text-right">
                    <p class="text-body-2 text-medium-emphasis">
                      {{ formatDate(listing.created_at) }}
                    </p>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-4">
                <p class="text-medium-emphasis">No recent listings</p>
              </div>
            </VCardText>
          </VCard>
        </VCol>

        <!-- Quick Actions -->
        <VCol cols="12">
          <VCard>
            <VCardTitle>Quick Actions</VCardTitle>
            <VCardText>
              <VRow>
                <VCol cols="12" sm="6" md="3">
                  <VBtn
                    block
                    color="primary"
                    variant="outlined"
                    prepend-icon="tabler-users"
                    @click="navigateToUsers"
                  >
                    Manage Users
                  </VBtn>
                </VCol>
                <VCol cols="12" sm="6" md="3">
                  <VBtn
                    block
                    color="info"
                    variant="outlined"
                    prepend-icon="tabler-map-pin"
                    @click="navigateToListings"
                  >
                    Manage Listings
                  </VBtn>
                </VCol>
                <VCol cols="12" sm="6" md="3">
                  <VBtn
                    block
                    color="success"
                    variant="outlined"
                    prepend-icon="tabler-chart-bar"
                    @click="navigateToStatistics"
                  >
                    View Statistics
                  </VBtn>
                </VCol>
                <VCol cols="12" sm="6" md="3">
                  <VBtn
                    block
                    color="warning"
                    variant="outlined"
                    prepend-icon="tabler-settings"
                    @click="navigateToSettings"
                  >
                    System Settings
                  </VBtn>
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

const router = useRouter();
const loading = ref(false);
const stats = ref({});
const userData = ref(null);

// Get user data from cookies
const userDataCookie = useCookie("userData");
if (userDataCookie.value) {
  userData.value = userDataCookie.value;
}

// Additional protection - redirect non-admin users
onMounted(() => {
  if (!userData.value || userData.value.role !== "admin") {
    console.log("Non-admin user detected, redirecting...");
    router.push("/");
    return;
  }
  loadDashboardData();
});

const loadDashboardData = async () => {
  loading.value = true;
  try {
    const response = await $api("/admin/dashboard", {
      method: "GET",
    });
    stats.value = response;
  } catch (error) {
    console.error("Error loading dashboard data:", error);

    // Show fallback data instead of redirecting
    stats.value = {
      total_users: 0,
      total_admins: 0,
      total_listings: 0,
      total_company_users: 0,
      recent_registrations: [],
      recent_listings: [],
    };

    // Only redirect if it's an authentication error
    if (error.response?.status === 401 || error.response?.status === 403) {
      console.log("Authentication error, redirecting to login");
      router.push("/login");
    }
  } finally {
    loading.value = false;
  }
};

const formatDate = (dateString) => {
  if (!dateString) return "";
  return new Date(dateString).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

const navigateToUsers = () => {
  router.push("/admin/users");
};

const navigateToListings = () => {
  router.push("/admin/listings");
};

const navigateToStatistics = () => {
  router.push("/admin/statistics");
};

const navigateToSettings = () => {
  router.push("/admin/settings");
};
</script>

<style scoped>
.stat-card {
  transition: transform 0.2s ease-in-out;
}

.stat-card:hover {
  transform: translateY(-2px);
}

.border-bottom {
  border-bottom: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
}

.border-bottom:last-child {
  border-bottom: none;
}
</style>
