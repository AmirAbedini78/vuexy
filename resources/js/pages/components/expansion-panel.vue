<script setup>
import {
  accordion,
  basic,
  customIcon,
  inset,
  model,
  popout,
  withBorder,
} from '@/views/demos/components/expansion-panel/demoCodeExpansionPanel'
</script>

<template>
  <VRow class="match-height">
    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Basic  -->
      <AppCardCode
        title="Basic"
        variant="outlined"
        :code="basic"
      >
        <p>Expansion panels in their simplest form display a list of expandable items. However, with the <code>multiple</code> prop, the expansion-panel can remain open until explicitly closed.</p>

        <DemoExpansionPanelBasic />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Accordion -->
      <AppCardCode
        title="Accordion"
        variant="outlined"
        :code="accordion"
      >
        <p>Use <code>accordion</code> variant option to create <strong>Accordion</strong> Panels. Accordion expansion-panel hasn't got margins around active panel.</p>

        <DemoExpansionPanelAccordion />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Inset -->
      <AppCardCode
        title="Inset"
        variant="outlined"
        :code="inset"
      >
        <p>Use <code>inset</code> variant option to create Inset Panels. The Inset expansion-panel becomes smaller when activated.</p>

        <DemoExpansionPanelInset />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Popout -->
      <AppCardCode
        title="Popout"
        variant="outlined"
        :code="popout"
      >
        <p>
          Use <code>popout</code> variant option to create expansion-panel with popout design. With it, expansion-panel is enlarged when activated.
        </p>

        <DemoExpansionPanelPopout />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Custom Icon -->
      <AppCardCode
        title="Custom Icon"
        variant="outlined"
        :code="customIcon"
      >
        <p>Expand action icon can be customized with <code>expand-icon</code> prop or the <code>actions</code> slot. Also, use the <code>.no-icon-rotate</code> class in conjunction with the <code>VExpansionPanels</code> component to disable icon rotation.</p>

        <DemoExpansionPanelCustomIcon />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Model -->
      <AppCardCode
        title="Model"
        variant="outlined"
        :code="model"
      >
        <p>Expansion panels can be controlled externally by modifying the <code>v-model</code>. If <code>multiple</code> prop is used then it is an array containing the indices of the open items.</p>

        <DemoExpansionPanelModel />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 With Border -->
      <AppCardCode
        title="With Border"
        variant="outlined"
        :code="withBorder"
      >
        <p>Please use the <code>.expansion-panels-width-border</code> class in conjunction with the <code>VExpansionPanels</code> component to create panels with borders.</p>

        <DemoExpansionPanelWithBorder />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
