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
  companyType: "",
  registrationNumber: "",
  taxId: "",
  foundedYear: "",
  employeeCount: "",
  website: "",
  industry: "",
  // Step 2 fields - Business Details
  companyLogo: null,
  businessLicense: null,
  servicesOffered: "",
  operatingRegions: "",
  emergencyContactName: "",
  wantToBeListed: "",
  companyDescription: "",
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
const handleCompanyLogoUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      formData.value.companyLogo = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const handleBusinessLicenseUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      formData.value.businessLicense = e.target.result;
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
                <VCol cols="12">
                  <h6 class="text-h6 font-weight-medium">
                    Company Information
                  </h6>
                  <p class="mb-0">Enter your company details</p>
                </VCol>
                <!-- Left column -->
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="formData.companyName"
                    label="Company Name"
                    placeholder="Enter your company name"
                  />
                  <AppSelect
                    v-model="formData.companyType"
                    label="Company Type"
                    placeholder="Select company type"
                    :items="[
                      'Corporation',
                      'LLC',
                      'Partnership',
                      'Sole Proprietorship',
                      'Non-Profit',
                      'Other',
                    ]"
                    class="mt-4"
                  />
                  <AppTextField
                    v-model="formData.registrationNumber"
                    label="Registration Number"
                    placeholder="Enter business registration number"
                    class="mt-4"
                  />
                  <AppTextField
                    v-model="formData.taxId"
                    label="Tax ID"
                    placeholder="Enter tax identification number"
                    class="mt-4"
                  />
                </VCol>
                <!-- Right column -->
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="formData.foundedYear"
                    label="Founded Year"
                    placeholder="YYYY"
                    type="number"
                  />
                  <AppSelect
                    v-model="formData.employeeCount"
                    label="Number of Employees"
                    placeholder="Select employee count"
                    :items="[
                      '1-10',
                      '11-50',
                      '51-100',
                      '101-500',
                      '501-1000',
                      '1000+',
                    ]"
                    class="mt-4"
                  />
                  <AppTextField
                    v-model="formData.website"
                    label="Company Website"
                    placeholder="https://www.yourcompany.com"
                    class="mt-4"
                  />
                  <AppSelect
                    v-model="formData.industry"
                    label="Industry"
                    placeholder="Select your industry"
                    :items="[
                      'Tourism',
                      'Adventure Sports',
                      'Travel',
                      'Hospitality',
                      'Technology',
                      'Other',
                    ]"
                    class="mt-4"
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
                      High quality logo, shown as your company profile image
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
                          v-if="!formData.companyLogo"
                          icon="tabler-building"
                          size="40"
                          color="grey"
                        />
                        <img
                          v-else
                          :src="formData.companyLogo"
                          style="width: 100%; height: 100%; object-fit: cover"
                        />
                      </div>
                      <div class="d-flex flex-column gap-2">
                        <VBtn
                          variant="outlined"
                          size="small"
                          @click="$refs.logoInput.click()"
                        >
                          Upload Company Logo
                        </VBtn>
                        <VBtn
                          v-if="formData.companyLogo"
                          variant="tonal"
                          size="small"
                          color="error"
                          @click="formData.companyLogo = null"
                        >
                          <VIcon icon="tabler-trash" size="16" />
                        </VBtn>
                      </div>
                    </div>
                    <input
                      ref="logoInput"
                      type="file"
                      accept="image/*"
                      style="display: none"
                      @change="handleCompanyLogoUpload"
                    />
                    <p class="text-caption text-medium-emphasis mt-2">
                      Allowed JPG or PNG, Preferred 520*430 px, Max Size 5Mb
                    </p>
                  </div>

                  <!-- Services Offered -->
                  <AppSelect
                    v-model="formData.servicesOffered"
                    label="Services Offered"
                    placeholder="Select services your company provides"
                    :items="[
                      'Adventure Tours',
                      'Guided Hiking',
                      'Diving Trips',
                      'Rock Climbing',
                      'Skiing Tours',
                      'Cultural Tours',
                      'Photography Tours',
                      'Corporate Events',
                      'Other',
                    ]"
                    multiple
                    class="mb-4"
                  />

                  <!-- Operating Regions -->
                  <AppSelect
                    v-model="formData.operatingRegions"
                    label="Operating Regions"
                    placeholder="Select regions where you operate"
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
                    multiple
                    class="mb-4"
                  />

                  <!-- Emergency Contact Name -->
                  <AppTextField
                    v-model="formData.emergencyContactName"
                    label="Emergency Contact Name"
                    placeholder="Primary contact for emergencies"
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
                      Upload your business license or permit
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
                        "
                      >
                        <VIcon
                          v-if="!formData.businessLicense"
                          icon="tabler-file-text"
                          size="30"
                          color="grey"
                        />
                        <img
                          v-else
                          :src="formData.businessLicense"
                          style="width: 100%; height: 100%; object-fit: cover"
                        />
                      </div>
                      <div class="d-flex flex-column gap-2">
                        <VBtn
                          variant="outlined"
                          size="small"
                          @click="$refs.licenseInput.click()"
                        >
                          Upload Business License
                        </VBtn>
                        <VBtn
                          v-if="formData.businessLicense"
                          variant="tonal"
                          size="small"
                          color="error"
                          @click="formData.businessLicense = null"
                        >
                          <VIcon icon="tabler-trash" size="16" />
                        </VBtn>
                      </div>
                    </div>
                    <input
                      ref="licenseInput"
                      type="file"
                      accept="image/*,.pdf"
                      style="display: none"
                      @change="handleBusinessLicenseUpload"
                    />
                    <p class="text-caption text-medium-emphasis mt-2">
                      Allowed JPG, PNG or PDF, Max Size 5Mb
                    </p>
                  </div>

                  <!-- Company Description -->
                  <AppTextField
                    v-model="formData.companyDescription"
                    label="Company Description"
                    placeholder="Tell us about your company and what you do"
                    type="textarea"
                    rows="3"
                    class="mb-4"
                  />

                  <!-- Certifications -->
                  <AppTextField
                    v-model="formData.certifications"
                    label="Business Certifications"
                    placeholder="Upload relevant business certifications"
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
