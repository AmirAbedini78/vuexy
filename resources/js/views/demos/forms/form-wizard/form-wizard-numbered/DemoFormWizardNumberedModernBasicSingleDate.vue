<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const numberedSteps = [
  {
    title: "Basic Information",
  },
  {
    title: "Date & Time",
  },
  {
    title: "Pricing & Details",
  },
];

const currentStep = ref(0);
const loading = ref(false);

const formData = ref({
  // Step 1 fields
  startingDate: "",
  finishingDate: "",
  listingTitle: "",
  listingDescription: "",
  price: "",
  minCapacity: "",
  maxCapacity: "",
  subtitle: "",
  experienceLevel: "",
  fitnessLevel: "",
  // Step 2 fields
  startDate: "",
  endDate: "",
  startTime: "",
  endTime: "",
  // Step 3 fields
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

    console.log("Single Date Listing submitted:", formData.value);
    alert("Single Date Listing created successfully!");

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
                <!-- Left column -->
                <VCol cols="12" md="6">
                  <!-- Starting Date -->
                  <div class="date-picker-wrapper mb-4">
                    <AppDateTimePicker
                      ref="startingDatePicker"
                      v-model="formData.startingDate"
                      label="Starting Date*"
                      placeholder="Select your listing starting date"
                      :config="{ dateFormat: 'Y-m-d', allowInput: true }"
                    />
                  </div>

                  <!-- Finishing Date -->
                  <div class="date-picker-wrapper mb-4">
                    <AppDateTimePicker
                      ref="finishingDatePicker"
                      v-model="formData.finishingDate"
                      label="Finishing Date*"
                      placeholder="Select your listing finishing date"
                      :config="{ dateFormat: 'Y-m-d', allowInput: true }"
                    />
                  </div>

                  <!-- Listing Title -->
                  <AppTextField
                    v-model="formData.listingTitle"
                    label="Listing Title"
                    placeholder="The main title of Your listing"
                    class="mb-4"
                  />

                  <!-- Listing Description -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Listing Description
                    </label>
                    <VTextarea
                      v-model="formData.listingDescription"
                      placeholder="Tell us any further information we should have about your adventure"
                      rows="5"
                      class="rich-text-area"
                    />
                    <p
                      class="text-caption text-error mt-2 mb-0"
                      style="font-size: 11px"
                    >
                      Note: Please add at least 500 characters.
                    </p>
                  </div>
                </VCol>

                <!-- Right column -->
                <VCol cols="12" md="6">
                  <!-- Price -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Price <span class="required-star">*</span>
                    </label>
                    <div class="price-input-wrapper">
                      <VTextField
                        v-model="formData.price"
                        placeholder="Add price In Euros"
                        type="number"
                        class="price-input"
                      />
                      <span class="euro-symbol">€</span>
                    </div>
                    <p
                      class="text-caption text-error mt-2 mb-0"
                      style="font-size: 11px"
                    >
                      Note: This system uses Euro (€) only
                    </p>
                  </div>

                  <!-- Departure Capacity -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Departure Capacity <span class="required-star">*</span>
                    </label>
                    <div class="d-flex gap-3">
                      <VTextField
                        v-model="formData.minCapacity"
                        placeholder="Min Num"
                        type="number"
                        class="capacity-input"
                        style="max-width: 120px"
                      />
                      <VTextField
                        v-model="formData.maxCapacity"
                        placeholder="Max Num"
                        type="number"
                        class="capacity-input"
                        style="max-width: 120px"
                      />
                    </div>
                  </div>

                  <!-- Subtitle -->
                  <VTextField
                    v-model="formData.subtitle"
                    label="Subtitle"
                    placeholder="A tagline for your adventure"
                    class="mb-6"
                  />

                  <!-- Experience Level and Fitness Level -->
                  <VRow>
                    <VCol cols="6">
                      <div class="mb-4">
                        <label
                          class="v-label text-body-2 mb-3 d-block"
                          style="
                            font-size: 16px !important;
                            font-weight: 400 !important;
                          "
                        >
                          Experience Level
                          <VIcon
                            icon="tabler-help-circle"
                            size="16"
                            class="question-icon"
                          />
                        </label>
                        <VRadioGroup
                          v-model="formData.experienceLevel"
                          class="mt-2"
                        >
                          <VRadio value="journeys" label="Journeys" />
                          <VRadio value="discovery" label="Discovery" />
                          <VRadio value="expedition" label="Expedition" />
                          <VRadio
                            value="extreme-expedition"
                            label="Extreme Expedition"
                          />
                          <VRadio value="not-sure" label="Not Sure" />
                        </VRadioGroup>
                      </div>
                    </VCol>
                    <VCol cols="6">
                      <div class="mb-4">
                        <label
                          class="v-label text-body-2 mb-3 d-block"
                          style="
                            font-size: 16px !important;
                            font-weight: 400 !important;
                          "
                        >
                          Fitness Level
                          <VIcon
                            icon="tabler-help-circle"
                            size="16"
                            class="question-icon"
                          />
                        </label>
                        <VRadioGroup
                          v-model="formData.fitnessLevel"
                          class="mt-2"
                        >
                          <VRadio value="easy" label="Easy" />
                          <VRadio value="moderate" label="Moderate" />
                          <VRadio value="challenging" label="Challenging" />
                          <VRadio value="intense" label="Intense" />
                          <VRadio value="not-sure" label="Not Sure" />
                        </VRadioGroup>
                      </div>
                    </VCol>
                  </VRow>
                </VCol>
              </VRow>
            </VWindowItem>
            <!-- Step 2: Date & Time -->
            <VWindowItem>
              <VRow>
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="formData.startDate"
                    label="Start Date"
                    type="date"
                    class="mb-4"
                  />
                  <AppTextField
                    v-model="formData.startTime"
                    label="Start Time"
                    type="time"
                    class="mb-4"
                  />
                </VCol>
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="formData.endDate"
                    label="End Date"
                    type="date"
                    class="mb-4"
                  />
                  <AppTextField
                    v-model="formData.endTime"
                    label="End Time"
                    type="time"
                    class="mb-4"
                  />
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
                    label="Maximum Participants"
                    placeholder="How many people can join?"
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

/* Required star styling */
.required-star {
  color: #ff4444;
  font-weight: bold;
}

/* Date picker styles */
.date-picker-wrapper {
  position: relative;
}
.date-picker-wrapper .calendar-icon-png {
  position: absolute;
  top: 50%;
  right: 12px;
  transform: translateY(-50%);
  width: 24px;
  height: 24px;
  background: url("/images/4svg/wired-outline-28-calendar-hover-pinch.png")
    no-repeat center center;
  background-size: 24px 24px;
  cursor: pointer;
  z-index: 3;
}
.date-picker-wrapper input.flat-picker-custom-style {
  padding-right: 40px !important;
}

:deep(input.flat-picker-custom-style),
:deep(input.flatpickr-input) {
  background: url("/images/4svg/wired-outline-28-calendar-hover-pinch.png")
    no-repeat right 12px center !important;
  background-size: 24px 24px !important;
  padding-right: 44px !important;
}

/* Form field styles */
.v-label,
.v-field__label {
  font-size: 20px !important;
  font-weight: 700 !important;
}

.v-input input,
.v-input textarea,
.v-select__selection,
.v-select__selections,
.v-select__input,
.v-field__input {
  font-size: 19px !important;
  line-height: 1.5 !important;
}

.v-input input::placeholder,
.v-input textarea::placeholder,
.v-field__input::placeholder {
  font-size: 18px !important;
  color: #b0b0b0 !important;
}

/* Radio button spacing */
.v-radio-group .v-radio {
  margin-bottom: 8px;
}

.v-radio-group .v-radio:last-child {
  margin-bottom: 0;
}

/* Price input with Euro symbol */
.price-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.price-input {
  flex: 1;
}

.euro-symbol {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #666;
  font-weight: 500;
  font-size: 16px;
  pointer-events: none;
  z-index: 2;
}

/* Rich text area styling */
.rich-text-area {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  background: #fff;
}

.rich-text-area .v-field__input {
  padding: 12px;
  font-size: 16px;
  line-height: 1.5;
}

/* Question icon styling */
.question-icon {
  color: #666;
  margin-left: 4px;
  cursor: help;
}

/* Capacity input styling */
.capacity-input .v-field__input {
  text-align: center;
}

/* Improved form field styling */
.v-text-field,
.v-textarea {
  border-radius: 8px;
}

.v-text-field .v-field__outline,
.v-textarea .v-field__outline {
  border-color: #e0e0e0;
}

.v-text-field .v-field--focused .v-field__outline,
.v-textarea .v-field--focused .v-field__outline {
  border-color: #ec8d22;
}

/* Radio button styling */
.v-radio .v-selection-control__input {
  color: #ec8d22;
}

.v-radio .v-selection-control__input .v-icon {
  color: #ec8d22;
}

/* Label styling improvements */
.v-label {
  color: #333;
  font-weight: 500;
}

/* Placeholder styling */
.v-field__input::placeholder {
  color: #999;
  font-size: 16px;
}
</style>
