# Auto-Save System Setup

## Overview
This system implements auto-save functionality for listing forms that saves data to both localStorage and database.

## Database Setup

### 1. Run Migration
Execute the SQL file to create the auto_save_listings table:

```sql
-- Run the SQL file: database/migrations/create_auto_save_listings_table.sql
```

Or manually create the table using the provided SQL.

### 2. Laravel Artisan (if available)
If you have Laravel CLI access, run:
```bash
php artisan migrate
```

## Features Implemented

### 1. Auto-Save Database Table
- **Table**: `auto_save_listings`
- **Purpose**: Stores auto-saved form data with status "on_process"
- **Fields**: All listing form fields + metadata

### 2. Auto-Save API Endpoints
- `GET /api/auto-save-listings` - Get user's auto-save listings
- `POST /api/auto-save-listings` - Save/update auto-save data
- `GET /api/auto-save-listings/{id}` - Get specific auto-save listing
- `PUT /api/auto-save-listings/{id}` - Update auto-save listing
- `DELETE /api/auto-save-listings/{id}` - Delete auto-save listing
- `POST /api/auto-save-listings/{id}/convert` - Convert to regular listing

### 3. Updated Components

#### useAutoSave Composable
- Added `saveToDatabase` option
- Added `listingType` parameter
- Added `apiEndpoint` parameter
- Now saves to both localStorage and database

#### Form Wizards
- **Single Date**: `DemoFormWizardNumberedModernBasicSingleDate.vue`
- **Multi Date**: `DemoFormWizardNumberedModernBasicMultiDate.vue`
- **Open Date**: `DemoFormWizardNumberedModernBasicOpenDate.vue`

All wizards now save to database with appropriate listing type.

#### User Dashboard
- **File**: `resources/js/pages/dashboards/crm.vue`
- Shows both regular listings and auto-save listings
- Auto-save listings have status "On Process" with orange color
- Auto-save listings have "AUTO-" prefix in Adventure ID

## Status System

### Auto-Save Statuses
- **"on_process"**: Auto-saved data (default)
- **"submitted"**: Converted to regular listing

### Display Statuses
- **"On Process"**: Auto-saved data (orange color)
- **"Submitted"**: Regular listing data

## Usage

### 1. Form Auto-Save
When users fill forms, data is automatically saved to:
- localStorage (immediate)
- Database (with debounce)

### 2. Dashboard Display
User dashboard shows:
- Regular listings (submitted)
- Auto-save listings (on process)
- Combined and sorted by update time

### 3. Form Continuation
When users return to forms:
- Data is loaded from localStorage
- If no localStorage data, loads from database
- Continues from where they left off

## API Integration

### Auto-Save Data Structure
```javascript
{
  listing_type: 'single-date' | 'multi-date' | 'open-date',
  form_data: { /* all form fields */ },
  adventure_title: 'string',
  description: 'string',
  location: 'string',
  price: number,
  // ... other fields
}
```

### Response Format
```javascript
{
  success: true,
  data: { /* auto-save listing data */ },
  message: 'Auto-save data saved successfully'
}
```

## Testing

1. Fill out a listing form
2. Check browser localStorage for saved data
3. Check database for auto-save record
4. Check user dashboard for "On Process" status
5. Complete form submission
6. Verify conversion to regular listing

## Troubleshooting

### Common Issues
1. **Migration not run**: Execute the SQL file manually
2. **API not working**: Check routes/api.php for auto-save endpoints
3. **Data not saving**: Check browser console for errors
4. **Status not showing**: Verify database connection

### Debug Steps
1. Check browser console for auto-save logs
2. Verify API endpoints are accessible
3. Check database for auto-save records
4. Verify user authentication
