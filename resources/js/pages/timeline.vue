<template>
  <div>
    <!-- Import and use the existing Timeline component with default parameters -->
    <Timeline />
  </div>
</template>

<script setup>
// Import the existing Timeline component
import Timeline from "@/views/pages/registration/Timeline.vue";

// Set default route params for the timeline
const route = useRoute();

// Override route params with default values if not provided
if (!route.params.type) {
  route.params.type = "individual";
}
if (!route.params.id) {
  route.params.id = "1";
}

definePage({
  meta: {
    layout: "default",
    action: "read",
    subject: "Welcome",
    public: true,
  },
});
</script>
