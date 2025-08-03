<template>
  <VDialog
    v-model="isOpen"
    max-width="1200px"
    class="package-modal"
    @click:outside="closeDialog"
    @keydown.esc="closeDialog"
    @update:model-value="handleModelValueUpdate"
  >
    <VCard class="package-card">
      <!-- Header -->
      <div class="modal-header">
        <div class="header-content">
          <div class="header-text">
            <h1 class="modal-title">Add your Package details</h1>
            <p class="modal-subtitle">
              On your Listing Page, packages will be listed as well
            </p>
          </div>
          <VBtn icon variant="text" @click="closeDialog" class="close-btn">
            <VIcon icon="tabler-x" size="24" />
          </VBtn>
        </div>
      </div>

      <div class="modal-content">
        <!-- Left Sidebar -->
        <div class="sidebar">
          <div class="sidebar-content">
            <div
              v-for="(pkg, index) in localPackages"
              :key="index"
              class="sidebar-item"
              :class="{ active: selectedPackageIndex === index }"
              @click="selectPackage(index)"
            >
              <div class="package-badge">
                <span class="badge-number">{{
                  String(index + 1).padStart(2, "0")
                }}</span>
              </div>
              <div class="package-info">
                <div class="package-title-row">
                  <span class="package-title">{{
                    pkg.title || `Package ${index + 1} title`
                  }}</span>
                  <VIcon
                    :icon="
                      selectedPackageIndex === index
                        ? 'tabler-chevron-down'
                        : 'tabler-chevron-right'
                    "
                    size="16"
                    class="chevron"
                  />
                </div>
                <div class="package-description">
                  <VIcon icon="tabler-target" size="16" class="package-icon" />
                  <span class="description-text">{{
                    pkg.description || "Package description"
                  }}</span>
                </div>
                <div class="package-price">
                  € {{ (pkg.price || 0).toFixed(2) }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
          <div
            v-for="(pkg, index) in localPackages"
            :key="index"
            class="package-section"
            :class="{ active: selectedPackageIndex === index }"
          >
            <div class="content-header">
              <h2 class="content-title">
                Package {{ String(index + 1).padStart(2, "0") }}
              </h2>
            </div>

            <div class="form-content">
              <div class="form-field">
                <label class="field-label">Package Title</label>
                <VTextField
                  v-model="pkg.title"
                  placeholder="Enter package title"
                  variant="outlined"
                  density="comfortable"
                  class="title-input"
                  hide-details
                  bg-color="white"
                />
              </div>

              <div class="form-field description-field">
                <label class="field-label">Package Description</label>
                <VTextarea
                  v-model="pkg.description"
                  placeholder="Enter package description"
                  variant="outlined"
                  density="comfortable"
                  rows="4"
                  class="description-input"
                  hide-details
                  bg-color="white"
                />
              </div>

              <div class="form-field price-field">
                <label class="field-label">Package Price</label>
                <div class="price-input-container">
                  <VTextField
                    v-model.number="pkg.price"
                    placeholder="0.00"
                    variant="outlined"
                    density="comfortable"
                    type="number"
                    min="0"
                    step="0.01"
                    class="price-input"
                    hide-details
                    bg-color="white"
                  />
                  <div class="currency-symbol">€</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Same Price Checkbox -->
          <div class="same-price-section">
            <VCheckbox
              v-model="samePriceForAll"
              label="Same price for every Package"
              color="primary"
              hide-details
              class="same-price-checkbox"
              @click.stop
            />
          </div>
        </div>
      </div>

      <!-- Footer Actions -->
      <div class="modal-footer">
        <VBtn
          variant="elevated"
          @click.stop="handleDone"
          :loading="loading"
          class="done-btn"
        >
          Done
        </VBtn>
        <VBtn variant="elevated" @click="addNewPackage" class="add-more-btn">
          Add More Packages
        </VBtn>
      </div>
    </VCard>
  </VDialog>
</template>

<script setup>
import { computed, ref, watch } from "vue";

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  initialPackages: {
    type: Array,
    default: () => [],
  },
  editingIndex: {
    type: Number,
    default: -1,
  },
});

