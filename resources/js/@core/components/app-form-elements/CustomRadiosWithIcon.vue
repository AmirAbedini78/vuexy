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
      font-size: 20px;
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
</style>
