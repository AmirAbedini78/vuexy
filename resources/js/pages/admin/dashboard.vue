<template>
  <div class="admin-dashboard">
    <!-- Admin Panel Title -->
    <div class="admin-panel-title mb-6">
      <h1 class="text-h3 font-weight-bold">Admin Panel</h1>
    </div>

    <!-- Overview Section -->
    <div class="section-title mb-4">
      <h2 class="text-h4 font-weight-bold">Overview</h2>
    </div>

    <!-- KPI Cards Section - Single Card with Dividers -->
    <VCard class="kpi-container mb-8">
      <VCardText class="pa-0">
        <VRow no-gutters>
          <VCol cols="12" sm="6" md="3" class="kpi-section">
            <div class="kpi-content-wrapper">
              <div class="kpi-content">
                <div class="kpi-header">
                  <div class="kpi-icon-wrapper">
                    <VIcon icon="tabler-eye" size="20" color="#6c757d" />
                  </div>
                  <p class="kpi-title">All Providers</p>
                </div>
                <h3 class="kpi-value">{{ stats.all_providers || 211 }}</h3>
                <p class="kpi-trend positive">This month +15.9%</p>
              </div>
            </div>
          </VCol>

          <VCol cols="12" sm="6" md="3" class="kpi-section">
            <div class="kpi-content-wrapper">
              <div class="kpi-content">
                <div class="kpi-header">
                  <div class="kpi-icon-wrapper">
                    <VIcon icon="tabler-file-text" size="20" color="#6c757d" />
                  </div>
                  <p class="kpi-title">Total Bookings</p>
                </div>
                <h3 class="kpi-value">{{ stats.total_bookings || 345 }}</h3>
                <p class="kpi-trend positive">This month +5.7%</p>
              </div>
            </div>
          </VCol>

          <VCol cols="12" sm="6" md="3" class="kpi-section">
            <div class="kpi-content-wrapper">
              <div class="kpi-content">
                <div class="kpi-header">
                  <div class="kpi-icon-wrapper">
                    <VIcon icon="tabler-chart-bar" size="20" color="#6c757d" />
                  </div>
                  <p class="kpi-title">Total Explorer Passports</p>
                </div>
                <h3 class="kpi-value">
                  {{ stats.total_explorer_passports || 840 }}
                </h3>
                <p class="kpi-trend positive">This month +12.4%</p>
              </div>
            </div>
          </VCol>

          <VCol cols="12" sm="6" md="3" class="kpi-section">
            <div class="kpi-content-wrapper">
              <div class="kpi-content">
                <div class="kpi-header">
                  <div class="kpi-icon-wrapper">
                    <VIcon
                      icon="tabler-currency-euro"
                      size="20"
                      color="#6c757d"
                    />
                  </div>
                  <p class="kpi-title">Total Revenue</p>
                </div>
                <h3 class="kpi-value">
                  €{{ formatCurrency(stats.total_revenue || 590000) }}
                </h3>
                <p class="kpi-trend">This month</p>
              </div>
            </div>
          </VCol>
        </VRow>
      </VCardText>
    </VCard>

    <!-- All Events Section -->
    <div class="section-title mb-4">
      <h2 class="text-h4 font-weight-bold">All Events</h2>
    </div>

    <VCard class="mb-8">
      <VCardTitle class="d-flex align-center justify-space-between pa-6">
        <div class="d-flex align-center">
          <VTextField
            v-model="eventsSearch"
            label="Search Events"
            placeholder="Search events..."
            prepend-inner-icon="tabler-search"
            variant="outlined"
            density="compact"
            class="me-4"
            style="width: 300px"
          />
        </div>
        <div class="d-flex gap-2">
          <VBtn
            variant="outlined"
            prepend-icon="tabler-upload"
            @click="exportEvents"
          >
            Export
          </VBtn>
          <VBtn color="warning" prepend-icon="tabler-plus" @click="addEvent">
            Add Event
          </VBtn>
        </div>
      </VCardTitle>

      <VDataTable
        :headers="eventsHeaders"
        :items="eventsData"
        :search="eventsSearch"
        :items-per-page="7"
        class="events-table"
      >
        <!-- Events Column -->
        <template #item.events="{ item }">
          <div>
            <div class="font-weight-medium">{{ item.eventTitle }}</div>
            <div class="text-caption text-medium-emphasis">
              {{ item.location }}
            </div>
          </div>
        </template>

        <!-- Status Column -->
        <template #item.status="{ item }">
          <VChip
            :color="getStatusColor(item.status)"
            size="small"
            class="font-weight-medium"
          >
            {{ item.status }}
          </VChip>
        </template>

        <!-- Actions Column -->
        <template #item.actions="{ item }">
          <div class="d-flex gap-1">
            <VBtn icon variant="text" size="small" @click="editEvent(item)">
              <VIcon icon="tabler-edit" size="18" />
            </VBtn>
            <VBtn icon variant="text" size="small" @click="showEventMenu(item)">
              <VIcon icon="tabler-dots-vertical" size="18" />
            </VBtn>
          </div>
        </template>

        <!-- Pagination -->
        <template #bottom>
          <VCardText class="pt-2">
            <div
              class="d-flex flex-wrap justify-center justify-sm-space-between gap-y-2 mt-2"
            >
              <span class="text-body-2">Showing 1 to 7 of 100 entries</span>
              <VPagination
                v-model="eventsPage"
                :total-visible="$vuetify.display.smAndDown ? 3 : 5"
                :length="Math.ceil(eventsData.length / 7)"
              />
            </div>
          </VCardText>
        </template>
      </VDataTable>
    </VCard>

    <!-- All Bookings Section -->
    <div class="section-title mb-4">
      <h2 class="text-h4 font-weight-bold">All Bookings</h2>
    </div>

    <VCard class="mb-8">
      <VCardTitle class="d-flex align-center justify-space-between pa-6">
        <div class="d-flex align-center">
          <VTextField
            v-model="usersSearch"
            label="Search Users"
            placeholder="Search users..."
            prepend-inner-icon="tabler-search"
            variant="outlined"
            density="compact"
            class="me-4"
            style="width: 300px"
          />
        </div>
        <div class="d-flex gap-2">
          <VBtn
            variant="outlined"
            prepend-icon="tabler-upload"
            @click="exportUsers"
          >
            Export
          </VBtn>
          <VBtn color="warning" prepend-icon="tabler-plus" @click="addBooking">
            Add Booking
          </VBtn>
        </div>
      </VCardTitle>

      <VDataTable
        :headers="usersHeaders"
        :items="usersData"
        :search="usersSearch"
        :items-per-page="7"
        class="users-table"
      >
        <!-- User Details Column -->
        <template #item.userDetails="{ item }">
          <div>
            <div class="font-weight-medium">{{ item.name }}</div>
            <div class="text-caption text-medium-emphasis">
              {{ item.username }}
            </div>
          </div>
        </template>

        <!-- Status Column -->
        <template #item.status="{ item }">
          <VChip
            :color="getUserStatusColor(item.status)"
            size="small"
            class="font-weight-medium"
          >
            {{ item.status }}
          </VChip>
        </template>

        <!-- Action Column -->
        <template #item.action="{ item }">
          <div class="d-flex gap-1">
            <VBtn icon variant="text" size="small" @click="viewUser(item)">
              <VIcon icon="tabler-eye" size="18" />
            </VBtn>
            <VBtn icon variant="text" size="small" @click="showUserMenu(item)">
              <VIcon icon="tabler-dots-vertical" size="18" />
            </VBtn>
          </div>
        </template>

        <!-- Pagination -->
        <template #bottom>
          <VCardText class="pt-2">
            <div
              class="d-flex flex-wrap justify-center justify-sm-space-between gap-y-2 mt-2"
            >
              <span class="text-body-2">Showing 1 to 7 of 50 entries</span>
              <VPagination
                v-model="usersPage"
                :total-visible="$vuetify.display.smAndDown ? 3 : 5"
                :length="Math.ceil(usersData.length / 7)"
              />
            </div>
          </VCardText>
        </template>
      </VDataTable>
    </VCard>

    <!-- All Providers Section -->
    <div class="section-title mb-4">
      <h2 class="text-h4 font-weight-bold">All Providers</h2>
    </div>

    <VCard class="mb-8">
      <VCardTitle class="d-flex align-center justify-space-between pa-6">
        <div class="d-flex align-center">
          <VTextField
            v-model="providersSearch"
            label="Search Providers"
            placeholder="Search providers..."
            prepend-inner-icon="tabler-search"
            variant="outlined"
            density="compact"
            class="me-4"
            style="width: 300px"
          />
        </div>
        <div class="d-flex gap-2">
          <VBtn
            variant="outlined"
            prepend-icon="tabler-upload"
            @click="exportProviders"
          >
            Export
          </VBtn>
          <VBtn color="warning" prepend-icon="tabler-plus" @click="addProvider">
            Add Provider
          </VBtn>
        </div>
      </VCardTitle>

      <VDataTable
        :headers="providersHeaders"
        :items="providersData"
        :search="providersSearch"
        :items-per-page="7"
        class="providers-table"
      >
        <!-- Provider Column -->
        <template #item.provider="{ item }">
          <div>
            <div class="font-weight-medium">{{ item.provider_name }}</div>
            <div class="text-caption text-medium-emphasis">
              {{
                item.provider_type === "individual"
                  ? "Individual Provider"
                  : "Company Provider"
              }}
            </div>
          </div>
        </template>

        <!-- Provider ID Column -->
        <template #item.provider_id="{ item }">
          <span class="font-weight-medium">{{
            item.id.toString().padStart(6, "0")
          }}</span>
        </template>

        <!-- Status Column -->
        <template #item.status="{ item }">
          <VChip
            :color="getProviderStatusColor(item.status)"
            size="small"
            class="font-weight-medium"
          >
            {{ item.status }}
          </VChip>
        </template>

        <!-- Total Listing Column -->
        <template #item.total_listings="{ item }">
          <span class="font-weight-medium">{{ item.total_listings }}</span>
        </template>

        <!-- Total Booking Column -->
        <template #item.total_bookings="{ item }">
          <span class="font-weight-medium">{{ item.total_bookings }}</span>
        </template>

        <!-- Actions Column -->
        <template #item.actions="{ item }">
          <div class="d-flex gap-1">
            <VBtn icon variant="text" size="small" @click="viewProvider(item)">
              <VIcon icon="tabler-eye" size="18" />
            </VBtn>
            <VBtn
              icon
              variant="text"
              size="small"
              @click="showProviderMenu(item)"
            >
              <VIcon icon="tabler-dots-vertical" size="18" />
            </VBtn>
          </div>
        </template>

        <!-- Pagination -->
        <template #bottom>
          <VCardText class="pt-2">
            <div
              class="d-flex flex-wrap justify-center justify-sm-space-between gap-y-2 mt-2"
            >
              <span class="text-body-2">Showing 1 to 7 of 100 entries</span>
              <VPagination
                v-model="providersPage"
                :total-visible="$vuetify.display.smAndDown ? 3 : 5"
                :length="Math.ceil(providersData.length / 7)"
              />
            </div>
          </VCardText>
        </template>
      </VDataTable>
    </VCard>

    <!-- All Users Section -->
    <div class="section-title mb-4">
      <h2 class="text-h4 font-weight-bold">All Users</h2>
    </div>

    <VCard>
      <VCardTitle class="d-flex align-center justify-space-between pa-6">
        <div class="d-flex align-center">
          <VTextField
            v-model="ordersSearch"
            label="Search Order"
            placeholder="Search orders..."
            prepend-inner-icon="tabler-search"
            variant="outlined"
            density="compact"
            class="me-4"
            style="width: 300px"
          />
        </div>
        <div class="d-flex gap-2">
          <VBtn
            variant="outlined"
            prepend-icon="tabler-upload"
            @click="exportOrders"
          >
            Export
          </VBtn>
          <VBtn color="warning" prepend-icon="tabler-plus" @click="addUser">
            Add User
          </VBtn>
        </div>
      </VCardTitle>

      <VDataTable
        :headers="ordersHeaders"
        :items="ordersData"
        :search="ordersSearch"
        :items-per-page="8"
        class="orders-table"
      >
        <!-- Users Column -->
        <template #item.users="{ item }">
          <div>
            <div class="font-weight-medium">{{ item.name }}</div>
            <div class="text-caption text-medium-emphasis">
              {{ item.email }}
            </div>
          </div>
        </template>

        <!-- Total Spent Column -->
        <template #item.totalSpent="{ item }">
          <span class="font-weight-medium"
            >€{{ formatCurrency(item.totalSpent) }}</span
          >
        </template>

        <!-- Action Column -->
        <template #item.action="{ item }">
          <div class="d-flex gap-1">
            <VBtn icon variant="text" size="small" @click="viewOrder(item)">
              <VIcon icon="tabler-eye" size="18" />
            </VBtn>
            <VBtn icon variant="text" size="small" @click="showOrderMenu(item)">
              <VIcon icon="tabler-dots-vertical" size="18" />
            </VBtn>
          </div>
        </template>

        <!-- Pagination -->
        <template #bottom>
          <VCardText class="pt-2">
            <div
              class="d-flex flex-wrap justify-center justify-sm-space-between gap-y-2 mt-2"
            >
              <span class="text-body-2">Showing 1 to 8 of 100 entries</span>
              <VPagination
                v-model="ordersPage"
                :total-visible="$vuetify.display.smAndDown ? 3 : 5"
                :length="Math.ceil(ordersData.length / 8)"
              />
            </div>
          </VCardText>
        </template>
      </VDataTable>
    </VCard>
  </div>
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

