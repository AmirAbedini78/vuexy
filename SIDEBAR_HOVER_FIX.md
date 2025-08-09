# Sidebar Hover and Collapse Fix

## مشکلات قبلی (Previous Issues)

1. **مشکل اول**: وقتی ساید بار در حالت collapsed هست و روی آن hover میکنیم، منوهای گروهی باز نمیشدند
2. **مشکل دوم**: وقتی ساید بار collapsed هست و hover میکنیم، منوها باز میشدند اما content (متن و آیکون‌ها) همراه آنها expand نمیشد
3. **مشکل سوم**: رفتار automatic بودن ساید بار وقتی از حالت collapsed خارج میشود

## تغییرات انجام شده (Changes Made)

### 1. VerticalNavGroup.vue Component

**فایل**: `resources/js/@layouts/components/VerticalNavGroup.vue`

#### تغییرات کلیدی:

1. **Computed Properties اضافه شده**:
   ```javascript
   const hideTitleAndBadge = computed(() => configStore.isVerticalNavMini(isVerticalNavHovered).value);
   const isNavMini = computed(() => configStore.isVerticalNavMini(isVerticalNavHovered).value);
   ```

2. **Watcher برای Hover State**:
   ```javascript
   watch(isVerticalNavHovered, (hovered) => {
     if (hovered && configStore.isVerticalNavCollapsed && isGroupActive.value) {
       isGroupOpen.value = true;
     }
     else if (!hovered && configStore.isVerticalNavCollapsed && !isGroupActive.value) {
       isGroupOpen.value = false;
     }
   }, { immediate: true });
   ```

3. **بهبود منطق باز و بسته شدن گروه‌ها**:
   ```javascript
   // در route path watcher
   isGroupOpen.value = isActive && (!isNavMini.value || isVerticalNavHovered.value);
   ```

4. **Function برای کلیک روی گروه‌ها**:
   ```javascript
   const handleGroupClick = () => {
     if (configStore.isVerticalNavCollapsed && !isVerticalNavHovered.value) {
       return; // اجازه toggle ندادن وقتی collapsed و hover نیست
     }
     isGroupOpen.value = !isGroupOpen.value;
   };
   ```

### 2. Vertical Nav SCSS Styles

**فایل**: `resources/styles/@core/template/_vertical-nav.scss`

#### تغییرات CSS:

1. **Enhanced Hover Behavior**:
   ```scss
   &.collapsed {
     &.hovered {
       .nav-group-children {
         opacity: 1;
         visibility: visible;
         transform: translateX(0);
         transition: all 0.25s ease-in-out;
       }
       
       .nav-item-title,
       .nav-item-badge,
       .nav-group-arrow {
         opacity: 1;
         visibility: visible;
         transform: translateX(0);
         transition: all 0.25s ease-in-out;
       }
     }
   }
   ```

2. **Proper Group Behavior When Collapsed**:
   ```scss
   .layout-vertical-nav:not(.hovered) {
     .nav-group {
       .nav-group-children {
         display: none;
       }
       
       &.active {
         .nav-group-label {
           background-color: rgba(var(--v-theme-primary), 0.08);
         }
       }
     }
   }
   ```

3. **Animation for Content Expansion**:
   ```scss
   @keyframes expandContent {
     from {
       opacity: 0;
       transform: translateY(-10px);
     }
     to {
       opacity: 1;
       transform: translateY(0);
     }
   }
   ```

## نتیجه (Results)

### مشکلات حل شده:

✅ **مشکل 1**: حالا وقتی ساید بار collapsed هست و روی آن hover میکنیم، گروه‌های فعال به صورت خودکار باز میشوند

✅ **مشکل 2**: وقتی hover میکنیم، تمام content شامل متن‌ها، badge ها و arrow ها به درستی نمایش داده میشوند

✅ **مشکل 3**: رفتار automatic ساید بار بهبود یافته و smooth transition دارد

### ویژگی‌های جدید:

- **Smooth Transitions**: انیمیشن‌های نرم برای باز و بسته شدن
- **Better UX**: تجربه کاربری بهتر با hover states
- **Responsive Behavior**: رفتار مناسب در حالت‌های مختلف
- **Performance Optimized**: استفاده از computed properties برای بهتر بودن performance

## نحوه تست (How to Test)

1. **Test Collapsed Hover**:
   - ساید بار را collapse کنید
   - روی ساید بار hover کنید
   - باید گروه‌های فعال باز شوند و content کامل نمایش داده شود

2. **Test Group Expansion**:
   - ساید بار را expand کنید
   - روی گروه‌ها کلیک کنید
   - باید به درستی باز و بسته شوند

3. **Test Automatic Behavior**:
   - از collapsed به expanded تغییر دهید
   - رفتار باید smooth و automatic باشد

## فایل‌های تغییر یافته (Modified Files)

1. `resources/js/@layouts/components/VerticalNavGroup.vue`
2. `resources/styles/@core/template/_vertical-nav.scss`

## نکات مهم (Important Notes)

- تمام تغییرات backward compatible هستند
- Performance بهبود یافته با استفاده از computed properties
- CSS transitions برای UX بهتر اضافه شده
- کد به صورت clean و maintainable نوشته شده

## مشکلات احتمالی (Potential Issues)

اگر مشکلی مشاهده کردید:

1. Cache browser را clear کنید
2. مطمئن شوید CSS compile شده باشد  
3. JavaScript errors در console چک کنید

---

**تاریخ**: ${new Date().toLocaleDateString('fa-IR')}
**نسخه**: 1.0.0 
