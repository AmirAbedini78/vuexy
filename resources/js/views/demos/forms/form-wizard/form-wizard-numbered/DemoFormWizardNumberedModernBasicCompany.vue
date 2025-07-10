<script setup>
import { ref } from "vue";

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
  <!-- Stepper -->
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
            <!-- Step 1: Company Information -->
            <VWindowItem>
              <VRow>
                <!-- Left column -->
                <VCol cols="12" md="6">
                  <!-- Company Name -->
                  <AppTextField
                    v-model="formData.companyName"
                    label="Company Name*"
                    placeholder="Legal name of your company"
                    class="mb-4"
                  />

                  <!-- VAT ID -->
                  <AppTextField
                    v-model="formData.vatId"
                    label="VAT ID"
                    placeholder="Provide your VAT number for invoicing"
                    class="mb-6"
                  />

                  <!-- Company Address Section -->
                  <div class="mb-4">
                    <h6 class="text-h6 font-weight-medium mb-2">
                      Company Address*
                    </h6>
                    <p class="text-body-2 text-medium-emphasis mb-4">
                      Provide your company address for invoicing
                    </p>
                  </div>

                  <!-- Address Line 1 -->
                  <AppTextField
                    v-model="formData.address1"
                    label="Address Line 1*"
                    placeholder="Address Line 1"
                    class="mb-4"
                  />

                  <!-- City -->
                  <AppTextField
                    v-model="formData.city"
                    label="City"
                    placeholder="Munich"
                    class="mb-4"
                  />

                  <!-- State / Province -->
                  <AppTextField
                    v-model="formData.state"
                    label="State / Province"
                    placeholder="Bavaria"
                    class="mb-4"
                  />

                  <!-- Contact Person -->
                  <AppTextField
                    v-model="formData.contactPerson"
                    label="Contact Person"
                    placeholder="Let us know who we can get in touch with"
                    class="mb-4"
                  />
                </VCol>
                <!-- Right column -->
                <VCol cols="12" md="6">
                  <!-- Country of Registration (outside address section) -->
                  <AppSelect
                    v-model="formData.countryOfRegistration"
                    label="Country of Registration*"
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
                    class="mb-6"
                  />

                  <!-- Address Line 2 -->
                  <AppTextField
                    v-model="formData.address2"
                    label="Address Line 2 (optional)"
                    placeholder="Address Line 2"
                    class="mb-4"
                  />

                  <!-- Postal Code -->
                  <AppTextField
                    v-model="formData.postalCode"
                    label="Postal Code"
                    placeholder="231465"
                    class="mb-4"
                  />

                  <!-- Country -->
                  <AppSelect
                    v-model="formData.country"
                    label="Country*"
                    placeholder="Germany"
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

                  <!-- Business Type -->
                  <AppSelect
                    v-model="formData.businessType"
                    label="Business Type*"
                    placeholder="e.g. Tour Operator, Activity Center, Gear Rental, etc..."
                    :items="[
                      'Tour Operator',
                      'Activity Center',
                      'Gear Rental',
                      'Adventure Guide',
                      'Travel Agency',
                      'Outdoor Equipment',
                      'Training Center',
                      'Other',
                    ]"
                    class="mb-4"
                  />
                </VCol>
              </VRow>
            </VWindowItem>
            <!-- Step 2: Business Details -->
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
                    multiple
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
                </VCol>
              </VRow>
            </VWindowItem>
            <!-- Step 3: Social Links -->
            <VWindowItem>
              <VRow>
                <VCol cols="12">
                  <h6 class="text-h6 font-weight-medium">Social Links</h6>
                  <p class="mb-0">Add your company's social media links</p>
                </VCol>
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="formData.twitter"
                    placeholder="https://twitter.com/yourcompany"
                    label="Twitter"
                  />
                </VCol>
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="formData.facebook"
                    placeholder="https://facebook.com/yourcompany"
                    label="Facebook"
                  />
                </VCol>
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="formData.linkedIn"
                    placeholder="https://linkedin.com/company/yourcompany"
                    label="LinkedIn"
                  />
                </VCol>
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="formData.instagram"
                    placeholder="https://instagram.com/yourcompany"
                    label="Instagram"
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
