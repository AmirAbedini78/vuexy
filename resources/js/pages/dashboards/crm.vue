<script setup>
// All chart and table components removed for redesign

// Import router for navigation
import AddEventListingDialog from "@/components/dialogs/AddEventListingDialog.vue";
import { companyUserService, individualUserService } from "@/services/api";
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

// User data for dynamic provider name
const userData = ref(null);
const loggedInUser = ref(null);
const loading = ref(false);
const error = ref(null);

// Modal state
const showAddEventListingModal = ref(false);

// Get logged-in user data from cookies
const getLoggedInUser = () => {
  const userDataCookie = useCookie("userData");
  if (userDataCookie.value) {
    loggedInUser.value = userDataCookie.value;
    console.log("Logged in user data:", userDataCookie.value);
    // If the cookie contains the user data with name, use it directly
    if (userDataCookie.value.full_name || userDataCookie.value.company_name) {
      userData.value = userDataCookie.value;
      console.log("Using user data from cookie:", userData.value);
    }
  } else {
    console.log("No user data cookie found");
  }
};

// Fetch user data from API if needed
const fetchUserData = async () => {
  if (!loggedInUser.value) {
    console.log("No logged in user, skipping fetch");
    return;
  }

  // If we already have the name from cookies, no need to fetch
  if (userData.value?.full_name || userData.value?.company_name) {
    console.log("User data already available, skipping fetch");
    return;
  }

  try {
    loading.value = true;
    console.log("Fetching user data from API...");

    // Try to get user ID from logged-in user data
    const userId = loggedInUser.value.id || loggedInUser.value.user_id;
    console.log("User ID:", userId);

    if (!userId) {
      console.warn("User ID not found in logged-in user data");
      return;
    }

    // Try to determine user type from logged-in user data
    let userType = loggedInUser.value.user_type;
    console.log("User type from cookie:", userType);

    // If user_type is not available, try to infer from the data structure
    if (!userType) {
      if (loggedInUser.value.company_name) {
        userType = "company";
      } else if (loggedInUser.value.full_name) {
        userType = "individual";
      }
      console.log("Inferred user type:", userType);
    }

    // If we still don't have user type, try both endpoints
    if (!userType) {
      console.log("Trying both endpoints...");
      try {
        const individualResponse = await individualUserService.getById(userId);
        userData.value = individualResponse.data;
        console.log("Found individual user data:", userData.value);
        return;
      } catch (e) {
        console.log("Individual user fetch failed:", e);
        try {
          const companyResponse = await companyUserService.getById(userId);
          userData.value = companyResponse.data;
          console.log("Found company user data:", userData.value);
          return;
        } catch (e2) {
          console.warn("Could not fetch user data from either endpoint");
        }
      }
    } else {
      // Fetch user data based on type
      if (userType === "individual") {
        const response = await individualUserService.getById(userId);
        userData.value = response.data;
        console.log("Fetched individual user data:", userData.value);
      } else if (userType === "company") {
        const response = await companyUserService.getById(userId);
        userData.value = response.data;
        console.log("Fetched company user data:", userData.value);
      }
    }
  } catch (err) {
    console.error("Error fetching user data:", err);
    error.value = err.message || "Failed to load user data";
  } finally {
    loading.value = false;
  }
};

// Get user display name
const getUserDisplayName = () => {
  // First try to get from userData (from API)
  if (userData.value) {
    return (
      userData.value.full_name ||
      userData.value.company_name ||
      userData.value.name ||
      "User"
    );
  }

  // Fallback to loggedInUser data
  if (loggedInUser.value) {
    return (
      loggedInUser.value.full_name ||
      loggedInUser.value.company_name ||
      loggedInUser.value.name ||
      loggedInUser.value.username ||
      "User"
    );
  }

  return "User";
};

// Modal handlers
const handleModalSubmit = (selectedOption) => {
  console.log("Selected option:", selectedOption);
  if (selectedOption === "listing") {
    // Navigate to listing page
    router.push("/listing");
  } else if (selectedOption && selectedOption.type === "event") {
    // Handle event creation - this creates a listing record
    console.log("Event listing created:", {
      wizardType: selectedOption.wizardType,
      data: selectedOption.data,
    });

    // Here you would typically:
    // 1. Save the event as a listing record in the database
    // 2. Show success message to user
    // 3. Optionally navigate to the listing page or refresh the events table

    // For now, just show a success message
    alert(`Event created successfully! Type: ${selectedOption.wizardType}`);
  }
};

