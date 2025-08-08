// ACL Configuration
// Define initial ability rules for the application

export const initialAbility = [
  {
    action: 'read',
    subject: 'all',
  },
  {
    action: 'create',
    subject: 'all',
  },
  {
    action: 'update',
    subject: 'all',
  },
  {
    action: 'delete',
    subject: 'all',
  },
  {
    action: 'manage',
    subject: 'all',
  },
]

// Default user ability rules
export const defaultUserAbility = [
  {
    action: 'read',
    subject: 'Events',
  },
  {
    action: 'read',
    subject: 'Listing',
  },
  {
    action: 'read',
    subject: 'Bookings',
  },
  {
    action: 'read',
    subject: 'Participants',
  },
  {
    action: 'read',
    subject: 'Statistics',
  },
  {
    action: 'read',
    subject: 'Finance',
  },
  {
    action: 'read',
    subject: 'Profile',
  },
  {
    action: 'read',
    subject: 'Settings',
  },
  {
    action: 'read',
    subject: 'Support',
  },
]

// Admin ability rules
export const adminAbility = [
  {
    action: 'manage',
    subject: 'all',
  },
] 
