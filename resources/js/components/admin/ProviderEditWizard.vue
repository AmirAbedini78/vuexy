<template>
  <div class="provider-edit-wizard">
    <VCard>
      <VRow>
        <VCol
          cols="12"
          md="4"
          :class="$vuetify.display.smAndDown ? 'border-b' : 'border-e'"
        >
          <VCardText>
            <!-- 👉 Stepper -->
            <AppStepper
              v-model:current-step="currentStep"
              direction="vertical"
              :items="numberedSteps"
            />
          </VCardText>
        </VCol>
        <!-- 👉 stepper content -->
        <VCol cols="12" md="8">
          <VCardText>
            <VForm>
              <VWindow v-model="currentStep" class="disable-tab-transition">
                <!-- Step 1: Basic Information -->
                <VWindowItem>
                  <VRow>
                    <VCol cols="12">
                      <h6 class="text-h6 font-weight-medium">
                        {{ getStepTitle(0) }}
                      </h6>
                      <p class="mb-0">
                        {{ getStepDescription(0) }}
                      </p>
                    </VCol>

                    <!-- Individual Provider Fields -->
                    <template v-if="provider.provider_type === 'individual'">
                      <VCol cols="12" md="6">
                        <VTextField
                          v-model="formData.fullName"
                          label="Full Name"
                          placeholder="Enter full name"
                          variant="outlined"
                          density="comfortable"
                          :error-messages="formValidationErrors.fullName"
                          @blur="validateField('fullName')"
                        />
                      </VCol>

                      <VCol cols="12" md="6">
                        <VTextField
                          v-model="formData.nationality"
                          label="Nationality"
                          placeholder="Enter nationality"
                          variant="outlined"
                          density="comfortable"
                          :error-messages="formValidationErrors.nationality"
                          @blur="validateField('nationality')"
                        />
                      </VCol>

                      <VCol cols="12" md="6">
                        <VTextField
                          v-model="formData.address1"
                          label="Address Line 1"
                          placeholder="Enter address"
                          variant="outlined"
                          density="comfortable"
                          :error-messages="formValidationErrors.address1"
                          @blur="validateField('address1')"
                        />
                      </VCol>

                      <VCol cols="12" md="6">
                        <VTextField
                          v-model="formData.city"
                          label="City"
                          placeholder="Enter city"
                          variant="outlined"
                          density="comfortable"
                          :error-messages="formValidationErrors.city"
                          @blur="validateField('city')"
                        />
                      </VCol>

                      <VCol cols="12" md="6">
                        <VTextField
                          v-model="formData.state"
                          label="State/Province"
                          placeholder="Enter state"
                          variant="outlined"
                          density="comfortable"
                          :error-messages="formValidationErrors.state"
                          @blur="validateField('state')"
                        />
                      </VCol>

                      <VCol cols="12" md="6">
                        <VTextField
                          v-model="formData.postalCode"
                          label="Postal Code"
                          placeholder="Enter postal code"
                          variant="outlined"
                          density="comfortable"
                          :error-messages="formValidationErrors.postalCode"
                          @blur="validateField('postalCode')"
                        />
                      </VCol>

                      <VCol cols="12" md="6">
                        <VTextField
                          v-model="formData.country"
                          label="Country"
                          placeholder="Enter country"
                          variant="outlined"
                          density="comfortable"
                          :error-messages="formValidationErrors.country"
                          @blur="validateField('country')"
                        />
                      </VCol>

                      <VCol cols="12" md="6">
                        <VTextField
                          v-model="formData.dob"
                          label="Date of Birth"
                          type="date"
                          variant="outlined"
                          density="comfortable"
                          :error-messages="formValidationErrors.dob"
                          @blur="validateField('dob')"
                        />
                      </VCol>

                      <VCol cols="12">
                        <VTextField
                          v-model="formData.languages"
                          label="Languages (comma separated)"
                          placeholder="e.g., English, Spanish, French"
                          variant="outlined"
                          density="comfortable"
                          :error-messages="formValidationErrors.languages"
                          @blur="validateField('languages')"
                        />
                      </VCol>
                    </template>

                    <!-- Company Provider Fields -->
                    <template v-else>
                      <VCol cols="12" md="6">
                        <VTextField
                          v-model="formData.companyName"
                          label="Company Name"
                          placeholder="Enter company name"
                          variant="outlined"
                          density="comfortable"
                          :error-messages="formValidationErrors.companyName"
                          @blur="validateField('companyName')"
                        />
                      </VCol>

                      <VCol cols="12" md="6">
                        <VTextField
                          v-model="formData.vatId"
                          label="VAT ID"
                          placeholder="Enter VAT ID"
                          variant="outlined"
                          density="comfortable"
                          :error-messages="formValidationErrors.vatId"
                          @blur="validateField('vatId')"
                        />
                      </VCol>

                      <VCol cols="12" md="6">
                        <VTextField
                          v-model="formData.address1"
                          label="Address Line 1"
                          placeholder="Enter address"
                          variant="outlined"
                          density="comfortable"
                          :error-messages="formValidationErrors.address1"
                          @blur="validateField('address1')"
                        />
                      </VCol>

                      <VCol cols="12" md="6">
                        <VTextField
                          v-model="formData.city"
                          label="City"
                          placeholder="Enter city"
                          variant="outlined"
                          density="comfortable"
                          :error-messages="formValidationErrors.city"
                          @blur="validateField('city')"
                        />
                      </VCol>

                      <VCol cols="12" md="6">
                        <VTextField
                          v-model="formData.state"
                          label="State/Province"
                          placeholder="Enter state"
                          variant="outlined"
                          density="comfortable"
                          :error-messages="formValidationErrors.state"
                          @blur="validateField('state')"
                        />
                      </VCol>

                      <VCol cols="12" md="6">
                        <VTextField
                          v-model="formData.contactPerson"
                          label="Contact Person"
                          placeholder="Enter contact person"
                          variant="outlined"
                          density="comfortable"
                          :error-messages="formValidationErrors.contactPerson"
                          @blur="validateField('contactPerson')"
                        />
                      </VCol>

                      <VCol cols="12" md="6">
                        <VTextField
                          v-model="formData.countryOfRegistration"
                          label="Country of Registration"
                          placeholder="Enter country of registration"
                          variant="outlined"
                          density="comfortable"
                          :error-messages="
                            formValidationErrors.countryOfRegistration
                          "
                          @blur="validateField('countryOfRegistration')"
                        />
                      </VCol>

                      <VCol cols="12" md="6">
                        <VTextField
                          v-model="formData.businessType"
                          label="Business Type"
                          placeholder="Enter business type"
                          variant="outlined"
                          density="comfortable"
                          :error-messages="formValidationErrors.businessType"
                          @blur="validateField('businessType')"
                        />
                      </VCol>
                    </template>
                  </VRow>
                </VWindowItem>

                <!-- Step 2: Business Details -->
                <VWindowItem>
                  <VRow>
                    <VCol cols="12">
                      <h6 class="text-h6 font-weight-medium">
                        {{ getStepTitle(1) }}
                      </h6>
                      <p class="mb-0">
                        {{ getStepDescription(1) }}
                      </p>
                    </VCol>

                    <VCol cols="12" md="6">
                      <VTextField
                        v-model="formData.activitySpecialization"
                        label="Activity Specialization"
                        placeholder="Enter specialization"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="
                          formValidationErrors.activitySpecialization
                        "
                        @blur="validateField('activitySpecialization')"
                      />
                    </VCol>

                    <VCol cols="12" md="6">
                      <VSelect
                        v-model="formData.wantToBeListed"
                        label="Want to be Listed"
                        :items="['yes', 'no', 'unsure']"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="formValidationErrors.wantToBeListed"
                        @blur="validateField('wantToBeListed')"
                      />
                    </VCol>

                    <VCol cols="12">
                      <VTextarea
                        v-model="formData.shortBio"
                        label="Short Bio"
                        placeholder="Enter short bio"
                        variant="outlined"
                        density="comfortable"
                        rows="4"
                        :error-messages="formValidationErrors.shortBio"
                        @blur="validateField('shortBio')"
                      />
                    </VCol>

                    <!-- Individual specific fields -->
                    <template v-if="provider.provider_type === 'individual'">
                      <VCol cols="12" md="6">
                        <VTextField
                          v-model="formData.yearsOfExperience"
                          label="Years of Experience"
                          placeholder="Enter years of experience"
                          variant="outlined"
                          density="comfortable"
                          :error-messages="
                            formValidationErrors.yearsOfExperience
                          "
                          @blur="validateField('yearsOfExperience')"
                        />
                      </VCol>

                      <VCol cols="12" md="6">
                        <VTextField
                          v-model="formData.countryOfOperation"
                          label="Country of Operation"
                          placeholder="Enter country of operation"
                          variant="outlined"
                          density="comfortable"
                          :error-messages="
                            formValidationErrors.countryOfOperation
                          "
                          @blur="validateField('countryOfOperation')"
                        />
                      </VCol>

                      <VCol cols="12" md="6">
                        <VTextField
                          v-model="formData.emergencyContactName"
                          label="Emergency Contact Name"
                          placeholder="Enter emergency contact name"
                          variant="outlined"
                          density="comfortable"
                          :error-messages="
                            formValidationErrors.emergencyContactName
                          "
                          @blur="validateField('emergencyContactName')"
                        />
                      </VCol>

                      <VCol cols="12" md="6">
                        <VTextField
                          v-model="formData.emergencyContactPhone"
                          label="Emergency Contact Phone"
                          placeholder="Enter emergency contact phone"
                          variant="outlined"
                          density="comfortable"
                          :error-messages="
                            formValidationErrors.emergencyContactPhone
                          "
                          @blur="validateField('emergencyContactPhone')"
                        />
                      </VCol>
                    </template>

                    <!-- Company specific fields -->
                    <template v-else>
                      <VCol cols="12" md="6">
                        <VTextField
                          v-model="formData.companyWebsite"
                          label="Company Website"
                          placeholder="Enter company website"
                          variant="outlined"
                          density="comfortable"
                          :error-messages="formValidationErrors.companyWebsite"
                          @blur="validateField('companyWebsite')"
                        />
                      </VCol>
                    </template>
                  </VRow>
                </VWindowItem>

                <!-- Step 3: Social Links -->
                <VWindowItem>
                  <VRow>
                    <VCol cols="12">
                      <h6 class="text-h6 font-weight-medium">
                        {{ getStepTitle(2) }}
                      </h6>
                      <p class="mb-0">
                        {{ getStepDescription(2) }}
                      </p>
                    </VCol>

                    <VCol cols="12" md="6">
                      <VTextField
                        v-model="formData.twitter"
                        label="Twitter"
                        placeholder="https://twitter.com/username"
                        variant="outlined"
                        density="comfortable"
                      />
                    </VCol>

                    <VCol cols="12" md="6">
                      <VTextField
                        v-model="formData.facebook"
                        label="Facebook"
                        placeholder="https://facebook.com/username"
                        variant="outlined"
                        density="comfortable"
                      />
                    </VCol>

                    <VCol cols="12" md="6">
                      <VTextField
                        v-model="formData.linkedIn"
                        label="LinkedIn"
                        placeholder="https://linkedin.com/in/username"
                        variant="outlined"
                        density="comfortable"
                      />
                    </VCol>

                    <VCol cols="12" md="6">
                      <VTextField
                        v-model="formData.instagram"
                        label="Instagram"
                        placeholder="https://instagram.com/username"
                        variant="outlined"
                        density="comfortable"
                      />
                    </VCol>

                    <VCol cols="12">
                      <VCheckbox
                        v-model="formData.termsAccepted"
                        label="I accept the terms and conditions"
                        color="primary"
                        :error-messages="formValidationErrors.termsAccepted"
                        @change="validateField('termsAccepted')"
                      />
                    </VCol>
                  </VRow>
                </VWindowItem>
              </VWindow>

              <!-- Navigation Buttons -->
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
                  @click="handleSubmit"
                  :loading="loading"
                >
                  Update Provider
                </VBtn>

                <VBtn v-else @click="handleNext">
                  Next
                  <VIcon icon="tabler-arrow-right" end class="flip-in-rtl" />
                </VBtn>
              </div>
            </VForm>
          </VCardText>
        </VCol>
      </VRow>
    </VCard>
  </div>
