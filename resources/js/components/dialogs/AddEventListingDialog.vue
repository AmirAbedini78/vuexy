<script setup>
import CustomRadiosWithIcon from "@/@core/components/app-form-elements/CustomRadiosWithIcon.vue";
import { ref } from "vue";

const props = defineProps({
  isDialogVisible: {
    type: Boolean,
    required: true,
  },
});

const emit = defineEmits(["update:isDialogVisible", "submit"]);

// Radio button content for the two options
const radioContent = [
  {
    title: "Add Explorer Elite Listing",
    desc: "Create your listing on Explorer Elite Marketplace",
    value: "listing",
    icon: {
      icon: "tabler-calendar",
      size: "48",
    },
  },
  {
    title: "Add an Event",
    desc: "Add your custom Event to manage on Explorer Elite",
    value: "event",
    icon: {
      icon: "tabler-calendar-plus",
      size: "48",
    },
  },
];

const selectedRadio = ref("listing");
const showWizard = ref(false);
const currentWizardStep = ref(0);

// Wizard steps for Add Event
const wizardSteps = [
  {
    title: "Single Date",
    subtitle: "Create a single date event",
    icon: "tabler-calendar",
  },
  {
    title: "Multi Date",
    subtitle: "Create a multi date event",
    icon: "tabler-calendar-stats",
  },
  {
    title: "Open Date",
    subtitle: "Create an open date event",
    icon: "tabler-calendar-clock",
  },
];

// Event form data for each wizard type
const singleDateFormData = ref({
  eventTitle: "",
  startingDate: "",
  finishingDate: "",
  eventCapacity: "",
});

const multiDateFormData = ref({
  eventTitle: "",
  startingDate: "",
  finishingDate: "",
  eventCapacity: "",
});

const openDateFormData = ref({
  eventTitle: "",
  startingDate: "",
  finishingDate: "",
  eventCapacity: "",
});

const dialogVisibleUpdate = (val) => {
  emit("update:isDialogVisible", val);
};

const handleSubmit = () => {
  if (selectedRadio.value === "listing") {
    emit("submit", selectedRadio.value);
    dialogVisibleUpdate(false);
  } else if (selectedRadio.value === "event") {
    showWizard.value = true;
  }
};

const handleWizardSubmit = () => {
  let formData;
  let wizardType;

  switch (currentWizardStep.value) {
    case 0:
      formData = singleDateFormData.value;
      wizardType = "single_date";
      break;
    case 1:
      formData = multiDateFormData.value;
      wizardType = "multi_date";
      break;
    case 2:
      formData = openDateFormData.value;
      wizardType = "open_date";
      break;
  }

  console.log("Event created:", { type: wizardType, data: formData });
  emit("submit", {
    type: "event",
    wizardType: wizardType,
    data: formData,
  });
  dialogVisibleUpdate(false);
};

const closeDialog = () => {
  showWizard.value = false;
  currentWizardStep.value = 0;
  dialogVisibleUpdate(false);
};

const goBackToSelection = () => {
  showWizard.value = false;
  currentWizardStep.value = 0;
};

const handleNext = () => {
  if (currentWizardStep.value < wizardSteps.length - 1) {
    currentWizardStep.value++;
  }
};

const handlePrevious = () => {
  if (currentWizardStep.value > 0) {
    currentWizardStep.value--;
  }
};
</script>

