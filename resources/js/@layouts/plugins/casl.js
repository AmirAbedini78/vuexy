import { createMongoAbility } from '@casl/ability'
import { initialAbility } from './configs/acl'

//  Read ability from localStorage
// * Handles auto fetching previous ability if exists
// * You can update it if you consider user is not verified
// ! Bitte entfernen Sie es, wenn der Benutzer nicht verifiziert ist
const existingAbility = localStorage.getItem('userAbility') ? JSON.parse(localStorage.getItem('userAbility')) : null

export default createMongoAbility(existingAbility || initialAbility)

export const can = (action, resource) => {
  // For now, allow all actions for all resources
  return true
}

export const setAbility = ability => {
  localStorage.setItem('userAbility', JSON.stringify(ability))
}

export const getAbility = () => {
  return JSON.parse(localStorage.getItem('userAbility'))
}

/**
 * Check if user can view item based on it's ability
 * Based on item's action and subject & Hide group if all of it's children are hidden
 * @param {object} item navigation object item
 */
export const canViewNavMenuGroup = item => {
  // Check if user is admin
  const userDataCookie = useCookie("userData");
  const userData = userDataCookie.value;
  const isAdmin = userData?.role === 'admin';
  
  if (isAdmin) {
    // Admin users can see all menu items
    return true;
  }
  
  // For regular users, check provider status
  const providerStatus = localStorage.getItem('providerStatus');
  
  // If no provider status or not found, only show Welcome
  if (!providerStatus || providerStatus === 'not_found') {
    return item.title === 'Welcome';
  }
  
  // If provider status is 'active', show all menu items except Welcome
  if (providerStatus === 'active') {
    return item.title !== 'Welcome';
  }
  
  // For 'approved' or 'rejected' status, only show Welcome
  return item.title === 'Welcome';
}

export const canNavigate = to => {
  // Check if user is admin
  const userDataCookie = useCookie("userData");
  const userData = userDataCookie.value;
  const isAdmin = userData?.role === 'admin';
  
  if (isAdmin) {
    // Admin users can navigate anywhere
    return true;
  }
  
  // For regular users, check provider status
  const providerStatus = localStorage.getItem('providerStatus');

  // Always allow access-control for logged-in users to complete their profile
  const isAccessControlRoute = to?.name === 'access-control' || to?.path === '/access-control';
  if (isAccessControlRoute) {
    return true;
  }
  
  // If no provider status, allow only timeline, welcome, home
  if (!providerStatus || providerStatus === 'not_found') {
    return to?.name === 'timeline' || to?.name === 'welcome' || to?.path === '/';
  }
  
  // If provider status is 'active', allow all navigation except Welcome
  if (providerStatus === 'active') {
    return to?.name !== 'welcome';
  }
  
  // For 'approved' or 'rejected' status, allow limited pages
  return to?.name === 'timeline' || to?.name === 'welcome' || to?.path === '/';
}
