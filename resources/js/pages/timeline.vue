<template>
  <div>
    <Timeline />
  </div>
</template>

<script setup>
import Timeline from "@/views/pages/registration/Timeline.vue";
const route = useRoute();

if (!route.params.type) route.params.type = "individual";
if (!route.params.id) route.params.id = "1";

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
