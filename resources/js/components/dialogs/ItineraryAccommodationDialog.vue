<template>
  <div class="itinerary-dialog-wrapper">
    <div class="itinerary-dialog-header">
      <span class="itinerary-dialog-title">Itinerary/Accommodations</span>
      <span class="itinerary-dialog-close" @click="$emit('close')"
        >&times;</span
      >
    </div>
    <div class="itinerary-dialog-desc">
      Please fill the form carefully (or something kinder maybe)
    </div>
    <div class="itinerary-dialog-content">
      <div class="itinerary-sidebar">
        <div
          v-for="(day, idx) in days"
          :key="day.id"
          class="itinerary-sidebar-item"
          :class="{ active: idx === activeDayIndex }"
          @click="activeDayIndex = idx"
        >
          <div class="itinerary-sidebar-number">
            {{ (idx + 1).toString().padStart(2, "0") }}
          </div>
          <div>
            <div class="itinerary-sidebar-title">
              {{ day.title || `Day ${idx + 1} Itinerary Title` }}
            </div>
            <div
              v-if="idx === activeDayIndex"
              class="itinerary-sidebar-accommodation active"
            >
              Day {{ idx + 1 }} Accommodation
            </div>
            <div
              v-if="idx === activeDayIndex"
              class="itinerary-sidebar-location"
            >
              Location would take place here
            </div>
          </div>
        </div>
      </div>
      <div class="itinerary-main">
        <div
          v-for="(day, idx) in days"
          :key="day.id"
          v-show="idx === activeDayIndex"
          class="itinerary-day-form"
        >
          <div class="itinerary-day-label">Day {{ idx + 1 }}</div>
          <div class="itinerary-day-fields">
            <input
              v-model="day.title"
              class="itinerary-input"
              placeholder="Itinerary Title"
            />
            <input
              v-model="day.accommodation"
              class="itinerary-input"
              placeholder="Add accommodation here"
            />
            <input
              v-model="day.description"
              class="itinerary-input"
              placeholder="Itinerary Description"
            />
            <input
              v-model="day.location"
              class="itinerary-input"
              placeholder="Add exact accommodation location"
            />
            <div class="itinerary-link-row">
              <input
                v-model="day.link"
                class="itinerary-input"
                placeholder="Add your link here"
              />
              <button class="itinerary-link-add">+</button>
            </div>
          </div>
          <div class="itinerary-changes-saved">
            <VIcon icon="tabler-check" size="18" style="color: #aaa" /> Changes
            saved
          </div>
        </div>
        <div class="itinerary-actions">
          <VBtn class="itinerary-done-btn" @click="handleDone">Done</VBtn>
          <VBtn class="itinerary-add-btn" @click="addDay">Add More Days</VBtn>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { VBtn, VIcon } from "vuetify/components";

const props = defineProps({
  modelValue: Boolean,
  initialDays: {
    type: Array,
    default: () => [],
  },
});
const emit = defineEmits(["close", "done"]);

const days = ref(
  props.initialDays.length
    ? JSON.parse(JSON.stringify(props.initialDays))
    : [
        {
          id: 1,
          title: "",
          description: "",
          accommodation: "",
          location: "",
          link: "",
        },
        {
          id: 2,
          title: "",
          description: "",
          accommodation: "",
          location: "",
          link: "",
        },
      ]
);
const activeDayIndex = ref(0);

function addDay() {
  days.value.push({
    id: Date.now(),
    title: "",
    description: "",
    accommodation: "",
    location: "",
    link: "",
  });
  activeDayIndex.value = days.value.length - 1;
}
function handleDone() {
  emit("done", days.value);
  emit("close");
}
</script>

<style scoped>
.itinerary-dialog-wrapper {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 16px 0 rgba(44, 44, 44, 0.08);
  padding: 32px 24px 24px 24px;
  max-width: 900px;
  margin: 32px auto;
  position: relative;
}
.itinerary-dialog-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.itinerary-dialog-title {
  font-family: "Anton", sans-serif;
  font-size: 2rem;
  font-weight: 700;
  color: #222;
}
.itinerary-dialog-close {
  font-size: 2rem;
  color: #aaa;
  cursor: pointer;
  font-weight: 700;
  transition: color 0.2s;
}
.itinerary-dialog-close:hover {
  color: #ec8d22;
}
.itinerary-dialog-desc {
  font-size: 1.1rem;
  color: #444;
  font-family: "Karla", sans-serif;
  margin-bottom: 24px;
  margin-top: 4px;
}
.itinerary-dialog-content {
  display: flex;
  gap: 32px;
}
.itinerary-sidebar {
  min-width: 180px;
  max-width: 200px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.itinerary-sidebar-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  cursor: pointer;
  padding: 8px 0;
  border-radius: 8px;
  transition: background 0.2s;
}
.itinerary-sidebar-item.active {
  background: #f8f8f8;
}
.itinerary-sidebar-number {
  background: #ec8d22;
  color: #fff;
  font-weight: 700;
  font-size: 1.1rem;
  border-radius: 6px;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 4px;
}
.itinerary-sidebar-title {
  font-weight: 700;
  font-size: 1rem;
  color: #222;
}
.itinerary-sidebar-accommodation.active {
  color: #00c853;
  font-size: 0.95rem;
  font-weight: 600;
}
.itinerary-sidebar-location {
  color: #bbb;
  font-size: 0.92rem;
}
.itinerary-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 32px;
}
.itinerary-day-form {
  background: #fafafa;
  border-radius: 10px;
  padding: 18px 18px 12px 18px;
  margin-bottom: 18px;
  border: 1px solid #eee;
  position: relative;
}
.itinerary-day-label {
  font-family: "Anton", sans-serif;
  font-size: 1.2rem;
  font-weight: 700;
  color: #222;
  margin-bottom: 10px;
}
.itinerary-day-fields {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px 16px;
}
.itinerary-input {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 10px 12px;
  font-size: 1rem;
  font-family: "Karla", sans-serif;
  background: #fff;
  outline: none;
  transition: border 0.2s;
}
.itinerary-input:focus {
  border-color: #ec8d22;
}
.itinerary-link-row {
  grid-column: 1 / span 2;
  display: flex;
  gap: 8px;
  align-items: center;
  margin-top: 4px;
}
.itinerary-link-add {
  background: #ec8d22;
  color: #fff;
  border: none;
  border-radius: 6px;
  font-size: 1.3rem;
  width: 36px;
  height: 36px;
  cursor: pointer;
  font-weight: 700;
  transition: background 0.2s;
}
.itinerary-link-add:hover {
  background: #d67d1a;
}
.itinerary-changes-saved {
  position: absolute;
  top: 10px;
  right: 18px;
  color: #888;
  font-size: 0.95rem;
  display: flex;
  align-items: center;
  gap: 4px;
}
.itinerary-actions {
  display: flex;
  justify-content: flex-end;
  gap: 16px;
  margin-top: 12px;
}
.itinerary-done-btn {
  background: #111 !important;
  color: #fff !important;
  font-weight: 700;
  min-width: 100px;
  border-radius: 8px;
  font-size: 1rem;
}
.itinerary-add-btn {
  background: #ec8d22 !important;
  color: #fff !important;
  font-weight: 700;
  min-width: 140px;
  border-radius: 8px;
  font-size: 1rem;
}
</style>
