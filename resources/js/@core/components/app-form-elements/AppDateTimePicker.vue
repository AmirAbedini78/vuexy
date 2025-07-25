<script setup>
import FlatPickr from 'vue-flatpickr-component'
import { useTheme } from 'vuetify'
import {
  VField,
  makeVFieldProps,
} from 'vuetify/lib/components/VField/VField'
import {
  VInput,
  makeVInputProps,
} from 'vuetify/lib/components/VInput/VInput'


import { filterInputAttrs } from 'vuetify/lib/util/helpers'
import { useConfigStore } from '@core/stores/config'

const props = defineProps({
  autofocus: Boolean,
  counter: [
    Boolean,
    Number,
    String,
  ],
  counterValue: Function,
  prefix: String,
  placeholder: String,
  persistentPlaceholder: Boolean,
  persistentCounter: Boolean,
  suffix: String,
  type: {
    type: String,
    default: 'text',
  },
  modelModifiers: Object,
  ...makeVInputProps({
    density: 'comfortable',
    hideDetails: 'auto',
  }),
  ...makeVFieldProps({
    variant: 'outlined',
    color: 'primary',
  }),
})

const emit = defineEmits([
  'click:control',
  'mousedown:control',
  'update:focused',
  'update:modelValue',
  'click:clear',
])

defineOptions({
  inheritAttrs: false,
})

const configStore = useConfigStore()
const attrs = useAttrs()
const [rootAttrs, compAttrs] = filterInputAttrs(attrs)
const inputProps = ref(VInput.filterProps(props))
const fieldProps = ref(VField.filterProps(props))
const refFlatPicker = ref()
const { focused } = useFocus(refFlatPicker)
const isCalendarOpen = ref(false)
const isInlinePicker = ref(false)

// flat picker prop manipulation
if (compAttrs.config && compAttrs.config.inline) {
  isInlinePicker.value = compAttrs.config.inline
  Object.assign(compAttrs, { altInputClass: 'inlinePicker' })
}
compAttrs.config = {
  ...compAttrs.config,
  prevArrow: '<i class="tabler-chevron-left v-icon" style="font-size: 20px; height: 20px; width: 20px;"></i>',
  nextArrow: '<i class="tabler-chevron-right v-icon" style="font-size: 20px; height: 20px; width: 20px;"></i>',
}

const onClear = el => {
  el.stopPropagation()
  nextTick(() => {
    emit('update:modelValue', '')
    emit('click:clear', el)
  })
}

const vuetifyTheme = useTheme()
const vuetifyThemesName = Object.keys(vuetifyTheme.themes.value)

// Themes class added to flat-picker component for light and dark support
const updateThemeClassInCalendar = () => {

  // ℹ️ Flatpickr don't render it's instance in mobile and device simulator
  if (!refFlatPicker.value.fp.calendarContainer)
    return
  vuetifyThemesName.forEach(t => {
    refFlatPicker.value.fp.calendarContainer.classList.remove(`v-theme--${ t }`)
  })
  refFlatPicker.value.fp.calendarContainer.classList.add(`v-theme--${ vuetifyTheme.global.name.value }`)
}

watch(() => configStore.theme, updateThemeClassInCalendar)
onMounted(() => {
  updateThemeClassInCalendar()
})

const emitModelValue = val => {
  emit('update:modelValue', val)
}

watch(() => props, () => {
  fieldProps.value = VField.filterProps(props)
  inputProps.value = VInput.filterProps(props)
}, {
  deep: true,
  immediate: true,
})

const elementId = computed(() => {
  const _elementIdToken = fieldProps.value.id || fieldProps.value.label || inputProps.value.id
  const _id = useId()
  
  return _elementIdToken ? `app-picker-field-${ _elementIdToken }` : _id
})
</script>

