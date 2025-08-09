export default [
  // Dashboard
  {
    title: 'Dashboard',
    icon: { icon: 'tabler-compass' },
    to: 'admin-dashboard',
    badgeContent: '5',
    badgeClass: 'bg-error',
  },

  // Adventures
  { heading: 'Adventures' },
  {
    title: 'View All Events',
    icon: { icon: 'tabler-calendar' },
    to: 'all-events',
  },
  {
    title: 'Event Statistics',
    icon: { icon: 'tabler-chart-bar' },
    comingSoon: true,
  },

  // Bookings
  { heading: 'Bookings' },
  {
    title: 'All Bookings',
    icon: { icon: 'tabler-list-details' },
    to: 'all-bookings',
  },
  {
    title: 'Booking Statistics',
    icon: { icon: 'tabler-chart-bar' },
    to: 'booking-statistics',
  },

  // User Management
  { heading: 'User Management' },
  {
    title: 'Super Admins',
    icon: { icon: 'tabler-star' },
    comingSoon: true,
  },
  {
    title: 'Admins',
    icon: { icon: 'tabler-user-star' },
    comingSoon: true,
  },
  {
    title: 'Providers',
    icon: { icon: 'tabler-user-heart' },
    comingSoon: true,
  },
  {
    title: 'Users',
    icon: { icon: 'tabler-users' },
    to: 'admin-users',
  },
  {
    title: 'Roles & Permissions',
    icon: { icon: 'tabler-shield-lock' },
    comingSoon: true,
  },

  // Finance
  { heading: 'Finance' },
  {
    title: 'Incomes',
    icon: { icon: 'tabler-currency-dollar' },
    comingSoon: true,
  },
  {
    title: 'Orders',
    icon: { icon: 'tabler-shopping-bag' },
    comingSoon: true,
  },
  {
    title: 'Invoices',
    icon: { icon: 'tabler-file-invoice' },
    comingSoon: true,
  },
  {
    title: 'Create/ Send Invoice',
    icon: { icon: 'tabler-send' },
    comingSoon: true,
  },
  {
    title: 'Finance Statistics',
    icon: { icon: 'tabler-chart-bar' },
    to: 'finance-statistics',
  },

  // Support Management
  { heading: 'Support Management' },
  {
    title: 'All Requests',
    icon: { icon: 'tabler-messages' },
    comingSoon: true,
  },
  {
    title: 'Create Ticket',
    icon: { icon: 'tabler-message-plus' },
    comingSoon: true,
  },
] 
