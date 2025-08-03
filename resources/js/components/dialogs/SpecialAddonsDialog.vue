<template>
  <VDialog
    v-model="isOpen"
    max-width="1200px"
    class="special-addons-modal"
    @click:outside="closeDialog"
    @keydown.esc="closeDialog"
    @update:model-value="handleModelValueUpdate"
  >
    <VCard class="special-addons-card">
      <!-- Header -->
      <div class="modal-header">
        <div class="header-content">
          <div class="header-text">
            <h1 class="modal-title">Special Addons</h1>
            <p class="modal-subtitle">
              Please fill the form carefully (or something kinder maybe)
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
              v-for="(addon, index) in localAddons"
              :key="index"
              class="sidebar-item"
              :class="{ active: selectedAddonIndex === index }"
              @click="selectAddon(index)"
            >
              <div class="addon-badge">
                <span class="badge-number">{{
                  String(index + 1).padStart(2, "0")
                }}</span>
              </div>
              <div class="addon-info">
                <div class="addon-title-row">
                  <span class="addon-title">{{
                    addon.title || `Addon ${index + 1} Title`
                  }}</span>
                  <VIcon
                    :icon="
                      selectedAddonIndex === index
                        ? 'tabler-chevron-down'
                        : 'tabler-chevron-right'
                    "
                    size="16"
                    class="chevron"
                  />
                </div>
                <div
                  v-if="selectedAddonIndex === index"
                  class="addon-description"
                >
                  <VIcon icon="tabler-star" size="16" class="star-icon" />
                  <span class="description-text">{{
                    addon.description ||
                    "Your addon description would take place here"
                  }}</span>
                </div>
                <div v-if="selectedAddonIndex === index" class="addon-price">
                  € {{ addon.price || "200.00" }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
          <div
            v-for="(addon, index) in localAddons"
            :key="index"
            class="addon-section"
            :class="{ active: selectedAddonIndex === index }"
          >
            <div class="content-header">
              <h2 class="content-title">Special Addon {{ index + 1 }}</h2>
              <div class="status-indicator">
                <VIcon icon="tabler-check" size="16" class="check-icon" />
                <span class="status-text">Changes saved</span>
              </div>
            </div>

            <div class="form-content">
              <div class="form-row">
                <div class="form-field">
                  <VTextField
                    v-model="addon.title"
                    placeholder="Title"
                    variant="outlined"
                    density="comfortable"
                    class="title-input"
                    hide-details
                  />
                </div>
                <div class="form-field price-field">
                  <VTextField
                    v-model.number="addon.price"
                    placeholder="Price"
                    variant="outlined"
                    density="comfortable"
                    type="number"
                    min="0"
                    step="0.01"
                    class="price-input"
                    hide-details
                  />
                  <div class="currency-symbol">€</div>
                </div>
              </div>

              <div class="form-field description-field">
                <VTextarea
                  v-model="addon.description"
                  placeholder="Description"
                  variant="outlined"
                  density="comfortable"
                  rows="4"
                  class="description-input"
                  hide-details
                />
              </div>
            </div>
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
        <VBtn variant="elevated" @click="addNewAddon" class="add-more-btn">
          Add More Addons
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
  specialAddons: {
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
const selectedAddonIndex = ref(0);

// Local state for addons
const localAddons = ref([]);

// Computed property for dialog visibility
const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => {
    emit("update:modelValue", value);
  },
});

// Initialize local addons when dialog opens
watch(
  () => props.modelValue,
  (newValue) => {
    if (newValue) {
      // When dialog opens, initialize with existing data or default
      if (props.specialAddons && props.specialAddons.length > 0) {
        // If we have existing addons, show them in the modal
        localAddons.value = JSON.parse(JSON.stringify(props.specialAddons));
      } else {
        // First time opening - create one empty addon
        localAddons.value = [createEmptyAddon()];
      }
      selectedAddonIndex.value = 0;
    }
  },
  { immediate: true }
);

// Watch for editing index changes
watch(
  () => props.editingIndex,
  (newIndex) => {
    if (newIndex >= 0 && props.specialAddons && props.specialAddons[newIndex]) {
      // If editing, show only the specific addon
      localAddons.value = [
        JSON.parse(JSON.stringify(props.specialAddons[newIndex])),
      ];
      selectedAddonIndex.value = 0;
    } else if (newIndex === -1) {
      // If not editing, show all existing addons or create one empty addon
      if (props.specialAddons && props.specialAddons.length > 0) {
        // Show all existing addons
        localAddons.value = JSON.parse(JSON.stringify(props.specialAddons));
      } else {
        // No existing addons, create one empty addon
        localAddons.value = [createEmptyAddon()];
      }
      selectedAddonIndex.value = 0;
    }
  }
);