<template>
  <VDialog
    :model-value="props.isDialogVisible"
    :max-width="showWizard ? '1200px' : '800px'"
    class="add-event-listing-modal"
    @update:model-value="dialogVisibleUpdate"
    @click:outside="closeDialog"
    @keydown.esc="closeDialog"
  >
    <VCard class="add-event-listing-card">
      <!-- Initial Selection View -->
      <template v-if="!showWizard">
        <!-- Header -->
        <div class="modal-header">
          <div class="header-content">
            <div class="header-text">
              <h1 class="modal-title">Add Event/Listing</h1>
              <p class="modal-subtitle">
                Add your listings on Explorer Elite marketplace or just add it
                as an Event
              </p>
            </div>
            <VBtn icon variant="text" @click="closeDialog" class="close-btn">
              <VIcon icon="tabler-x" size="24" />
            </VBtn>
          </div>
        </div>

        <!-- Modal Content -->
        <div class="modal-content">
          <div class="radio-cards-container">
            <CustomRadiosWithIcon
              v-model:selected-radio="selectedRadio"
              :radio-content="radioContent"
              :grid-column="{ sm: '6', cols: '12' }"
              class="add-event-listing-radios"
            />
          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer">
          <VBtn
            color="warning"
            size="large"
            class="submit-btn"
            @click="handleSubmit"
          >
            Submit
          </VBtn>
        </div>
      </template>

      <!-- Event Wizard View -->
      <template v-else>
        <!-- Wizard Header -->
        <div class="wizard-header">
          <div class="header-content">
            <div class="header-text">
              <h1 class="modal-title">Add Event</h1>
              <p class="modal-subtitle">
                In this section you can create and manage events without getting
                listed on Explorer Elite
              </p>
            </div>
            <VBtn icon variant="text" @click="closeDialog" class="close-btn">
              <VIcon icon="tabler-x" size="24" />
            </VBtn>
          </div>
        </div>

        <!-- Wizard Content -->
        <div class="wizard-content">
          <VRow>
            <!-- Left Sidebar - Wizard Steps -->
            <VCol cols="12" md="4" class="wizard-sidebar">
              <div class="wizard-steps">
                <div
                  v-for="(step, index) in wizardSteps"
                  :key="index"
                  class="wizard-step"
                  :class="{
                    active: currentWizardStep === index,
                    completed: currentWizardStep > index,
                  }"
                  @click="currentWizardStep = index"
                >
                  <div class="step-icon">
                    <VIcon
                      :icon="step.icon"
                      :color="currentWizardStep === index ? 'white' : '#ec8d22'"
                      size="24"
                    />
                  </div>
                  <div class="step-content">
                    <h6 class="step-title">{{ step.title }}</h6>
                    <p class="step-subtitle">{{ step.subtitle }}</p>
                  </div>
                </div>
              </div>
            </VCol>

            <!-- Right Side - Form Content -->
            <VCol cols="12" md="8" class="wizard-form">
              <div class="form-content">
                <VWindow
                  v-model="currentWizardStep"
                  class="disable-tab-transition"
                >
                  <!-- Single Date Step -->
                  <VWindowItem>
                    <div class="step-header">
                      <h6 class="step-form-title">Period 1</h6>
                    </div>
                    <VForm>
                      <VRow>
                        <VCol cols="12">
                          <VTextField
                            v-model="singleDateFormData.eventTitle"
                            label="Event title"
                            placeholder="Add your event title here"
                            variant="outlined"
                            density="comfortable"
                          />
                        </VCol>
                        <VCol cols="12" md="6">
                          <VTextField
                            v-model="singleDateFormData.startingDate"
                            label="Starting Date"
                            placeholder="Select starting date"
                            variant="outlined"
                            density="comfortable"
                          />
                        </VCol>
                        <VCol cols="12" md="6">
                          <VTextField
                            v-model="singleDateFormData.finishingDate"
                            label="Finishing Date"
                            placeholder="Select finishing date"
                            variant="outlined"
                            density="comfortable"
                          />
                        </VCol>
                        <VCol cols="12" md="6">
                          <VTextField
                            v-model="singleDateFormData.eventCapacity"
                            label="Event Capacity"
                            variant="outlined"
                            density="comfortable"
                          />
                        </VCol>
                      </VRow>
                    </VForm>
                  </VWindowItem>

                  <!-- Multi Date Step -->
                  <VWindowItem>
                    <div class="step-header">
                      <h6 class="step-form-title">Multi Date Event</h6>
                    </div>
                    <VForm>
                      <VRow>
                        <VCol cols="12">
                          <VTextField
                            v-model="multiDateFormData.eventTitle"
                            label="Event title"
                            placeholder="Add your event title here"
                            variant="outlined"
                            density="comfortable"
                          />
                        </VCol>
                        <VCol cols="12" md="6">
                          <VTextField
                            v-model="multiDateFormData.startingDate"
                            label="Starting Date"
                            placeholder="Select starting date"
                            variant="outlined"
                            density="comfortable"
                          />
                        </VCol>
                        <VCol cols="12" md="6">
                          <VTextField
                            v-model="multiDateFormData.finishingDate"
                            label="Finishing Date"
                            placeholder="Select finishing date"
                            variant="outlined"
                            density="comfortable"
                          />
                        </VCol>
                        <VCol cols="12" md="6">
                          <VTextField
                            v-model="multiDateFormData.eventCapacity"
                            label="Event Capacity"
                            variant="outlined"
                            density="comfortable"
                          />
                        </VCol>
                      </VRow>
                    </VForm>
                  </VWindowItem>

                  <!-- Open Date Step -->
                  <VWindowItem>
                    <div class="step-header">
                      <h6 class="step-form-title">Open Date Event</h6>
                    </div>
                    <VForm>
                      <VRow>
                        <VCol cols="12">
                          <VTextField
                            v-model="openDateFormData.eventTitle"
                            label="Event title"
                            placeholder="Add your event title here"
                            variant="outlined"
                            density="comfortable"
                          />
                        </VCol>
                        <VCol cols="12" md="6">
                          <VTextField
                            v-model="openDateFormData.startingDate"
                            label="Starting Date"
                            placeholder="Select starting date"
                            variant="outlined"
                            density="comfortable"
                          />
                        </VCol>
                        <VCol cols="12" md="6">
                          <VTextField
                            v-model="openDateFormData.finishingDate"
                            label="Finishing Date"
                            placeholder="Select finishing date"
                            variant="outlined"
                            density="comfortable"
                          />
                        </VCol>
                        <VCol cols="12" md="6">
                          <VTextField
                            v-model="openDateFormData.eventCapacity"
                            label="Event Capacity"
                            variant="outlined"
                            density="comfortable"
                          />
                        </VCol>
                      </VRow>
                    </VForm>
                  </VWindowItem>
                </VWindow>

                <!-- Wizard Actions -->
                <div class="wizard-actions">
                  <VBtn
                    v-if="currentWizardStep > 0"
                    variant="outlined"
                    @click="handlePrevious"
                  >
                    Previous
                  </VBtn>
                  <VSpacer />
                  <VBtn
                    v-if="currentWizardStep === wizardSteps.length - 1"
                    color="dark"
                    @click="handleWizardSubmit"
                  >
                    Done
                  </VBtn>
                  <VBtn v-else color="warning" @click="handleNext"> Next </VBtn>
                  <VBtn color="warning" variant="outlined" class="ml-2">
                    Add More Periods
                  </VBtn>
                </div>
              </div>
            </VCol>
          </VRow>
        </div>

        <!-- Note Section -->
        <div class="note-section">
          <div class="note-content">
            <div class="note-header">
              <h6 class="note-title">NOTE</h6>
              <VBtn icon variant="text" size="small" class="note-close-btn">
                <VIcon icon="tabler-x" size="16" />
              </VBtn>
            </div>
            <p class="note-text">
              List your experience on Explorer Elite and connect with a global
              community of explorers
            </p>
            <a href="#" class="note-link">Learn more</a>
          </div>
        </div>
      </template>
    </VCard>
  </VDialog>
