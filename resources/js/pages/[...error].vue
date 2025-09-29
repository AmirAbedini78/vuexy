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
    <!-- Warning Icon -->
    <div class="ee-404-icon">
      <VIcon icon="tabler-alert-triangle" size="48" color="warning" />
    </div>
    
    <!-- Error Code -->
    <div class="ee-404-code">404</div>
    
    <!-- Main Message -->
    <div class="ee-404-title">Page Not Found!</div>
    
    <!-- Description -->
    <div class="ee-404-description">we couldn't find the page you are looking for</div>
    
    <!-- Action Buttons -->
    <div class="ee-404-actions">
      <VBtn
        variant="outlined"
        color="default"
        to="/admin/dashboard"
        class="back-btn"
      >
        Back To Dashboard
      </VBtn>
      <VBtn
        color="warning"
        href="https://explorerelite.com/support/"
        target="_blank"
        class="support-btn"
      >
        Get Support
      </VBtn>
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
  gap: 24px;
  padding: 20px;
  background: #ffffff;
}

.ee-404-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 8px;
}

.ee-404-code {
  font-size: 72px;
  font-weight: 700;
  color: #000000;
  line-height: 1;
  margin: 0;
  font-family: 'Anton', sans-serif;
  font-weight: 300;
}

.ee-404-title {
  font-size: 32px;
  font-weight: 700;
  color: #000000;
  margin: 0;
  font-family: 'Anton', sans-serif;
  font-weight: 300;
}

.ee-404-description {
  font-size: 18px;
  font-weight: 400;
  color: #666666;
  margin: 0;
  line-height: 1.4;
}

.ee-404-actions {
  display: flex;
  gap: 16px;
  margin-top: 32px;
  flex-wrap: wrap;
  justify-content: center;
}

.back-btn {
  background: #ffffff !important;
  color: #000000 !important;
  border: 2px solid #000000 !important;
  border-radius: 8px !important;
  padding: 12px 24px !important;
  font-weight: 500 !important;
  text-transform: none !important;
  min-width: 160px !important;
}

.back-btn:hover {
  background: #f5f5f5 !important;
  border-color: #333333 !important;
}

.support-btn {
  background: linear-gradient(135deg, #ff9800, #f57c00) !important;
  color: #ffffff !important;
  border: none !important;
  border-radius: 8px !important;
  padding: 12px 24px !important;
  font-weight: 500 !important;
  text-transform: none !important;
  min-width: 160px !important;
  box-shadow: 0 2px 8px rgba(255, 152, 0, 0.3) !important;
}

.support-btn:hover {
  background: linear-gradient(135deg, #f57c00, #ef6c00) !important;
  box-shadow: 0 4px 12px rgba(255, 152, 0, 0.4) !important;
}

/* Responsive Design */
@media (max-width: 768px) {
  .ee-404-code {
    font-size: 56px;
  }
  
  .ee-404-title {
    font-size: 24px;
  }
  
  .ee-404-description {
    font-size: 16px;
  }
  
  .ee-404-actions {
    flex-direction: column;
    align-items: center;
  }
  
  .back-btn,
  .support-btn {
    width: 100%;
    max-width: 280px;
  }
}
</style>
