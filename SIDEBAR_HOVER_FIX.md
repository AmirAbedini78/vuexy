# Sidebar Hover and Collapse Fix - Enhanced Version

## مشکلات قبلی (Previous Issues)

1. **مشکل اول**: وقتی ساید بار در حالت collapsed هست و روی آن hover میکنیم، منوهای گروهی باز نمیشدند
2. **مشکل دوم**: وقتی ساید بار collapsed هست و hover میکنیم، منوها باز میشدند اما content (متن و آیکون‌ها) همراه آنها expand نمیشد
3. **مشکل سوم**: رفتار automatic بودن ساید بار وقتی از حالت collapsed خارج میشود
4. **مشکل چهارم**: دکمه collapse به شکل دایره کامل نبود و hover effects مناسب نداشت
5. **مشکل پنجم**: عدم هماهنگی بین VerticalNavLink و VerticalNavGroup در نمایش content

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

5. **NEW: Enhanced Content Display Logic**:
   ```javascript
   const shouldShowContent = computed(() => {
     // Always show content when sidebar is expanded
     if (!configStore.isVerticalNavCollapsed) {
       return true;
     }
     
     // When collapsed, show content only when hovered
     return isVerticalNavHovered.value;
   });
   ```

### 2. VerticalNavLink.vue Component

**فایل**: `resources/js/@layouts/components/VerticalNavLink.vue`

#### تغییرات جدید:

1. **Hover State Injection**:
   ```javascript
   const isVerticalNavHovered = inject(
     injectionKeyIsVerticalNavHovered,
     ref(false)
   );
   ```

2. **Consistent Content Display**:
   ```javascript
   const shouldShowContent = computed(() => {
     if (!configStore.isVerticalNavCollapsed) {
       return true;
     }
     return isVerticalNavHovered.value;
   });
   ```

3. **Template Updates**: استفاده از `shouldShowContent` به جای `hideTitleAndBadge`

### 3. VerticalNav.vue Component

**فایل**: `resources/js/@layouts/components/VerticalNav.vue`

#### تغییرات جدید:

1. **Enhanced Collapse Button Styling**:
   ```scss
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
     z-index: 10;
     box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
     transition: all 0.2s ease-in-out;
     cursor: pointer;

     &:hover {
       background-color: rgb(var(--v-theme-primary));
       border-color: rgb(var(--v-theme-primary));
       box-shadow: 0 4px 12px rgba(var(--v-theme-primary), 0.3);
       transform: translateY(-50%) scale(1.1);
     }
   }
   ```

2. **Improved Hover Behavior for Collapsed Sidebar**:
   ```scss
   &.collapsed {
     &.hovered {
       inline-size: variables.$layout-vertical-nav-width;
       box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
     }
   }
   ```

### 4. Vertical Nav SCSS Styles

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

✅ **مشکل 4**: دکمه collapse حالا به شکل دایره کامل است و hover effects زیبا دارد

✅ **مشکل 5**: هماهنگی کامل بین VerticalNavLink و VerticalNavGroup در نمایش content

### ویژگی‌های جدید:

- **Perfect Circle Collapse Button**: دکمه collapse به شکل دایره کامل با hover effects زیبا
- **Enhanced Hover Behavior**: ساید بار به صورت خودکار expand میشود هنگام hover
- **Consistent Content Display**: نمایش یکسان content در تمام navigation components
- **Smooth Transitions**: انیمیشن‌های نرم برای باز و بسته شدن
- **Better UX**: تجربه کاربری بهتر با hover states
- **Responsive Behavior**: رفتار مناسب در حالت‌های مختلف
- **Performance Optimized**: استفاده از computed properties برای بهتر بودن performance

## نحوه تست (How to Test)

1. **Test Collapsed Hover**:
   - ساید بار را collapse کنید
   - روی ساید بار hover کنید
   - باید گروه‌های فعال باز شوند و content کامل نمایش داده شود
   - ساید بار باید به صورت خودکار expand شود

2. **Test Collapse Button**:
   - دکمه collapse را بررسی کنید (باید دایره کامل باشد)
   - روی دکمه hover کنید (باید رنگ primary شود و scale شود)
   - کلیک کنید تا ساید بار collapse/expand شود

3. **Test Group Expansion**:
   - ساید بار را expand کنید
   - روی گروه‌ها کلیک کنید
   - باید به درستی باز و بسته شوند

4. **Test Automatic Behavior**:
   - از collapsed به expanded تغییر دهید
   - رفتار باید smooth و automatic باشد

5. **Test Content Consistency**:
   - در حالت collapsed، content باید مخفی باشد
   - هنگام hover، تمام content باید نمایش داده شود
   - هم VerticalNavLink و هم VerticalNavGroup باید رفتار یکسان داشته باشند

## فایل‌های تغییر یافته (Modified Files)

1. `resources/js/@layouts/components/VerticalNavGroup.vue`
2. `resources/js/@layouts/components/VerticalNavLink.vue`
3. `resources/js/@layouts/components/VerticalNav.vue`
4. `resources/styles/@core/template/_vertical-nav.scss`

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
