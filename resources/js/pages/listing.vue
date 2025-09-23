<script setup>
import CustomRadiosWithIcon from "@/@core/components/app-form-elements/CustomRadiosWithIcon.vue";
import { companyUserService, individualUserService } from "@/services/api";
import DemoFormWizardNumberedModernBasicMultiDate from "@/views/demos/forms/form-wizard/form-wizard-numbered/DemoFormWizardNumberedModernBasicMultiDate.vue";
import DemoFormWizardNumberedModernBasicOpenDate from "@/views/demos/forms/form-wizard/form-wizard-numbered/DemoFormWizardNumberedModernBasicOpenDate.vue";
import DemoFormWizardNumberedModernBasicSingleDate from "@/views/demos/forms/form-wizard/form-wizard-numbered/DemoFormWizardNumberedModernBasicSingleDate.vue";
import { onMounted, ref } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();

definePage({
  meta: {
    layout: "default", // Use default layout with navbar
    public: true, // Make this page public for testing
  },
});

// Sidebar management is now handled globally by route watcher

// User data for dynamic provider name
const userData = ref(null);
const loggedInUser = ref(null);
const loading = ref(false);
const error = ref(null);

// Get logged-in user data from cookies
const getLoggedInUser = () => {
  const userDataCookie = useCookie("userData");
  if (userDataCookie.value) {
    loggedInUser.value = userDataCookie.value;
    console.log("Logged in user data:", userDataCookie.value);
    // If the cookie contains the user data with name, use it directly
    if (userDataCookie.value.full_name || userDataCookie.value.company_name) {
      userData.value = userDataCookie.value;
      console.log("Using user data from cookie:", userData.value);
    }
  } else {
    console.log("No user data cookie found");
  }
};

// Fetch user data from API if needed
const fetchUserData = async () => {
  if (!loggedInUser.value) {
    console.log("No logged in user, skipping fetch");
    return;
  }

  // If we already have the name from cookies, no need to fetch
  if (userData.value?.full_name || userData.value?.company_name) {
    console.log("User data already available, skipping fetch");
    return;
  }

  try {
    loading.value = true;
    console.log("Fetching user data from API...");

    // Try to get user ID from logged-in user data
    const userId = loggedInUser.value.id || loggedInUser.value.user_id;
    console.log("User ID:", userId);

    if (!userId) {
      console.warn("User ID not found in logged-in user data");
      return;
    }

    // Try to determine user type from logged-in user data
    let userType = loggedInUser.value.user_type;
    console.log("User type from cookie:", userType);

    // If user_type is not available, try to infer from the data structure
    if (!userType) {
      if (loggedInUser.value.company_name) {
        userType = "company";
      } else if (loggedInUser.value.full_name) {
        userType = "individual";
      }
      console.log("Inferred user type:", userType);
    }

    // If we still don't have user type, try both endpoints
    if (!userType) {
      console.log("Trying both endpoints...");
      try {
        const individualResponse = await individualUserService.getById(userId);
        userData.value = individualResponse.data;
        console.log("Found individual user data:", userData.value);
        return;
      } catch (e) {
        console.log("Individual user fetch failed:", e);
        try {
          const companyResponse = await companyUserService.getById(userId);
          userData.value = companyResponse.data;
          console.log("Found company user data:", userData.value);
          return;
        } catch (e2) {
          console.warn("Could not fetch user data from either endpoint");
        }
      }
    } else {
      // Fetch user data based on type
      if (userType === "individual") {
        const response = await individualUserService.getById(userId);
        userData.value = response.data;
        console.log("Fetched individual user data:", userData.value);
      } else if (userType === "company") {
        const response = await companyUserService.getById(userId);
        userData.value = response.data;
        console.log("Fetched company user data:", userData.value);
      }
    }
  } catch (err) {
    console.error("Error fetching user data:", err);
    error.value = err.message || "Failed to load user data";
  } finally {
    loading.value = false;
  }
};

// Get user display name
const getUserDisplayName = () => {
  // First try to get from userData (from API)
  if (userData.value) {
    return (
      userData.value.full_name ||
      userData.value.company_name ||
      userData.value.name ||
      "User"
    );
  }

  // Fallback to loggedInUser data
  if (loggedInUser.value) {
    return (
      loggedInUser.value.full_name ||
      loggedInUser.value.company_name ||
      loggedInUser.value.name ||
      loggedInUser.value.username ||
      "User"
    );
  }

  return "User";
};

// Fetch user data on mount
onMounted(async () => {
  console.log("Listing page mounted");
  getLoggedInUser();
  await fetchUserData();
  console.log("Listing page setup complete");
});

