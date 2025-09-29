<script setup>
import AllAdventuresDialog from "@/components/dialogs/AllAdventuresDialog.vue";
import ItineraryAccommodationDialog from "@/components/dialogs/ItineraryAccommodationDialog.vue";
import PackageDialog from "@/components/dialogs/PackageDialog.vue";
import SpecialAddonsDialog from "@/components/dialogs/SpecialAddonsDialog.vue";
import { useAutoSave } from "@/composables/useAutoSave";
import { onMounted, ref, watch } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const numberedSteps = [
  {
    title: "Basic Information",
  },
  {
    title: "Detailed Information",
  },
  {
    title: "Logistics",
  },
];

const currentStep = ref(0);
const loading = ref(false);
const listingId = ref(null);
const itineraries = ref([]);
const specialAddons = ref([]);
const packages = ref([]);
const editingAddonIndex = ref(-1); // برای ویرایش addon
const editingItineraryIndex = ref(-1); // برای ویرایش روز
const editingPackageIndex = ref(-1); // برای ویرایش package
const showConfirmation = ref(false); // برای نمایش صفحه تأیید
const formValidationErrors = ref({});
const submissionData = ref({
  adventureTitle: "",
  adventureId: "",
});

// Required fields for step 1 (Basic Information)
const requiredFieldsStep1 = [
  "startingDate",
  "finishingDate",
  "price",
  "minCapacity",
  "maxCapacity",
  "packages",
];

// Validation function for step 1
const validateStep1 = () => {
  const errors = {};

  requiredFieldsStep1.forEach((field) => {
    if (field === "packages") {
      if (!packages.value || packages.value.length === 0) {
        errors[field] = "At least one package is required";
      }
    } else if (field === "minCapacity" || field === "maxCapacity") {
      if (!formData.value[field] || formData.value[field].trim() === "") {
        errors[field] = "This field is required";
      }
    } else {
      if (!formData.value[field] || formData.value[field].trim() === "") {
        errors[field] = "This field is required";
      }
    }
  });

  formValidationErrors.value = errors;
  return Object.keys(errors).length === 0;
};

// Check if field has error
const hasFieldError = (fieldName) => {
  return formValidationErrors.value[fieldName];
};

// Check if field is required for step 1
const isFieldRequiredStep1 = (fieldName) => {
  return requiredFieldsStep1.includes(fieldName);
};

// Clear validation errors when field changes
const clearFieldError = (fieldName) => {
  if (formValidationErrors.value[fieldName]) {
    delete formValidationErrors.value[fieldName];
  }
};

async function fetchItineraries() {
  if (!listingId.value) return;
  loading.value = true;
  try {
    const res = await fetch(`/api/listings/${listingId.value}/itineraries`);
    const data = await res.json();
    itineraries.value = data.data || [];
  } catch (e) {
    alert("خطا در دریافت لیست روزها");
  } finally {
    loading.value = false;
  }
}

async function fetchSpecialAddons(listingIdParam = null) {
  const id = listingIdParam || listingId.value;
  if (!id) return;
  loading.value = true;
  try {
    const res = await fetch(`/api/listings/${id}/special-addons`);
    const data = await res.json();
    specialAddons.value = data.data || [];
  } catch (e) {
    alert("خطا در دریافت لیست افزونه‌ها");
  } finally {
    loading.value = false;
  }
}

watch(currentStep, async (val) => {
  if (val === 2) {
    await fetchItineraries();
    await fetchSpecialAddons();
  }
});

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
  packages: [],
  // Step 2 fields
  activitiesIncluded: "",
  groupLanguage: "",
  mapsAndRoutes: [""],
  locations: "",
  listingMedia: [""],
  promotionalVideo: [""],
  whatsIncluded: "",
  whatsNotIncluded: "",
  additionalNotes: "",
  providersFAQ: "",
  // Step 3 fields
  itineraryAccommodation: "",
  personalPolicies: "",
  personalPoliciesText: "",
  specialAddons: "",
  termsAccepted: false,
  autoSaveId: null, // Add auto-save ID field
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
} = useAutoSave(formData, 'listing-form-single-date', {
  debounceMs: 300, // Save after 300ms of inactivity
  saveToDatabase: true,
  listingType: 'single-date',
  apiEndpoint: '/auto-save-listings',
  onSave: (data) => {
    console.log('Form data auto-saved:', data);
  },
  onLoad: (data, meta) => {
    console.log('Form data loaded from storage:', data);
    if (meta) {
      console.log('Last saved:', new Date(meta.timestamp).toLocaleString());
    }
  }
});

const showItineraryDialog = ref(false);
const showSpecialAddonsDialog = ref(false);
const showPackageDialog = ref(false);
const showAllAdventuresDialog = ref(false);
const showComingSoonPopup = ref(false);

// Handle certifications upload
const handleCertificationsUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    formData.value.certifications = file;
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

