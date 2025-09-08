import { canNavigate } from '@layouts/plugins/casl'

export const setupGuards = router => {
  // 👉 router.beforeEach
  // Docs: https://router.vuejs.org/guide/advanced/navigation-guards.html#global-before-guards
  router.beforeEach(to => {
    /*
         * If it's a public route, continue navigation. This kind of pages are allowed to visited by login & non-login users. Basically, without any restrictions.
         * Examples of public routes are, 404, under maintenance, etc.
         */
    if (to.meta.public)
      return

    /**
         * Check if user is logged in by checking if token & user data exists in cookies
         * Feel free to update this logic to suit your needs
         */
    const userData = useCookie('userData')
    const accessToken = useCookie('accessToken')
    const isLoggedIn = !!(userData.value && accessToken.value)

    /*
          If user is logged in and is trying to access login like page, redirect to home
          else allow visiting the page
          (WARN: Don't allow executing further by return statement because next code will check for permissions)
         */
    if (to.meta.unauthenticatedOnly) {
      if (isLoggedIn) {
        console.log('User is logged in, checking provider status for redirect')
        
        // Check if user is admin
        if (userData.value?.role === 'admin') {
          console.log('Admin user, redirecting to admin dashboard')
          return '/admin/dashboard'
        }
        
        // For regular users, check provider status
        const providerStatus = localStorage.getItem('providerStatus')
        if (providerStatus === 'active') {
          console.log('User is active, redirecting to dashboard')
          return '/'
        } else {
          console.log('User is not active, redirecting to timeline')
          // Determine user type for timeline routing
          let userType = "individual"; // Default type
          if (userData.value?.user_type) {
            userType = userData.value.user_type;
          } else if (userData.value?.role === "company") {
            userType = "company";
          } else if (userData.value?.role === "individual") {
            userType = "individual";
          }
          return `/registration/timeline/${userType}/${userData.value.id}`
        }
      }
      else {
        console.log('User is not logged in, allowing access to auth page')
        return undefined
      }
    }

    // If user is not logged in and trying to access protected route, redirect to login
    if (!isLoggedIn && to.path !== '/login' && to.path !== '/register' && to.path !== '/verify-email') {
      console.log('User is not logged in, redirecting to login')
      return {
        name: 'login',
        query: {
          ...to.query,
          to: to.fullPath !== '/' ? to.path : undefined,
        },
      }
    }

    // Only check permissions for logged-in users and routes that have explicit permissions
    if (isLoggedIn && to.matched.some(route => route.meta?.action && route.meta?.subject)) {
      if (!canNavigate(to)) {
        console.log('User does not have permission, redirecting to not-authorized')
        return { name: 'not-authorized' }
      }
    }
  })
}
