<template>
  <VCard>
    <VCardTitle class="d-flex align-center justify-space-between">
      <span>Listing Management</span>
      <VBtn
        color="primary"
        prepend-icon="tabler-refresh"
        @click="loadListings"
        :loading="loading"
      >
        Refresh
      </VBtn>
    </VCardTitle>

    <VCardText>
      <!-- Search -->
      <VRow class="mb-4">
        <VCol cols="12" md="6">
          <VTextField
            v-model="searchQuery"
            label="Search listings"
            placeholder="Search by title..."
            prepend-inner-icon="tabler-search"
            variant="outlined"
            density="compact"
            @input="debouncedSearch"
          />
        </VCol>
      </VRow>

      <!-- Listings Table -->
      <VDataTable
        :headers="headers"
        :items="listings"
        :loading="loading"
        :search="searchQuery"
        class="text-no-wrap"
      >
        <!-- Title Column -->
        <template #item.listing_title="{ item }">
          <div>
            <div class="font-weight-medium">
              {{ item.listing_title || "N/A" }}
            </div>
            <div class="text-caption text-medium-emphasis">
              {{ item.subtitle || "No subtitle" }}
            </div>
          </div>
        </template>

        <!-- Type Column -->
        <template #item.listing_type="{ item }">
          <VChip
            :color="getListingTypeColor(item.listing_type)"
            size="small"
            class="font-weight-medium"
          >
            {{ formatListingType(item.listing_type) }}
          </VChip>
        </template>

        <!-- Status Column -->
        <template #item.status="{ item }">
          <VChip
            :color="getListingStatusColor(item.status)"
            size="small"
            class="font-weight-medium"
          >
            {{ item.status || "Draft" }}
          </VChip>
        </template>

        <!-- Price Column -->
        <template #item.price="{ item }">
          <span class="font-weight-medium">
            €{{ formatCurrency(item.price || 0) }}
          </span>
        </template>

        <!-- Capacity Column -->
        <template #item.capacity="{ item }">
          <span class="font-weight-medium">
            {{ item.min_capacity || 0 }} - {{ item.max_capacity || 0 }}
          </span>
        </template>

        <!-- Provider Column -->
        <template #item.provider="{ item }">
          <div>
            <div class="font-weight-medium">{{ item.user?.name || "N/A" }}</div>
            <div class="text-caption text-medium-emphasis">
              {{ item.user?.email || "N/A" }}
            </div>
          </div>
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
            @click="viewListing(item)"
          >
            <VIcon icon="tabler-eye" />
          </VBtn>
          <VBtn
            icon
            variant="text"
            size="small"
            color="warning"
            @click="editListing(item)"
          >
            <VIcon icon="tabler-edit" />
          </VBtn>
          <VBtn
            icon
            variant="text"
            size="small"
            color="error"
            @click="deleteListing(item)"
          >
            <VIcon icon="tabler-trash" />
          </VBtn>
        </template>
      </VDataTable>

      <!-- Pagination -->
      <div class="d-flex align-center justify-space-between mt-4">
        <div class="text-body-2 text-medium-emphasis">
          Showing {{ paginationInfo.from }} to {{ paginationInfo.to }} of
          {{ paginationInfo.total }} listings
        </div>
        <VPagination
          v-model="currentPage"
          :length="totalPages"
          @update:model-value="loadListings"
        />
      </div>
    </VCardText>
  </VCard>

  <!-- Listing View Dialog -->
  <VDialog v-model="showListingViewDialog" max-width="800" persistent>
    <VCard>
      <VCardTitle class="d-flex align-center justify-space-between">
        <span>Listing Details</span>
        <VBtn
          icon
          variant="text"
          size="small"
          @click="showListingViewDialog = false"
        >
          <VIcon icon="tabler-x" />
        </VBtn>
      </VCardTitle>

      <VCardText v-if="selectedListing">
        <VRow>
          <!-- Basic Information -->
          <VCol cols="12">
            <h6 class="text-h6 font-weight-medium mb-3">Basic Information</h6>
          </VCol>

          <VCol cols="12" md="6">
            <strong>Title:</strong>
            {{ selectedListing.listing_title || "N/A" }}
          </VCol>

          <VCol cols="12" md="6">
            <strong>Subtitle:</strong>
            {{ selectedListing.subtitle || "N/A" }}
          </VCol>

          <VCol cols="12" md="6">
            <strong>Type:</strong>
            <VChip
              :color="getListingTypeColor(selectedListing.listing_type)"
              size="small"
              class="ml-2"
            >
              {{ formatListingType(selectedListing.listing_type) }}
            </VChip>
          </VCol>

          <VCol cols="12" md="6">
            <strong>Status:</strong>
            <VChip
              :color="getListingStatusColor(selectedListing.status)"
              size="small"
              class="ml-2"
            >
              {{ selectedListing.status || "Draft" }}
            </VChip>
          </VCol>

          <VCol cols="12" md="6">
            <strong>Starting Date:</strong>
            {{ selectedListing.starting_date || "N/A" }}
          </VCol>

          <VCol cols="12" md="6">
            <strong>Finishing Date:</strong>
            {{ selectedListing.finishing_date || "N/A" }}
          </VCol>

          <VCol cols="12" md="6">
            <strong>Price:</strong>
            €{{ formatCurrency(selectedListing.price || 0) }}
          </VCol>

          <VCol cols="12" md="6">
            <strong>Capacity:</strong>
            {{ selectedListing.min_capacity || 0 }} -
            {{ selectedListing.max_capacity || 0 }}
          </VCol>

          <VCol cols="12">
            <strong>Description:</strong>
            {{ selectedListing.listing_description || "N/A" }}
          </VCol>

          <!-- Provider Information -->
          <VCol cols="12">
            <h6 class="text-h6 font-weight-medium mb-3 mt-4">
              Provider Information
            </h6>
          </VCol>

          <VCol cols="12" md="6">
            <strong>Provider Name:</strong>
            {{ selectedListing.user?.name || "N/A" }}
          </VCol>

          <VCol cols="12" md="6">
            <strong>Provider Email:</strong>
            {{ selectedListing.user?.email || "N/A" }}
          </VCol>

          <!-- Additional Details -->
          <VCol cols="12">
            <h6 class="text-h6 font-weight-medium mb-3 mt-4">
              Additional Details
            </h6>
          </VCol>

          <VCol cols="12" md="6">
            <strong>Experience Level:</strong>
            {{ selectedListing.user?.experience_level || "N/A" }}
          </VCol>

          <VCol cols="12" md="6">
            <strong>Fitness Level:</strong>
            {{ selectedListing.fitness_level || "N/A" }}
          </VCol>

          <VCol cols="12" md="6">
            <strong>Group Language:</strong>
            {{ selectedListing.group_language || "N/A" }}
          </VCol>

          <VCol cols="12" md="6">
            <strong>Locations:</strong>
            {{ selectedListing.locations || "N/A" }}
          </VCol>

          <VCol cols="12">
            <strong>Activities Included:</strong>
            {{ selectedListing.activities_included || "N/A" }}
          </VCol>

          <VCol cols="12">
            <strong>What's Included:</strong>
            {{ selectedListing.whats_included || "N/A" }}
          </VCol>

          <VCol cols="12">
            <strong>What's Not Included:</strong>
            {{ selectedListing.whats_not_included || "N/A" }}
          </VCol>

          <VCol cols="12">
            <strong>Additional Notes:</strong>
            {{ selectedListing.additional_notes || "N/A" }}
          </VCol>

          <VCol cols="12">
            <strong>Provider's FAQ:</strong>
            {{ selectedListing.providers_faq || "N/A" }}
          </VCol>

          <VCol cols="12">
            <strong>Personal Policies:</strong>
            {{ selectedListing.personal_policies || "N/A" }}
          </VCol>

          <VCol cols="12">
            <strong>Personal Policies Text:</strong>
            {{ selectedListing.personal_policies_text || "N/A" }}
          </VCol>

          <VCol cols="12">
            <strong>Terms Accepted:</strong>
            <VChip
              :color="selectedListing.terms_accepted ? 'success' : 'error'"
              size="small"
              class="ml-2"
            >
              {{ selectedListing.terms_accepted ? "Yes" : "No" }}
            </VChip>
          </VCol>
        </VRow>
      </VCardText>

      <VCardActions>
        <VSpacer />
        <VBtn color="primary" @click="showListingViewDialog = false">
          Close
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>

  <!-- Listing Edit Dialog -->
  <VDialog v-model="showListingEditDialog" max-width="1200" persistent>
    <VCard>
      <VCardTitle class="d-flex align-center justify-space-between">
        <span>Edit Listing: {{ selectedListing?.listing_title }}</span>
        <VBtn
          icon
          variant="text"
          size="small"
          @click="showListingEditDialog = false"
        >
          <VIcon icon="tabler-x" />
        </VBtn>
      </VCardTitle>

      <VCardText v-if="selectedListing">
        <!-- Listing Edit Wizard -->
        <ListingEditWizard
          :listing="selectedListing"
          @close="showListingEditDialog = false"
          @updated="handleListingUpdated"
        />
      </VCardText>
    </VCard>
  </VDialog>
