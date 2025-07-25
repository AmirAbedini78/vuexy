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

    // Redirect to timeline page with user ID
    router.push({
      name: "registration-timeline",
      params: {
        type: "individual",
        id: response.data.id,
      },
    });
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
  }
};

const handleAvatarImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    formData.value.avatarImage = file;
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
                  <AppTextField
                    v-model="formData.fullName"
                    label="Full Name"
                    placeholder="Enter your full name"
                  />
                  <AppTextField
                    v-model="formData.nationality"
                    label="Nationality"
                    placeholder="Enter your nationality"
                    class="mt-4"
                  />
                  <AppTextField
                    v-model="formData.address1"
                    label="Address Line 1"
                    placeholder="Enter address line 1"
                    class="mt-4"
                  />
                  <AppTextField
                    v-model="formData.city"
                    label="City"
                    placeholder="Enter your city"
                    class="mt-4"
                  />
                <AppTextField
                    v-model="formData.state"
                    label="State/Province"
                    placeholder="Enter state or province"
                    class="mt-4"
                />
              </VCol>
                <!-- Right column -->
                <VCol cols="12" md="6">
                  <div class="date-picker-wrapper">
                    <AppDateTimePicker
                      ref="dobPicker"
                      v-model="formData.dob"
                      label="Date of Birth"
                      placeholder="YYYY-MM-DD"
                      :config="{ dateFormat: 'Y-m-d', allowInput: true }"
                    >
                      <template #label>
                        Date of Birth <span class="required-star">*</span>
                      </template>
                    </AppDateTimePicker>
                  </div>
                  <AppSelect
                    v-model="formData.languages"
                    label="Languages Spoken"
                    placeholder="Select languages"
                    :items="['English', 'French', 'German', 'Spanish', 'Other']"
                    multiple
                    class="mt-4"
                  />
                <AppTextField
                    v-model="formData.address2"
                    label="Address Line 2"
                    placeholder="Enter address line 2"
                    class="mt-4"
                  />
                <AppTextField
                    v-model="formData.postalCode"
                    label="Postal Code"
                    placeholder="Enter postal code"
                    class="mt-4"
                  />
                <AppTextField
                    v-model="formData.country"
                    label="Country"
                    placeholder="Enter your country"
                    class="mt-4"
                />
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
                    <h6 class="text-h6 font-weight-medium mb-2">
                      Explorer Passport Image
                </h6>
                    <p class="text-body-2 text-medium-emphasis mb-4">
                      High quality image, shown as your Explorer Elite passport
                      profile image
                    </p>
                    <div class="d-flex align-center gap-4">
                      <div
                        class="image-preview"
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
                          :src="formData.passportImage"
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
                            @click="formData.passportImage = null"
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
                  <AppSelect
                    v-model="formData.activitySpecialization"
                    label="Activity Specialization"
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
                    class="mb-4"
                  />

                  <!-- Years of Experience -->
                  <AppSelect
                    v-model="formData.yearsOfExperience"
                    label="Years of Experience"
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
                    class="mb-4"
                  />

                  <!-- Emergency Contact Name -->
                <AppTextField
                    v-model="formData.emergencyContactName"
                    label="Emergency Contact Name"
                    placeholder="In case we need to contact someone urgently"
                    class="mb-4"
                  />

                  <!-- Listing Preference -->
                  <div class="mb-4">
                    <label
                      class="v-label text-body-2 mb-3 d-block"
                      style="
                        font-size: 16px !important;
                        font-weight: 400 !important;
                      "
                      >Would you like to get listed with adventures in Explorer
                      Elite?</label
                    >
                    <VRadioGroup v-model="formData.wantToBeListed" class="mt-2">
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
                  </div>
              </VCol>
                <!-- Right column -->
                <VCol cols="12" md="6">
                  <!-- Avatar Image -->
                  <div class="mb-6">
                    <h6 class="text-h6 font-weight-medium mb-2">
                      Avatar Image
                    </h6>
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
                          :src="formData.avatarImage"
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
                            @click="formData.avatarImage = null"
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
                  <AppTextField
                    v-model="formData.shortBio"
                    label="Short Bio"
                    placeholder="Tell us who you are and what you do in a few sentences"
                    type="textarea"
                    rows="5"
                    class="mb-4"
                  />

                  <!-- Certifications -->
                  <AppTextField
                    v-model="formData.certifications"
                    label="Certifications/Licenses"
                    placeholder="Upload relevant guiding or safety certifications (first aid, alpine guide)"
                    type="file"
                    accept=".pdf,.doc,.docx"
                    class="mb-4"
                  />

                  <!-- Country/Region of Operation -->
                <AppSelect
                    v-model="formData.countryOfOperation"
                    label="Country/Region of Operation"
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
                    class="mb-4"
                  />

                  <!-- Emergency Contact Phone -->
                  <AppTextField
                    v-model="formData.emergencyContactPhone"
                    label="Emergency Contact Phone"
                    placeholder="+49 1236 456 789"
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
</template>

<style scoped>
.custom-stepper {
  width: 100%;
  margin-bottom: 32px;
  margin-top: -50px !important;
  padding-top: 0 !important;
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
</style>
