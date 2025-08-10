<script setup>
import { individualUserService } from "@/services/api";
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const numberedSteps = [
  {
    title: "Personal Information",
  },
  {
    title: "Account Details",
  },
  {
    title: "Social Links",
  },
];

const currentStep = ref(0);
const loading = ref(false);
const formValidationErrors = ref({});

const formData = ref({
  // Step 1 fields
  fullName: "",
  nationality: "",
  address1: "",
  city: "",
  state: "",
  dob: "",
  languages: [],
  address2: "",
  postalCode: "",
  country: "",
  // Step 2 fields
  passportImage: null,
  avatarImage: null,
  activitySpecialization: "",
  yearsOfExperience: "",
  emergencyContactName: "",
  wantToBeListed: "",
  shortBio: "",
  certifications: null,
  countryOfOperation: "",
  emergencyContactPhone: "",
  // Step 3 fields
  firstName: "",
  lastName: "",
  twitter: "",
  facebook: "",
  googlePlus: "",
  linkedIn: "",
  // New Step 3 fields
  socialMediaLinks: [""],
  socialProofLinks: [""],
  termsAccepted: false,
});

// Required fields for step 1 (Personal Information)
const requiredFieldsStep1 = [
  "fullName",
  "nationality",
  "address1",
  "city",
  "dob",
  "languages",
  "country",
];

// Required fields for step 2 (Account Details)
const requiredFieldsStep2 = [
  "passportImage",
  "activitySpecialization",
  "yearsOfExperience",
  "emergencyContactName",
  "wantToBeListed",
  "shortBio",
  "countryOfOperation",
  "emergencyContactPhone",
];

