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
const listings = ref([]);
const searchQuery = ref("");
const currentPage = ref(1);
const totalPages = ref(1);
const paginationInfo = ref({
  from: 0,
  to: 0,
  total: 0,
});

const headers = [
  { title: "Title", key: "title", sortable: true },
  { title: "Description", key: "description", sortable: true },
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

const viewListing = (listing) => {
  // Navigate to view listing page
  router.push(`/listing/${listing.id}`);
};

const editListing = (listing) => {
  // Navigate to edit listing page
  router.push(`/admin/listings/${listing.id}/edit`);
};

const deleteListing = async (listing) => {
  if (confirm(`Are you sure you want to delete listing "${listing.title}"?`)) {
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