</template>

<style lang="scss" scoped>
.add-event-listing-modal {
  .add-event-listing-card {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
  }

  .modal-header {
    background: #fff;
    padding: 2rem 2rem 1rem 2rem;
    border-bottom: 1px solid #e0e0e0;

    .header-content {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 1rem;

      .header-text {
        flex: 1;

        .modal-title {
          font-family: "Anton", sans-serif;
          font-size: 2rem;
          font-weight: 300;
          color: #2f2b3d;
          margin: 0 0 0.5rem 0;
          line-height: 1.2;
        }

        .modal-subtitle {
          font-family: "Karla", sans-serif;
          font-size: 1rem;
          color: #6c757d;
          margin: 0;
          line-height: 1.5;
        }
      }

      .close-btn {
        color: #6c757d;
        transition: color 0.3s ease;

        &:hover {
          color: #ec8d22;
        }
      }
    }
  }

  .modal-content {
    padding: 2rem;
    background: #fff;

    .radio-cards-container {
      max-width: 600px;
      margin: 0 auto;
    }
  }

  .modal-footer {
    background: #f8f9fa;
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: center;
    border-top: 1px solid #e0e0e0;

    .submit-btn {
      font-family: "Karla", sans-serif;
      font-size: 1rem;
      font-weight: 600;
      text-transform: none;
      letter-spacing: 0.5px;
      border-radius: 8px;
      padding: 12px 32px;
      background: linear-gradient(135deg, #ec8d22 0%, #d17a1a 100%);
      border: none;
      transition: all 0.3s ease;
      min-width: 120px;

      &:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(236, 141, 34, 0.3);
      }
    }
  }
}

