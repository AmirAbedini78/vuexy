<script setup>
// remove invalid Nuxt alias; useCookie is available globally via @vueuse/integrations in this project
import AllAdventuresDialog from "@/components/dialogs/AllAdventuresDialog.vue";
import { useAutoSave } from "@/composables/useAutoSave";
import { onMounted, ref } from "vue";
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

const showItineraryDialog = ref(false);
const showSpecialAddonsDialog = ref(false);
const showPeriodsDialog = ref(false);
const showAllAdventuresDialog = ref(false);
const showComingSoonPopup = ref(false);
const formValidationErrors = ref({});
const showConfirmation = ref(false);
const submissionData = ref({
  adventureTitle: "",
  adventureId: "",
});

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

// Auto-save functionality
const { 
  isSaving, 
  lastSaved, 
  hasUnsavedChanges, 
  showSavedIndicator,
  saveToStorage, 
  loadFromStorage, 
  clearSavedData,
  hasSavedData,
  getSavedDataInfo 
} = useAutoSave(formData, 'listing-form-multi-date', {
  debounceMs: 300, // Save after 300ms of inactivity
  saveToDatabase: true,
  listingType: 'multi-date',
  apiEndpoint: '/auto-save-listings',
  onSave: (data) => {
    console.log('Multi-date form data auto-saved:', data);
  },
  onLoad: (data, meta) => {
    console.log('Multi-date form data loaded from storage:', data);
    if (meta) {
      console.log('Last saved:', new Date(meta.timestamp).toLocaleString());
    }
  }
});

const itineraries = ref([]);
const specialAddons = ref([]);
const periods = ref([]);
const editingAddonIndex = ref(-1);
const editingItineraryIndex = ref(-1);
const editingPeriodIndex = ref(-1);
const listingId = ref(null); // For ItineraryAccommodationDialog

// Required fields for Step 1
const requiredFieldsStep1 = [
  "listingTitle",
  "listingDescription",
  "activitiesIncluded",
];

const validateStep1 = () => {
  const errors = {};
  requiredFieldsStep1.forEach((field) => {
    const value = formData.value[field];
    if (!value || (typeof value === "string" && value.trim() === "")) {
      errors[field] = "This field is required";
    }
  });
  // Validate periods (Listing departures)
  if (!periods.value || periods.value.length === 0) {
    errors["periods"] = "At least one departure is required";
  }
  formValidationErrors.value = errors;
  return Object.keys(errors).length === 0;
};

// Step 2 validation
const requiredFieldsStep2 = ["whatsIncluded", "whatsNotIncluded", "locations"];

const validateStep2 = () => {
  const errors = { ...formValidationErrors.value };
  // listingMedia must have at least one non-empty entry
  const hasMedia =
    Array.isArray(formData.value.listingMedia) &&
    formData.value.listingMedia.some(
      (m) => typeof m === "string" && m.trim() !== ""
    );
  if (!hasMedia) errors["listingMedia"] = "Please add at least one media URL";
  // mapsAndRoutes must have at least one non-empty entry
  const hasRoute =
    Array.isArray(formData.value.mapsAndRoutes) &&
    formData.value.mapsAndRoutes.some(
      (r) => typeof r === "string" && r.trim() !== ""
    );
  if (!hasRoute)
    errors["mapsAndRoutes"] = "Please add at least one map/route URL";
  // simple text fields
  requiredFieldsStep2.forEach((field) => {
    const value = formData.value[field];
    if (!value || (typeof value === "string" && value.trim() === "")) {
      errors[field] = "This field is required";
    }
  });
  formValidationErrors.value = errors;
  return ["listingMedia", "mapsAndRoutes", ...requiredFieldsStep2].every(
    (k) => !errors[k]
  );
};

// Step 3 validation
const requiredFieldsStep3 = ["personalPolicies"]; // radio selection required

const validateStep3 = () => {
  const errors = { ...formValidationErrors.value };
  // At least one itinerary item
  if (!itineraries.value || itineraries.value.length === 0) {
    errors["itineraries"] = "Please add at least one day in your itinerary";
  }
  // Radio must be selected and not empty
  requiredFieldsStep3.forEach((field) => {
    const value = formData.value[field];
    if (!value || (typeof value === "string" && value.trim() === "")) {
      errors[field] = "This field is required";
    }
  });
  formValidationErrors.value = errors;
  return ["itineraries", ...requiredFieldsStep3].every((k) => !errors[k]);
};

const hasFieldError = (fieldName) => !!formValidationErrors.value[fieldName];
const clearFieldError = (fieldName) => {
  if (formValidationErrors.value[fieldName])
    delete formValidationErrors.value[fieldName];
};