// Create empty addon template
function createEmptyAddon() {
  return {
    title: "",
    description: "",
    price: 0,
    number: 1,
    is_active: true,
  };
}

// Select addon
function selectAddon(index) {
  selectedAddonIndex.value = index;
}

// Add new addon
function addNewAddon() {
  console.log("addNewAddon called");
  console.log("localAddons before adding:", localAddons.value);

  const newNumber = localAddons.value.length + 1;
  const newAddon = {
    ...createEmptyAddon(),
    number: newNumber,
  };

  console.log("New addon to be added:", newAddon);

  localAddons.value.push(newAddon);
  selectedAddonIndex.value = localAddons.value.length - 1;

  console.log("localAddons after adding:", localAddons.value);
  console.log("selectedAddonIndex:", selectedAddonIndex.value);
}

// Close dialog
function closeDialog() {
  emit("update:modelValue", false);
  emit("close");
}

// Handle Done button
function handleDone() {
  try {
    console.log("handleDone called");
    console.log("localAddons before filtering:", localAddons.value);
    console.log("editingIndex:", props.editingIndex);

    // Filter out completely empty addons (no title, no description, no price)
    const validAddons = localAddons.value.filter((addon) => {
      const hasTitle = addon.title && addon.title.trim() !== "";
      const hasDescription =
        addon.description && addon.description.trim() !== "";
      const hasPrice = addon.price && addon.price > 0;

      console.log(
        `Addon ${addon.number}: title="${addon.title}", description="${addon.description}", price="${addon.price}"`
      );
      console.log(
        `Addon ${addon.number}: hasTitle=${hasTitle}, hasDescription=${hasDescription}, hasPrice=${hasPrice}`
      );

      // Consider an addon valid if it has at least a title
      return hasTitle;
    });

    console.log("validAddons after filtering:", validAddons);

    if (validAddons.length === 0) {
      alert("لطفاً حداقل یک افزونه با عنوان وارد کنید");
      return;
    }

    console.log("Emitting valid addons:", validAddons);
    console.log("Emitting editingIndex:", props.editingIndex);

    // Emit the data to parent with editing info
    emit("done", validAddons, props.editingIndex);

    // Close dialog
    closeDialog();
  } catch (error) {
    console.error("Error in handleDone:", error);
    alert("خطا در ذخیره اطلاعات");
  }
}

// Handle modelValue update to prevent immediate re-opening
function handleModelValueUpdate(newValue) {
  if (!newValue) {
    // Modal is closing
    emit("close");
  }
  emit("update:modelValue", newValue);
}
</script>

<style scoped>
.special-addons-modal {
  --orange-color: #ec8d22;
  --dark-grey: #333333;
  --light-grey: #666666;
  --border-color: #e0e0e0;
  --background-grey: #f5f5f5;
}

.special-addons-card {
  border-radius: 12px !important;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1) !important;
}