// Fetch user data on mount
onMounted(async () => {
  console.log("Dashboard CRM mounted");
  getLoggedInUser();
  await fetchUserData();
  console.log("Dashboard CRM setup complete");
});

// Dynamic events data for the table
const events = ref([]);

const statusToColor = (s) =>
  ({
    submitted: "warning",
    approved: "info",
    live: "success",
    denied: "error",
    inactive: "secondary",
    other_events: "secondary",
    edit_review: "warning",
  }[s] || "warning");

const loadMyEvents = async () => {
  loading.value = true;
  try {
    console.log("Loading user's events...");
    
    // Get user ID from cookies
    const userDataCookie = useCookie("userData");
    const userId = userDataCookie.value?.id || userDataCookie.value?.user_id;
    
    if (!userId) {
      console.error("User ID not found");
      events.value = [];
      return;
    }
    
    console.log("Loading events for user ID:", userId);
    
    // Try to get user's listings from the listings API
    const res = await $api("/listings", { method: "GET" });
    console.log("Listings API response:", res);
    
    // Handle different response formats
    let allListings = [];
    if (Array.isArray(res)) {
      allListings = res;
    } else if (res?.data && Array.isArray(res.data)) {
      allListings = res.data;
    } else if (res?.data?.data && Array.isArray(res.data.data)) {
      allListings = res.data.data;
    }
    
    console.log("All listings:", allListings);
    
    // Filter listings for current user
    const userListings = allListings.filter((listing) => {
      return listing.user_id === userId || listing.user?.id === userId;
    });
    
    console.log("User's listings:", userListings);
    
    // Map to events format
    events.value = userListings.map((listing) => ({
      id: listing.id,
      eventTitle: listing.listing_title || listing.title || "Untitled Event",
      location: listing.locations || listing.location || "No location",
      advId: String(listing.id).padStart(5, "0"),
      status: (listing.status || "draft")
        .replace("other_events", "Other Events")
        .replace("edit_review", "Edit Review Pending")
        .replace("_", " "),
      participants: `${listing.min_capacity || 0}/${listing.max_capacity || 0}`,
      statusColor: statusToColor(listing.status),
      price: listing.price || 0,
      listing_type: listing.listing_type || "single-date",
      created_at: listing.created_at,
      updated_at: listing.updated_at,
    }));
    
    console.log("Mapped events:", events.value);
  } catch (e) {
    console.error("Failed to load my events", e);
    events.value = [];
  } finally {
    loading.value = false;
  }
};

onMounted(loadMyEvents);

// Helper functions for listing type
const getListingTypeColor = (type) => {
  const colors = {
    'single-date': 'primary',
    'multi-date': 'success', 
    'open-date': 'warning',
    'other': 'secondary'
  };
  return colors[type] || 'secondary';
};

const formatListingType = (type) => {
  const types = {
    'single-date': 'Single Date',
    'multi-date': 'Multi Date',
    'open-date': 'Open Date',
    'other': 'Other'
  };
  return types[type] || 'Unknown';
};

// Event actions
const editEvent = (item) => {
  console.log('Edit event:', item);
  // Navigate to edit page or open edit modal
  router.push(`/listing/${item.id}/edit`);
};

const viewEvent = (item) => {
  console.log('View event:', item);
  // Navigate to view page or open view modal
  router.push(`/listing/${item.id}`);
};

// Table headers
const headers = [
  { title: "EVENT TITLE", key: "eventTitle", width: "30%" },
  { title: "ADV. ID", key: "advId", width: "12%" },
  { title: "STATUS", key: "status", width: "18%" },
  { title: "PARTICIPANTS", key: "participants", width: "12%" },
  { title: "PRICE", key: "price", width: "10%" },
  { title: "TYPE", key: "listing_type", width: "8%" },
  { title: "ACTION", key: "actions", sortable: false, width: "10%" },
];

// Pagination options
const options = ref({
  page: 1,
  itemsPerPage: 6,
  sortBy: [""],
  sortDesc: [false],
});

