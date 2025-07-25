<script setup>
import CustomRadiosWithIcon from "@/@core/components/app-form-elements/CustomRadiosWithIcon.vue";
import DemoFormWizardNumberedModernBasicMultiDate from "@/views/demos/forms/form-wizard/form-wizard-numbered/DemoFormWizardNumberedModernBasicMultiDate.vue";
import DemoFormWizardNumberedModernBasicOpenDate from "@/views/demos/forms/form-wizard/form-wizard-numbered/DemoFormWizardNumberedModernBasicOpenDate.vue";
import DemoFormWizardNumberedModernBasicSingleDate from "@/views/demos/forms/form-wizard/form-wizard-numbered/DemoFormWizardNumberedModernBasicSingleDate.vue";
import { ref } from "vue";

definePage({
  meta: {
    layout: "default", // فقط نوار بالا، سایدبار مخفی می‌شود
    public: true, // Make this page public for testing
  },
});

const radioContent = [
  {
    title: "Single Date",
    desc: "The Listing has a Specific Start and End Date",
    value: "single-date",
    img: {
      active: "/images/4svg/wired-outline-1-calendar-active.png",
      inactive: "/images/4svg/wired-outline-1-calendar-inactive.png",
    },
  },
  {
    title: "Multi Date",
    desc: "The Listing can be Booked for any Date within a Certain Period",
    value: "multi-date",
    img: {
      active: "/images/4svg/wired-outline-2-calendar-active.png",
      inactive: "/images/4svg/wired-outline-2-calendar-inactive.png",
    },
  },
  {
    title: "Open Date",
    desc: "The Listing can be Booked for any Date within a Certain Period",
    value: "open-date",
    img: {
      active: "/images/4svg/wired-outline-3-calendar-active.png",
      inactive: "/images/4svg/wired-outline-3-calendar-inactive.png",
    },
  },
  {
    title: "Other",
    desc: "Listing that doesn't Fit the Above Categories",
    value: "other",
    img: {
      active: "/images/4svg/wired-outline-4-calendar-active.png",
      inactive: "/images/4svg/wired-outline-4-calendar-inactive.png",
    },
  },
];

const selectedRadio = ref("single-date");
const showWizard = ref(false);
const selectedWizardType = ref("");

function handleNext() {
  if (selectedRadio.value === "single-date") {
    selectedWizardType.value = "single-date";
    showWizard.value = true;
  } else if (selectedRadio.value === "multi-date") {
    selectedWizardType.value = "multi-date";
    showWizard.value = true;
  } else if (selectedRadio.value === "open-date") {
    selectedWizardType.value = "open-date";
    showWizard.value = true;
  } else if (selectedRadio.value === "other") {
    alert("Other option selected. This will be implemented later.");
  }
}
</script>

<template>
  <template v-if="!showWizard">
    <div
      class="d-flex flex-column align-center justify-center listing-content"
      style="min-height: 80vh"
    >
      <div class="listing-header text-center mb-4">
        <h2 class="listing-title">
          Hey <span class="provider-name">[Provider Name]</span>, Let's Add a
          New Explorer Elite Listing
        </h2>
        <div class="listing-subtitle mt-1 mb-6">
          Please Select Your Listing Type Here
        </div>
      </div>
      <div class="d-flex justify-center align-center w-100">
        <div style="max-width: 700px; gap: 20rem !important" class="d-flex">
          <CustomRadiosWithIcon
            v-model:selected-radio="selectedRadio"
            :radio-content="radioContent"
            :grid-column="{ md: 6, cols: 12 }"
            class="listing-radios"
          />
        </div>
      </div>
      <div class="d-flex justify-center mt-10 mb-8">
        <VBtn
          class="listing-next-btn"
          color="dark"
          size="large"
          @click="handleNext"
        >
          Next
        </VBtn>
      </div>
    </div>
  </template>
  <template v-else>
    <div class="wizard-center-container">
      <DemoFormWizardNumberedModernBasicSingleDate
        v-if="selectedWizardType === 'single-date'"
      />
      <DemoFormWizardNumberedModernBasicMultiDate
        v-else-if="selectedWizardType === 'multi-date'"
      />
      <DemoFormWizardNumberedModernBasicOpenDate
        v-else-if="selectedWizardType === 'open-date'"
      />
    </div>
  </template>
</template>

<style>
/* مخفی کردن سایدبار فقط در این صفحه */
.layout-wrapper .vertical-nav-wrapper {
  display: none !important;
}
@media (min-width: 1280px) {
  .layout-wrapper .layout-content-wrapper {
    padding-left: 0 !important;
  }
}

.listing-content {
  background: #fff;
  border-radius: 6px;
  box-shadow: 0 2px 16px 0 rgba(44, 44, 44, 0.08);
  padding: 3rem 2rem 2.5rem 2rem;
  max-width: 1050px;
  margin-left: 110px;
  min-height: 80vh;
}

.listing-header .listing-title {
  font-family: "Anton", sans-serif;
  font-size: 2rem;
  font-weight: 700;
  color: #222;
}
.listing-header .provider-name {
  color: #ec8d22;
}
.listing-header .listing-subtitle {
  font-size: 1.1rem;
  color: #444;
  font-family: "Karla", sans-serif;
}

.listing-next-btn {
  min-width: 140px;
  min-height: 40px;
  font-size: 1.1rem;
  border-radius: 8px;
  font-weight: 700;
  background: #111 !important;
  color: #fff !important;
  box-shadow: 0 2px 8px 0 rgba(44, 44, 44, 0.08);
  transition: background 0.2s;
}

.wizard-center-container {
  max-width: 100%;
  margin: 48px auto 0 auto;
  padding: 0 16px;
  display: flex;
  flex-direction: column;
  align-items: center;
}
</style>
