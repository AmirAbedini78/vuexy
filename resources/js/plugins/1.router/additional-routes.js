const emailRouteComponent = () => import('@/pages/apps/email/index.vue')

// 👉 Redirects
export const redirects = [
  // ℹ️ We are redirecting to different pages based on role.
  // NOTE: Role is just for UI purposes. ACL is based on abilities.
  {
    path: '/',
    name: 'index',
    redirect: to => {
      // TODO: Get type from backend
      const userData = useCookie('userData')
      const accessToken = useCookie('accessToken')
      
      // If user is not logged in, redirect to login
      if (!userData.value || !accessToken.value) {
        return { name: 'login', query: to.query }
      }
      
      const userRole = userData.value?.role
      
      // Check if user is admin - always redirect to admin dashboard
      if (userRole === 'admin') {
        return { name: 'admin-dashboard' }
      }
      
      // For client role (if exists)
      if (userRole === 'client') {
        return { name: 'access-control' }
      }
      
      // Default dashboard for regular users
      return { name: 'dashboards-crm' }
    },
  },
  {
    path: '/pages/user-profile',
    name: 'pages-user-profile',
    redirect: () => ({ name: 'pages-user-profile-tab', params: { tab: 'profile' } }),
  },
  {
    path: '/pages/account-settings',
    name: 'pages-account-settings',
    redirect: () => ({ name: 'pages-account-settings-tab', params: { tab: 'account' } }),
  },
]
export const routes = [
  // Email filter
  {
    path: '/apps/email/filter/:filter',
    name: 'apps-email-filter',
    component: emailRouteComponent,
    meta: {
      navActiveLink: 'apps-email',
      layoutWrapperClasses: 'layout-content-height-fixed',
    },
  },

  // Email label
  {
    path: '/apps/email/label/:label',
    name: 'apps-email-label',
    component: emailRouteComponent,
    meta: {
      // contentClass: 'email-application',
      navActiveLink: 'apps-email',
      layoutWrapperClasses: 'layout-content-height-fixed',
    },
  },
  {
    path: '/dashboards/logistics',
    name: 'dashboards-logistics',
    component: () => import('@/pages/apps/logistics/dashboard.vue'),
  },
  {
    path: '/dashboards/academy',
    name: 'dashboards-academy',
    component: () => import('@/pages/apps/academy/dashboard.vue'),
  },
  {
    path: '/apps/ecommerce/dashboard',
    name: 'apps-ecommerce-dashboard',
    component: () => import('@/pages/dashboards/ecommerce.vue'),
  },
  {
    path: '/verify-email',
    name: 'verify-email-page',
    component: () => import('@/pages/verify-email.vue'),
    meta: {
      layout: 'blank',
      public: true,
    },
  },
  {
    path: '/reset-password/:token',
    name: 'reset-password',
    component: () => import('@/pages/reset-password.vue'),
    meta: {
      layout: 'blank',
      public: true,
    },
  },
  {
    path: '/registration/activity',
    name: 'registration-activity',
    component: () => import('@/views/pages/registration/Activity.vue'),
    meta: {
      layout: 'blank',
      public: true,
    },
  },
  {
    path: '/welcome',
    name: 'welcome',
    component: () => import('@/pages/welcome.vue'),
    meta: {
      layout: 'default',
      action: 'read',
      subject: 'Welcome',
    },
  },
  {
    path: '/registration/timeline/:type/:id',
    name: 'registration-timeline',
    component: () => import('@/views/pages/registration/Timeline.vue'),
    meta: {
      layout: 'default',
      public: true,
    },
  },
  // Admin routes
  {
    path: '/admin/dashboard',
    name: 'admin-dashboard',
    component: () => import('@/pages/admin/dashboard.vue'),
    meta: {
      layout: 'default',
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: '/admin/users',
    name: 'admin-users',
    component: () => import('@/pages/admin/users.vue'),
    meta: {
      layout: 'default',
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: '/admin/listings',
    name: 'admin-listings',
    component: () => import('@/pages/admin/listings.vue'),
    meta: {
      layout: 'default',
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: '/admin/statistics',
    name: 'admin-statistics',
    component: () => import('@/pages/admin/statistics.vue'),
    meta: {
      layout: 'default',
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: '/admin/test-navigation',
    name: 'admin-test-navigation',
    component: () => import('@/pages/admin/test-navigation.vue'),
    meta: {
      layout: 'default',
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: '/admin/debug',
    name: 'admin-debug',
    component: () => import('@/pages/admin/debug.vue'),
    meta: {
      layout: 'default',
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: '/admin/simple-dashboard',
    name: 'admin-simple-dashboard',
    component: () => import('@/pages/admin/simple-dashboard.vue'),
    meta: {
      layout: 'default',
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: '/listing',
    name: 'listing',
    component: () => import('@/pages/listing.vue'),
    meta: {
      layout: 'default',
      public: true, // Make it public like access control
    },
  },
  {
    path: '/listing/single-date',
    name: 'listing-single-date',
    component: () => import('@/views/demos/forms/form-wizard/form-wizard-numbered/DemoFormWizardNumberedModernBasicSingleDate.vue'),
    meta: {
      layout: 'default',
      public: true,
    },
  },
  {
    path: '/listing/multi-date',
    name: 'listing-multi-date',
    component: () => import('@/views/demos/forms/form-wizard/form-wizard-numbered/DemoFormWizardNumberedModernBasicMultiDate.vue'),
    meta: {
      layout: 'default',
      public: true,
    },
  },
  {
    path: '/listing/open-date',
    name: 'listing-open-date',
    component: () => import('@/views/demos/forms/form-wizard/form-wizard-numbered/DemoFormWizardNumberedModernBasicOpenDate.vue'),
    meta: {
      layout: 'default',
      public: true,
    },
  },
  // User sidebar routes
  {
    path: '/all-events',
    name: 'all-events',
    component: () => import('@/pages/all-events.vue'),
    meta: {
      layout: 'default',
      action: 'read',
      subject: 'Events',
    },
  },
  {
    path: '/create-listing',
    name: 'create-listing',
    component: () => import('@/pages/create-listing.vue'),
    meta: {
      layout: 'default',
      action: 'read',
      subject: 'Listing',
    },
  },
  {
    path: '/add-event',
    name: 'add-event',
    component: () => import('@/pages/add-event.vue'),
    meta: {
      layout: 'default',
      action: 'read',
      subject: 'Events',
    },
  },
  {
    path: '/all-bookings',
    name: 'all-bookings',
    component: () => import('@/pages/all-bookings.vue'),
    meta: {
      layout: 'default',
      action: 'read',
      subject: 'Bookings',
    },
  },
  {
    path: '/invite-participants',
    name: 'invite-participants',
    component: () => import('@/pages/invite-participants.vue'),
    meta: {
      layout: 'default',
      action: 'read',
      subject: 'Participants',
    },
  },
  {
    path: '/all-participants',
    name: 'all-participants',
    component: () => import('@/pages/all-participants.vue'),
    meta: {
      layout: 'default',
      action: 'read',
      subject: 'Participants',
    },
  },
  {
    path: '/booking-statistics',
    name: 'booking-statistics',
    component: () => import('@/pages/booking-statistics.vue'),
    meta: {
      layout: 'default',
      action: 'read',
      subject: 'Statistics',
    },
  },
  {
    path: '/wallet',
    name: 'wallet',
    component: () => import('@/pages/wallet.vue'),
    meta: {
      layout: 'default',
      action: 'read',
      subject: 'Finance',
    },
  },
  {
    path: '/finance-statistics',
    name: 'finance-statistics',
    component: () => import('@/pages/finance-statistics.vue'),
    meta: {
      layout: 'default',
      action: 'read',
      subject: 'Statistics',
    },
  },
  {
    path: '/user-profile-settings',
    name: 'user-profile-settings',
    component: () => import('@/pages/user-profile-settings.vue'),
    meta: {
      layout: 'default',
      action: 'read',
      subject: 'Profile',
    },
  },
  {
    path: '/account-settings',
    name: 'account-settings',
    component: () => import('@/pages/account-settings.vue'),
    meta: {
      layout: 'default',
      action: 'read',
      subject: 'Settings',
    },
  },
  {
    path: '/get-support',
    name: 'get-support',
    component: () => import('@/pages/get-support.vue'),
    meta: {
      layout: 'default',
      action: 'read',
      subject: 'Support',
    },
  },
]