const radioContent = [
  {
    title: "Single Date",
    desc: "The Listing has a Specific Start and End Date",
    value: "single-date",
    img: {
      active: "/images/4svg/single.png",
      inactive: "/images/4svg/single.png",
    },
  },
  {
    title: "Multi Date",
    desc: "The Listing can be Booked for any Date within a Certain Period",
    value: "multi-date",
    img: {
      active: "/images/4svg/multi.png",
      inactive: "/images/4svg/multi.png",
    },
  },
  {
    title: "Open Date",
    desc: "The Listing can be Booked for any Date within a Certain Period",
    value: "open-date",
    img: {
      active: "/images/4svg/open.png",
      inactive: "/images/4svg/open.png",
    },
  },
  {
    title: "Other",
    desc: "Listing that doesn't Fit the Above Categories",
    value: "other",
    img: {
      active: "/images/4svg/wired-outline-28-calendar-hover-pinch.png",
      inactive: "/images/4svg/wired-outline-28-calendar-hover-pinch.png",
    },
  },
];

const selectedRadio = ref("single-date");
const showWizard = ref(false);
const showContactSupport = ref(false);
const selectedWizardType = ref("");

function handleNext() {
  if (selectedRadio.value === "single-date") {
    selectedWizardType.value = "single-date";
    showWizard.value = true;
  } else if (selectedRadio.value === "multi-date") {
    selectedWizardType.value = "multi-date";
    showWizard.value = true;
  } else if (selectedRadio.value === "open-date") {
    selectedWizardType.value = "open-date";
    showWizard.value = true;
  } else if (selectedRadio.value === "other") {
    showContactSupport.value = true;
  }
}

function goBack() {
  showContactSupport.value = false;
}

function contactSupport() {
  // Here you can implement the actual contact support functionality
  // For now, we'll just show an alert
  alert("Contacting Explorer Elite's Support Team...");
  // You can redirect to a contact form or open a chat window here
}
</script>

<template>
  <template v-if="!showWizard && !showContactSupport">
    <div
      class="d-flex flex-column align-center justify-center listing-content"
      style="min-height: 80vh"
    >
      <!-- Loading State -->
      <div v-if="loading" class="text-center">
        <VProgressCircular indeterminate color="primary" size="64" />
        <p class="mt-4">Loading your information...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center">
        <VIcon size="64" color="error">tabler-alert-circle</VIcon>
        <p class="mt-4 text-error">{{ error }}</p>
        <VBtn @click="fetchUserData" class="mt-4">Retry</VBtn>
      </div>

      <!-- Main Content -->
      <div v-else>
        <div class="listing-header text-center mb-4">
          <h2 class="listing-title">
            Hey <span class="provider-name">{{ getUserDisplayName() }}</span
            >, Let's Add a New Explorer Elite Listing
          </h2>
          <div class="listing-subtitle mt-1 mb-6">
            Please Select Your Listing Type Here
          </div>
        </div>
        <div class="d-flex justify-center align-center w-100">
          <div style="max-width: 900px; width: 100%" class="d-flex">
            <CustomRadiosWithIcon
              v-model:selected-radio="selectedRadio"
              :radio-content="radioContent"
              :grid-column="{ md: 6, cols: 12 }"
              class="listing-radios"
            />
          </div>
        </div>
        <div class="d-flex justify-center mt-10 mb-8">
          <VBtn
            class="listing-next-btn"
            color="dark"
            size="large"
            @click="handleNext"
          >
            Next
          </VBtn>
        </div>
      </div>
    </div>
  </template>
  <template v-else-if="showWizard">
    <div class="wizard-center-container">
      <DemoFormWizardNumberedModernBasicSingleDate
        v-if="selectedWizardType === 'single-date'"
      />
      <DemoFormWizardNumberedModernBasicMultiDate
        v-else-if="selectedWizardType === 'multi-date'"
      />
      <DemoFormWizardNumberedModernBasicOpenDate
        v-else-if="selectedWizardType === 'open-date'"
      />
    </div>
  </template>

  <!-- Contact Support Page -->
  <template v-else-if="showContactSupport">
    <div class="contact-support-container">
      <div class="contact-support-card">
        <!-- Support Icon -->
        <div class="support-icon-container">
          <img
            src="/images/4svg/suport.png"
            alt="Support"
            class="support-icon-img"
          />
        </div>

        <!-- Main Heading -->
        <h1 class="contact-support-title">Contact us now!</h1>

        <!-- Description -->
        <p class="contact-support-description">
          If you think you need other options for your adventure type, contact
          our support team right now and let us help.
        </p>

        <!-- Action Buttons -->
        <div class="contact-support-buttons">
          <VBtn
            color="dark"
            variant="elevated"
            class="back-btn"
            @click="goBack"
          >
            Back
          </VBtn>
          <VBtn
            color="primary"
            variant="elevated"
            class="contact-btn"
            @click="contactSupport"
          >
            Contact Explorer Elite's Support
          </VBtn>
        </div>
      </div>
    </div>
  </template>
