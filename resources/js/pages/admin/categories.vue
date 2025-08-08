<template>
  <div>
    <VCard>
      <VCardTitle>Categories Management</VCardTitle>
      <VCardText>
        <p>Categories management page content will go here.</p>
      </VCardText>
    </VCard>
  </div>
</template>

<script setup>
definePage({
  meta: {
    layout: "default",
    requiresAuth: true,
    requiresAdmin: true,
  },
});
</script>
