<script setup>
definePage({
  meta: {
    layout: "default",
    action: "read",
    subject: "Events",
  },
});

const form = ref({
  title: "",
  type: "",
  date: "",
  time: "",
  duration: "",
  maxParticipants: "",
  price: "",
  location: "",
  description: "",
  requirements: "",
  includedItems: "",
  notIncludedItems: "",
});

const eventTypes = [
  { title: "Adventure", value: "adventure" },
  { title: "Cultural", value: "cultural" },
  { title: "Wellness", value: "wellness" },
  { title: "Culinary", value: "culinary" },
  { title: "Educational", value: "educational" },
  { title: "Entertainment", value: "entertainment" },
];

const durationOptions = [
  { title: "1 hour", value: "1" },
  { title: "2 hours", value: "2" },
  { title: "3 hours", value: "3" },
  { title: "4 hours", value: "4" },
  { title: "5 hours", value: "5" },
  { title: "6 hours", value: "6" },
  { title: "Full day", value: "8" },
  { title: "Multi-day", value: "24" },
];

const rules = {
  title: [(v) => !!v || "Event title is required"],
  type: [(v) => !!v || "Event type is required"],
  date: [(v) => !!v || "Date is required"],
  time: [(v) => !!v || "Time is required"],
  maxParticipants: [(v) => !!v || "Maximum participants is required"],
  price: [(v) => !!v || "Price is required"],
  location: [(v) => !!v || "Location is required"],
  description: [(v) => !!v || "Description is required"],
};

const refForm = ref();
const loading = ref(false);

const handleSubmit = async () => {
  const { valid } = await refForm.value.validate();

  if (!valid) return;

  loading.value = true;

  try {
    // Here you would typically send the data to your API
    console.log("Creating event:", form.value);

    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Show success message
    // You can use a toast notification here

    // Reset form
    form.value = {
      title: "",
      type: "",
      date: "",
      time: "",
      duration: "",
      maxParticipants: "",
      price: "",
      location: "",
      description: "",
      requirements: "",
      includedItems: "",
      notIncludedItems: "",
    };

    // Navigate to events list
    await $router.push("/all-events");
  } catch (error) {
    console.error("Error creating event:", error);
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div>
    <VCard>
      <VCardTitle class="pa-6">
        <div>
          <h2 class="text-h4 font-weight-bold">Add New Event</h2>
          <p class="text-body-2 text-medium-emphasis mt-1">
            Create a new event for your participants
          </p>
        </div>
      </VCardTitle>

      <VDivider />

      <VCardText class="pa-6">
        <VForm ref="refForm" @submit.prevent="handleSubmit">
          <VRow>
            <!-- Event Title -->
            <VCol cols="12" md="6">
              <VTextField
                v-model="form.title"
                label="Event Title"
                placeholder="Enter event title"
                :rules="rules.title"
                variant="outlined"
                required
              />
            </VCol>

            <!-- Event Type -->
            <VCol cols="12" md="6">
              <VSelect
                v-model="form.type"
                :items="eventTypes"
                label="Event Type"
                placeholder="Select event type"
                :rules="rules.type"
                variant="outlined"
                required
              />
            </VCol>

            <!-- Date -->
            <VCol cols="12" md="4">
              <VTextField
                v-model="form.date"
                label="Date"
                type="date"
                :rules="rules.date"
                variant="outlined"
                required
              />
            </VCol>

            <!-- Time -->
            <VCol cols="12" md="4">
              <VTextField
                v-model="form.time"
                label="Time"
                type="time"
                :rules="rules.time"
                variant="outlined"
                required
              />
            </VCol>

            <!-- Duration -->
            <VCol cols="12" md="4">
              <VSelect
                v-model="form.duration"
                :items="durationOptions"
                label="Duration"
                placeholder="Select duration"
                variant="outlined"
              />
            </VCol>

            <!-- Max Participants -->
            <VCol cols="12" md="6">
              <VTextField
                v-model="form.maxParticipants"
                label="Maximum Participants"
                type="number"
                placeholder="Enter max participants"
                :rules="rules.maxParticipants"
                variant="outlined"
                required
              />
            </VCol>

            <!-- Price -->
            <VCol cols="12" md="6">
              <VTextField
                v-model="form.price"
                label="Price (€)"
                type="number"
                placeholder="Enter price"
                :rules="rules.price"
                variant="outlined"
                required
                prepend-inner-icon="tabler-currency-euro"
              />
            </VCol>

            <!-- Location -->
            <VCol cols="12">
              <VTextField
                v-model="form.location"
                label="Location"
                placeholder="Enter event location"
                :rules="rules.location"
                variant="outlined"
                required
              />
            </VCol>

            <!-- Description -->
            <VCol cols="12">
              <VTextarea
                v-model="form.description"
                label="Event Description"
                placeholder="Describe your event..."
                :rules="rules.description"
                variant="outlined"
                rows="4"
                required
              />
            </VCol>

            <!-- Requirements -->
            <VCol cols="12" md="6">
              <VTextarea
                v-model="form.requirements"
                label="Requirements"
                placeholder="What participants need to bring or prepare..."
                variant="outlined"
                rows="3"
              />
            </VCol>

            <!-- Included Items -->
            <VCol cols="12" md="6">
              <VTextarea
                v-model="form.includedItems"
                label="What's Included"
                placeholder="Items or services included in the price..."
                variant="outlined"
                rows="3"
              />
            </VCol>

            <!-- Not Included Items -->
            <VCol cols="12">
              <VTextarea
                v-model="form.notIncludedItems"
                label="What's Not Included"
                placeholder="Items or services not included in the price..."
                variant="outlined"
                rows="3"
              />
            </VCol>

            <!-- Submit Buttons -->
            <VCol cols="12" class="d-flex gap-4 justify-end">
              <VBtn variant="outlined" @click="$router.push('/all-events')">
                Cancel
              </VBtn>
              <VBtn
                type="submit"
                color="primary"
                :loading="loading"
                prepend-icon="tabler-plus"
              >
                Create Event
              </VBtn>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </VCard>
  </div>
</template>

<style scoped>
.v-card {
  border-radius: 8px;
}
</style>
