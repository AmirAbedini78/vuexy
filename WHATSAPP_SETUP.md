# راهنمای راه‌اندازی WhatsApp Business API

## مراحل راه‌اندازی WhatsApp Business API

### 1. ایجاد حساب WhatsApp Business API

1. به [Meta for Developers](https://developers.facebook.com/) بروید
2. یک حساب توسعه‌دهنده ایجاد کنید
3. یک اپلیکیشن جدید ایجاد کنید
4. WhatsApp Business API را به اپلیکیشن اضافه کنید

### 2. دریافت Access Token

1. در Meta Developer Console، به بخش "Tools" > "Graph API Explorer" بروید
2. اپلیکیشن خود را انتخاب کنید
3. مجوزهای زیر را اضافه کنید:
   - `whatsapp_business_messaging`
   - `whatsapp_business_management`
4. Access Token را کپی کنید

### 3. دریافت Phone Number ID

1. در Meta Developer Console، به بخش "WhatsApp" > "Getting Started" بروید
2. یک شماره تلفن WhatsApp Business اضافه کنید
3. Phone Number ID را کپی کنید (معمولاً یک عدد طولانی است)

### 4. ایجاد Message Template

1. در Meta Developer Console، به بخش "WhatsApp" > "Message Templates" بروید
2. یک template جدید ایجاد کنید:
   - **Name**: `verification_code`
   - **Category**: `Authentication`
   - **Language**: `English`
   - **Template Content**: 
     ```
     Your verification code is: {{1}}
     
     This code will expire in 10 minutes.
     If you didn't request this code, please ignore this message.
     ```

### 5. تنظیم فایل .env

مقادیر زیر را در فایل `.env` قرار دهید:

```env
# WhatsApp Business API Configuration
WHATSAPP_ACCESS_TOKEN=your_actual_access_token_here
WHATSAPP_PHONE_NUMBER_ID=your_actual_phone_number_id_here
WHATSAPP_TEMPLATE_NAME=verification_code
```

### 6. تست عملکرد

پس از تنظیم، می‌توانید عملکرد را تست کنید:

1. به صفحه Timeline بروید
2. شماره WhatsApp خود را وارد کنید
3. روی "Send Code" کلیک کنید
4. کد 6 رقمی را در WhatsApp دریافت کنید
5. کد را در فرم وارد کنید

## نکات مهم

### فرمت شماره تلفن
- شماره‌های ایران: `09123456789` یا `9123456789`
- سیستم به طور خودکار کد کشور `98` را اضافه می‌کند

### محدودیت‌ها
- WhatsApp Business API فقط برای شماره‌های تأیید شده کار می‌کند
- در مرحله توسعه، فقط شماره‌های تست کار می‌کنند
- برای استفاده تجاری، باید تأیید Meta را دریافت کنید

### امنیت
- Access Token را در فایل `.env` نگه دارید
- هرگز Access Token را در کد قرار ندهید
- از HTTPS استفاده کنید

## عیب‌یابی

### خطای "Invalid phone number"
- شماره تلفن را با فرمت صحیح وارد کنید
- مطمئن شوید که شماره در WhatsApp ثبت شده است

### خطای "Template not found"
- نام template را در Meta Developer Console بررسی کنید
- مطمئن شوید که template تأیید شده است

### خطای "Access token invalid"
- Access Token را دوباره بررسی کنید
- مطمئن شوید که مجوزهای لازم را دارید

## منابع مفید

- [WhatsApp Business API Documentation](https://developers.facebook.com/docs/whatsapp)
- [Meta for Developers](https://developers.facebook.com/)
- [WhatsApp Business API Getting Started](https://developers.facebook.com/docs/whatsapp/getting-started) 
