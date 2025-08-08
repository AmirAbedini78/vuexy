<script setup>
definePage({
  meta: {
    layout: "default",
    action: "read",
    subject: "Support",
  },
});

const supportForm = ref({
  name: "",
  email: "",
  subject: "",
  category: "",
  priority: "medium",
  message: "",
});

const loading = ref(false);

const categories = [
  { title: "Technical Issue", value: "technical" },
  { title: "Billing & Payment", value: "billing" },
  { title: "Account Access", value: "account" },
  { title: "Event Management", value: "events" },
  { title: "Booking Issues", value: "booking" },
  { title: "General Inquiry", value: "general" },
];

const priorities = [
  { title: "Low", value: "low" },
  { title: "Medium", value: "medium" },
  { title: "High", value: "high" },
  { title: "Urgent", value: "urgent" },
];

const rules = {
  name: [(v) => !!v || "Name is required"],
  email: [
    (v) => !!v || "Email is required",
    (v) => /.+@.+\..+/.test(v) || "Email must be valid",
  ],
  subject: [(v) => !!v || "Subject is required"],
  category: [(v) => !!v || "Category is required"],
  message: [(v) => !!v || "Message is required"],
};

const refForm = ref();

const submitSupportRequest = async () => {
  const { valid } = await refForm.value.validate();

  if (!valid) return;

  loading.value = true;

  try {
    // Here you would typically send the support request to your API
    console.log("Submitting support request:", supportForm.value);

    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 2000));

    // Reset form
    supportForm.value = {
      name: "",
      email: "",
      subject: "",
      category: "",
      priority: "medium",
      message: "",
    };

    // Show success message
    // You can use a toast notification here
  } catch (error) {
    console.error("Error submitting support request:", error);
  } finally {
    loading.value = false;
  }
};

const contactMethods = [
  {
    title: "Live Chat",
    description: "Get instant help from our support team",
    icon: "tabler-message-circle",
    color: "primary",
    action: () => console.log("Open live chat"),
  },
  {
    title: "Email Support",
    description: "Send us an email and we'll respond within 24 hours",
    icon: "tabler-mail",
    color: "success",
    action: () => console.log("Open email client"),
  },
  {
    title: "Phone Support",
    description: "Call us for immediate assistance",
    icon: "tabler-phone",
    color: "info",
    action: () => console.log("Call support"),
  },
];

const faqItems = [
  {
    question: "How do I create a new event?",
    answer:
      'To create a new event, go to the "Adventures" section and click on "Add Event". Fill in the required information and submit the form.',
  },
  {
    question: "How do I manage my bookings?",
    answer:
      'You can manage all your bookings in the "Manage Bookings" section. Here you can view, confirm, or cancel bookings.',
  },
  {
    question: "How do I withdraw money from my wallet?",
    answer:
      'To withdraw money, go to the "Wallet" section and click on the "Withdraw" button. Follow the instructions to complete the withdrawal.',
  },
  {
    question: "How do I invite participants to my events?",
    answer:
      'You can invite participants by going to "Invite/Add Participants" and either adding them manually or generating a shareable link.',
  },
];
</script>

