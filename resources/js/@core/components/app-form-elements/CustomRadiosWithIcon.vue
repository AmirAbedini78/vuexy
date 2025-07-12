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
                  style="width: 32px; height: 32px; margin-bottom: 8px"
                />
                <img
                  v-else
                  :src="item.img"
                  alt=""
                  style="width: 32px; height: 32px; margin-bottom: 8px"
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

  .v-radio {
    margin-block-end: -0.5rem;
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
  }
}
</style>
