<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const numberedSteps = [
  {
    title: "Basic Information",
  },
  {
    title: "Availability",
  },
  {
    title: "Pricing & Details",
  },
];

const currentStep = ref(0);
const loading = ref(false);

const formData = ref({
  // Step 1 fields
  title: "",
  description: "",
  location: "",
  category: "",
  // Step 2 fields
  minAdvanceNotice: "",
  maxAdvanceBooking: "",
  availableDays: [],
  // Step 3 fields
  price: "",
  maxParticipants: "",
  requirements: "",
  termsAccepted: false,
});

const onSubmit = async () => {
  loading.value = true;

  try {
    // Validate required fields
    if (!formData.value.termsAccepted) {
      alert("Please accept the terms and conditions");
      return;
    }

    console.log("Open Date Listing submitted:", formData.value);
    alert("Open Date Listing created successfully!");

    // Redirect back to listing page
    router.push({ name: "listing" });
  } catch (error) {
    console.error("Submission failed:", error);
    alert("Submission failed: " + (error.message || "Unknown error"));
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <!-- 👉 Custom Stepper -->
  <div class="custom-stepper mb-6">
    <div class="stepper-container">
      <template v-for="(step, idx) in numberedSteps" :key="idx">
        <div class="step-item" :class="{ active: idx === currentStep }">
          <span
            class="step-circle"
            :class="{ active: idx === currentStep }"
          ></span>
          <span class="step-number">{{
            (idx + 1).toString().padStart(2, "0")
          }}</span>
          <span class="step-label">{{ step.title }}</span>
        </div>
        <div v-if="idx < numberedSteps.length - 1" class="step-line"></div>
      </template>
    </div>
  </div>

  <div class="d-flex justify-center align-center" style="min-height: 60vh">
    <VCard style="width: 90vw; max-width: 1200px">
      <VCardText>
        <!-- 👉 stepper content -->
        <VForm>
          <VWindow v-model="currentStep" class="disable-tab-transition">
            <!-- Step 1: Basic Information -->
            <VWindowItem>
              <VRow>
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="formData.title"
                    label="Listing Title"
                    placeholder="Enter your listing title"
                    class="mb-4"
                  />
                  <AppTextField
                    v-model="formData.description"
                    label="Description"
                    placeholder="Describe your activity"
                    type="textarea"
                    rows="4"
                    class="mb-4"
                  />
                </VCol>
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="formData.location"
                    label="Location"
                    placeholder="Where will this activity take place?"
                    class="mb-4"
                  />
                  <AppSelect
                    v-model="formData.category"
                    label="Category"
                    placeholder="Select activity category"
                    :items="[
                      'Hiking',
                      'Diving',
                      'Rock Climbing',
                      'Skiing',
                      'Cycling',
                      'Kayaking',
                      'Photography Tours',
                      'Cultural Tours',
                      'Wildlife Tours',
                      'Other',
                    ]"
                    class="mb-4"
                  />
                </VCol>
              </VRow>
            </VWindowItem>
            <!-- Step 2: Availability -->
            <VWindowItem>
              <VRow>
                <VCol cols="12" md="6">
                  <AppSelect
                    v-model="formData.minAdvanceNotice"
                    label="Minimum Advance Notice"
                    placeholder="How much notice do you need?"
                    :items="[
                      'Same day',
                      '1 day in advance',
                      '2 days in advance',
                      '3 days in advance',
                      '1 week in advance',
                      '2 weeks in advance',
                    ]"
                    class="mb-4"
                  />
                  <AppSelect
                    v-model="formData.maxAdvanceBooking"
                    label="Maximum Advance Booking"
                    placeholder="How far in advance can people book?"
                    :items="[
                      '1 week ahead',
                      '2 weeks ahead',
                      '1 month ahead',
                      '2 months ahead',
                      '3 months ahead',
                      '6 months ahead',
                      '1 year ahead',
                    ]"
                    class="mb-4"
                  />
                </VCol>
                <VCol cols="12" md="6">
                  <div class="mb-4">
                    <label class="v-label text-body-2 mb-3 d-block">
                      Available Days of Week
                    </label>
                    <VCheckbox
                      v-model="formData.availableDays"
                      value="monday"
                      label="Monday"
                      class="mb-2"
                    />
                    <VCheckbox
                      v-model="formData.availableDays"
                      value="tuesday"
                      label="Tuesday"
                      class="mb-2"
                    />
                    <VCheckbox
                      v-model="formData.availableDays"
                      value="wednesday"
                      label="Wednesday"
                      class="mb-2"
                    />
                    <VCheckbox
                      v-model="formData.availableDays"
                      value="thursday"
                      label="Thursday"
                      class="mb-2"
                    />
                    <VCheckbox
                      v-model="formData.availableDays"
                      value="friday"
                      label="Friday"
                      class="mb-2"
                    />
                    <VCheckbox
                      v-model="formData.availableDays"
                      value="saturday"
                      label="Saturday"
                      class="mb-2"
                    />
                    <VCheckbox
                      v-model="formData.availableDays"
                      value="sunday"
                      label="Sunday"
                      class="mb-2"
                    />
                  </div>
                </VCol>
              </VRow>
            </VWindowItem>
            <!-- Step 3: Pricing & Details -->
            <VWindowItem>
              <VRow>
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="formData.price"
                    label="Price per Person"
                    placeholder="Enter price in EUR"
                    type="number"
                    class="mb-4"
                  />
                  <AppTextField
                    v-model="formData.maxParticipants"
                    label="Maximum Participants per Session"
                    placeholder="How many people can join per session?"
                    type="number"
                    class="mb-4"
                  />
                </VCol>
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="formData.requirements"
                    label="Requirements"
                    placeholder="Any special requirements or notes"
                    type="textarea"
                    rows="4"
                    class="mb-4"
                  />
                  <!-- Terms and Conditions -->
                  <div class="mb-6">
                    <div class="d-flex align-start gap-3">
                      <VCheckbox
                        v-model="formData.termsAccepted"
                        class="mt-1"
                      />
                      <div class="text-body-2">
                        I agree to the terms and conditions for creating this
                        listing.
                      </div>
                    </div>
                  </div>
                </VCol>
              </VRow>
            </VWindowItem>
          </VWindow>
          <div
            class="d-flex flex-wrap gap-4 justify-sm-space-between justify-center mt-8"
          >
            <VBtn
              color="secondary"
              variant="tonal"
              :disabled="currentStep === 0"
              @click="currentStep--"
            >
              <VIcon icon="tabler-arrow-left" start class="flip-in-rtl" />
              Previous
            </VBtn>
            <VBtn
              v-if="numberedSteps.length - 1 === currentStep"
              color="success"
              :loading="loading"
              :disabled="loading"
              @click="onSubmit"
            >
              {{ loading ? "Creating..." : "Create Listing" }}
            </VBtn>
            <VBtn v-else class="next-btn-dark" @click="currentStep++">
              Next
              <VIcon icon="tabler-arrow-right" end class="flip-in-rtl" />
            </VBtn>
          </div>
        </VForm>
      </VCardText>
    </VCard>
  </div>