// Action cards data
const actionCards = [
  {
    id: 1,
    title: "Create Listing",
    icon: "/images/4svg/creating list.png",
    color: "primary",
    action: () => {
      console.log("Create Listing clicked - navigating to listing page");
      // Navigate to listing page using route name
      try {
        console.log("Current route:", router.currentRoute.value);
        console.log("Available routes:", router.getRoutes().filter(r => r.name?.includes('listing')));
        
        // Try to navigate to listing page
        router.push({ name: 'listing' }).then(() => {
          console.log("Navigation successful");
        }).catch((error) => {
          console.error("Navigation failed:", error);
          // Try alternative navigation
          router.push('/listing').then(() => {
            console.log("Alternative navigation successful");
          }).catch((altError) => {
            console.error("Alternative navigation failed:", altError);
            // Final fallback
            window.location.href = '/listing';
          });
        });
      } catch (error) {
        console.error("Navigation failed:", error);
        // Fallback to direct URL
        window.location.href = '/listing';
      }
    },
  },
  {
    id: 2,
    title: "Add Event",
    icon: "/images/4svg/add event.png",
    color: "success",
    action: () => (showAddEventListingModal.value = true),
  },
  {
    id: 3,
    title: "Invite/Add Participants",
    icon: "/images/4svg/invite.png",
    color: "info",
    action: () => console.log("Invite/Add Participants clicked"),
  },
  {
    id: 4,
    title: "Explorer Passport",
    icon: "/images/4svg/passport.png",
    color: "warning",
    action: () => console.log("Explorer Passport clicked"),
  },
];
</script>

<template>
  <VRow class="match-height">
    <!-- 👉 Welcome Component -->
    <VCol cols="12" md="8" lg="6">
      <div class="welcome-content">
        <h2 class="welcome-title">
          Welcome <span class="provider-name">{{ getUserDisplayName() }}</span
          >,
        </h2>
        <p class="welcome-text">
          Welcome to your dashboard. Here's a quick overview of your adventures.
          You can manage your listings, view new bookings, and track your
          earnings, all in one place. And if you ever need help with anything,
          Just Contact support!
        </p>
        <VBtn
          color="warning"
          size="large"
          class="support-btn"
          elevation="0"
          href="https://explorerelite.com/support/"
          target="_blank"
        >
          Get Support
        </VBtn>
      </div>
    </VCol>

    <!-- 👉 Camping PNG Image -->
    <VCol cols="12" md="4" lg="6" class="d-flex justify-end align-center">
      <div class="camping-image">
        <img
          src="/images/4svg/wired-flat-1697-campsite-hover-pinch.png"
          alt="Camping Icon"
          class="camping-png"
        />
      </div>
    </VCol>

    <!-- 👉 Events Data Table -->
    <VCol cols="12" md="8" class="mt-6">
      <VCard>
        <VCardTitle class="text-h5 mb-4"> Your Events </VCardTitle>

        <VDataTable
          :headers="headers"
          :items="events"
          :items-per-page="options.itemsPerPage"
          :page="options.page"
          :options="options"
          class="events-table"
          density="comfortable"
        >
          <!-- Event Title with Location -->
          <template #item.eventTitle="{ item }">
            <div class="event-title-cell">
              <div class="event-title">{{ item.eventTitle }}</div>
              <div class="event-location">{{ item.location }}</div>
            </div>
          </template>

          <!-- Status -->
          <template #item.status="{ item }">
            <VChip
              :color="item.statusColor"
              size="small"
              class="font-weight-medium"
            >
              {{ item.status }}
            </VChip>
          </template>

          <!-- Price -->
          <template #item.price="{ item }">
            <span class="font-weight-medium">
              €{{ item.price ? item.price.toFixed(2) : '0.00' }}
            </span>
          </template>

          <!-- Listing Type -->
          <template #item.listing_type="{ item }">
            <VChip
              :color="getListingTypeColor(item.listing_type)"
              size="small"
              variant="outlined"
            >
              {{ formatListingType(item.listing_type) }}
            </VChip>
          </template>

          <!-- Actions -->
          <template #item.actions="{ item }">
            <div class="d-flex gap-2">
              <IconBtn size="small" @click="editEvent(item)">
                <VIcon icon="tabler-edit" size="18" />
              </IconBtn>
              <IconBtn size="small" @click="viewEvent(item)">
                <VIcon icon="tabler-eye" size="18" />
              </IconBtn>
              <IconBtn size="small">
                <VIcon icon="tabler-dots-vertical" size="18" />
              </IconBtn>
            </div>
          </template>

          <!-- Empty state -->
          <template #no-data>
            <div class="text-center w-100 pa-6">
              <div class="mb-4">
                <VIcon icon="tabler-calendar-off" size="48" color="grey" />
              </div>
              <h6 class="text-h6 mb-2">No Events Found</h6>
              <p class="text-medium-emphasis mb-4">
                You haven't created any events yet. Start by adding your first adventure!
              </p>
              <VBtn 
                color="primary" 
                @click="showAddEventListingModal = true"
                prepend-icon="tabler-plus"
              >
                Add Your First Event
              </VBtn>
            </div>
          </template>

          <!-- Pagination -->
          <template #bottom>
            <VCardText class="pt-2">
              <div
                class="d-flex flex-wrap justify-center justify-sm-space-between gap-y-2 mt-2"
              >
                <span class="text-body-2">
                  Showing {{ (options.page - 1) * options.itemsPerPage + 1 }} to
                  {{
                    Math.min(options.page * options.itemsPerPage, events.length)
                  }}
                  of {{ events.length }} entries
                </span>

                <VPagination
                  v-model="options.page"
                  :total-visible="$vuetify.display.smAndDown ? 3 : 5"
                  :length="Math.ceil(events.length / options.itemsPerPage)"
                />
              </div>
            </VCardText>
          </template>
        </VDataTable>
      </VCard>
    </VCol>

    <!-- 👉 Action Cards -->
    <VCol cols="12" md="4" class="mt-6">
      <div class="action-cards-container">
        <VRow>
          <VCol
            v-for="card in actionCards"
            :key="card.id"
            cols="6"
            class="action-card-col"
          >
            <VCard class="action-card" elevation="2" @click="card.action">
              <VCardText class="action-card-content">
                <div class="action-icon">
                  <img
                    :src="card.icon"
                    :alt="card.title"
                    class="action-icon-img"
                  />
                </div>
                <div class="action-title">
                  {{ card.title }}
                </div>
              </VCardText>
            </VCard>
          </VCol>
        </VRow>
      </div>
    </VCol>
  </VRow>

  <!-- Add Event/Listing Modal -->
  <AddEventListingDialog
    v-model:is-dialog-visible="showAddEventListingModal"
    @submit="handleModalSubmit"
  />
