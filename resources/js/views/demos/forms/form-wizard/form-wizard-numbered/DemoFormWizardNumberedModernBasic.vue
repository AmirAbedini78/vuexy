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
  // Step 2 & 3 fields (if needed, keep or remove as you wish)
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
                <VCol cols="12">
                  <h6 class="text-h6 font-weight-medium">
                    Personal Information
                  </h6>
                  <p class="mb-0">Enter your personal details</p>
                </VCol>
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
            <!-- Step 2: (You can customize this as needed) -->
            <VWindowItem>
              <VRow>
                <VCol cols="12">
                  <h6 class="text-h6 font-weight-medium">Personal Info</h6>
                  <p class="mb-0">Setup Information</p>
                </VCol>
                <!-- Example fields for step 2 -->
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="formData.firstName"
                    label="First Name"
                    placeholder="Leonard"
                  />
                </VCol>
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="formData.lastName"
                    label="Last Name"
                    placeholder="Carter"
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
