<template>
  <div class="multi-date-wizard">
    <VCard>
      <!-- Header with Step Counter -->
      <VCardTitle class="d-flex align-center justify-space-between">
        <div class="d-flex align-center">
          <span class="text-h6 me-3">Edit Multi Date Listing</span>
          <VChip color="primary" size="small">
            Step {{ currentStep + 1 }} of {{ numberedSteps.length }}
          </VChip>
        </div>
        <VBtn icon variant="text" size="small" @click="$emit('close')">
          <VIcon icon="tabler-x" />
        </VBtn>
      </VCardTitle>

      <VCardText>
        <!-- Vertical Stepper -->
        <VRow>
          <VCol cols="12" md="3">
            <AppStepper
              v-model:current-step="currentStep"
              direction="vertical"
              :items="numberedSteps"
            />
          </VCol>

          <!-- Wizard Content -->
          <VCol cols="12" md="9">
            <VForm>
              <VWindow v-model="currentStep" class="disable-tab-transition">
                <!-- Step 1: Basic Information -->
                <VWindowItem>
                  <VRow>
                    <VCol cols="12">
                      <h6 class="text-h6 font-weight-medium mb-4">
                        {{ getStepTitle(0) }}
                      </h6>
                      <p class="text-body-2 text-medium-emphasis mb-4">
                        {{ getStepDescription(0) }}
                      </p>
                    </VCol>

                    <VCol cols="12" md="6">
                      <VTextField
                        v-model="formData.listingTitle"
                        label="Listing Title *"
                        placeholder="Enter listing title"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="formValidationErrors.listingTitle"
                        @blur="validateField('listingTitle')"
                      />
                    </VCol>

                    <VCol cols="12" md="6">
                      <VTextField
                        v-model="formData.subtitle"
                        label="Subtitle"
                        placeholder="Enter subtitle"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="formValidationErrors.subtitle"
                        @blur="validateField('subtitle')"
                      />
                    </VCol>

                    <VCol cols="12">
                      <VTextarea
                        v-model="formData.listingDescription"
                        label="Listing Description *"
                        placeholder="Enter listing description"
                        variant="outlined"
                        density="comfortable"
                        rows="4"
                        :error-messages="
                          formValidationErrors.listingDescription
                        "
                        @blur="validateField('listingDescription')"
                      />
                    </VCol>

                    <VCol cols="12" md="6">
                      <VTextField
                        v-model="formData.locations"
                        label="Locations"
                        placeholder="Enter locations"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="formValidationErrors.locations"
                        @blur="validateField('locations')"
                      />
                    </VCol>

                    <VCol cols="12" md="6">
                      <VSelect
                        v-model="formData.groupLanguage"
                        label="Group Language"
                        :items="groupLanguageOptions"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="formValidationErrors.groupLanguage"
                        @update:model-value="validateField('groupLanguage')"
                      />
                    </VCol>

                    <VCol cols="12">
                      <VTextarea
                        v-model="formData.activitiesIncluded"
                        label="Activities Included *"
                        placeholder="Enter activities included"
                        variant="outlined"
                        density="comfortable"
                        rows="3"
                        :error-messages="
                          formValidationErrors.activitiesIncluded
                        "
                        @blur="validateField('activitiesIncluded')"
                      />
                    </VCol>

                    <VCol cols="12" md="6">
                      <VSelect
                        v-model="formData.experienceLevel"
                        label="Experience Level"
                        :items="experienceLevelOptions"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="formValidationErrors.experienceLevel"
                        @update:model-value="validateField('experienceLevel')"
                      />
                    </VCol>

                    <VCol cols="12" md="6">
                      <VSelect
                        v-model="formData.fitnessLevel"
                        label="Fitness Level"
                        :items="fitnessLevelOptions"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="formValidationErrors.fitnessLevel"
                        @update:model-value="validateField('fitnessLevel')"
                      />
                    </VCol>
                  </VRow>
                </VWindowItem>

                <!-- Step 2: Date Range -->
                <VWindowItem>
                  <VRow>
                    <VCol cols="12">
                      <h6 class="text-h6 font-weight-medium mb-4">
                        {{ getStepTitle(1) }}
                      </h6>
                      <p class="text-body-2 text-medium-emphasis mb-4">
                        {{ getStepDescription(1) }}
                      </p>
                    </VCol>

                    <VCol cols="12" md="6">
                      <VTextField
                        v-model="formData.startDate"
                        label="Start Date *"
                        type="date"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="formValidationErrors.startDate"
                        @blur="validateField('startDate')"
                      />
                    </VCol>

                    <VCol cols="12" md="6">
                      <VTextField
                        v-model="formData.endDate"
                        label="End Date *"
                        type="date"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="formValidationErrors.endDate"
                        @blur="validateField('endDate')"
                      />
                    </VCol>

                    <VCol cols="12">
                      <VSelect
                        v-model="formData.availableDays"
                        label="Available Days"
                        :items="availableDaysOptions"
                        multiple
                        variant="outlined"
                        density="comfortable"
                        :error-messages="formValidationErrors.availableDays"
                        @update:model-value="validateField('availableDays')"
                      />
                    </VCol>

                    <VCol cols="12">
                      <VTextarea
                        v-model="formData.whatsIncluded"
                        label="What's Included"
                        placeholder="Enter what's included"
                        variant="outlined"
                        density="comfortable"
                        rows="3"
                        :error-messages="formValidationErrors.whatsIncluded"
                        @blur="validateField('whatsIncluded')"
                      />
                    </VCol>

                    <VCol cols="12">
                      <VTextarea
                        v-model="formData.whatsNotIncluded"
                        label="What's Not Included"
                        placeholder="Enter what's not included"
                        variant="outlined"
                        density="comfortable"
                        rows="3"
                        :error-messages="formValidationErrors.whatsNotIncluded"
                        @blur="validateField('whatsNotIncluded')"
                      />
                    </VCol>

                    <VCol cols="12">
                      <VTextarea
                        v-model="formData.additionalNotes"
                        label="Additional Notes"
                        placeholder="Enter additional notes"
                        variant="outlined"
                        density="comfortable"
                        rows="3"
                        :error-messages="formValidationErrors.additionalNotes"
                        @blur="validateField('additionalNotes')"
                      />
                    </VCol>

                    <VCol cols="12">
                      <VTextarea
                        v-model="formData.providersFAQ"
                        label="Provider's FAQ"
                        placeholder="Enter provider's FAQ"
                        variant="outlined"
                        density="comfortable"
                        rows="3"
                        :error-messages="formValidationErrors.providersFAQ"
                        @blur="validateField('providersFAQ')"
                      />
                    </VCol>

                    <!-- Media and Advanced Sections -->
                    <VCol cols="12">
                      <h6 class="text-h6 font-weight-medium mb-3">Media</h6>
                    </VCol>

                    <VCol cols="12" md="6">
                      <VTextarea
                        v-model="formData.listingMediaString"
                        label="Listing Media (JSON array)"
                        placeholder='e.g. ["/img1.jpg","/img2.jpg"]'
                        variant="outlined"
                        density="comfortable"
                        rows="3"
                      />
                    </VCol>

                    <VCol cols="12" md="6">
                      <VTextarea
                        v-model="formData.promotionalVideoString"
                        label="Promotional Video URLs (JSON array)"
                        placeholder='e.g. ["https://youtu.be/..."]'
                        variant="outlined"
                        density="comfortable"
                        rows="3"
                      />
                    </VCol>

                    <VCol cols="12">
                      <VTextarea
                        v-model="formData.mapsAndRoutesString"
                        label="Maps and Routes (JSON array)"
                        placeholder='e.g. [{"title":"Route A","link":"..."}]'
                        variant="outlined"
                        density="comfortable"
                        rows="3"
                      />
                    </VCol>

                    <!-- Wizard-specific modals triggers -->
                    <VCol cols="12">
                      <h6 class="text-h6 font-weight-medium mb-3 mt-4">
                        Advanced Configuration
                      </h6>
                      <div class="d-flex flex-wrap gap-3">
                        <VBtn
                          color="primary"
                          variant="elevated"
                          @click="openItineraryModal"
                        >
                          Manage Itinerary / Accommodation
                        </VBtn>
                        <VBtn
                          color="primary"
                          variant="elevated"
                          @click="openSpecialAddonsModal"
                        >
                          Manage Special Addons
                        </VBtn>
                        <VBtn
                          color="primary"
                          variant="elevated"
                          @click="openPeriodsModal"
                        >
                          Manage Departures
                        </VBtn>
                      </div>
                    </VCol>
                  </VRow>
                </VWindowItem>

                <!-- Step 3: Pricing & Details -->
                <VWindowItem>
                  <VRow>
                    <VCol cols="12">
                      <h6 class="text-h6 font-weight-medium mb-4">
                        {{ getStepTitle(2) }}
                      </h6>
                      <p class="text-body-2 text-medium-emphasis mb-4">
                        {{ getStepDescription(2) }}
                      </p>
                    </VCol>

                    <VCol cols="12" md="6">
                      <VTextField
                        v-model="formData.price"
                        label="Price *"
                        type="number"
                        placeholder="Enter price"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="formValidationErrors.price"
                        @blur="validateField('price')"
                      />
                    </VCol>

                    <VCol cols="12" md="6">
                      <VTextField
                        v-model="formData.minParticipants"
                        label="Minimum Participants"
                        type="number"
                        placeholder="Enter minimum participants"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="formValidationErrors.minParticipants"
                        @blur="validateField('minParticipants')"
                      />
                    </VCol>

                    <VCol cols="12" md="6">
                      <VTextField
                        v-model="formData.maxParticipants"
                        label="Maximum Participants *"
                        type="number"
                        placeholder="Enter max participants"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="formValidationErrors.maxParticipants"
                        @blur="validateField('maxParticipants')"
                      />
                    </VCol>

                    <VCol cols="12">
                      <VTextarea
                        v-model="formData.requirements"
                        label="Requirements"
                        placeholder="Enter requirements"
                        variant="outlined"
                        density="comfortable"
                        rows="3"
                        :error-messages="formValidationErrors.requirements"
                        @blur="validateField('requirements')"
                      />
                    </VCol>

                    <VCol cols="12" md="6">
                      <VSelect
                        v-model="formData.personalPolicies"
                        label="Personal Policies"
                        :items="personalPoliciesOptions"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="formValidationErrors.personalPolicies"
                        @update:model-value="validateField('personalPolicies')"
                      />
                    </VCol>

                    <VCol cols="12" md="6">
                      <VSelect
                        v-model="formData.termsAccepted"
                        label="Terms Accepted"
                        :items="termsOptions"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="formValidationErrors.termsAccepted"
                        @update:model-value="validateField('termsAccepted')"
                      />
                    </VCol>
                  </VRow>
                </VWindowItem>
              </VWindow>
            </VForm>
          </VCol>
        </VRow>
      </VCardText>

      <!-- Action Buttons -->
      <VDivider />
      <VCardActions class="pa-6">
        <VBtn
          variant="outlined"
          @click="previousStep"
          :disabled="currentStep === 0"
        >
          Previous
        </VBtn>
        <VSpacer />
        <VBtn
          v-if="currentStep < numberedSteps.length - 1"
          color="primary"
          @click="nextStep"
        >
          Next
        </VBtn>
        <VBtn v-else color="success" @click="saveListing" :loading="saving">
          Save Changes
        </VBtn>
      </VCardActions>
    </VCard>

    <!-- Dialogs for advanced configuration -->
    <ItineraryAccommodationDialog
      v-model="showItineraryDialog"
      :listing-id="props.listing?.id"
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
      :use-period-terminology="false"
      @close="showPeriodsDialog = false"
      @done="handlePeriodsDone"
    />
  </div>
