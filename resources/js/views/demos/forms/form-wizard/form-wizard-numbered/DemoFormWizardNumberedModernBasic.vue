<script setup>
import { ref } from "vue";

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
  // Step 3 fields (if needed, keep or remove as you wish)
  firstName: "",
  lastName: "",
  twitter: "",
  facebook: "",
  googlePlus: "",
  linkedIn: "",
});

const onSubmit = () => {
  console.log(formData.value);
};

// Handle image uploads
const handlePassportImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      formData.value.passportImage = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const handleAvatarImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      formData.value.avatarImage = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};
</script>

<template>
  <!-- 👉 Stepper -->
  <div class="mb-6">
    <AppStepper
      v-model:current-step="currentStep"
      align="start"
      :items="numberedSteps"
    />
  </div>

  <div class="d-flex justify-center align-center" style="min-height: 60vh">
    <VCard style="width: 80vw; max-width: 1200px">
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
                  <AppTextField
                    v-model="formData.dob"
                    label="Date of Birth"
                    placeholder="YYYY-MM-DD"
                    type="date"
                  />
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
                          width: 120px;
                          height: 100px;
                          border: 2px dashed #ccc;
                          display: flex;
                          align-items: center;
                          justify-content: center;
                          background-color: #f5f5f5;
                        "
                      >
                        <VIcon
                          v-if="!formData.passportImage"
                          icon="tabler-user"
                          size="40"
                          color="grey"
                        />
                        <img
                          v-else
                          :src="formData.passportImage"
                          style="width: 100%; height: 100%; object-fit: cover"
                        />
                      </div>
                      <div class="d-flex flex-column gap-2">
                        <VBtn
                          variant="outlined"
                          size="small"
                          @click="$refs.passportInput.click()"
                        >
                          Upload Explorer Passport Image
                        </VBtn>
                        <VBtn
                          v-if="formData.passportImage"
                          variant="tonal"
                          size="small"
                          color="error"
                          @click="formData.passportImage = null"
                        >
                          <VIcon icon="tabler-trash" size="16" />
                        </VBtn>
                      </div>
                    </div>
                    <input
                      ref="passportInput"
                      type="file"
                      accept="image/*"
                      style="display: none"
                      @change="handlePassportImageUpload"
                    />
                    <p class="text-caption text-medium-emphasis mt-2">
                      Allowed JPG or PNG, Preferred 520*430 px, Max Size 5Mb
                    </p>
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
                    <label class="v-label text-body-2 mb-3 d-block"
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
                          width: 80px;
                          height: 80px;
                          border: 2px dashed #ccc;
                          display: flex;
                          align-items: center;
                          justify-content: center;
                          background-color: #f5f5f5;
                          border-radius: 50%;
                        "
                      >
                        <VIcon
                          v-if="!formData.avatarImage"
                          icon="tabler-user"
                          size="30"
                          color="grey"
                        />
                        <img
                          v-else
                          :src="formData.avatarImage"
                          style="
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                            border-radius: 50%;
                          "
                        />
                      </div>
                      <div class="d-flex flex-column gap-2">
                        <VBtn
                          variant="outlined"
                          size="small"
                          @click="$refs.avatarInput.click()"
                        >
                          Upload Avatar Image
                        </VBtn>
                        <VBtn
                          v-if="formData.avatarImage"
                          variant="tonal"
                          size="small"
                          color="error"
                          @click="formData.avatarImage = null"
                        >
                          <VIcon icon="tabler-trash" size="16" />
                        </VBtn>
                      </div>
                    </div>
                    <input
                      ref="avatarInput"
                      type="file"
                      accept="image/*"
                      style="display: none"
                      @change="handleAvatarImageUpload"
                    />
                    <p class="text-caption text-medium-emphasis mt-2">
                      Allowed JPG or PNG, Preferred 250*250 px, Max size 3Mb
                    </p>
                  </div>

                  <!-- Short Bio -->
                  <AppTextField
                    v-model="formData.shortBio"
                    label="Short Bio"
                    placeholder="Tell us who you are and what you do in a few sentences"
                    type="textarea"
                    rows="3"
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
                <VCol cols="12">
                  <h6 class="text-h6 font-weight-medium">Social Links</h6>
                  <p class="mb-0">Add Social Links</p>
                </VCol>
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="formData.twitter"
                    placeholder="https://twitter.com/abc"
                    label="Twitter"
                  />
                </VCol>
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="formData.facebook"
                    placeholder="https://facebook.com/abc"
                    label="Facebook"
                  />
                </VCol>
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="formData.googlePlus"
                    placeholder="https://plus.google.com/abc"
                    label="Google+"
                  />
                </VCol>
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="formData.linkedIn"
                    placeholder="https://linkedin.com/abc"
                    label="LinkedIn"
                  />
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
              @click="onSubmit"
            >
              submit
            </VBtn>
            <VBtn v-else @click="currentStep++">
              Next
              <VIcon icon="tabler-arrow-right" end class="flip-in-rtl" />
            </VBtn>
          </div>
        </VForm>
      </VCardText>
    </VCard>
  </div>
</template>
