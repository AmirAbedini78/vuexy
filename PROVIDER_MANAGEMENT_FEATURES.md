# Provider Management Features

This document describes the new provider management functionality that has been implemented in the admin dashboard and providers page.

## Overview

The provider management system allows administrators to:
- View all providers (individual and company) in a data table
- View detailed provider information in a modal
- Edit provider information using a wizard interface
- Delete providers with confirmation
- Search and filter providers by type and status

## Features Implemented

### 1. Data Table with Actions

The providers table now includes:
- **Eye Icon (👁️)**: Click to view detailed provider information
- **Three Dots Menu (⋮)**: Contains edit and delete options
  - Edit: Opens the provider edit wizard
  - Delete: Shows confirmation dialog before deletion

### 2. Provider View Modal

When clicking the eye icon, a modal opens showing:
- Basic provider information (name, type, specialization, etc.)
- Detailed information based on provider type:
  - **Individual Providers**: Personal information, emergency contacts, etc.
  - **Company Providers**: Company details, VAT ID, contact person, etc.

### 3. Provider Edit Wizard

A vertical wizard interface for editing provider information:

#### Individual Provider Wizard Steps:
1. **Personal Information**: Name, nationality, address, city, state, postal code, country, DOB, languages
2. **Account Details**: Specialization, experience, emergency contacts, bio, country of operation
3. **Social Links**: Social media links and terms acceptance

#### Company Provider Wizard Steps:
1. **Company Information**: Company name, VAT ID, address, contact person, registration country, business type
2. **Business Details**: Specialization, bio, website, listing preference
3. **Social Links**: Social media links and terms acceptance

### 4. Backend API Endpoints

New API endpoints have been added:
- `PUT /api/admin/providers/{id}/{type}` - Update provider information
- `DELETE /api/admin/providers/{id}/{type}` - Delete provider

### 5. Enhanced Data Display

The admin controller now returns additional fields for better provider information display:
- Individual users: nationality, address details, emergency contacts, etc.
- Company users: VAT ID, contact person, registration country, etc.

## File Structure

```
resources/js/
├── pages/admin/
│   ├── providers.vue          # Dedicated providers management page
│   └── dashboard.vue          # Main admin dashboard with providers section
└── components/admin/
    └── ProviderEditWizard.vue # Provider edit wizard component

app/Http/Controllers/
└── AdminController.php         # Backend controller with CRUD operations

routes/
└── api.php                     # API routes for admin operations
```

## Usage

### For Administrators:

1. **Navigate to Admin Dashboard** (`/admin/dashboard`)
   - View providers in the "All Providers" section
   - Use search and filter options

2. **Navigate to Providers Page** (`/admin/providers`)
   - Dedicated page for provider management
   - More detailed table with all provider information

3. **View Provider Details**:
   - Click the eye icon (👁️) on any provider row
   - Modal shows comprehensive provider information

4. **Edit Provider**:
   - Click the three dots menu (⋮) → Edit
   - Use the wizard interface to update information
   - Navigate between steps using Previous/Next buttons
   - Click "Update Provider" on the final step

5. **Delete Provider**:
   - Click the three dots menu (⋮) → Delete
   - Confirm deletion in the confirmation dialog

### Wizard Navigation:

- **Step Validation**: Each step validates required fields before allowing progression
- **Field Validation**: Real-time validation with error messages
- **Responsive Design**: Wizard adapts to different screen sizes
- **Vertical Layout**: Steps are displayed vertically for better mobile experience

## Technical Implementation

### Frontend Components:
- **VDataTable**: For displaying provider data
- **VDialog**: For modals (view, edit, delete confirmation)
- **VWindow**: For wizard step navigation
- **Custom Stepper**: Visual step indicator

### State Management:
- Reactive data using Vue 3 Composition API
- Form validation with error handling
- API integration for CRUD operations

### Backend Features:
- Input validation and sanitization
- Error handling and logging
- Database operations with proper relationships
- RESTful API design

## Future Enhancements

Potential improvements for future versions:
1. **Bulk Operations**: Select multiple providers for batch actions
2. **Advanced Filtering**: Date range, status changes, etc.
3. **Provider Analytics**: Performance metrics and statistics
4. **Audit Trail**: Track changes made to provider information
5. **Export Functionality**: Export provider data to CSV/Excel
6. **Provider Verification**: Additional verification workflows
7. **Notification System**: Alert admins of important changes

## Notes

- The wizard interface is designed to be similar to the access control wizards but adapted for editing existing data
- All forms include proper validation and error handling
- The system supports both individual and company provider types
- Responsive design ensures usability on all device sizes
- Backend includes proper error handling and logging for debugging