// Validation function for step 1
const validateStep1 = () => {
  const errors = {};

  requiredFieldsStep1.forEach((field) => {
    if (field === "languages") {
      if (!formData.value[field] || formData.value[field].length === 0) {
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
      // Scroll to first error
      const firstErrorField = document.querySelector(".field-error");
      if (firstErrorField) {
        firstErrorField.scrollIntoView({ behavior: "smooth", block: "center" });
      }
    }
  } else if (currentStep.value === 1) {
    // Validate step 2
    if (validateStep2()) {
      currentStep.value++;
    } else {
      // Scroll to first error
      const firstErrorField = document.querySelector(".field-error");
      if (firstErrorField) {
        firstErrorField.scrollIntoView({ behavior: "smooth", block: "center" });
      }
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
      alert("Please accept the terms and conditions");
      return;
    }

    const response = await individualUserService.register(formData.value);
    console.log("Registration successful:", response);

    // Show success message
    alert("Registration completed successfully!");

    // Redirect to dashboard instead of listing
    router.push("/");
  } catch (error) {
    console.error("Registration failed:", error);
    alert("Registration failed: " + (error.message || "Unknown error"));
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

// Add new social media link field
const addSocialMediaLink = () => {
  formData.value.socialMediaLinks.push("");
};

// Remove social media link field
const removeSocialMediaLink = (index) => {
  if (formData.value.socialMediaLinks.length > 1) {
    formData.value.socialMediaLinks.splice(index, 1);
  }
};

// Add new social proof link field
const addSocialProofLink = () => {
  formData.value.socialProofLinks.push("");
};

// Remove social proof link field
const removeSocialProofLink = (index) => {
  if (formData.value.socialProofLinks.length > 1) {
    formData.value.socialProofLinks.splice(index, 1);
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
            <!-- Step 1: Personal Information -->
            <VWindowItem>
              <VRow>
                <!-- Left column -->
                <VCol cols="12" md="6">
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Full Name <span class="required-star">*</span>
                    </label>
                    <AppTextField
                      v-model="formData.fullName"
                      placeholder="Enter your full name"
                      :error="hasFieldError('fullName')"
                      :error-messages="formValidationErrors['fullName']"
                      @input="clearFieldError('fullName')"
                      :class="{ 'field-error': hasFieldError('fullName') }"
                    />
                  </div>
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Nationality <span class="required-star">*</span>
                    </label>
                    <AppTextField
                      v-model="formData.nationality"
                      placeholder="Enter your nationality"
                      :error="hasFieldError('nationality')"
                      :error-messages="formValidationErrors['nationality']"
                      @input="clearFieldError('nationality')"
                      :class="{ 'field-error': hasFieldError('nationality') }"
                    />
                  </div>
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
                      placeholder="Enter address line 1"
                      :error="hasFieldError('address1')"
                      :error-messages="formValidationErrors['address1']"
                      @input="clearFieldError('address1')"
                      :class="{ 'field-error': hasFieldError('address1') }"
                    />
                  </div>
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      City <span class="required-star">*</span>
                    </label>
                    <AppTextField
                      v-model="formData.city"
                      placeholder="Enter your city"
                      :error="hasFieldError('city')"
                      :error-messages="formValidationErrors['city']"
                      @input="clearFieldError('city')"
                      :class="{ 'field-error': hasFieldError('city') }"
                    />
                  </div>
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      State/Province
                    </label>
                    <AppTextField
                      v-model="formData.state"
                      placeholder="Enter state or province"
                    />
                  </div>
                </VCol>
                <!-- Right column -->
                <VCol cols="12" md="6">
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Date of Birth <span class="required-star">*</span>
                    </label>
                    <AppDateTimePicker
                      ref="dobPicker"
                      v-model="formData.dob"
                      placeholder="YYYY-MM-DD"
                      :config="{ dateFormat: 'Y-m-d', allowInput: true }"
                      :error="hasFieldError('dob')"
                      :error-messages="formValidationErrors['dob']"
                      @input="clearFieldError('dob')"
                      :class="{ 'field-error': hasFieldError('dob') }"
                    />
                  </div>
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Languages Spoken <span class="required-star">*</span>
                    </label>
                    <AppSelect
                      v-model="formData.languages"
                      placeholder="Select languages"
                      :items="[
                        'English',
                        'French',
                        'German',
                        'Spanish',
                        'Other',
                      ]"
                      multiple
                      :error="hasFieldError('languages')"
                      :error-messages="formValidationErrors['languages']"
                      @input="clearFieldError('languages')"
                      :class="{ 'field-error': hasFieldError('languages') }"
                    />
                  </div>
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Address Line 2
                    </label>
                    <AppTextField
                      v-model="formData.address2"
                      placeholder="Enter address line 2"
                    />
                  </div>
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
                      placeholder="Enter postal code"
                    />
                  </div>
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
                </VCol>
              </VRow>
            </VWindowItem>
            <!-- Step 2: Account Details -->
            <VWindowItem>
              <VRow>
                <!-- Left column -->
                <VCol cols="12" md="6">
                  <!-- Explorer Passport Image -->
                  <div class="mb-6">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Explorer Passport Image
                      <span class="required-star">*</span>
                    </label>
                    <p class="text-body-2 text-medium-emphasis mb-4">
                      High quality image, shown as your Explorer Elite passport
                      profile image
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
                          :src="
                            formData.passportImagePreview ||
                            formData.passportImage
                          "
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
                            Upload Explorer Passport Image
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
                      placeholder="Activities you can lead (e.g., hiking, diving)"
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

                  <!-- Years of Experience -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Years of Experience <span class="required-star">*</span>
                    </label>
                    <AppSelect
                      v-model="formData.yearsOfExperience"
                      placeholder="Total years you've been guiding adventures"
                      :items="[
                        'Less than 1 year',
                        '1-2 years',
                        '3-5 years',
                        '6-10 years',
                        '11-15 years',
                        '16-20 years',
                        'More than 20 years',
                      ]"
                      :error="hasFieldError('yearsOfExperience')"
                      :error-messages="
                        formValidationErrors['yearsOfExperience']
                      "
                      @input="clearFieldError('yearsOfExperience')"
                      :class="{
                        'field-error': hasFieldError('yearsOfExperience'),
                      }"
                    />
                  </div>

                  <!-- Emergency Contact Name -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Emergency Contact Name
                      <span class="required-star">*</span>
                    </label>
                    <AppTextField
                      v-model="formData.emergencyContactName"
                      placeholder="In case we need to contact someone urgently"
                      :error="hasFieldError('emergencyContactName')"
                      :error-messages="
                        formValidationErrors['emergencyContactName']
                      "
                      @input="clearFieldError('emergencyContactName')"
                      :class="{
                        'field-error': hasFieldError('emergencyContactName'),
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
                  <!-- Avatar Image -->
                  <div class="mb-6">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Avatar Image
                    </label>
                    <p class="text-body-2 text-medium-emphasis mb-4">
                      Shown as your platform profile image
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
                          :src="
                            formData.avatarImagePreview || formData.avatarImage
                          "
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
                            Upload Avatar Image
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
                          Allowed JPG or PNG, Preferred 250*250 px, Max size 3Mb
                        </p>
                      </div>
                    </div>
                    <input
                      ref="avatarInput"
                      type="file"
                      accept="image/*"
                      style="display: none"
                      @change="handleAvatarImageUpload"
                    />
                  </div>

                  <!-- Short Bio -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Short Bio <span class="required-star">*</span>
                    </label>
                    <AppTextField
                      v-model="formData.shortBio"
                      placeholder="Tell us who you are and what you do in a few sentences"
                      type="textarea"
                      rows="5"
                      :error="hasFieldError('shortBio')"
                      :error-messages="formValidationErrors['shortBio']"
                      @input="clearFieldError('shortBio')"
                      :class="{ 'field-error': hasFieldError('shortBio') }"
                    />
                  </div>

                  <!-- Certifications -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Certifications/Licenses
                    </label>
                    <AppTextField
                      v-model="formData.certifications"
                      placeholder="Upload relevant guiding or safety certifications (first aid, alpine guide)"
                      type="file"
                      accept=".pdf,.doc,.docx"
                    />
                  </div>

                  <!-- Country/Region of Operation -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Country/Region of Operation
                      <span class="required-star">*</span>
                    </label>
                    <AppSelect
                      v-model="formData.countryOfOperation"
                      placeholder="Areas you usually operate in (select the main areas of activity)"
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
                      :error="hasFieldError('countryOfOperation')"
                      :error-messages="
                        formValidationErrors['countryOfOperation']
                      "
                      @input="clearFieldError('countryOfOperation')"
                      :class="{
                        'field-error': hasFieldError('countryOfOperation'),
                      }"
                    />
                  </div>

                  <!-- Emergency Contact Phone -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                    >
                      Emergency Contact Phone
                      <span class="required-star">*</span>
                    </label>
                    <AppTextField
                      v-model="formData.emergencyContactPhone"
                      placeholder="+49 1236 456 789"
                      :error="hasFieldError('emergencyContactPhone')"
                      :error-messages="
                        formValidationErrors['emergencyContactPhone']
                      "
                      @input="clearFieldError('emergencyContactPhone')"
                      :class="{
                        'field-error': hasFieldError('emergencyContactPhone'),
                      }"
                    />
                  </div>
                </VCol>
              </VRow>
            </VWindowItem>
            <!-- Step 3: Social Links -->
            <VWindowItem>
              <VRow>
                <!-- Left Column -->
                <VCol cols="12" md="6">
                  <!-- Website or Social Media Links -->
                  <div class="mb-6">
                    <h6 class="text-h6 font-weight-medium mb-2">
                      Website or Social Media Links
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
                          placeholder="Links to reviews, social proof, or feedbacks about your activities"
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

/* Required star styling */
.required-star {
  color: #ff4444;
  font-weight: bold;
  margin-left: 4px;
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

/* بزرگ کردن لیبل فیلدها حتی برای AppTextField سفارشی */
.v-label,
.v-field-label,
.v-field__label,
.app-text-field-label,
.AppTextField label,
.AppTextField .v-label {
  font-size: 220px !important;
  font-weight: 700 !important;
  line-height: 1.5 !important;
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

@media (max-width: 768px) {
  .stepper-container {
    overflow-x: auto;
    justify-content: flex-start;
    padding: 0 16px;
    /* Custom scrollbar styles */
    scrollbar-width: thin;
    scrollbar-color: #ec8d22 #f5f5f5;
  }

  /* Webkit scrollbar styles for Chrome/Safari */
  .stepper-container::-webkit-scrollbar {
    height: 6px;
  }

  .stepper-container::-webkit-scrollbar-track {
    background: #f5f5f5;
    border-radius: 3px;
  }

  .stepper-container::-webkit-scrollbar-thumb {
    background: #ec8d22;
    border-radius: 3px;
    transition: background 0.2s;
  }

  .stepper-container::-webkit-scrollbar-thumb:hover {
    background: #d67d1a;
  }

  .step-item {
    padding: 4px 6px;
  }
  .step-number {
    font-size: 20px;
  }
  .step-label {
    font-size: 14px;
  }
  .step-line {
    min-width: 40px;
    max-width: 80px;
    margin: 0 2px;
  }
}

/* Remove custom card and font styles, revert to default */
.d-flex.justify-center.align-center {
  min-height: unset !important;
  margin-top: 0 !important;
  padding-top: 0 !important;
}

.v-card {
  margin-top: 0 !important;
}

.v-card .v-label,
.v-card label.v-label {
  font-size: 22px !important;
  font-weight: 700 !important;
  line-height: 1.5 !important;
}

/* Move browser date icon to the right for type=date fields */
:deep(input[type="date"]) {
  background-image: url("/images/4svg/wired-outline-28-calendar-hover-pinch.png");
  background-repeat: no-repeat;
  background-position: right 12px center;
  background-size: 24px 24px;
  padding-right: 36px !important;
  padding-left: 8px !important;
}
:deep(input[type="date"]::-webkit-calendar-picker-indicator) {
  opacity: 0;
  z-index: 2;
  position: absolute;
  right: 12px;
  width: 24px;
  height: 24px;
  cursor: pointer;
}

.flatpickr-calendar {
  border-radius: 16px !important;
  background: #fff !important;
  box-shadow: 0 4px 24px 0 rgba(44, 44, 44, 0.1) !important;
  font-family: "Karla", sans-serif !important;
  border: none !important;
}
.flatpickr-months .flatpickr-month {
  border-radius: 12px !important;
  background: #fff !important;
  font-family: "Karla", sans-serif !important;
  font-size: 18px !important;
  color: #2f2b3d !important;
}
.flatpickr-current-month input.cur-year {
  font-size: 18px !important;
  font-family: "Karla", sans-serif !important;
  color: #2f2b3d !important;
}
.flatpickr-weekdays {
  font-size: 16px !important;
  color: #444151 !important;
  font-family: "Karla", sans-serif !important;
}
.flatpickr-day {
  font-size: 16px !important;
  color: #444151 !important;
  border-radius: 8px !important;
  font-family: "Karla", sans-serif !important;
  transition: background 0.2s;
}
.flatpickr-day.selected,
.flatpickr-day.startRange,
.flatpickr-day.endRange,
.flatpickr-day.selected.inRange {
  background: #ec8d22 !important;
  color: #fff !important;
  border: none !important;
}
.flatpickr-day.today:not(.selected) {
  border: 1.5px solid #ec8d22 !important;
  color: #ec8d22 !important;
  background: #fff !important;
}
.flatpickr-day.flatpickr-disabled,
.flatpickr-day.prevMonthDay,
.flatpickr-day.nextMonthDay {
  color: #b0b0b0 !important;
  background: #fff !important;
  opacity: 0.6 !important;
}
.flatpickr-day:hover:not(.selected):not(.flatpickr-disabled) {
  background: #f7e3c7 !important;
  color: #ec8d22 !important;
}
.flatpickr-months .flatpickr-prev-month,
.flatpickr-months .flatpickr-next-month {
  background: #f2f2f2 !important;
  border-radius: 50% !important;
  width: 32px !important;
  height: 32px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  color: #b0b0b0 !important;
  margin: 0 4px !important;
  box-shadow: none !important;
}
.flatpickr-months .flatpickr-prev-month:hover,
.flatpickr-months .flatpickr-next-month:hover {
  background: #e0e0e0 !important;
  color: #ec8d22 !important;
}
.flatpickr-monthDropdown-months .flatpickr-monthDropdown-month,
.flatpickr-months .flatpickr-month {
  border-radius: 8px !important;
  font-size: 16px !important;
  color: #444151 !important;
}
.flatpickr-monthDropdown-months .flatpickr-monthDropdown-month.selected,
.flatpickr-monthDropdown-months .flatpickr-monthDropdown-month:hover {
  background: #ec8d22 !important;
  color: #fff !important;
}

.app-picker-field {
  position: relative;
}
.app-picker-field .calendar-icon-png {
  position: absolute;
  top: 50%;
  right: 8px;
  transform: translateY(-50%);
  width: 24px;
  height: 24px;
  background: url("/images/4svg/wired-outline-28-calendar-hover-pinch.png")
    no-repeat center center;
  background-size: 24px 24px;
  cursor: pointer;
  z-index: 3;
  pointer-events: auto;
}
.app-picker-field input.flat-picker-custom-style {
  padding-right: 44px !important;
}

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
