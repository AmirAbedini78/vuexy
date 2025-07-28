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
const listingId = ref(null);
const itineraries = ref([]);
const specialAddons = ref([]);
const periods = ref([]);
const editingAddonIndex = ref(-1);
const editingItineraryIndex = ref(-1);
const editingPeriodIndex = ref(-1);
const showConfirmation = ref(false);
const submissionData = ref({
  adventureTitle: "",
  adventureId: "",
});

const showItineraryDialog = ref(false);
const showSpecialAddonsDialog = ref(false);
const showPeriodsDialog = ref(false);

const formData = ref({
  // Step 1 fields
  listingTitle: "",
  subtitle: "",
  description: "",
  activitiesIncluded: "",
  locations: "",
  groupLanguage: "",
  experienceLevel: "",
  fitnessLevel: "",
  // Step 2 fields
  minAdvanceNotice: "",
  maxAdvanceBooking: "",
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
  personalPoliciesText: "",
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
    // Generate random adventure ID
    const randomId = Math.floor(Math.random() * 90000) + 10000; // 5-digit number

    // Set submission data
    submissionData.value = {
      adventureTitle: formData.value.listingTitle || "Adventure Title",
      adventureId: randomId.toString().padStart(5, "0"),
    };

    // Show confirmation page
    showConfirmation.value = true;
  } catch (error) {
    console.error("Submission failed:", error);
    alert("Submission failed: " + (error.message || "Unknown error"));
  } finally {
    loading.value = false;
  }
};

const openSpecialAddonsModal = () => {
  editingAddonIndex.value = -1; // Reset editing index for new addons
  showSpecialAddonsDialog.value = true;
};

const openItineraryModal = () => {
  editingItineraryIndex.value = -1; // Reset editing index for new itineraries
  showItineraryDialog.value = true;
};

const openPeriodsModal = () => {
  editingPeriodIndex.value = -1; // Reset editing index for new periods
  showPeriodsDialog.value = true;
};

const createNewAdventure = () => {
  // Reset form data
  formData.value = {
    listingTitle: "",
    subtitle: "",
    description: "",
    activitiesIncluded: "",
    locations: "",
    groupLanguage: "",
    experienceLevel: "",
    fitnessLevel: "",
    minAdvanceNotice: "",
    maxAdvanceBooking: "",
    availableDays: [],
    mapsAndRoutes: [""],
    listingMedia: [""],
    promotionalVideo: [""],
    whatsIncluded: "",
    whatsNotIncluded: "",
    additionalNotes: "",
    providersFAQ: "",
    price: "",
    maxParticipants: "",
    requirements: "",
    termsAccepted: false,
    personalPolicies: "not-sure",
    personalPoliciesText: "",
  };

  // Reset other data
  itineraries.value = [];
  specialAddons.value = [];
  currentStep.value = 0;
  showConfirmation.value = false;
};

// Handle itinerary completion
const handleItineraryDone = (itineraryData, editingIndex = -1) => {
  try {
    if (editingIndex >= 0) {
      // Editing existing itinerary
      itineraries.value[editingIndex] = itineraryData[0];
      editingItineraryIndex.value = -1;
    } else {
      // Adding new itineraries
      if (itineraries.value.length > 0) {
        const newItineraries = itineraryData.filter((newItinerary) => {
          return !itineraries.value.some(
            (existingItinerary) =>
              existingItinerary.title === newItinerary.title &&
              existingItinerary.description === newItinerary.description &&
              existingItinerary.accommodation === newItinerary.accommodation &&
              existingItinerary.location === newItinerary.location
          );
        });

        if (newItineraries.length > 0) {
          itineraries.value = [...itineraries.value, ...newItineraries];
        }
      } else {
        itineraries.value = itineraryData;
      }
    }

    // Update the numbering for all itineraries
    itineraries.value.forEach((itinerary, index) => {
      itinerary.number = index + 1;
    });
  } catch (error) {
    console.error("Error in handleItineraryDone:", error);
    alert("خطا در ذخیره اطلاعات");
  } finally {
    showItineraryDialog.value = false;
  }
};

