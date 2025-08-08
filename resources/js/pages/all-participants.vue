<script setup>
definePage({
  meta: {
    layout: "default",
    action: "read",
    subject: "Participants",
  },
});

const search = ref("");
const selectedEvent = ref("all");
const selectedStatus = ref("all");

const participants = ref([
  {
    id: 1,
    name: "John Doe",
    email: "john.doe@example.com",
    phone: "+1 234 567 8900",
    events: ["Mountain Hiking Adventure", "City Walking Tour"],
    totalBookings: 3,
    totalSpent: "€450",
    status: "Active",
    joinDate: "2024-01-15",
    lastActivity: "2024-02-15",
  },
  {
    id: 2,
    name: "Jane Smith",
    email: "jane.smith@example.com",
    phone: "+1 234 567 8901",
    events: ["City Walking Tour"],
    totalBookings: 1,
    totalSpent: "€45",
    status: "Active",
    joinDate: "2024-02-10",
    lastActivity: "2024-02-20",
  },
  {
    id: 3,
    name: "Mike Johnson",
    email: "mike.johnson@example.com",
    phone: "+1 234 567 8902",
    events: ["Wine Tasting Experience"],
    totalBookings: 2,
    totalSpent: "€240",
    status: "Active",
    joinDate: "2024-01-20",
    lastActivity: "2024-02-25",
  },
  {
    id: 4,
    name: "Sarah Wilson",
    email: "sarah.wilson@example.com",
    phone: "+1 234 567 8903",
    events: ["Beach Yoga Session"],
    totalBookings: 1,
    totalSpent: "€30",
    status: "Inactive",
    joinDate: "2024-01-25",
    lastActivity: "2024-02-01",
  },
  {
    id: 5,
    name: "David Brown",
    email: "david.brown@example.com",
    phone: "+1 234 567 8904",
    events: ["Mountain Hiking Adventure", "Wine Tasting Experience"],
    totalBookings: 4,
    totalSpent: "€520",
    status: "Active",
    joinDate: "2024-01-10",
    lastActivity: "2024-02-28",
  },
]);

const filteredParticipants = computed(() => {
  return participants.value.filter((participant) => {
    const matchesSearch =
      participant.name.toLowerCase().includes(search.value.toLowerCase()) ||
      participant.email.toLowerCase().includes(search.value.toLowerCase());
    const matchesEvent =
      selectedEvent.value === "all" ||
      participant.events.some((event) =>
        event.toLowerCase().includes(selectedEvent.value.toLowerCase())
      );
    const matchesStatus =
      selectedStatus.value === "all" ||
      participant.status.toLowerCase() === selectedStatus.value;

    return matchesSearch && matchesEvent && matchesStatus;
  });
});

const eventOptions = [
  { title: "All Events", value: "all" },
  { title: "Mountain Hiking Adventure", value: "mountain hiking adventure" },
  { title: "City Walking Tour", value: "city walking tour" },
  { title: "Wine Tasting Experience", value: "wine tasting experience" },
  { title: "Beach Yoga Session", value: "beach yoga session" },
];

const statusOptions = [
  { title: "All Status", value: "all" },
  { title: "Active", value: "active" },
  { title: "Inactive", value: "inactive" },
];

