<script setup>
definePage({
  meta: {
    layout: "default",
    action: "read",
    subject: "Participants",
  },
});

const selectedEvent = ref("");
const invitationType = ref("email");
const participants = ref([]);
const newParticipant = ref({
  name: "",
  email: "",
  phone: "",
});

const events = ref([
  { title: "Mountain Hiking Adventure", value: "mountain-hiking" },
  { title: "City Walking Tour", value: "city-walking" },
  { title: "Wine Tasting Experience", value: "wine-tasting" },
  { title: "Beach Yoga Session", value: "beach-yoga" },
]);

const invitationTypes = [
  { title: "Email Invitation", value: "email" },
  { title: "Direct Link", value: "link" },
  { title: "Bulk Import", value: "bulk" },
];

const rules = {
  name: [(v) => !!v || "Name is required"],
  email: [
    (v) => !!v || "Email is required",
    (v) => /.+@.+\..+/.test(v) || "Email must be valid",
  ],
  phone: [
    (v) =>
      !v || /^[\+]?[1-9][\d]{0,15}$/.test(v) || "Phone number must be valid",
  ],
};

const refForm = ref();
const loading = ref(false);

const addParticipant = () => {
  const { valid } = refForm.value.validate();

  if (!valid) return;

  participants.value.push({
    id: Date.now(),
    ...newParticipant.value,
  });

  // Reset form
  newParticipant.value = {
    name: "",
    email: "",
    phone: "",
  };
};

const removeParticipant = (id) => {
  const index = participants.value.findIndex((p) => p.id === id);
  if (index > -1) {
    participants.value.splice(index, 1);
  }
};

const sendInvitations = async () => {
  if (participants.value.length === 0) {
    // Show error message
    return;
  }

  loading.value = true;

  try {
    // Here you would typically send the invitations to your API
    console.log("Sending invitations:", {
      event: selectedEvent.value,
      type: invitationType.value,
      participants: participants.value,
    });

    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 2000));

    // Show success message
    // You can use a toast notification here

    // Reset form
    selectedEvent.value = "";
    participants.value = [];
  } catch (error) {
    console.error("Error sending invitations:", error);
  } finally {
    loading.value = false;
  }
};

const generateInviteLink = () => {
  if (!selectedEvent.value) return;

  // Generate a unique invite link
  const link = `${window.location.origin}/invite/${
    selectedEvent.value
  }/${Date.now()}`;

  // Copy to clipboard
  navigator.clipboard.writeText(link);

  // Show success message
  // You can use a toast notification here
};
</script>

