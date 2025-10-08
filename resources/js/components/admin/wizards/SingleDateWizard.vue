<template>
  <div class="single-date-wizard">
    <VCard>
      <!-- Header with Step Counter -->
      <VCardTitle class="d-flex align-center justify-space-between">
        <div class="d-flex align-center">
          <span class="text-h6 me-3">Edit Single Date Listing</span>
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

                    <!-- Left column -->
                    <VCol cols="12" md="6">
                      <!-- Starting Date -->
                      <div class="mb-4">
                        <label
                          class="v-label text-body-2 mb-3 d-block"
                          style="
                            font-size: 16px !important;
                            font-weight: 400 !important;
                          "
                        >
                          Starting Date <span class="required-star">*</span>
                        </label>
                        <AppDateTimePicker
                          ref="startingDatePicker"
                          v-model="formData.startingDate"
                          placeholder="Select your listing starting date"
                          :config="{ dateFormat: 'Y-m-d', allowInput: true }"
                          :error="hasFieldError('startingDate')"
                          :error-messages="formValidationErrors['startingDate']"
                          @input="clearFieldError('startingDate')"
                          :class="{
                            'field-error': hasFieldError('startingDate'),
                          }"
                        />
                      </div>

                      <!-- Finishing Date -->
                      <div class="mb-4">
                        <label
                          class="v-label text-body-2 mb-3 d-block"
                          style="
                            font-size: 16px !important;
                            font-weight: 400 !important;
                          "
                        >
                          Finishing Date <span class="required-star">*</span>
                        </label>
                        <AppDateTimePicker
                          ref="finishingDatePicker"
                          v-model="formData.finishingDate"
                          placeholder="Select your listing finishing date"
                          :config="{ dateFormat: 'Y-m-d', allowInput: true }"
                          :error="hasFieldError('finishingDate')"
                          :error-messages="
                            formValidationErrors['finishingDate']
                          "
                          @input="clearFieldError('finishingDate')"
                          :class="{
                            'field-error': hasFieldError('finishingDate'),
                          }"
                        />
                      </div>

                      <!-- Listing Title -->
                      <div class="mb-4">
                        <label
                          class="v-label text-body-2 mb-3 d-block"
                          style="
                            font-size: 16px !important;
                            font-weight: 400 !important;
                          "
                        >
                          Listing Title
                        </label>
                        <AppTextField
                          v-model="formData.listingTitle"
                          placeholder="The main title of Your listing"
                        />
                      </div>

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
                            :error="hasFieldError('price')"
                            :error-messages="formValidationErrors['price']"
                            @input="clearFieldError('price')"
                            :class="{ 'field-error': hasFieldError('price') }"
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
                          Departure Capacity
                          <span class="required-star">*</span>
                        </label>
                        <div class="d-flex gap-3">
                          <VTextField
                            v-model="formData.minCapacity"
                            placeholder="Min Num"
                            type="number"
                            class="capacity-input"
                            style="max-width: 120px"
                            :error="hasFieldError('minCapacity')"
                            :error-messages="
                              formValidationErrors['minCapacity']
                            "
                            @input="clearFieldError('minCapacity')"
                            :class="{
                              'field-error': hasFieldError('minCapacity'),
                            }"
                          />
                          <VTextField
                            v-model="formData.maxCapacity"
                            placeholder="Max Num"
                            type="number"
                            class="capacity-input"
                            style="max-width: 120px"
                            :error="hasFieldError('maxCapacity')"
                            :error-messages="
                              formValidationErrors['maxCapacity']
                            "
                            @input="clearFieldError('maxCapacity')"
                            :class="{
                              'field-error': hasFieldError('maxCapacity'),
                            }"
                          />
                        </div>
                      </div>

                      <!-- Subtitle -->
                      <div class="mb-4">
                        <label
                          class="v-label text-body-2 mb-3 d-block"
                          style="
                            font-size: 16px !important;
                            font-weight: 400 !important;
                          "
                        >
                          Subtitle
                        </label>
                        <AppTextField
                          v-model="formData.subtitle"
                          placeholder="A tagline for your adventure"
                        />
                      </div>

                      <!-- Listing Packages -->
                      <div class="mb-6">
                        <label
                          class="v-label text-body-2 mb-3 d-block"
                          style="
                            font-size: 16px !important;
                            font-weight: 400 !important;
                          "
                        >
                          Listing Packages <span class="required-star">*</span>
                        </label>
                        <p
                          class="text-caption text-muted mb-3"
                          style="font-size: 14px; color: #666"
                        >
                          Add your Packages and all related details here
                        </p>

                        <!-- Show button when no items -->
                        <div
                          v-if="packages.length === 0"
                          class="text-center py-4"
                        >
                          <VBtn
                            color="primary"
                            variant="outlined"
                            @click="openPackageModal"
                          >
                            <VIcon icon="tabler-plus" class="me-2" />
                            Add First Package
                          </VBtn>
                        </div>

                        <!-- Show existing packages -->
                        <div v-else>
                          <div
                            v-for="(packageItem, index) in packages"
                            :key="index"
                            class="package-item mb-3 p-3 border rounded"
                          >
                            <div
                              class="d-flex justify-space-between align-center mb-2"
                            >
                              <h6 class="text-h6 mb-0">
                                Package {{ index + 1 }}: {{ packageItem.name }}
                              </h6>
                              <div class="d-flex gap-2">
                                <VBtn
                                  icon
                                  variant="text"
                                  size="small"
                                  color="primary"
                                  @click="editPackage(index)"
                                >
                                  <VIcon icon="tabler-edit" />
                                </VBtn>
                                <VBtn
                                  icon
                                  variant="text"
                                  size="small"
                                  color="error"
                                  @click="removePackage(index)"
                                >
                                  <VIcon icon="tabler-trash" />
                                </VBtn>
                              </div>
                            </div>
                            <p class="text-body-2 mb-1">
                              <strong>Price:</strong> €{{ packageItem.price }}
                            </p>
                            <p class="text-body-2 mb-0">
                              <strong>Description:</strong>
                              {{ packageItem.description }}
                            </p>
                          </div>

                          <VBtn
                            color="primary"
                            variant="outlined"
                            @click="openPackageModal"
                            class="mt-2"
                          >
                            <VIcon icon="tabler-plus" class="me-2" />
                            Add Another Package
                          </VBtn>
                        </div>
                      </div>
                    </VCol>
                  </VRow>
                </VWindowItem>

                <!-- Step 2: Detailed Information -->
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

                    <VCol cols="12" md="6">
                      <AppAutocomplete
                        v-model="formData.groupLanguage"
                        label="Group Language"
                        placeholder="Select group language(s)"
                        :items="ALL_LANGUAGES"
                        multiple
                        chips
                        closable-chips
                        clearable
                        :error-messages="formValidationErrors.groupLanguage"
                        @update:model-value="validateField('groupLanguage')"
                      />
                    </VCol>

                    <VCol cols="12" md="6">
                      <AppAutocomplete
                        v-model="formData.locations"
                        label="Locations"
                        placeholder="Select countries"
                        :items="ALL_COUNTRIES"
                        multiple
                        chips
                        closable-chips
                        clearable
                        :error-messages="formValidationErrors.locations"
                        @update:model-value="validateField('locations')"
                      />
                    </VCol>

                    <VCol cols="12">
                      <VTextarea
                        v-model="formData.activitiesIncluded"
                        label="Activities Included"
                        placeholder="Describe the activities included in this adventure"
                        variant="outlined"
                        density="comfortable"
                        rows="4"
                        :error-messages="
                          formValidationErrors.activitiesIncluded
                        "
                        @blur="validateField('activitiesIncluded')"
                      />
                    </VCol>

                    <VCol cols="12">
                      <VTextarea
                        v-model="formData.whatsIncluded"
                        label="What's Included"
                        placeholder="List what is included in the price"
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
                        placeholder="List what is not included in the price"
                        variant="outlined"
                        density="comfortable"
                        rows="3"
                        :error-messages="formValidationErrors.whatsNotIncluded"
                        @blur="validateField('whatsNotIncluded')"
                      />
                    </VCol>

                    <!-- Maps and Routes -->
                    <VCol cols="12">
                      <label class="v-label text-body-2 mb-3 d-block">
                        Maps and Routes
                      </label>
                      <div
                        v-for="(route, index) in formData.mapsAndRoutes"
                        :key="index"
                        class="d-flex gap-2 mb-2"
                      >
                        <VTextField
                          v-model="formData.mapsAndRoutes[index]"
                          placeholder="Enter map or route URL"
                          variant="outlined"
                          density="comfortable"
                          class="flex-grow-1"
                        />
                        <VBtn
                          v-if="formData.mapsAndRoutes.length > 1"
                          icon
                          variant="text"
                          size="small"
                          color="error"
                          @click="removeMapsAndRoutes(index)"
                        >
                          <VIcon icon="tabler-trash" />
                        </VBtn>
                      </div>
                      <VBtn
                        color="primary"
                        variant="outlined"
                        size="small"
                        @click="addMapsAndRoutes"
                      >
                        <VIcon icon="tabler-plus" class="me-2" />
                        Add Route
                      </VBtn>
                    </VCol>

                    <!-- Listing Media -->
                    <VCol cols="12">
                      <label class="v-label text-body-2 mb-3 d-block">
                        Listing Media
                      </label>
                      <div
                        v-for="(media, index) in formData.listingMedia"
                        :key="index"
                        class="d-flex gap-2 mb-2"
                      >
                        <VTextField
                          v-model="formData.listingMedia[index]"
                          placeholder="Enter media URL"
                          variant="outlined"
                          density="comfortable"
                          class="flex-grow-1"
                        />
                        <VBtn
                          v-if="formData.listingMedia.length > 1"
                          icon
                          variant="text"
                          size="small"
                          color="error"
                          @click="removeListingMedia(index)"
                        >
                          <VIcon icon="tabler-trash" />
                        </VBtn>
                      </div>
                      <VBtn
                        color="primary"
                        variant="outlined"
                        size="small"
                        @click="addListingMedia"
                      >
                        <VIcon icon="tabler-plus" class="me-2" />
                        Add Media
                      </VBtn>
                    </VCol>

                    <!-- Promotional Video -->
                    <VCol cols="12">
                      <label class="v-label text-body-2 mb-3 d-block">
                        Promotional Video
                      </label>
                      <div
                        v-for="(video, index) in formData.promotionalVideo"
                        :key="index"
                        class="d-flex gap-2 mb-2"
                      >
                        <VTextField
                          v-model="formData.promotionalVideo[index]"
                          placeholder="Enter video URL"
                          variant="outlined"
                          density="comfortable"
                          class="flex-grow-1"
                        />
                        <VBtn
                          v-if="formData.promotionalVideo.length > 1"
                          icon
                          variant="text"
                          size="small"
                          color="error"
                          @click="removePromotionalVideo(index)"
                        >
                          <VIcon icon="tabler-trash" />
                        </VBtn>
                      </div>
                      <VBtn
                        color="primary"
                        variant="outlined"
                        size="small"
                        @click="addPromotionalVideo"
                      >
                        <VIcon icon="tabler-plus" class="me-2" />
                        Add Video
                      </VBtn>
                    </VCol>
                  </VRow>
                </VWindowItem>

                <!-- Step 3: Additional Information -->
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

                    <VCol cols="12">
                      <VTextarea
                        v-model="formData.additionalNotes"
                        label="Additional Notes"
                        placeholder="Any additional information or special requirements"
                        variant="outlined"
                        density="comfortable"
                        rows="4"
                        :error-messages="formValidationErrors.additionalNotes"
                        @blur="validateField('additionalNotes')"
                      />
                    </VCol>

                    <VCol cols="12">
                      <VTextarea
                        v-model="formData.providersFaq"
                        label="Provider's FAQ"
                        placeholder="Frequently asked questions about this adventure"
                        variant="outlined"
                        density="comfortable"
                        rows="4"
                        :error-messages="formValidationErrors.providersFaq"
                        @blur="validateField('providersFaq')"
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
                      <VTextField
                        v-model="formData.personalPoliciesText"
                        label="Personal Policies Text"
                        placeholder="Additional policy details"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="
                          formValidationErrors.personalPoliciesText
                        "
                        @blur="validateField('personalPoliciesText')"
                      />
                    </VCol>

                    <!-- Itinerary & Accommodation -->
                    <VCol cols="12">
                      <label class="v-label text-body-2 mb-3 d-block">
                        Itinerary & Accommodation
                      </label>
                      <p
                        class="text-caption text-muted mb-3"
                        style="font-size: 14px; color: #666"
                      >
                        Manage your daily itinerary and accommodation details
                      </p>

                      <!-- Show button when no items -->
                      <div
                        v-if="itineraries.length === 0"
                        class="text-center py-4"
                      >
                        <VBtn
                          color="primary"
                          variant="outlined"
                          @click="openItineraryModal"
                        >
                          <VIcon icon="tabler-plus" class="me-2" />
                          Add First Day
                        </VBtn>
                      </div>

                      <!-- Show existing itineraries -->
                      <div v-else>
                        <div
                          v-for="(itinerary, index) in itineraries"
                          :key="index"
                          class="itinerary-item mb-3 p-3 border rounded"
                        >
                          <div
                            class="d-flex justify-space-between align-center mb-2"
                          >
                            <h6 class="text-h6 mb-0">
                              Day {{ index + 1 }}: {{ itinerary.title }}
                            </h6>
                            <div class="d-flex gap-2">
                              <VBtn
                                icon
                                variant="text"
                                size="small"
                                color="primary"
                                @click="editItinerary(index)"
                              >
                                <VIcon icon="tabler-edit" />
                              </VBtn>
                              <VBtn
                                icon
                                variant="text"
                                size="small"
                                color="error"
                                @click="removeItinerary(index)"
                              >
                                <VIcon icon="tabler-trash" />
                              </VBtn>
                            </div>
                          </div>
                          <p class="text-body-2 mb-1">
                            <strong>Description:</strong>
                            {{ itinerary.description }}
                          </p>
                          <p class="text-body-2 mb-0">
                            <strong>Accommodation:</strong>
                            {{ itinerary.accommodation }}
                          </p>
                        </div>

                        <VBtn
                          color="primary"
                          variant="outlined"
                          @click="openItineraryModal"
                          class="mt-2"
                        >
                          <VIcon icon="tabler-plus" class="me-2" />
                          Add Another Day
                        </VBtn>
                      </div>
                    </VCol>

                    <!-- Special Addons -->
                    <VCol cols="12">
                      <label class="v-label text-body-2 mb-3 d-block">
                        Special Addons
                      </label>
                      <p
                        class="text-caption text-muted mb-3"
                        style="font-size: 14px; color: #666"
                      >
                        Add special services or extras for your adventure
                      </p>

                      <!-- Show button when no items -->
                      <div
                        v-if="specialAddons.length === 0"
                        class="text-center py-4"
                      >
                        <VBtn
                          color="primary"
                          variant="outlined"
                          @click="openSpecialAddonsModal"
                        >
                          <VIcon icon="tabler-plus" class="me-2" />
                          Add First Addon
                        </VBtn>
                      </div>

                      <!-- Show existing addons -->
                      <div v-else>
                        <div
                          v-for="(addon, index) in specialAddons"
                          :key="index"
                          class="addon-item mb-3 p-3 border rounded"
                        >
                          <div
                            class="d-flex justify-space-between align-center mb-2"
                          >
                            <h6 class="text-h6 mb-0">
                              {{ addon.name }} - €{{ addon.price }}
                            </h6>
                            <div class="d-flex gap-2">
                              <VBtn
                                icon
                                variant="text"
                                size="small"
                                color="primary"
                                @click="editSpecialAddon(index)"
                              >
                                <VIcon icon="tabler-edit" />
                              </VBtn>
                              <VBtn
                                icon
                                variant="text"
                                size="small"
                                color="error"
                                @click="removeSpecialAddon(index)"
                              >
                                <VIcon icon="tabler-trash" />
                              </VBtn>
                            </div>
                          </div>
                          <p class="text-body-2 mb-0">
                            {{ addon.description }}
                          </p>
                        </div>

                        <VBtn
                          color="primary"
                          variant="outlined"
                          @click="openSpecialAddonsModal"
                          class="mt-2"
                        >
                          <VIcon icon="tabler-plus" class="me-2" />
                          Add Another Addon
                        </VBtn>
                      </div>
                    </VCol>

                    <VCol cols="12">
                      <VCheckbox
                        v-model="formData.termsAccepted"
                        label="I accept the terms and conditions"
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
    <PackageDialog
      v-model="showPackageDialog"
      :initial-packages="packages"
      :editing-index="editingPackageIndex"
      @close="showPackageDialog = false"
      @done="handlePackageDone"
    />
  </div>