// Search queries
const eventsSearch = ref("");
const usersSearch = ref("");
const ordersSearch = ref("");
const providersSearch = ref("");

// Pagination
const eventsPage = ref(3);
const usersPage = ref(3);
const ordersPage = ref(3);
const providersPage = ref(3);

// Get user data from cookies
const userDataCookie = useCookie("userData");
if (userDataCookie.value) {
  userData.value = userDataCookie.value;
}

// Events data - exact data from screenshot
const eventsData = ref([
  {
    id: 1,
    eventTitle: "Event Title 1",
    location: "Event Location 1",
    provider: "Sam Smith",
    advId: "647838",
    price: "€1189",
    participants: "6/6",
    status: "Submitted",
  },
  {
    id: 2,
    eventTitle: "Event Title 2",
    location: "Event Location 2",
    provider: "Sara Smith",
    advId: "765497",
    price: "€1456",
    participants: "12/12",
    status: "Other Events",
  },
  {
    id: 3,
    eventTitle: "Event Title 3",
    location: "Event Location 3",
    provider: "Sam Smith",
    advId: "348579",
    price: "€1999",
    participants: "10/15",
    status: "Denied",
  },
  {
    id: 4,
    eventTitle: "Event Title 4",
    location: "Event Location 4",
    provider: "Sara Smith",
    advId: "903847",
    price: "€2400",
    participants: "0/10",
    status: "Edit Review Pending",
  },
  {
    id: 5,
    eventTitle: "Event Title 5",
    location: "Event Location 5",
    provider: "Sam Smith",
    advId: "384769",
    price: "€1349",
    participants: "12/12",
    status: "Approved",
  },
  {
    id: 6,
    eventTitle: "Event Title 6",
    location: "Event Location 6",
    provider: "Sara Smith",
    advId: "348579",
    price: "€1800",
    participants: "2/4",
    status: "Live",
  },
  {
    id: 7,
    eventTitle: "Event Title 7",
    location: "Event Location 7",
    provider: "Sam Smith",
    advId: "238474",
    price: "€2000",
    participants: "11/12",
    status: "Submitted",
  },
]);

