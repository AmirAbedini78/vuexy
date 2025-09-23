<template>
  <VDialog
    v-model="isOpen"
    max-width="800px"
    @click:outside="closeDialog"
    @keydown.esc="closeDialog"
  >
    <VCard>
      <VCardTitle class="d-flex justify-space-between align-center pa-6">
        <div>
          <h2 class="text-h4 font-weight-bold">Itinerary/Accommodations</h2>
          <p class="text-body-1 text-medium-emphasis mt-2">
            Please fill the form carefully (or something kinder maybe)
          </p>
        </div>
        <VBtn icon variant="text" @click="closeDialog">
          <VIcon icon="tabler-x" size="24" />
        </VBtn>
      </VCardTitle>

      <VCardText class="pa-6">
        <!-- Auto-save indicator -->
        <div class="auto-save-indicator" v-if="isSaving || showSavedIndicator">
          <div class="save-status" :class="{ 'saving': isSaving, 'saved': showSavedIndicator && !isSaving }">
            <VIcon 
              :icon="isSaving ? 'tabler-loader-2' : 'tabler-check'" 
              :class="{ 'spinning': isSaving }"
            />
            <span v-if="isSaving">Saving...</span>
            <span v-else-if="showSavedIndicator">Changes saved</span>
          </div>
        </div>

        <div class="d-flex gap-6">
          <!-- Left Sidebar -->
          <div class="sidebar" style="min-width: 200px">
            <div
              v-for="(day, index) in localDays"
              :key="index"
              class="sidebar-item mb-4 pa-4 rounded"
              :class="{ 'bg-grey-lighten-4': selectedDayIndex === index }"
              @click="selectDay(index)"
              style="cursor: pointer"
            >
              <div class="d-flex align-center gap-3">
                <div
                  class="day-badge d-flex align-center justify-center"
                  style="
                    background: #ec8d22;
                    color: white;
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    font-weight: bold;
                    font-family: 'Anton', sans-serif;
                  "
                >
                  {{ String(index + 1).padStart(2, "0") }}
                </div>
                <div>
                  <div class="font-weight-bold text-body-1">
                    {{ day.title || `Day ${index + 1} Itinerary Title` }}
                  </div>
                  <div
                    v-if="selectedDayIndex === index"
                    class="d-flex align-center mt-2"
                    style="color: #00c853; font-weight: bold"
                  >
                    <VIcon
                      icon="tabler-map-pin"
                      size="16"
                      style="color: #ec8d22; margin-right: 4px"
                    />
                    Day {{ index + 1 }} Accommodation
                  </div>
                  <div
                    v-if="selectedDayIndex === index"
                    class="text-caption mt-1"
                    style="color: #bbb"
                  >
                    {{ day.location || "Location would take place here" }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="main-content flex-grow-1">
            <div
              v-for="(day, index) in localDays"
              :key="index"
              v-show="selectedDayIndex === index"
            >
              <div class="d-flex justify-space-between align-center mb-4">
                <h3 class="text-h5 font-weight-bold">Day {{ index + 1 }}</h3>
                <div class="d-flex align-center" style="color: #4caf50">
                  <VIcon icon="tabler-check" size="16" />
                  <span class="ml-1">Changes saved</span>
                </div>
              </div>

              <div class="d-flex gap-4">
                <div class="flex-grow-1">
                  <VTextField
                    v-model="day.title"
                    placeholder="Itinerary Title"
                    variant="outlined"
                    density="comfortable"
                    class="mb-4"
                    hide-details
                  />
                  <VTextarea
                    v-model="day.description"
                    placeholder="Itinerary Description"
                    variant="outlined"
                    density="comfortable"
                    rows="4"
                    class="mb-4"
                    hide-details
                  />
                  <div class="d-flex gap-2">
                    <VTextField
                      v-model="day.link"
                      placeholder="Add your link here"
                      variant="outlined"
                      density="comfortable"
                      class="flex-grow-1"
                      hide-details
                    />
                    <VBtn
                      icon
                      variant="elevated"
                      color="primary"
                      @click="addLink"
                    >
                      <VIcon icon="tabler-plus" size="20" />
                    </VBtn>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <VTextField
                    v-model="day.accommodation"
                    placeholder="Add accommodation here"
                    variant="outlined"
                    density="comfortable"
                    class="mb-4"
                    hide-details
                  />
                  <VTextarea
                    v-model="day.location"
                    placeholder="Add exact accommodation location"
                    variant="outlined"
                    density="comfortable"
                    rows="4"
                    hide-details
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </VCardText>

      <VCardActions class="pa-6 pt-0">
        <VSpacer />
        <VBtn
          variant="elevated"
          @click="handleDone"
          :loading="loading"
          style="background: #111; color: white"
        >
          Done
        </VBtn>
        <VBtn
          variant="elevated"
          @click="addNewDay"
          style="background: #ec8d22; color: white"
        >
          Add More Days
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import { useAutoSave } from "@/composables/useAutoSave";
import {
  VBtn,
  VCard,
  VCardActions,
  VCardText,
  VCardTitle,
  VDialog,
  VIcon,
  VSpacer,
  VTextField,
  VTextarea,
} from "vuetify/components";

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  initialDays: {
    type: Array,
    default: () => [],
  },
  listingId: {
    type: [String, Number],
    default: null,
  },
  editingIndex: {
    type: Number,
    default: -1,
  },
});

const emit = defineEmits(["update:modelValue", "close", "done"]);

const loading = ref(false);
const selectedDayIndex = ref(0);

// Local state for days
const localDays = ref([]);

