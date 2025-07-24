<template>
  <div class="special-addons-modal-overlay">
    <div class="special-addons-dialog-wrapper">
      <div class="special-addons-dialog-header">
        <span class="special-addons-dialog-title">Special Addons</span>
        <span class="special-addons-dialog-close" @click="$emit('close')"
          >&times;</span
        >
      </div>
      <div class="special-addons-dialog-desc">
        Please fill the form carefully (or something kinder maybe)
      </div>
      <div class="special-addons-dialog-content">
        <div class="special-addons-sidebar">
          <div
            v-for="(addon, idx) in addons"
            :key="idx"
            :class="[
              'special-addons-sidebar-item',
              { active: idx === activeAddonIndex },
            ]"
            @click="selectAddon(idx)"
            style="cursor: pointer"
          >
            <div class="special-addons-sidebar-row">
              <div class="special-addons-sidebar-col-number">
                <div class="special-addons-sidebar-number">
                  {{ (idx + 1).toString().padStart(2, "0") }}
                </div>
                <span class="special-addons-sidebar-star"
                  ><svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="#ec8d22"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <polygon
                      points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"
                    /></svg
                ></span>
                <div
                  v-if="idx !== addons.length - 1"
                  class="special-addons-sidebar-dotted-vertical"
                ></div>
              </div>
              <div class="special-addons-sidebar-col-content">
                <div class="special-addons-sidebar-title">
                  {{ addon.title }}
                </div>
                <div class="special-addons-sidebar-desc-row">
                  <span class="special-addons-sidebar-desc">{{
                    addon.description ||
                    "Your addon description would take place here"
                  }}</span>
                  <span class="special-addons-sidebar-price"
                    >€ {{ addon.price.toFixed(2) }}</span
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="special-addons-main">
          <div class="special-addons-main-title">
            Special Addon {{ activeAddonIndex + 1 }}
          </div>
          <div
            v-if="changesSaved[activeAddonIndex]"
            style="
              color: #444;
              font-size: 0.98rem;
              margin-bottom: 8px;
              display: flex;
              align-items: center;
              gap: 6px;
            "
          >
            <svg
              width="16"
              height="16"
              viewBox="0 0 24 24"
              fill="none"
              stroke="#222"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              style="margin-right: 2px"
            >
              <path d="M20 6L9 17l-5-5" />
            </svg>
            Changes saved
          </div>
          <div class="special-addons-main-form">
            <div class="special-addons-form-box">
              <div class="special-addons-form-row">
                <input
                  class="special-addons-input"
                  placeholder="Title"
                  v-model="addons[activeAddonIndex].title"
                  @input="updateAddonField('title', $event.target.value)"
                />
                <div class="special-addons-price-input-wrapper">
                  <input
                    class="special-addons-input"
                    placeholder="Price"
                    type="number"
                    v-model.number="addons[activeAddonIndex].price"
                    @input="
                      updateAddonField('price', Number($event.target.value))
                    "
                  />
                  <span class="special-addons-euro">€</span>
                </div>
              </div>
              <div class="special-addons-form-row">
                <input
                  class="special-addons-input special-addons-desc-input"
                  placeholder="Description"
                  v-model="addons[activeAddonIndex].description"
                  @input="updateAddonField('description', $event.target.value)"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="special-addons-actions">
        <button class="special-addons-done-btn">Done</button>
        <button class="special-addons-add-btn" @click="addAddon">
          Add More Addons
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";

const emit = defineEmits(["close"]);

// آرایه داینامیک Addons
const addons = ref([
  {
    title: "Addon 1 Title",
    description: "Your addon description would take place here",
    price: 200,
  },
  {
    title: "Addon 2 Title",
    description: "",
    price: 0,
  },
]);

// اندیس Addon فعال
const activeAddonIndex = ref(0);

// آرایه وضعیت نمایش پیام ذخیره شدن برای هر Addon
const changesSaved = ref(addons.value.map(() => false));

// همگام‌سازی changesSaved با تعداد addons
watch(
  addons,
  (newVal, oldVal) => {
    if (newVal.length > oldVal.length) {
      // Addon جدید اضافه شده
      changesSaved.value.push(false);
    } else if (newVal.length < oldVal.length) {
      // Addon حذف شده (در آینده)
      changesSaved.value.splice(newVal.length);
    }
  },
  { deep: true }
);

// هندل انتخاب Addon
function selectAddon(idx) {
  activeAddonIndex.value = idx;
}

// هندل تغییر فیلدهای فرم
function updateAddonField(field, value) {
  addons.value[activeAddonIndex.value][field] = value;
  changesSaved.value[activeAddonIndex.value] = true;
  setTimeout(() => {
    changesSaved.value[activeAddonIndex.value] = false;
  }, 2000);
}

// تابع افزودن Addon جدید
function addAddon() {
  addons.value.push({
    title: `Addon ${addons.value.length + 1} Title`,
    description: "",
    price: 0,
  });
  activeAddonIndex.value = addons.value.length - 1;
}
</script>

