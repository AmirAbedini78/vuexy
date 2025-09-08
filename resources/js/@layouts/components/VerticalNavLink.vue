<script setup>
import { layoutConfig } from "@layouts";
import { can } from "@layouts/plugins/casl";
import { useLayoutConfigStore } from "@layouts/stores/config";
import { injectionKeyIsVerticalNavHovered } from "@layouts/symbols";
import {
  getComputedNavLinkToProp,
  getDynamicI18nProps,
  isNavLinkActive,
} from "@layouts/utils";

const props = defineProps({
  item: {
    type: null,
    required: true,
  },
});

const configStore = useLayoutConfigStore();

/*ℹ️ We provided default value `ref(false)` because inject will return `T | undefined`
Docs: https://vuejs.org/api/composition-api-dependency-injection.html#inject
*/
const isVerticalNavHovered = inject(
  injectionKeyIsVerticalNavHovered,
  ref(false)
);

// FIXED: Use the same hover logic as VerticalNavGroup
const shouldShowContent = computed(() => {
  // Always show content when sidebar is expanded
  if (!configStore.isVerticalNavCollapsed) {
    return true;
  }
  
  // When collapsed, show content only when hovered
  return isVerticalNavHovered.value;
});
</script>

<template>
  <li
    v-if="can(item.action, item.subject)"
    class="nav-link"
    :class="{ disabled: item.disable, 'coming-soon': item.comingSoon }"
  >
    <VTooltip v-if="item.comingSoon" text="Coming soon" location="end">
      <template #activator="{ props: tipProps }">
        <Component
          :is="item.to ? 'div' : 'a'"
          v-bind="{ ...tipProps }"
          class="nav-link-inner"
          :class="{
            'router-link-active router-link-exact-active': isNavLinkActive(
              item,
              $router
            ),
          }"
          :title="'Coming soon'"
          @click.prevent
        >
          <Component
            :is="layoutConfig.app.iconRenderer || 'div'"
            v-bind="
              item.icon || layoutConfig.verticalNav.defaultNavItemIconProps
            "
            class="nav-item-icon"
          />
          <TransitionGroup name="transition-slide-x">
            <!-- 👉 Title -->
            <Component
              :is="layoutConfig.app.i18n.enable ? 'i18n-t' : 'span'"
              v-show="shouldShowContent"
              key="title"
              class="nav-item-title"
              v-bind="getDynamicI18nProps(item.title, 'span')"
            >
              {{ item.title }}
            </Component>

            <!-- 👉 Badge -->
            <Component
              :is="layoutConfig.app.i18n.enable ? 'i18n-t' : 'span'"
              v-if="item.badgeContent"
              v-show="shouldShowContent"
              key="badge"
              class="nav-item-badge"
              :class="item.badgeClass"
              v-bind="getDynamicI18nProps(item.badgeContent, 'span')"
            >
              {{ item.badgeContent }}
            </Component>
          </TransitionGroup>
        </Component>
      </template>
    </VTooltip>

    <Component
      v-else
      :is="item.to ? 'RouterLink' : 'a'"
      v-bind="getComputedNavLinkToProp(item)"
      class="nav-link-inner"
      :class="{
        'router-link-active router-link-exact-active': isNavLinkActive(
          item,
          $router
        ),
      }"
    >
      <Component
        :is="layoutConfig.app.iconRenderer || 'div'"
        v-bind="item.icon || layoutConfig.verticalNav.defaultNavItemIconProps"
        class="nav-item-icon"
      />
      <TransitionGroup name="transition-slide-x">
        <!-- 👉 Title -->
        <Component
          :is="layoutConfig.app.i18n.enable ? 'i18n-t' : 'span'"
          v-show="shouldShowContent"
          key="title"
          class="nav-item-title"
          v-bind="getDynamicI18nProps(item.title, 'span')"
        >
          {{ item.title }}
        </Component>

        <!-- 👉 Badge -->
        <Component
          :is="layoutConfig.app.i18n.enable ? 'i18n-t' : 'span'"
          v-if="item.badgeContent"
          v-show="shouldShowContent"
          key="badge"
          class="nav-item-badge"
          :class="item.badgeClass"
          v-bind="getDynamicI18nProps(item.badgeContent, 'span')"
        >
          {{ item.badgeContent }}
        </Component>
      </TransitionGroup>
    </Component>
  </li>
</template>

<style lang="scss">
.layout-vertical-nav {
  .nav-link a,
  .nav-link .nav-link-inner {
    display: flex;
    align-items: center;
  }

  .nav-link.coming-soon .nav-link-inner {
    opacity: 0.45;
    filter: blur(0.4px);
    cursor: not-allowed;
    pointer-events: auto; // keep tooltip working
  }
}
</style>