const emit = defineEmits(["update:modelValue", "close", "done"]);

const loading = ref(false);
const selectedPackageIndex = ref(0);
const samePriceForAll = ref(false);

// Local state for packages
const localPackages = ref([]);

// Computed property for dialog visibility
const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => {
    if (!value) {
      // Reset form state when closing
      samePriceForAll.value = false;
    }
    emit("update:modelValue", value);
  },
});

// Initialize local packages when dialog opens
watch(
  () => props.modelValue,
  (newValue) => {
    if (newValue) {
      // When dialog opens, initialize with existing data or default
      if (props.initialPackages && props.initialPackages.length > 0) {
        // If we have existing packages, show them in the modal
        localPackages.value = JSON.parse(JSON.stringify(props.initialPackages));
      } else {
        // First time opening - create one empty package
        localPackages.value = [createEmptyPackage()];
      }
      selectedPackageIndex.value = 0;
    }
  },
  { immediate: true }
);

// Watch for editing index changes
watch(
  () => props.editingIndex,
  (newIndex) => {
    if (
      newIndex >= 0 &&
      props.initialPackages &&
      props.initialPackages[newIndex]
    ) {
      // If editing, show only the specific package
      localPackages.value = [
        JSON.parse(JSON.stringify(props.initialPackages[newIndex])),
      ];
      selectedPackageIndex.value = 0;
    } else if (newIndex === -1) {
      // If not editing, show all existing packages or create one empty package
      if (props.initialPackages && props.initialPackages.length > 0) {
        // Show all existing packages
        localPackages.value = JSON.parse(JSON.stringify(props.initialPackages));
      } else {
        // No existing packages, create one empty package
        localPackages.value = [createEmptyPackage()];
      }
      selectedPackageIndex.value = 0;
    }
  }
);

// Watch for same price checkbox changes
watch(samePriceForAll, (newValue) => {
  if (newValue && localPackages.value.length > 0) {
    // Get the price from the first package
    const firstPackagePrice = localPackages.value[0].price || 0;
    // Apply the same price to all packages
    localPackages.value.forEach((pkg) => {
      pkg.price = firstPackagePrice;
    });
  }
});

// Watch for price changes in the first package when same price is enabled
watch(
  () => localPackages.value[0]?.price,
  (newPrice) => {
    if (
      samePriceForAll.value &&
      newPrice !== undefined &&
      localPackages.value.length > 1
    ) {
      // Apply the new price to all other packages
      for (let i = 1; i < localPackages.value.length; i++) {
        localPackages.value[i].price = newPrice;
      }
    }
  }
);

// Create empty package template
function createEmptyPackage() {
  return {
    title: "",
    description: "",
    price: 0,
    number: 1,
    is_active: true,
  };
}

// Select package
function selectPackage(index) {
  selectedPackageIndex.value = index;
}

// Add new package
function addNewPackage() {
  console.log("addNewPackage called");
  console.log("localPackages before adding:", localPackages.value);

  const newNumber = localPackages.value.length + 1;
  const newPackage = {
    ...createEmptyPackage(),
    number: newNumber,
  };

  console.log("New package to be added:", newPackage);

  localPackages.value.push(newPackage);
  selectedPackageIndex.value = localPackages.value.length - 1;

  console.log("localPackages after adding:", localPackages.value);
  console.log("selectedPackageIndex:", selectedPackageIndex.value);
}

// Close dialog
function closeDialog() {
  emit("update:modelValue", false);
  emit("close");
  // Reset form state if needed
  samePriceForAll.value = false;
}

