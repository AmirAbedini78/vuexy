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
          <VSelect
            :model-value="item.status"
            :items="statusChoices"
            item-title="title"
            item-value="value"
            variant="outlined"
            density="compact"
            hide-details
            style="min-width: 150px"
            @update:model-value="(val) => changeStatus(item, val)"
          >
            <template #selection="{ item: sel }">
              <VChip
                :color="getStatusColor(sel?.value)"
                size="small"
                class="font-weight-medium"
              >
                {{ sel?.title }}
              </VChip>
            </template>
            <template #item="{ item: opt }">
              <div class="d-flex align-center gap-2">
                <VChip :color="getStatusColor(opt?.value)" size="x-small" />
                <span>{{ opt?.title }}</span>
              </div>
            </template>
          </VSelect>
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
          <div class="d-flex gap-1">
            <!-- Eye icon for viewing provider details -->
            <VBtn
              icon
              variant="text"
              size="small"
              color="primary"
              @click="viewProvider(item)"
              title="View Provider Details"
            >
              <VIcon icon="tabler-eye" />
            </VBtn>

            <!-- Three dots menu for edit/delete options -->
            <VMenu>
              <template #activator="{ props }">
                <VBtn
                  icon
                  variant="text"
                  size="small"
                  color="default"
                  v-bind="props"
                  title="More Options"
                >
                  <VIcon icon="tabler-dots-vertical" />
                </VBtn>
              </template>

              <VList>
                <VListItem @click="editProvider(item)">
                  <template #prepend>
                    <VIcon icon="tabler-edit" size="18" />
                  </template>
                  <VListItemTitle>Edit Provider</VListItemTitle>
                </VListItem>

                <VDivider />

                <VListItem @click="deleteProvider(item)" color="error">
                  <template #prepend>
                    <VIcon icon="tabler-trash" size="18" color="error" />
                  </template>
                  <VListItemTitle class="text-error"
                    >Delete Provider</VListItemTitle
                  >
                </VListItem>
              </VList>
            </VMenu>
          </div>
        </template>
      </VDataTable>
    </VCardText>

    <!-- Provider View Dialog -->
    <VDialog v-model="showProviderViewDialog" max-width="800" persistent>
      <VCard>
        <VCardTitle class="d-flex align-center justify-space-between">
          <span>Provider Details</span>
          <VBtn
            icon
            variant="text"
            size="small"
            @click="showProviderViewDialog = false"
          >
            <VIcon icon="tabler-x" />
          </VBtn>
        </VCardTitle>

        <VCardText v-if="selectedProvider">
          <VRow>
            <!-- Basic Information -->
            <VCol cols="12">
              <h6 class="text-h6 font-weight-medium mb-3">Basic Information</h6>
            </VCol>

            <VCol cols="12" md="6">
              <strong>Name:</strong> {{ selectedProvider.provider_name }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>Type:</strong>
              <VChip
                :color="
                  selectedProvider.provider_type === 'individual'
                    ? 'primary'
                    : 'secondary'
                "
                size="small"
                class="ml-2"
              >
                {{
                  selectedProvider.provider_type === "individual"
                    ? "Individual"
                    : "Company"
                }}
              </VChip>
            </VCol>

            <VCol cols="12" md="6">
              <strong>Specialization:</strong>
              {{ selectedProvider.activity_specialization || "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>Country:</strong>
              {{
                selectedProvider.country_of_operation ||
                selectedProvider.country ||
                "N/A"
              }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>Experience:</strong>
              {{
                selectedProvider.years_of_experience ||
                selectedProvider.business_type ||
                "N/A"
              }}
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

            <VCol cols="12" md="6">
              <strong>Want to be listed:</strong>
              {{ selectedProvider.want_to_be_listed || "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>Created:</strong>
              {{ formatDate(selectedProvider.created_at) }}
            </VCol>

            <!-- Additional fields based on provider type -->
            <template v-if="selectedProvider.provider_type === 'individual'">
              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3 mt-4">
                  Personal Information
                </h6>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Nationality:</strong>
                {{ selectedProvider.nationality || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Address:</strong>
                {{ selectedProvider.address1 || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>City:</strong>
                {{ selectedProvider.city || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>State:</strong>
                {{ selectedProvider.state || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Postal Code:</strong>
                {{ selectedProvider.postal_code || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Date of Birth:</strong>
                {{
                  selectedProvider.dob
                    ? formatDate(selectedProvider.dob)
                    : "N/A"
                }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Languages:</strong>
                {{
                  Array.isArray(selectedProvider.languages)
                    ? selectedProvider.languages.join(", ")
                    : selectedProvider.languages || "N/A"
                }}
              </VCol>

              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3 mt-4">
                  Business Details
                </h6>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Years of Experience:</strong>
                {{ selectedProvider.years_of_experience || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Country of Operation:</strong>
                {{ selectedProvider.country_of_operation || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Emergency Contact:</strong>
                {{ selectedProvider.emergency_contact_name || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Emergency Phone:</strong>
                {{ selectedProvider.emergency_contact_phone || "N/A" }}
              </VCol>

              <VCol cols="12">
                <strong>Short Bio:</strong>
                {{ selectedProvider.short_bio || "N/A" }}
              </VCol>

              <!-- Social Links -->
              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3 mt-4">
                  Social Links
                </h6>
              </VCol>
              <VCol cols="12" md="6"
                ><strong>Twitter:</strong>
                {{
                  selectedProvider.social_media_links?.twitter ||
                  selectedProvider.twitter ||
                  "N/A"
                }}</VCol
              >
              <VCol cols="12" md="6"
                ><strong>Facebook:</strong>
                {{
                  selectedProvider.social_media_links?.facebook ||
                  selectedProvider.facebook ||
                  "N/A"
                }}</VCol
              >
              <VCol cols="12" md="6"
                ><strong>LinkedIn:</strong>
                {{
                  selectedProvider.social_media_links?.linkedIn ||
                  selectedProvider.linkedIn ||
                  "N/A"
                }}</VCol
              >
              <VCol cols="12" md="6"
                ><strong>Instagram:</strong>
                {{
                  selectedProvider.social_media_links?.instagram ||
                  selectedProvider.instagram ||
                  "N/A"
                }}</VCol
              >

              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3 mt-4">
                  Terms & Conditions
                </h6>
              </VCol>

              <VCol cols="12">
                <strong>Terms Accepted:</strong>
                <VChip
                  :color="selectedProvider.terms_accepted ? 'success' : 'error'"
                  size="small"
                  class="ml-2"
                >
                  {{ selectedProvider.terms_accepted ? "Yes" : "No" }}
                </VChip>
              </VCol>
            </template>

            <template v-else>
              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3 mt-4">
                  Company Information
                </h6>
              </VCol>

              <VCol cols="12" md="6">
                <strong>VAT ID:</strong>
                {{ selectedProvider.vat_id || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Contact Person:</strong>
                {{ selectedProvider.contact_person || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Country of Registration:</strong>
                {{ selectedProvider.country_of_registration || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Address:</strong>
                {{ selectedProvider.address1 || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>City:</strong>
                {{ selectedProvider.city || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>State:</strong>
                {{ selectedProvider.state || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Postal Code:</strong>
                {{ selectedProvider.postal_code || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Business Type:</strong>
                {{ selectedProvider.business_type || "N/A" }}
              </VCol>

              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3 mt-4">
                  Business Details
                </h6>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Company Website:</strong>
                {{ selectedProvider.company_website || "N/A" }}
              </VCol>

              <VCol cols="12">
                <strong>Short Bio:</strong>
                {{ selectedProvider.short_bio || "N/A" }}
              </VCol>

              <!-- Social Links -->
              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3 mt-4">
                  Social Links
                </h6>
              </VCol>
              <VCol cols="12" md="6"
                ><strong>Twitter:</strong>
                {{
                  selectedProvider.social_media_links?.twitter ||
                  selectedProvider.twitter ||
                  "N/A"
                }}</VCol
              >
              <VCol cols="12" md="6"
                ><strong>Facebook:</strong>
                {{
                  selectedProvider.social_media_links?.facebook ||
                  selectedProvider.facebook ||
                  "N/A"
                }}</VCol
              >
              <VCol cols="12" md="6"
                ><strong>LinkedIn:</strong>
                {{
                  selectedProvider.social_media_links?.linkedIn ||
                  selectedProvider.linkedIn ||
                  "N/A"
                }}</VCol
              >
              <VCol cols="12" md="6"
                ><strong>Instagram:</strong>
                {{
                  selectedProvider.social_media_links?.instagram ||
                  selectedProvider.instagram ||
                  "N/A"
                }}</VCol
              >

              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3 mt-4">
                  Terms & Conditions
                </h6>
              </VCol>

              <VCol cols="12">
                <strong>Terms Accepted:</strong>
                <VChip
                  :color="selectedProvider.terms_accepted ? 'success' : 'error'"
                  size="small"
                  class="ml-2"
                >
                  {{ selectedProvider.terms_accepted ? "Yes" : "No" }}
                </VChip>
              </VCol>
            </template>
          </VRow>
        </VCardText>

        <VCardActions>
          <VSpacer />
          <VBtn color="primary" @click="showProviderViewDialog = false">
            Close
          </VBtn>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Provider Edit Dialog -->
    <VDialog v-model="showProviderEditDialog" max-width="1200" persistent>
      <VCard>
        <VCardTitle class="d-flex align-center justify-space-between">
          <span>Edit Provider: {{ selectedProvider?.provider_name }}</span>
          <VBtn
            icon
            variant="text"
            size="small"
            @click="showProviderEditDialog = false"
          >
            <VIcon icon="tabler-x" />
          </VBtn>
        </VCardTitle>

        <VCardText v-if="selectedProvider">
          <!-- Provider Edit Wizard -->
          <ProviderEditWizard
            :provider="selectedProvider"
            @close="showProviderEditDialog = false"
            @updated="handleProviderUpdated"
          />
        </VCardText>
      </VCard>
    </VDialog>

    <!-- Delete Confirmation Dialog -->
    <VDialog v-model="showDeleteDialog" max-width="400" persistent>
      <VCard>
        <VCardTitle class="text-center">
          <VIcon
            icon="tabler-alert-triangle"
            color="error"
            size="48"
            class="mb-3"
          />
          <div>Delete Provider</div>
        </VCardTitle>

        <VCardText class="text-center">
          Are you sure you want to delete
          <strong>{{ selectedProvider?.provider_name }}</strong
          >?
          <br />
          This action cannot be undone.
        </VCardText>

        <VCardActions class="justify-center">
          <VBtn variant="outlined" @click="showDeleteDialog = false">
            Cancel
          </VBtn>
          <VBtn
            color="error"
            variant="elevated"
            @click="confirmDeleteProvider"
            :loading="deleteLoading"
          >
            Delete
          </VBtn>
        </VCardActions>
      </VCard>
    </VDialog>
  </VCard>
</template>

<script setup>
import ProviderEditWizard from "@/components/admin/ProviderEditWizard.vue";
import { $api } from "@/utils/api";
import { onMounted, ref } from "vue";

definePage({
  meta: {
    layout: "default",
    action: "read",
    subject: "AdminProviders",
  },
});

const loading = ref(false);
const deleteLoading = ref(false);
const providers = ref([]);
const searchQuery = ref("");
const providerTypeFilter = ref("all");
const statusFilter = ref("all");
const showProviderViewDialog = ref(false);
const showProviderEditDialog = ref(false);
const showDeleteDialog = ref(false);
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
  { title: "Active", value: "active" },
  { title: "Approved", value: "approved" },
  { title: "Rejected", value: "rejected" },
];

// For inline status select
const statusChoices = [
  { title: "Active", value: "active" },
  { title: "Approved", value: "approved" },
  { title: "Rejected", value: "rejected" },
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
      query: params,
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
    case "active":
      return "success";
    case "approved":
      return "warning";
    case "rejected":
      return "error";
    default:
      return "default";
  }
};

const formatDate = (date) => {
  if (!date) return "-";
  return new Date(date).toLocaleDateString();
};

const changeStatus = async (provider, status) => {
  try {
    const res = await $api(
      `/admin/providers/${provider.id}/${provider.provider_type}/status`,
      {
        method: "PUT",
        body: { status },
      }
    );

    if (res?.success || res?.message) {
      provider.status = status;
      // Optionally show a toast here
    }
  } catch (e) {
    console.error("Failed to change status", e);
  }
};

const viewProvider = (provider) => {
  selectedProvider.value = provider;
  showProviderViewDialog.value = true;
};

const editProvider = (provider) => {
  selectedProvider.value = provider;
  showProviderEditDialog.value = true;
};

const deleteProvider = (provider) => {
  selectedProvider.value = provider;
  showDeleteDialog.value = true;
};

const confirmDeleteProvider = async () => {
  if (!selectedProvider.value) return;

  deleteLoading.value = true;
  try {
    // Call API to delete provider
    const response = await $api(
      `/admin/providers/${selectedProvider.value.id}/${selectedProvider.value.provider_type}`,
      {
        method: "DELETE",
      }
    );

    if (response.success) {
      // Remove from local list
      const index = providers.value.findIndex(
        (p) => p.id === selectedProvider.value.id
      );
      if (index > -1) {
        providers.value.splice(index, 1);
      }

      showDeleteDialog.value = false;
      selectedProvider.value = null;

      // Show success message
      // You can use your preferred notification system here
      console.log("Provider deleted successfully");
    } else {
      console.error("Failed to delete provider:", response.message);
      // You can show an error message here
    }
  } catch (error) {
    console.error("Error deleting provider:", error);
    // You can show an error message here
  } finally {
    deleteLoading.value = false;
  }
};

const handleProviderUpdated = (updatedProvider) => {
  // Update the provider in the local list
  const index = providers.value.findIndex((p) => p.id === updatedProvider.id);
  if (index > -1) {
    providers.value[index] = { ...providers.value[index], ...updatedProvider };
  }

  showProviderEditDialog.value = false;
  selectedProvider.value = null;

  // Show success message
  // You can use your preferred notification system here
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
