<script setup>
import CustomRadiosWithIcon from "@/@core/components/app-form-elements/CustomRadiosWithIcon.vue";
import DemoFormWizardNumberedModernBasic from "@/views/demos/forms/form-wizard/form-wizard-numbered/DemoFormWizardNumberedModernBasic.vue";
import DemoFormWizardNumberedModernBasicCompany from "@/views/demos/forms/form-wizard/form-wizard-numbered/DemoFormWizardNumberedModernBasicCompany.vue";
import { ref } from "vue";

definePage({
  meta: {
    layout: "default", // Use default layout with navbar
    action: "read",
    subject: "AclDemo",
  },
});

// Sidebar management is now handled globally by route watcher

const radioContent = [
  {
    title: "Register as Individual",
    desc: "For guides working solo, freelancers or a single person offering experiences",
    value: "individual",
    img: {
      active: "/images/4svg/wired-outline-21-avatar-hover-jumping.png",
      inactive: "/images/4svg/wired-outline-21-avatar-hover-jumping-black.png",
    },
  },
  {
    title: "Register as Company",
    desc: "For businesses with multiple guides or a registered entity",
    value: "company",
    img: {
      active:
        "/images/4svg/wired-outline-314-three-avatars-icon-calm-hover-nodding.png",
      inactive:
        "/images/4svg/wired-outline-314-three-avatars-icon-calm-hover-nodding-black.png",
    },
  },
];
const selectedRadio = ref("individual");
const showWizard = ref(false);
const selectedWizardType = ref("");

function handleNext() {
  if (selectedRadio.value === "individual") {
    selectedWizardType.value = "individual";
    showWizard.value = true;
  } else if (selectedRadio.value === "company") {
    selectedWizardType.value = "company";
    showWizard.value = true;
  }
}
</script>

<template>
  <template v-if="!showWizard">
    <div
      class="d-flex flex-column align-center justify-center access-control-content"
      style="min-height: 80vh"
    >
      <div class="d-flex justify-center align-center w-100">
        <div style="max-width: 550px; gap: 20rem !important" class="d-flex">
          <CustomRadiosWithIcon
            v-model:selected-radio="selectedRadio"
            :radio-content="radioContent"
            :grid-column="{ sm: '6', cols: '12' }"
            class="access-control-radios"
          />
        </div>
      </div>
      <div class="d-flex justify-center mt-10 mb-8">
        <VBtn
          class="access-control-btn"
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
      <DemoFormWizardNumberedModernBasic
        v-if="selectedWizardType === 'individual'"
      />
      <DemoFormWizardNumberedModernBasicCompany
        v-else-if="selectedWizardType === 'company'"
      />
    </div>
  </template>
</template>

<style>
/* Removed sidebar hiding CSS - sidebar will be managed by layout configuration */

/* Custom styles for access control radio buttons */
.access-control-radios .custom-radio-icon h6 {
  font-family: "Anton", sans-serif !important;
  font-size: 20px !important;
  font-weight: 300 !important;
  color: #444151 !important;
}

.access-control-radios .custom-radio-icon .text-body-2 {
  font-family: "Karla", sans-serif !important;
  font-size: 12px !important;
  font-weight: 400 !important;
  color: #444151;
}

.access-control-content {
  background: #fff;
  border-radius: 6px;
  box-shadow: 0 2px 16px 0 rgba(44, 44, 44, 0.08);
  padding: 3rem 2rem 2.5rem 2rem;
  max-width: 1050px;
  margin: 0 auto;
  min-height: 80vh;
}

.access-control-btn {
  min-width: 127px;
  min-height: 38px;
  font-size: 12px;
  background: #000000 !important;
  color: #fff !important;
  border-radius: 8px;
  font-weight: 700;
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