// Handle Done button
function handleDone() {
  try {
    console.log("handleDone called");
    console.log("localPackages before filtering:", localPackages.value);
    console.log("editingIndex:", props.editingIndex);

    // Filter out completely empty packages (no title, no description, no price)
    const validPackages = localPackages.value.filter((pkg) => {
      const hasTitle = pkg.title && pkg.title.trim() !== "";
      const hasDescription = pkg.description && pkg.description.trim() !== "";
      const hasPrice = pkg.price && pkg.price > 0;

      console.log(
        `Package ${pkg.number}: title="${pkg.title}", description="${pkg.description}", price="${pkg.price}"`
      );
      console.log(
        `Package ${pkg.number}: hasTitle=${hasTitle}, hasDescription=${hasDescription}, hasPrice=${hasPrice}`
      );

      // Consider a package valid if it has at least a title
      return hasTitle;
    });

    console.log("validPackages after filtering:", validPackages);

    if (validPackages.length === 0) {
      alert("Please add at least one package with a title");
      return;
    }

    console.log("Emitting valid packages:", validPackages);
    console.log("Emitting editingIndex:", props.editingIndex);

    emit("done", validPackages, props.editingIndex);
    closeDialog();

    // Reset form state
    samePriceForAll.value = false;
  } catch (error) {
    console.error("Error in handleDone:", error);
    alert("Error saving packages: " + error.message);
  }
}

// Handle modelValue update to prevent double closing
function handleModelValueUpdate(newValue) {
  if (!newValue) {
    // Modal is closing, reset form state
    samePriceForAll.value = false;
  }
  emit("update:modelValue", newValue);
}
</script>

<style scoped>
.package-modal {
  z-index: 1000;
}

.package-card {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
}

.modal-header {
  background: white;
  padding: 32px 40px 24px 40px;
  color: #333;
  border-bottom: 1px solid #e0e0e0;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.modal-title {
  font-family: "Anton", sans-serif;
  font-size: 32px;
  font-weight: 700;
  margin: 0 0 12px 0;
  color: #333;
  line-height: 1.2;
}

.modal-subtitle {
  font-family: "Karla", sans-serif;
  font-size: 16px;
  margin: 0;
  color: #666;
  line-height: 1.5;
  max-width: 500px;
}

.close-btn {
  color: #333 !important;
  margin-top: 4px;
}

.close-btn:hover {
  background-color: rgba(0, 0, 0, 0.1) !important;
}

.modal-content {
  display: flex;
  min-height: 500px;
}

.sidebar {
  width: 320px;
  background: #f8f9fa;
  border-right: 1px solid #e0e0e0;
  overflow-y: auto;
}

.sidebar-content {
  padding: 24px 20px;
}

.sidebar-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 20px;
  margin-bottom: 16px;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s ease;
  background: white;
  border: 1px solid #e0e0e0;
  position: relative;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.sidebar-item:hover {
  border-color: #ec8d22;
  box-shadow: 0 4px 12px rgba(236, 141, 34, 0.15);
  transform: translateY(-1px);
}

.sidebar-item.active {
  border-color: #ec8d22;
  background: #fff8f0;
  box-shadow: 0 6px 16px rgba(236, 141, 34, 0.2);
  transform: translateY(-2px);
}

.package-badge {
  width: 32px;
  height: 32px;
  background: #ec8d22;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  position: relative;
}

.badge-number {
  color: white;
  font-weight: 700;
  font-size: 12px;
  font-family: "Anton", sans-serif;
}

.package-info {
  flex: 1;
  min-width: 0;
}

.package-title-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}

.package-title {
  font-size: 16px;
  font-weight: 600;
  color: #333333;
  font-family: "Karla", sans-serif;
}

.chevron {
  color: #333333;
}

.package-description {
  display: flex;
  align-items: flex-start;
  gap: 6px;
  margin-bottom: 12px;
  position: relative;
}

.package-icon {
  color: #ec8d22;
  margin-top: 2px;
  flex-shrink: 0;
}

.description-text {
  font-size: 14px;
  color: #666666;
  line-height: 1.4;
  font-family: "Karla", sans-serif;
}

.package-price {
  font-size: 14px;
  font-weight: 600;
  color: #333333;
  text-align: right;
  font-family: "Karla", sans-serif;
}

.main-content {
  flex: 1;
  padding: 40px;
  overflow-y: auto;
  background: white;
}

.package-section {
  display: none;
}

.package-section.active {
  display: block;
}

.content-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 40px;
  padding-bottom: 20px;
  border-bottom: 1px solid #e0e0e0;
}