/* Header Styles */
.modal-header {
  background: white;
  padding: 24px 32px;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.header-text {
  flex: 1;
}

.modal-title {
  font-size: 28px;
  font-weight: 700;
  color: var(--dark-grey);
  margin: 0 0 8px 0;
  font-family: "Anton", sans-serif;
}

.modal-subtitle {
  font-size: 16px;
  color: var(--light-grey);
  margin: 0;
  font-weight: 400;
  font-family: "Karla", sans-serif;
}

.close-btn {
  color: var(--dark-grey) !important;
  margin-left: 16px;
}

/* Content Layout */
.modal-content {
  display: flex;
  min-height: 500px;
}

/* Sidebar Styles */
.sidebar {
  width: 35%;
  background: white;
  padding: 32px 24px;
}

.sidebar-content {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.sidebar-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 16px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  background: white;
}

.sidebar-item:hover {
  background: white;
}

.sidebar-item.active {
  background: white;
}

.addon-badge {
  width: 32px;
  height: 32px;
  background: var(--orange-color);
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.badge-number {
  color: white;
  font-weight: 700;
  font-size: 12px;
  font-family: "Anton", sans-serif;
}

.addon-info {
  flex: 1;
  min-width: 0;
}

.addon-title-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}

.addon-title {
  font-size: 16px;
  font-weight: 600;
  color: var(--dark-grey);
  font-family: "Karla", sans-serif;
}

.chevron {
  color: var(--dark-grey);
}

.addon-description {
  display: flex;
  align-items: flex-start;
  gap: 6px;
  margin-bottom: 12px;
  position: relative;
}

.star-icon {
  color: var(--orange-color);
  margin-top: 2px;
  flex-shrink: 0;
}

.description-text {
  font-size: 14px;
  color: var(--light-grey);
  line-height: 1.4;
  font-family: "Karla", sans-serif;
}

.addon-description::after {
  content: "";
  position: absolute;
  left: 8px;
  top: 20px;
  bottom: -12px;
  width: 1px;
  background: repeating-linear-gradient(
    to bottom,
    var(--light-grey) 0,
    var(--light-grey) 2px,
    transparent 2px,
    transparent 4px
  );
}

.addon-price {
  font-size: 14px;
  font-weight: 600;
  color: var(--dark-grey);
  text-align: right;
  font-family: "Karla", sans-serif;
}

/* Main Content Styles */
.main-content {
  flex: 1;
  padding: 32px 24px;
  background: white;
  overflow-y: auto;
}

.addon-section {
  margin-bottom: 48px;
  opacity: 0.3;
  transition: all 0.3s ease;
}

.addon-section.active {
  opacity: 1;
}

.addon-section:last-child {
  margin-bottom: 0;
}

.content-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.content-title {
  font-size: 18px;
  font-weight: 700;
  color: var(--dark-grey);
  margin: 0;
  font-family: "Anton", sans-serif;
}

.status-indicator {
  display: flex;
  align-items: center;
  gap: 6px;
}

.check-icon {
  color: #4caf50;
}

.status-text {
  font-size: 12px;
  color: var(--light-grey);
  font-family: "Karla", sans-serif;
}

.form-content {
  display: flex;
  flex-direction: column;
  gap: 16px;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  padding: 20px;
}

.form-row {
  display: flex;
  gap: 12px;
}

.form-field {
  flex: 1;
}

.price-field {
  position: relative;
  display: flex;
  align-items: center;
}

.currency-symbol {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: white;
  border: 1px solid var(--border-color);
  border-left: none;
  padding: 8px 12px;
  border-radius: 0 4px 4px 0;
  color: var(--light-grey);
  font-size: 14px;
  font-family: "Karla", sans-serif;
}

.price-input :deep(.v-field__input) {
  padding-right: 50px !important;
}

.title-input :deep(.v-field),
.price-input :deep(.v-field),
.description-input :deep(.v-field) {
  background: white !important;
  border: 1px solid var(--border-color) !important;
  border-radius: 6px !important;
}

.title-input :deep(.v-field__outline),
.price-input :deep(.v-field__outline),
.description-input :deep(.v-field__outline) {
  display: none !important;
}

.title-input :deep(.v-field__input),
.price-input :deep(.v-field__input),
.description-input :deep(.v-field__input) {
  padding: 10px 14px !important;
  font-size: 16px !important;
  color: var(--dark-grey) !important;
  font-family: "Karla", sans-serif !important;
  line-height: 1.4 !important;
}

.title-input :deep(.v-field__input)::placeholder,
.price-input :deep(.v-field__input)::placeholder,
.description-input :deep(.v-field__input)::placeholder {
  color: #b0b0b0 !important;
  font-family: "Karla", sans-serif !important;
  font-size: 15px !important;
}

.description-input :deep(.v-field__input) {
  min-height: 80px !important;
  max-height: 120px !important;
}

/* Footer Styles */
.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 20px;
  padding: 24px 32px;
  background: white;
  border-top: 1px solid var(--border-color);
}

.done-btn {
  background: #000000 !important;
  color: white !important;
  border-radius: 8px !important;
  font-weight: 500 !important;
  font-family: "Karla", sans-serif !important;
  text-transform: none !important;
  padding: 10px 24px !important;
  min-width: 80px !important;
  height: 40px !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15) !important;
  border: none !important;
  font-size: 14px !important;
  letter-spacing: 0.5px !important;
}

.add-more-btn {
  background: var(--orange-color) !important;
  color: white !important;
  border-radius: 8px !important;
  font-weight: 500 !important;
  font-family: "Karla", sans-serif !important;
  text-transform: none !important;
  padding: 10px 24px !important;
  min-width: 180px !important;
  height: 40px !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15) !important;
  border: none !important;
  font-size: 14px !important;
  letter-spacing: 0.5px !important;
}

/* Responsive Design */
@media (max-width: 768px) {
  .modal-content {
    flex-direction: column;
  }

  .sidebar {
    width: 100%;
    border-right: none;
    border-bottom: 1px solid var(--border-color);
  }

  .form-row {
    flex-direction: column;
  }
}
</style>
