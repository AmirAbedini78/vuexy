import navItems from '@/navigation/vertical'
import adminNavItems from '@/navigation/vertical/admin'
import { computed } from 'vue'

export const useAdminNavigation = () => {
  const userDataCookie = useCookie("userData")
  
  const isAdmin = computed(() => {
    if (typeof window !== 'undefined') {
      const userData = userDataCookie.value
      console.log('useAdminNavigation - checking user data:', userData)
      const isAdminUser = userData?.role === 'admin'
      console.log('useAdminNavigation - is admin:', isAdminUser)
      return isAdminUser
    }
    return false
  })

  const navigationItems = computed(() => {
    console.log('useAdminNavigation - computing navigation items...')
    console.log('useAdminNavigation - isAdmin.value:', isAdmin.value)
    console.log('useAdminNavigation - userData:', userDataCookie.value)
    
    if (isAdmin.value) {
      console.log('useAdminNavigation - returning admin items:', adminNavItems)
      return adminNavItems
    }
    
    console.log('useAdminNavigation - returning regular items:', navItems)
    return navItems
  })

  return {
    isAdmin,
    navigationItems
  }
} 