</template>

<style lang="scss" scoped>
.welcome-content {
  padding: 2rem;

  .welcome-title {
    font-family: "Anton", "Inter", "Segoe UI", Tahoma, Geneva, Verdana,
      sans-serif;
    font-size: 1.5rem;
    font-weight: unset;
    line-height: 1.2;
    margin-bottom: 1.5rem;
    color: #000000;

    .provider-name {
      color: #ec8d22;
      font-weight: 700;
      font-family: inherit;
    }
  }

  .welcome-text {
    font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.6;
    color: #6c757d;
    margin-bottom: 2rem;
    max-width: 600px;
  }

  .support-btn {
    font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: none;
    letter-spacing: 0.5px;
    border-radius: 8px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #ec8d22 0%, #d17a1a 100%);
    border: none;
    transition: all 0.3s ease;

    &:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(236, 141, 34, 0.3);
    }
  }
}

.camping-image {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  padding: 2rem;
  margin-left: 2rem;

  .camping-png {
    max-width: 200px;
    height: auto;
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
  }
}

.events-table {
  .v-data-table__wrapper {
    border-radius: 8px;
  }

  .v-data-table-header {
    background-color: #f8f9fa;
  }

  .event-title-cell {
    padding: 8px 0;

    .event-title {
      font-size: 0.875rem;
      font-weight: 600;
      color: #000;
      line-height: 1.3;
      margin-bottom: 4px;
      word-wrap: break-word;
      hyphens: auto;
    }

    .event-location {
      font-size: 0.75rem;
      color: #6c757d;
      line-height: 1.2;
      font-style: italic;
    }
  }

  // Improve table spacing
  .v-data-table__td {
    padding: 12px 16px;
    vertical-align: middle;
  }

  .v-data-table__th {
    padding: 16px;
    font-weight: 600;
    font-size: 0.875rem;
    color: #495057;
  }

  // Make table more compact
  .v-data-table__tbody .v-data-table__tr {
    border-bottom: 1px solid #e9ecef;

    &:hover {
      background-color: #f8f9fa;
    }
  }
}

.action-cards-container {
  padding: 0.5rem;
}

.action-card-col {
  padding: 0.5rem;
}

.action-card {
  cursor: pointer;
  transition: all 0.3s ease;
  border-radius: 12px;
  height: 140px;
  display: flex;
  align-items: center;
  justify-content: center;

  &:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  }

  .action-card-content {
    text-align: center;
    padding: 1.5rem;
    width: 100%;
  }

  .action-icon {
    margin-bottom: 1rem;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .action-icon-img {
    width: 48px;
    height: 48px;
    object-fit: contain;
  }

  .action-title {
    font-size: 1rem;
    font-weight: 700;
    color: #000;
    line-height: 1.3;
    text-align: center;
  }
}
</style>