const goToNextStep = () => {
  if (currentStep.value === 0) {
    if (validateStep1()) {
      currentStep.value++;
    } else {
      const firstError = document.querySelector(".field-error");
      if (firstError)
        firstError.scrollIntoView({ behavior: "smooth", block: "center" });
    }
  } else if (currentStep.value === 1) {
    if (validateStep2()) {
      currentStep.value++;
    } else {
      const firstError = document.querySelector(".field-error");
      if (firstError)
        firstError.scrollIntoView({ behavior: "smooth", block: "center" });
    }
  } else {
    currentStep.value++;
  }
};

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

const openItineraryModal = () => {
  console.log("openItineraryModal called");
  console.log("Current showItineraryDialog value:", showItineraryDialog.value);
  editingItineraryIndex.value = -1; // Reset editing index for new itineraries
  showItineraryDialog.value = true;
  console.log("New showItineraryDialog value:", showItineraryDialog.value);
};

const openSpecialAddonsModal = () => {
  editingAddonIndex.value = -1; // Reset editing index for new addons
  showSpecialAddonsDialog.value = true;
};

const openPeriodsModal = () => {
  editingPeriodIndex.value = -1; // Reset editing index for new periods
  showPeriodsDialog.value = true;
};

const handlePeriodsDone = async (newPeriods, editingIndex) => {
  // Close the modal immediately
  showPeriodsDialog.value = false;

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

    // Clear validation error when periods are added
    clearFieldError("periods");

    console.log("Total periods count:", periods.value.length);
  } catch (error) {
    console.error("Error in handlePeriodsDone:", error);
  }
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

    // Update numbering
    itineraries.value.forEach((itinerary, index) => {
      itinerary.number = index + 1;
    });

    // Clear validation error if any
    clearFieldError("itineraries");

    showItineraryDialog.value = false;
  } catch (error) {
    console.error("Error in handleItineraryDone:", error);
  }
};

// Handle special addon completion
const handleSpecialAddonDone = (addonData, editingIndex = -1) => {
  try {
    if (editingIndex >= 0) {
      // Editing existing addon
      specialAddons.value[editingIndex] = addonData[0];
      editingAddonIndex.value = -1;
    } else {
      // Adding new addons
      if (specialAddons.value.length > 0) {
        const newAddons = addonData.filter((newAddon) => {
          return !specialAddons.value.some(
            (existingAddon) =>
              existingAddon.title === newAddon.title &&
              existingAddon.description === newAddon.description &&
              existingAddon.price === newAddon.price
          );
        });

        if (newAddons.length > 0) {
          specialAddons.value = [...specialAddons.value, ...newAddons];
        }
      } else {
        specialAddons.value = addonData;
      }
    }

    // Update numbering
    specialAddons.value.forEach((addon, index) => {
      addon.number = index + 1;
    });

    showSpecialAddonsDialog.value = false;
  } catch (error) {
    console.error("Error in handleSpecialAddonDone:", error);
  }
};

// Edit period
const editPeriod = (index) => {
  editingPeriodIndex.value = index;
  showPeriodsDialog.value = true;
};

// Remove period
const removePeriod = (index) => {
  periods.value.splice(index, 1);
  // Update numbers
  periods.value.forEach((period, idx) => {
    period.number = idx + 1;
  });
};

// Edit itinerary item
const editItineraryItem = (index) => {
  editingItineraryIndex.value = index;
  showItineraryDialog.value = true;
};

// Remove itinerary
const removeItinerary = (index) => {
  itineraries.value.splice(index, 1);
  // Update numbers
  itineraries.value.forEach((itinerary, idx) => {
    itinerary.number = idx + 1;
  });
};

// Edit special addon
const editSpecialAddon = (index) => {
  editingAddonIndex.value = index;
  showSpecialAddonsDialog.value = true;
};

// Remove special addon
const removeSpecialAddon = (index) => {
  specialAddons.value.splice(index, 1);
  // Update numbers
  specialAddons.value.forEach((addon, idx) => {
    addon.number = idx + 1;
  });
};

// Drag and drop handlers for periods
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

// Drag and drop handlers for itineraries
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

// Drag and drop handlers for special addons
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

const onSubmit = async () => {
  loading.value = true;

  try {
    // Validate step 3 before submitting
    if (!validateStep3()) {
      const firstError = document.querySelector(".field-error");
      if (firstError)
        firstError.scrollIntoView({ behavior: "smooth", block: "center" });
      loading.value = false;
      return;
    }

    // Create listing if it doesn't exist
    if (!listingId.value) {
      await createListing();
    }

    // Update listing with form data
    await updateListing();

    // Generate random adventure ID
    const randomId = Math.floor(Math.random() * 90000) + 10000;
    submissionData.value = {
      adventureTitle: formData.value.listingTitle || "Adventure Title",
      adventureId: randomId.toString().padStart(5, "0"),
    };
    showConfirmation.value = true;
  } catch (error) {
    console.error("Submission failed:", error);
    alert("Submission failed: " + (error.message || "Unknown error"));
  } finally {
    loading.value = false;
  }
};

