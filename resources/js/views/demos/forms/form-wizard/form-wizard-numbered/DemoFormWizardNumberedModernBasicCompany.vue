<script setup>
import { companyUserService } from "@/services/api";
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const numberedSteps = [
  {
    title: "Company Information",
  },
  {
    title: "Business Details",
  },
  {
    title: "Social Links",
  },
];

const currentStep = ref(0);
const loading = ref(false);
const formValidationErrors = ref({});
const showErrorDialog = ref(false);
const errorMessage = ref("");
const showSuccessDialog = ref(false);
const successMessage = ref("");

const formData = ref({
  // Step 1 fields - Company Information
  companyName: "",
  vatId: "",
  address1: "",
  city: "",
  state: "",
  contactPerson: "",
  countryOfRegistration: "",
  address2: "",
  postalCode: "",
  country: "",
  businessType: "",
  // Step 2 fields - Business Details (copied from individual wizard)
  passportImage: null,
  avatarImage: null,
  activitySpecialization: "",
  wantToBeListed: "",
  shortBio: "",
  certifications: null,
  // Step 3 fields
  twitter: "",
  facebook: "",
  linkedIn: "",
  instagram: "",
  // New Step 3 fields
  companyWebsite: "",
  socialProofLinks: [""],
  socialMediaLinks: [""],
  termsAccepted: false,
});

// Required fields for step 1 (Company Information)
const requiredFieldsStep1 = [
  "companyName",
  "address1",
  "countryOfRegistration",
  "country",
  "businessType",
];

// Required fields for step 2 (Business Details)
const requiredFieldsStep2 = [
  "passportImage",
  "activitySpecialization",
  "shortBio",
  "wantToBeListed",
];

// Validation function for step 1
const validateStep1 = () => {
  const errors = {};

  requiredFieldsStep1.forEach((field) => {
    if (!formData.value[field] || formData.value[field].trim() === "") {
      errors[field] = "This field is required";
    }
  });

  formValidationErrors.value = errors;
  return Object.keys(errors).length === 0;
};