// Handle special addon completion
const handleSpecialAddonDone = async (addons, editingIndex) => {
  try {
    console.log("handleSpecialAddonDone called with:", {
      addons,
      editingIndex,
    });

    if (editingIndex >= 0) {
      // Editing existing addon
      specialAddons.value[editingIndex] = addons[0];
    } else {
      // Adding new addons
      specialAddons.value.push(...addons);
    }

    console.log("Total addons count:", specialAddons.value.length);
  } catch (error) {
    console.error("Error in handleSpecialAddonDone:", error);
    alert("خطا در ذخیره اطلاعات");
  } finally {
    // همیشه مدال را ببند
    showSpecialAddonsDialog.value = false;
  }
};

const handlePeriodsDone = async (newPeriods, editingIndex) => {
  try {
    console.log("handlePeriodsDone called with:", { newPeriods, editingIndex });

    if (editingIndex >= 0) {
      // Editing existing period
      periods.value[editingIndex] = newPeriods[0];
      editingPeriodIndex.value = -1;
    } else {
      // Adding new periods
      periods.value.push(...newPeriods);
    }

    // Update the numbering for all periods
    periods.value.forEach((period, index) => {
      period.number = index + 1;
    });

    console.log("Total periods count:", periods.value.length);
  } catch (error) {
    console.error("Error in handlePeriodsDone:", error);
    alert("خطا در ذخیره اطلاعات");
  } finally {
    // همیشه مدال را ببند
    showPeriodsDialog.value = false;
  }
};

// Edit special addon
const editSpecialAddon = (index) => {
  editingAddonIndex.value = index;
  showSpecialAddonsDialog.value = true;
};

// Remove special addon
const removeSpecialAddon = (index) => {
  if (confirm("آیا مطمئن هستید که می‌خواهید این افزونه را حذف کنید؟")) {
    specialAddons.value.splice(index, 1);
    // Update numbers
    specialAddons.value.forEach((addon, idx) => {
      addon.number = idx + 1;
    });
  }
};

// Edit itinerary item
const editItineraryItem = (index) => {
  editingItineraryIndex.value = index;
  showItineraryDialog.value = true;
};

// Remove itinerary
const removeItinerary = (index) => {
  if (confirm("آیا مطمئن هستید که می‌خواهید این روز را حذف کنید؟")) {
    itineraries.value.splice(index, 1);
    // Update numbers
    itineraries.value.forEach((itinerary, idx) => {
      itinerary.number = idx + 1;
    });
  }
};

// Edit period
const editPeriod = (index) => {
  editingPeriodIndex.value = index;
  showPeriodsDialog.value = true;
};

// Remove period
const removePeriod = (index) => {
  if (confirm("آیا مطمئن هستید که می‌خواهید این روز را حذف کنید؟")) {
    periods.value.splice(index, 1);
    // Update numbers
    periods.value.forEach((period, idx) => {
      period.number = idx + 1;
    });
  }
};

// Drag and drop handlers
const dragStartItinerary = (index, event) => {
  event.dataTransfer.setData("text/plain", index);
  event.dataTransfer.effectAllowed = "move";
};

const dropItinerary = (index, event) => {
  event.preventDefault();
  const draggedIndex = event.dataTransfer.getData("text/plain");
  const [draggedItem] = itineraries.value.splice(draggedIndex, 1);
  itineraries.value.splice(index, 0, draggedItem);
  itineraries.value.forEach((itinerary, idx) => {
    itinerary.number = idx + 1;
  });
};

const dragStartSpecialAddon = (index, event) => {
  event.dataTransfer.setData("text/plain", index);
  event.dataTransfer.effectAllowed = "move";
};

const dropSpecialAddon = (index, event) => {
  event.preventDefault();
  const draggedIndex = event.dataTransfer.getData("text/plain");
  const [draggedItem] = specialAddons.value.splice(draggedIndex, 1);
  specialAddons.value.splice(index, 0, draggedItem);
  specialAddons.value.forEach((addon, idx) => {
    addon.number = idx + 1;
  });
};

const dragStartPeriod = (index, event) => {
  event.dataTransfer.setData("text/plain", index);
  event.dataTransfer.effectAllowed = "move";
};

