<script setup>
import CustomRadiosWithIcon from "@/@core/components/app-form-elements/CustomRadiosWithIcon.vue";
import DemoFormWizardNumberedModernBasic from "@/views/demos/forms/form-wizard/form-wizard-numbered/DemoFormWizardNumberedModernBasic.vue";
import DemoFormWizardNumberedModernBasicCompany from "@/views/demos/forms/form-wizard/form-wizard-numbered/DemoFormWizardNumberedModernBasicCompany.vue";
import { ref } from "vue";

definePage({
  meta: {
    layout: "default", // فقط نوار بالا، سایدبار مخفی می‌شود
    action: "read",
    subject: "AclDemo",
  },
});

const radioContent = [
  {
    title: "Register as Individual",
    desc: "Guides working solo, freelancers or a single person offering experiences",
    value: "individual",
    icon: {
      icon: "tabler-user",
      size: "32",
    },
  },
  {
    title: "Register as Company",
    desc: "Businesses with multiple guides or a registered entity",
    value: "company",
    icon: {
      icon: "tabler-users",
      size: "32",
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
  <div
    class="d-flex flex-column align-center justify-center"
    style="min-height: 80vh"
  >
    <template v-if="!showWizard">
      <div class="d-flex justify-center align-center w-100">
        <div style="max-width: 600px; gap: 20rem !important" class="d-flex">
          <CustomRadiosWithIcon
            v-model:selected-radio="selectedRadio"
            :radio-content="radioContent"
            :grid-column="{ sm: '6', cols: '12' }"
            class="access-control-radios"
          />
        </div>
      </div>
      <div class="d-flex justify-center mt-10 mb-8">
        <VBtn color="dark" size="large" @click="handleNext"> Next </VBtn>
      </div>
    </template>
    <template v-else>
      <DemoFormWizardNumberedModernBasic
        v-if="selectedWizardType === 'individual'"
      />
      <DemoFormWizardNumberedModernBasicCompany
        v-else-if="selectedWizardType === 'company'"
      />
    </template>
  </div>
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

/* Custom styles for access control radio buttons */
.access-control-radios .custom-radio-icon h6 {
  font-family: "Anton", sans-serif !important;
  font-size: 32 !important;
  font-weight: 400 !important;
  color: #2f2b3d !important;
}

.access-control-radios .custom-radio-icon .text-body-2 {
  font-family: "Karla", sans-serif !important;
  font-size: 15 !important;
  font-weight: 400 !important;
  color: rgba(var(--v-theme-on-surface), 0.7) !important;
}
</style>