<style scoped>
.special-addons-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(44, 44, 44, 0.25);
  z-index: 3000;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow-y: auto;
}
.special-addons-dialog-wrapper {
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 2px 24px 0 rgba(44, 44, 44, 0.1);
  padding: 56px 64px 44px 64px;
  width: 90vw;
  max-width: 1700px;
  min-width: 1000px;
  min-height: 90vh;
  margin: 32px auto;
  position: relative;
  max-height: 95vh;
  overflow-y: auto;
}
.special-addons-dialog-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.special-addons-dialog-title {
  font-family: "Anton", sans-serif;
  font-size: 2.3rem;
  font-weight: 700;
  color: #222;
}
.special-addons-dialog-close {
  font-size: 2rem;
  color: #aaa;
  cursor: pointer;
  font-weight: 700;
  transition: color 0.2s;
}
.special-addons-dialog-close:hover {
  color: #ec8d22;
}
.special-addons-dialog-desc {
  font-size: 1.1rem;
  color: #444;
  font-family: "Karla", sans-serif;
  margin-bottom: 24px;
  margin-top: 4px;
}
.special-addons-dialog-content {
  display: flex;
  gap: 64px;
}
.special-addons-sidebar {
  min-width: 220px;
  max-width: 260px;
  display: flex;
  flex-direction: column;
  gap: 24px;
  margin-left: 32px;
}
.special-addons-sidebar-item.active {
  background: #fff;
  border-radius: 18px;
  padding: 36px 32px 36px 32px;
  margin-bottom: 24px;
}
.special-addons-sidebar-row {
  display: flex;
  flex-direction: row;
  align-items: flex-start;
  gap: 18px;
}
.special-addons-sidebar-col-number {
  display: flex;
  flex-direction: column;
  align-items: center;
  min-width: 40px;
  position: relative;
}
.special-addons-sidebar-star {
  margin-top: 12px;
  margin-bottom: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.special-addons-sidebar-dotted-vertical {
  border-left: 2px dotted #e0e0e0;
  width: 0;
  height: 60px;
  margin: 0;
  position: relative;
  left: 50%;
  top: 0;
  transform: translateX(-50%);
  z-index: 0;
}
.special-addons-sidebar-col-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  margin-left: 16px;
}
.special-addons-sidebar-title {
  font-weight: 700;
  font-size: 1.13rem;
  color: #222;
  font-family: "Karla", sans-serif;
  margin-bottom: 8px;
  margin-top: 0;
}
.special-addons-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  margin-top: 24px;
}
.special-addons-main-title {
  font-family: "Anton", sans-serif;
  font-size: 2rem;
  font-weight: 700;
  color: #222;
  margin-bottom: 18px;
}
.special-addons-main-form {
  width: 100%;
  min-width: 420px;
  max-width: 100%;
  /* فرم اصلی در گام بعد */
}
.special-addons-actions {
  display: flex;
  justify-content: flex-end;
  gap: 18px;
  margin-top: 32px;
}
.special-addons-done-btn {
  background: #111;
  color: #fff;
  font-weight: 700;
  min-width: 100px;
  border-radius: 8px;
  font-size: 1rem;
  min-height: 44px;
  box-shadow: 0 2px 8px 0 rgba(44, 44, 44, 0.08);
  transition: background 0.2s;
  border: none;
}
.special-addons-done-btn:hover {
  background: #222;
}
.special-addons-add-btn {
  background: #ec8d22;
  color: #fff;
  font-weight: 700;
  min-width: 140px;
  border-radius: 8px;
  font-size: 1rem;
  min-height: 44px;
  box-shadow: 0 2px 8px 0 rgba(44, 44, 44, 0.08);
  transition: background 0.2s;
  border: none;
}
.special-addons-add-btn:hover {
  background: #d67d1a;
}
.special-addons-sidebar-desc-row {
  display: flex;
  align-items: center;
  gap: 18px;
  margin-top: 8px;
}
.special-addons-sidebar-desc {
  color: #bbb;
  font-size: 1.01rem;
  font-family: "Karla", sans-serif;
}
.special-addons-sidebar-price {
  color: #222;
  font-size: 1.15rem;
  font-weight: 700;
  font-family: "Karla", sans-serif;
  margin-left: 18px;
}
.special-addons-form-box {
  background: #fff;
  border: 1.5px solid #e0e0e0;
  border-radius: 12px;
  padding: 28px 24px 18px 24px;
  margin-bottom: 18px;
  min-width: 420px;
  max-width: 100%;
  box-shadow: 0 1px 6px 0 rgba(44, 44, 44, 0.04);
}
.special-addons-form-row {
  display: flex;
  gap: 18px;
  margin-bottom: 16px;
}
.special-addons-input {
  border: 1.5px solid #e0e0e0;
  border-radius: 8px;
  padding: 10px 14px;
  font-size: 1.08rem;
  font-family: "Karla", sans-serif;
  background: #fff;
  outline: none;
  transition: border 0.2s;
  color: #222;
  flex: 1;
}
.special-addons-input::placeholder {
  color: #b0b0b0;
  font-size: 1.08rem;
  font-family: "Karla", sans-serif;
}
.special-addons-input:focus {
  border-color: #ec8d22;
}
.special-addons-price-input-wrapper {
  position: relative;
  flex: 1;
  display: flex;
  align-items: center;
}
.special-addons-euro {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #666;
  font-weight: 500;
  font-size: 1.15rem;
  pointer-events: none;
  z-index: 2;
}
.special-addons-desc-input {
  flex: 2;
}
</style>
