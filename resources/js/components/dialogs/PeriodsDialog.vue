<template>
  <VDialog
    v-model="isOpen"
    max-width="1200px"
    @click:outside="closeDialog"
    @keydown.esc="closeDialog"
    @update:model-value="handleModelValueUpdate"
  >
    <VCard>
      <VCardTitle class="d-flex justify-space-between align-center pa-6">
        <div>
          <h2 class="text-h4 font-weight-bold">Add your Periods details</h2>
          <p class="text-body-1 text-medium-emphasis mt-2">
            On your Listing Page each Departure Date/Price will be Bookable
            Separately
          </p>
        </div>
        <VBtn icon variant="text" @click="closeDialog">
          <VIcon icon="tabler-x" size="24" />
        </VBtn>
      </VCardTitle>

      <VCardText class="pa-6">
        <div class="d-flex gap-6">
          <!-- Left Sidebar - Departures List -->
          <div class="sidebar" style="min-width: 280px">
            <div
              v-for="(period, index) in localPeriods"
              :key="index"
              class="sidebar-item mb-4 pa-4 rounded"
              :class="{ 'bg-grey-lighten-4': selectedPeriodIndex === index }"
              @click="selectPeriod(index)"
              style="cursor: pointer; border: 1px solid #e0e0e0"
            >
              <div class="d-flex align-center gap-3">
                <div
                  class="period-badge d-flex align-center justify-center"
                  style="
                    background: #ec8d22;
                    color: white;
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    font-weight: bold;
                    font-family: 'Anton', sans-serif;
                    font-size: 14px;
                  "
                >
                  {{ String(index + 1).padStart(2, "0") }}
                </div>
                <div class="flex-grow-1">
                  <div class="d-flex align-center justify-space-between">
                    <div class="font-weight-bold text-body-1">
                      {{ period.title || `Period ${index + 1} title` }}
                    </div>
                    <VIcon
                      :icon="
                        selectedPeriodIndex === index
                          ? 'tabler-chevron-down'
                          : 'tabler-chevron-right'
                      "
                      size="16"
                      style="color: #666"
                    />
                  </div>
                  <div
                    v-if="selectedPeriodIndex === index"
                    class="d-flex align-center mt-2"
                    style="color: #666; font-size: 14px"
                  >
                    <VIcon
                      icon="tabler-calendar"
                      size="16"
                      style="color: #ec8d22; margin-right: 4px"
                    />
                    {{ period.startingDate || "Starting Date" }} -
                    {{ period.finishingDate || "Finishing Date" }}
                  </div>
                  <div
                    v-if="selectedPeriodIndex === index"
                    class="text-caption mt-1"
                    style="color: #666; font-size: 12px"
                  >
                    {{ period.minCapacity || "Min" }} -
                    {{ period.maxCapacity || "Max" }} people
                  </div>
                  <div
                    v-if="selectedPeriodIndex === index"
                    class="text-caption mt-1"
                    style="color: #ec8d22; font-weight: 600; font-size: 14px"
                  >
                    € {{ (period.price || 0).toFixed(2) }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Main Content Area - Departure Details Form -->
          <div class="main-content flex-grow-1">
            <div
              v-for="(period, index) in localPeriods"
              :key="index"
              v-show="selectedPeriodIndex === index"
            >
              <div class="d-flex justify-space-between align-center mb-6">
                <h3 class="text-h5 font-weight-bold">
                  Period {{ String(index + 1).padStart(2, "0") }}
                </h3>
                <div class="d-flex align-center" style="color: #4caf50">
                  <VIcon icon="tabler-check" size="16" />
                  <span class="ml-1">Changes saved</span>
                </div>
              </div>

              <!-- Period Title -->
              <VRow>
                <VCol cols="12">
                  <div class="mb-4">
                    <label class="v-label text-body-2 mb-3 d-block">
                      Period Title
                    </label>
                    <VTextField
                      v-model="period.title"
                      placeholder="Period Title"
                      variant="outlined"
                      density="compact"
                    />
                  </div>
                </VCol>
              </VRow>

              <VRow>
                <VCol cols="12" md="6">
                  <!-- Starting Date -->
                  <div class="mb-4">
                    <label class="v-label text-body-2 mb-3 d-block">
                      Starting Date
                    </label>
                    <AppDateTimePicker
                      v-model="period.startingDate"
                      placeholder="Starting Date"
                      :config="{ dateFormat: 'Y-m-d', allowInput: true }"
                    />
                  </div>
                </VCol>
                <VCol cols="12" md="6">
                  <!-- Finishing Date -->
                  <div class="mb-4">
                    <label class="v-label text-body-2 mb-3 d-block">
                      Finishing Date
                    </label>
                    <AppDateTimePicker
                      v-model="period.finishingDate"
                      placeholder="Finishing Date"
                      :config="{ dateFormat: 'Y-m-d', allowInput: true }"
                    />
                  </div>
                </VCol>
              </VRow>

              <VRow>
                <VCol cols="12" md="6">
                  <!-- Departure Capacity -->
                  <div class="mb-4">
                    <label class="v-label text-body-2 mb-3 d-block">
                      Period Capacity
                    </label>
                    <div class="d-flex gap-3">
                      <VTextField
                        v-model.number="period.minCapacity"
                        placeholder="Min Num"
                        type="number"
                        min="1"
                        step="1"
                        class="capacity-input"
                        style="max-width: 120px"
                      />
                      <VTextField
                        v-model.number="period.maxCapacity"
                        placeholder="Max Num"
                        type="number"
                        min="1"
                        step="1"
                        class="capacity-input"
                        style="max-width: 120px"
                      />
                    </div>
                  </div>
                </VCol>
                <VCol cols="12" md="6">
                  <!-- Price -->
                  <div class="mb-4">
                    <label class="v-label text-body-2 mb-3 d-block">
                      Price
                    </label>
                    <div class="price-input-wrapper">
                      <span class="euro-symbol-left">€</span>
                      <VTextField
                        v-model.number="period.price"
                        placeholder="Price"
                        type="number"
                        min="0"
                        step="0.01"
                        class="price-input"
                      />
                    </div>
                    <p
                      class="text-caption text-error mt-2 mb-0"
                      style="font-size: 11px"
                    >
                      Note: This system uses Euro (€) only
                    </p>
                  </div>
                </VCol>
              </VRow>

              <!-- Global Checkboxes -->
              <VRow>
                <VCol cols="12">
                  <div class="mb-4">
                    <VCheckbox
                      v-model="sameCapacityForAll"
                      label="Same Capacity for all Periods"
                      class="mb-2"
                      @click.stop
                    />
                    <VCheckbox
                      v-model="samePriceForAll"
                      label="Same price for every Periods"
                      @click.stop
                    />
                  </div>
                </VCol>
              </VRow>

              <!-- Disabled {{ itemTerm }} 02 (when {{ itemTerm }} 01 is selected) -->
              <div
                v-if="selectedPeriodIndex === 0 && localPeriods.length > 1"
                class="mt-6"
              >
                <div class="d-flex justify-space-between align-center mb-4">
                  <h4 class="text-h6 font-weight-bold" style="color: #999">
                    Period 02
                  </h4>
                </div>

                <!-- Departure Title -->
                <VRow>
                  <VCol cols="12">
                    <div class="mb-4">
                      <label
                        class="v-label text-body-2 mb-3 d-block"
                        style="color: #999"
                      >
                        Period Title
                      </label>
                      <VTextField
                        v-model="localPeriods[1].title"
                        placeholder="Period Title"
                        variant="outlined"
                        density="compact"
                        disabled
                      />
                    </div>
                  </VCol>
                </VRow>

                <VRow>
                  <VCol cols="12" md="6">
                    <div class="mb-4">
                      <label
                        class="v-label text-body-2 mb-3 d-block"
                        style="color: #999"
                      >
                        Starting Date
                      </label>
                      <AppDateTimePicker
                        v-model="localPeriods[1].startingDate"
                        placeholder="Starting Date"
                        :config="{ dateFormat: 'Y-m-d', allowInput: true }"
                        disabled
                      />
                    </div>
                  </VCol>
                  <VCol cols="12" md="6">
                    <div class="mb-4">
                      <label
                        class="v-label text-body-2 mb-3 d-block"
                        style="color: #999"
                      >
                        Finishing Date
                      </label>
                      <AppDateTimePicker
                        v-model="localPeriods[1].finishingDate"
                        placeholder="Finishing Date"
                        :config="{ dateFormat: 'Y-m-d', allowInput: true }"
                        disabled
                      />
                    </div>
                  </VCol>
                </VRow>
                <VRow>
                  <VCol cols="12" md="6">
                    <div class="mb-4">
                      <label
                        class="v-label text-body-2 mb-3 d-block"
                        style="color: #999"
                      >
                        Period Capacity
                      </label>
                      <div class="d-flex gap-3">
                        <VTextField
                          v-model.number="localPeriods[1].minCapacity"
                          placeholder="Min Num"
                          type="number"
                          min="1"
                          step="1"
                          disabled
                          style="max-width: 120px"
                        />
                        <VTextField
                          v-model.number="localPeriods[1].maxCapacity"
                          placeholder="Max Num"
                          type="number"
                          min="1"
                          step="1"
                          disabled
                          style="max-width: 120px"
                        />
                      </div>
                    </div>
                  </VCol>
                  <VCol cols="12" md="6">
                    <div class="mb-4">
                      <label
                        class="v-label text-body-2 mb-3 d-block"
                        style="color: #999"
                      >
                        Price
                      </label>
                      <div class="price-input-wrapper">
                        <span class="euro-symbol-left" style="color: #999"
                          >€</span
                        >
                        <VTextField
                          v-model.number="localPeriods[1].price"
                          placeholder="Price"
                          type="number"
                          min="0"
                          step="0.01"
                          disabled
                        />
                      </div>
                      <p
                        class="text-caption text-error mt-2 mb-0"
                        style="font-size: 11px; color: #999"
                      >
                        Note: This system uses Euro (€) only
                      </p>
                    </div>
                  </VCol>
                </VRow>
              </div>
            </div>
          </div>
        </div>
      </VCardText>

      <VCardActions class="pa-6 pt-0">
        <VSpacer />
        <VBtn
          color="dark"
          variant="elevated"
          @click.stop="handleDone"
          style="
            background-color: #111 !important;
            color: #fff !important;
            border-radius: 8px;
            font-weight: 500;
            min-width: 100px;
          "
        >
          Done
        </VBtn>
        <VBtn
          color="primary"
          variant="elevated"
          @click="addMorePeriods"
          style="
            background-color: #ec8d22 !important;
            color: #fff !important;
            border-radius: 8px;
            font-weight: 500;
            min-width: 140px;
          "
        >
          Add More Periods
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

<script setup>
import AppDateTimePicker from "@/@core/components/app-form-elements/AppDateTimePicker.vue";
import { computed, ref, watch } from "vue";

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  periods: {
    type: Array,
    default: () => [],
  },
  editingIndex: {
    type: Number,
    default: -1,
  },
  usePeriodTerminology: {
    type: Boolean,
    default: false, // false for Departure (MultiDate), true for Period (OpenDate)
  },
});

