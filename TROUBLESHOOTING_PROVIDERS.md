# راهنمای عیب‌یابی مشکل Providers

## مشکل
پس از پر کردن فرم individual wizard، داده‌ها در دیتاتیبل "All Providers" نمایش داده نمی‌شوند.

## مراحل عیب‌یابی

### 1. بررسی داده‌های دیتابیس
فایل `debug_auth.php` را اجرا کنید:
```bash
php debug_auth.php
```

این فایل موارد زیر را بررسی می‌کند:
- وجود کاربران admin
- وجود داده‌ها در جداول `individual_users` و `company_users`

### 2. بررسی Authentication
اگر داده‌ها در دیتابیس وجود دارند اما نمایش داده نمی‌شوند:

#### الف) بررسی کاربر admin
- آیا کاربری با role = 'admin' وجود دارد؟
- آیا کاربر فعلی admin است؟

#### ب) بررسی Token
- آیا token معتبر است؟
- آیا token در localStorage ذخیره شده است؟

### 3. تست API Routes

#### الف) تست route بدون authentication
```
GET /api/test/providers
```

#### ب) تست route با authentication
```
GET /api/admin/providers
```

### 4. بررسی Console Logs
در مرورگر، Developer Tools را باز کنید و Console را بررسی کنید:
- خطاهای JavaScript
- خطاهای API
- لاگ‌های اضافه شده

### 5. راه‌حل‌های موقت

#### الف) استفاده از Test Route
اگر authentication مشکل دارد، از route تست استفاده کنید:
```javascript
const testResponse = await $api("/test/providers", {
  method: "GET",
});
```

#### ب) ایجاد کاربر Admin
اگر هیچ کاربر admin وجود ندارد:
```sql
UPDATE users SET role = 'admin' WHERE email = 'your-email@example.com';
```

### 6. بررسی ساختار داده‌ها

#### Individual Users
- `full_name` - نام کامل
- `activity_specialization` - تخصص
- `years_of_experience` - سال‌های تجربه
- `country_of_operation` - کشور فعالیت
- `want_to_be_listed` - وضعیت لیست شدن

#### Company Users
- `company_name` - نام شرکت
- `activity_specialization` - تخصص
- `business_type` - نوع کسب‌وکار
- `country` - کشور
- `want_to_be_listed` - وضعیت لیست شدن

### 7. بررسی Middleware

#### AdminMiddleware
- بررسی می‌کند که کاربر authenticated باشد
- بررسی می‌کند که کاربر admin باشد

#### ApiMiddleware
- بررسی می‌کند که درخواست معتبر باشد

### 8. خطاهای احتمالی

#### 401 Unauthorized
- کاربر login نیست
- Token منقضی شده است

#### 403 Forbidden
- کاربر admin نیست
- دسترسی کافی ندارد

#### 500 Internal Server Error
- خطا در کد backend
- مشکل در دیتابیس

### 9. راه‌حل نهایی

اگر هیچ‌کدام از راه‌حل‌ها کار نکرد:

1. **بررسی کامل دیتابیس**:
   ```sql
   SELECT * FROM individual_users ORDER BY created_at DESC LIMIT 5;
   SELECT * FROM company_users ORDER BY created_at DESC LIMIT 5;
   ```

2. **بررسی کامل API**:
   - تست مستقیم API با Postman یا curl
   - بررسی response headers
   - بررسی response body

3. **بررسی کامل Frontend**:
   - بررسی network tab در Developer Tools
   - بررسی console logs
   - بررسی localStorage

### 10. تماس با پشتیبانی

اگر مشکل حل نشد، اطلاعات زیر را جمع‌آوری کنید:
- خروجی `debug_auth.php`
- Screenshot از console errors
- Screenshot از network tab
- توضیح کامل مراحل انجام شده



