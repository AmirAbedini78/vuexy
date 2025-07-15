<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const numberedSteps = [
  {
    title: "Basic Information",
  },
  {
    title: "Date Range",
  },
  {
    title: "Pricing & Details",
  },
];

const currentStep = ref(0);
const loading = ref(false);

const formData = ref({
  // Step 1 fields
  listingTitle: "",
  listingDescription: "",
  locations: "",
  groupLanguage: "",
  subtitle: "",
  activitiesIncluded: "",
  experienceLevel: "",
  fitnessLevel: "",
  // Step 2 fields
  startDate: "",
  endDate: "",
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

    console.log("Multi Date Listing submitted:", formData.value);
    alert("Multi Date Listing created successfully!");

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
                  <!-- Listing Title -->
                  <AppTextField
                    v-model="formData.listingTitle"
                    label="Listing Title*"
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
                      Listing Description <span class="required-star">*</span>
                    </label>
                    <VTextarea
                      v-model="formData.listingDescription"
                      placeholder="Tell us any further information we should have about your adventure"
                      rows="5"
                      class="rich-text-area"
                    />
                    <!-- Rich Text Editor Controls -->
                    <div class="rich-text-controls mt-2">
                      <div class="d-flex gap-2 align-center">
                        <VBtn
                          variant="text"
                          size="small"
                          class="text-control-btn"
                        >
                          <VIcon icon="tabler-bold" size="16" />
                        </VBtn>
                        <VBtn
                          variant="text"
                          size="small"
                          class="text-control-btn"
                        >
                          <VIcon icon="tabler-italic" size="16" />
                        </VBtn>
                        <VBtn
                          variant="text"
                          size="small"
                          class="text-control-btn"
                        >
                          <VIcon icon="tabler-underline" size="16" />
                        </VBtn>
                        <VBtn
                          variant="text"
                          size="small"
                          class="text-control-btn"
                        >
                          <VIcon icon="tabler-strikethrough" size="16" />
                        </VBtn>
                        <VDivider vertical />
                        <VBtn
                          variant="text"
                          size="small"
                          class="text-control-btn"
                        >
                          <VIcon icon="tabler-list" size="16" />
                        </VBtn>
                        <VBtn
                          variant="text"
                          size="small"
                          class="text-control-btn"
                        >
                          <VIcon icon="tabler-list-numbers" size="16" />
                        </VBtn>
                        <VDivider vertical />
                        <VBtn
                          variant="text"
                          size="small"
                          class="text-control-btn"
                        >
                          <VIcon icon="tabler-align-left" size="16" />
                        </VBtn>
                        <VBtn
                          variant="text"
                          size="small"
                          class="text-control-btn"
                        >
                          <VIcon icon="tabler-align-center" size="16" />
                        </VBtn>
                        <VBtn
                          variant="text"
                          size="small"
                          class="text-control-btn"
                        >
                          <VIcon icon="tabler-align-right" size="16" />
                        </VBtn>
                        <VDivider vertical />
                        <VBtn
                          variant="text"
                          size="small"
                          class="text-control-btn"
                        >
                          <VIcon icon="tabler-link" size="16" />
                        </VBtn>
                        <VBtn
                          variant="text"
                          size="small"
                          class="text-control-btn"
                        >
                          <VIcon icon="tabler-photo" size="16" />
                        </VBtn>
                      </div>
                    </div>
                    <p
                      class="text-caption text-error mt-2 mb-0"
                      style="font-size: 11px"
                    >
                      Note: Please add at least 500 characters.
                    </p>
                  </div>

                  <!-- Locations -->
                  <AppSelect
                    v-model="formData.locations"
                    label="Locations*"
                    placeholder="Were the adventure takes place (country/continent)"
                    :items="[
                      'Europe',
                      'North America',
                      'South America',
                      'Asia',
                      'Africa',
                      'Australia',
                      'Antarctica',
                    ]"
                    class="mb-4"
                  />

                  <!-- Group Language -->
                  <AppSelect
                    v-model="formData.groupLanguage"
                    label="Group language"
                    placeholder="Language(s) the adventure will be conducted in"
                    :items="[
                      'English',
                      'German',
                      'French',
                      'Spanish',
                      'Italian',
                      'Portuguese',
                      'Russian',
                      'Chinese',
                      'Japanese',
                      'Arabic',
                      'Other',
                    ]"
                    class="mb-4"
                  />
                </VCol>

                <!-- Right column -->
                <VCol cols="12" md="6">
                  <!-- Subtitle -->
                  <AppTextField
                    v-model="formData.subtitle"
                    label="Subtitle"
                    placeholder="A tagline for the listing"
                    class="mb-4"
                  />

                  <!-- Activities Included -->
                  <AppSelect
                    v-model="formData.activitiesIncluded"
                    label="Activities included*"
                    placeholder="Select the activities that are included in your adventure"
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
                      'Cooking Classes',
                      'Wine Tasting',
                      'Historical Tours',
                      'Adventure Sports',
                      'Other',
                    ]"
                    class="mb-4"
                  />

                  <!-- Listing Departures -->
                  <div class="mb-6">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Listing departures <span class="required-star">*</span>
                    </label>
                    <p
                      class="text-caption text-medium-emphasis mb-3"
                      style="font-size: 12px"
                    >
                      Add your departures and all related details here
                    </p>
                    <VBtn
                      color="primary"
                      variant="elevated"
                      class="add-departure-btn"
                      style="
                        background-color: #ec8d22 !important;
                        color: #fff !important;
                        border-radius: 8px;
                        font-weight: 500;
                      "
                    >
                      Add Departure
                    </VBtn>
                  </div>

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
            <!-- Step 2: Date Range -->
            <VWindowItem>
              <VRow>
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="formData.startDate"
                    label="Available From"
                    type="date"
                    class="mb-4"
                  />
                  <AppTextField
                    v-model="formData.endDate"
                    label="Available Until"
                    type="date"
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