const dropPeriod = (index, event) => {
  event.preventDefault();
  const draggedIndex = event.dataTransfer.getData("text/plain");
  const [draggedItem] = periods.value.splice(draggedIndex, 1);
  periods.value.splice(index, 0, draggedItem);
  periods.value.forEach((period, idx) => {
    period.number = idx + 1;
  });
};
</script>

<template>
  <!-- Confirmation Page -->
  <div v-if="showConfirmation" class="confirmation-page">
    <div class="confirmation-container">
      <div class="confirmation-card">
        <!-- Icon -->
        <div class="confirmation-icon">
          <div class="icon-container">
            <div class="icon-left">
              <div class="icon-lines">
                <div class="line"></div>
                <div class="line"></div>
                <div class="line"></div>
              </div>
              <div class="checkmark">✓</div>
            </div>
            <div class="icon-right">
              <div class="icon-lines">
                <div class="line"></div>
                <div class="line"></div>
                <div class="line"></div>
              </div>
              <div class="cross">✗</div>
            </div>
          </div>
        </div>

        <!-- Main Heading -->
        <h1 class="confirmation-title">
          Your Listing Just Submitted for Approval
        </h1>

        <!-- Dynamic Information -->
        <div class="adventure-info">
          <div class="adventure-title">{{ submissionData.adventureTitle }}</div>
          <div class="adventure-id">
            Adventure ID : {{ submissionData.adventureId }}
          </div>
        </div>

        <!-- Descriptive Text -->
        <p class="confirmation-description">
          Thank you for submitting your adventure! Our team is reviewing your
          listing, and we'll notify you once it's approved or if any changes are
          needed. In the meantime, you can view your submitted adventures or
          create a new one.
        </p>

        <!-- Action Buttons -->
        <div class="confirmation-buttons">
          <VBtn
            color="dark"
            variant="elevated"
            class="see-adventures-btn"
            @click="router.push({ name: 'listing' })"
          >
            See All Your Adventures
          </VBtn>
          <VBtn
            color="primary"
            variant="elevated"
            class="add-another-btn"
            @click="createNewAdventure"
          >
            Add Another Adventure
          </VBtn>
        </div>
      </div>
    </div>
  </div>

  <!-- Wizard Content -->
  <div v-else>
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

                      <!-- Show button when no items -->
                      <div
                        v-if="periods.length === 0"
                        class="empty-state-container"
                      >
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
                          @click="openPeriodsModal"
                        >
                          Add Departure
                        </VBtn>
                      </div>

                      <!-- Show header with button when items exist -->
                      <div v-else class="departures-header">
                        <h3 class="departures-title">Listing Departures</h3>
                        <VBtn
                          color="primary"
                          variant="elevated"
                          class="add-more-btn"
                          style="
                            background-color: #ec8d22 !important;
                            color: #fff !important;
                            border-radius: 8px;
                            font-weight: 500;
                            font-size: 14px;
                            padding: 8px 16px;
                            min-height: 36px;
                            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
                          "
                          @click="openPeriodsModal"
                        >
                          Add More
                        </VBtn>
                      </div>

                      <!-- Departures List -->
                      <div
                        v-if="periods.length > 0"
                        class="departures-list-container"
                      >
                        <div
                          v-for="(period, index) in periods"
                          :key="index"
                          class="departure-list-item"
                          draggable="true"
                          @dragstart="dragStartPeriod(index, $event)"
                          @dragover.prevent
                          @drop="dropPeriod(index, $event)"
                          @dragenter.prevent
                        >
                          <div class="departure-content">
                            <div class="departure-left">
                              <div
                                class="departure-badge"
                                style="position: relative"
                              >
                                <span class="badge-number">{{
                                  (period.number || index + 1)
                                    .toString()
                                    .padStart(2, "0")
                                }}</span>
                                <!-- Calendar icon under number badge -->
                                <div
                                  v-if="index < periods.length - 1"
                                  style="
                                    position: absolute;
                                    left: 50%;
                                    top: 100%;
                                    transform: translateX(-50%);
                                    margin-top: 4px;
                                    z-index: 2;
                                  "
                                >
                                  <VIcon
                                    icon="tabler-calendar"
                                    size="16"
                                    style="color: #ec8d22"
                                  />
                                </div>
                                <!-- Vertical dotted line under icon -->
                                <div
                                  v-if="index < periods.length - 1"
                                  class="vertical-dotted-line"
                                  style="
                                    position: absolute;
                                    left: 50%;
                                    top: 100%;
                                    transform: translateX(-50%);
                                    margin-top: 20px;
                                    bottom: -16px;
                                    width: 1px;
                                    background: repeating-linear-gradient(
                                      to bottom,
                                      #666 0,
                                      #666 2px,
                                      transparent 2px,
                                      transparent 4px
                                    );
                                  "
                                ></div>
                              </div>
                              <div class="departure-info">
                                <div class="departure-title-row">
                                  <span class="departure-title">{{
                                    period.title ||
                                    `Departure ${
                                      period.number || index + 1
                                    } title`
                                  }}</span>
                                  <VIcon
                                    icon="tabler-chevron-down"
                                    size="16"
                                    class="chevron-icon"
                                  />
                                </div>
                                <div class="departure-details">
                                  <div class="departure-date">
                                    <VIcon
                                      icon="tabler-target"
                                      size="14"
                                      style="color: #666; margin-right: 4px"
                                    />
                                    <span class="date-text">{{
                                      period.startingDate &&
                                      period.finishingDate
                                        ? `${period.startingDate} - ${period.finishingDate}`
                                        : "Date range not set"
                                    }}</span>
                                  </div>
                                  <div class="departure-people">
                                    <span class="people-text">{{
                                      period.minCapacity && period.maxCapacity
                                        ? `${period.minCapacity} - ${period.maxCapacity} people`
                                        : "Capacity not set"
                                    }}</span>
                                  </div>
                                </div>
                                <div class="departure-price">
                                  € {{ (period.price || 0).toFixed(2) }}
                                </div>
                              </div>
                            </div>
                            <div class="departure-actions">
                              <VBtn
                                icon
                                size="small"
                                variant="text"
                                @click="editPeriod(index)"
                                class="action-btn"
                              >
                                <VIcon icon="tabler-edit" size="18" />
                              </VBtn>
                              <VBtn
                                icon
                                size="small"
                                variant="text"
                                color="error"
                                @click="removePeriod(index)"
                                class="action-btn"
                              >
                                <VIcon icon="tabler-trash" size="18" />
                              </VBtn>
                              <VBtn
                                icon
                                size="small"
                                variant="text"
                                class="action-btn drag-handle"
                                draggable="true"
                                @dragstart="dragStartPeriod(index, $event)"
                              >
                                <VIcon icon="tabler-arrows-move" size="18" />
                              </VBtn>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Experience Description -->
                    <div class="mb-6">
                      <label
                        class="v-label text-body-2 mb-3 d-block"
                        style="
                          font-size: 16px !important;
                          font-weight: 400 !important;
                        "
                      >
                        Experience Description
                        <span class="required-star">*</span>
                      </label>

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
                    </div>
                  </VCol>

                  <!-- Right column -->
                  <VCol cols="12" md="6">
                    <!-- Listing Title -->
                    <AppTextField
                      v-model="formData.listingTitle"
                      label="Listing Title*"
                      placeholder="The main title of Your adventure"
                      class="mb-4"
                    />

                    <!-- Subtitle -->
                    <AppTextField
                      v-model="formData.subtitle"
                      label="Subtitle"
                      placeholder="A tagline for the adventure"
                      class="mb-4"
                    />

                    <!-- Description -->
                    <div class="mb-4">
                      <label
                        class="v-label text-body-2 mb-3 d-block"
                        style="
                          font-size: 16px !important;
                          font-weight: 400 !important;
                        "
                      >
                        Description <span class="required-star">*</span>
                      </label>
                      <VTextarea
                        v-model="formData.description"
                        placeholder="Tell us everything about this adventure"
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
                </VRow>
              </VWindowItem>
              <!-- Step 2: Availability -->
              <VWindowItem>
                <VRow>
                  <!-- Left Column -->
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
                                v-if="
                                  index === formData.listingMedia.length - 1
                                "
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
                                v-if="
                                  index === formData.mapsAndRoutes.length - 1
                                "
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

                  <!-- Right Column -->
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
                      <h3 class="section-title">
                        Itinerary/Accommodation Day by Day*
                      </h3>
                      <p class="section-description">
                        Add your Itinerary/Accommodations and all related
                        details here
                      </p>

                      <!-- Show button when no items -->
                      <div
                        v-if="itineraries.length === 0"
                        class="empty-state-container"
                      >
                        <VBtn
                          color="primary"
                          variant="elevated"
                          class="add-item-btn"
                          @click="openItineraryModal"
                        >
                          Add Itinerary/Accomodation
                        </VBtn>
                      </div>

                      <!-- Show header with button when items exist -->
                      <div v-else class="itinerary-header">
                        <h3 class="itinerary-title">
                          Itinerary/Accommodations
                        </h3>
                        <VBtn
                          color="primary"
                          variant="elevated"
                          class="add-more-btn"
                          style="
                            background-color: #ec8d22 !important;
                            color: #fff !important;
                            border-radius: 8px;
                            font-weight: 500;
                            font-size: 14px;
                            padding: 8px 16px;
                            min-height: 36px;
                            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
                          "
                          @click="openItineraryModal"
                        >
                          Add More
                        </VBtn>
                      </div>

                      <div
                        v-if="itineraries.length > 0"
                        class="itinerary-list-container"
                      >
                        <div
                          v-for="(itinerary, index) in itineraries"
                          :key="itinerary.id"
                          class="itinerary-list-item"
                          style="
                            background: transparent;
                            border: none;
                            border-radius: 0;
                            padding: 16px 0;
                            margin-top: 0;
                            box-shadow: none;
                            position: relative;
                          "
                          draggable="true"
                          @dragstart="dragStartItinerary(index, $event)"
                          @dragover.prevent
                          @drop="dropItinerary(index, $event)"
                          @dragenter.prevent
                        >
                          <div
                            class="d-flex justify-space-between align-center"
                          >
                            <div class="d-flex align-center">
                              <div
                                class="itinerary-number"
                                style="
                                  background: #ec8d22;
                                  color: white;
                                  width: 32px;
                                  height: 32px;
                                  border-radius: 6px;
                                  display: flex;
                                  align-items: center;
                                  justify-content: center;
                                  font-weight: bold;
                                  margin-right: 12px;
                                  font-family: 'Anton', sans-serif;
                                  font-size: 12px;
                                  position: relative;
                                "
                              >
                                {{
                                  (itinerary.number || 1)
                                    .toString()
                                    .padStart(2, "0")
                                }}
                                <!-- Location icon under number badge -->
                                <div
                                  v-if="index < itineraries.length - 1"
                                  style="
                                    position: absolute;
                                    left: 50%;
                                    top: 100%;
                                    transform: translateX(-50%);
                                    margin-top: 4px;
                                    z-index: 2;
                                  "
                                >
                                  <VIcon
                                    icon="tabler-map-pin"
                                    size="16"
                                    style="color: #ec8d22"
                                  />
                                </div>
                                <!-- Vertical dotted line under icon -->
                                <div
                                  v-if="index < itineraries.length - 1"
                                  class="vertical-dotted-line"
                                  style="
                                    position: absolute;
                                    left: 50%;
                                    top: 100%;
                                    transform: translateX(-50%);
                                    margin-top: 20px;
                                    bottom: -16px;
                                    width: 1px;
                                    background: repeating-linear-gradient(
                                      to bottom,
                                      #666 0,
                                      #666 2px,
                                      transparent 2px,
                                      transparent 4px
                                    );
                                  "
                                ></div>
                              </div>
                              <div>
                                <div
                                  class="itinerary-title"
                                  style="
                                    font-weight: 600;
                                    color: #333;
                                    font-family: 'Karla', sans-serif;
                                    font-size: 16px;
                                  "
                                >
                                  {{
                                    itinerary.title ||
                                    `Day ${
                                      itinerary.number || 1
                                    } Itinerary Title`
                                  }}
                                </div>
                                <div
                                  class="itinerary-accommodation"
                                  style="
                                    color: #666;
                                    font-size: 14px;
                                    margin-top: 4px;
                                    font-family: 'Karla', sans-serif;
                                  "
                                >
                                  Day {{ itinerary.number || 1 }} Accommodation
                                </div>
                                <div
                                  class="itinerary-location"
                                  style="
                                    color: #999;
                                    font-size: 12px;
                                    margin-top: 2px;
                                    font-family: 'Karla', sans-serif;
                                  "
                                >
                                  {{
                                    itinerary.location ||
                                    "Location would take place here"
                                  }}
                                </div>
                              </div>
                            </div>
                            <div class="itinerary-actions">
                              <VBtn
                                icon
                                size="small"
                                variant="text"
                                @click="editItineraryItem(index)"
                                style="margin-right: 8px; color: #666"
                              >
                                <VIcon icon="tabler-edit" size="18" />
                              </VBtn>
                              <VBtn
                                icon
                                size="small"
                                variant="text"
                                color="error"
                                @click="removeItinerary(index)"
                                style="margin-right: 8px; color: #666"
                              >
                                <VIcon icon="tabler-trash" size="18" />
                              </VBtn>
                              <VBtn
                                icon
                                size="small"
                                variant="text"
                                style="color: #333"
                                class="drag-handle"
                                draggable="true"
                                @dragstart="dragStartItinerary(index, $event)"
                              >
                                <VIcon icon="tabler-arrows-move" size="18" />
                              </VBtn>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </VCol>

                  <!-- Right Column -->
                  <VCol cols="12" md="6">
                    <!-- Special Addons -->
                    <div class="mb-6">
                      <h3 class="section-title">Special Addons</h3>
                      <p class="section-description">
                        Add your Special addons and all related details here
                      </p>

                      <!-- Show button when no items -->
                      <div
                        v-if="specialAddons.length === 0"
                        class="empty-state-container"
                      >
                        <VBtn
                          color="primary"
                          variant="elevated"
                          class="add-item-btn"
                          @click="openSpecialAddonsModal"
                        >
                          Add Special Addons
                        </VBtn>
                      </div>

                      <!-- Show header with button when items exist -->
                      <div v-else class="special-addons-header">
                        <h3 class="special-addons-title">Special Addons</h3>
                        <VBtn
                          color="primary"
                          variant="elevated"
                          class="add-more-btn"
                          style="
                            background-color: #ec8d22 !important;
                            color: #fff !important;
                            border-radius: 8px;
                            font-weight: 500;
                            font-size: 14px;
                            padding: 8px 16px;
                            min-height: 36px;
                            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
                          "
                          @click="openSpecialAddonsModal"
                        >
                          Add More
                        </VBtn>
                      </div>

                      <div
                        v-if="specialAddons.length > 0"
                        class="special-addons-list"
                      >
                        <div
                          v-for="(addon, index) in specialAddons"
                          :key="index"
                          class="special-addon-item"
                          draggable="true"
                          @dragstart="dragStartSpecialAddon(index, $event)"
                          @dragover.prevent
                          @drop="dropSpecialAddon(index, $event)"
                          @dragenter.prevent
                        >
                          <div class="addon-content">
                            <div class="addon-left">
                              <div
                                class="addon-badge"
                                style="position: relative"
                              >
                                <span class="badge-number">{{
                                  (addon.number || index + 1)
                                    .toString()
                                    .padStart(2, "0")
                                }}</span>
                                <!-- Star icon under number badge -->
                                <div
                                  v-if="index < specialAddons.length - 1"
                                  style="
                                    position: absolute;
                                    left: 50%;
                                    top: 100%;
                                    transform: translateX(-50%);
                                    margin-top: 4px;
                                    z-index: 2;
                                  "
                                >
                                  <VIcon
                                    icon="tabler-star"
                                    size="16"
                                    style="color: #ec8d22"
                                  />
                                </div>
                                <!-- Vertical dotted line under icon -->
                                <div
                                  v-if="index < specialAddons.length - 1"
                                  class="vertical-dotted-line"
                                  style="
                                    position: absolute;
                                    left: 50%;
                                    top: 100%;
                                    transform: translateX(-50%);
                                    margin-top: 20px;
                                    bottom: -16px;
                                    width: 1px;
                                    background: repeating-linear-gradient(
                                      to bottom,
                                      #666 0,
                                      #666 2px,
                                      transparent 2px,
                                      transparent 4px
                                    );
                                  "
                                ></div>
                              </div>
                              <div class="addon-info">
                                <div class="addon-title-row">
                                  <span class="addon-title">{{
                                    addon.title ||
                                    `Addon ${addon.number || index + 1} Title`
                                  }}</span>
                                  <VIcon
                                    icon="tabler-chevron-down"
                                    size="16"
                                    class="chevron-icon"
                                  />
                                </div>
                                <div class="addon-description">
                                  <span class="description-text">{{
                                    addon.description ||
                                    "Your addon description would take place here"
                                  }}</span>
                                </div>
                                <div class="addon-price">
                                  € {{ (addon.price || 0).toFixed(2) }}
                                </div>
                              </div>
                            </div>
                            <div class="addon-actions">
                              <VBtn
                                icon
                                size="small"
                                variant="text"
                                @click="editSpecialAddon(index)"
                                class="action-btn"
                              >
                                <VIcon icon="tabler-edit" size="18" />
                              </VBtn>
                              <VBtn
                                icon
                                size="small"
                                variant="text"
                                color="error"
                                @click="removeSpecialAddon(index)"
                                class="action-btn"
                              >
                                <VIcon icon="tabler-trash" size="18" />
                              </VBtn>
                              <VBtn
                                icon
                                size="small"
                                variant="text"
                                class="action-btn drag-handle"
                                draggable="true"
                                @dragstart="
                                  dragStartSpecialAddon(index, $event)
                                "
                              >
                                <VIcon icon="tabler-arrows-move" size="18" />
                              </VBtn>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </VCol>
                </VRow>

                <!-- Provider's Personal Policies - Full Width -->
                <VRow>
                  <VCol cols="12">
                    <div class="mb-6">
                      <h3 class="section-title">
                        Provider's Personal Policies*
                      </h3>
                      <p class="section-description">
                        How do you want to add policies?
                      </p>
                      <VRadioGroup
                        v-model="formData.personalPolicies"
                        class="mt-2"
                      >
                        <div class="radio-option">
                          <VRadio
                            value="personal"
                            label="I have my personal policies"
                          />
                          <div
                            v-if="formData.personalPolicies === 'personal'"
                            class="mt-2 mb-2"
                          >
                            <VTextarea
                              v-model="formData.personalPoliciesText"
                              placeholder="Add your personal policies"
                              rows="5"
                              class="rich-text-area"
                            />
                            <div class="rich-text-controls mt-2">
                              <div class="d-flex gap-2 align-center">
                                <VBtn
                                  variant="text"
                                  size="small"
                                  class="text-control-btn"
                                  ><VIcon icon="tabler-bold" size="16"
                                /></VBtn>
                                <VBtn
                                  variant="text"
                                  size="small"
                                  class="text-control-btn"
                                  ><VIcon icon="tabler-italic" size="16"
                                /></VBtn>
                                <VBtn
                                  variant="text"
                                  size="small"
                                  class="text-control-btn"
                                  ><VIcon icon="tabler-underline" size="16"
                                /></VBtn>
                                <VBtn
                                  variant="text"
                                  size="small"
                                  class="text-control-btn"
                                  ><VIcon icon="tabler-list" size="16"
                                /></VBtn>
                                <VBtn
                                  variant="text"
                                  size="small"
                                  class="text-control-btn"
                                  ><VIcon icon="tabler-list-numbers" size="16"
                                /></VBtn>
                                <VBtn
                                  variant="text"
                                  size="small"
                                  class="text-control-btn"
                                  ><VIcon icon="tabler-link" size="16"
                                /></VBtn>
                                <VBtn
                                  variant="text"
                                  size="small"
                                  class="text-control-btn"
                                  ><VIcon icon="tabler-photo" size="16"
                                /></VBtn>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="radio-option">
                          <VRadio
                            value="explorer-elite"
                            label="I want to use Explorer Elite's policies"
                          />
                          <div
                            v-if="
                              formData.personalPolicies === 'explorer-elite'
                            "
                            class="mt-2 mb-2"
                          >
                            <a
                              href="#"
                              style="
                                color: #ec8d22;
                                text-decoration: underline;
                                font-size: 16px;
                                margin-left: 32px;
                                display: inline-block;
                              "
                              >Read Explorer Elite Policies</a
                            >
                          </div>
                        </div>
                        <div class="radio-option">
                          <VRadio value="not-sure" label="I'm not sure" />
                          <div
                            v-if="formData.personalPolicies === 'not-sure'"
                            class="mt-3 d-flex gap-3"
                          >
                            <VBtn
                              style="
                                background: #111;
                                color: #fff;
                                font-weight: 600;
                                min-width: 130px;
                                min-height: 44px;
                                font-size: 16px;
                                border-radius: 8px;
                              "
                              >Learn More</VBtn
                            >
                            <VBtn
                              style="
                                background: #ec8d22;
                                color: #fff;
                                font-weight: 600;
                                min-width: 170px;
                                min-height: 44px;
                                font-size: 16px;
                                border-radius: 8px;
                              "
                              >Contact Support</VBtn
                            >
                          </div>
                        </div>
                      </VRadioGroup>
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
          </VForm>
        </VCardText>
      </VCard>
    </div>
  </div>

  <ItineraryAccommodationDialog
    v-model="showItineraryDialog"
    :listing-id="listingId"
    :initial-days="itineraries"
    :editing-index="editingItineraryIndex"
    @close="showItineraryDialog = false"
    @done="handleItineraryDone"
  />
  <SpecialAddonsDialog
    v-model="showSpecialAddonsDialog"
    :special-addons="specialAddons"
    :editing-index="editingAddonIndex"
    @close="showSpecialAddonsDialog = false"
    @done="handleSpecialAddonDone"
  />
  <PeriodsDialog
    v-model="showPeriodsDialog"
    :periods="periods"
    :editing-index="editingPeriodIndex"
    @done="handlePeriodsDone"
  />
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