// Users data - exact data from screenshot
const usersData = ref([
  {
    id: 1,
    name: "UserName",
    username: "User ID",
    advId: "647838",
    accountType: "Platform Access",
    billingType: "Bank wire",
    status: "Booked",
  },
  {
    id: 2,
    name: "Richard Payne",
    username: "richard247",
    advId: "647838",
    accountType: "Subscription",
    billingType: "Auto Debit",
    status: "Booked",
  },
  {
    id: 3,
    name: "Jennifer Summers",
    username: "summers.45",
    advId: "647838",
    accountType: "Platform Access",
    billingType: "Auto Debit",
    status: "Booked",
  },
  {
    id: 4,
    name: "Mr. Justin Richardson",
    username: "jr.3734",
    advId: "647838",
    accountType: "Subscription",
    billingType: "Manual Paypal",
    status: "Booked",
  },
  {
    id: 5,
    name: "Nicholas Tanner",
    username: "nicholas.t",
    advId: "647838",
    accountType: "Platform Access",
    billingType: "Bank Wire",
    status: "Pending",
  },
  {
    id: 6,
    name: "Mary Garcia",
    username: "mary.garcia",
    advId: "647838",
    accountType: "Subscription",
    billingType: "Auto Debit",
    status: "Failed",
  },
  {
    id: 7,
    name: "Joseph Oliver",
    username: "joseph.87",
    advId: "647838",
    accountType: "Platform Access",
    billingType: "Manual Cash",
    status: "Booked",
  },
]);