// Custom styles for the radio cards
:deep(.add-event-listing-radios) {
  .custom-radio-icon {
    min-height: 180px;
    padding: 2rem;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    transition: all 0.3s ease;
    background: #fff;
    justify-content: center;

    &:hover {
      border-color: #ec8d22;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    &.active {
      border-color: #ec8d22;
      background: linear-gradient(135deg, #fff 0%, #fff8f0 100%);
      box-shadow: 0 6px 20px rgba(236, 141, 34, 0.15);
    }

    h6 {
      font-family: "Anton", sans-serif;
      font-size: 1.25rem;
      font-weight: 300;
      color: #2f2b3d;
      margin-bottom: 0.75rem;
      line-height: 1.3;
    }

    p {
      font-family: "Karla", sans-serif;
      font-size: 0.9rem;
      color: #6c757d;
      line-height: 1.5;
      margin: 0;
    }

    .v-icon {
      color: #ec8d22;
      margin-bottom: 1rem;
    }

    .v-radio {
      margin-block-end: -0.5rem;

      .v-selection-control__input {
        color: #ec8d22;

        .v-icon {
          color: #ec8d22;
          font-size: 20px;
        }
      }
    }
  }

  .radio-card-col {
    margin-bottom: 1rem;
  }
}

// Wizard Styles
.wizard-header {
  background: #fff;
  padding: 2rem 2rem 1rem 2rem;
  border-bottom: 1px solid #e0e0e0;

  .header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;

    .header-text {
      flex: 1;

      .modal-title {
        font-family: "Anton", sans-serif;
        font-size: 1.75rem;
        font-weight: 300;
        color: #2c3e50;
        margin: 0 0 0.5rem 0;
        line-height: 1.2;
      }

      .modal-subtitle {
        font-family: "Karla", sans-serif;
        font-size: 0.9rem;
        color: #6c757d;
        margin: 0;
        line-height: 1.4;
      }
    }

    .close-btn {
      color: #6c757d;
      transition: color 0.3s ease;

      &:hover {
        color: #ec8d22;
      }
    }
  }
}

