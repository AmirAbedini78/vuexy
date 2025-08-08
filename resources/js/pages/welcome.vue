<template>
  <div>
    <VCard>
      <VCardTitle class="text-h4 mb-4">
        <VIcon icon="tabler-heart" color="primary" class="me-2" />
        Welcome to Explorer Elite!
      </VCardTitle>
      <VCardText>
        <p class="text-body-1 mb-4">
          Welcome to your adventure dashboard! Here you can explore amazing
          events, create listings, and manage your bookings.
        </p>

        <VRow>
          <VCol cols="12" md="6">
            <VCard variant="outlined" class="h-100">
              <VCardTitle class="text-h6">
                <VIcon icon="tabler-calendar" color="success" class="me-2" />
                Explore Events
              </VCardTitle>
              <VCardText>
                Discover amazing events and adventures waiting for you.
              </VCardText>
              <VCardActions>
                <VBtn
                  color="primary"
                  variant="outlined"
                  @click="$router.push('/all-events')"
                >
                  View Events
                </VBtn>
              </VCardActions>
            </VCard>
          </VCol>

          <VCol cols="12" md="6">
            <VCard variant="outlined" class="h-100">
              <VCardTitle class="text-h6">
                <VIcon icon="tabler-list" color="info" class="me-2" />
                Browse Listings
              </VCardTitle>
              <VCardText>
                Check out all available listings and find your next adventure.
              </VCardText>
              <VCardActions>
                <VBtn
                  color="primary"
                  variant="outlined"
                  @click="$router.push('/listing')"
                >
                  View Listings
                </VBtn>
              </VCardActions>
            </VCard>
          </VCol>
        </VRow>
      </VCardText>
    </VCard>
  </div>
</template>

<script setup>
definePage({
  meta: {
    layout: "default",
    action: "read",
    subject: "Welcome",
  },
});
</script>