const emit = defineEmits(["update:modelValue", "close", "done"]);

// Computed property for dialog visibility
const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => {
    if (!value) {
      // Reset form state when closing
      sameCapacityForAll.value = false;
      samePriceForAll.value = false;
    }
    emit("update:modelValue", value);
  },
});

// Computed properties for terminology
const itemTerm = computed(() =>
  props.usePeriodTerminology ? "Period" : "Departure"
);
const itemTermLower = computed(() =>
  props.usePeriodTerminology ? "period" : "departure"
);
const itemTermPlural = computed(() =>
  props.usePeriodTerminology ? "Periods" : "Departures"
);
const itemTermPluralLower = computed(() =>
  props.usePeriodTerminology ? "periods" : "departures"
);

const localPeriods = ref([]);
const selectedPeriodIndex = ref(0);
const sameCapacityForAll = ref(false);
const samePriceForAll = ref(false);

// Initialize local periods
watch(
  () => props.periods,
  (newPeriods) => {
    if (newPeriods.length > 0) {
      localPeriods.value = JSON.parse(JSON.stringify(newPeriods));
    } else {
      // Initialize with default period
      localPeriods.value = [
        {
          title: "",
          startingDate: "",
          finishingDate: "",
          minCapacity: "",
          maxCapacity: "",
          price: "",
        },
      ];
    }
  },
  { immediate: true }
);

