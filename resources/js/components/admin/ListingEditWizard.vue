<template>
  <div class="listing-edit-wizard">
    <!-- Dynamic Wizard Selection based on Listing Type -->
    <div v-if="formData.listingType === 'single-date'">
      <SingleDateWizard
        :listing="props.listing"
        @updated="handleWizardUpdated"
        @close="$emit('close')"
      />
    </div>

    <div v-else-if="formData.listingType === 'multi-date'">
      <MultiDateWizard
        :listing="props.listing"
        @updated="handleWizardUpdated"
        @close="$emit('close')"
      />
    </div>

    <div v-else-if="formData.listingType === 'open-date'">
      <OpenDateWizard
        :listing="props.listing"
        @updated="handleWizardUpdated"
        @close="$emit('close')"
      />
    </div>

    <!-- Fallback for other types or when type is not set -->
    <div v-else>
      <VCard>
        <VCardTitle class="d-flex align-center justify-space-between">
          <span>Select Listing Type</span>
        </VCardTitle>
        <VCardText>
          <VSelect
            v-model="formData.listingType"
            label="Listing Type"
            :items="listingTypeOptions"
            variant="outlined"
            density="comfortable"
            @update:model-value="handleListingTypeChange"
          />
          <p class="text-caption mt-2">
            Please select the listing type to continue with editing.
          </p>
        </VCardText>
      </VCard>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import MultiDateWizard from "./wizards/MultiDateWizard.vue";
import OpenDateWizard from "./wizards/OpenDateWizard.vue";
import SingleDateWizard from "./wizards/SingleDateWizard.vue";

const props = defineProps({
  listing: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["close", "updated"]);

// Form data
const formData = ref({
  listingType: "",
});

// Options for select fields
const listingTypeOptions = [
  { title: "Single Date", value: "single-date" },
  { title: "Multi Date", value: "multi-date" },
  { title: "Open Date", value: "open-date" },
  { title: "Other", value: "other" },
];

// Initialize form data from props
onMounted(() => {
  if (props.listing) {
    formData.value.listingType = props.listing.listing_type || "single-date";
  }
});

// Handle listing type change
const handleListingTypeChange = (newType) => {
  formData.value.listingType = newType;
};

// Handle wizard updates
const handleWizardUpdated = (updatedData) => {
  emit("updated", updatedData);
};
</script>

<style lang="scss" scoped>
.listing-edit-wizard {
  .v-card {
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }
}
</style>
