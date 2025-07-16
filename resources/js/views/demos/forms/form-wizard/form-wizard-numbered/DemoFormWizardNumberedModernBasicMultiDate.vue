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
  mapsAndRoutes: [""],
  listingMedia: [""],
  promotionalVideo: [""],
  whatsIncluded: "",
  whatsNotIncluded: "",
  additionalNotes: "",
  providersFAQ: "",
  // Step 3 fields
  price: "",
  maxParticipants: "",
  requirements: "",
  termsAccepted: false,
  personalPolicies: "not-sure",
});

// Add new maps and routes field
const addMapsAndRoutes = () => {
  formData.value.mapsAndRoutes.push("");
};

// Remove maps and routes field
const removeMapsAndRoutes = (index) => {
  if (formData.value.mapsAndRoutes.length > 1) {
    formData.value.mapsAndRoutes.splice(index, 1);
  }
};

// Add new listing media field
const addListingMedia = () => {
  formData.value.listingMedia.push("");
};

// Remove listing media field
const removeListingMedia = (index) => {
  if (formData.value.listingMedia.length > 1) {
    formData.value.listingMedia.splice(index, 1);
  }
};

// Add new promotional video field
const addPromotionalVideo = () => {
  formData.value.promotionalVideo.push("");
};

// Remove promotional video field
const removePromotionalVideo = (index) => {
  if (formData.value.promotionalVideo.length > 1) {
    formData.value.promotionalVideo.splice(index, 1);
  }
};

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
                <!-- Top Row -->
                <VCol cols="12" md="6">
                  <!-- Experience Media -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Experience Media <span class="required-star">*</span>
                    </label>
                    <div
                      v-for="(media, index) in formData.listingMedia"
                      :key="index"
                      class="mb-3"
                    >
                      <div class="d-flex gap-2">
                        <AppTextField
                          v-model="formData.listingMedia[index]"
                          placeholder="Add all related images and videos to a drive and add the URL here"
                          class="flex-grow-1"
                        >
                          <template #append-inner>
                            <VBtn
                              v-if="index === formData.listingMedia.length - 1"
                              variant="text"
                              size="small"
                              @click="addListingMedia"
                              class="px-0"
                            >
                              <VIcon icon="tabler-plus" size="20" />
                            </VBtn>
                          </template>
                        </AppTextField>
                        <VBtn
                          v-if="formData.listingMedia.length > 1"
                          variant="tonal"
                          size="small"
                          color="error"
                          @click="removeListingMedia(index)"
                          class="mt-1"
                        >
                          <VIcon icon="tabler-minus" size="16" />
                        </VBtn>
                      </div>
                    </div>
                    <p
                      class="text-caption text-error mt-2 mb-0"
                      style="font-size: 11px"
                    >
                      Note: Make it public or Share the access with
                      id@ExplorerElite.com
                    </p>
                  </div>
                </VCol>
                <VCol cols="12" md="6">
                  <!-- Promotional Video -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Promotional Video
                    </label>
                    <div
                      v-for="(video, index) in formData.promotionalVideo"
                      :key="index"
                      class="mb-3"
                    >
                      <div class="d-flex gap-2">
                        <AppTextField
                          v-model="formData.promotionalVideo[index]"
                          placeholder="Add your promotional video of the adventure here"
                          class="flex-grow-1"
                        >
                          <template #append-inner>
                            <VBtn
                              v-if="
                                index === formData.promotionalVideo.length - 1
                              "
                              variant="text"
                              size="small"
                              @click="addPromotionalVideo"
                              class="px-0"
                            >
                              <VIcon icon="tabler-plus" size="20" />
                            </VBtn>
                          </template>
                        </AppTextField>
                        <VBtn
                          v-if="formData.promotionalVideo.length > 1"
                          variant="tonal"
                          size="small"
                          color="error"
                          @click="removePromotionalVideo(index)"
                          class="mt-1"
                        >
                          <VIcon icon="tabler-minus" size="16" />
                        </VBtn>
                      </div>
                    </div>
                  </div>
                </VCol>

                <!-- Second Row -->
                <VCol cols="12" md="6">
                  <!-- Maps and Routes -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Maps and Routes <span class="required-star">*</span>
                    </label>
                    <div
                      v-for="(route, index) in formData.mapsAndRoutes"
                      :key="index"
                      class="mb-3"
                    >
                      <div class="d-flex gap-2">
                        <AppTextField
                          v-model="formData.mapsAndRoutes[index]"
                          placeholder="Add multiple URL of the approximate routes, GPX or others"
                          class="flex-grow-1"
                        >
                          <template #append-inner>
                            <VBtn
                              v-if="index === formData.mapsAndRoutes.length - 1"
                              variant="text"
                              size="small"
                              @click="addMapsAndRoutes"
                              class="px-0"
                            >
                              <VIcon icon="tabler-plus" size="20" />
                            </VBtn>
                          </template>
                        </AppTextField>
                        <VBtn
                          v-if="formData.mapsAndRoutes.length > 1"
                          variant="tonal"
                          size="small"
                          color="error"
                          @click="removeMapsAndRoutes(index)"
                          class="mt-1"
                        >
                          <VIcon icon="tabler-minus" size="16" />
                        </VBtn>
                      </div>
                    </div>
                  </div>
                </VCol>
                <VCol cols="12" md="6">
                  <!-- Locations -->
                  <AppSelect
                    v-model="formData.locations"
                    label="Locations*"
                    placeholder="where the adventure takes place (countries/continent)"
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
                </VCol>

                <!-- Third Row - Large Text Areas -->
                <VCol cols="12" md="6">
                  <!-- What's Included -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      What's Included <span class="required-star">*</span>
                    </label>
                    <VTextarea
                      v-model="formData.whatsIncluded"
                      placeholder="List of items/services included in the adventure"
                      rows="4"
                      class="rich-text-area"
                    />
                  </div>
                </VCol>
                <VCol cols="12" md="6">
                  <!-- What's Not Included -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      What's Not Included <span class="required-star">*</span>
                    </label>
                    <VTextarea
                      v-model="formData.whatsNotIncluded"
                      placeholder="List of items/services not included in the adventure"
                      rows="4"
                      class="rich-text-area"
                    />
                  </div>
                </VCol>

                <!-- Fourth Row - Rich Text Areas -->
                <VCol cols="12" md="6">
                  <!-- Additional Notes -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Additional Notes to Explorer Elite's Admin
                    </label>
                    <VTextarea
                      v-model="formData.additionalNotes"
                      placeholder="Tell us any further information we should have about your adventure"
                      rows="4"
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
                          <VIcon icon="tabler-list" size="16" />
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
                  </div>
                </VCol>
                <VCol cols="12" md="6">
                  <!-- Provider's Personal FAQ -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Provider's Personal FAQ
                    </label>
                    <VTextarea
                      v-model="formData.providersFAQ"
                      placeholder="Answer all the questions you think adventurers might ask"
                      rows="4"
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
                          <VIcon icon="tabler-list" size="16" />
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
                  </div>
                </VCol>
              </VRow>
            </VWindowItem>
            <!-- Step 3: Pricing & Details -->
            <VWindowItem>
              <VRow>
                <!-- Left Column -->
                <VCol cols="12" md="6">
                  <!-- Itinerary/Accommodation Day by Day -->
                  <div class="mb-6">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Itinerary/Accommodation Day by Day
                      <span class="required-star">*</span>
                    </label>
                    <p
                      class="text-caption text-medium-emphasis mb-3"
                      style="font-size: 12px"
                    >
                      Add your Itinerary/Accommodations and all related details
                      here
                    </p>
                    <VBtn
                      color="primary"
                      variant="elevated"
                      class="add-itinerary-btn"
                      style="
                        background-color: #ec8d22 !important;
                        color: #fff !important;
                        border-radius: 8px;
                        font-weight: 500;
                      "
                    >
                      Add Itinerary/Accomodation
                    </VBtn>
                  </div>

                  <!-- Provider's Personal Policies -->
                  <div class="mb-6">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Provider's Personal Policies
                      <span class="required-star">*</span>
                    </label>
                    <p
                      class="text-caption text-medium-emphasis mb-3"
                      style="font-size: 12px"
                    >
                      How do you want to add policies?
                    </p>
                    <VRadioGroup
                      v-model="formData.personalPolicies"
                      class="mt-2"
                    >
                      <VRadio
                        value="personal"
                        label="I have my personal policies"
                      />
                      <VRadio
                        value="explorer-elite"
                        label="I want to use Explorer Elite's policies"
                      />
                      <VRadio value="not-sure" label="I'm not sure" />
                    </VRadioGroup>
                  </div>
                </VCol>

                <!-- Right Column -->
                <VCol cols="12" md="6">
                  <!-- Special Addons -->
                  <div class="mb-6">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Special Addons
                    </label>
                    <p
                      class="text-caption text-medium-emphasis mb-3"
                      style="font-size: 12px"
                    >
                      Add your Special addons and all related details here
                    </p>
                    <VBtn
                      color="primary"
                      variant="elevated"
                      class="add-addons-btn"
                      style="
                        background-color: #ec8d22 !important;
                        color: #fff !important;
                        border-radius: 8px;
                        font-weight: 500;
                      "
                    >
                      Add Special Addons
                    </VBtn>
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
              {{ loading ? "Submitting..." : "Submit" }}
            </VBtn>
            <VBtn v-else class="next-btn-dark" @click="currentStep++">
              Next
              <VIcon icon="tabler-arrow-right" end class="flip-in-rtl" />
            </VBtn>
          </div>

          <!-- Help Section -->
          <div class="help-section mt-8">
            <VRow>
              <VCol cols="12" md="8">
                <div class="help-content">
                  <h3 class="help-title">Need some help?</h3>
                  <p class="help-description">
                    This is the first step towards your next big adventure, but
                    you're not in this alone. Along your trip creation process,
                    we will be right here to help you.
                  </p>
                  <div class="help-buttons">
                    <VBtn
                      color="dark"
                      variant="elevated"
                      class="tutorial-btn"
                      style="
                        background-color: #111 !important;
                        color: #fff !important;
                        border-radius: 8px;
                        font-weight: 500;
                        margin-right: 12px;
                      "
                    >
                      Watch The Tutorial Video
                    </VBtn>
                    <VBtn
                      color="primary"
                      variant="elevated"
                      class="support-btn"
                      style="
                        background-color: #ec8d22 !important;
                        color: #fff !important;
                        border-radius: 8px;
                        font-weight: 500;
                      "
                    >
                      Contact Support
                    </VBtn>
                  </div>
                </div>
              </VCol>
              <VCol cols="12" md="4">
                <div class="help-video">
                  <iframe
                    src="https://www.youtube.com/embed/dQw4w9WgXcQ"
                    title="Tutorial Video"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    class="youtube-video"
                    style="width: 100%; height: 200px; border-radius: 8px"
                  ></iframe>
                </div>
              </VCol>
            </VRow>
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
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 10px;
  padding: 8px;
  background-color: #f5f5f5;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
}