/* Departures list styling */
.departures-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.departures-title {
  font-family: "Karla", sans-serif;
  font-size: 18px;
  font-weight: 600;
  color: #333;
  margin: 0;
}

.departures-list-container {
  background: #fff;
  border-radius: 12px;
  border: 1px solid #e0e0e0;
  overflow: hidden;
}

.departure-list-item {
  padding: 16px;
  border-bottom: 1px solid #f0f0f0;
  transition: background-color 0.2s;
  cursor: move;
}

.departure-list-item:last-child {
  border-bottom: none;
}

.departure-list-item:hover {
  background-color: #f8f8f8;
}

.departure-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.departure-left {
  display: flex;
  align-items: flex-start;
  flex: 1;
}

.departure-badge {
  background: #ec8d22;
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  margin-right: 12px;
  font-family: "Anton", sans-serif;
  font-size: 12px;
  flex-shrink: 0;
}

.badge-number {
  color: white;
  font-weight: bold;
}

.departure-info {
  flex: 1;
  min-width: 0;
}

.departure-title-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.departure-title {
  font-family: "Karla", sans-serif;
  font-size: 16px;
  font-weight: 600;
  color: #333;
  flex: 1;
  margin-right: 8px;
}

.chevron-icon {
  color: #666;
  cursor: pointer;
  transition: transform 0.2s;
}

.chevron-icon:hover {
  transform: rotate(180deg);
}

.departure-details {
  margin-bottom: 8px;
}

.departure-date {
  display: flex;
  align-items: center;
  margin-bottom: 4px;
}

.date-text {
  font-family: "Karla", sans-serif;
  font-size: 14px;
  color: #666;
}

.departure-people {
  margin-left: 18px;
}

.people-text {
  font-family: "Karla", sans-serif;
  font-size: 14px;
  color: #666;
}

.departure-price {
  font-family: "Karla", sans-serif;
  font-size: 16px;
  font-weight: 600;
  color: #333;
  text-align: right;
}

.departure-actions {
  display: flex;
  align-items: center;
  gap: 4px;
  margin-left: 12px;
}

.action-btn {
  color: #666 !important;
  transition: color 0.2s;
}

.action-btn:hover {
  color: #333 !important;
}

.drag-handle {
  cursor: move;
}

.drag-handle:hover {
  color: #ec8d22 !important;
}

/* Empty state styling */
.empty-state-container {
  text-align: center;
  padding: 24px;
  background: #f8f8f8;
  border-radius: 8px;
  border: 2px dashed #e0e0e0;
}
</style>