async function createListing() {
  loading.value = true;
  try {
    console.log("Creating new listing...");

    // دریافت CSRF token
    const token = document
      .querySelector('meta[name="csrf-token"]')
      ?.getAttribute("content");

    // Get user ID from cookies or session
    const userDataCookie = useCookie("userData");
    const userData = userDataCookie.value;
    const userId = userData?.id || userData?.user_id || 1;

    console.log("Creating listing for user ID:", userId);

    // فقط فیلدهای اولیه را ارسال کن
    const res = await fetch("/api/listings", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": token || "",
        Accept: "application/json",
      },
      body: JSON.stringify({
        user_id: userId,
        listing_type: "single-date",
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

    // اطمینان از اینکه data.data.id وجود دارد (API response در data.data قرار دارد)
    if (!data.data || !data.data.id) {
      console.error("No data or ID in response:", data);
      throw new Error("No ID returned from listing creation");
    }

    listingId.value = data.data.id;
    console.log("listingId set to:", listingId.value);

    // مقداردهی اولیه فرم با داده دریافتی (در صورت نیاز)
    return data.data.id; // return the ID
  } catch (e) {
    console.error("Error in createListing:", e);
    alert("خطا در ساخت لیستینگ جدید: " + e.message);
    throw e; // خطا را دوباره throw کن تا caller بتواند آن را مدیریت کند
  } finally {
    loading.value = false;
  }
}

async function updateListing() {
  if (!listingId.value) return;
  loading.value = true;
  try {
    // Map form data to database field names
    const updateData = {
      listing_title: formData.value.listingTitle,
      listing_description: formData.value.listingDescription,
      starting_date: formData.value.startingDate,
      finishing_date: formData.value.finishingDate,
      price: formData.value.price ? parseFloat(formData.value.price) : null,
      min_capacity: formData.value.minCapacity
        ? parseInt(formData.value.minCapacity)
        : null,
      max_capacity: formData.value.maxCapacity
        ? parseInt(formData.value.maxCapacity)
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
      personal_policies_text: formData.value.personalPoliciesText,
      terms_accepted: formData.value.termsAccepted,
      status: "submitted", // Change to submitted when form is submitted
      auto_save_id: formData.value.autoSaveId || null, // Include auto-save ID if exists
    };

    console.log("Updating listing with data:", updateData);

    const res = await fetch(`/api/listings/${listingId.value}`, {
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
    console.log("Listing updated successfully:", data);

    // مقداردهی مجدد فرم با داده دریافتی (در صورت نیاز)
  } catch (e) {
    console.error("Error updating listing:", e);
    alert("خطا در ذخیره لیستینگ: " + e.message);
    throw e; // Re-throw to handle in onSubmit
  } finally {
    loading.value = false;
  }
}

async function addItinerary(newDay) {
  if (!listingId.value) return;
  loading.value = true;
  try {
    console.log("Adding itinerary:", newDay);
    const response = await fetch(
      `/api/listings/${listingId.value}/itineraries`,
      {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(newDay),
      }
    );

    if (response.ok) {
      console.log("Itinerary added successfully");
    } else {
      console.error("Failed to add itinerary:", response.statusText);
    }
  } catch (e) {
    console.error("Error adding itinerary:", e);
    alert("خطا در افزودن روز جدید");
  } finally {
    loading.value = false;
  }
}

async function editItinerary(dayId, updatedDay) {
  if (!listingId.value) return;
  loading.value = true;
  try {
    console.log("Editing itinerary:", dayId, updatedDay);
    const response = await fetch(
      `/api/listings/${listingId.value}/itineraries/${dayId}`,
      {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(updatedDay),
      }
    );

    if (response.ok) {
      console.log("Itinerary updated successfully");
    } else {
      console.error("Failed to update itinerary:", response.statusText);
    }
  } catch (e) {
    console.error("Error editing itinerary:", e);
    alert("خطا در ویرایش روز");
  } finally {
    loading.value = false;
  }
}

async function deleteItinerary(dayId) {
  if (!listingId.value) return;
  loading.value = true;
  try {
    console.log("Deleting itinerary:", dayId);
    const response = await fetch(
      `/api/listings/${listingId.value}/itineraries/${dayId}`,
      {
        method: "DELETE",
      }
    );

    if (response.ok) {
      console.log("Itinerary deleted successfully");
    } else {
      console.error("Failed to delete itinerary:", response.statusText);
    }
  } catch (e) {
    console.error("Error deleting itinerary:", e);
    alert("خطا در حذف روز");
  } finally {
    loading.value = false;
  }
}

async function handleItineraryDone(itineraryData, editingIndex = -1) {
  try {
    console.log("=== handleItineraryDone called ===");
    console.log("itineraryData:", itineraryData);
    console.log("editingIndex:", editingIndex);
    console.log("Current itineraries before update:", itineraries.value);
    console.log("itineraryData length:", itineraryData.length);

    if (editingIndex >= 0) {
      // Editing existing itinerary - replace the specific itinerary
      console.log("Editing mode - replacing itinerary at index:", editingIndex);
      itineraries.value[editingIndex] = itineraryData[0];
      editingItineraryIndex.value = -1; // Reset editing index
    } else {
      // Adding new itineraries - only add truly new itineraries
      console.log("Adding mode - checking for new itineraries");

      // If we have existing itineraries, we need to filter out the ones that already exist
      if (itineraries.value.length > 0) {
        // Find only the new itineraries that don't exist in the current list
        const newItineraries = itineraryData.filter((newItinerary) => {
          // Check if this itinerary already exists by comparing titles and descriptions
          return !itineraries.value.some(
            (existingItinerary) =>
              existingItinerary.title === newItinerary.title &&
              existingItinerary.description === newItinerary.description &&
              existingItinerary.accommodation === newItinerary.accommodation &&
              existingItinerary.location === newItinerary.location
          );
        });

        console.log("New itineraries found:", newItineraries);

        // Only add the new itineraries
        if (newItineraries.length > 0) {
          itineraries.value = [...itineraries.value, ...newItineraries];
        }
      } else {
        // First time adding itineraries - add all
        console.log("First time adding itineraries");
        itineraries.value = itineraryData;
      }
    }

    // Update the numbering for all itineraries
    itineraries.value.forEach((itinerary, index) => {
      itinerary.number = index + 1;
    });

    console.log("Itineraries after update:", itineraries.value);
    console.log("Total itineraries count:", itineraries.value.length);
  } catch (error) {
    console.error("Error in handleItineraryDone:", error);
    alert("خطا در ذخیره اطلاعات");
  } finally {
    // همیشه مدال را ببند
    showItineraryDialog.value = false;
  }
}

// Edit special addon
function editSpecialAddon(index) {
  // Store the index for editing
  editingAddonIndex.value = index;
  showSpecialAddonsDialog.value = true;
}

// Remove special addon
function removeSpecialAddon(index) {
  if (confirm("آیا مطمئن هستید که می‌خواهید این افزونه را حذف کنید؟")) {
    specialAddons.value.splice(index, 1);
    // Update numbers
    specialAddons.value.forEach((addon, idx) => {
      addon.number = idx + 1;
    });
  }
}

async function handleSpecialAddonDone(addonsData, editingIndex = -1) {
  try {
    console.log("=== handleSpecialAddonDone called ===");
    console.log("addonsData:", addonsData);
    console.log("editingIndex:", editingIndex);
    console.log("Current specialAddons before update:", specialAddons.value);
    console.log("addonsData length:", addonsData.length);

    if (editingIndex >= 0) {
      // Editing existing addon - replace the specific addon
      console.log("Editing mode - replacing addon at index:", editingIndex);
      specialAddons.value[editingIndex] = addonsData[0];
      editingAddonIndex.value = -1; // Reset editing index
    } else {
      // Adding new addons - only add truly new addons
      console.log("Adding mode - checking for new addons");

      // If we have existing addons, we need to filter out the ones that already exist
      if (specialAddons.value.length > 0) {
        // Find only the new addons that don't exist in the current list
        const newAddons = addonsData.filter((newAddon) => {
          // Check if this addon already exists by comparing titles
          return !specialAddons.value.some(
            (existingAddon) =>
              existingAddon.title === newAddon.title &&
              existingAddon.description === newAddon.description &&
              existingAddon.price === newAddon.price
          );
        });

        console.log("New addons found:", newAddons);

        // Only add the new addons
        if (newAddons.length > 0) {
          specialAddons.value = [...specialAddons.value, ...newAddons];
        }
      } else {
        // First time adding addons - add all
        console.log("First time adding addons");
        specialAddons.value = addonsData;
      }
    }

    // Update the numbering for all addons
    specialAddons.value.forEach((addon, index) => {
      addon.number = index + 1;
    });

    console.log("Special addons after update:", specialAddons.value);
    console.log("Total addons count:", specialAddons.value.length);
  } catch (error) {
    console.error("Error in handleSpecialAddonDone:", error);
    alert("خطا در ذخیره اطلاعات");
  } finally {
    // همیشه مدال را ببند
    showSpecialAddonsDialog.value = false;
  }
}

async function handlePackageDone(packagesData, editingIndex = -1) {
  try {
    console.log("=== handlePackageDone called ===");
    console.log("packagesData:", packagesData);
    console.log("editingIndex:", editingIndex);
    console.log("Current packages before update:", packages.value);
    console.log("packagesData length:", packagesData.length);

    if (editingIndex >= 0) {
      // Editing existing package - replace the specific package
      console.log("Editing mode - replacing package at index:", editingIndex);
      packages.value[editingIndex] = packagesData[0];
      editingPackageIndex.value = -1; // Reset editing index
    } else {
      // Adding new packages - only add truly new packages
      console.log("Adding mode - checking for new packages");

      // If we have existing packages, we need to filter out the ones that already exist
      if (packages.value.length > 0) {
        // Find only the new packages that don't exist in the current list
        const newPackages = packagesData.filter((newPackage) => {
          // Check if this package already exists by comparing titles
          return !packages.value.some(
            (existingPackage) =>
              existingPackage.title === newPackage.title &&
              existingPackage.description === newPackage.description &&
              existingPackage.price === newPackage.price
          );
        });

        console.log("New packages found:", newPackages);

        // Only add the new packages
        if (newPackages.length > 0) {
          packages.value = [...packages.value, ...newPackages];
        }
      } else {
        // First time adding packages - add all
        console.log("First time adding packages");
        packages.value = packagesData;
      }
    }

    // Update the numbering for all packages
    packages.value.forEach((pkg, index) => {
      pkg.number = index + 1;
    });

    // Clear validation error when packages are added
    clearFieldError("packages");

    console.log("Packages after update:", packages.value);
    console.log("Total packages count:", packages.value.length);
  } catch (error) {
    console.error("Error in handlePackageDone:", error);
    alert("خطا در ذخیره اطلاعات");
  } finally {
    // همیشه مدال را ببند
    showPackageDialog.value = false;
  }
}

const goToNextStep = async () => {
  if (currentStep.value === 0) {
    // Validate step 1
    if (validateStep1()) {
      await updateListing();
      currentStep.value++;
    } else {
      // Scroll to first error
      const firstErrorField = document.querySelector(".field-error");
      if (firstErrorField) {
        firstErrorField.scrollIntoView({ behavior: "smooth", block: "center" });
      }
    }
  } else {
    await updateListing();
    currentStep.value++;
  }
};

const goToPrevStep = () => {
  currentStep.value--;
};

onMounted(async () => {
  if (!listingId.value) {
    await createListing();
  }
});

const onSubmit = async () => {
  loading.value = true;

  try {
    await updateListing();

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
  console.log("openItineraryModal called");
  console.log("Current showItineraryDialog value:", showItineraryDialog.value);
  editingItineraryIndex.value = -1; // Reset editing index for new itineraries
  showItineraryDialog.value = true;
  console.log("New showItineraryDialog value:", showItineraryDialog.value);
};

const createNewAdventure = () => {
  // Reset form data
  formData.value = {
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
    packages: [],
    activitiesIncluded: "",
    groupLanguage: "",
    mapsAndRoutes: [""],
    locations: "",
    listingMedia: [""],
    promotionalVideo: [""],
    whatsIncluded: "",
    whatsNotIncluded: "",
    additionalNotes: "",
    providersFAQ: "",
    itineraryAccommodation: "",
    personalPolicies: "",
    personalPoliciesText: "",
    specialAddons: "",
    termsAccepted: false,
  };

  // Reset other data
  itineraries.value = [];
  specialAddons.value = [];
  currentStep.value = 0;
  showConfirmation.value = false;

  // Create new listing
  createListing();
};

const openPackageModal = () => {
  editingPackageIndex.value = -1; // Reset editing index for new packages
  showPackageDialog.value = true;
};

// Drag and drop functionality for reordering addons
const draggedIndex = ref(-1);

const dragStart = (index, event) => {
  draggedIndex.value = index;
  event.dataTransfer.effectAllowed = "move";
  event.dataTransfer.setData("text/html", event.target.outerHTML);

  // Add visual feedback
  event.target.style.opacity = "0.5";
};

const drop = (index, event) => {
  event.preventDefault();

  if (draggedIndex.value === -1 || draggedIndex.value === index) {
    return;
  }

  // Reorder the addons array
  const addons = [...specialAddons.value];
  const draggedAddon = addons[draggedIndex.value];

  // Remove the dragged item from its original position
  addons.splice(draggedIndex.value, 1);

  // Insert it at the new position
  addons.splice(index, 0, draggedAddon);

  // Update the specialAddons array
  specialAddons.value = addons;

  // Update the numbering
  specialAddons.value.forEach((addon, idx) => {
    addon.number = idx + 1;
  });

  // Reset dragged index
  draggedIndex.value = -1;

  // Remove visual feedback
  event.target.style.opacity = "1";
};

// Drag and drop functionality for reordering itineraries
const draggedItineraryIndex = ref(-1);

const dragStartItinerary = (index, event) => {
  draggedItineraryIndex.value = index;
  event.dataTransfer.effectAllowed = "move";
  event.dataTransfer.setData("text/html", event.target.outerHTML);

  // Add visual feedback
  event.target.style.opacity = "0.5";
};

const dropItinerary = (index, event) => {
  event.preventDefault();

  if (
    draggedItineraryIndex.value === -1 ||
    draggedItineraryIndex.value === index
  ) {
    return;
  }

  // Reorder the itineraries array
  const itineraryList = [...itineraries.value];
  const draggedItinerary = itineraryList[draggedItineraryIndex.value];

  // Remove the dragged item from its original position
  itineraryList.splice(draggedItineraryIndex.value, 1);

  // Insert it at the new position
  itineraryList.splice(index, 0, draggedItinerary);

  // Update the itineraries array
  itineraries.value = itineraryList;

  // Update the numbering
  itineraries.value.forEach((itinerary, idx) => {
    itinerary.number = idx + 1;
  });

  // Reset dragged index
  draggedItineraryIndex.value = -1;

  // Remove visual feedback
  event.target.style.opacity = "1";
};

// Drag and drop functionality for reordering packages
const draggedPackageIndex = ref(-1);

const dragStartPackage = (index, event) => {
  draggedPackageIndex.value = index;
  event.dataTransfer.effectAllowed = "move";
  event.dataTransfer.setData("text/html", event.target.outerHTML);

  // Add visual feedback
  event.target.style.opacity = "0.5";
};

const dropPackage = (index, event) => {
  event.preventDefault();

  if (draggedPackageIndex.value === -1 || draggedPackageIndex.value === index) {
    return;
  }

  // Reorder the packages array
  const packageList = [...packages.value];
  const draggedPackage = packageList[draggedPackageIndex.value];

  // Remove the dragged item from its original position
  packageList.splice(draggedPackageIndex.value, 1);

  // Insert it at the new position
  packageList.splice(index, 0, draggedPackage);

  // Update the packages array
  packages.value = packageList;

  // Update the numbering
  packages.value.forEach((pkg, idx) => {
    pkg.number = idx + 1;
  });

  // Reset dragged index
  draggedPackageIndex.value = -1;

  // Remove visual feedback
  event.target.style.opacity = "1";
};

// Edit itinerary item
function editItineraryItem(index) {
  // Store the index for editing
  editingItineraryIndex.value = index;
  showItineraryDialog.value = true;
}

// Remove itinerary
function removeItinerary(index) {
  if (confirm("آیا مطمئن هستید که می‌خواهید این روز را حذف کنید؟")) {
    itineraries.value.splice(index, 1);
    // Update numbers
    itineraries.value.forEach((itinerary, idx) => {
      itinerary.number = idx + 1;
    });
  }
}

// Edit package
function editPackage(index) {
  // Store the index for editing
  editingPackageIndex.value = index;
  showPackageDialog.value = true;
}

// Remove package
function removePackage(index) {
  if (confirm("Are you sure you want to remove this package?")) {
    packages.value.splice(index, 1);
    // Update numbers
    packages.value.forEach((pkg, idx) => {
      pkg.number = idx + 1;
    });
  }
}
</script>

<template>
  <!-- Confirmation Page -->
  <div v-if="showConfirmation" class="confirmation-page">
    <div class="confirmation-container">
      <div class="confirmation-card">
        <!-- Icon -->
        <div class="confirmation-icon">
          <div class="icon-container">
            <img 
              src="/images/4svg/listingend.png" 
              alt="Listing End Icon"
              style="width: 120px; height: 120px; object-fit: contain;"
            />
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
        <!-- Auto-save indicator - Above form -->
        <div class="auto-save-indicator" v-if="isSaving || showSavedIndicator">
          <div class="save-status" :class="{ 'saving': isSaving, 'saved': showSavedIndicator && !isSaving }">
            <VIcon 
              :icon="isSaving ? 'tabler-loader-2' : 'tabler-check'" 
              :class="{ 'spinning': isSaving }"
            />
            <span v-if="isSaving">Saving...</span>
            <span v-else-if="showSavedIndicator">Changes saved</span>
          </div>
        </div>
        
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
                        :error-messages="formValidationErrors['finishingDate']"
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
                        Departure Capacity <span class="required-star">*</span>
                      </label>
                      <div class="d-flex gap-3">
                        <VTextField
                          v-model="formData.minCapacity"
                          placeholder="Min Num"
                          type="number"
                          class="capacity-input"
                          style="max-width: 120px"
                          :error="hasFieldError('minCapacity')"
                          :error-messages="formValidationErrors['minCapacity']"
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
                          :error-messages="formValidationErrors['maxCapacity']"
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
                        class="empty-state-container"
                        :class="{ 'field-error': hasFieldError('packages') }"
                      >
                        <VBtn
                          color="primary"
                          variant="elevated"
                          class="add-package-btn"
                          style="
                            background-color: #ec8d22 !important;
                            color: #fff !important;
                            border-radius: 8px;
                            font-weight: 500;
                            font-size: 14px;
                            padding: 12px 20px;
                            min-height: 40px;
                            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
                            text-transform: none;
                            transition: all 0.2s ease;
                          "
                          @click="openPackageModal"
                        >
                          Add Package
                        </VBtn>
                        <p
                          v-if="hasFieldError('packages')"
                          class="text-caption text-error mt-2 mb-0"
                          style="font-size: 11px"
                        >
                          {{ formValidationErrors["packages"] }}
                        </p>
                      </div>

                      <!-- Display Packages List -->
                      <div
                        v-if="packages.length > 0"
                        class="packages-list-container"
                      >
                        <!-- Show header with button when items exist -->
                        <div class="packages-header">
                          <h3 class="packages-title">Listing Packages</h3>
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
                            @click="openPackageModal"
                          >
                            Add More
                          </VBtn>
                        </div>

                        <div class="packages-list">
                          <div
                            v-for="(pkg, index) in packages"
                            :key="index"
                            class="package-list-item"
                            draggable="true"
                            @dragstart="dragStartPackage(index, $event)"
                            @dragover.prevent
                            @drop="dropPackage(index, $event)"
                            @dragenter.prevent
                          >
                            <div class="package-content">
                              <div class="package-left">
                                <div
                                  class="package-badge"
                                  style="position: relative"
                                >
                                  <span class="badge-number">{{
                                    (pkg.number || index + 1)
                                      .toString()
                                      .padStart(2, "0")
                                  }}</span>
                                  <!-- Package icon under number badge -->
                                  <div
                                    v-if="index < packages.length - 1"
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
                                      icon="tabler-package"
                                      size="16"
                                      style="color: #ec8d22"
                                    />
                                  </div>
                                  <!-- Vertical dotted line under icon -->
                                  <div
                                    v-if="index < packages.length - 1"
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
                                <div class="package-info">
                                  <div class="package-title-row">
                                    <span class="package-title">{{
                                      pkg.title ||
                                      `Package ${pkg.number || index + 1} Title`
                                    }}</span>
                                    <VIcon
                                      icon="tabler-chevron-down"
                                      size="16"
                                      class="chevron-icon"
                                    />
                                  </div>
                                  <div class="package-description">
                                    <span class="description-text">{{
                                      pkg.description ||
                                      "Your package description would take place here"
                                    }}</span>
                                  </div>
                                  <div class="package-price">
                                    € {{ (pkg.price || 0).toFixed(2) }}
                                  </div>
                                </div>
                              </div>
                              <div class="package-actions">
                                <VBtn
                                  icon
                                  size="small"
                                  variant="text"
                                  @click="editPackage(index)"
                                  class="action-btn"
                                >
                                  <VIcon icon="tabler-edit" size="18" />
                                </VBtn>
                                <VBtn
                                  icon
                                  size="small"
                                  variant="text"
                                  color="error"
                                  @click="removePackage(index)"
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
                                  @dragstart="dragStartPackage(index, $event)"
                                >
                                  <VIcon icon="tabler-arrows-move" size="18" />
                                </VBtn>
                              </div>
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
              <!-- Step 2: Date & Time -->
              <VWindowItem>
                <VRow>
                  <!-- Top Row -->
                  <VCol cols="12" md="6">
                    <!-- Activities Included -->
                    <div class="mb-4">
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
                        placeholder="Select the activities that are included in your listing"
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
                      />
                    </div>
                  </VCol>
                  <VCol cols="12" md="6">
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
                  </VCol>
                  <VCol cols="12" md="6">
                    <!-- Locations -->
                    <div class="mb-4">
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
                      />
                    </div>
                  </VCol>

                  <!-- Third Row -->
                  <VCol cols="12" md="6">
                    <!-- Listing Media -->
                    <div class="mb-4">
                      <label
                        class="v-label text-body-2 mb-3 d-block"
                        style="
                          font-size: 16px !important;
                          font-weight: 400 !important;
                        "
                      >
                        Listing Media <span class="required-star">*</span>
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

                  <!-- Fourth Row - Large Text Areas -->
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
                        placeholder="Items/services included in the listing"
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
                        placeholder="Items/services not included in the listing"
                        rows="4"
                        class="rich-text-area"
                      />
                    </div>
                  </VCol>

                  <!-- Fifth Row - Rich Text Areas -->
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
                        placeholder="Tell us any further information we should have about your listing"
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
                          @dragstart="dragStart(index, $event)"
                          @dragover.prevent
                          @drop="drop(index, $event)"
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
                                @dragstart="dragStart(index, $event)"
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
                @click="
                  currentStep === 0
                    ? router.push({ name: 'listing' })
                    : goToPrevStep()
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
  <PackageDialog
    v-model="showPackageDialog"
    :initial-packages="packages"
    :editing-index="editingPackageIndex"
    @close="showPackageDialog = false"
    @done="handlePackageDone"
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
  font-weight: 300;
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

/* Date picker wrapper with star positioning */
.date-picker-wrapper {
  position: relative;
}

.date-picker-wrapper .required-star {
  position: absolute;
  top: 0;
  right: -20px;
  z-index: 10;
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

/* Date picker styles */
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

/* Included Textarea styling */
.included-textarea-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  background: #fff;
  padding-left: 10px; /* Align with text content */
}

.included-textarea {
  flex: 1;
  padding-left: 30px; /* Adjust for icon */
  padding-right: 10px;
  font-size: 16px;
  line-height: 1.5;
  border: none;
  box-shadow: none;
  resize: none;
  min-height: 100px; /* Ensure minimum height */
}

.included-textarea:focus {
  outline: none;
}

/* Rich Text Editor Controls */
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

/* Add buttons styling */
.add-itinerary-btn,
.add-addons-btn {
  min-height: 40px;
  padding: 0 20px;
  font-size: 14px;
  text-transform: none;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.add-itinerary-btn:hover,
.add-addons-btn:hover {
  background-color: #d67d1a !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Section styling for empty state */
.section-title {
  font-family: "Karla", sans-serif;
  font-size: 18px;
  font-weight: 600;
  color: #333333;
  margin: 0 0 8px 0;
  line-height: 1.2;
}

.section-description {
  font-family: "Karla", sans-serif;
  font-size: 14px;
  color: #666666;
  margin: 0 0 16px 0;
  line-height: 1.4;
}

.empty-state-container {
  margin-bottom: 24px;
}

.add-item-btn {
  background-color: #ec8d22 !important;
  color: #fff !important;
  border-radius: 8px;
  font-weight: 500;
  font-size: 14px;
  padding: 12px 20px;
  min-height: 40px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  text-transform: none;
  transition: all 0.2s ease;
}

.add-item-btn:hover {
  background-color: #d67d1a !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.add-package-btn:hover {
  background-color: #d67d1a !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Radio options styling */
.radio-option {
  margin-bottom: 16px;
}

.radio-option:last-child {
  margin-bottom: 0;
}

.radio-option .v-radio {
  margin-bottom: 8px;
}

.radio-option .v-radio:last-child {
  margin-bottom: 0;
}

/* Help section styling */
.help-section {
  padding: 32px 0;
  margin-top: 48px;
}

.help-title {
  font-family: "Anton", sans-serif;
  font-size: 28px;
  font-weight: 300;
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

/* Itinerary Styling */
.itinerary-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.itinerary-title {
  font-family: "Anton", sans-serif;
  font-size: 28px;
  font-weight: 300;
  color: #333333;
  margin: 0;
}

.itinerary-list-container {
  display: flex;
  flex-direction: column;
  gap: 16px;
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  border: none;
}

.itinerary-list-item {
  background: transparent;
  border-radius: 0;
  transition: all 0.2s ease;
  border: none;
  border-bottom: 1px solid #e0e0e0;
  box-shadow: none;
  padding: 16px 0;
  margin-bottom: 16px;
  position: relative;
  cursor: grab;
}

.itinerary-list-item:last-child {
  margin-bottom: 0;
  border-bottom: none;
}

.itinerary-list-item:hover {
  background: transparent;
  transform: none;
  box-shadow: none;
}

.itinerary-list-item:active {
  cursor: grabbing;
}

.itinerary-list-item.dragging {
  opacity: 0.5;
  transform: rotate(2deg);
}

.itinerary-actions {
  display: flex;
  align-items: center;
  gap: 4px;
  margin-left: 16px;
}

.itinerary-actions .v-btn {
  color: #333333 !important;
}

.itinerary-actions .v-btn:hover {
  color: #666666 !important;
}

.itinerary-actions .v-btn--color-error {
  color: #d32f2f !important;
}

.itinerary-actions .v-btn--color-error:hover {
  color: #b71c1c !important;
}

/* Special Addons Styling */
.special-addons-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.special-addons-title {
  font-family: "Anton", sans-serif;
  font-size: 28px;
  font-weight: 300;
  color: #333333;
  margin: 0;
}

.special-addons-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  border: none;
}

.special-addon-item {
  background: transparent;
  border-radius: 0;
  transition: all 0.2s ease;
  cursor: grab;
  border: none;
  border-bottom: 1px solid #e0e0e0;
  box-shadow: none;
  padding: 16px 0;
  margin-bottom: 16px;
  position: relative;
}

.special-addon-item:last-child {
  margin-bottom: 0;
  border-bottom: none;
}

.special-addon-item:hover {
  background: transparent;
  transform: none;
  box-shadow: none;
}

.special-addon-item:active {
  cursor: grabbing;
}

.special-addon-item.dragging {
  opacity: 0.5;
  transform: rotate(2deg);
}

.addon-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 16px;
}

.addon-left {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  flex: 1;
}

.addon-badge {
  width: 32px;
  height: 32px;
  background: #ec8d22;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.badge-number {
  color: white;
  font-weight: 700;
  font-size: 12px;
  font-family: "Anton", sans-serif;
}

.addon-info {
  flex: 1;
  min-width: 0;
}

.addon-title-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}

.addon-title {
  font-size: 16px;
  font-weight: 600;
  color: #333333;
  font-family: "Karla", sans-serif;
}

.chevron-icon {
  color: #333333;
}

.addon-description {
  display: flex;
  align-items: flex-start;
  gap: 6px;
  margin-bottom: 12px;
  position: relative;
}

.star-icon {
  color: #ec8d22;
  margin-top: 2px;
  flex-shrink: 0;
}

.description-text {
  font-size: 14px;
  color: #666666;
  line-height: 1.4;
  font-family: "Karla", sans-serif;
}

.addon-price {
  font-size: 14px;
  font-weight: 600;
  color: #333333;
  text-align: right;
  font-family: "Karla", sans-serif;
}

.addon-actions {
  display: flex;
  align-items: center;
  gap: 4px;
  margin-left: 16px;
}

.addon-actions .v-btn {
  color: #333333 !important;
}

.addon-actions .v-btn:hover {
  color: #666666 !important;
}

.addon-actions .v-btn--color-error {
  color: #d32f2f !important;
}

.addon-actions .v-btn--color-error:hover {
  color: #b71c1c !important;
}

.action-btn {
  color: #333333 !important;
  transition: color 0.2s ease;
}

.action-btn:hover {
  color: #666666 !important;
}

.action-btn.v-btn--color-error {
  color: #d32f2f !important;
}

.action-btn.v-btn--color-error:hover {
  color: #b71c1c !important;
}

.drag-handle {
  cursor: grab !important;
}

.drag-handle:active {
  cursor: grabbing !important;
}

.drag-handle:hover {
  color: #ec8d22 !important;
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

.checkmark {
  color: #ec8d22;
  font-size: 16px;
  font-weight: bold;
}

.cross {
  color: #ec8d22;
  font-size: 16px;
  font-weight: bold;
}

.confirmation-title {
  font-family: "Anton", sans-serif;
  font-size: 28px;
  font-weight: 300;
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

/* Packages List Styling */
.packages-list-container {
  margin-top: 16px;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
}

.packages-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.packages-title {
  font-family: "Anton", sans-serif;
  font-size: 28px;
  font-weight: 300;
  color: #333333;
  margin: 0;
}

.packages-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  border: none;
}

.package-list-item {
  background: transparent;
  border-radius: 0;
  transition: all 0.2s ease;
  cursor: grab;
  border: none;
  border-bottom: 1px solid #e0e0e0;
  box-shadow: none;
  padding: 16px 0;
  margin-bottom: 16px;
  position: relative;
}

.package-list-item:last-child {
  margin-bottom: 0;
  border-bottom: none;
}

.package-list-item:hover {
  background: transparent;
  transform: none;
  box-shadow: none;
}

.package-list-item:active {
  cursor: grabbing;
}

.package-list-item.dragging {
  opacity: 0.5;
  transform: rotate(2deg);
}

.package-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 16px;
}

.package-left {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  flex: 1;
}

.package-badge {
  width: 32px;
  height: 32px;
  background: #ec8d22;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.badge-number {
  color: white;
  font-weight: 700;
  font-size: 12px;
  font-family: "Anton", sans-serif;
}

.package-info {
  flex: 1;
  min-width: 0;
}

.package-title-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}

.package-title {
  font-size: 16px;
  font-weight: 600;
  color: #333333;
  font-family: "Karla", sans-serif;
}

.chevron-icon {
  color: #333333;
}

.package-description {
  display: flex;
  align-items: flex-start;
  gap: 6px;
  margin-bottom: 12px;
  position: relative;
}

.description-text {
  font-size: 14px;
  color: #666666;
  line-height: 1.4;
  font-family: "Karla", sans-serif;
}

.package-price {
  font-size: 14px;
  font-weight: 600;
  color: #333333;
  text-align: right;
  font-family: "Karla", sans-serif;
}

.package-actions {
  display: flex;
  align-items: center;
  gap: 4px;
  margin-left: 16px;
}

.package-actions .v-btn {
  color: #333333 !important;
}

.package-actions .v-btn:hover {
  color: #666666 !important;
}

.package-actions .v-btn--color-error {
  color: #d32f2f !important;
}

.package-actions .v-btn--color-error:hover {
  color: #b71c1c !important;
}

.action-btn {
  color: #333333 !important;
  transition: color 0.2s ease;
}

.action-btn:hover {
  color: #666666 !important;
}

.action-btn.v-btn--color-error {
  color: #d32f2f !important;
}

.action-btn.v-btn--color-error:hover {
  color: #b71c1c !important;
}

.drag-handle {
  cursor: grab !important;
}

.drag-handle:active {
  cursor: grabbing !important;
}

.drag-handle:hover {
  color: #ec8d22 !important;
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
