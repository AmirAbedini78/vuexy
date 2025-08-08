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

const financialStats = ref({
  totalRevenue: 12500,
  totalExpenses: 3200,
  netProfit: 9300,
  profitMargin: 74.4,
  averageTransactionValue: 80.13,
  totalTransactions: 156,
  pendingPayments: 450,
  refundedAmount: 120,
});

const revenueTrends = ref([
  { month: "Jan", revenue: 960, expenses: 240, profit: 720 },
  { month: "Feb", revenue: 1520, expenses: 380, profit: 1140 },
  { month: "Mar", revenue: 1200, expenses: 300, profit: 900 },
  { month: "Apr", revenue: 1760, expenses: 440, profit: 1320 },
  { month: "May", revenue: 2240, expenses: 560, profit: 1680 },
  { month: "Jun", revenue: 2800, expenses: 700, profit: 2100 },
  { month: "Jul", revenue: 3360, expenses: 840, profit: 2520 },
  { month: "Aug", revenue: 3040, expenses: 760, profit: 2280 },
  { month: "Sep", revenue: 2480, expenses: 620, profit: 1860 },
  { month: "Oct", revenue: 2000, expenses: 500, profit: 1500 },
  { month: "Nov", revenue: 1440, expenses: 360, profit: 1080 },
  { month: "Dec", revenue: 1760, expenses: 440, profit: 1320 },
]);

const expenseBreakdown = ref([
  { category: "Platform Fees", amount: 1250, percentage: 39.1 },
  { category: "Marketing", amount: 800, percentage: 25.0 },
  { category: "Equipment", amount: 600, percentage: 18.8 },
  { category: "Insurance", amount: 400, percentage: 12.5 },
  { category: "Other", amount: 150, percentage: 4.7 },
]);