<template>
  <div class="app-picker-field">
    <!-- v-input -->
    <VLabel
      v-if="fieldProps.label"
      class="mb-1 text-body-2"
      :for="elementId"
      :text="fieldProps.label"
    />

    <VInput
      v-bind="{ ...inputProps, ...rootAttrs }"
      :model-value="modelValue"
      :hide-details="props.hideDetails"
      :class="[{
        'v-text-field--prefixed': props.prefix,
        'v-text-field--suffixed': props.suffix,
        'v-text-field--flush-details': ['plain', 'underlined'].includes(props.variant),
      }, props.class]"
      class="position-relative v-text-field"
      :style="props.style"
    >
      <template #default="{ id, isDirty, isValid, isDisabled, isReadonly, validate }">
        <!-- v-field -->
        <VField
          v-bind="{ ...fieldProps, label: undefined }"
          :id="id.value"
          role="textbox"
          :active="focused || isDirty.value || isCalendarOpen"
          :focused="focused || isCalendarOpen"
          :dirty="isDirty.value || props.dirty"
          :error="isValid.value === false"
          :disabled="isDisabled.value"
          @click:clear="onClear"
        >
          <template #default="{ props: vFieldProps }">
            <div v-bind="vFieldProps">
              <!-- flat-picker  -->
              <FlatPickr
                v-if="!isInlinePicker"
                v-bind="compAttrs"
                ref="refFlatPicker"
                :model-value="modelValue"
                :placeholder="props.placeholder"
                :readonly="isReadonly.value"
                class="flat-picker-custom-style h-100 w-100"
                :disabled="isReadonly.value"
                @on-open="isCalendarOpen = true"
                @on-close="isCalendarOpen = false; validate()"
                @update:model-value="emitModelValue"
              />

              <!-- simple input for inline prop -->
              <input
                v-if="isInlinePicker"
                :value="modelValue"
                :placeholder="props.placeholder"
                :readonly="isReadonly.value"
                class="flat-picker-custom-style h-100 w-100"
                type="text"
              >
            </div>
          </template>
        </VField>
      </template>
    </VInput>

    <!-- flat picker for inline props -->
    <FlatPickr
      v-if="isInlinePicker"
      v-bind="compAttrs"
      ref="refFlatPicker"
      :model-value="modelValue"
      @update:model-value="emitModelValue"
      @on-open="isCalendarOpen = true"
      @on-close="isCalendarOpen = false"
    />
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/mixins" as templateMixins;

/* stylelint-disable no-descending-specificity */
@use "flatpickr/dist/flatpickr.css";
@use "@core-scss/base/mixins";

.flat-picker-custom-style {
  position: absolute;
  color: inherit;
  inline-size: 100%;
  inset: 0;
  outline: none;
  padding-block: 0;
  padding-inline: var(--v-field-padding-start);
}

$heading-color: rgba(var(--v-theme-on-background), var(--v-high-emphasis-opacity));
$body-color: rgba(var(--v-theme-on-background), var(--v-high-emphasis-opacity));
$disabled-color: rgba(var(--v-theme-on-background), var(--v-disabled-opacity));

// hide the input when your picker is inline
input[altinputclass="inlinePicker"] {
  display: none;
}

.flatpickr-time input.flatpickr-hour {
  font-weight: 400;
}

.flatpickr-calendar {
  @include mixins.elevation(6);

  background-color: rgb(var(--v-theme-surface));
  inline-size: 16.875rem;

  .flatpickr-day:focus {
    border-color: rgba(var(--v-border-color), var(--v-border-opacity));
    background: rgba(var(--v-border-color), var(--v-border-opacity));
  }

  .flatpickr-rContainer {
    .flatpickr-weekdays {
      block-size: 1.25rem;
      padding-inline: 0.5625rem;
    }

    .flatpickr-days {
      min-inline-size: 16.875rem;

      .dayContainer {
        justify-content: center !important;
        inline-size: 16.875rem;
        min-inline-size: 16.875rem;
        padding-block: 0.75rem 0.5rem;

        .flatpickr-day {
          block-size: 2.25rem;
          font-size: 0.9375rem;
          line-height: 2.25rem;
          margin-block-start: 0 !important;
          max-inline-size: 2.25rem;
        }
      }
    }
  }

  .flatpickr-day {
    color: $body-color;

    &.today {
      &:not(.selected) {
        border: none !important;
        background: rgba(var(--v-theme-primary), 0.24);
        color: rgb(var(--v-theme-primary));
      }

      &:hover {
        border: none !important;
        background: rgba(var(--v-theme-primary), 0.24);
        color: rgb(var(--v-theme-primary));
      }
    }

    &.selected,
    &.selected:hover {
      border-color: rgb(var(--v-theme-primary));
      background: rgb(var(--v-theme-primary));
      color: rgb(var(--v-theme-on-primary));

      @include templateMixins.custom-elevation(var(--v-theme-primary), "sm");
    }

    &.inRange,
    &.inRange:hover {
      border: none;
      background: rgba(var(--v-theme-primary), var(--v-activated-opacity)) !important;
      box-shadow: none !important;
      color: rgb(var(--v-theme-primary));
    }

    &.startRange {
      @include templateMixins.custom-elevation(var(--v-theme-primary), "sm");
    }

    &.endRange {
      @include templateMixins.custom-elevation(var(--v-theme-primary), "sm");
    }

    &.startRange,
    &.endRange,
    &.startRange:hover,
    &.endRange:hover {
      border-color: rgb(var(--v-theme-primary));
      background: rgb(var(--v-theme-primary));
      color: rgb(var(--v-theme-on-primary));
    }

    &.selected.startRange + .endRange:not(:nth-child(7n + 1)),
    &.startRange.startRange + .endRange:not(:nth-child(7n + 1)),
    &.endRange.startRange + .endRange:not(:nth-child(7n + 1)) {
      box-shadow: -10px 0 0 rgb(var(--v-theme-primary));
    }

    &.flatpickr-disabled,
    &.prevMonthDay:not(.startRange,.inRange),
    &.nextMonthDay:not(.endRange,.inRange) {
      opacity: var(--v-disabled-opacity);
    }

    &:hover {
      border-color: transparent;
      background: rgba(var(--v-theme-on-surface), 0.06);
    }
  }

  .flatpickr-weekday {
    color: $heading-color;
    font-size: 0.8125rem;
    font-weight: 400;
    inline-size: 2.25rem;
    line-height: 1.25rem;
  }

  .flatpickr-days {
    inline-size: 16.875rem;
  }

  &::after,
  &::before {
    display: none;
  }

  .flatpickr-months {
    .flatpickr-prev-month,
    .flatpickr-next-month {
      color: rgba(var(--v-theme-on-surface), var(--v-high-emphasis-opacity));
      fill: $body-color;

      &:hover {
        color: rgba(var(--v-theme-on-surface), var(--v-high-emphasis-opacity));
      }

      &:hover i,
      &:hover svg {
        fill: $body-color;
      }
    }
  }

  .flatpickr-current-month span.cur-month {
    font-weight: 300;
  }

  &.open {
    // Open calendar above overlay
    z-index: 2401;
  }

  &.hasTime.open {
    .flatpickr-innerContainer + .flatpickr-time {
      block-size: auto;
      border-block-start: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
    }

    .flatpickr-time {
      border-block-start: none;
    }

    .flatpickr-hour,
    .flatpickr-minute,
    .flatpickr-am-pm {
      font-size: 0.9375rem;
    }
  }
}

