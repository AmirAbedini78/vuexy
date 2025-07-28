<template>
  <VDialog v-model="isOpen" max-width="1200px" persistent>
    <VCard>
      <VCardTitle class="d-flex justify-space-between align-center pa-6">
        <div>
          <h2 class="text-h4 font-weight-bold">Add your Departures details</h2>
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
                      {{ period.title || `Departure ${index + 1} title` }}
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
              <!-- Vertical dotted line connecting departures -->
              <div
                v-if="index < localPeriods.length - 1"
                class="vertical-dotted-line"
                style="
                  position: relative;
                  left: 20px;
                  top: 8px;
                  width: 1px;
                  height: 16px;
                  background: repeating-linear-gradient(
                    to bottom,
                    #666 0,
                    #666 2px,
                    transparent 2px,
                    transparent 4px
                  );
                "
              ></div>
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
                  Departure {{ String(index + 1).padStart(2, "0") }}
                </h3>
                <div class="d-flex align-center" style="color: #4caf50">
                  <VIcon icon="tabler-check" size="16" />
                  <span class="ml-1">Changes saved</span>
                </div>
              </div>

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
                      Departure Capacity
                    </label>
                    <div class="d-flex gap-3">
                      <VTextField
                        v-model="period.minCapacity"
                        placeholder="Min Num"
                        type="number"
                        class="capacity-input"
                        style="max-width: 120px"
                      />
                      <VTextField
                        v-model="period.maxCapacity"
                        placeholder="Max Num"
                        type="number"
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
                        v-model="period.price"
                        placeholder="Price"
                        type="number"
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
                      label="Same Capacity for all departures"
                      class="mb-2"
                    />
                    <VCheckbox
                      v-model="samePriceForAll"
                      label="Same price for every departure"
                    />
                  </div>
                </VCol>
              </VRow>

              <!-- Disabled Departure 02 (when Departure 01 is selected) -->
              <div
                v-if="selectedPeriodIndex === 0 && localPeriods.length > 1"
                class="mt-6"
              >
                <div class="d-flex justify-space-between align-center mb-4">
                  <h4 class="text-h6 font-weight-bold" style="color: #999">
                    Departure 02
                  </h4>
                </div>
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
                        Departure Capacity
                      </label>
                      <div class="d-flex gap-3">
                        <VTextField
                          v-model="localPeriods[1].minCapacity"
                          placeholder="Min Num"
                          type="number"
                          disabled
                          style="max-width: 120px"
                        />
                        <VTextField
                          v-model="localPeriods[1].maxCapacity"
                          placeholder="Max Num"
                          type="number"
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
                          v-model="localPeriods[1].price"
                          placeholder="Price"
                          type="number"
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
          @click="handleDone"
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
          Add More Departures
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
});

const emit = defineEmits(["update:modelValue", "close", "done"]);

const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit("update:modelValue", value),
});

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

const closeDialog = () => {
  isOpen.value = false;
};

const handleDone = () => {
  emit("done", localPeriods.value, props.editingIndex);
  closeDialog();
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
watch(sameCapacityForAll, (newValue) => {
  if (newValue && localPeriods.value.length > 1) {
    const firstPeriod = localPeriods.value[0];
    localPeriods.value.forEach((period, index) => {
      if (index > 0) {
        period.minCapacity = firstPeriod.minCapacity;
        period.maxCapacity = firstPeriod.maxCapacity;
      }
    });
  }
});

watch(samePriceForAll, (newValue) => {
  if (newValue && localPeriods.value.length > 1) {
    const firstPeriod = localPeriods.value[0];
    localPeriods.value.forEach((period, index) => {
      if (index > 0) {
        period.price = firstPeriod.price;
      }
    });
  }
});
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