// Orders data - exact data from screenshot
const ordersData = ref([
  {
    id: 1,
    name: "Jordan Stevenson",
    email: "dayna19@yahoo.com",
    userId: "#345889",
    country: "United States",
    bookings: 41,
    totalSpent: 32907,
  },
  {
    id: 2,
    name: "Benedetto Rossiter",
    email: "lorena@yahoo.com",
    userId: "#234875",
    country: "Brazil",
    bookings: 12,
    totalSpent: 5347,
  },
  {
    id: 3,
    name: "Bentlee Emblin",
    email: "bemblinf@gmail.com",
    userId: "#783645",
    country: "India",
    bookings: 36,
    totalSpent: 12458,
  },
  {
    id: 4,
    name: "Bertha Biner",
    email: "bbinerh@gmail.com",
    userId: "#234876",
    country: "Australia",
    bookings: 7,
    totalSpent: 1345,
  },
  {
    id: 5,
    name: "Beverlie Krabbe",
    email: "bkrabbeld@gmail.com",
    userId: "#234598",
    country: "France",
    bookings: 34,
    totalSpent: 24435,
  },
  {
    id: 6,
    name: "Bradan Rosebotham",
    email: "brosebothamz@gmail.com",
    userId: "#345978",
    country: "China",
    bookings: 67,
    totalSpent: 72345,
  },
  {
    id: 7,
    name: "Bree Kilday",
    email: "bkildayr@gmail.com",
    userId: "#987623",
    country: "United States",
    bookings: 28,
    totalSpent: 18458,
  },
  {
    id: 8,
    name: "Breena Gallemore",
    email: "bgallemore6@gmail.com",
    userId: "#873645",
    country: "India",
    bookings: 127,
    totalSpent: 142349,
  },
]);

