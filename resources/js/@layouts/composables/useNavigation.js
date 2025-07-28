import { useLayoutConfigStore } from '@layouts/stores/config'

export const useNavigation = () => {
  const router = useRouter()
  const configStore = useLayoutConfigStore()
  
  // Navigate to dashboard and preserve sidebar state
  const goToDashboard = async () => {
    // Navigate to dashboard - sidebar state will be handled by route watcher
    await router.push('/')
  }
  
  return {
    goToDashboard,
  }
} 
