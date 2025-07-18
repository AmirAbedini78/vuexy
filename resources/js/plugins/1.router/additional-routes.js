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
      if (userRole === 'admin')
        return { name: 'dashboards-crm' }
      if (userRole === 'client')
        return { name: 'access-control' }
      
      // Default dashboard for users without specific role
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
    path: '/registration/timeline/:type/:id',
    name: 'registration-timeline',
    component: () => import('@/views/pages/registration/Timeline.vue'),
    meta: {
      layout: 'blank',
      public: true,
    },
  },
  {
    path: '/listing',
    name: 'listing',
    component: () => import('@/pages/listing.vue'),
    meta: {
      layout: 'default',
      action: 'read',
      subject: 'Listing',
    },
  },
  {
    path: '/listing/single-date',
    name: 'listing-single-date',
    component: () => import('@/views/demos/forms/form-wizard/form-wizard-numbered/DemoFormWizardNumberedModernBasicSingleDate.vue'),
    meta: {
      layout: 'default',
      action: 'read',
      subject: 'Listing',
    },
  },
  {
    path: '/listing/multi-date',
    name: 'listing-multi-date',
    component: () => import('@/views/demos/forms/form-wizard/form-wizard-numbered/DemoFormWizardNumberedModernBasicMultiDate.vue'),
    meta: {
      layout: 'default',
      action: 'read',
      subject: 'Listing',
    },
  },
  {
    path: '/listing/open-date',
    name: 'listing-open-date',
    component: () => import('@/views/demos/forms/form-wizard/form-wizard-numbered/DemoFormWizardNumberedModernBasicOpenDate.vue'),
    meta: {
      layout: 'default',
      action: 'read',
      subject: 'Listing',
    },
  },
]