/* Required star styling */
.required-star {
  color: #ff4444;
  font-weight: bold;
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

/* Rich text editor controls */
.rich-text-controls {
  background: #f8f9fa;
  border: 1px solid #e0e0e0;
  border-top: none;
  border-radius: 0 0 8px 8px;
  padding: 8px 12px;
}

.text-control-btn {
  min-width: 32px !important;
  height: 32px !important;
  border-radius: 4px !important;
  color: #666 !important;
}

.text-control-btn:hover {
  background: #e9ecef !important;
  color: #333 !important;
}

/* Question icon styling */
.question-icon {
  color: #666;
  margin-left: 4px;
  cursor: help;
}

/* Add departure button styling */
.add-departure-btn {
  min-height: 40px;
  padding: 0 20px;
  font-size: 14px;
  text-transform: none;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.add-departure-btn:hover {
  background-color: #d67d1a !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Form field styling */
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

/* Radio button styling */
.v-radio .v-selection-control__input {
  color: #ec8d22;
}

.v-radio .v-selection-control__input .v-icon {
  color: #ec8d22;
}

/* Improved form field styling */
.v-text-field,
.v-textarea,
.v-select {
  border-radius: 8px;
}

.v-text-field .v-field__outline,
.v-textarea .v-field__outline,
.v-select .v-field__outline {
  border-color: #e0e0e0;
}

.v-text-field .v-field--focused .v-field__outline,
.v-textarea .v-field--focused .v-field__outline,
.v-select .v-field--focused .v-field__outline {
  border-color: #ec8d22;
}

/* Select field styling */
.v-select .v-field__append-inner {
  color: #666;
}

/* Divider styling */
.v-divider--vertical {
  height: 20px;
  margin: 0 4px;
  border-color: #e0e0e0;
}
</style>
