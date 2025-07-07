<script setup>
import CustomRadiosWithIcon from "@/@core/components/app-form-elements/CustomRadiosWithIcon.vue";
import { ref } from "vue";

definePage({
  meta: {
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
</script>

<template>
  <div
    class="d-flex flex-column align-center justify-center"
    style="min-height: 80vh"
  >
    <div class="w-60 d-flex justify-center mb-10">
      <CustomRadiosWithIcon
        v-model:selected-radio="selectedRadio"
        :radio-content="radioContent"
        :grid-column="{ sm: '6', cols: '12' }"
      />
    </div>
    <div class="d-flex justify-center mt-10 mb-8">
      <VBtn color="dark" size="large"> Next </VBtn>
    </div>
  </div>
</template>