</template>

<script setup>
import ItineraryAccommodationDialog from "@/components/dialogs/ItineraryAccommodationDialog.vue";
import PackageDialog from "@/components/dialogs/PackageDialog.vue";
import SpecialAddonsDialog from "@/components/dialogs/SpecialAddonsDialog.vue";
import { ALL_COUNTRIES } from "@/constants/countries";
import { ALL_LANGUAGES } from "@/constants/languages";
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
    subtitle: "Listing title, dates, price, and capacity",
    icon: "tabler-info-circle",
  },
  {
    title: "Detailed Information",
    subtitle: "Experience level, activities, and inclusions",
    icon: "tabler-details",
  },
  {
    title: "Additional Information",
    subtitle: "Policies, terms, and additional notes",
    icon: "tabler-plus",
  },
];

const currentStep = ref(0);
const saving = ref(false);

// Advanced dialog state
const showItineraryDialog = ref(false);
const showSpecialAddonsDialog = ref(false);
const showPackageDialog = ref(false);
const editingItineraryIndex = ref(-1);
const editingAddonIndex = ref(-1);
const editingPackageIndex = ref(-1);
const itineraries = ref([]);
const specialAddons = ref([]);
const packages = ref([]);

// Form data
const formData = ref({
  startingDate: "",
  finishingDate: "",
  listingTitle: "",
  subtitle: "",
  price: "",
  minCapacity: "",
  maxCapacity: "",
  listingDescription: "",
  experienceLevel: "",
  fitnessLevel: "",
  groupLanguage: [],
  locations: [],
  activitiesIncluded: "",
  whatsIncluded: "",
  whatsNotIncluded: "",
  personalPolicies: "",
  personalPoliciesText: "",
  providersFaq: "",
  additionalNotes: "",
  termsAccepted: false,
  mapsAndRoutes: [""],
  listingMedia: [""],
  promotionalVideo: [""],
});