</template>

<style>
/* Removed sidebar hiding CSS - sidebar will be managed by layout configuration */

.listing-content {
  background: #fff;
  border-radius: 6px;
  box-shadow: 0 2px 16px 0 rgba(44, 44, 44, 0.08);
  padding: 3rem 2rem 2.5rem 2rem;
  max-width: 1050px;
  margin: 0 auto;
  min-height: 80vh;
}

.listing-header .listing-title {
  font-family: "Anton", sans-serif;
  font-size: 2rem;
  font-weight: 300;
  color: #222;
  margin-bottom: 16px;
}
.listing-header .provider-name {
  color: #ec8d22;
  font-family: inherit;
}
.listing-header .listing-subtitle {
  font-size: 1.1rem;
  color: #444;
  font-family: "Karla", sans-serif;
  margin-bottom: 32px;
}

.listing-next-btn {
  min-width: 140px;
  min-height: 40px;
  font-size: 1.1rem;
  border-radius: 8px;
  font-weight: 700;
  background: #111 !important;
  color: #fff !important;
  box-shadow: 0 2px 8px 0 rgba(44, 44, 44, 0.08);
  transition: background 0.2s;
}

.listing-next-btn:hover {
  background: #222 !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px 0 rgba(44, 44, 44, 0.12);
}

.listing-radios {
  width: 100%;
  margin: 32px 0;
}

.wizard-center-container {
  max-width: 100%;
  margin: 48px auto 0 auto;
  padding: 0 16px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

/* Responsive design */
@media (max-width: 768px) {
  .listing-content {
    padding: 2rem 1rem 1.5rem 1rem;
    margin: 0 8px;
  }

  .listing-header .listing-title {
    font-size: 1.5rem;
  }

  .listing-header .listing-subtitle {
    font-size: 1rem;
  }
}

/* Contact Support Page Styles */
.contact-support-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f5f5f5;
  padding: 20px;
}

.contact-support-card {
  background: #fff;
  border-radius: 12px;
  padding: 48px;
  text-align: center;
  max-width: 500px;
  width: 100%;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.support-icon-container {
  margin-bottom: 24px;
}

.support-icon-img {
  width: 64px;
  height: 64px;
  object-fit: contain;
}

.support-icon {
  color: #ec8d22;
}

.contact-support-title {
  font-family: "Anton", sans-serif;
  font-size: 2.5rem;
  font-weight: 300;
  color: #333;
  margin-bottom: 16px;
  line-height: 1.2;
}

.contact-support-description {
  font-family: "Karla", sans-serif;
  font-size: 1.1rem;
  color: #666;
  line-height: 1.6;
  margin-bottom: 32px;
  max-width: 400px;
  margin-left: auto;
  margin-right: auto;
}

/* Fix radio button sizing and clipping for listing page */
.listing-radios ::deep(.v-radio) {
  overflow: visible;
}

.listing-radios ::deep(.v-radio .v-selection-control) {
  min-height: 30px;
  padding: 6px 4px;
  overflow: visible;
}

.listing-radios ::deep(.v-radio .v-selection-control__wrapper) {
  width: 24px;
  height: 24px;
  padding: 2px;
  overflow: visible;
}

.listing-radios ::deep(.v-radio .v-selection-control__input) {
  width: 20px;
  height: 20px;
}

.listing-radios ::deep(.v-radio .v-selection-control__ripple) {
  inset: -8px;
}

.contact-support-buttons {
  display: flex;
  gap: 16px;
  justify-content: center;
  flex-wrap: wrap;
}

.back-btn {
  min-width: 120px;
  min-height: 44px;
  font-size: 1rem;
  border-radius: 8px;
  font-weight: 600;
  background: #111 !important;
  color: #fff !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  transition: all 0.2s ease;
}

.back-btn:hover {
  background: #222 !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.contact-btn {
  min-width: 200px;
  min-height: 44px;
  font-size: 1rem;
  border-radius: 8px;
  font-weight: 600;
  background: #ec8d22 !important;
  color: #fff !important;
  box-shadow: 0 2px 8px rgba(236, 141, 34, 0.3);
  transition: all 0.2s ease;
}

.contact-btn:hover {
  background: #d67d1a !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(236, 141, 34, 0.4);
}

/* Responsive design for contact support */
@media (max-width: 768px) {
  .contact-support-container {
    padding: 16px;
  }

  .contact-support-card {
    padding: 32px 24px;
  }

  .contact-support-title {
    font-size: 2rem;
  }

  .contact-support-description {
    font-size: 1rem;
  }

  .contact-support-buttons {
    flex-direction: column;
    align-items: center;
  }

  .back-btn,
  .contact-btn {
    width: 100%;
    max-width: 280px;
  }
}
</style>