.text-control-btn {
  padding: 4px 8px;
  border-radius: 6px;
  background-color: #fff;
  border: 1px solid #e0e0e0;
  color: #333;
  font-size: 14px;
  font-weight: 500;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background-color 0.2s, border-color 0.2s, color 0.2s;
}

.text-control-btn:hover {
  background-color: #ec8d22;
  border-color: #ec8d22;
  color: #fff;
}

.text-control-btn:active {
  background-color: #d37a1f;
  border-color: #d37a1f;
  color: #fff;
}

.text-control-btn .v-icon {
  font-size: 18px;
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

/* Add itinerary button styling */
.add-itinerary-btn {
  min-height: 40px;
  padding: 0 20px;
  font-size: 14px;
  text-transform: none;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.add-itinerary-btn:hover {
  background-color: #d67d1a !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Add addons button styling */
.add-addons-btn {
  min-height: 40px;
  padding: 0 20px;
  font-size: 14px;
  text-transform: none;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.add-addons-btn:hover {
  background-color: #d67d1a !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Help section styling */
.help-section {
  padding: 32px 0;
  margin-top: 48px;
}

.help-title {
  font-family: "Anton", sans-serif;
  font-size: 28px;
  font-weight: 700;
  color: #2f2b3d;
  margin-bottom: 16px;
  line-height: 1.2;
}

.help-description {
  font-family: "Karla", sans-serif;
  font-size: 16px;
  color: #666;
  line-height: 1.6;
  margin-bottom: 24px;
  max-width: 600px;
}

.help-buttons {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.tutorial-btn,
.support-btn {
  min-height: 44px;
  padding: 0 24px;
  font-size: 14px;
  text-transform: none;
  font-weight: 500;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  transition: all 0.2s ease;
}

.tutorial-btn:hover {
  background-color: #222 !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.support-btn:hover {
  background-color: #d67d1a !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.mountain-image {
  transition: transform 0.2s ease;
}

.mountain-image:hover {
  transform: scale(1.02);
}

/* Responsive design for help section */
@media (max-width: 768px) {
  .help-section {
    padding: 24px 0;
    margin-top: 32px;
  }

  .help-title {
    font-size: 24px;
  }

  .help-description {
    font-size: 14px;
  }

  .help-buttons {
    flex-direction: column;
  }

  .tutorial-btn,
  .support-btn {
    width: 100%;
    justify-content: center;
  }
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

/* Radio button styling */
.v-radio .v-selection-control__input {
  color: #ec8d22;
}

.v-radio .v-selection-control__input .v-icon {
  color: #ec8d22;
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

/* Dynamic field styling */
.dynamic-field-container {
  margin-bottom: 16px;
}

.dynamic-field-container:last-child {
  margin-bottom: 0;
}

/* Plus/Minus button styling */
.v-btn--variant-text.v-btn--size-small {
  min-width: 32px !important;
  height: 32px !important;
  padding: 0 !important;
}

.v-btn--variant-tonal.v-btn--size-small {
  min-width: 32px !important;
  height: 32px !important;
  padding: 0 !important;
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