<template>
  <div>
    <VCard>
      <VCardTitle class="pa-6">
        <div>
          <h2 class="text-h4 font-weight-bold">Invite/Add Participants</h2>
          <p class="text-body-2 text-medium-emphasis mt-1">
            Invite participants to your events
          </p>
        </div>
      </VCardTitle>

      <VDivider />

      <VCardText class="pa-6">
        <VRow>
          <!-- Event Selection -->
          <VCol cols="12" md="6">
            <VSelect
              v-model="selectedEvent"
              :items="events"
              label="Select Event"
              placeholder="Choose an event to invite participants to"
              variant="outlined"
              required
            />
          </VCol>

          <!-- Invitation Type -->
          <VCol cols="12" md="6">
            <VSelect
              v-model="invitationType"
              :items="invitationTypes"
              label="Invitation Type"
              variant="outlined"
            />
          </VCol>

          <!-- Direct Link Section -->
          <VCol v-if="invitationType === 'link'" cols="12">
            <VCard variant="outlined" class="pa-4">
              <div class="d-flex align-center justify-space-between">
                <div>
                  <h4 class="text-h6 font-weight-medium mb-2">
                    Direct Invite Link
                  </h4>
                  <p class="text-body-2 text-medium-emphasis">
                    Share this link with participants to join your event
                  </p>
                </div>
                <VBtn
                  color="primary"
                  variant="outlined"
                  @click="generateInviteLink"
                >
                  Generate Link
                </VBtn>
              </div>
            </VCard>
          </VCol>

          <!-- Email Invitation Section -->
          <VCol v-if="invitationType === 'email'" cols="12">
            <VCard variant="outlined" class="pa-4">
              <h4 class="text-h6 font-weight-medium mb-4">Add Participants</h4>

              <VForm ref="refForm" @submit.prevent="addParticipant">
                <VRow>
                  <VCol cols="12" md="4">
                    <VTextField
                      v-model="newParticipant.name"
                      label="Full Name"
                      placeholder="Enter participant name"
                      :rules="rules.name"
                      variant="outlined"
                      density="compact"
                      required
                    />
                  </VCol>
                  <VCol cols="12" md="4">
                    <VTextField
                      v-model="newParticipant.email"
                      label="Email Address"
                      placeholder="Enter email address"
                      :rules="rules.email"
                      variant="outlined"
                      density="compact"
                      required
                    />
                  </VCol>
                  <VCol cols="12" md="3">
                    <VTextField
                      v-model="newParticipant.phone"
                      label="Phone (Optional)"
                      placeholder="Enter phone number"
                      :rules="rules.phone"
                      variant="outlined"
                      density="compact"
                    />
                  </VCol>
                  <VCol cols="12" md="1" class="d-flex align-end">
                    <VBtn type="submit" color="primary" icon size="small">
                      <VIcon icon="tabler-plus" />
                    </VBtn>
                  </VCol>
                </VRow>
              </VForm>

              <!-- Participants List -->
              <div v-if="participants.length > 0" class="mt-4">
                <h5 class="text-subtitle-1 font-weight-medium mb-3">
                  Added Participants
                </h5>
                <VList>
                  <VListItem
                    v-for="participant in participants"
                    :key="participant.id"
                    class="px-0"
                  >
                    <VListItemTitle class="font-weight-medium">
                      {{ participant.name }}
                    </VListItemTitle>
                    <VListItemSubtitle class="text-caption">
                      {{ participant.email }}
                      <span v-if="participant.phone">
                        • {{ participant.phone }}</span
                      >
                    </VListItemSubtitle>

                    <template #append>
                      <VBtn
                        icon
                        size="small"
                        color="error"
                        variant="text"
                        @click="removeParticipant(participant.id)"
                      >
                        <VIcon icon="tabler-trash" size="16" />
                      </VBtn>
                    </template>
                  </VListItem>
                </VList>
              </div>
            </VCard>
          </VCol>

          <!-- Bulk Import Section -->
          <VCol v-if="invitationType === 'bulk'" cols="12">
            <VCard variant="outlined" class="pa-4">
              <h4 class="text-h6 font-weight-medium mb-4">
                Bulk Import Participants
              </h4>
              <p class="text-body-2 text-medium-emphasis mb-4">
                Upload a CSV file with participant information (Name, Email,
                Phone)
              </p>

              <VFileInput
                label="Upload CSV File"
                accept=".csv"
                variant="outlined"
                prepend-icon="tabler-upload"
                density="compact"
              />

              <div class="mt-4">
                <VBtn
                  variant="outlined"
                  prepend-icon="tabler-download"
                  class="me-3"
                >
                  Download Template
                </VBtn>
                <VBtn variant="outlined" prepend-icon="tabler-eye">
                  Preview Data
                </VBtn>
              </div>
            </VCard>
          </VCol>

          <!-- Send Invitations Button -->
          <VCol
            v-if="participants.length > 0 || invitationType === 'link'"
            cols="12"
            class="d-flex justify-end"
          >
            <VBtn
              color="primary"
              :loading="loading"
              prepend-icon="tabler-send"
              @click="sendInvitations"
            >
              {{
                invitationType === "link"
                  ? "Generate & Share Link"
                  : "Send Invitations"
              }}
            </VBtn>
          </VCol>
        </VRow>
      </VCardText>
    </VCard>
  </div>
</template>

<style scoped>
.v-card {
  border-radius: 8px;
}
</style>