const paymentMethods = ref([
  { method: "Credit Card", transactions: 89, amount: 7120, percentage: 57.0 },
  { method: "PayPal", transactions: 45, amount: 3600, percentage: 28.8 },
  { method: "Bank Transfer", transactions: 15, amount: 1200, percentage: 9.6 },
  { method: "Cash", transactions: 7, amount: 580, percentage: 4.6 },
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
          <h2 class="text-h4 font-weight-bold">Finance Statistics</h2>
          <p class="text-body-2 text-medium-emphasis mt-1">
            Analyze your financial performance and trends
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

    <!-- Key Financial Metrics -->
    <VRow class="mb-6">
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
                  {{ formatCurrency(financialStats.totalRevenue) }}
                </h2>
                <p class="text-caption text-success">+15.2% vs last period</p>
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
                  Net Profit
                </h3>
                <h2 class="text-h4 font-weight-bold text-primary">
                  {{ formatCurrency(financialStats.netProfit) }}
                </h2>
                <p class="text-caption text-primary">+12.8% vs last period</p>
              </div>
              <VAvatar color="primary" variant="tonal" size="48">
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
                  Profit Margin
                </h3>
                <h2 class="text-h4 font-weight-bold text-info">
                  {{ formatPercentage(financialStats.profitMargin) }}
                </h2>
                <p class="text-caption text-info">+2.1% vs last period</p>
              </div>
              <VAvatar color="info" variant="tonal" size="48">
                <VIcon size="24" icon="tabler-percentage" />
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
                  Avg Transaction
                </h3>
                <h2 class="text-h4 font-weight-bold text-warning">
                  {{ formatCurrency(financialStats.averageTransactionValue) }}
                </h2>
                <p class="text-caption text-warning">+3.4% vs last period</p>
              </div>
              <VAvatar color="warning" variant="tonal" size="48">
                <VIcon size="24" icon="tabler-receipt" />
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
            <h3 class="text-h5 font-weight-bold">Revenue vs Expenses Trend</h3>
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
                <VIcon size="48" icon="tabler-chart-area" color="grey" />
                <p class="text-body-1 text-medium-emphasis mt-2">
                  Area Chart Component
                </p>
                <p class="text-caption text-medium-emphasis">
                  Revenue vs expenses over time would be displayed here
                </p>
              </div>
            </div>
          </VCardText>
        </VCard>
      </VCol>

      <VCol cols="12" lg="4">
        <VCard>
          <VCardTitle class="pa-6">
            <h3 class="text-h5 font-weight-bold">Expense Breakdown</h3>
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
                  Expense breakdown would be displayed here
                </p>
              </div>
            </div>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>

    <!-- Additional Financial Data -->
    <VRow class="mb-6">
      <VCol cols="12" md="6">
        <VCard>
          <VCardTitle class="pa-6">
            <h3 class="text-h5 font-weight-bold">Expense Breakdown</h3>
          </VCardTitle>
          <VCardText class="pa-6 pt-0">
            <VList>
              <VListItem
                v-for="expense in expenseBreakdown"
                :key="expense.category"
                class="px-0"
              >
                <VListItemTitle class="font-weight-medium">
                  {{ expense.category }}
                </VListItemTitle>
                <VListItemSubtitle class="text-caption">
                  {{ formatPercentage(expense.percentage) }} of total expenses
                </VListItemSubtitle>

                <template #append>
                  <div class="text-right">
                    <div class="font-weight-medium">
                      {{ formatCurrency(expense.amount) }}
                    </div>
                    <div class="text-caption text-medium-emphasis">
                      {{ formatPercentage(expense.percentage) }}
                    </div>
                  </div>
                </template>
              </VListItem>
            </VList>
          </VCardText>
        </VCard>
      </VCol>

      <VCol cols="12" md="6">
        <VCard>
          <VCardTitle class="pa-6">
            <h3 class="text-h5 font-weight-bold">Payment Methods</h3>
          </VCardTitle>
          <VCardText class="pa-6 pt-0">
            <VList>
              <VListItem
                v-for="payment in paymentMethods"
                :key="payment.method"
                class="px-0"
              >
                <VListItemTitle class="font-weight-medium">
                  {{ payment.method }}
                </VListItemTitle>
                <VListItemSubtitle class="text-caption">
                  {{ payment.transactions }} transactions
                </VListItemSubtitle>

                <template #append>
                  <div class="text-right">
                    <div class="font-weight-medium">
                      {{ formatCurrency(payment.amount) }}
                    </div>
                    <div class="text-caption text-medium-emphasis">
                      {{ formatPercentage(payment.percentage) }}
                    </div>
                  </div>
                </template>
              </VListItem>
            </VList>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>

    <!-- Financial Summary -->
    <VCard>
      <VCardTitle class="pa-6">
        <h3 class="text-h5 font-weight-bold">Financial Summary</h3>
      </VCardTitle>
      <VCardText class="pa-6 pt-0">
        <VRow>
          <VCol cols="12" md="3">
            <div class="text-center pa-4">
              <h4 class="text-h6 font-weight-medium text-medium-emphasis mb-2">
                Total Revenue
              </h4>
              <h3 class="text-h4 font-weight-bold text-success">
                {{ formatCurrency(financialStats.totalRevenue) }}
              </h3>
            </div>
          </VCol>
          <VCol cols="12" md="3">
            <div class="text-center pa-4">
              <h4 class="text-h6 font-weight-medium text-medium-emphasis mb-2">
                Total Expenses
              </h4>
              <h3 class="text-h4 font-weight-bold text-error">
                {{ formatCurrency(financialStats.totalExpenses) }}
              </h3>
            </div>
          </VCol>
          <VCol cols="12" md="3">
            <div class="text-center pa-4">
              <h4 class="text-h6 font-weight-medium text-medium-emphasis mb-2">
                Net Profit
              </h4>
              <h3 class="text-h4 font-weight-bold text-primary">
                {{ formatCurrency(financialStats.netProfit) }}
              </h3>
            </div>
          </VCol>
          <VCol cols="12" md="3">
            <div class="text-center pa-4">
              <h4 class="text-h6 font-weight-medium text-medium-emphasis mb-2">
                Profit Margin
              </h4>
              <h3 class="text-h4 font-weight-bold text-info">
                {{ formatPercentage(financialStats.profitMargin) }}
              </h3>
            </div>
          </VCol>
        </VRow>

        <VDivider class="my-6" />

        <VRow>
          <VCol cols="12" md="4">
            <div class="text-center pa-4">
              <h4 class="text-h6 font-weight-medium text-medium-emphasis mb-2">
                Pending Payments
              </h4>
              <h3 class="text-h4 font-weight-bold text-warning">
                {{ formatCurrency(financialStats.pendingPayments) }}
              </h3>
            </div>
          </VCol>
          <VCol cols="12" md="4">
            <div class="text-center pa-4">
              <h4 class="text-h6 font-weight-medium text-medium-emphasis mb-2">
                Total Transactions
              </h4>
              <h3 class="text-h4 font-weight-bold text-info">
                {{ financialStats.totalTransactions }}
              </h3>
            </div>
          </VCol>
          <VCol cols="12" md="4">
            <div class="text-center pa-4">
              <h4 class="text-h6 font-weight-medium text-medium-emphasis mb-2">
                Refunded Amount
              </h4>
              <h3 class="text-h4 font-weight-bold text-error">
                {{ formatCurrency(financialStats.refundedAmount) }}
              </h3>
            </div>
          </VCol>
        </VRow>
      </VCardText>
    </VCard>

    <!-- Quick Actions -->
    <VRow class="mt-6">
      <VCol cols="12">
        <VCard>
          <VCardTitle class="pa-6">
            <h3 class="text-h5 font-weight-bold">Quick Actions</h3>
          </VCardTitle>
          <VCardText class="pa-6 pt-0">
            <VRow>
              <VCol cols="12" sm="6" md="3">
                <VBtn
                  block
                  variant="outlined"
                  prepend-icon="tabler-download"
                  class="mb-3"
                >
                  Export Report
                </VBtn>
              </VCol>
              <VCol cols="12" sm="6" md="3">
                <VBtn
                  block
                  variant="outlined"
                  prepend-icon="tabler-chart-bar"
                  class="mb-3"
                >
                  Detailed Analytics
                </VBtn>
              </VCol>
              <VCol cols="12" sm="6" md="3">
                <VBtn
                  block
                  variant="outlined"
                  prepend-icon="tabler-mail"
                  class="mb-3"
                >
                  Email Report
                </VBtn>
              </VCol>
              <VCol cols="12" sm="6" md="3">
                <VBtn
                  block
                  variant="outlined"
                  prepend-icon="tabler-settings"
                  class="mb-3"
                >
                  Financial Settings
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
.v-card {
  border-radius: 8px;
}
</style>