// Validation function for step 2
const validateStep2 = () => {
  const errors = {};

  requiredFieldsStep2.forEach((field) => {
    if (field === "passportImage") {
      if (!formData.value[field]) {
        errors[field] = "This field is required";
      }
    } else if (field === "wantToBeListed") {
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

// Check if field is required for step 2
const isFieldRequiredStep2 = (fieldName) => {
  return requiredFieldsStep2.includes(fieldName);
};

// Handle next step with validation
const handleNextStep = () => {
  if (currentStep.value === 0) {
    // Validate step 1
    if (validateStep1()) {
      currentStep.value++;
    } else {
      // Show error popup with missing fields
      const missingFields = Object.keys(formValidationErrors.value);
      const fieldNames = missingFields.map((field) => {
        const fieldLabels = {
          companyName: "Company Name",
          address1: "Address Line 1",
          countryOfRegistration: "Country of Registration",
          country: "Country",
          businessType: "Business Type",
        };
        return fieldLabels[field] || field;
      });
      showValidationError(
        `Please fill in the following required fields: ${fieldNames.join(", ")}`
      );

      // Scroll to first error
      setTimeout(() => {
        const firstErrorField = document.querySelector(".field-error");
        if (firstErrorField) {
          firstErrorField.scrollIntoView({
            behavior: "smooth",
            block: "center",
          });
        }
      }, 100);
    }
  } else if (currentStep.value === 1) {
    // Validate step 2
    if (validateStep2()) {
      currentStep.value++;
    } else {
      // Show error popup with missing fields
      const missingFields = Object.keys(formValidationErrors.value);
      const fieldNames = missingFields.map((field) => {
        const fieldLabels = {
          passportImage: "Company Logo",
          activitySpecialization: "Activity Specialization",
          shortBio: "Company Bio",
          wantToBeListed: "Listing Preference",
        };
        return fieldLabels[field] || field;
      });
      showValidationError(
        `Please fill in the following required fields: ${fieldNames.join(", ")}`
      );

      // Scroll to first error
      setTimeout(() => {
        const firstErrorField = document.querySelector(".field-error");
        if (firstErrorField) {
          firstErrorField.scrollIntoView({
            behavior: "smooth",
            block: "center",
          });
        }
      }, 100);
    }
  } else {
    currentStep.value++;
  }
};

// Clear validation errors when field changes
const clearFieldError = (fieldName) => {
  if (formValidationErrors.value[fieldName]) {
    delete formValidationErrors.value[fieldName];
  }
};

const onSubmit = async () => {
  loading.value = true;

  try {
    // Validate required fields
    if (!formData.value.termsAccepted) {
      showValidationError(
        "Please accept the terms and conditions to continue."
      );
      return;
    }

    // Safely extract arrays from reactive proxies
    const socialMediaLinksArray = Array.isArray(formData.value.socialMediaLinks)
      ? [...formData.value.socialMediaLinks]
      : [];
    const socialProofLinksArray = Array.isArray(formData.value.socialProofLinks)
      ? [...formData.value.socialProofLinks]
      : [];

    // Prepare form data for submission
    const submitData = {
      ...formData.value,
      // Convert arrays to JSON strings for backend
      socialMediaLinks: JSON.stringify(
        socialMediaLinksArray.filter((link) => link && link.trim() !== "")
      ),
      socialProofLinks: JSON.stringify(
        socialProofLinksArray.filter((link) => link && link.trim() !== "")
      ),
    };

    const response = await companyUserService.register(submitData);
    console.log("Registration successful:", response);

    // Show success message
    showSuccessMessage("Registration completed successfully!");

    // Redirect back to timeline to update status
    const userDataCookie = useCookie("userData");
    if (userDataCookie.value?.id) {
      router.push(`/registration/timeline/company/${userDataCookie.value.id}`);
    } else {
      router.push("/");
    }
  } catch (error) {
    console.error("Registration failed:", error);
    showValidationError(
      "Registration failed: " + (error.message || "Unknown error")
    );
  } finally {
    loading.value = false;
  }
};

// Handle image uploads
const handlePassportImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    formData.value.passportImage = file;
    // Clear validation error when image is uploaded
    clearFieldError("passportImage");
    // Create preview URL
    const reader = new FileReader();
    reader.onload = (e) => {
      formData.value.passportImagePreview = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const handleAvatarImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    formData.value.avatarImage = file;
    // Create preview URL
    const reader = new FileReader();
    reader.onload = (e) => {
      formData.value.avatarImagePreview = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

// Handle certifications upload
const handleCertificationsUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    formData.value.certifications = file;
  }
};

// Add new social proof link field
const addSocialProofLink = () => {
  // Ensure socialProofLinks is always an array
  if (!Array.isArray(formData.value.socialProofLinks)) {
    formData.value.socialProofLinks = [""];
  }
  // Create a new array to trigger reactivity
  formData.value.socialProofLinks = [...formData.value.socialProofLinks, ""];
};

// Remove social proof link field
const removeSocialProofLink = (index) => {
  // Ensure socialProofLinks is always an array
  if (!Array.isArray(formData.value.socialProofLinks)) {
    formData.value.socialProofLinks = [""];
    return;
  }
  if (formData.value.socialProofLinks.length > 1) {
    // Create a new array to trigger reactivity
    formData.value.socialProofLinks = formData.value.socialProofLinks.filter(
      (_, i) => i !== index
    );
  }
};

// Add new social media link field
const addSocialMediaLink = () => {
  // Ensure socialMediaLinks is always an array
  if (!Array.isArray(formData.value.socialMediaLinks)) {
    formData.value.socialMediaLinks = [""];
  }
  // Create a new array to trigger reactivity
  formData.value.socialMediaLinks = [...formData.value.socialMediaLinks, ""];
};

// Remove social media link field
const removeSocialMediaLink = (index) => {
  // Ensure socialMediaLinks is always an array
  if (!Array.isArray(formData.value.socialMediaLinks)) {
    formData.value.socialMediaLinks = [""];
    return;
  }
  if (formData.value.socialMediaLinks.length > 1) {
    // Create a new array to trigger reactivity
    formData.value.socialMediaLinks = formData.value.socialMediaLinks.filter(
      (_, i) => i !== index
    );
  }
};

// Show validation error popup
const showValidationError = (message) => {
  errorMessage.value = message;
  showErrorDialog.value = true;
};

// Show success message popup
const showSuccessMessage = (message) => {
  successMessage.value = message;
  showSuccessDialog.value = true;
};

// Close error dialog
const closeErrorDialog = () => {
  showErrorDialog.value = false;
  errorMessage.value = "";
};

// Close success dialog
const closeSuccessDialog = () => {
  showSuccessDialog.value = false;
  successMessage.value = "";
};
</script>

<template>
  <!-- Error Dialog -->
  <VDialog v-model="showErrorDialog" max-width="500">
    <VCard>
      <VCardTitle class="d-flex align-center">
        <VIcon icon="tabler-alert-circle" color="error" class="me-2" />
        Validation Error
      </VCardTitle>
      <VCardText>
        {{ errorMessage }}
      </VCardText>
      <VCardActions>
        <VSpacer />
        <VBtn color="error" @click="closeErrorDialog">OK</VBtn>
      </VCardActions>
    </VCard>
  </VDialog>

  <!-- Success Dialog -->
  <VDialog v-model="showSuccessDialog" max-width="500">
    <VCard>
      <VCardTitle class="d-flex align-center">
        <VIcon icon="tabler-check-circle" color="success" class="me-2" />
        Success
      </VCardTitle>
      <VCardText>
        {{ successMessage }}
      </VCardText>
      <VCardActions>
        <VSpacer />
        <VBtn color="success" @click="closeSuccessDialog">OK</VBtn>
      </VCardActions>
    </VCard>
  </VDialog>

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
            <!-- Step 1: Company Information -->
            <VWindowItem>
              <VRow>
                <!-- Left column -->
                <VCol cols="12" md="6">
                  <!-- Company Name -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Company Name <span class="required-star">*</span>
                    </label>
                    <AppTextField
                      v-model="formData.companyName"
                      placeholder="Enter your company name"
                      :error="hasFieldError('companyName')"
                      :error-messages="formValidationErrors['companyName']"
                      @input="clearFieldError('companyName')"
                      :class="{ 'field-error': hasFieldError('companyName') }"
                    />
                  </div>

                  <!-- VAT ID -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      VAT ID
                    </label>
                    <AppTextField
                      v-model="formData.vatId"
                      placeholder="Enter your VAT ID"
                    />
                  </div>

                  <!-- Company Address Section -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Company Address <span class="required-star">*</span>
                    </label>
                    <p class="text-body-2 text-medium-emphasis mb-4">
                      Provide your company address for invoicing
                    </p>
                  </div>

                  <!-- Address Line 1 -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Address Line 1 <span class="required-star">*</span>
                    </label>
                    <AppTextField
                      v-model="formData.address1"
                      placeholder="Address Line 1"
                      :error="hasFieldError('address1')"
                      :error-messages="formValidationErrors['address1']"
                      @input="clearFieldError('address1')"
                      :class="{ 'field-error': hasFieldError('address1') }"
                    />
                  </div>

                  <!-- City -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      City
                    </label>
                    <AppTextField
                      v-model="formData.city"
                      placeholder="Munich"
                    />
                  </div>

                  <!-- State / Province -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      State / Province
                    </label>
                    <AppTextField
                      v-model="formData.state"
                      placeholder="Bavaria"
                    />
                  </div>

                  <!-- Contact Person -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Contact Person
                    </label>
                    <AppTextField
                      v-model="formData.contactPerson"
                      placeholder="Let us know who we can get in touch with"
                    />
                  </div>
                </VCol>
                <!-- Right column -->
                <VCol cols="12" md="6">
                  <!-- Country of Registration -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Country of Registration
                      <span class="required-star">*</span>
                    </label>
                    <AppSelect
                      v-model="formData.countryOfRegistration"
                      placeholder="Where your company is officially registered"
                      :items="[
                        'Germany',
                        'Austria',
                        'Switzerland',
                        'Italy',
                        'France',
                        'Spain',
                        'United States',
                        'Canada',
                        'Australia',
                        'New Zealand',
                        'Other',
                      ]"
                      :error="hasFieldError('countryOfRegistration')"
                      :error-messages="
                        formValidationErrors['countryOfRegistration']
                      "
                      @input="clearFieldError('countryOfRegistration')"
                      :class="{
                        'field-error': hasFieldError('countryOfRegistration'),
                      }"
                    />
                  </div>

                  <!-- Address Line 2 -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Address Line 2 (optional)
                    </label>
                    <AppTextField
                      v-model="formData.address2"
                      placeholder="Address Line 2"
                    />
                  </div>

                  <!-- Postal Code -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Postal Code
                    </label>
                    <AppTextField
                      v-model="formData.postalCode"
                      placeholder="231465"
                    />
                  </div>

                  <!-- Country -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Country <span class="required-star">*</span>
                    </label>
                    <AppTextField
                      v-model="formData.country"
                      placeholder="Enter your country"
                      :error="hasFieldError('country')"
                      :error-messages="formValidationErrors['country']"
                      @input="clearFieldError('country')"
                      :class="{ 'field-error': hasFieldError('country') }"
                    />
                  </div>

                  <!-- Business Type -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Business Type <span class="required-star">*</span>
                    </label>
                    <AppSelect
                      v-model="formData.businessType"
                      placeholder="Select your business type"
                      :items="[
                        'Sole Proprietorship',
                        'Partnership',
                        'Limited Liability Company (LLC)',
                        'Corporation',
                        'Non-Profit Organization',
                        'Other',
                      ]"
                      :error="hasFieldError('businessType')"
                      :error-messages="formValidationErrors['businessType']"
                      @input="clearFieldError('businessType')"
                      :class="{ 'field-error': hasFieldError('businessType') }"
                    />
                  </div>
                </VCol>
              </VRow>
            </VWindowItem>
            <!-- Step 2: Business Details -->
            <VWindowItem>
              <VRow>
                <!-- Left column -->
                <VCol cols="12" md="6">
                  <!-- Company Logo -->
                  <div class="mb-6">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Company Logo <span class="required-star">*</span>
                    </label>
                    <p class="text-body-2 text-medium-emphasis mb-4">
                      High quality image, shown as your company logo
                    </p>
                    <div class="d-flex align-center gap-4">
                      <div
                        class="image-preview"
                        :class="{
                          'field-error': hasFieldError('passportImage'),
                        }"
                        style="
                          width: 184px;
                          height: 239px;
                          display: flex;
                          align-items: center;
                          justify-content: center;
                          background-color: rgba(114, 136, 129, 0.26);
                          border-radius: 8px;
                          box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
                        "
                      >
                        <VIcon
                          v-if="!formData.passportImage"
                          icon="tabler-photo"
                          size="24"
                          color="grey"
                        />
                        <img
                          v-else
                          :src="formData.passportImagePreview"
                          style="width: 100%; height: 100%; object-fit: cover"
                        />
                      </div>
                      <div
                        class="d-flex flex-column gap-2"
                        style="margin-top: 130px; max-width: 260px"
                      >
                        <div class="d-flex gap-2 align-center">
                          <VBtn
                            variant="flat"
                            size="small"
                            style="
                              background-color: #ec8d22 !important;
                              color: #fff !important;
                              font-size: 13px !important;
                              min-height: 36px;
                              box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
                              border-radius: 6px !important;
                              max-width: 260px;
                            "
                            @click="$refs.passportInput.click()"
                          >
                            Upload Company Logo
                          </VBtn>
                          <VBtn
                            v-if="formData.passportImage"
                            variant="flat"
                            size="small"
                            style="
                              background-color: rgba(
                                114,
                                136,
                                129,
                                0.26
                              ) !important;
                              color: #728881 !important;
                              min-width: 36px;
                              min-height: 36px;
                              max-width: 36px;
                              border-radius: 6px !important;
                              box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
                            "
                            @click="
                              () => {
                                formData.passportImage = null;
                                formData.passportImagePreview = null;
                                clearFieldError('passportImage');
                              }
                            "
                          >
                            <VIcon
                              icon="tabler-trash"
                              size="16"
                              color="#728881"
                            />
                          </VBtn>
                        </div>
                        <p
                          class="text-caption text-medium-emphasis mt-2 mb-0"
                          style="max-width: 260px; font-size: 11px"
                        >
                          Allowed JPG or PNG, Preferred 520*430 px, Max Size 5Mb
                        </p>
                        <p
                          v-if="hasFieldError('passportImage')"
                          class="text-caption text-error mt-1 mb-0"
                          style="font-size: 11px"
                        >
                          {{ formValidationErrors["passportImage"] }}
                        </p>
                      </div>
                    </div>
                    <input
                      ref="passportInput"
                      type="file"
                      accept="image/*"
                      style="display: none"
                      @change="handlePassportImageUpload"
                    />
                  </div>

                  <!-- Activity Specialization -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Activity Specialization
                      <span class="required-star">*</span>
                    </label>
                    <AppSelect
                      v-model="formData.activitySpecialization"
                      placeholder="Activities your company can lead (e.g., hiking, diving)"
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
                      :error="hasFieldError('activitySpecialization')"
                      :error-messages="
                        formValidationErrors['activitySpecialization']
                      "
                      @input="clearFieldError('activitySpecialization')"
                      :class="{
                        'field-error': hasFieldError('activitySpecialization'),
                      }"
                    />
                  </div>

                  <!-- Listing Preference -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Would you like to get listed with adventures in Explorer
                      Elite? <span class="required-star">*</span>
                    </label>
                    <VRadioGroup
                      v-model="formData.wantToBeListed"
                      class="mt-2"
                      :class="{
                        'field-error': hasFieldError('wantToBeListed'),
                      }"
                      @update:model-value="clearFieldError('wantToBeListed')"
                    >
                      <VRadio
                        value="yes"
                        label="Yes, I would like to get listed"
                      />
                      <VRadio
                        value="no"
                        label="No, I would like to use the platform as a software solution"
                      />
                      <VRadio value="unsure" label="Not sure yet" />
                    </VRadioGroup>
                    <p
                      v-if="hasFieldError('wantToBeListed')"
                      class="text-caption text-error mt-1 mb-0"
                      style="font-size: 11px"
                    >
                      {{ formValidationErrors["wantToBeListed"] }}
                    </p>
                  </div>
                </VCol>
                <!-- Right column -->
                <VCol cols="12" md="6">
                  <!-- Business License -->
                  <div class="mb-6">
                    <h6 class="text-h6 font-weight-medium mb-2">
                      Business License
                    </h6>
                    <p class="text-body-2 text-medium-emphasis mb-4">
                      Upload your business license or registration document
                    </p>
                    <div class="d-flex align-center gap-4">
                      <div
                        class="image-preview"
                        style="
                          width: 97px;
                          height: 95px;
                          display: flex;
                          align-items: center;
                          justify-content: center;
                          background-color: rgba(114, 136, 129, 0.26);
                          border-radius: 8px;
                          box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
                        "
                      >
                        <VIcon
                          v-if="!formData.avatarImage"
                          icon="tabler-photo"
                          size="24"
                          color="grey"
                        />
                        <img
                          v-else
                          :src="formData.avatarImagePreview"
                          style="
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                            border-radius: 8px;
                          "
                        />
                      </div>
                      <div
                        class="d-flex flex-column gap-2"
                        style="margin-top: 20px"
                      >
                        <div class="d-flex gap-2 align-center">
                          <VBtn
                            variant="flat"
                            size="small"
                            style="
                              background-color: #ec8d22 !important;
                              color: #fff !important;
                              font-size: 13px !important;
                              min-height: 36px;
                              box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
                              border-radius: 6px !important;
                              max-width: 200px;
                            "
                            @click="$refs.avatarInput.click()"
                          >
                            Upload Business License
                          </VBtn>
                          <VBtn
                            v-if="formData.avatarImage"
                            variant="flat"
                            size="small"
                            style="
                              background-color: rgba(
                                114,
                                136,
                                129,
                                0.26
                              ) !important;
                              color: #728881 !important;
                              min-width: 36px;
                              min-height: 36px;
                              max-width: 36px;
                              border-radius: 6px !important;
                              box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
                            "
                            @click="
                              () => {
                                formData.avatarImage = null;
                                formData.avatarImagePreview = null;
                              }
                            "
                          >
                            <VIcon
                              icon="tabler-trash"
                              size="16"
                              color="#728881"
                            />
                          </VBtn>
                        </div>
                        <p
                          class="text-caption text-medium-emphasis mt-2 mb-0"
                          style="font-size: 11px; max-width: 400px"
                        >
                          Allowed JPG, PNG or PDF, Max size 5Mb
                        </p>
                      </div>
                    </div>
                    <input
                      ref="avatarInput"
                      type="file"
                      accept="image/*,.pdf"
                      style="display: none"
                      @change="handleAvatarImageUpload"
                    />
                  </div>

                  <!-- Short Bio -->
                  <AppTextField
                    v-model="formData.shortBio"
                    label="Company Bio *"
                    placeholder="Tell us about your company and what you do in a few sentences"
                    type="textarea"
                    rows="5"
                    class="mb-4"
                    :error="hasFieldError('shortBio')"
                    :error-messages="formValidationErrors['shortBio']"
                    @input="clearFieldError('shortBio')"
                    :class="{ 'field-error': hasFieldError('shortBio') }"
                  />

                  <!-- Certifications -->
                  <AppTextField
                    v-model="formData.certifications"
                    label="Certifications/Licenses"
                    placeholder="Upload relevant company certifications or licenses"
                    type="file"
                    accept=".pdf,.doc,.docx"
                    class="mb-4"
                  />
                </VCol>
              </VRow>
            </VWindowItem>
            <!-- Step 3: Social Links -->
            <VWindowItem>
              <VRow>
                <!-- Left Column -->
                <VCol cols="12" md="6">
                  <!-- Company Website -->
                  <div class="mb-6">
                    <h6 class="text-h6 font-weight-medium mb-2">
                      Company Website
                    </h6>
                    <AppTextField
                      v-model="formData.companyWebsite"
                      placeholder="Add company official website"
                      class="mb-4"
                    />
                  </div>

                  <!-- Social Media Links -->
                  <div class="mb-6">
                    <h6 class="text-h6 font-weight-medium mb-2">
                      Social Media Links
                    </h6>

                    <div
                      v-for="(link, index) in formData.socialMediaLinks"
                      :key="index"
                      class="mb-3"
                    >
                      <div class="d-flex gap-2">
                        <AppTextField
                          v-model="formData.socialMediaLinks[index]"
                          placeholder="Add a link to your socials or website that shows your previous work"
                          class="flex-grow-1"
                        >
                          <template #append-inner>
                            <VBtn
                              v-if="
                                index === formData.socialMediaLinks.length - 1
                              "
                              variant="text"
                              size="small"
                              @click="addSocialMediaLink"
                              class="px-0"
                            >
                              <VIcon icon="tabler-plus" size="20" />
                            </VBtn>
                          </template>
                        </AppTextField>
                        <VBtn
                          v-if="formData.socialMediaLinks.length > 1"
                          variant="tonal"
                          size="small"
                          color="error"
                          @click="removeSocialMediaLink(index)"
                          class="mt-1"
                        >
                          <VIcon icon="tabler-minus" size="16" />
                        </VBtn>
                      </div>
                    </div>
                  </div>

                  <!-- Terms and Conditions -->
                  <div class="mb-6">
                    <div class="d-flex align-start gap-3">
                      <VCheckbox
                        v-model="formData.termsAccepted"
                        class="mt-1"
                      />
                      <div class="text-body-2">
                        By continuing, you agree to our
                        <a href="#" class="text-primary">Terms of Service</a>,
                        <a href="#" class="text-primary">Privacy Policy</a>, and
                        all related
                        <a href="#" class="text-primary">Policies</a>. Full
                        details available on our
                        <a href="#" class="text-primary">Legal Page</a>.
                      </div>
                    </div>
                  </div>
                </VCol>

                <!-- Right Column -->
                <VCol cols="12" md="6">
                  <!-- Social Proof Links -->
                  <div class="mb-6">
                    <h6 class="text-h6 font-weight-medium mb-2">
                      Social Proof Links
                    </h6>

                    <div
                      v-for="(link, index) in formData.socialProofLinks"
                      :key="index"
                      class="mb-3"
                    >
                      <div class="d-flex gap-2">
                        <AppTextField
                          v-model="formData.socialProofLinks[index]"
                          placeholder="Links to 'reviews', 'social proof', or 'feedbacks' about your activities"
                          class="flex-grow-1"
                        >
                          <template #append-inner>
                            <VBtn
                              v-if="
                                index === formData.socialProofLinks.length - 1
                              "
                              variant="text"
                              size="small"
                              @click="addSocialProofLink"
                              class="px-0"
                            >
                              <VIcon icon="tabler-plus" size="20" />
                            </VBtn>
                          </template>
                        </AppTextField>
                        <VBtn
                          v-if="formData.socialProofLinks.length > 1"
                          variant="tonal"
                          size="small"
                          color="error"
                          @click="removeSocialProofLink(index)"
                          class="mt-1"
                        >
                          <VIcon icon="tabler-minus" size="16" />
                        </VBtn>
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
                  ? router.push({ name: 'access-control' })
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
            <VBtn v-else class="next-btn-dark" @click="handleNextStep">
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

.v-card {
  margin-top: 0 !important;
}
@media (max-width: 1200px) {
  .v-card {
    max-width: 98vw !important;
    padding: 32px 8vw 24px 8vw !important;
  }
}
@media (max-width: 600px) {
  .v-card {
    padding: 16px 4vw 16px 4vw !important;
  }
}

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