// Watch for editing index changes
watch(
  () => props.editingIndex,
  (newIndex) => {
    if (newIndex >= 0 && newIndex < localPeriods.value.length) {
      selectedPeriodIndex.value = newIndex;
    }
  },
  { immediate: true }
);

const selectPeriod = (index) => {
  selectedPeriodIndex.value = index;
};

const handleModelValueUpdate = (value) => {
  if (!value) {
    // Modal is closing, reset form state
    sameCapacityForAll.value = false;
    samePriceForAll.value = false;
  }
  emit("update:modelValue", value);
};

const closeDialog = () => {
  // Close modal by updating the model value
  emit("update:modelValue", false);
  emit("close");
  // Reset form state if needed
  sameCapacityForAll.value = false;
  samePriceForAll.value = false;
};

const handleDone = () => {
  // Map the data to match backend expectations
  const mappedPeriods = localPeriods.value.map(period => ({
    title: period.title,
    start_date: period.startingDate,
    end_date: period.finishingDate,
    price: period.price,
    min_capacity: period.minCapacity,
    max_capacity: period.maxCapacity,
    is_active: true,
    order: 0
  }));

  // First emit the done event
  emit("done", mappedPeriods, props.editingIndex);

  // Close modal by updating the model value
  emit("update:modelValue", false);
  emit("close");

  // Reset form state
  sameCapacityForAll.value = false;
  samePriceForAll.value = false;
};