</template>

<script setup>
import { $api } from "@/utils/api";
import { computed, onMounted, ref } from "vue";

const props = defineProps({
  provider: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["close", "updated"]);

const currentStep = ref(0);
const loading = ref(false);
const formValidationErrors = ref({});

// Define steps based on provider type
const numberedSteps = computed(() => {
  if (props.provider.provider_type === "individual") {
    return [
      {
        title: "Personal Information",
        subtitle: "Update your personal information",
      },
      {
        title: "Account Details",
        subtitle: "Update your account and business details",
      },
      {
        title: "Social Links",
        subtitle: "Update your social media links",
      },
    ];
  } else {
    return [
      {
        title: "Company Information",
        subtitle: "Update your company information",
      },
      {
        title: "Business Details",
        subtitle: "Update your business details",
      },
      {
        title: "Social Links",
        subtitle: "Update your social media links",
      },
    ];
  }
});

// Initialize form data based on provider type
const formData = ref({});

onMounted(() => {
  initializeFormData();
});

const initializeFormData = () => {
  if (props.provider.provider_type === "individual") {
    formData.value = {
      // Step 1: Personal Information
      fullName: props.provider.provider_name || "",
      nationality: props.provider.nationality || "",
      address1: props.provider.address1 || "",
      city: props.provider.city || "",
      state: props.provider.state || "",
      dob: props.provider.dob || "",
      languages: Array.isArray(props.provider.languages)
        ? props.provider.languages.join(", ")
        : props.provider.languages || "",
      address2: props.provider.address2 || "",
      postalCode: props.provider.postal_code || "",
      country: props.provider.country || "",

      // Step 2: Account Details
      activitySpecialization: props.provider.activity_specialization || "",
      yearsOfExperience: props.provider.years_of_experience || "",
      emergencyContactName: props.provider.emergency_contact_name || "",
      wantToBeListed: props.provider.want_to_be_listed || "",
      shortBio: props.provider.short_bio || "",
      countryOfOperation: props.provider.country_of_operation || "",
      emergencyContactPhone: props.provider.emergency_contact_phone || "",

      // Step 3: Social Links
      twitter: "",
      facebook: "",
      linkedIn: "",
      instagram: "",
      socialMediaLinks: [],
      socialProofLinks: [],
      termsAccepted: props.provider.terms_accepted || false,
    };
  } else {
    formData.value = {
      // Step 1: Company Information
      companyName: props.provider.provider_name || "",
      vatId: props.provider.vat_id || "",
      address1: props.provider.address1 || "",
      city: props.provider.city || "",
      state: props.provider.state || "",
      contactPerson: props.provider.contact_person || "",
      countryOfRegistration: props.provider.country_of_registration || "",
      address2: props.provider.address2 || "",
      postalCode: props.provider.postal_code || "",
      country: props.provider.country || "",
      businessType: props.provider.business_type || "",

      // Step 2: Business Details
      activitySpecialization: props.provider.activity_specialization || "",
      wantToBeListed: props.provider.want_to_be_listed || "",
      shortBio: props.provider.short_bio || "",
      companyWebsite: props.provider.company_website || "",

      // Step 3: Social Links
      twitter: "",
      facebook: "",
      linkedIn: "",
      instagram: "",
      socialMediaLinks: [],
      socialProofLinks: [],
      termsAccepted: props.provider.terms_accepted || false,
    };
  }
};

const getStepTitle = (stepIndex) => {
  return numberedSteps.value[stepIndex]?.title || "";
};

const getStepDescription = (stepIndex) => {
  return numberedSteps.value[stepIndex]?.subtitle || "";
};

const validateField = (fieldName) => {
  const value = formData.value[fieldName];
  let error = "";

  if (!value || (typeof value === "string" && value.trim() === "")) {
    error = "This field is required";
  }

  formValidationErrors.value[fieldName] = error;
  return !error;
};

const validateStep = (stepIndex) => {
  const errors = {};

  // Only validate the final step (terms and conditions)
  if (stepIndex === 2) {
    // Validate Step 3 fields - only terms acceptance is required
    if (!formData.value.termsAccepted) {
      errors.termsAccepted = "You must accept the terms and conditions";
    }
  }

  formValidationErrors.value = errors;
  return Object.keys(errors).length === 0;
};

const handleNext = () => {
  if (validateStep(currentStep.value)) {
    currentStep.value++;
  }
};

const handleSubmit = async () => {
  if (!validateStep(currentStep.value)) {
    return;
  }

  loading.value = true;
  try {
    // Prepare data for API
    const updateData = prepareUpdateData();

    console.log("Updating provider with data:", updateData);
    console.log("Provider ID:", props.provider.id);
    console.log("Provider Type:", props.provider.provider_type);

    // Call API to update provider
    const response = await $api(
      `/admin/providers/${props.provider.id}/${props.provider.provider_type}`,
      {
        method: "PUT",
        body: updateData,
      }
    );

    console.log("API Response:", response);

    if (response.success) {
      console.log("Provider updated successfully");
      // Emit updated provider data
      emit("updated", {
        ...props.provider,
        ...updateData,
      });

      // Close the dialog
      emit("close");
    } else {
      console.error("Failed to update provider:", response.message);
      // You can show an error message here
    }
  } catch (error) {
    console.error("Error updating provider:", error);
    console.error("Error details:", error.response || error);
    // You can show an error message here
  } finally {
    loading.value = false;
  }
};

const prepareUpdateData = () => {
  const data = {};

  if (props.provider.provider_type === "individual") {
    data.full_name = formData.value.fullName;
    data.nationality = formData.value.nationality;
    data.address1 = formData.value.address1;
    data.city = formData.value.city;
    data.state = formData.value.state;
    data.dob = formData.value.dob;
    data.languages =
      typeof formData.value.languages === "string"
        ? formData.value.languages
            .split(",")
            .map((lang) => lang.trim())
            .filter((lang) => lang)
        : formData.value.languages;
    data.postal_code = formData.value.postalCode;
    data.country = formData.value.country;
    data.activity_specialization = formData.value.activitySpecialization;
    data.years_of_experience = formData.value.yearsOfExperience;
    data.emergency_contact_name = formData.value.emergencyContactName;
    data.want_to_be_listed = formData.value.wantToBeListed;
    data.short_bio = formData.value.shortBio;
    data.country_of_operation = formData.value.countryOfOperation;
    data.emergency_contact_phone = formData.value.emergencyContactPhone;
    data.terms_accepted = formData.value.termsAccepted;
  } else {
    data.company_name = formData.value.companyName;
    data.vat_id = formData.value.vatId;
    data.address1 = formData.value.address1;
    data.city = formData.value.city;
    data.state = formData.value.state;
    data.contact_person = formData.value.contactPerson;
    data.country_of_registration = formData.value.countryOfRegistration;
    data.postal_code = formData.value.postalCode;
    data.country = formData.value.country;
    data.business_type = formData.value.businessType;
    data.activity_specialization = formData.value.activitySpecialization;
    data.want_to_be_listed = formData.value.wantToBeListed;
    data.short_bio = formData.value.shortBio;
    data.company_website = formData.value.companyWebsite;
    data.terms_accepted = formData.value.termsAccepted;
  }

  return data;
};
</script>

<style scoped>
.provider-edit-wizard {
  max-width: 100%;
}

/* Border styles for responsive layout */
.border-b {
  border-bottom: 1px solid #e0e0e0;
  padding-bottom: 1rem;
}

.border-e {
  border-right: 1px solid #e0e0e0;
}

/* Form Styles */
.v-text-field,
.v-select,
.v-textarea,
.v-checkbox {
  margin-bottom: 1rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .border-e {
    border-right: none;
    border-bottom: 1px solid #e0e0e0;
    padding-bottom: 1rem;
  }
}
</style>
