<script setup>
definePage({
  meta: {
    layout: "default",
    action: "read",
    subject: "Statistics",
  },
});

const selectedPeriod = ref("month");
const selectedEvent = ref("all");

const periodOptions = [
  { title: "Last 7 Days", value: "week" },
  { title: "Last 30 Days", value: "month" },
  { title: "Last 3 Months", value: "quarter" },
  { title: "Last Year", value: "year" },
];

const eventOptions = [
  { title: "All Events", value: "all" },
  { title: "Mountain Hiking Adventure", value: "mountain-hiking" },
  { title: "City Walking Tour", value: "city-walking" },
  { title: "Wine Tasting Experience", value: "wine-tasting" },
  { title: "Beach Yoga Session", value: "beach-yoga" },
];

const statistics = ref({
  totalBookings: 156,
  totalRevenue: 12500,
  averageBookingValue: 80.13,
  conversionRate: 12.5,
  repeatCustomers: 45,
  newCustomers: 111,
});

const bookingTrends = ref([
  { month: "Jan", bookings: 12, revenue: 960 },
  { month: "Feb", bookings: 19, revenue: 1520 },
  { month: "Mar", bookings: 15, revenue: 1200 },
  { month: "Apr", bookings: 22, revenue: 1760 },
  { month: "May", bookings: 28, revenue: 2240 },
  { month: "Jun", bookings: 35, revenue: 2800 },
  { month: "Jul", bookings: 42, revenue: 3360 },
  { month: "Aug", bookings: 38, revenue: 3040 },
  { month: "Sep", bookings: 31, revenue: 2480 },
  { month: "Oct", bookings: 25, revenue: 2000 },
  { month: "Nov", bookings: 18, revenue: 1440 },
  { month: "Dec", bookings: 22, revenue: 1760 },
]);

const eventPerformance = ref([
  {
    event: "Mountain Hiking Adventure",
    bookings: 45,
    revenue: 6750,
    avgRating: 4.8,
    completionRate: 95,
  },
  {
    event: "City Walking Tour",
    bookings: 38,
    revenue: 1710,
    avgRating: 4.6,
    completionRate: 92,
  },
  {
    event: "Wine Tasting Experience",
    bookings: 32,
    revenue: 2560,
    avgRating: 4.9,
    completionRate: 98,
  },
  {
    event: "Beach Yoga Session",
    bookings: 25,
    revenue: 750,
    avgRating: 4.7,
    completionRate: 88,
  },
]);

const customerSegments = ref([
  { segment: "New Customers", count: 111, percentage: 71 },
  { segment: "Repeat Customers", count: 45, percentage: 29 },
]);

const formatCurrency = (amount) => {
  return new Intl.NumberFormat("de-DE", {
    style: "currency",
    currency: "EUR",
  }).format(amount);
};

const formatPercentage = (value) => {
  return `${value}%`;
};
</script>

