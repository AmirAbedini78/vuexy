<script setup>
definePage({
  meta: {
    layout: "default",
    action: "read",
    subject: "Bookings",
  },
});

const search = ref("");
const selectedStatus = ref("all");
const selectedEvent = ref("all");

const bookings = ref([
  {
    id: 1,
    eventTitle: "Mountain Hiking Adventure",
    participantName: "John Doe",
    participantEmail: "john.doe@example.com",
    status: "Confirmed",
    bookingDate: "2024-02-10",
    eventDate: "2024-02-15",
    participants: 2,
    totalPrice: "€300",
    paymentStatus: "Paid",
  },
  {
    id: 2,
    eventTitle: "City Walking Tour",
    participantName: "Jane Smith",
    participantEmail: "jane.smith@example.com",
    status: "Pending",
    bookingDate: "2024-02-12",
    eventDate: "2024-02-20",
    participants: 1,
    totalPrice: "€45",
    paymentStatus: "Pending",
  },
  {
    id: 3,
    eventTitle: "Wine Tasting Experience",
    participantName: "Mike Johnson",
    participantEmail: "mike.johnson@example.com",
    status: "Confirmed",
    bookingDate: "2024-02-08",
    eventDate: "2024-02-25",
    participants: 3,
    totalPrice: "€240",
    paymentStatus: "Paid",
  },
  {
    id: 4,
    eventTitle: "Beach Yoga Session",
    participantName: "Sarah Wilson",
    participantEmail: "sarah.wilson@example.com",
    status: "Cancelled",
    bookingDate: "2024-02-05",
    eventDate: "2024-03-01",
    participants: 1,
    totalPrice: "€30",
    paymentStatus: "Refunded",
  },
]);

const filteredBookings = computed(() => {
  return bookings.value.filter((booking) => {
    const matchesSearch =
      booking.eventTitle.toLowerCase().includes(search.value.toLowerCase()) ||
      booking.participantName
        .toLowerCase()
        .includes(search.value.toLowerCase()) ||
      booking.participantEmail
        .toLowerCase()
        .includes(search.value.toLowerCase());
    const matchesStatus =
      selectedStatus.value === "all" ||
      booking.status.toLowerCase() === selectedStatus.value;
    const matchesEvent =
      selectedEvent.value === "all" ||
      booking.eventTitle
        .toLowerCase()
        .includes(selectedEvent.value.toLowerCase());

    return matchesSearch && matchesStatus && matchesEvent;
  });
});

const statusOptions = [
  { title: "All Status", value: "all" },
  { title: "Confirmed", value: "confirmed" },
  { title: "Pending", value: "pending" },
  { title: "Cancelled", value: "cancelled" },
];

const eventOptions = [
  { title: "All Events", value: "all" },
  { title: "Mountain Hiking Adventure", value: "mountain hiking adventure" },
  { title: "City Walking Tour", value: "city walking tour" },
  { title: "Wine Tasting Experience", value: "wine tasting experience" },
  { title: "Beach Yoga Session", value: "beach yoga session" },
];

const getStatusColor = (status) => {
  switch (status.toLowerCase()) {
    case "confirmed":
      return "success";
    case "pending":
      return "warning";
    case "cancelled":
      return "error";
    default:
      return "default";
  }
};

const getPaymentStatusColor = (status) => {
  switch (status.toLowerCase()) {
    case "paid":
      return "success";
    case "pending":
      return "warning";
    case "refunded":
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
          <h2 class="text-h4 font-weight-bold">All Bookings</h2>
          <p class="text-body-2 text-medium-emphasis mt-1">
            Manage and view all your event bookings
          </p>
        </div>
        <VBtn
          color="primary"
          prepend-icon="tabler-user-plus"
          @click="$router.push('/invite-participants')"
        >
          Invite Participants
        </VBtn>
      </VCardTitle>

      <VDivider />

      <VCardText class="pa-6">
        <!-- Search and Filters -->
        <VRow class="mb-6">
          <VCol cols="12" md="4">
            <VTextField
              v-model="search"
              label="Search Bookings"
              placeholder="Search by event, participant name or email..."
              prepend-inner-icon="tabler-search"
              variant="outlined"
              density="compact"
            />
          </VCol>
          <VCol cols="12" md="4">
            <VSelect
              v-model="selectedStatus"
              :items="statusOptions"
              label="Booking Status"
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
        </VRow>

        <!-- Bookings Table -->
        <VDataTable
          :headers="[
            { title: 'Event', key: 'eventTitle', sortable: true },
            { title: 'Participant', key: 'participantName', sortable: true },
            { title: 'Status', key: 'status', sortable: true },
            { title: 'Booking Date', key: 'bookingDate', sortable: true },
            { title: 'Event Date', key: 'eventDate', sortable: true },
            { title: 'Participants', key: 'participants', sortable: true },
            { title: 'Total Price', key: 'totalPrice', sortable: true },
            { title: 'Payment', key: 'paymentStatus', sortable: true },
            { title: 'Actions', key: 'actions', sortable: false },
          ]"
          :items="filteredBookings"
          :items-per-page="10"
          class="bookings-table"
        >
          <!-- Event Title Column -->
          <template #item.eventTitle="{ item }">
            <div>
              <div class="font-weight-medium">{{ item.eventTitle }}</div>
              <div class="text-caption text-medium-emphasis">
                {{ item.participantEmail }}
              </div>
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

          <!-- Payment Status Column -->
          <template #item.paymentStatus="{ item }">
            <VChip
              :color="getPaymentStatusColor(item.paymentStatus)"
              size="small"
              class="font-weight-medium"
            >
              {{ item.paymentStatus }}
            </VChip>
          </template>

          <!-- Actions Column -->
          <template #item.actions="{ item }">
            <div class="d-flex gap-2">
              <VBtn
                size="small"
                variant="outlined"
                @click="() => $router.push(`/booking-details/${item.id}`)"
              >
                View
              </VBtn>
              <VBtn
                size="small"
                color="success"
                variant="outlined"
                v-if="item.status === 'Pending'"
                @click="() => console.log('Confirm booking:', item.id)"
              >
                Confirm
              </VBtn>
              <VBtn
                size="small"
                color="error"
                variant="outlined"
                v-if="item.status === 'Pending'"
                @click="() => console.log('Cancel booking:', item.id)"
              >
                Cancel
              </VBtn>
            </div>
          </template>
        </VDataTable>
      </VCardText>
    </VCard>
  </div>
</template>

<style scoped>
.bookings-table {
  border-radius: 8px;
}
</style>
