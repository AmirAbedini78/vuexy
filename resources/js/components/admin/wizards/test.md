# Test Checklist for Admin Listing Wizards

## ✅ فایل‌های ایجاد شده
- [x] `ListingEditWizard.vue` - فایل اصلی انتخاب ویزارد
- [x] `SingleDateWizard.vue` - ویزارد لیستینگ‌های تک تاریخی
- [x] `MultiDateWizard.vue` - ویزارد لیستینگ‌های چند تاریخی
- [x] `OpenDateWizard.vue` - ویزارد لیستینگ‌های باز تاریخی
- [x] `README.md` - مستندات کامل سیستم

## 🔍 بررسی عملکرد

### 1. تست SingleDateWizard
- [ ] نمایش صحیح 3 مرحله
- [ ] شمارنده "Step X of 3" در هدر
- [ ] استپر عمودی در سمت چپ
- [ ] فیلدهای اجباری با علامت *
- [ ] اعتبارسنجی فیلدهای تاریخ و قیمت
- [ ] دکمه‌های Previous/Next/Save
- [ ] مودال‌های Itinerary, Special Addons, Packages

### 2. تست MultiDateWizard
- [ ] نمایش صحیح 3 مرحله
- [ ] شمارنده "Step X of 3" در هدر
- [ ] استپر عمودی در سمت چپ
- [ ] فیلدهای اجباری با علامت *
- [ ] اعتبارسنجی فیلدهای عنوان و توضیحات
- [ ] دکمه‌های Previous/Next/Save
- [ ] مودال‌های Itinerary, Special Addons, Periods

### 3. تست OpenDateWizard
- [ ] نمایش صحیح 3 مرحله
- [ ] شمارنده "Step X of 3" در هدر
- [ ] استپر عمودی در سمت چپ
- [ ] فیلدهای اجباری با علامت *
- [ ] اعتبارسنجی فیلدهای عنوان و توضیحات
- [ ] دکمه‌های Previous/Next/Save
- [ ] مودال‌های Itinerary, Special Addons, Periods

### 4. تست ListingEditWizard اصلی
- [ ] تشخیص خودکار نوع لیستینگ
- [ ] نمایش ویزارد مناسب بر اساس `listing_type`
- [ ] تغییر نوع لیستینگ و نمایش ویزارد جدید
- [ ] ارسال رویداد `updated` به کامپوننت والد
- [ ] ارسال رویداد `close` به کامپوننت والد

## 🎯 ویژگی‌های کلیدی

### شماره‌گذاری و شمارنده
- [ ] نمایش شماره مرحله فعلی
- [ ] شمارنده "Step X of Y"
- [ ] رنگ آبی برای شمارنده
- [ ] به‌روزرسانی خودکار شمارنده

### استپر عمودی
- [ ] نمایش مراحل به شکل عمودی
- [ ] فعال بودن مرحله فعلی
- [ ] غیرفعال بودن مراحل باقی‌مانده
- [ ] آیکون‌های مناسب برای هر مرحله

### فیلدهای فرم
- [ ] فیلدهای متن، تاریخ، عدد
- [ ] Select box برای گزینه‌ها
- [ ] Textarea برای توضیحات طولانی
- [ ] اعتبارسنجی فیلدهای اجباری
- [ ] نمایش پیام‌های خطا

### مودال‌های پیشرفته
- [ ] ItineraryAccommodationDialog
- [ ] SpecialAddonsDialog
- [ ] PackageDialog (فقط Single Date)
- [ ] PeriodsDialog (Multi Date و Open Date)

## 🚀 تست عملکرد

### تست API
- [ ] ارسال درخواست PUT به `/admin/listings/{id}`
- [ ] مدیریت خطاها
- [ ] نمایش loading state
- [ ] به‌روزرسانی موفق

### تست State Management
- [ ] بارگذاری داده‌ها از props
- [ ] ذخیره تغییرات در فرم محلی
- [ ] مدیریت state مودال‌ها
- [ ] مدیریت state مراحل

### تست UI/UX
- [ ] Responsive design
- [ ] Accessibility
- [ ] Keyboard navigation
- [ ] Focus management

## 📝 نکات مهم

1. **Import ها**: مطمئن شوید که تمام کامپوننت‌های مورد نیاز import شده‌اند
2. **Props**: بررسی کنید که props `listing` به درستی ارسال می‌شود
3. **Events**: رویدادهای `close` و `updated` باید به درستی emit شوند
4. **Styling**: استایل‌های SCSS باید به درستی اعمال شوند
5. **Validation**: اعتبارسنجی فیلدها باید کار کند

## 🔧 عیب‌یابی

### مشکلات احتمالی
- **Import Error**: بررسی مسیر import کامپوننت‌ها
- **Props Error**: بررسی ارسال props
- **Styling Issue**: بررسی اعمال استایل‌ها
- **API Error**: بررسی endpoint و authentication

### راه‌حل‌ها
- بررسی console برای خطاها
- بررسی Network tab برای API calls
- بررسی Vue DevTools برای state
- بررسی CSS برای styling issues


