// Providers data - will be loaded from API
const providersData = ref([]);

// Table headers
const eventsHeaders = [
  { title: "EVENTS", key: "events", sortable: false },
  { title: "PROVIDER", key: "provider", sortable: false },
  { title: "ADV ID", key: "advId", sortable: false },
  { title: "PRICE", key: "price", sortable: false },
  { title: "PARTICIPANTS", key: "participants", sortable: false },
  { title: "STATUS?", key: "status", sortable: false },
  { title: "ACTIONS", key: "actions", sortable: false, width: "120px" },
];

const usersHeaders = [
  { title: "USER DETAILS", key: "userDetails", sortable: false },
  { title: "ADV ID", key: "advId", sortable: false },
  { title: "ACCOUNT TYPE", key: "accountType", sortable: false },
  { title: "BILLING TYPE", key: "billingType", sortable: false },
  { title: "STATUS", key: "status", sortable: false },
  { title: "ACTION", key: "action", sortable: false, width: "120px" },
];

const ordersHeaders = [
  { title: "USERS", key: "users", sortable: false },
  { title: "USER ID", key: "userId", sortable: false },
  { title: "COUNTRY", key: "country", sortable: false },
  { title: "BOOKINGS", key: "bookings", sortable: false },
  { title: "TOTAL SPENT", key: "totalSpent", sortable: false },
  { title: "ACTION", key: "action", sortable: false, width: "120px" },
];