</template>

<style scoped>
.custom-stepper {
  width: 100%;
  margin-bottom: 32px;
  margin-top: -50px !important;
  padding-top: 0 !important;
}

.stepper-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0;
  width: 100%;
}

.step-item {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 4px 8px;
  min-width: max-content;
  flex-shrink: 0;
}

.step-circle {
  width: 20px;
  height: 20px;
  border: 3px solid #ec8d22;
  border-radius: 50%;
  background: #fff;
  display: inline-block;
  position: relative;
  transition: all 0.2s;
  flex-shrink: 0;
}

.step-item.active .step-circle {
  background: #ec8d22;
}

.step-item.active .step-circle::after {
  content: "";
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 8px;
  height: 8px;
  background: #fff;
  border-radius: 50%;
}

.step-number {
  font-family: "Anton", sans-serif;
  font-size: 24px;
  color: #2f2b3d;
  font-weight: bold;
  margin-left: 2px;
  flex-shrink: 0;
}

.step-label {
  font-family: "Karla", sans-serif;
  font-size: 16px;
  color: #444151;
  margin-left: 4px;
  white-space: nowrap;
}

.step-line {
  flex: 1 1 0;
  min-width: 60px;
  max-width: 120px;
  height: 3px;
  background: #ec8d22;
  margin: 0 4px;
  border-radius: 2px;
  flex-shrink: 0;
}

.next-btn-dark {
  background: #111 !important;
  color: #fff !important;
  font-size: 12px !important;
  min-width: 92px;
  min-height: 38px;
  border-radius: 8px;
  font-weight: 700;
  box-shadow: 0 2px 8px 0 rgba(44, 44, 44, 0.08);
  transition: background 0.2s;
}
.next-btn-dark:hover {
  background: #222 !important;
}
</style>
