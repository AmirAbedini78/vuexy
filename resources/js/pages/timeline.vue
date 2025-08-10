<template>
  <div>
    <Timeline />
  </div>
</template>

<script setup>
import Timeline from "@/views/pages/registration/Timeline.vue";

definePage({
  meta: {
    layout: "default",
    layoutWrapperClasses: "hide-vertical-nav",
    action: "read",
    subject: "Welcome",
    public: true,
  },
});
</script>
