<script setup>
definePage({
  meta: {
    layout: "default",
    action: "read",
    subject: "Listing",
  },
});

// Redirect to the existing listing page
onMounted(() => {
  $router.push("/listing");
});
</script>

<template>
  <div class="d-flex align-center justify-center" style="min-height: 400px">
    <VCard class="pa-6 text-center">
      <VIcon size="48" icon="tabler-loader" class="mb-4" />
      <h3 class="text-h5 font-weight-bold mb-2">Redirecting to Listing Page</h3>
      <p class="text-body-2 text-medium-emphasis">Please wait...</p>
    </VCard>
  </div>
</template>
