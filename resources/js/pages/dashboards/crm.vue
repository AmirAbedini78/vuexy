<script setup>
// All chart and table components removed for redesign

// Events data for the table
const eventsData = [
  {
    id: 1,
    eventTitle: "Watching Stars in Night Sky",
    location: "Mountain Peak Observatory",
    advId: "02357",
    status: "Submitted",
    participants: "0/12",
    statusColor: "warning",
  },
  {
    id: 2,
    eventTitle: "Collecting Shells on Beach",
    location: "Crystal Beach Resort",
    advId: "02358",
    status: "Approved",
    participants: "0/6",
    statusColor: "info",
  },
  {
    id: 3,
    eventTitle: "Hot Air Balloon Ride",
    location: "Sky Adventure Park",
    advId: "02359",
    status: "Live",
    participants: "11/15",
    statusColor: "success",
  },
  {
    id: 4,
    eventTitle: "Some Thrilling Adventure",
    location: "Extreme Sports Center",
    advId: "02360",
    status: "Denied",
    participants: "0/8",
    statusColor: "error",
  },
  {
    id: 5,
    eventTitle: "Some Romantic Adventure",
    location: "Sunset Valley Resort",
    advId: "02361",
    status: "Other Events",
    participants: "6/6",
    statusColor: "secondary",
  },
  {
    id: 6,
    eventTitle: "Some Fun Adventure",
    location: "Adventure Land Park",
    advId: "02362",
    status: "Edit Review Pending",
    participants: "0/5",
    statusColor: "warning",
  },
];

// Table headers
const headers = [
  { title: "EVENT TITLE", key: "eventTitle", width: "35%" },
  { title: "ADV. ID", key: "advId", width: "15%" },
  { title: "STATUS", key: "status", width: "20%" },
  { title: "PARTICIPANTS", key: "participants", width: "15%" },
  { title: "ACTION", key: "actions", sortable: false, width: "15%" },
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
      // Navigate to listing page
      navigateTo("/listing");
    },
  },
  {
    id: 2,
    title: "Add Event",
    icon: "/images/4svg/add event.png",
    color: "success",
    action: () => console.log("Add Event clicked"),
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
          Welcome <span class="provider-name">[Provider's Name]</span>,
        </h2>
        <p class="welcome-text">
          Welcome to your dashboard. Here's a quick overview of your adventures.
          You can manage your listings, view new bookings, and track your
          earnings, all in one place. And if you ever need help with anything,
          Just Contact support!
        </p>
        <VBtn color="warning" size="large" class="support-btn" elevation="0">
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
          :items="eventsData"
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

          <!-- Actions -->
          <template #item.actions="{ item }">
            <div class="d-flex gap-2">
              <IconBtn size="small">
                <VIcon icon="tabler-edit" size="18" />
              </IconBtn>
              <IconBtn size="small">
                <VIcon icon="tabler-dots-vertical" size="18" />
              </IconBtn>
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
                    Math.min(
                      options.page * options.itemsPerPage,
                      eventsData.length
                    )
                  }}
                  of {{ eventsData.length }} entries
                </span>

                <VPagination
                  v-model="options.page"
                  :total-visible="$vuetify.display.smAndDown ? 3 : 5"
                  :length="Math.ceil(eventsData.length / options.itemsPerPage)"
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
</template>

<style lang="scss" scoped>
.welcome-content {
  padding: 2rem;

  .welcome-title {
    font-family: "Anton", "Inter", "Segoe UI", Tahoma, Geneva, Verdana,
      sans-serif;
    font-size: 1.5rem;
    font-weight: 400;
    line-height: 1.2;
    margin-bottom: 1.5rem;
    color: #000000;

    .provider-name {
      color: #ec8d22;
      font-weight: 400;
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
      color: #2c3e50;
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
    color: #2c3e50;
    line-height: 1.3;
    text-align: center;
  }
}
</style>