</template>

<script setup>
import ItineraryAccommodationDialog from "@/components/dialogs/ItineraryAccommodationDialog.vue";
import PeriodsDialog from "@/components/dialogs/PeriodsDialog.vue";
import SpecialAddonsDialog from "@/components/dialogs/SpecialAddonsDialog.vue";
import { onMounted, ref } from "vue";

const props = defineProps({
  listing: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["close", "updated"]);

// Stepper configuration
const numberedSteps = [
  {
    title: "Basic Information",
    subtitle: "Listing title, description, and activities",
    icon: "tabler-info-circle",
  },
  {
    title: "Date Range",
    subtitle: "Available dates and inclusions",
    icon: "tabler-calendar",
  },
  {
    title: "Pricing & Details",
    subtitle: "Price, capacity, and policies",
    icon: "tabler-currency-dollar",
  },
];

const currentStep = ref(0);
const saving = ref(false);

// Advanced dialog state
const showItineraryDialog = ref(false);
const showSpecialAddonsDialog = ref(false);
const showPeriodsDialog = ref(false);
const editingItineraryIndex = ref(-1);
const editingAddonIndex = ref(-1);
const editingPeriodIndex = ref(-1);
const itineraries = ref([]);
const specialAddons = ref([]);
const periods = ref([]);

// Form data
const formData = ref({
  listingTitle: "",
  subtitle: "",
  listingDescription: "",
  locations: "",
  groupLanguage: "",
  activitiesIncluded: "",
  experienceLevel: "",
  fitnessLevel: "",
  startDate: "",
  endDate: "",
  availableDays: [],
  whatsIncluded: "",
  whatsNotIncluded: "",
  additionalNotes: "",
  providersFAQ: "",
  listingMediaString: "",
  promotionalVideoString: "",
  mapsAndRoutesString: "",
  price: "",
  minParticipants: "",
  maxParticipants: "",
  requirements: "",
  personalPolicies: "",
  termsAccepted: false,
});

// Form validation errors
const formValidationErrors = ref({});

// Options for select fields
const groupLanguageOptions = [
  { title: "English", value: "english" },
  { title: "Spanish", value: "spanish" },
  { title: "French", value: "french" },
  { title: "German", value: "german" },
  { title: "Italian", value: "italian" },
  { title: "Other", value: "other" },
];

const experienceLevelOptions = [
  { title: "Beginner", value: "beginner" },
  { title: "Intermediate", value: "intermediate" },
  { title: "Advanced", value: "advanced" },
  { title: "Expert", value: "expert" },
];

const fitnessLevelOptions = [
  { title: "Low", value: "low" },
  { title: "Moderate", value: "moderate" },
  { title: "High", value: "high" },
  { title: "Very High", value: "very-high" },
];

const availableDaysOptions = [
  { title: "Monday", value: "monday" },
  { title: "Tuesday", value: "tuesday" },
  { title: "Wednesday", value: "wednesday" },
  { title: "Thursday", value: "thursday" },
  { title: "Friday", value: "friday" },
  { title: "Saturday", value: "saturday" },
  { title: "Sunday", value: "sunday" },
];

const personalPoliciesOptions = [
  { title: "Not Sure", value: "not-sure" },
  { title: "Yes", value: "yes" },
  { title: "No", value: "no" },
];

const termsOptions = [
  { title: "Yes", value: true },
  { title: "No", value: false },
];

// Initialize form data from props
onMounted(() => {
  if (props.listing) {
    formData.value = {
      listingTitle: props.listing.listing_title || "",
      subtitle: props.listing.subtitle || "",
      listingDescription: props.listing.listing_description || "",
      locations: props.listing.locations || "",
      groupLanguage: props.listing.group_language || "",
      activitiesIncluded: props.listing.activities_included || "",
      experienceLevel: props.listing.experience_level || "",
      fitnessLevel: props.listing.fitness_level || "",
      startDate: props.listing.start_date || "",
      endDate: props.listing.end_date || "",
      availableDays: props.listing.available_days || [],
      whatsIncluded: props.listing.whats_included || "",
      whatsNotIncluded: props.listing.whats_not_included || "",
      additionalNotes: props.listing.additional_notes || "",
      providersFAQ: props.listing.providers_faq || "",
      listingMediaString: normalizeToStringArray(props.listing.listing_media),
      promotionalVideoString: normalizeToStringArray(
        props.listing.promotional_video
      ),
      mapsAndRoutesString: normalizeToStringArray(
        props.listing.maps_and_routes
      ),
      price: props.listing.price || "",
      minParticipants: props.listing.min_capacity || "",
      maxParticipants: props.listing.max_participants || "",
      requirements: props.listing.requirements || "",
      personalPolicies: props.listing.personal_policies || "",
      termsAccepted: props.listing.terms_accepted || false,
    };

    // Initialize advanced arrays from listing if available
    if (props.listing.itineraries) {
      itineraries.value = JSON.parse(JSON.stringify(props.listing.itineraries));
    }
    if (props.listing.special_addons || props.listing.specialAddons) {
      specialAddons.value = JSON.parse(
        JSON.stringify(
          props.listing.special_addons || props.listing.specialAddons
        )
      );
    }
    if (props.listing.periods) {
      periods.value = JSON.parse(JSON.stringify(props.listing.periods));
    }
  }
});

// Navigation methods
const nextStep = () => {
  if (currentStep.value < numberedSteps.length - 1) {
    currentStep.value++;
  }
};

const previousStep = () => {
  if (currentStep.value > 0) {
    currentStep.value--;
  }
};

// Helper methods
const getStepTitle = (stepIndex) => {
  return numberedSteps[stepIndex]?.title || "";
};

const getStepDescription = (stepIndex) => {
  return numberedSteps[stepIndex]?.subtitle || "";
};

// Validation methods
const validateField = (fieldName) => {
  const value = formData.value[fieldName];

  if (fieldName === "listingTitle" && (!value || value.trim() === "")) {
    formValidationErrors.value[fieldName] = "Listing title is required";
  } else if (
    fieldName === "listingDescription" &&
    (!value || value.trim() === "")
  ) {
    formValidationErrors.value[fieldName] = "Listing description is required";
  } else if (
    fieldName === "activitiesIncluded" &&
    (!value || value.trim() === "")
  ) {
    formValidationErrors.value[fieldName] = "Activities included is required";
  } else if (fieldName === "startDate" && (!value || value.trim() === "")) {
    formValidationErrors.value[fieldName] = "Start date is required";
  } else if (fieldName === "endDate" && (!value || value.trim() === "")) {
    formValidationErrors.value[fieldName] = "End date is required";
  } else if (fieldName === "price" && (!value || isNaN(value))) {
    formValidationErrors.value[fieldName] =
      "Price is required and must be a valid number";
  } else if (
    fieldName === "minParticipants" &&
    value !== "" &&
    value !== null &&
    isNaN(value)
  ) {
    formValidationErrors.value[fieldName] =
      "Minimum participants must be numeric";
  } else if (fieldName === "maxParticipants" && (!value || isNaN(value))) {
    formValidationErrors.value[fieldName] =
      "Maximum participants is required and must be a valid number";
  } else {
    delete formValidationErrors.value[fieldName];
  }
};

const validateForm = () => {
  // Clear previous errors
  formValidationErrors.value = {};

  // Validate required fields
  validateField("listingTitle");
  validateField("listingDescription");
  validateField("activitiesIncluded");
  validateField("startDate");
  validateField("endDate");
  validateField("price");
  validateField("minParticipants");
  validateField("maxParticipants");

  return Object.keys(formValidationErrors.value).length === 0;
};

// Save listing
const saveListing = async () => {
  if (!validateForm()) {
    return;
  }

  saving.value = true;

  try {
    // Prepare data for API
    const updateData = {
      listing_title: formData.value.listingTitle,
      subtitle: formData.value.subtitle,
      listing_description: formData.value.listingDescription,
      locations: normalizeToArray(formData.value.locations),
      group_language: normalizeToArray(formData.value.groupLanguage),
      activities_included: normalizeToArray(formData.value.activitiesIncluded, {
        delimiter: "\n",
      }),
      experience_level: formData.value.experienceLevel,
      fitness_level: formData.value.fitnessLevel,
      starting_date: formData.value.startDate,
      finishing_date: formData.value.endDate,
      available_days: normalizeToArray(formData.value.availableDays),
      whats_included: normalizeToArray(formData.value.whatsIncluded, {
        delimiter: "\n",
      }),
      whats_not_included: normalizeToArray(formData.value.whatsNotIncluded, {
        delimiter: "\n",
      }),
      additional_notes: normalizeToArray(formData.value.additionalNotes, {
        delimiter: "\n",
      }),
      providers_faq: normalizeToArray(formData.value.providersFAQ, {
        delimiter: "\n",
      }),
      listing_media: normalizeToArray(formData.value.listingMediaString, {
        attemptJson: true,
      }),
      promotional_video: normalizeToArray(
        formData.value.promotionalVideoString,
        {
          attemptJson: true,
        }
      ),
      maps_and_routes: normalizeToArray(formData.value.mapsAndRoutesString, {
        attemptJson: true,
      }),
      price: formData.value.price ? parseFloat(formData.value.price) : null,
      min_capacity: formData.value.minParticipants
        ? parseInt(formData.value.minParticipants)
        : props.listing.min_capacity || null,
      max_capacity: formData.value.maxParticipants
        ? parseInt(formData.value.maxParticipants)
        : props.listing.max_capacity || null,
      requirements: normalizeToArray(formData.value.requirements, {
        delimiter: "\n",
      }),
      personal_policies: normalizeToArray(formData.value.personalPolicies),
      terms_accepted: formData.value.termsAccepted,
    };

    // Call API to update listing
    const res = await $api(`/api/listings/${props.listing.id}`, {
      method: "PUT",
      body: updateData,
    });

    const syncedListing = res?.data || res || {};

    const formatCollectionPayload = (items) =>
      items.map(({ id, ...rest }) => ({ id, ...rest }));

    if (Array.isArray(itineraries.value)) {
      for (const itinerary of formatCollectionPayload(itineraries.value)) {
        const method = itinerary.id ? "PUT" : "POST";
        const endpoint = itinerary.id
          ? `/api/listings/${props.listing.id}/itineraries/${itinerary.id}`
          : `/api/listings/${props.listing.id}/itineraries`;
        await $api(endpoint, {
          method,
          body: { itinerary },
        }).catch(() => {});
      }
    }

    if (Array.isArray(specialAddons.value)) {
      for (const addon of formatCollectionPayload(specialAddons.value)) {
        const method = addon.id ? "PUT" : "POST";
        const endpoint = addon.id
          ? `/api/listings/${props.listing.id}/special-addons/${addon.id}`
          : `/api/listings/${props.listing.id}/special-addons`;
        await $api(endpoint, {
          method,
          body: { special_addon: addon },
        }).catch(() => {});
      }
    }

    if (Array.isArray(periods.value)) {
      for (const period of formatCollectionPayload(periods.value)) {
        const method = period.id ? "PUT" : "POST";
        const endpoint = period.id
          ? `/api/listings/${props.listing.id}/periods/${period.id}`
          : `/api/listings/${props.listing.id}/periods`;
        await $api(endpoint, {
          method,
          body: { period },
        }).catch(() => {});
      }
    }

    emit("updated", {
      ...props.listing,
      ...syncedListing,
      ...updateData,
      itineraries: itineraries.value,
      special_addons: specialAddons.value,
      periods: periods.value,
    });
  } catch (error) {
    console.error("Error updating listing:", error);
  } finally {
    saving.value = false;
  }
};

// Advanced dialog handlers
const openItineraryModal = () => {
  editingItineraryIndex.value = -1;
  showItineraryDialog.value = true;
};

const openSpecialAddonsModal = () => {
  editingAddonIndex.value = -1;
  showSpecialAddonsDialog.value = true;
};

const openPeriodsModal = () => {
  editingPeriodIndex.value = -1;
  showPeriodsDialog.value = true;
};

const handleItineraryDone = (itineraryData, editingIndex = -1) => {
  if (editingIndex >= 0) {
    itineraries.value[editingIndex] = itineraryData[0];
    editingItineraryIndex.value = -1;
  } else {
    const newItems = itineraryData.filter((newItem) => {
      return !itineraries.value.some(
        (existing) =>
          existing.title === newItem.title &&
          existing.description === newItem.description &&
          existing.accommodation === newItem.accommodation &&
          existing.location === newItem.location
      );
    });
    itineraries.value.push(...newItems);
  }
  showItineraryDialog.value = false;
};

const handleSpecialAddonDone = (addonData, editingIndex = -1) => {
  if (editingIndex >= 0) {
    specialAddons.value[editingIndex] = addonData[0];
    editingAddonIndex.value = -1;
  } else {
    specialAddons.value.push(...addonData);
  }
  specialAddons.value.forEach((addon, idx) => (addon.number = idx + 1));
  showSpecialAddonsDialog.value = false;
};

const handlePeriodsDone = (newPeriods, editingIndex = -1) => {
  if (editingIndex >= 0) {
    periods.value[editingIndex] = newPeriods[0];
    editingPeriodIndex.value = -1;
  } else {
    periods.value.push(...newPeriods);
  }
  periods.value.forEach((p, idx) => (p.number = idx + 1));
  showPeriodsDialog.value = false;
};

function safeParseJson(str) {
  if (!str || typeof str !== "string") return undefined;
  try {
    const parsed = JSON.parse(str);
    return parsed;
  } catch (e) {
    return undefined;
  }
}

function normalizeToStringArray(value) {
  if (!value) return "";
  if (typeof value === "string") {
    const trimmed = value.trim();
    if (
      (trimmed.startsWith("[") && trimmed.endsWith("]")) ||
      (trimmed.startsWith("{") && trimmed.endsWith("}"))
    ) {
      return trimmed;
    }
    return JSON.stringify([trimmed]);
  }
  try {
    return JSON.stringify(value);
  } catch (e) {
    return "";
  }
}

function normalizeToArray(value, options = {}) {
  const {
    delimiter = ",",
    newlineDelimiter = "\n",
    attemptJson = false,
  } = options;

  if (Array.isArray(value)) {
    return value
      .map((item) => (typeof item === "string" ? item.trim() : item))
      .filter((item) => item !== undefined && item !== null && item !== "");
  }

  if (value === null || value === undefined || value === "") {
    return [];
  }

  if (typeof value === "string") {
    const trimmed = value.trim();
    if (!trimmed) return [];

    if (attemptJson) {
      const parsed = safeParseJson(trimmed);
      if (Array.isArray(parsed)) {
        return parsed
          .map((item) => (typeof item === "string" ? item.trim() : item))
          .filter((item) => item !== undefined && item !== null && item !== "");
      }
    }

    if (trimmed.includes(newlineDelimiter)) {
      return trimmed
        .split(newlineDelimiter)
        .map((item) => item.trim())
        .filter(Boolean);
    }

    if (delimiter && trimmed.includes(delimiter)) {
      return trimmed
        .split(delimiter)
        .map((item) => item.trim())
        .filter(Boolean);
    }

    return [trimmed];
  }

  if (typeof value === "object") {
    return Object.values(value)
      .map((item) => (typeof item === "string" ? item.trim() : item))
      .filter((item) => item !== undefined && item !== null && item !== "");
  }

  return [value];
}
</script>

<style lang="scss" scoped>
.multi-date-wizard {
  .v-card {
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  .v-stepper {
    .v-stepper-header {
      box-shadow: none;
    }
  }

  .v-window {
    min-height: 400px;
  }

  .v-text-field,
  .v-textarea,
  .v-select {
    .v-field {
      border-radius: 6px;
    }
  }

  .v-btn {
    border-radius: 6px;
    text-transform: none;
    font-weight: 500;
  }
}
</style>
