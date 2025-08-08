<script setup>
definePage({
  meta: {
    layout: "default",
    action: "read",
    subject: "Finance",
  },
});

const walletBalance = ref(2847.5);
const pendingAmount = ref(450.0);
const totalEarnings = ref(12500.0);

const transactions = ref([
  {
    id: 1,
    type: "credit",
    amount: 150.0,
    description: "Mountain Hiking Adventure - Booking #1234",
    date: "2024-02-15",
    status: "completed",
    participantName: "John Doe",
  },
  {
    id: 2,
    type: "credit",
    amount: 45.0,
    description: "City Walking Tour - Booking #1235",
    date: "2024-02-14",
    status: "completed",
    participantName: "Jane Smith",
  },
  {
    id: 3,
    type: "debit",
    amount: -25.0,
    description: "Platform Fee - February 2024",
    date: "2024-02-13",
    status: "completed",
    participantName: "Platform",
  },
  {
    id: 4,
    type: "credit",
    amount: 240.0,
    description: "Wine Tasting Experience - Booking #1236",
    date: "2024-02-12",
    status: "pending",
    participantName: "Mike Johnson",
  },
  {
    id: 5,
    type: "credit",
    amount: 80.0,
    description: "Beach Yoga Session - Booking #1237",
    date: "2024-02-11",
    status: "completed",
    participantName: "Sarah Wilson",
  },
]);

const recentTransactions = computed(() => {
  return transactions.value.slice(0, 5);
});

const getTransactionIcon = (type) => {
  return type === "credit" ? "tabler-arrow-down" : "tabler-arrow-up";
};

const getTransactionColor = (type) => {
  return type === "credit" ? "success" : "error";
};

const getStatusColor = (status) => {
  switch (status) {
    case "completed":
      return "success";
    case "pending":
      return "warning";
    case "failed":
      return "error";
    default:
      return "default";
  }
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat("de-DE", {
    style: "currency",
    currency: "EUR",
  }).format(amount);
};
</script>

