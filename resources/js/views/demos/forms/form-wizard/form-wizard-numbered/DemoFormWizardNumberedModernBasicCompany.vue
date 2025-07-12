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

const onSubmit = async () => {
  loading.value = true;

  try {
    // Validate required fields
    if (!formData.value.termsAccepted) {
      alert("Please accept the terms and conditions");
      return;
    }

    const response = await companyUserService.register(formData.value);
    console.log("Registration successful:", response);

    // Show success message
    alert("Registration completed successfully!");

    // Redirect to timeline page with company ID
    router.push({
      name: "registration-timeline",
      params: {
        type: "company",
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
                <VCol cols="12">
                  <h6 class="text-h6 font-weight-medium">
                    Company Information
                  </h6>
                  <p class="mb-0">Enter your company details</p>
                </VCol>
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
                    placeholder="Select your country"
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
                    placeholder="Select your business type"
                    :items="[
                      'Sole Proprietorship',
                      'Partnership',
                      'Limited Liability Company (LLC)',
                      'Corporation',
                      'Non-Profit Organization',
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
                <VCol cols="12">
                  <h6 class="text-h6 font-weight-medium">Business Details</h6>
                  <p class="mb-0">Setup your business information</p>
                </VCol>
                <!-- Left column -->
                <VCol cols="12" md="6">
                  <!-- Company Logo -->
                  <div class="mb-6">
                    <h6 class="text-h6 font-weight-medium mb-2">
                      Company Logo
                    </h6>
                    <p class="text-body-2 text-medium-emphasis mb-4">
                      High quality image, shown as your company logo
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
                          border-radius: 8px;
                        "
                      >
                        <VIcon
                          v-if="!formData.passportImage"
                          icon="tabler-photo"
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
                          Upload Company Logo
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
                          width: 80px;
                          height: 80px;
                          border: 2px dashed #ccc;
                          display: flex;
                          align-items: center;
                          justify-content: center;
                          background-color: #f5f5f5;
                          border-radius: 8px;
                        "
                      >
                        <VIcon
                          v-if="!formData.avatarImage"
                          icon="tabler-file-document"
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
                            border-radius: 8px;
                          "
                        />
                      </div>
                      <div class="d-flex flex-column gap-2">
                        <VBtn
                          variant="outlined"
                          size="small"
                          @click="$refs.avatarInput.click()"
                        >
                          Upload Business License
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
                      accept="image/*,.pdf"
                      style="display: none"
                      @change="handleAvatarImageUpload"
                    />
                    <p class="text-caption text-medium-emphasis mt-2">
                      Allowed JPG, PNG or PDF, Max size 5Mb
                    </p>
                  </div>

                  <!-- Short Bio -->
                  <AppTextField
                    v-model="formData.shortBio"
                    label="Company Bio"
                    placeholder="Tell us about your company and what you do in a few sentences"
                    type="textarea"
                    rows="3"
                    class="mb-4"
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
                <VCol cols="12">
                  <h6 class="text-h6 font-weight-medium">Social Links</h6>
                  <p class="mb-0">
                    Add your company's social media and proof links
                  </p>
                </VCol>

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