<template>
  <div>
    <!-- Header with Filters -->
    <VCard class="mb-6">
      <VCardTitle class="d-flex align-center justify-space-between pa-6">
        <div>
          <h2 class="text-h4 font-weight-bold">Booking Statistics</h2>
          <p class="text-body-2 text-medium-emphasis mt-1">
            Analyze your booking performance and trends
          </p>
        </div>
        <div class="d-flex gap-3">
          <VSelect
            v-model="selectedPeriod"
            :items="periodOptions"
            label="Time Period"
            variant="outlined"
            density="compact"
            style="width: 150px"
          />
          <VSelect
            v-model="selectedEvent"
            :items="eventOptions"
            label="Event"
            variant="outlined"
            density="compact"
            style="width: 200px"
          />
        </div>
      </VCardTitle>
    </VCard>

    <!-- Key Metrics Cards -->
    <VRow class="mb-6">
      <VCol cols="12" sm="6" md="3">
        <VCard>
          <VCardText class="pa-4">
            <div class="d-flex align-center justify-space-between">
              <div>
                <h3
                  class="text-h6 font-weight-medium text-medium-emphasis mb-1"
                >
                  Total Bookings
                </h3>
                <h2 class="text-h4 font-weight-bold text-primary">
                  {{ statistics.totalBookings }}
                </h2>
                <p class="text-caption text-success">+12.5% vs last period</p>
              </div>
              <VAvatar color="primary" variant="tonal" size="48">
                <VIcon size="24" icon="tabler-calendar" />
              </VAvatar>
            </div>
          </VCardText>
        </VCard>
      </VCol>

      <VCol cols="12" sm="6" md="3">
        <VCard>
          <VCardText class="pa-4">
            <div class="d-flex align-center justify-space-between">
              <div>
                <h3
                  class="text-h6 font-weight-medium text-medium-emphasis mb-1"
                >
                  Total Revenue
                </h3>
                <h2 class="text-h4 font-weight-bold text-success">
                  {{ formatCurrency(statistics.totalRevenue) }}
                </h2>
                <p class="text-caption text-success">+8.3% vs last period</p>
              </div>
              <VAvatar color="success" variant="tonal" size="48">
                <VIcon size="24" icon="tabler-currency-euro" />
              </VAvatar>
            </div>
          </VCardText>
        </VCard>
      </VCol>

      <VCol cols="12" sm="6" md="3">
        <VCard>
          <VCardText class="pa-4">
            <div class="d-flex align-center justify-space-between">
              <div>
                <h3
                  class="text-h6 font-weight-medium text-medium-emphasis mb-1"
                >
                  Avg Booking Value
                </h3>
                <h2 class="text-h4 font-weight-bold text-info">
                  {{ formatCurrency(statistics.averageBookingValue) }}
                </h2>
                <p class="text-caption text-info">+2.1% vs last period</p>
              </div>
              <VAvatar color="info" variant="tonal" size="48">
                <VIcon size="24" icon="tabler-chart-line" />
              </VAvatar>
            </div>
          </VCardText>
        </VCard>
      </VCol>

      <VCol cols="12" sm="6" md="3">
        <VCard>
          <VCardText class="pa-4">
            <div class="d-flex align-center justify-space-between">
              <div>
                <h3
                  class="text-h6 font-weight-medium text-medium-emphasis mb-1"
                >
                  Conversion Rate
                </h3>
                <h2 class="text-h4 font-weight-bold text-warning">
                  {{ formatPercentage(statistics.conversionRate) }}
                </h2>
                <p class="text-caption text-warning">+1.2% vs last period</p>
              </div>
              <VAvatar color="warning" variant="tonal" size="48">
                <VIcon size="24" icon="tabler-target" />
              </VAvatar>
            </div>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>

    <!-- Charts Row -->
    <VRow class="mb-6">
      <VCol cols="12" lg="8">
        <VCard>
          <VCardTitle class="pa-6">
            <h3 class="text-h5 font-weight-bold">Booking Trends</h3>
          </VCardTitle>
          <VCardText class="pa-6 pt-0">
            <div
              style="
                height: 300px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #f5f5f5;
                border-radius: 8px;
              "
            >
              <div class="text-center">
                <VIcon size="48" icon="tabler-chart-line" color="grey" />
                <p class="text-body-1 text-medium-emphasis mt-2">
                  Chart Component
                </p>
                <p class="text-caption text-medium-emphasis">
                  Booking trends over time would be displayed here
                </p>
              </div>
            </div>
          </VCardText>
        </VCard>
      </VCol>

      <VCol cols="12" lg="4">
        <VCard>
          <VCardTitle class="pa-6">
            <h3 class="text-h5 font-weight-bold">Customer Segments</h3>
          </VCardTitle>
          <VCardText class="pa-6 pt-0">
            <div
              style="
                height: 300px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #f5f5f5;
                border-radius: 8px;
              "
            >
              <div class="text-center">
                <VIcon size="48" icon="tabler-chart-pie" color="grey" />
                <p class="text-body-1 text-medium-emphasis mt-2">
                  Pie Chart Component
                </p>
                <p class="text-caption text-medium-emphasis">
                  Customer segments would be displayed here
                </p>
              </div>
            </div>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>

    <!-- Event Performance Table -->
    <VCard>
      <VCardTitle class="pa-6">
        <h3 class="text-h5 font-weight-bold">Event Performance</h3>
      </VCardTitle>
      <VCardText class="pa-0">
        <VDataTable
          :headers="[
            { title: 'Event', key: 'event', sortable: true },
            { title: 'Bookings', key: 'bookings', sortable: true },
            { title: 'Revenue', key: 'revenue', sortable: true },
            { title: 'Avg Rating', key: 'avgRating', sortable: true },
            { title: 'Completion Rate', key: 'completionRate', sortable: true },
          ]"
          :items="eventPerformance"
          :items-per-page="10"
          class="performance-table"
        >
          <!-- Revenue Column -->
          <template #item.revenue="{ item }">
            <span class="font-weight-medium">{{
              formatCurrency(item.revenue)
            }}</span>
          </template>

          <!-- Avg Rating Column -->
          <template #item.avgRating="{ item }">
            <div class="d-flex align-center">
              <span class="font-weight-medium me-2">{{ item.avgRating }}</span>
              <VIcon icon="tabler-star" size="16" color="warning" />
            </div>
          </template>

          <!-- Completion Rate Column -->
          <template #item.completionRate="{ item }">
            <VChip
              :color="
                item.completionRate >= 95
                  ? 'success'
                  : item.completionRate >= 90
                  ? 'warning'
                  : 'error'
              "
              size="small"
              class="font-weight-medium"
            >
              {{ formatPercentage(item.completionRate) }}
            </VChip>
          </template>
        </VDataTable>
      </VCardText>
    </VCard>

    <!-- Additional Statistics -->
    <VRow class="mt-6">
      <VCol cols="12" md="6">
        <VCard>
          <VCardTitle class="pa-6">
            <h3 class="text-h5 font-weight-bold">Customer Insights</h3>
          </VCardTitle>
          <VCardText class="pa-6 pt-0">
            <div class="d-flex justify-space-between align-center mb-4">
              <span class="text-body-1">New Customers</span>
              <div class="d-flex align-center">
                <span class="font-weight-medium me-2">{{
                  statistics.newCustomers
                }}</span>
                <span class="text-caption text-medium-emphasis"
                  >({{ formatPercentage(71) }})</span
                >
              </div>
            </div>
            <div class="d-flex justify-space-between align-center mb-4">
              <span class="text-body-1">Repeat Customers</span>
              <div class="d-flex align-center">
                <span class="font-weight-medium me-2">{{
                  statistics.repeatCustomers
                }}</span>
                <span class="text-caption text-medium-emphasis"
                  >({{ formatPercentage(29) }})</span
                >
              </div>
            </div>
            <VDivider class="my-4" />
            <div class="d-flex justify-space-between align-center">
              <span class="text-h6 font-weight-bold">Total Customers</span>
              <span class="text-h6 font-weight-bold text-primary">{{
                statistics.newCustomers + statistics.repeatCustomers
              }}</span>
            </div>
          </VCardText>
        </VCard>
      </VCol>

      <VCol cols="12" md="6">
        <VCard>
          <VCardTitle class="pa-6">
            <h3 class="text-h5 font-weight-bold">Quick Actions</h3>
          </VCardTitle>
          <VCardText class="pa-6 pt-0">
            <VRow>
              <VCol cols="6">
                <VBtn
                  block
                  variant="outlined"
                  prepend-icon="tabler-download"
                  class="mb-3"
                >
                  Export Report
                </VBtn>
              </VCol>
              <VCol cols="6">
                <VBtn
                  block
                  variant="outlined"
                  prepend-icon="tabler-chart-bar"
                  class="mb-3"
                >
                  Detailed Analytics
                </VBtn>
              </VCol>
              <VCol cols="6">
                <VBtn
                  block
                  variant="outlined"
                  prepend-icon="tabler-mail"
                  class="mb-3"
                >
                  Email Report
                </VBtn>
              </VCol>
              <VCol cols="6">
                <VBtn
                  block
                  variant="outlined"
                  prepend-icon="tabler-settings"
                  class="mb-3"
                >
                  Configure Alerts
                </VBtn>
              </VCol>
            </VRow>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </div>
</template>

<style scoped>
.performance-table {
  border-radius: 8px;
}
</style>
