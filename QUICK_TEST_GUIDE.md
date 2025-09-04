# راهنمای تست سریع Providers

## وضعیت فعلی
✅ داده‌ها در دیتابیس موجود هستند (1 Individual User)  
✅ کاربران admin موجود هستند (2 admin)  
❌ مشکل در frontend یا authentication  

## مراحل تست

### 1. مرورگر را باز کنید
- به آدرس `http://localhost:3000` بروید
- به بخش admin dashboard بروید

### 2. Developer Tools را باز کنید
- F12 را فشار دهید
- به تب Console بروید

### 3. صفحه را refresh کنید
- F5 را فشار دهید
- لاگ‌های زیر را در console ببینید:

```
Loading dashboard data...
Loading providers data...
Test providers response: {data: [...], total: 1, ...}
Test providers data set: [{id: 1, provider_name: "امیرحسین عابدینی نژاد", ...}]
```

### 4. بررسی کنید
- آیا بخش "All Providers" در dashboard نمایش داده می‌شود؟
- آیا نام "امیرحسین عابدینی نژاد" در جدول دیده می‌شود؟

## اگر کار نکرد:

### الف) بررسی Network Tab
- Developer Tools → Network
- ببینید کدام API calls موفق/ناموفق هستند
- خطاهای 401 یا 403 را بررسی کنید

### ب) بررسی localStorage
- Developer Tools → Application → Local Storage
- ببینید آیا token ذخیره شده است

### ج) تست مستقیم API
در مرورگر این آدرس را باز کنید:
```
http://localhost:8000/api/test/providers
```

باید JSON زیر را ببینید:
```json
{
  "data": [
    {
      "id": 1,
      "provider_name": "امیرحسین عابدینی نژاد",
      "provider_type": "individual",
      "status": "Live",
      "total_listings": 0,
      "total_bookings": 0
    }
  ],
  "total": 1,
  "current_page": 1,
  "per_page": 20
}
```

## نتیجه
اگر همه چیز درست کار کرد، مشکل حل شده است. اگر نه، لطفاً خطاهای console را برای من ارسال کنید.



