export default [
  {
    title: 'Admin Dashboard',
    icon: { icon: 'tabler-shield-check' },
    children: [
      {
        title: 'Overview',
        to: 'admin-dashboard',
      },
      {
        title: 'Simple Dashboard',
        to: 'admin-simple-dashboard',
      },
      {
        title: 'Statistics',
        to: 'admin-statistics',
      },
      {
        title: 'Test Navigation',
        to: 'admin-test-navigation',
      },
      {
        title: 'Debug',
        to: 'admin-debug',
      },
    ],
  },
  {
    title: 'User Management',
    icon: { icon: 'tabler-users' },
    children: [
      {
        title: 'All Users',
        to: 'admin-users',
      },
      {
        title: 'User Analytics',
        to: 'admin-user-analytics',
      },
    ],
  },
  {
    title: 'Content Management',
    icon: { icon: 'tabler-map-pin' },
    children: [
      {
        title: 'Listings',
        to: 'admin-listings',
      },
      {
        title: 'Categories',
        to: 'admin-categories',
      },
      {
        title: 'Reviews',
        to: 'admin-reviews',
      },
    ],
  },
  {
    title: 'System Management',
    icon: { icon: 'tabler-settings' },
    children: [
      {
        title: 'System Settings',
        to: 'admin-settings',
      },
      {
        title: 'System Logs',
        to: 'admin-logs',
      },
      {
        title: 'Backup & Restore',
        to: 'admin-backup',
      },
    ],
  },
  {
    title: 'Reports & Analytics',
    icon: { icon: 'tabler-chart-bar' },
    children: [
      {
        title: 'User Reports',
        to: 'admin-user-reports',
      },
      {
        title: 'Content Reports',
        to: 'admin-content-reports',
      },
      {
        title: 'Financial Reports',
        to: 'admin-financial-reports',
      },
    ],
  },
] 