// Form validation errors
const formValidationErrors = ref({});

// Options for select fields
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

const personalPoliciesOptions = [
  { title: "Not Sure", value: "not-sure" },
  { title: "Flexible", value: "flexible" },
  { title: "Moderate", value: "moderate" },
  { title: "Strict", value: "strict" },
];

// Initialize form data from props
onMounted(() => {
  if (props.listing) {
    // Map listing data to form data
    formData.value = {
      startingDate: props.listing.starting_date || "",
      finishingDate: props.listing.finishing_date || "",
      listingTitle: props.listing.listing_title || "",
      subtitle: props.listing.subtitle || "",
      price: props.listing.price || "",
      minCapacity: props.listing.min_capacity || "",
      maxCapacity: props.listing.max_capacity || "",
      listingDescription: props.listing.listing_description || "",
      experienceLevel: props.listing.experience_level || "",
      fitnessLevel: props.listing.fitness_level || "",
      groupLanguage: props.listing.group_language || "",
      locations: props.listing.locations || "",
      activitiesIncluded: props.listing.activities_included || "",
      whatsIncluded: props.listing.whats_included || "",
      whatsNotIncluded: props.listing.whats_not_included || "",
      personalPolicies: props.listing.personal_policies || "",
      personalPoliciesText: props.listing.personal_policies_text || "",
      providersFaq: props.listing.providers_faq || "",
      additionalNotes: props.listing.additional_notes || "",
      termsAccepted: props.listing.terms_accepted || false,
      mapsAndRoutes: props.listing.maps_and_routes
        ? [props.listing.maps_and_routes]
        : [""],
      listingMedia: props.listing.listing_media
        ? [props.listing.listing_media]
        : [""],
      promotionalVideo: props.listing.promotional_video
        ? [props.listing.promotional_video]
        : [""],
    };

    // Load existing data
    loadExistingData();
  }
});

