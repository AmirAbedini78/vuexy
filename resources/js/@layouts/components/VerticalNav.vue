<script setup>
import { layoutConfig } from "@layouts";
import {
  VerticalNavGroup,
  VerticalNavLink,
  VerticalNavSectionTitle,
} from "@layouts/components";
import { useLayoutConfigStore } from "@layouts/stores/config";
import { injectionKeyIsVerticalNavHovered } from "@layouts/symbols";
import { PerfectScrollbar } from "vue3-perfect-scrollbar";
import { VNodeRenderer } from "./VNodeRenderer";

const props = defineProps({
  tag: {
    type: null,
    required: false,
    default: "aside",
  },
  navItems: {
    type: null,
    required: true,
  },
  isOverlayNavActive: {
    type: Boolean,
    required: true,
  },
  toggleIsOverlayNavActive: {
    type: Function,
    required: true,
  },
});

const refNav = ref();
const isHovered = useElementHover(refNav);

provide(injectionKeyIsVerticalNavHovered, isHovered);

const configStore = useLayoutConfigStore();

const resolveNavItemComponent = (item) => {
  if ("heading" in item) return VerticalNavSectionTitle;
  if ("children" in item) return VerticalNavGroup;

  return VerticalNavLink;
};

/*ℹ️ Close overlay side when route is changed
Close overlay vertical nav when link is clicked
*/
const route = useRoute();

watch(
  () => route.name,
  () => {
    // Only close overlay nav on mobile devices
    if (configStore.isLessThanOverlayNavBreakpoint) {
      props.toggleIsOverlayNavActive(false);
    }
  }
);

const isVerticalNavScrolled = ref(false);
const updateIsVerticalNavScrolled = (val) =>
  (isVerticalNavScrolled.value = val);

const handleNavScroll = (evt) => {
  isVerticalNavScrolled.value = evt.target.scrollTop > 0;
};

const hideTitleAndIcon = configStore.isVerticalNavMini(isHovered);
</script>

<template>
  <Component
    :is="props.tag"
    ref="refNav"
    data-allow-mismatch
    class="layout-vertical-nav"
    :class="[
      {
        'overlay-nav': configStore.isLessThanOverlayNavBreakpoint,
        hovered: isHovered,
        visible: isOverlayNavActive,
        scrolled: isVerticalNavScrolled,
        collapsed:
          configStore.isVerticalNavCollapsed &&
          !configStore.isLessThanOverlayNavBreakpoint,
      },
    ]"
  >
    <!-- 👉 Header -->
    <div class="nav-header">
      <slot name="nav-header">
        <RouterLink to="/" class="app-logo app-title-wrapper">
          <VNodeRenderer
            :nodes="layoutConfig.app.logo"
            :class="{
              'logo-collapsed':
                configStore.isVerticalNavCollapsed && !isHovered,
              'logo-expanded': !configStore.isVerticalNavCollapsed || isHovered,
            }"
          />

          <!-- App Title - Removed as requested -->
          <!-- <Transition name="vertical-nav-app-title">
            <div v-show="!hideTitleAndIcon" class="app-title">
              <div class="app-title-line">Explorer</div>
              <div class="app-title-line">Elite</div>
            </div>
          </Transition> -->
        </RouterLink>

        <!-- Collapse toggle button for desktop -->
        <IconBtn
          v-if="!configStore.isLessThanOverlayNavBreakpoint"
          class="nav-collapse-btn d-none d-lg-inline-flex"
          size="small"
          @click="
            configStore.isVerticalNavCollapsed =
              !configStore.isVerticalNavCollapsed
          "
        >
          <VIcon
            size="16"
            :icon="
              configStore.isVerticalNavCollapsed
                ? 'tabler-chevron-right'
                : 'tabler-chevron-left'
            "
          />
        </IconBtn>
      </slot>
    </div>
    <slot name="before-nav-items">
      <div class="vertical-nav-items-shadow" />
    </slot>
    <slot
      name="nav-items"
      :update-is-vertical-nav-scrolled="updateIsVerticalNavScrolled"
    >
      <PerfectScrollbar
        :key="configStore.isAppRTL"
        tag="ul"
        class="nav-items"
        :options="{ wheelPropagation: false }"
        @ps-scroll-y="handleNavScroll"
      >
        <Component
          :is="resolveNavItemComponent(item)"
          v-for="(item, index) in navItems"
          :key="index"
          :item="item"
        />
      </PerfectScrollbar>
    </slot>
    <slot name="after-nav-items" />

    <!-- 👉 Bottom Button -->
    <div class="nav-bottom-button">
      <VBtn
        variant="outlined"
        color="primary"
        class="get-beyond-button"
        @click="() => $router.push('/get-support')"
      >
        <VIcon icon="tabler-ship" color="warning" class="me-2" />
        <span v-show="!hideTitleAndIcon" class="button-text">
          Get Support
        </span>
      </VBtn>
    </div>
  </Component>
