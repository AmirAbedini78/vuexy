<template>
  <div>
    <VCard>
      <VCardTitle>User Roles Management</VCardTitle>
      <VCardText>
        <p>User roles management page content will go here.</p>
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