// Load existing data for the listing
const loadExistingData = async () => {
  if (!props.listing?.id) return;

  try {
    // Load itineraries
    const itinerariesResponse = await $api(
      `/admin/listings/${props.listing.id}/itineraries`
    );
    itineraries.value = itinerariesResponse.data || [];

    // Load special addons
    const addonsResponse = await $api(
      `/admin/listings/${props.listing.id}/special-addons`
    );
    specialAddons.value = addonsResponse.data || [];

    // Load packages
    const packagesResponse = await $api(
      `/admin/listings/${props.listing.id}/packages`
    );
    packages.value = packagesResponse.data || [];
  } catch (error) {
    console.error("Error loading existing data:", error);
  }
};

// Helper functions
const getStepTitle = (stepIndex) => {
  return numberedSteps[stepIndex]?.title || "";
};

const getStepDescription = (stepIndex) => {
  return numberedSteps[stepIndex]?.subtitle || "";
};

const hasFieldError = (fieldName) => {
  return !!formValidationErrors.value[fieldName];
};

const validateField = (fieldName) => {
  const value = formData.value[fieldName];
  let error = "";

  if (fieldName === "startingDate" || fieldName === "finishingDate") {
    if (!value) error = "This field is required";
  } else if (fieldName === "price") {
    if (!value || value <= 0) error = "Price must be greater than 0";
  } else if (fieldName === "minCapacity" || fieldName === "maxCapacity") {
    if (!value || value <= 0) error = "Capacity must be greater than 0";
  } else if (fieldName === "termsAccepted") {
    if (!value) error = "You must accept the terms and conditions";
  }

  if (error) {
    formValidationErrors.value[fieldName] = error;
  } else {
    delete formValidationErrors.value[fieldName];
  }

  return !error;
};