// Create listing function
async function createListing() {
  loading.value = true;
  try {
    console.log("Creating new multi-date listing...");

    // Get CSRF token
    const token = document
      .querySelector('meta[name="csrf-token"]')
      ?.getAttribute("content");

    // Get authentication token
    const accessToken = useCookie("accessToken").value;

    // Get user ID from cookies or session
    const userDataCookie = useCookie("userData");
    const userData = userDataCookie.value;
    const userId = userData?.id || userData?.user_id || 1;

    console.log("Creating listing for user ID:", userId);

    const res = await fetch("/api/listings", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": token || "",
        "Authorization": accessToken ? `Bearer ${accessToken}` : "",
        Accept: "application/json",
      },
      body: JSON.stringify({
        user_id: userId,
        listing_type: "multi-date",
        status: "draft",
      }),
    });

    if (!res.ok) {
      const errorText = await res.text();
      console.error("Failed to create listing:", res.status, errorText);
      throw new Error(`Failed to create listing: ${res.status} ${errorText}`);
    }

    const data = await res.json();
    console.log("Listing created successfully:", data);

    if (!data.data || !data.data.id) {
      console.error("No data or ID in response:", data);
      throw new Error("No ID returned from listing creation");
    }

    listingId.value = data.data.id;
    console.log("listingId set to:", listingId.value);
    return data.data.id;
  } catch (e) {
    console.error("Error in createListing:", e);
    alert("خطا در ساخت لیستینگ جدید: " + e.message);
    throw e;
  } finally {
    loading.value = false;
  }
}

