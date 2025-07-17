# Deployment Checklist for Explorer Elite

## 🚀 Server Deployment Steps

### 1. **File Upload**
- [ ] Upload all changed files to server
- [ ] Ensure `.env` file is updated with production settings
- [ ] Upload new email template: `resources/views/emails/base-template.blade.php`

### 2. **Database Migration**
- [ ] Run: `php artisan migrate` (not fresh - to preserve existing data)
- [ ] Verify `password_reset_tokens` table exists
- [ ] Check all tables are properly created

### 3. **Configuration Updates**
- [ ] Update `.env` file with production settings
- [ ] Clear config cache: `php artisan config:clear`
- [ ] Clear route cache: `php artisan route:clear`
- [ ] Clear view cache: `php artisan view:clear`

### 4. **Frontend Build**
- [ ] Run: `npm install` (if new packages)
- [ ] Run: `npm run build` for production build
- [ ] Verify all assets are compiled

### 5. **Email Configuration**
- [ ] Verify SMTP settings in `.env`
- [ ] Test email sending functionality
- [ ] Check email templates are working

### 6. **Server Configuration**
- [ ] Check web server (Apache/Nginx) configuration
- [ ] Verify SSL certificate for portal.explorerelite.com
- [ ] Check file permissions (storage, cache directories)

### 7. **Testing**
- [ ] Test registration flow
- [ ] Test email verification
- [ ] Test forgot password flow
- [ ] Test password reset flow
- [ ] Test login functionality

## 📁 Files That Need to be Updated on Server

### Backend Files:
- `app/Http/Controllers/Api/AuthController.php`
- `app/Models/User.php`
- `app/Notifications/ResetPassword.php`
- `app/Notifications/VerifyEmail.php`
- `config/auth.php`
- `routes/web.php`
- `routes/api.php`
- `resources/js/plugins/1.router/additional-routes.js`

### Frontend Files:
- `resources/js/pages/login.vue`
- `resources/js/pages/forgot-password.vue`
- `resources/js/pages/reset-password.vue`

### New Files:
- `resources/views/emails/base-template.blade.php`

### Config Files:
- `.env` (update APP_NAME and other production settings)

## 🔧 Production Environment Settings

### Required .env Updates:
```env
APP_NAME="Explorer Elite"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://portal.explorerelite.com
FRONTEND_URL=https://portal.explorerelite.com

# Database settings (production)
DB_HOST=your_production_db_host
DB_DATABASE=your_production_db_name
DB_USERNAME=your_production_db_user
DB_PASSWORD=your_production_db_password

# Email settings (production)
MAIL_MAILER=smtp
MAIL_HOST=your_production_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_production_email
MAIL_PASSWORD=your_production_email_password
MAIL_FROM_ADDRESS=your_production_email
MAIL_FROM_NAME="Explorer Elite"

# Frontend API URL
VITE_API_BASE_URL=https://portal.explorerelite.com/api
```

## ⚠️ Important Notes

1. **Don't run `migrate:fresh`** on production - it will delete all data
2. **Backup database** before running migrations
3. **Test email functionality** after deployment
4. **Check file permissions** for storage and cache directories
5. **Verify SSL certificate** is working properly
6. **Test all authentication flows** after deployment 
