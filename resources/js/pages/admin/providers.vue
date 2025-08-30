<template>
  <VCard>
    <VCardTitle class="d-flex align-center justify-space-between">
      <span>Provider Management</span>
      <VBtn
        color="primary"
        prepend-icon="tabler-refresh"
        @click="loadProviders"
        :loading="loading"
      >
        Refresh
      </VBtn>
    </VCardTitle>

    <VCardText>
      <!-- Search and Filters -->
      <VRow class="mb-4">
        <VCol cols="12" md="6">
          <VTextField
            v-model="searchQuery"
            label="Search Providers"
            placeholder="Search by name, specialization, or country..."
            prepend-inner-icon="tabler-search"
            variant="outlined"
            density="compact"
            @input="debouncedSearch"
          />
        </VCol>
        <VCol cols="12" md="3">
          <VSelect
            v-model="providerTypeFilter"
            label="Filter by type"
            :items="providerTypeOptions"
            variant="outlined"
            density="compact"
            @update:model-value="loadProviders"
          />
        </VCol>
        <VCol cols="12" md="3">
          <VSelect
            v-model="statusFilter"
            label="Filter by status"
            :items="statusOptions"
            variant="outlined"
            density="compact"
            @update:model-value="loadProviders"
          />
        </VCol>
      </VRow>

      <!-- Providers Table -->
      <VDataTable
        :headers="headers"
        :items="providers"
        :loading="loading"
        :search="searchQuery"
        class="text-no-wrap"
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
            :color="getStatusColor(item.status)"
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

        <!-- Created At Column -->
        <template #item.created_at="{ item }">
          {{ formatDate(item.created_at) }}
        </template>

        <!-- Actions Column -->
        <template #item.actions="{ item }">
          <VBtn
            icon
            variant="text"
            size="small"
            color="primary"
            @click="viewProvider(item)"
          >
            <VIcon icon="tabler-eye" />
          </VBtn>
          <VBtn
            icon
            variant="text"
            size="small"
            color="warning"
            @click="editProvider(item)"
          >
            <VIcon icon="tabler-edit" />
          </VBtn>
          <VBtn
            icon
            variant="text"
            size="small"
            color="error"
            @click="deleteProvider(item)"
          >
            <VIcon icon="tabler-trash" />
          </VBtn>
        </template>
      </VDataTable>
    </VCardText>

    <!-- Provider Details Dialog -->
    <VDialog v-model="showProviderDialog" max-width="600">
      <VCard>
        <VCardTitle>Provider Details</VCardTitle>
        <VCardText v-if="selectedProvider">
          <VRow>
            <VCol cols="12" md="6">
              <strong>Name:</strong> {{ selectedProvider.provider_name }}
            </VCol>
            <VCol cols="12" md="6">
              <strong>Type:</strong>
              {{
                selectedProvider.provider_type === "individual"
                  ? "Individual"
                  : "Company"
              }}
            </VCol>
            <VCol cols="12" md="6">
              <strong>Specialization:</strong>
              {{ selectedProvider.activity_specialization }}
            </VCol>
            <VCol cols="12" md="6">
              <strong>Country:</strong>
              {{ selectedProvider.country_of_operation }}
            </VCol>
            <VCol cols="12" md="6">
              <strong>Experience:</strong>
              {{ selectedProvider.years_of_experience }}
            </VCol>
            <VCol cols="12" md="6">
              <strong>Status:</strong>
              <VChip
                :color="getStatusColor(selectedProvider.status)"
                size="small"
                class="ml-2"
              >
                {{ selectedProvider.status }}
              </VChip>
            </VCol>
            <VCol cols="12">
              <strong>Created:</strong>
              {{ formatDate(selectedProvider.created_at) }}
            </VCol>
          </VRow>
        </VCardText>
        <VCardActions>
          <VSpacer />
          <VBtn color="primary" @click="showProviderDialog = false">
            Close
          </VBtn>
        </VCardActions>
      </VCard>
    </VDialog>
  </VCard>
</template>

<script setup>
import { onMounted, ref } from "vue";

definePage({
  meta: {
    layout: "default",
    action: "read",
    subject: "AdminProviders",
  },
});
const loading = ref(false);
const providers = ref([]);
const searchQuery = ref("");
const providerTypeFilter = ref("all");
const statusFilter = ref("all");
const showProviderDialog = ref(false);
const selectedProvider = ref(null);

const headers = [
  { title: "PROVIDER", key: "provider", sortable: true },
  { title: "PROVIDER ID", key: "provider_id", sortable: true },
  { title: "STATUS", key: "status", sortable: true },
  { title: "TOTAL LISTING", key: "total_listings", sortable: true },
  { title: "TOTAL BOOKING", key: "total_bookings", sortable: true },
  { title: "ACTIONS", key: "actions", sortable: false },
];

const providerTypeOptions = [
  { title: "All Types", value: "all" },
  { title: "Individual", value: "individual" },
  { title: "Company", value: "company" },
];

const statusOptions = [
  { title: "All Status", value: "all" },
  { title: "Live", value: "live" },
  { title: "Denied", value: "denied" },
];

onMounted(() => {
  loadProviders();
});

const loadProviders = async () => {
  loading.value = true;
  try {
    const params = {
      search: searchQuery.value,
      provider_type: providerTypeFilter.value,
      status: statusFilter.value,
    };

    const response = await $api("/admin/providers", {
      method: "GET",
      params: params,
    });
    providers.value = response.data || [];
  } catch (error) {
    console.error("Error loading providers:", error);
  } finally {
    loading.value = false;
  }
};

const getStatusColor = (status) => {
  switch (status?.toLowerCase()) {
    case "live":
      return "success";
    case "denied":
      return "error";
    default:
      return "default";
  }
};

const formatDate = (date) => {
  if (!date) return "-";
  return new Date(date).toLocaleDateString();
};

const viewProvider = (provider) => {
  selectedProvider.value = provider;
  showProviderDialog.value = true;
};

const editProvider = (provider) => {
  // TODO: Implement edit functionality
  console.log("Edit provider:", provider);
};

const deleteProvider = async (provider) => {
  // TODO: Implement delete functionality
  console.log("Delete provider:", provider);
};

// Debounce search
let searchTimeout;
const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    loadProviders();
  }, 300);
};
</script>

<style scoped>
.v-data-table {
  border-radius: 8px;
}

.v-data-table th {
  font-weight: 600;
  color: #6c757d;
}

.v-data-table td {
  padding: 12px 16px;
}
</style>
