<template>
  <VDialog v-model="isDialogVisible" max-width="800" persistent>
    <VCard>
      <VCardTitle class="d-flex align-center justify-space-between">
        <span>Provider Details</span>
        <VBtn icon variant="text" size="small" @click="closeDialog">
          <VIcon icon="tabler-x" />
        </VBtn>
      </VCardTitle>

      <VCardText v-if="provider">
        <VRow>
          <!-- Basic Information -->
          <VCol cols="12">
            <h6 class="text-h6 font-weight-medium mb-3">Basic Information</h6>
          </VCol>

          <VCol cols="12" md="6">
            <strong>Name:</strong> {{ provider.provider_name }}
          </VCol>

          <VCol cols="12" md="6">
            <strong>Type:</strong>
            <VChip
              :color="
                provider.provider_type === 'individual'
                  ? 'primary'
                  : 'secondary'
              "
              size="small"
              class="ml-2"
            >
              {{
                provider.provider_type === "individual"
                  ? "Individual"
                  : "Company"
              }}
            </VChip>
          </VCol>

          <VCol cols="12" md="6">
            <strong>Specialization:</strong>
            {{ provider.activity_specialization || "N/A" }}
          </VCol>

          <VCol cols="12" md="6">
            <strong>Country:</strong>
            {{ provider.country_of_operation || provider.country || "N/A" }}
          </VCol>

          <VCol cols="12" md="6">
            <strong>Experience:</strong>
            {{
              provider.years_of_experience || provider.business_type || "N/A"
            }}
          </VCol>

          <VCol cols="12" md="6">
            <strong>Status:</strong>
            <VChip
              :color="getStatusColor(provider.status)"
              size="small"
              class="ml-2"
            >
              {{ provider.status }}
            </VChip>
          </VCol>

          <VCol cols="12" md="6">
            <strong>Created:</strong>
            {{ formatDate(provider.created_at) }}
          </VCol>

          <VCol cols="12" md="6">
            <strong>Total Listings:</strong>
            {{ provider.total_listings || 0 }}
          </VCol>

          <VCol cols="12" md="6">
            <strong>Total Bookings:</strong>
            {{ provider.total_bookings || 0 }}
          </VCol>

          <!-- Additional fields for individual users -->
          <template v-if="provider.provider_type === 'individual'">
            <VCol cols="12">
              <h6 class="text-h6 font-weight-medium mb-3 mt-4">
                Personal Information
              </h6>
            </VCol>

            <VCol cols="12" md="6">
              <strong>Nationality:</strong>
              {{ provider.nationality || "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>Address:</strong>
              {{ provider.address1 || "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>City:</strong>
              {{ provider.city || "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>State:</strong>
              {{ provider.state || "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>Postal Code:</strong>
              {{ provider.postal_code || "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>Emergency Contact:</strong>
              {{ provider.emergency_contact_name || "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>Emergency Phone:</strong>
              {{ provider.emergency_contact_phone || "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>Date of Birth:</strong>
              {{ provider.dob ? formatDate(provider.dob) : "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>Languages:</strong>
              {{
                Array.isArray(provider.languages)
                  ? provider.languages.join(", ")
                  : "N/A"
              }}
            </VCol>

            <VCol cols="12">
              <strong>Short Bio:</strong>
              <p class="mt-1">{{ provider.short_bio || "N/A" }}</p>
            </VCol>
          </template>

          <!-- Additional fields for company users -->
          <template v-if="provider.provider_type === 'company'">
            <VCol cols="12">
              <h6 class="text-h6 font-weight-medium mb-3 mt-4">
                Company Information
              </h6>
            </VCol>

            <VCol cols="12" md="6">
              <strong>VAT ID:</strong>
              {{ provider.vat_id || "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>Contact Person:</strong>
              {{ provider.contact_person || "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>Country of Registration:</strong>
              {{ provider.country_of_registration || "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>Business Type:</strong>
              {{ provider.business_type || "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>Company Website:</strong>
              <a
                v-if="provider.company_website"
                :href="provider.company_website"
                target="_blank"
                class="text-primary"
              >
                {{ provider.company_website }}
              </a>
              <span v-else>N/A</span>
            </VCol>

            <VCol cols="12">
              <strong>Short Bio:</strong>
              <p class="mt-1">{{ provider.short_bio || "N/A" }}</p>
            </VCol>
          </template>
        </VRow>
      </VCardText>

      <VCardActions>
        <VSpacer />
        <VBtn color="primary" @click="closeDialog"> Close </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

<script setup>
import { ref, watch } from "vue";

const props = defineProps({
  isVisible: {
    type: Boolean,
    default: false,
  },
  provider: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(["update:isVisible"]);

const isDialogVisible = ref(false);

watch(
  () => props.isVisible,
  (newVal) => {
    isDialogVisible.value = newVal;
  }
);

watch(isDialogVisible, (newVal) => {
  emit("update:isVisible", newVal);
});

const closeDialog = () => {
  isDialogVisible.value = false;
};

const getStatusColor = (status) => {
  switch (status?.toLowerCase()) {
    case "live":
      return "success";
    case "denied":
      return "error";
    default:
      return "default";
  }
};

const formatDate = (date) => {
  if (!date) return "-";
  return new Date(date).toLocaleDateString();
};
</script>

<style lang="scss" scoped>
.info-section {
  background-color: #f8f9fa;
  border-radius: 8px;
  padding: 20px;
  border: 1px solid #e9ecef;

  h6 {
    color: #2c3e50;
    margin-bottom: 16px;
  }
}

.info-item {
  display: flex;
  align-items: center;
  margin-bottom: 12px;
  padding: 8px 0;
  border-bottom: 1px solid #e9ecef;

  &:last-child {
    border-bottom: none;
    margin-bottom: 0;
  }

  .info-label {
    font-weight: 600;
    color: #495057;
    min-width: 140px;
    margin-right: 16px;
  }

  .info-value {
    color: #2c3e50;
    flex: 1;
  }
}

.v-chip {
  text-transform: none;
  font-weight: 500;
}
</style>
