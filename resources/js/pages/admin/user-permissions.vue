<template>
  <div>
    <VCard>
      <VCardTitle>User Permissions Management</VCardTitle>
      <VCardText>
        <p>User permissions management page content will go here.</p>
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