const clearFieldError = (fieldName) => {
  if (formValidationErrors.value[fieldName]) {
    delete formValidationErrors.value[fieldName];
  }
};

// Navigation functions
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

// Save listing function
const saveListing = async () => {
  saving.value = true;
  try {
    // Validate all required fields
    const requiredFields = [
      "startingDate",
      "finishingDate",
      "price",
      "minCapacity",
      "maxCapacity",
    ];

    let hasErrors = false;
    requiredFields.forEach((field) => {
      if (!validateField(field)) {
        hasErrors = true;
      }
    });

    if (hasErrors) {
      return;
    }

    // Prepare data for API
    const updateData = {
      ...formData.value,
      starting_date: formData.value.startingDate,
      finishing_date: formData.value.finishingDate,
      listing_title: formData.value.listingTitle,
      min_capacity: formData.value.minCapacity,
      max_capacity: formData.value.maxCapacity,
      listing_description: formData.value.listingDescription,
      experience_level: formData.value.experienceLevel,
      fitness_level: formData.value.fitnessLevel,
      group_language: Array.isArray(formData.value.groupLanguage)
        ? formData.value.groupLanguage
        : [],
      activities_included: formData.value.activitiesIncluded,
      whats_included: formData.value.whatsIncluded,
      whats_not_included: formData.value.whatsNotIncluded,
      personal_policies: formData.value.personalPolicies,
      personal_policies_text: formData.value.personalPoliciesText,
      providers_faq: formData.value.providersFaq,
      additional_notes: formData.value.additionalNotes,
      terms_accepted: formData.value.termsAccepted,
      maps_and_routes: formData.value.mapsAndRoutes.filter((r) => r.trim()),
      listing_media: formData.value.listingMedia.filter((m) => m.trim()),
      promotional_video: formData.value.promotionalVideo.filter((v) =>
        v.trim()
      ),
      locations: Array.isArray(formData.value.locations)
        ? formData.value.locations
        : [],
    };

    // Update listing
    await $api(`/admin/listings/${props.listing.id}`, {
      method: "PUT",
      body: updateData,
    });

    // Update related data
    if (itineraries.value.length > 0) {
      await $api(`/admin/listings/${props.listing.id}/itineraries`, {
        method: "PUT",
        body: { itineraries: itineraries.value },
      });
    }

    if (specialAddons.value.length > 0) {
      await $api(`/admin/listings/${props.listing.id}/special-addons`, {
        method: "PUT",
        body: { special_addons: specialAddons.value },
      });
    }

    if (packages.value.length > 0) {
      await $api(`/admin/listings/${props.listing.id}/packages`, {
        method: "PUT",
        body: { packages: packages.value },
      });
    }

    emit("updated", { ...props.listing, ...updateData });
  } catch (error) {
    console.error("Error saving listing:", error);
    alert("Error saving listing: " + (error.message || "Unknown error"));
  } finally {
    saving.value = false;
  }
};