.wizard-content {
  padding: 0;
  background: #fff;

  .wizard-sidebar {
    background: #f8f9fa;
    border-right: 1px solid #e0e0e0;
    padding: 2rem 1.5rem;

    .wizard-steps {
      .wizard-step {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        margin-bottom: 0.75rem;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #fff;
        border: 2px solid #e0e0e0;
        min-height: 80px;

        &:hover {
          border-color: #d0d0d0;
          box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        &.active {
          background: #2c3e50;
          border-color: #2c3e50;
          box-shadow: 0 4px 12px rgba(44, 62, 80, 0.3);

          .step-title {
            color: white;
            font-weight: 600;
          }

          .step-subtitle {
            color: rgba(255, 255, 255, 0.9);
          }

          .step-icon {
            background: white;
            color: #2c3e50;
          }
        }

        .step-icon {
          width: 40px;
          height: 40px;
          border-radius: 8px;
          background: #f0f0f0;
          display: flex;
          align-items: center;
          justify-content: center;
          flex-shrink: 0;
          color: #6c757d;
        }

        .step-content {
          flex: 1;

          .step-title {
            font-family: "Karla", sans-serif;
            font-size: 1rem;
            font-weight: 500;
            color: #2c3e50;
            margin: 0 0 0.25rem 0;
            line-height: 1.3;
          }

          .step-subtitle {
            font-family: "Karla", sans-serif;
            font-size: 0.8rem;
            color: #6c757d;
            margin: 0;
            line-height: 1.3;
          }
        }
      }
    }
  }

  .wizard-form {
    padding: 2rem;

    .form-content {
      .step-header {
        margin-bottom: 1.5rem;

        .step-form-title {
          font-family: "Karla", sans-serif;
          font-size: 1.25rem;
          font-weight: 600;
          color: #2c3e50;
          margin: 0;
        }
      }

      .wizard-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e0e0e0;
      }
    }
  }
}

.note-section {
  background: #ec8d22;
  padding: 1.25rem 2rem;
  margin-top: 0;

  .note-content {
    position: relative;

    .note-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 0.5rem;

      .note-title {
        font-family: "Karla", sans-serif;
        font-size: 1rem;
        font-weight: 700;
        color: white;
        margin: 0;
      }

      .note-close-btn {
        color: white;
        opacity: 0.8;

        &:hover {
          opacity: 1;
        }
      }
    }

    .note-text {
      font-family: "Karla", sans-serif;
      font-size: 0.85rem;
      color: white;
      margin: 0 0 0.5rem 0;
      line-height: 1.4;
    }

    .note-link {
      font-family: "Karla", sans-serif;
      font-size: 0.85rem;
      color: white;
      text-decoration: underline;
      font-weight: 600;

      &:hover {
        color: #fff8f0;
      }
    }
  }
}

// Responsive design
@media (max-width: 768px) {
  .add-event-listing-modal {
    .modal-header,
    .wizard-header {
      padding: 1.5rem 1.5rem 1rem 1.5rem;

      .header-content {
        .header-text {
          .modal-title {
            font-size: 1.5rem;
          }

          .modal-subtitle {
            font-size: 0.9rem;
          }
        }
      }
    }

    .modal-content {
      padding: 1.5rem;

      .radio-cards-container {
        :deep(.custom-radio-icon) {
          min-height: 160px;
          padding: 1.5rem;

          h6 {
            font-size: 1.1rem;
          }

          p {
            font-size: 0.85rem;
          }
        }
      }
    }

    .modal-footer {
      padding: 1rem 1.5rem;

      .submit-btn {
        font-size: 0.9rem;
        padding: 10px 24px;
      }
    }

    .wizard-content {
      .wizard-sidebar {
        padding: 1.5rem 1rem;

        .wizard-steps {
          .wizard-step {
            padding: 1rem;
            margin-bottom: 0.5rem;
            min-height: 70px;

            .step-icon {
              width: 36px;
              height: 36px;
            }

            .step-content {
              .step-title {
                font-size: 0.9rem;
              }

              .step-subtitle {
                font-size: 0.75rem;
              }
            }
          }
        }
      }

      .wizard-form {
        padding: 1.5rem;

        .form-content {
          .step-header {
            margin-bottom: 1.25rem;

            .step-form-title {
              font-size: 1.1rem;
            }
          }

          .wizard-actions {
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            flex-wrap: wrap;
            gap: 0.5rem;
          }
        }
      }
    }

    .note-section {
      padding: 1rem 1.5rem;

      .note-content {
        .note-header {
          .note-title {
            font-size: 1rem;
          }
        }

        .note-text {
          font-size: 0.85rem;
        }

        .note-link {
          font-size: 0.85rem;
        }
      }
    }
  }
}
</style>
