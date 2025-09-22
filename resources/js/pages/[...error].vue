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
    <div class="ee-404-icon">⚠️</div>
    <div class="ee-404-code">404</div>
    <div class="ee-404-title">Page Not Found!</div>
    <div class="ee-404-desc">we couldn't find the page you are looking for</div>
    <div class="ee-404-actions">
      <VBtn variant="outlined" to="/">Back To Dashboard</VBtn>
      <VBtn
        color="warning"
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
  gap: 12px;
}
.ee-404-icon {
  font-size: 56px;
  margin-bottom: 8px;
}
.ee-404-code {
  font-family: "Anton", sans-serif;
  font-size: 96px;
  line-height: 1;
  color: #111;
}
.ee-404-title {
  font-family: "Anton", sans-serif;
  font-size: 28px;
  color: #111;
}
.ee-404-desc {
  color: #6b7280;
  margin: 6px 0 18px;
}
.ee-404-actions {
  display: flex;
  gap: 12px;
}
</style>