// Dialog management functions
const openItineraryModal = () => {
  editingItineraryIndex.value = -1;
  showItineraryDialog.value = true;
};

const editItinerary = (index) => {
  editingItineraryIndex.value = index;
  showItineraryDialog.value = true;
};

const removeItinerary = (index) => {
  itineraries.value.splice(index, 1);
};

const handleItineraryDone = (data) => {
  if (editingItineraryIndex.value >= 0) {
    itineraries.value[editingItineraryIndex.value] = data;
  } else {
    itineraries.value.push(data);
  }
  editingItineraryIndex.value = -1;
};

const openSpecialAddonsModal = () => {
  editingAddonIndex.value = -1;
  showSpecialAddonsDialog.value = true;
};

const editSpecialAddon = (index) => {
  editingAddonIndex.value = index;
  showSpecialAddonsDialog.value = true;
};

const removeSpecialAddon = (index) => {
  specialAddons.value.splice(index, 1);
};

const handleSpecialAddonDone = (data) => {
  if (editingAddonIndex.value >= 0) {
    specialAddons.value[editingAddonIndex.value] = data;
  } else {
    specialAddons.value.push(data);
  }
  editingAddonIndex.value = -1;
};

const openPackageModal = () => {
  editingPackageIndex.value = -1;
  showPackageDialog.value = true;
};

