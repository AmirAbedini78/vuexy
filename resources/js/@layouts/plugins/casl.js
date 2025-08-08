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
  // For now, always return true to show all menu items
  return true
}

export const canNavigate = to => {
  // For now, always allow navigation
  return true
}
