<script setup>
definePage({
  meta: {
    layout: "default",
    action: "read",
    subject: "Events",
  },
});

const search = ref("");
const selectedStatus = ref("all");
const selectedType = ref("all");

const events = ref([
  {
    id: 1,
    title: "Mountain Hiking Adventure",
    type: "Adventure",
    status: "Active",
    date: "2024-02-15",
    participants: 12,
    maxParticipants: 20,
    price: "€150",
    location: "Swiss Alps",
  },
  {
    id: 2,
    title: "City Walking Tour",
    type: "Cultural",
    status: "Active",
    date: "2024-02-20",
    participants: 8,
    maxParticipants: 15,
    price: "€45",
    location: "Paris, France",
  },
  {
    id: 3,
    title: "Beach Yoga Session",
    type: "Wellness",
    status: "Draft",
    date: "2024-03-01",
    participants: 0,
    maxParticipants: 10,
    price: "€30",
    location: "Bali, Indonesia",
  },
  {
    id: 4,
    title: "Wine Tasting Experience",
    type: "Culinary",
    status: "Active",
    date: "2024-02-25",
    participants: 15,
    maxParticipants: 15,
    price: "€80",
    location: "Tuscany, Italy",
  },
]);

const filteredEvents = computed(() => {
  return events.value.filter((event) => {
    const matchesSearch =
      event.title.toLowerCase().includes(search.value.toLowerCase()) ||
      event.location.toLowerCase().includes(search.value.toLowerCase());
    const matchesStatus =
      selectedStatus.value === "all" ||
      event.status.toLowerCase() === selectedStatus.value;
    const matchesType =
      selectedType.value === "all" ||
      event.type.toLowerCase() === selectedType.value;

    return matchesSearch && matchesStatus && matchesType;
  });
});

const statusOptions = [
  { title: "All Status", value: "all" },
  { title: "Active", value: "active" },
  { title: "Draft", value: "draft" },
  { title: "Completed", value: "completed" },
];

const typeOptions = [
  { title: "All Types", value: "all" },
  { title: "Adventure", value: "adventure" },
  { title: "Cultural", value: "cultural" },
  { title: "Wellness", value: "wellness" },
  { title: "Culinary", value: "culinary" },
];

const getStatusColor = (status) => {
  switch (status.toLowerCase()) {
    case "active":
      return "success";
    case "draft":
      return "warning";
    case "completed":
      return "info";
    default:
      return "default";
  }
};
</script>

<template>
  <div>
    <VCard>
      <VCardTitle class="d-flex align-center justify-space-between pa-6">
        <div>
          <h2 class="text-h4 font-weight-bold">All Events</h2>
          <p class="text-body-2 text-medium-emphasis mt-1">
            Manage and view all your created events
          </p>
        </div>
        <VBtn
          color="primary"
          prepend-icon="tabler-plus"
          @click="$router.push('/add-event')"
        >
          Add New Event
        </VBtn>
      </VCardTitle>

      <VDivider />

      <VCardText class="pa-6">
        <!-- Search and Filters -->
        <VRow class="mb-6">
          <VCol cols="12" md="4">
            <VTextField
              v-model="search"
              label="Search Events"
              placeholder="Search by title or location..."
              prepend-inner-icon="tabler-search"
              variant="outlined"
              density="compact"
            />
          </VCol>
          <VCol cols="12" md="4">
            <VSelect
              v-model="selectedStatus"
              :items="statusOptions"
              label="Status"
              variant="outlined"
              density="compact"
            />
          </VCol>
          <VCol cols="12" md="4">
            <VSelect
              v-model="selectedType"
              :items="typeOptions"
              label="Event Type"
              variant="outlined"
              density="compact"
            />
          </VCol>
        </VRow>

        <!-- Events Table -->
        <VDataTable
          :headers="[
            { title: 'Event', key: 'title', sortable: true },
            { title: 'Type', key: 'type', sortable: true },
            { title: 'Status', key: 'status', sortable: true },
            { title: 'Date', key: 'date', sortable: true },
            { title: 'Participants', key: 'participants', sortable: true },
            { title: 'Price', key: 'price', sortable: true },
            { title: 'Location', key: 'location', sortable: true },
            { title: 'Actions', key: 'actions', sortable: false },
          ]"
          :items="filteredEvents"
          :items-per-page="10"
          class="events-table"
        >
          <!-- Event Title Column -->
          <template #item.title="{ item }">
            <div>
              <div class="font-weight-medium">{{ item.title }}</div>
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

          <!-- Participants Column -->
          <template #item.participants="{ item }">
            <div class="d-flex align-center">
              <span class="font-weight-medium">{{ item.participants }}</span>
              <span class="text-medium-emphasis ms-1"
                >/ {{ item.maxParticipants }}</span
              >
            </div>
          </template>

          <!-- Actions Column -->
          <template #item.actions="{ item }">
            <div class="d-flex gap-2">
              <VBtn
                size="small"
                variant="outlined"
                @click="() => $router.push(`/edit-event/${item.id}`)"
              >
                Edit
              </VBtn>
              <VBtn
                size="small"
                color="info"
                variant="outlined"
                @click="() => $router.push(`/event-details/${item.id}`)"
              >
                View
              </VBtn>
            </div>
          </template>
        </VDataTable>
      </VCardText>
    </VCard>
  </div>
</template>

<style scoped>
.events-table {
  border-radius: 8px;
}
</style>