const editPackage = (index) => {
  editingPackageIndex.value = index;
  showPackageDialog.value = true;
};

const removePackage = (index) => {
  packages.value.splice(index, 1);
};

const handlePackageDone = (data) => {
  if (editingPackageIndex.value >= 0) {
    packages.value[editingPackageIndex.value] = data;
  } else {
    packages.value.push(data);
  }
  editingPackageIndex.value = -1;
};

// Array management functions
const addMapsAndRoutes = () => {
  formData.value.mapsAndRoutes.push("");
};

const removeMapsAndRoutes = (index) => {
  if (formData.value.mapsAndRoutes.length > 1) {
    formData.value.mapsAndRoutes.splice(index, 1);
  }
};

const addListingMedia = () => {
  formData.value.listingMedia.push("");
};

const removeListingMedia = (index) => {
  if (formData.value.listingMedia.length > 1) {
    formData.value.listingMedia.splice(index, 1);
  }
};

const addPromotionalVideo = () => {
  formData.value.promotionalVideo.push("");
};

const removePromotionalVideo = (index) => {
  if (formData.value.promotionalVideo.length > 1) {
    formData.value.promotionalVideo.splice(index, 1);
  }
};
</script>

<style lang="scss" scoped>
.single-date-wizard {
  .v-card {
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  .required-star {
    color: #ff5252;
  }

  .field-error {
    border-color: #ff5252 !important;
  }

  .price-input-wrapper {
    position: relative;

    .euro-symbol {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #666;
      font-weight: 500;
    }
  }

  .package-item,
  .itinerary-item,
  .addon-item {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef !important;

    &:hover {
      background-color: #e9ecef;
    }
  }

  .rich-text-area {
    border-radius: 8px;

    &:focus-within {
      border-color: rgb(var(--v-theme-primary));
    }
  }

  .capacity-input {
    border-radius: 8px;
  }
}
</style>
