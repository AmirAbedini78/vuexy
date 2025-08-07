# Admin System Documentation

## Overview

This project now includes a comprehensive admin system that separates regular users from administrators. The system provides role-based access control with different dashboards and functionalities for different user types.

## User Roles

### 1. Regular Users (`user`)
- Can register through the signup process
- Access the timeline/registration flow
- Use all regular user features

### 2. Admin Users (`admin`)
- Cannot register through signup (must be added manually to database)
- Access admin dashboard with management features
- Can manage users, listings, and view statistics
- All admins have the same privileges

## Admin Features

### Dashboard
- Overview of system statistics
- Recent user registrations
- Recent listings
- Quick action buttons

### User Management
- View all users in the system
- Search and filter users by role/status
- View user details
- Edit user information
- Delete users

### Listing Management
- View all listings in the system
- Search listings
- View listing details
- Edit listings
- Delete listings

### Statistics
- User registration trends
- Listing creation trends
- System overview statistics

## API Endpoints

### Admin Routes (Protected by AdminMiddleware)
- `GET /api/admin/dashboard` - Get dashboard statistics
- `GET /api/admin/users` - Get all users (with search/filter)
- `GET /api/admin/users/{id}` - Get specific user details
- `PUT /api/admin/users/{id}/status` - Update user status
- `GET /api/admin/listings` - Get all listings (with search)
- `GET /api/admin/statistics` - Get system statistics

## Frontend Routes

### Admin Pages
- `/admin/dashboard` - Admin dashboard
- `/admin/users` - User management
- `/admin/listings` - Listing management
- `/admin/statistics` - System statistics

## Authentication Flow

1. **Login Process**:
   - Users enter credentials on login page
   - System checks user role
   - Admins are redirected to `/admin/dashboard`
   - Regular users are redirected to `/registration/timeline/{type}/{id}`

2. **Registration Process**:
   - Only regular users can register through signup
   - Admins must be added manually to the database
   - New users get `user` role by default

## Database Structure

### Users Table
- `role` field determines user type (`user`, `admin`)
- Helper methods in User model:
  - `isAdmin()` - Returns true for admin users
  - `isUser()` - Returns true for regular users

## Adding Admin Users

### Method 1: Database Seeder
Run the AdminUserSeeder to create test admin accounts:

```bash
php artisan db:seed --class=AdminUserSeeder
```

This creates:
- Admin: `admin@example.com` / `password`
- Admin 2: `admin2@example.com` / `password`
- Regular User: `user@example.com` / `password`

### Method 2: Manual Database Entry
Insert admin users directly into the database:

```sql
INSERT INTO users (name, email, password, role, email_verified_at, created_at, updated_at)
VALUES ('Admin Name', 'admin@example.com', '$2y$10$...', 'admin', NOW(), NOW(), NOW());
```

### Method 3: Tinker
Use Laravel Tinker to create admin users:

```php
php artisan tinker
User::create([
    'name' => 'Admin Name',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'role' => 'admin',
    'email_verified_at' => now(),
]);
```

## Security Features

1. **AdminMiddleware**: Protects all admin routes
2. **Role-based redirects**: Users are automatically redirected based on their role
3. **Manual admin creation**: Admins cannot register through the signup process
4. **Frontend route protection**: Admin pages require authentication and admin privileges

## Testing the System

1. **Test Regular User Flow**:
   - Register a new user at `/register`
   - Login with the new user
   - Should be redirected to timeline

2. **Test Admin Flow**:
   - Login with admin credentials (`admin@example.com` / `password`)
   - Should be redirected to admin dashboard
   - Access admin features like user management

3. **Test Access Control**:
   - Try to access admin routes without admin privileges
   - Should be denied access

## Future Enhancements

1. **Admin User Management**: Add ability for admins to create new admin users
2. **Advanced Analytics**: More detailed statistics and charts
3. **Audit Logs**: Track admin actions for security
4. **Role Permissions**: Granular permissions for different admin roles
5. **Bulk Operations**: Bulk user/listings management features 