<template>
  <div>
    <!-- Wallet Overview Cards -->
    <VRow class="mb-6">
      <VCol cols="12" md="4">
        <VCard>
          <VCardText class="pa-6">
            <div class="d-flex align-center justify-space-between">
              <div>
                <h3
                  class="text-h6 font-weight-medium text-medium-emphasis mb-2"
                >
                  Available Balance
                </h3>
                <h2 class="text-h4 font-weight-bold text-success">
                  {{ formatCurrency(walletBalance) }}
                </h2>
              </div>
              <VAvatar color="success" variant="tonal" size="56">
                <VIcon size="28" icon="tabler-wallet" />
              </VAvatar>
            </div>
          </VCardText>
        </VCard>
      </VCol>

      <VCol cols="12" md="4">
        <VCard>
          <VCardText class="pa-6">
            <div class="d-flex align-center justify-space-between">
              <div>
                <h3
                  class="text-h6 font-weight-medium text-medium-emphasis mb-2"
                >
                  Pending Amount
                </h3>
                <h2 class="text-h4 font-weight-bold text-warning">
                  {{ formatCurrency(pendingAmount) }}
                </h2>
              </div>
              <VAvatar color="warning" variant="tonal" size="56">
                <VIcon size="28" icon="tabler-clock" />
              </VAvatar>
            </div>
          </VCardText>
        </VCard>
      </VCol>

      <VCol cols="12" md="4">
        <VCard>
          <VCardText class="pa-6">
            <div class="d-flex align-center justify-space-between">
              <div>
                <h3
                  class="text-h6 font-weight-medium text-medium-emphasis mb-2"
                >
                  Total Earnings
                </h3>
                <h2 class="text-h4 font-weight-bold text-primary">
                  {{ formatCurrency(totalEarnings) }}
                </h2>
              </div>
              <VAvatar color="primary" variant="tonal" size="56">
                <VIcon size="28" icon="tabler-chart-line" />
              </VAvatar>
            </div>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>

    <!-- Recent Transactions -->
    <VCard>
      <VCardTitle class="d-flex align-center justify-space-between pa-6">
        <div>
          <h2 class="text-h4 font-weight-bold">Recent Transactions</h2>
          <p class="text-body-2 text-medium-emphasis mt-1">
            Your latest financial activities
          </p>
        </div>
        <VBtn variant="outlined" prepend-icon="tabler-download"> Export </VBtn>
      </VCardTitle>

      <VDivider />

      <VCardText class="pa-0">
        <VList>
          <VListItem
            v-for="transaction in recentTransactions"
            :key="transaction.id"
            class="px-6 py-4"
          >
            <template #prepend>
              <VAvatar
                :color="getTransactionColor(transaction.type)"
                variant="tonal"
                size="40"
              >
                <VIcon :icon="getTransactionIcon(transaction.type)" size="20" />
              </VAvatar>
            </template>

            <VListItemTitle class="font-weight-medium">
              {{ transaction.description }}
            </VListItemTitle>
            <VListItemSubtitle class="text-caption">
              {{ transaction.participantName }} • {{ transaction.date }}
            </VListItemSubtitle>

            <template #append>
              <div class="d-flex align-center gap-3">
                <div class="text-right">
                  <div
                    class="font-weight-medium"
                    :class="
                      transaction.type === 'credit'
                        ? 'text-success'
                        : 'text-error'
                    "
                  >
                    {{ transaction.type === "credit" ? "+" : ""
                    }}{{ formatCurrency(transaction.amount) }}
                  </div>
                  <VChip
                    :color="getStatusColor(transaction.status)"
                    size="x-small"
                    class="mt-1"
                  >
                    {{ transaction.status }}
                  </VChip>
                </div>
              </div>
            </template>
          </VListItem>
        </VList>
      </VCardText>
    </VCard>

    <!-- Quick Actions -->
    <VRow class="mt-6">
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
                  prepend-icon="tabler-credit-card"
                  class="mb-3"
                >
                  Withdraw
                </VBtn>
              </VCol>
              <VCol cols="6">
                <VBtn
                  block
                  variant="outlined"
                  prepend-icon="tabler-receipt"
                  class="mb-3"
                >
                  View Reports
                </VBtn>
              </VCol>
              <VCol cols="6">
                <VBtn
                  block
                  variant="outlined"
                  prepend-icon="tabler-settings"
                  class="mb-3"
                >
                  Payment Settings
                </VBtn>
              </VCol>
              <VCol cols="6">
                <VBtn
                  block
                  variant="outlined"
                  prepend-icon="tabler-help"
                  class="mb-3"
                >
                  Get Help
                </VBtn>
              </VCol>
            </VRow>
          </VCardText>
        </VCard>
      </VCol>

      <VCol cols="12" md="6">
        <VCard>
          <VCardTitle class="pa-6">
            <h3 class="text-h5 font-weight-bold">Financial Summary</h3>
          </VCardTitle>
          <VCardText class="pa-6 pt-0">
            <div class="d-flex justify-space-between align-center mb-4">
              <span class="text-body-1">This Month</span>
              <span class="font-weight-medium text-success">+€1,250.00</span>
            </div>
            <div class="d-flex justify-space-between align-center mb-4">
              <span class="text-body-1">Last Month</span>
              <span class="font-weight-medium">€980.00</span>
            </div>
            <div class="d-flex justify-space-between align-center mb-4">
              <span class="text-body-1">Platform Fees</span>
              <span class="font-weight-medium text-error">-€125.00</span>
            </div>
            <VDivider class="my-4" />
            <div class="d-flex justify-space-between align-center">
              <span class="text-h6 font-weight-bold">Net Earnings</span>
              <span class="text-h6 font-weight-bold text-primary"
                >€2,105.50</span
              >
            </div>
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
