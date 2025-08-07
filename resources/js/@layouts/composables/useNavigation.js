import { useLayoutConfigStore } from '@layouts/stores/config'

export const useNavigation = () => {
  const router = useRouter()
  const configStore = useLayoutConfigStore()
  
  // Navigate to dashboard and preserve sidebar state
  const goToDashboard = async () => {
    try {
      // Check if user is admin
      const userDataCookie = useCookie("userData");
      const userData = userDataCookie.value;
      const isAdmin = userData?.role === 'admin';
      
      console.log('useNavigation - goToDashboard called');
      console.log('useNavigation - userData:', userData);
      console.log('useNavigation - isAdmin:', isAdmin);
      
      if (isAdmin) {
        // Admin users always go to admin dashboard
        console.log('useNavigation - redirecting admin to /admin/dashboard');
        await router.push('/admin/dashboard');
      } else {
        // Regular users go to regular dashboard
        console.log('useNavigation - redirecting user to /');
        await router.push('/');
      }
    } catch (error) {
      console.error('useNavigation - error during navigation:', error);
      // Fallback to admin dashboard if there's an error
      await router.push('/admin/dashboard');
    }
  }
  
  return {
    goToDashboard,
  }
} 
