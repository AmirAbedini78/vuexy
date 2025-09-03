# راهنمای راه‌اندازی سیستم Providers

## خلاصه
این سیستم برای نمایش پرووایدرهایی که از طریق فرم‌های ویزارد در صفحه access control ثبت‌نام کرده‌اند طراحی شده است.

## فایل‌های ایجاد شده

### Backend
- `app/Http/Controllers/AdminController.php` - متدهای providers اضافه شده
- `routes/api.php` - مسیرهای API برای providers

### Frontend  
- `resources/js/pages/admin/providers.vue` - صفحه جداگانه providers
- `resources/js/pages/admin/dashboard.vue` - دیتاتیبل providers در dashboard
- `resources/js/navigation/vertical/admin.js` - منوی providers فعال شده
- `resources/js/plugins/1.router/additional-routes.js` - مسیر providers اضافه شده

## نحوه تست

### 1. ایجاد داده‌های نمونه
```bash
php create_sample_providers.php
```

### 2. تست API
```bash
php test_api_providers.php
```

### 3. تست از طریق مرورگر
- به `/admin/dashboard` بروید
- بخش "All Providers" را مشاهده کنید
- یا به `/admin/providers` بروید

## ساختار داده‌ها

### Individual Users
- `full_name` - نام کامل
- `activity_specialization` - تخصص
- `years_of_experience` - سال‌های تجربه
- `country_of_operation` - کشور فعالیت
- `want_to_be_listed` - وضعیت لیست شدن

### Company Users
- `company_name` - نام شرکت
- `activity_specialization` - تخصص
- `business_type` - نوع کسب‌وکار
- `country` - کشور
- `want_to_be_listed` - وضعیت لیست شدن

## ویژگی‌های پیاده‌سازی شده

✅ نمایش پرووایدرهای Individual و Company  
✅ جستجو بر اساس نام، تخصص و کشور  
✅ فیلتر بر اساس نوع پرووایدر  
✅ فیلتر بر اساس وضعیت  
✅ صفحه‌بندی  
✅ نمایش جزئیات پرووایدر  
✅ دکمه‌های Export و Add Provider  
✅ طراحی ریسپانسیو  

## نکات مهم

1. **داده‌های واقعی**: سیستم فقط پرووایدرهایی که از طریق فرم‌های ویزارد ثبت‌نام کرده‌اند را نمایش می‌دهد
2. **وضعیت**: همه پرووایدرها به صورت پیش‌فرض "Live" هستند
3. **Total Listings/Bookings**: در حال حاضر 0 نمایش داده می‌شود (قابل توسعه)
4. **Provider ID**: بر اساس ID واقعی در دیتابیس با فرمت 6 رقمی نمایش داده می‌شود

## عیب‌یابی

اگر داده‌ای نمایش داده نمی‌شود:
1. بررسی کنید که جداول `individual_users` و `company_users` خالی نباشند
2. فایل `test_api_providers.php` را اجرا کنید
3. لاگ‌های مرورگر را بررسی کنید
4. اطمینان حاصل کنید که API routes درست تعریف شده‌اند