</template>

<style lang="scss" scoped>
.app-logo {
  display: flex;
  align-items: center;
  column-gap: 0.75rem;

  .app-title {
    display: flex;
    flex-direction: column;
    line-height: 1.2;

    .app-title-line {
      font-size: 1.1rem;
      font-weight: 700;
      letter-spacing: 0.25px;
      text-transform: capitalize;
      color: rgb(var(--v-theme-on-surface));
    }
  }

  // Dynamic logo sizing
  .logo-collapsed {
    transform: scale(0.7) translateX(-45px);
    transition: transform 0.3s ease;
  }

  .logo-expanded {
    transform: scale(1) translateX(0);
    transition: transform 0.3s ease;
  }
}

.nav-bottom-button {
  padding: 1rem;
  border-top: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
  margin-top: auto;

  .get-beyond-button {
    width: 100%;
    justify-content: flex-start;
    border-radius: 8px;
    font-weight: 600;
    text-transform: none;
    letter-spacing: 0.5px;

    .button-text {
      font-size: 0.875rem;
      font-weight: 600;
    }
  }
}

// Transition for app title
.vertical-nav-app-title-enter-active,
.vertical-nav-app-title-leave-active {
  transition: opacity 0.3s ease;
}

.vertical-nav-app-title-enter-from,
.vertical-nav-app-title-leave-to {
  opacity: 0;
}
</style>

<style lang="scss">
@use "@configured-variables" as variables;
@use "@layouts/styles/mixins";

// 👉 Vertical Nav
.layout-vertical-nav {
  position: fixed;
  z-index: variables.$layout-vertical-nav-z-index;
  display: flex;
  flex-direction: column;
  block-size: 100%;
  inline-size: variables.$layout-vertical-nav-width;
  inset-block-start: 0;
  inset-inline-start: 0;
  transition: inline-size 0.25s ease-in-out, box-shadow 0.25s ease-in-out,
    transform 0.25s ease-in-out;
  will-change: transform, inline-size;

  // 👉 Collapsed state - change the entire sidebar width
  &.collapsed {
    inline-size: variables.$layout-vertical-nav-collapsed-width;

    .nav-header {
      justify-content: center;
      padding: 0.75rem 0.5rem;

      .app-logo {
        .logo-collapsed {
          transform: scale(0.8) translateX(0);
        }
      }

      .nav-collapse-btn {
        position: absolute;
        right: -12px;
        top: 50%;
        transform: translateY(-50%);
        background-color: rgb(var(--v-theme-surface));
        border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
        border-radius: 50%;
        width: 24px;
        height: 24px;
        margin-left: 0;
        z-index: 1;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);

        .v-icon {
          font-size: 12px;
        }
      }
    }

    // 👉 Normal state button positioning
    &:not(.collapsed) .nav-header .nav-collapse-btn {
      position: relative;
      right: auto;
      top: auto;
      transform: none;
      background-color: transparent;
      border: none;
      border-radius: 4px;
      width: auto;
      height: auto;
      box-shadow: none;
      margin-left: auto;
      opacity: 0.7;
      transition: all 0.2s;
      flex-shrink: 0;

      &:hover {
        opacity: 1;
        background-color: rgba(var(--v-theme-on-surface), 0.04);
      }

      .v-icon {
        font-size: 16px;
      }
    }
  }

  // 👉 Hide overlay nav when not active (mobile only)
  &.overlay-nav {
    transform: translateX(-100%);
    visibility: hidden;

    &.visible {
      transform: translateX(0);
      visibility: visible;
    }
  }

  .nav-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    position: relative;

    .header-action {
      cursor: pointer;
    }
  }

  .nav-items {
    flex: 1;
    overflow-y: auto;
  }

  .nav-bottom-button {
    flex-shrink: 0;
  }
}
</style>