// Auto-save functionality for days
const { 
  isSaving, 
  lastSaved, 
  hasUnsavedChanges, 
  showSavedIndicator,
  saveToStorage, 
  loadFromStorage, 
  clearSavedData,
  hasSavedData,
  getSavedDataInfo 
} = useAutoSave(localDays, 'itinerary-accommodation-dialog-data', {
  debounceMs: 300, // Save after 300ms of inactivity
  onSave: (data) => {
    console.log('Itinerary accommodation dialog data auto-saved:', data);
  },
  onLoad: (data, meta) => {
    console.log('Itinerary accommodation dialog data loaded from storage:', data);
    if (meta) {
      console.log('Last saved:', new Date(meta.timestamp).toLocaleString());
    }
  }
});

// Computed property for dialog visibility
const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => {
    if (!value) {
      // Save data when closing dialog
      saveToStorage();
    }
    emit("update:modelValue", value);
  },
});

// Initialize local days when dialog opens
watch(
  () => props.modelValue,
  (newValue) => {
    console.log("Itinerary modal modelValue changed:", newValue);
    if (newValue) {
      console.log("Opening itinerary modal");
      // When dialog opens, initialize with existing data or default
      if (props.initialDays && props.initialDays.length > 0) {
        // If we have existing days, show them in the modal
        localDays.value = JSON.parse(JSON.stringify(props.initialDays));
        console.log("Using existing days:", localDays.value);
      } else {
        // First time opening - create one empty day
        localDays.value = [createEmptyDay()];
        console.log("Creating empty day:", localDays.value);
      }
      selectedDayIndex.value = 0;
    }
  },
  { immediate: true }
);

// Watch for editing index changes
watch(
  () => props.editingIndex,
  (newIndex) => {
    if (newIndex >= 0 && props.initialDays && props.initialDays[newIndex]) {
      // If editing, show only the specific itinerary
      localDays.value = [
        JSON.parse(JSON.stringify(props.initialDays[newIndex])),
      ];
      selectedDayIndex.value = 0;
    } else if (newIndex === -1) {
      // If not editing, show all existing itineraries or create one empty itinerary
      if (props.initialDays && props.initialDays.length > 0) {
        // Show all existing itineraries
        localDays.value = JSON.parse(JSON.stringify(props.initialDays));
      } else {
        // No existing itineraries, create one empty itinerary
        localDays.value = [createEmptyDay()];
      }
      selectedDayIndex.value = 0;
    }
  }
);

function createEmptyDay() {
  return {
    id: Date.now(),
    number: 1,
    title: "",
    description: "",
    accommodation: "",
    location: "",
    link: "",
  };
}

function closeDialog() {
  emit("update:modelValue", false);
  emit("close");
}

function selectDay(index) {
  selectedDayIndex.value = index;
}

function addNewDay() {
  const newDayNumber = localDays.value.length + 1;
  localDays.value.push({
    id: Date.now(),
    number: newDayNumber,
    title: "",
    description: "",
    accommodation: "",
    location: "",
    link: "",
  });
  selectedDayIndex.value = localDays.value.length - 1;
}

function removeDay(index) {
  if (localDays.value.length > 1) {
    localDays.value.splice(index, 1);
    // Update numbers
    localDays.value.forEach((day, idx) => {
      day.number = idx + 1;
    });
    if (selectedDayIndex.value >= localDays.value.length) {
      selectedDayIndex.value = localDays.value.length - 1;
    }
  }
}

function addLink() {
  const day = localDays.value[selectedDayIndex.value];
  if (!day.link) {
    day.link = "";
  }
  day.link += " "; // Add a space to allow adding more links
}

async function handleDone() {
  try {
    loading.value = true;
    console.log("handleDone called");
    console.log("localDays before filtering:", localDays.value);
    console.log("editingIndex:", props.editingIndex);

    // Filter out completely empty itineraries (no title, no description, no accommodation)
    const validItineraries = localDays.value.filter((day) => {
      const hasTitle = day.title && day.title.trim() !== "";
      const hasDescription = day.description && day.description.trim() !== "";
      const hasAccommodation =
        day.accommodation && day.accommodation.trim() !== "";

      console.log(
        `Day ${day.number}: title="${day.title}", description="${day.description}", accommodation="${day.accommodation}"`
      );
      console.log(
        `Day ${day.number}: hasTitle=${hasTitle}, hasDescription=${hasDescription}, hasAccommodation=${hasAccommodation}`
      );

      // Consider an itinerary valid if it has at least a title
      return hasTitle;
    });

    console.log("validItineraries after filtering:", validItineraries);

    if (validItineraries.length === 0) {
      alert("Please add at least one day with a title");
      loading.value = false;
      return;
    }

    console.log("Emitting valid itineraries:", validItineraries);
    console.log("Emitting editingIndex:", props.editingIndex);

    // Emit the data to parent with editing info
    emit("done", validItineraries, props.editingIndex);

    // Close dialog
    emit("update:modelValue", false);
    emit("close");
  } catch (error) {
    console.error("Error saving itinerary:", error);
    alert("Error saving itinerary data");
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.auto-save-indicator {
  position: absolute;
  top: 10px;
  left: 20px;
  z-index: 10;
  
  .save-status {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    
    &.saving {
      color: #1976d2;
      
      .v-icon {
        color: #1976d2;
      }
    }
    
    &.saved {
      color: #000000;
      
      .v-icon {
        color: #000000;
      }
    }
    
    .v-icon {
      font-size: 16px;
      
      &.spinning {
        animation: spin 1s linear infinite;
      }
    }
  }
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>