const getStatusColor = (status) => {
  switch (status.toLowerCase()) {
    case "active":
      return "success";
    case "inactive":
      return "warning";
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
          <h2 class="text-h4 font-weight-bold">All Participants</h2>
          <p class="text-body-2 text-medium-emphasis mt-1">
            Manage and view all your event participants
          </p>
        </div>
        <VBtn
          color="primary"
          prepend-icon="tabler-user-plus"
          @click="$router.push('/invite-participants')"
        >
          Add Participants
        </VBtn>
      </VCardTitle>

      <VDivider />

      <VCardText class="pa-6">
        <!-- Search and Filters -->
        <VRow class="mb-6">
          <VCol cols="12" md="4">
            <VTextField
              v-model="search"
              label="Search Participants"
              placeholder="Search by name or email..."
              prepend-inner-icon="tabler-search"
              variant="outlined"
              density="compact"
            />
          </VCol>
          <VCol cols="12" md="4">
            <VSelect
              v-model="selectedEvent"
              :items="eventOptions"
              label="Event"
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
        </VRow>

        <!-- Participants Table -->
        <VDataTable
          :headers="[
            { title: 'Participant', key: 'name', sortable: true },
            { title: 'Events', key: 'events', sortable: false },
            { title: 'Total Bookings', key: 'totalBookings', sortable: true },
            { title: 'Total Spent', key: 'totalSpent', sortable: true },
            { title: 'Status', key: 'status', sortable: true },
            { title: 'Join Date', key: 'joinDate', sortable: true },
            { title: 'Last Activity', key: 'lastActivity', sortable: true },
            { title: 'Actions', key: 'actions', sortable: false },
          ]"
          :items="filteredParticipants"
          :items-per-page="10"
          class="participants-table"
        >
          <!-- Participant Column -->
          <template #item.name="{ item }">
            <div>
              <div class="font-weight-medium">{{ item.name }}</div>
              <div class="text-caption text-medium-emphasis">
                {{ item.email }}
              </div>
              <div class="text-caption text-medium-emphasis">
                {{ item.phone }}
              </div>
            </div>
          </template>

          <!-- Events Column -->
          <template #item.events="{ item }">
            <div class="d-flex flex-wrap gap-1">
              <VChip
                v-for="event in item.events"
                :key="event"
                size="x-small"
                variant="outlined"
                color="primary"
              >
                {{ event }}
              </VChip>
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

          <!-- Actions Column -->
          <template #item.actions="{ item }">
            <div class="d-flex gap-2">
              <VBtn
                size="small"
                variant="outlined"
                @click="() => $router.push(`/participant-details/${item.id}`)"
              >
                View
              </VBtn>
              <VBtn
                size="small"
                color="info"
                variant="outlined"
                @click="() => $router.push(`/participant-bookings/${item.id}`)"
              >
                Bookings
              </VBtn>
              <VBtn
                size="small"
                color="success"
                variant="outlined"
                @click="
                  () =>
                    $router.push(`/invite-participants?participant=${item.id}`)
                "
              >
                Invite
              </VBtn>
            </div>
          </template>
        </VDataTable>
      </VCardText>
    </VCard>

    <!-- Statistics Cards -->
    <VRow class="mt-6">
      <VCol cols="12" md="3">
        <VCard>
          <VCardText class="pa-4">
            <div class="d-flex align-center justify-space-between">
              <div>
                <h3
                  class="text-h6 font-weight-medium text-medium-emphasis mb-1"
                >
                  Total Participants
                </h3>
                <h2 class="text-h4 font-weight-bold text-primary">
                  {{ participants.length }}
                </h2>
              </div>
              <VAvatar color="primary" variant="tonal" size="48">
                <VIcon size="24" icon="tabler-users" />
              </VAvatar>
            </div>
          </VCardText>
        </VCard>
      </VCol>

      <VCol cols="12" md="3">
        <VCard>
          <VCardText class="pa-4">
            <div class="d-flex align-center justify-space-between">
              <div>
                <h3
                  class="text-h6 font-weight-medium text-medium-emphasis mb-1"
                >
                  Active Participants
                </h3>
                <h2 class="text-h4 font-weight-bold text-success">
                  {{ participants.filter((p) => p.status === "Active").length }}
                </h2>
              </div>
              <VAvatar color="success" variant="tonal" size="48">
                <VIcon size="24" icon="tabler-user-check" />
              </VAvatar>
            </div>
          </VCardText>
        </VCard>
      </VCol>

      <VCol cols="12" md="3">
        <VCard>
          <VCardText class="pa-4">
            <div class="d-flex align-center justify-space-between">
              <div>
                <h3
                  class="text-h6 font-weight-medium text-medium-emphasis mb-1"
                >
                  Total Bookings
                </h3>
                <h2 class="text-h4 font-weight-bold text-info">
                  {{
                    participants.reduce((sum, p) => sum + p.totalBookings, 0)
                  }}
                </h2>
              </div>
              <VAvatar color="info" variant="tonal" size="48">
                <VIcon size="24" icon="tabler-calendar" />
              </VAvatar>
            </div>
          </VCardText>
        </VCard>
      </VCol>

      <VCol cols="12" md="3">
        <VCard>
          <VCardText class="pa-4">
            <div class="d-flex align-center justify-space-between">
              <div>
                <h3
                  class="text-h6 font-weight-medium text-medium-emphasis mb-1"
                >
                  Total Revenue
                </h3>
                <h2 class="text-h4 font-weight-bold text-warning">€1,285</h2>
              </div>
              <VAvatar color="warning" variant="tonal" size="48">
                <VIcon size="24" icon="tabler-currency-euro" />
              </VAvatar>
            </div>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </div>
</template>

<style scoped>
.participants-table {
  border-radius: 8px;
}
</style>