// Update listing function
async function updateListing() {
  if (!listingId.value) return;
  loading.value = true;
  try {
    // Map form data to database field names
    const updateData = {
      listing_title: formData.value.listingTitle,
      listing_description: formData.value.listingDescription,
      starting_date: formData.value.startDate,
      finishing_date: formData.value.endDate,
      price: formData.value.price ? parseFloat(formData.value.price) : null,
      min_capacity: 1, // Default for multi-date
      max_capacity: formData.value.maxParticipants
        ? parseInt(formData.value.maxParticipants)
        : null,
      subtitle: formData.value.subtitle,
      experience_level: formData.value.experienceLevel,
      fitness_level: formData.value.fitnessLevel,
      activities_included: formData.value.activitiesIncluded,
      group_language: formData.value.groupLanguage,
      maps_and_routes: formData.value.mapsAndRoutes,
      locations: formData.value.locations,
      listing_media: formData.value.listingMedia,
      promotional_video: formData.value.promotionalVideo,
      whats_included: formData.value.whatsIncluded,
      whats_not_included: formData.value.whatsNotIncluded,
      additional_notes: formData.value.additionalNotes,
      providers_faq: formData.value.providersFAQ,
      personal_policies: formData.value.personalPolicies,
      personal_policies_text: formData.value.requirements,
      terms_accepted: formData.value.termsAccepted,
      status: "draft",
    };

    console.log("Updating multi-date listing with data:", updateData);

    const res = await fetch(`/api/admin/listings/${listingId.value}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(updateData),
    });

    if (!res.ok) {
      const errorText = await res.text();
      console.error("Failed to update listing:", res.status, errorText);
      throw new Error(`Failed to update listing: ${res.status} ${errorText}`);
    }

    const data = await res.json();
    console.log("Multi-date listing updated successfully:", data);

    // Save itineraries data if itineraries exist
    if (itineraries.value && itineraries.value.length > 0) {
      try {
        console.log("Saving itineraries data:", itineraries.value);
        const itinerariesResponse = await fetch(`/api/admin/listings/${listingId.value}/itineraries`, {
          method: "PUT",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ itineraries: itineraries.value }),
        });

        if (!itinerariesResponse.ok) {
          const errorText = await itinerariesResponse.text();
          console.error("Failed to save itineraries:", itinerariesResponse.status, errorText);
          throw new Error(`Failed to save itineraries: ${itinerariesResponse.status} ${errorText}`);
        }

        const itinerariesData = await itinerariesResponse.json();
        console.log("Itineraries saved successfully:", itinerariesData);
      } catch (itinerariesError) {
        console.error("Error saving itineraries:", itinerariesError);
        alert("خطا در ذخیره برنامه سفر: " + itinerariesError.message);
        throw itinerariesError;
      }
    }

    // Save special addons data if special addons exist
    if (specialAddons.value && specialAddons.value.length > 0) {
      try {
        // Ensure number field is string for all addons
        const addonsToSave = specialAddons.value.map(addon => ({
          ...addon,
          number: addon.number ? addon.number.toString() : "1"
        }));
        
        console.log("Saving special addons data:", addonsToSave);
        const specialAddonsResponse = await fetch(`/api/admin/listings/${listingId.value}/special-addons`, {
          method: "PUT",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ special_addons: addonsToSave }),
        });

        if (!specialAddonsResponse.ok) {
          const errorText = await specialAddonsResponse.text();
          console.error("Failed to save special addons:", specialAddonsResponse.status, errorText);
          throw new Error(`Failed to save special addons: ${specialAddonsResponse.status} ${errorText}`);
        }

        const specialAddonsData = await specialAddonsResponse.json();
        console.log("Special addons saved successfully:", specialAddonsData);
      } catch (specialAddonsError) {
        console.error("Error saving special addons:", specialAddonsError);
        alert("خطا در ذخیره افزونه‌های ویژه: " + specialAddonsError.message);
        throw specialAddonsError;
      }
    }

    // Save periods data if periods exist
    if (periods.value && periods.value.length > 0) {
      try {
        console.log("Saving periods data:", periods.value);
        const periodsResponse = await fetch(`/api/admin/listings/${listingId.value}/periods`, {
          method: "PUT",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ periods: periods.value }),
        });

        if (!periodsResponse.ok) {
          const errorText = await periodsResponse.text();
          console.error("Failed to save periods:", periodsResponse.status, errorText);
          throw new Error(`Failed to save periods: ${periodsResponse.status} ${errorText}`);
        }

        const periodsData = await periodsResponse.json();
        console.log("Periods saved successfully:", periodsData);
      } catch (periodsError) {
        console.error("Error saving periods:", periodsError);
        alert("خطا در ذخیره دوره‌ها: " + periodsError.message);
        throw periodsError;
      }
    }
  } catch (e) {
    console.error("Error updating listing:", e);
    alert("خطا در ذخیره لیستینگ: " + e.message);
    throw e;
  } finally {
    loading.value = false;
  }
}

// Initialize listing ID on mount
onMounted(async () => {
  if (!listingId.value) {
    await createListing();
  }
});

const createNewAdventure = () => {
  // Reset form data to initial state
  formData.value = {
    listingTitle: "",
    listingDescription: "",
    locations: "",
    groupLanguage: "",
    subtitle: "",
    activitiesIncluded: "",
    experienceLevel: "",
    fitnessLevel: "",
    // Step 2
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
    // Step 3
    price: "",
    maxParticipants: "",
    requirements: "",
    termsAccepted: false,
    personalPolicies: "not-sure",
  };

  itineraries.value = [];
  specialAddons.value = [];
  periods.value = [];
  currentStep.value = 0;
  showConfirmation.value = false;
  formValidationErrors.value = {};

  // Create new listing
  createListing();
};
</script>

<template>
  <!-- Confirmation Page -->
  <div v-if="showConfirmation" class="confirmation-page">
    <div class="confirmation-container">
      <div class="confirmation-card">
        <div class="confirmation-icon">
          <div class="icon-container">
            <img 
              src="/images/4svg/listingend.png" 
              alt="Listing End Icon"
              style="width: 120px; height: 120px; object-fit: contain;"
            />
          </div>
        </div>

        <h1 class="confirmation-title">
          Your Listing Just Submitted for Approval
        </h1>

        <div class="adventure-info">
          <div class="adventure-title">{{ submissionData.adventureTitle }}</div>
          <div class="adventure-id">
            Adventure ID : {{ submissionData.adventureId }}
          </div>
        </div>

        <p class="confirmation-description">
          Thank you for submitting your adventure! Our team is reviewing your
          listing, and we'll notify you once it's approved or if any changes are
          needed. In the meantime, you can view your submitted adventures or
          create a new one.
        </p>

        <div class="confirmation-buttons">
          <VBtn
            color="dark"
            variant="elevated"
            class="see-adventures-btn"
            @click="showAllAdventuresDialog = true"
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
  <div v-else class="custom-stepper mb-6">
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
                    <div
                      class="mb-4"
                      :class="{ 'field-error': hasFieldError('listingTitle') }"
                    >
                      <label
                        class="v-label text-body-2 mb-3 d-block"
                        style="
                          font-size: 16px !important;
                          font-weight: 400 !important;
                        "
                      >
                        Listing Title <span class="required-star">*</span>
                      </label>
                      <AppTextField
                        v-model="formData.listingTitle"
                        placeholder="The main title of Your listing"
                        :error="hasFieldError('listingTitle')"
                        :error-messages="formValidationErrors['listingTitle']"
                        @input="clearFieldError('listingTitle')"
                        :class="{
                          'field-error': hasFieldError('listingTitle'),
                        }"
                      />
                      <p
                        v-if="hasFieldError('listingTitle')"
                        class="text-caption text-error mt-1 mb-0"
                        style="font-size: 11px"
                      >
                        {{ formValidationErrors["listingTitle"] }}
                      </p>
                    </div>

                    <!-- Listing Description -->
                    <div
                      class="mb-4"
                      :class="{
                        'field-error': hasFieldError('listingDescription'),
                      }"
                    >
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
                        :error="hasFieldError('listingDescription')"
                        :error-messages="
                          formValidationErrors['listingDescription']
                        "
                        @input="clearFieldError('listingDescription')"
                        :class="{
                          'field-error': hasFieldError('listingDescription'),
                        }"
                      />
                      <p
                        v-if="hasFieldError('listingDescription')"
                        class="text-caption text-error mt-1 mb-0"
                        style="font-size: 11px"
                      >
                        {{ formValidationErrors["listingDescription"] }}
                      </p>
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
                    <div
                      class="mb-4"
                      :class="{ 'field-error': hasFieldError('locations') }"
                    >
                      <label
                        class="v-label text-body-2 mb-3 d-block"
                        style="
                          font-size: 16px !important;
                          font-weight: 400 !important;
                        "
                      >
                        Locations <span class="required-star">*</span>
                      </label>
                      <AppSelect
                        v-model="formData.locations"
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
                        :error="hasFieldError('locations')"
                        :error-messages="formValidationErrors['locations']"
                        @input="clearFieldError('locations')"
                        :class="{ 'field-error': hasFieldError('locations') }"
                      />
                      <p
                        v-if="hasFieldError('locations')"
                        class="text-caption text-error mt-1 mb-0"
                        style="font-size: 11px"
                      >
                        {{ formValidationErrors["locations"] }}
                      </p>
                    </div>

                    <!-- Group Language -->
                    <div class="mb-4">
                      <label
                        class="v-label text-body-2 mb-3 d-block"
                        style="
                          font-size: 16px !important;
                          font-weight: 400 !important;
                        "
                      >
                        Group Language
                      </label>
                      <AppSelect
                        v-model="formData.groupLanguage"
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
                      />
                    </div>
                  </VCol>

                  <!-- Right column -->
                  <VCol cols="12" md="6">
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
                        placeholder="A tagline for the listing"
                      />
                    </div>

                    <!-- Activities Included -->
                    <div
                      class="mb-4"
                      :class="{
                        'field-error': hasFieldError('activitiesIncluded'),
                      }"
                    >
                      <label
                        class="v-label text-body-2 mb-3 d-block"
                        style="
                          font-size: 16px !important;
                          font-weight: 400 !important;
                        "
                      >
                        Activities Included <span class="required-star">*</span>
                      </label>
                      <AppSelect
                        v-model="formData.activitiesIncluded"
                        placeholder="Select the activities that are included in your adventure"
                        :items="[
                          // Land-Based Activities
                          'Skiing',
                          'Snowboarding',
                          'Backcountry Skiing',
                          'Snowshoeing',
                          'Ice Climbing',
                          'Heli‑Skiing',
                          'Road Cycling',
                          'Bikepacking',
                          'Mountain Biking',
                          'Rock Climbing',
                          'Bouldering',
                          'Mountaineering',
                          'Hiking',
                          'Wild Camping',
                          'Trail Running',
                          'Horseback Riding',
                          'ATV/Quad Biking',
                          'Glacier Hiking',
                          'Sandboarding',
                          'Avalanche Education',
                          // Water-Based Activities
                          'Windsurfing',
                          'Kitesurfing',
                          'Kayaking',
                          'Canoeing',
                          'Rafting',
                          'Canyoning',
                          'Freediving',
                          'Surfing',
                          'Snorkeling',
                          'Scuba Diving',
                          'Stand‑Up Paddleboarding',
                          'Sailing',
                          // Air-Based Activities
                          'Base Jumping',
                          'Skydiving',
                          'Bungee Jumping',
                          'Hot Air Ballooning',
                          'Parachuting',
                          'Paragliding',
                          'Snow Kiting',
                          // Additional Activities
                          'Photography Tours',
                          'Cultural Tours',
                          'Wildlife Tours',
                          'Cooking Classes',
                          'Wine Tasting',
                          'Historical Tours',
                          'Other',
                        ]"
                        :error="hasFieldError('activitiesIncluded')"
                        :error-messages="
                          formValidationErrors['activitiesIncluded']
                        "
                        @input="clearFieldError('activitiesIncluded')"
                        :class="{
                          'field-error': hasFieldError('activitiesIncluded'),
                        }"
                      />
                      <p
                        v-if="hasFieldError('activitiesIncluded')"
                        class="text-caption text-error mt-1 mb-0"
                        style="font-size: 11px"
                      >
                        {{ formValidationErrors["activitiesIncluded"] }}
                      </p>
                    </div>

                    <!-- Listing Departures -->
                    <div class="mb-6">
                      <label
                        class="v-label text-body-2 mb-3 d-block"
                        style="
                          font-size: 16px !important;
                          font-weight: 400 !important;
                        "
                      >
                        Listing Departures <span class="required-star">*</span>
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
                        :class="{ 'field-error': hasFieldError('periods') }"
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
                        <p
                          v-if="hasFieldError('periods')"
                          class="text-caption text-error mt-2 mb-0"
                          style="font-size: 11px"
                        >
                          {{ formValidationErrors["periods"] }}
                        </p>
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
                            :class="{
                              'field-error': hasFieldError('listingMedia'),
                            }"
                            @input="clearFieldError('listingMedia')"
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
                        v-if="hasFieldError('listingMedia')"
                        class="text-caption text-error mt-1 mb-0"
                        style="font-size: 11px"
                      >
                        {{ formValidationErrors["listingMedia"] }}
                      </p>
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
                            :class="{
                              'field-error': hasFieldError('mapsAndRoutes'),
                            }"
                            @input="clearFieldError('mapsAndRoutes')"
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
                      <p
                        v-if="hasFieldError('mapsAndRoutes')"
                        class="text-caption text-error mt-1 mb-0"
                        style="font-size: 11px"
                      >
                        {{ formValidationErrors["mapsAndRoutes"] }}
                      </p>
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
                        :error="hasFieldError('whatsIncluded')"
                        :error-messages="formValidationErrors['whatsIncluded']"
                        @input="clearFieldError('whatsIncluded')"
                        :class="{
                          'field-error': hasFieldError('whatsIncluded'),
                        }"
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
                        :error="hasFieldError('whatsNotIncluded')"
                        :error-messages="
                          formValidationErrors['whatsNotIncluded']
                        "
                        @input="clearFieldError('whatsNotIncluded')"
                        :class="{
                          'field-error': hasFieldError('whatsNotIncluded'),
                        }"
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
                      <p class="section-description">
                        Add your Itinerary/Accommodations and all related
                        details here
                      </p>
                      <p
                        v-if="hasFieldError('itineraries')"
                        class="text-caption text-error mt-1 mb-3"
                        style="font-size: 11px"
                      >
                        {{ formValidationErrors["itineraries"] }}
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
                            <div class="d-flex align-center" style="padding-left: 20px;">
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
                        :class="{
                          'field-error': hasFieldError('personalPolicies'),
                        }"
                        @update:model-value="
                          clearFieldError('personalPolicies')
                        "
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
                      <p
                        v-if="hasFieldError('personalPolicies')"
                        class="text-caption text-error mt-1 mb-0"
                        style="font-size: 11px"
                      >
                        {{ formValidationErrors["personalPolicies"] }}
                      </p>
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
              </VWindowItem>
            </VWindow>
            <div
              class="d-flex flex-wrap gap-4 justify-sm-space-between justify-center mt-8"
            >
              <VBtn
                color="secondary"
                variant="tonal"
                @click="
                  currentStep === 0
                    ? router.push({ name: 'listing' })
                    : currentStep--
                "
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
              <VBtn v-else class="next-btn-dark" @click="goToNextStep">
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
                      This is the first step towards your next big adventure,
                      but you're not in this alone. Along your trip creation
                      process, we will be right here to help you.
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
                          filter: blur(1px);
                          position: relative;
                          cursor: not-allowed;
                        "
                        @click="showComingSoonPopup = true"
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
                    <div class="video-container" style="position: relative;">
                      <iframe
                        src="https://www.youtube.com/embed/gwztooshVlQ"
                        title="Tutorial Video"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        class="youtube-video"
                        style="width: 100%; height: 200px; border-radius: 8px; filter: blur(5px); pointer-events: none;"
                      ></iframe>
                      <div class="video-overlay" style="
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background: rgba(0, 0, 0, 0.5);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        border-radius: 8px;
                        pointer-events: none;
                        z-index: 10;
                      ">
                        <div class="coming-soon-text" style="
                          color: white;
                          font-size: 18px;
                          font-weight: bold;
                          text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
                        ">
                          Coming Soon
                        </div>
                      </div>
                    </div>
                  </div>
                </VCol>
              </VRow>
            </div>
          </VForm>
        </VCardText>
      </VCard>
    </div>

    <PeriodsDialog
      v-model="showPeriodsDialog"
      :periods="periods"
      :editing-index="editingPeriodIndex"
      :use-period-terminology="false"
      @close="showPeriodsDialog = false"
      @done="handlePeriodsDone"
    />

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

    <ConfirmationDialog
      v-model="showConfirmation"
      :submission-data="submissionData"
      @close="showConfirmation = false"
      @create-new-adventure="createNewAdventure"
    />

    <!-- All Adventures Dialog -->
    <AllAdventuresDialog v-model="showAllAdventuresDialog" />

    <!-- Coming Soon Popup -->
    <VDialog v-model="showComingSoonPopup" max-width="400">
      <VCard>
        <VCardTitle class="text-center">
          <VIcon icon="tabler-clock" size="24" class="mr-2" />
          Coming Soon
        </VCardTitle>
        <VDivider />
        <VCardText class="text-center pa-6">
          <VIcon icon="tabler-video" size="48" color="primary" class="mb-4" />
          <h6 class="text-h6 mb-2">Tutorial Video</h6>
          <p class="text-medium-emphasis">
            We're working on creating an amazing tutorial video for you. 
            It will be available soon!
          </p>
        </VCardText>
        <VCardActions class="justify-center pb-4">
          <VBtn 
            color="primary" 
            @click="showComingSoonPopup = false"
          >
            Got it!
          </VBtn>
        </VCardActions>
      </VCard>
    </VDialog>
  </div>
</template>

<style scoped>
.auto-save-indicator {
  position: fixed;
  top: 80px;
  right: 20px;
  z-index: 9999;
  
  .save-status {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 10px;
    background: rgba(255, 255, 255, 0.15);
    color: #333;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 400;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    animation: slideIn 0.3s ease-out;
    
    &.saving {
      background: rgba(25, 118, 210, 0.1);
      color: #1976d2;
      border-color: rgba(25, 118, 210, 0.2);
      
      .v-icon {
        color: #1976d2;
      }
    }
    
    &.saved {
      background: rgba(76, 175, 80, 0.1);
      color: #2e7d32;
      border-color: rgba(76, 175, 80, 0.2);
      
      .v-icon {
        color: #4caf50;
      }
    }
    
    .v-icon {
      font-size: 12px;
      
      &.spinning {
        animation: spin 1s linear infinite;
      }
    }
  }
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
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
  color: #000000;
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
  color: #ff4444 !important;
  font-weight: bold;
  margin-left: 4px;
  display: inline-block;
}

/* Field error styling */
.field-error {
  border-color: #ff4444 !important;
}

.field-error .v-field__outline {
  border-color: #ff4444 !important;
}

/* Error message styling */
.v-messages__message {
  color: #ff4444 !important;
  font-size: 12px !important;
  margin-top: 4px !important;
}

/* Ensure fields remain simple without hover effects */
.v-field {
  transition: none !important;
}

.v-field__outline {
  transition: none !important;
}

.v-field__field {
  transition: none !important;
}

.v-field--focused .v-field__outline {
  border-color: #ec8d22 !important;
}

/* Remove any hover effects on labels */
.v-label {
  transition: none !important;
}

.v-field__label {
  transition: none !important;
}

/* Rich text area styling */
.rich-text-area {
  border: none !important; /* remove extra border under validation */
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
  color: #000000;
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
  padding: 0; /* remove extra padding to match simple button area */
  background: transparent !important; /* remove gray background */
  border-radius: 0;
  border: none !important; /* remove dashed border */
}

/* Section styling */
.section-title {
  font-family: "Karla", sans-serif;
  font-size: 18px;
  font-weight: 600;
  color: #333;
  margin-bottom: 8px;
}

.section-description {
  font-family: "Karla", sans-serif;
  font-size: 14px;
  color: #666;
  margin-bottom: 16px;
}

/* Itinerary styling */
.itinerary-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.itinerary-title {
  font-family: "Karla", sans-serif;
  font-size: 18px;
  font-weight: 600;
  color: #333;
  margin: 0;
}

.itinerary-list-container {
  background: #fff;
  border-radius: 12px;
  border: 1px solid #e0e0e0;
  overflow: hidden;
}

.itinerary-list-item {
  padding: 16px;
  border-bottom: 1px solid #f0f0f0;
  transition: background-color 0.2s;
  cursor: move;
}

.itinerary-list-item:last-child {
  border-bottom: none;
}

.itinerary-list-item:hover {
  background-color: #f8f8f8;
}

.itinerary-actions {
  display: flex;
  align-items: center;
  gap: 4px;
}

/* Special Addons styling */
.special-addons-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.special-addons-title {
  font-family: "Karla", sans-serif;
  font-size: 18px;
  font-weight: 600;
  color: #333;
  margin: 0;
}

.special-addons-list {
  background: #fff;
  border-radius: 12px;
  border: 1px solid #e0e0e0;
  overflow: hidden;
}

.special-addon-item {
  padding: 16px;
  border-bottom: 1px solid #f0f0f0;
  transition: background-color 0.2s;
  cursor: move;
}

.special-addon-item:last-child {
  border-bottom: none;
}

.special-addon-item:hover {
  background-color: #f8f8f8;
}

.addon-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.addon-left {
  display: flex;
  align-items: flex-start;
  flex: 1;
}

.addon-badge {
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

.addon-info {
  flex: 1;
  min-width: 0;
}

.addon-title-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.addon-title {
  font-family: "Karla", sans-serif;
  font-size: 16px;
  font-weight: 600;
  color: #333;
  flex: 1;
  margin-right: 8px;
}

.addon-description {
  margin-bottom: 8px;
}

.description-text {
  font-family: "Karla", sans-serif;
  font-size: 14px;
  color: #666;
}

.addon-price {
  font-family: "Karla", sans-serif;
  font-size: 16px;
  font-weight: 600;
  color: #333;
  text-align: right;
}

.addon-actions {
  display: flex;
  align-items: center;
  gap: 4px;
  margin-left: 12px;
}

/* Add item button styling */
.add-item-btn {
  min-height: 40px;
  padding: 0 20px;
  font-size: 14px;
  text-transform: none;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.add-item-btn:hover {
  background-color: #d67d1a !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Add more button styling */
.add-more-btn {
  min-height: 36px;
  padding: 0 16px;
  font-size: 14px;
  text-transform: none;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.add-more-btn:hover {
  background-color: #d67d1a !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Confirmation Page Styles */
.confirmation-page {
  min-height: 100vh;
  background-color: #f5f5f5;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.confirmation-container {
  width: 100%;
  max-width: 95vw;
}

.confirmation-card {
  background: white;
  border-radius: 12px;
  padding: 48px 40px;
  text-align: center;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.confirmation-icon {
  margin-bottom: 32px;
}
.icon-container {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
}
.icon-left,
.icon-right {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}
.icon-lines {
  display: flex;
  flex-direction: column;
  gap: 2px;
  margin-bottom: 4px;
}
.line {
  width: 24px;
  height: 2px;
  background-color: #333;
  border-radius: 1px;
}
.checkmark,
.cross {
  color: #ec8d22;
  font-size: 16px;
  font-weight: bold;
}

.confirmation-title {
  font-family: "Anton", sans-serif;
  font-size: 28px;
  font-weight: 700;
  color: #333;
  margin-bottom: 24px;
  line-height: 1.3;
}

.adventure-info {
  margin-bottom: 24px;
}
.adventure-title {
  font-family: "Karla", sans-serif;
  font-size: 18px;
  font-weight: 600;
  color: #ec8d22;
  margin-bottom: 8px;
}
.adventure-id {
  font-family: "Karla", sans-serif;
  font-size: 16px;
  font-weight: 500;
  color: #ec8d22;
}

.confirmation-description {
  font-family: "Karla", sans-serif;
  font-size: 16px;
  color: #666;
  line-height: 1.6;
  margin-bottom: 32px;
  max-width: 500px;
  margin-left: auto;
  margin-right: auto;
}

.confirmation-buttons {
  display: flex;
  gap: 16px;
  justify-content: center;
  flex-wrap: wrap;
}
.see-adventures-btn {
  background-color: #111 !important;
  color: #fff !important;
  border-radius: 8px;
  font-weight: 500;
  font-size: 14px;
  padding: 12px 24px;
  min-height: 44px;
  text-transform: none;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  transition: all 0.2s ease;
}
.see-adventures-btn:hover {
  background-color: #222 !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}
.add-another-btn {
  background-color: #ec8d22 !important;
  color: #fff !important;
  border-radius: 8px;
  font-weight: 500;
  font-size: 14px;
  padding: 12px 24px;
  min-height: 44px;
  text-transform: none;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  transition: all 0.2s ease;
}
.add-another-btn:hover {
  background-color: #d67d1a !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Responsive design for confirmation page */
@media (max-width: 768px) {
  .confirmation-card {
    padding: 32px 24px;
  }
  .confirmation-title {
    font-size: 20px;
  }
  .adventure-title {
    font-size: 16px;
  }
  .adventure-id {
    font-size: 14px;
  }
  .confirmation-description {
    font-size: 14px;
  }
  .confirmation-buttons {
    flex-direction: column;
  }
  .see-adventures-btn,
  .add-another-btn {
    width: 100%;
  }
}

/* Fix radio button sizing and clipping */
:deep(.v-radio) {
  overflow: visible;
}
:deep(.v-radio .v-selection-control) {
  min-height: 30px;
  padding: 6px 4px;
  overflow: visible;
}
:deep(.v-radio .v-selection-control__wrapper) {
  width: 24px;
  height: 24px;
  padding: 2px;
  overflow: visible;
  box-sizing: content-box;
}
:deep(.v-radio .v-selection-control__input) {
  width: 20px;
  height: 20px;
}
:deep(.v-radio .v-selection-control__ripple) {
  inset: -8px;
}
</style>