<template>
  <div>
    <!-- Header -->
    <VCard class="mb-6">
      <VCardTitle class="pa-6">
        <div>
          <h2 class="text-h4 font-weight-bold">Get Support</h2>
          <p class="text-body-2 text-medium-emphasis mt-1">
            We're here to help you with any questions or issues
          </p>
        </div>
      </VCardTitle>
    </VCard>

    <!-- Contact Methods -->
    <VRow class="mb-6">
      <VCol
        v-for="method in contactMethods"
        :key="method.title"
        cols="12"
        md="4"
      >
        <VCard class="h-100 cursor-pointer" @click="method.action">
          <VCardText class="pa-6 text-center">
            <VAvatar
              :color="method.color"
              variant="tonal"
              size="64"
              class="mb-4"
            >
              <VIcon size="32" :icon="method.icon" />
            </VAvatar>
            <h3 class="text-h6 font-weight-bold mb-2">{{ method.title }}</h3>
            <p class="text-body-2 text-medium-emphasis">
              {{ method.description }}
            </p>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>

    <!-- Support Form -->
    <VCard class="mb-6">
      <VCardTitle class="pa-6">
        <h3 class="text-h5 font-weight-bold">Submit Support Request</h3>
      </VCardTitle>

      <VDivider />

      <VCardText class="pa-6">
        <VForm ref="refForm" @submit.prevent="submitSupportRequest">
          <VRow>
            <VCol cols="12" md="6">
              <VTextField
                v-model="supportForm.name"
                label="Full Name"
                placeholder="Enter your full name"
                :rules="rules.name"
                variant="outlined"
                required
              />
            </VCol>

            <VCol cols="12" md="6">
              <VTextField
                v-model="supportForm.email"
                label="Email Address"
                placeholder="Enter your email address"
                :rules="rules.email"
                variant="outlined"
                required
              />
            </VCol>

            <VCol cols="12" md="6">
              <VTextField
                v-model="supportForm.subject"
                label="Subject"
                placeholder="Brief description of your issue"
                :rules="rules.subject"
                variant="outlined"
                required
              />
            </VCol>

            <VCol cols="12" md="6">
              <VSelect
                v-model="supportForm.category"
                :items="categories"
                label="Category"
                placeholder="Select a category"
                :rules="rules.category"
                variant="outlined"
                required
              />
            </VCol>

            <VCol cols="12" md="6">
              <VSelect
                v-model="supportForm.priority"
                :items="priorities"
                label="Priority"
                variant="outlined"
              />
            </VCol>

            <VCol cols="12">
              <VTextarea
                v-model="supportForm.message"
                label="Message"
                placeholder="Please describe your issue in detail..."
                :rules="rules.message"
                variant="outlined"
                rows="6"
                required
              />
            </VCol>

            <VCol cols="12" class="d-flex justify-end">
              <VBtn
                type="submit"
                color="primary"
                :loading="loading"
                prepend-icon="tabler-send"
              >
                Submit Request
              </VBtn>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </VCard>

    <!-- FAQ Section -->
    <VCard class="mb-6">
      <VCardTitle class="pa-6">
        <h3 class="text-h5 font-weight-bold">Frequently Asked Questions</h3>
      </VCardTitle>

      <VDivider />

      <VCardText class="pa-6">
        <VExpansionPanels>
          <VExpansionPanel v-for="(item, index) in faqItems" :key="index">
            <VExpansionPanelTitle class="font-weight-medium">
              {{ item.question }}
            </VExpansionPanelTitle>
            <VExpansionPanelText>
              <p class="text-body-2">{{ item.answer }}</p>
            </VExpansionPanelText>
          </VExpansionPanel>
        </VExpansionPanels>
      </VCardText>
    </VCard>

    <!-- Support Information -->
    <VRow>
      <VCol cols="12" md="6">
        <VCard>
          <VCardTitle class="pa-6">
            <h3 class="text-h5 font-weight-bold">Support Hours</h3>
          </VCardTitle>
          <VCardText class="pa-6 pt-0">
            <VList>
              <VListItem>
                <VListItemTitle class="font-weight-medium"
                  >Monday - Friday</VListItemTitle
                >
                <VListItemSubtitle>9:00 AM - 6:00 PM (CET)</VListItemSubtitle>
              </VListItem>
              <VListItem>
                <VListItemTitle class="font-weight-medium"
                  >Saturday</VListItemTitle
                >
                <VListItemSubtitle>10:00 AM - 4:00 PM (CET)</VListItemSubtitle>
              </VListItem>
              <VListItem>
                <VListItemTitle class="font-weight-medium"
                  >Sunday</VListItemTitle
                >
                <VListItemSubtitle>Closed</VListItemSubtitle>
              </VListItem>
            </VList>
          </VCardText>
        </VCard>
      </VCol>

      <VCol cols="12" md="6">
        <VCard>
          <VCardTitle class="pa-6">
            <h3 class="text-h5 font-weight-bold">Contact Information</h3>
          </VCardTitle>
          <VCardText class="pa-6 pt-0">
            <VList>
              <VListItem>
                <template #prepend>
                  <VIcon icon="tabler-mail" color="primary" />
                </template>
                <VListItemTitle class="font-weight-medium"
                  >Email</VListItemTitle
                >
                <VListItemSubtitle>support@explorerelite.com</VListItemSubtitle>
              </VListItem>
              <VListItem>
                <template #prepend>
                  <VIcon icon="tabler-phone" color="primary" />
                </template>
                <VListItemTitle class="font-weight-medium"
                  >Phone</VListItemTitle
                >
                <VListItemSubtitle>+41 44 123 4567</VListItemSubtitle>
              </VListItem>
              <VListItem>
                <template #prepend>
                  <VIcon icon="tabler-map-pin" color="primary" />
                </template>
                <VListItemTitle class="font-weight-medium"
                  >Address</VListItemTitle
                >
                <VListItemSubtitle>Zurich, Switzerland</VListItemSubtitle>
              </VListItem>
            </VList>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </div>
</template>

<style scoped>
.v-card {
  border-radius: 8px;
}

.cursor-pointer {
  cursor: pointer;
  transition: transform 0.2s ease;
}

.cursor-pointer:hover {
  transform: translateY(-2px);
}
</style>