const providersHeaders = [
  { title: "PROVIDER", key: "provider", sortable: false },
  { title: "PROVIDER ID", key: "provider_id", sortable: false },
  { title: "STATUS", key: "status", sortable: false },
  { title: "TOTAL LISTING", key: "total_listings", sortable: false },
  { title: "TOTAL BOOKING", key: "total_bookings", sortable: false },
  { title: "ACTIONS", key: "actions", sortable: false, width: "120px" },
];

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
    console.log("Loading dashboard data...");

    const response = await $api("/admin/dashboard", {
      method: "GET",
    });
    console.log("Dashboard response:", response);
    stats.value = response;

    // Load providers data
    console.log("Loading providers data...");
    try {
      // ابتدا از test route استفاده می‌کنیم (بدون authentication)
      const testResponse = await $api("/test/providers", {
        method: "GET",
      });
      console.log("Test providers response:", testResponse);
      providersData.value = testResponse.data || [];
      console.log("Test providers data set:", providersData.value);
    } catch (error) {
      console.log("Test route failed, trying admin route...");
      try {
        const providersResponse = await $api("/admin/providers", {
          method: "GET",
        });
        console.log("Admin providers response:", providersResponse);
        providersData.value = providersResponse.data || [];
        console.log("Admin providers data set:", providersData.value);
      } catch (adminError) {
        console.error("Both routes failed:", adminError);
        providersData.value = [];
      }
    }
  } catch (error) {
    console.error("Error loading dashboard data:", error);
    console.error("Error details:", {
      message: error.message,
      status: error.response?.status,
      data: error.response?.data,
    });

    // Show fallback data instead of redirecting
    stats.value = {
      all_providers: 211,
      total_bookings: 345,
      total_explorer_passports: 840,
      total_revenue: 590000,
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

const formatCurrency = (amount) => {
  return new Intl.NumberFormat("en-US", {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount);
};

const getStatusColor = (status) => {
  const colors = {
    Submitted: "warning",
    "Other Events": "secondary",
    Denied: "error",
    "Edit Review Pending": "warning",
    Approved: "info",
    Live: "success",
  };
  return colors[status] || "secondary";
};

const getUserStatusColor = (status) => {
  const colors = {
    Booked: "success",
    Pending: "warning",
    Failed: "error",
  };
  return colors[status] || "secondary";
};

// Action functions
const exportEvents = () => {
  console.log("Exporting events...");
};

const addEvent = () => {
  console.log("Adding event...");
};

const editEvent = (item) => {
  console.log("Editing event:", item);
};

const showEventMenu = (item) => {
  console.log("Showing event menu:", item);
};

const exportUsers = () => {
  console.log("Exporting users...");
};

const addBooking = () => {
  console.log("Adding booking...");
};

const viewUser = (item) => {
  console.log("Viewing user:", item);
};

const showUserMenu = (item) => {
  console.log("Showing user menu:", item);
};

const exportOrders = () => {
  console.log("Exporting orders...");
};

const addUser = () => {
  console.log("Adding user...");
};

const viewOrder = (item) => {
  console.log("Viewing order:", item);
};

const showOrderMenu = (item) => {
  console.log("Showing order menu:", item);
};

// Provider action functions
const exportProviders = () => {
  console.log("Exporting providers...");
};

const addProvider = () => {
  console.log("Adding provider...");
};

const viewProvider = (item) => {
  console.log("Viewing provider:", item);
};

const showProviderMenu = (item) => {
  console.log("Showing provider menu:", item);
};

const getProviderStatusColor = (status) => {
  const colors = {
    Live: "success",
    Denied: "error",
  };
  return colors[status] || "secondary";
};
</script>

<style lang="scss" scoped>
.admin-dashboard {
  padding: 24px;
  min-height: 100vh;
  font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

.admin-panel-title {
  h1 {
    color: #2c3e50;
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    font-family: "Anton", "Inter", "Segoe UI", Tahoma, Geneva, Verdana,
      sans-serif;
  }
}

.section-title {
  h2 {
    color: #2c3e50;
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0;
    font-family: "Anton", "Inter", "Segoe UI", Tahoma, Geneva, Verdana,
      sans-serif;
  }
}

.kpi-container {
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  background-color: #ffffff;
  overflow: hidden;

  .kpi-section {
    position: relative;
    padding: 24px;

    &:not(:last-child)::after {
      content: "";
      position: absolute;
      top: 20%;
      right: 0;
      bottom: 20%;
      width: 1px;
      background-color: #e9ecef;
    }

    .kpi-content-wrapper {
      display: flex;
      align-items: center;
      height: 100%;
    }

    .kpi-header {
      display: flex;
      align-items: center;
      margin-bottom: 8px;
    }

    .kpi-icon-wrapper {
      width: 40px;
      height: 40px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 16px;
      background-color: #f8f9fa;
      border: 1px solid #e9ecef;
    }

    .kpi-content {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      height: 100%;

      .kpi-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 4px 0;
        color: #2c3e50;
        font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      }

      .kpi-title {
        font-size: 0.875rem;
        color: #6c757d;
        margin: 0;
        font-weight: 500;
        font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      }

      .kpi-trend {
        font-size: 0.75rem;
        margin: 0;
        font-weight: 500;
        font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;

        &.positive {
          color: #28c76f;
        }

        &:not(.positive) {
          color: #6c757d;
        }
      }
    }
  }
}

.events-table,
.users-table,
.orders-table {
  .v-data-table {
    border-radius: 8px;
    overflow: hidden;
  }

  .v-data-table-header {
    background-color: #f8f9fa;
  }

  .v-data-table-header th {
    font-weight: 600;
    color: #495057;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  }
}

.v-card {
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  background-color: #ffffff;
}

.v-chip {
  font-weight: 500;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

.v-btn {
  border-radius: 6px;
  text-transform: none;
  font-weight: 500;
  font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

.v-text-field {
  .v-field {
    border-radius: 6px;
  }
}

.v-pagination {
  .v-btn {
    border-radius: 4px;
  }
}

// Global font family for all text elements
* {
  font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}
</style>