</template>

<script setup>
import ListingEditWizard from "@/components/admin/ListingEditWizard.vue";

definePage({
  meta: {
    layout: "default",
    requiresAuth: true,
    requiresAdmin: true,
  },
});

const loading = ref(false);
const listings = ref([]);
const searchQuery = ref("");
const currentPage = ref(1);
const totalPages = ref(1);
const paginationInfo = ref({
  from: 0,
  to: 0,
  total: 0,
});

// Dialogs
const showListingViewDialog = ref(false);
const showListingEditDialog = ref(false);
const selectedListing = ref(null);

const headers = [
  { title: "Title", key: "listing_title", sortable: true },
  { title: "Type", key: "listing_type", sortable: true },
  { title: "Status", key: "status", sortable: true },
  { title: "Price", key: "price", sortable: true },
  { title: "Capacity", key: "capacity", sortable: true },
  { title: "Provider", key: "provider", sortable: true },
  { title: "Created At", key: "created_at", sortable: true },
  { title: "Actions", key: "actions", sortable: false },
];

// Native debounce function
let searchTimeout = null;
const debouncedSearch = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    loadListings();
  }, 300);
};

const loadListings = async () => {
  loading.value = true;
  try {
    const params = new URLSearchParams({
      page: currentPage.value.toString(),
      search: searchQuery.value,
    });

    const response = await $api(`/admin/listings?${params}`, {
      method: "GET",
    });

    listings.value = response.data;
    paginationInfo.value = {
      from: response.from,
      to: response.to,
      total: response.total,
    };
    totalPages.value = response.last_page;
  } catch (error) {
    console.error("Error loading listings:", error);
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

const formatCurrency = (amount) => {
  return new Intl.NumberFormat("en-US", {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount);
};

const getListingStatusColor = (status) => {
  const colors = {
    draft: "warning",
    published: "success",
    archived: "error",
  };
  return colors[status] || "secondary";
};

const getListingTypeColor = (type) => {
  const colors = {
    "single-date": "primary",
    "multi-date": "info",
    "open-date": "success",
    other: "warning",
  };
  return colors[type] || "secondary";
};

const formatListingType = (type) => {
  const types = {
    "single-date": "Single Date",
    "multi-date": "Multi Date",
    "open-date": "Open Date",
    other: "Other",
  };
  return types[type] || type || "N/A";
};

const viewListing = (listing) => {
  selectedListing.value = listing;
  showListingViewDialog.value = true;
};

const editListing = (listing) => {
  selectedListing.value = listing;
  showListingEditDialog.value = true;
};

const handleListingUpdated = () => {
  console.log("Listing updated, reloading data...");
  loadListings();
  showListingEditDialog.value = false;
};

const deleteListing = async (listing) => {
  if (
    confirm(
      `Are you sure you want to delete listing "${listing.listing_title}"?`
    )
  ) {
    try {
      await $api(`/admin/listings/${listing.id}`, {
        method: "DELETE",
      });
      loadListings();
    } catch (error) {
      console.error("Error deleting listing:", error);
    }
  }
};

// Load listings on mount
onMounted(() => {
  // Check if user is admin
  const userDataCookie = useCookie("userData");
  const userData = userDataCookie.value;

  if (!userData || userData.role !== "admin") {
    console.log("Non-admin user detected, redirecting...");
    router.push("/");
    return;
  }

  loadListings();
});
</script>

<style scoped>
.v-data-table {
  border-radius: 8px;
  overflow: hidden;
}
</style>
