<script setup>
const props = defineProps({
  selectedRadio: {
    type: String,
    required: true,
  },
  radioContent: {
    type: Array,
    required: true,
  },
  gridColumn: {
    type: null,
    required: false,
  },
});

const emit = defineEmits(["update:selectedRadio"]);

const updateSelectedOption = (value) => {
  if (value !== null) emit("update:selectedRadio", value);
};
</script>

<template>
  <VRadioGroup
    v-if="props.radioContent"
    :model-value="props.selectedRadio"
    class="custom-input-wrapper"
    @update:model-value="updateSelectedOption"
  >
    <VRow>
      <VCol
        v-for="item in props.radioContent"
        :key="item.title"
        v-bind="gridColumn"
        class="radio-card-col"
      >
        <VLabel
          class="custom-input custom-radio-icon rounded cursor-pointer"
          :class="props.selectedRadio === item.value ? 'active' : ''"
        >
          <slot :item="item">
            <div class="d-flex flex-column align-center text-center gap-2">
              <span v-if="item.img">
                <img
                  v-if="typeof item.img === 'object'"
                  :src="
                    props.selectedRadio === item.value
                      ? item.img.active
                      : item.img.inactive
                  "
                  alt=""
                  style="
                    width: 48px;
                    height: 48px;
                    margin-bottom: 12px;
                    object-fit: contain;
                  "
                />
                <img
                  v-else
                  :src="item.img"
                  alt=""
                  style="
                    width: 48px;
                    height: 48px;
                    margin-bottom: 12px;
                    object-fit: contain;
                  "
                />
              </span>
              <VIcon v-else v-bind="item.icon" class="text-high-emphasis" />
              <h6 class="text-h6">
                {{ item.title }}
              </h6>

              <p class="text-body-2 mb-0">
                {{ item.desc }}
              </p>
            </div>
          </slot>

          <div>
            <VRadio :value="item.value" />
          </div>
        </VLabel>
      </VCol>
    </VRow>
  </VRadioGroup>
</template>

<style lang="scss" scoped>
.custom-radio-icon {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  padding: 24px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  transition: all 0.3s ease;
  background: #fff;
  min-height: 200px;
  justify-content: center;

  &:hover {
    border-color: #ec8d22;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  &.active {
    border-color: #ec8d22;
    background: linear-gradient(135deg, #fff 0%, #fff8f0 100%);
    box-shadow: 0 6px 20px rgba(236, 141, 34, 0.15);
  }

  .v-radio {
    margin-block-end: -0.5rem;
  }

  h6 {
    font-family: "Karla", sans-serif;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
  }

  p {
    font-family: "Karla", sans-serif;
    color: #666;
    line-height: 1.5;
  }
}
</style>

<style lang="scss">
.custom-radio-icon {
  .v-radio {
    margin-block-end: -0.25rem;

    .v-selection-control__wrapper {
      margin-inline-start: 0;
    }

    .v-selection-control__input {
      color: #ec8d22;
    }

    .v-selection-control__input .v-icon {
      color: #ec8d22;
      /* font-size: 20px; */
    }
  }

  &.active {
    .v-radio .v-selection-control__input {
      color: #ec8d22;
    }

    .v-radio .v-selection-control__input .v-icon {
      color: #ec8d22;
    }
  }
}

.radio-card-col {
  margin-bottom: 16px;
}

.custom-input-wrapper {
  width: 100%;
}

/* Fix radio button sizing and clipping */
::deep(.v-radio) {
  overflow: visible !important;
  width: auto !important;
  height: auto !important;
  position: relative !important;
  transform: translate(-4px, -4px) !important;
}

::deep(.v-radio .v-selection-control) {
  min-height: 30px;
  padding: 8px 8px;
  overflow: visible !important;
  width: auto !important;
  height: auto !important;
  position: relative !important;
}

::deep(.v-radio .v-selection-control__wrapper) {
  width: 24px !important;
  height: 24px !important;
  padding: 0 !important;
  overflow: visible !important;
  margin: 0 !important;
  position: relative !important;
}

::deep(.v-radio .v-selection-control__input) {
  width: 20px !important;
  height: 20px !important;
  overflow: visible !important;
  position: relative !important;
}

::deep(.v-radio .v-selection-control__ripple) {
  inset: -12px !important;
}

/* Ensure the radio button container has enough space */
.custom-radio-icon {
  overflow: visible !important;
  position: relative;
  padding: 8px 12px 8px 8px !important;
}

.custom-radio-icon .v-radio {
  position: relative;
  z-index: 1;
}
</style>