.v-theme--dark .flatpickr-calendar {
  box-shadow: 0 3px 14px 0 rgb(15 20 34 / 38%);
}

// Time picker hover & focus bg color
.flatpickr-time input:hover,
.flatpickr-time .flatpickr-am-pm:hover,
.flatpickr-time input:focus,
.flatpickr-time .flatpickr-am-pm:focus {
  background: transparent;
}

// Time picker
.flatpickr-time {
  .flatpickr-am-pm,
  .flatpickr-time-separator,
  input {
    color: $body-color;
  }

  .numInputWrapper {
    span {
      &.arrowUp {
        &::after {
          border-block-end-color: rgb(var(--v-border-color));
        }
      }

      &.arrowDown {
        &::after {
          border-block-start-color: rgb(var(--v-border-color));
        }
      }
    }
  }
}

//  Added bg color for flatpickr input only as it has default readonly attribute
.flatpickr-input[readonly],
.flatpickr-input ~ .form-control[readonly],
.flatpickr-human-friendly[readonly] {
  background-color: inherit;
}

// week sections
.flatpickr-weekdays {
  margin-block: 0.375rem;
}

// Month and year section
.flatpickr-current-month {
  .flatpickr-monthDropdown-months {
    appearance: none;
  }

  .flatpickr-monthDropdown-months,
  .numInputWrapper {
    padding: 2px;
    border-radius: 4px;
    color: $heading-color;
    font-size: 0.9375rem;
    font-weight: 400;
    line-height: 1.375rem;
    transition: all 0.15s ease-out;

    span {
      display: none;
    }

    .flatpickr-monthDropdown-month {
      background-color: rgb(var(--v-theme-surface));
    }

    .numInput.cur-year {
      font-weight: 400;
    }
  }
}

.flatpickr-day.flatpickr-disabled,
.flatpickr-day.flatpickr-disabled:hover {
  color: $body-color;
}

.flatpickr-months {
  padding-block: 0.75rem;
  padding-inline: 1rem;

  .flatpickr-prev-month,
  .flatpickr-next-month {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    border-radius: 5rem;
    background: rgba(var(--v-theme-on-surface), var(--v-selected-opacity));
    block-size: 1.875rem;
    inline-size: 1.875rem;
    inset-block-start: 15px !important;

    &.flatpickr-disabled {
      display: inline;
      opacity: var(--v-disabled-opacity);
      pointer-events: none;
    }
  }

  .flatpickr-next-month {
    inset-inline-end: 1.05rem !important;
  }

  .flatpickr-prev-month {
    /* stylelint-disable-next-line liberty/use-logical-spec */
    right: 3.65rem;
    left: unset !important;
  }

  .flatpickr-month {
    display: flex;
    align-items: center;
    block-size: 2.125rem;

    .flatpickr-current-month {
      display: flex;
      align-items: center;
      padding: 0;
      block-size: 1.75rem;
      inset-inline-start: 0;
      text-align: start;
    }
  }
}
</style>