.content-title {
  font-family: "Anton", sans-serif;
  font-size: 28px;
  font-weight: 700;
  color: #333333;
  margin: 0;
  line-height: 1.2;
}

.status-indicator {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #4caf50;
  font-size: 14px;
  font-weight: 500;
}

.check-icon {
  color: #4caf50;
}

.form-content {
  max-width: 600px;
}

.form-field {
  margin-bottom: 32px;
  position: relative;
}

.field-label {
  display: block;
  font-family: "Karla", sans-serif;
  font-size: 14px;
  font-weight: 600;
  color: #333333;
  margin-bottom: 8px;
  line-height: 1.4;
}

.form-field.price-field {
  max-width: 300px;
}

.price-input-container {
  position: relative;
}

.title-input,
.price-input,
.description-input {
  width: 100%;
}

.title-input :deep(.v-field),
.price-input :deep(.v-field),
.description-input :deep(.v-field) {
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  background-color: white;
  transition: all 0.2s ease;
}

.title-input :deep(.v-field:hover),
.price-input :deep(.v-field:hover),
.description-input :deep(.v-field:hover) {
  border-color: #ec8d22;
  box-shadow: 0 2px 8px rgba(236, 141, 34, 0.1);
}

.title-input :deep(.v-field--focused),
.price-input :deep(.v-field--focused),
.description-input :deep(.v-field--focused) {
  border-color: #ec8d22;
  box-shadow: 0 0 0 2px rgba(236, 141, 34, 0.1);
}

.title-input :deep(.v-field__input),
.price-input :deep(.v-field__input),
.description-input :deep(.v-field__input) {
  padding: 12px 16px;
  font-family: "Karla", sans-serif;
  font-size: 14px;
  color: #333333;
}

.title-input :deep(.v-field__input::placeholder),
.price-input :deep(.v-field__input::placeholder),
.description-input :deep(.v-field__input::placeholder) {
  color: #999999;
  font-family: "Karla", sans-serif;
  font-size: 14px;
}

.currency-symbol {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #666666;
  font-weight: 600;
  font-size: 16px;
  pointer-events: none;
  z-index: 2;
  font-family: "Karla", sans-serif;
}

.description-field {
  margin-bottom: 32px;
}

.description-input :deep(.v-field__input) {
  min-height: 120px;
  resize: vertical;
}

.modal-footer {
  padding: 32px 40px;
  background: #f8f9fa;
  border-top: 1px solid #e0e0e0;
  display: flex;
  gap: 16px;
  justify-content: flex-end;
}

.done-btn {
  background-color: #111 !important;
  color: white !important;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  padding: 14px 28px;
  min-height: 48px;
  text-transform: none;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  transition: all 0.2s ease;
  font-family: "Karla", sans-serif;
}

.done-btn:hover {
  background-color: #222 !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.add-more-btn {
  background-color: #ec8d22 !important;
  color: white !important;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  padding: 14px 28px;
  min-height: 48px;
  text-transform: none;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  transition: all 0.2s ease;
  font-family: "Karla", sans-serif;
}

.add-more-btn:hover {
  background-color: #d67d1a !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.same-price-section {
  margin-top: 32px;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
}

.same-price-checkbox {
  margin-bottom: 0;
}

.same-price-checkbox :deep(.v-label) {
  font-family: "Karla", sans-serif;
  font-size: 14px;
  font-weight: 600;
  color: #333333;
}

.same-price-checkbox :deep(.v-selection-control) {
  margin-right: 12px;
}

.same-price-checkbox :deep(.v-selection-control__input) {
  color: #ec8d22;
}

.same-price-checkbox :deep(.v-selection-control__input:hover) {
  color: #d67d1a;
}

/* Responsive design */
@media (max-width: 768px) {
  .modal-content {
    flex-direction: column;
  }

  .sidebar {
    width: 100%;
    max-height: 200px;
  }

  .form-field.price-field {
    max-width: 100%;
  }

  .modal-footer {
    flex-direction: column;
  }

  .done-btn,
  .add-more-btn {
    width: 100%;
  }
}
</style>
