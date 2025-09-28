<script setup>
import { useGenerateImageVariant } from "@core/composable/useGenerateImageVariant";
import miscMaskDark from "@images/pages/misc-mask-dark.png";
import miscMaskLight from "@images/pages/misc-mask-light.png";

const authThemeMask = useGenerateImageVariant(miscMaskLight, miscMaskDark);

definePage({
  alias: "/pages/misc/not-found/:error(.*)",
  meta: {
    layout: "blank",
    public: true,
  },
});
</script>

<template>
  <div class="ee-404">
    <img class="ee-404-img" src="/images/4svg/404.png" alt="404 Not Found" />
    <div class="ee-404-actions">
      <VBtn color="warning" to="/admin/dashboard">Dashboard</VBtn>
      <VBtn
        variant="outlined"
        href="https://explorerelite.com/support/"
        target="_blank"
        >Get Support</VBtn
      >
    </div>
  </div>
</template>

<style scoped>
.ee-404 {
  min-height: 80vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  gap: 16px;
}
.ee-404-img {
  max-width: 420px;
  width: 90%;
  height: auto;
  display: block;
}
.ee-404-actions {
  display: flex;
  gap: 12px;
  margin-top: 8px;
}
</style>