const addMorePeriods = () => {
  const newPeriod = {
    title: "",
    startingDate: "",
    finishingDate: "",
    minCapacity: "",
    maxCapacity: "",
    price: "",
  };

  localPeriods.value.push(newPeriod);
  selectedPeriodIndex.value = localPeriods.value.length - 1;
};

// Watch for global checkbox changes
watch(
  sameCapacityForAll,
  (newValue) => {
    if (newValue && localPeriods.value.length > 1) {
      const firstPeriod = localPeriods.value[0];
      // Only copy if first period has values
      if (firstPeriod.minCapacity && firstPeriod.maxCapacity) {
        localPeriods.value.forEach((period, index) => {
          if (index > 0) {
            period.minCapacity = firstPeriod.minCapacity;
            period.maxCapacity = firstPeriod.maxCapacity;
          }
        });
      }
    }
  },
  { immediate: false }
);

watch(
  samePriceForAll,
  (newValue) => {
    if (newValue && localPeriods.value.length > 1) {
      const firstPeriod = localPeriods.value[0];
      // Only copy if first period has price
      if (firstPeriod.price) {
        localPeriods.value.forEach((period, index) => {
          if (index > 0) {
            period.price = firstPeriod.price;
          }
        });
      }
    }
  },
  { immediate: false }
);
</script>

<style scoped>
.sidebar-item {
  transition: all 0.2s ease;
}

.sidebar-item:hover {
  background-color: #f5f5f5 !important;
}

.sidebar-item.bg-grey-lighten-4 {
  border-color: #ec8d22 !important;
}

.price-input-wrapper {
  position: relative;
}

.price-input {
  padding-left: 30px;
}

.euro-symbol-left {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #666;
  font-weight: 500;
  z-index: 1;
}

.capacity-input {
  text-align: center;
}

.vertical-dotted-line {
  margin-left: 20px;
}
</style>
