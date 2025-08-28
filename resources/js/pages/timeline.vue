<template>
  <div v-if="!isRedirecting && currentUser">
    <Timeline :userType="userType" :userId="userId" />
  </div>
  <div v-else-if="!isRedirecting && !currentUser">
    <p>Redirecting to login...</p>
  </div>
  <div v-else>
    <p>Redirecting...</p>
  </div>
</template>

<script setup>
import Timeline from "@/views/pages/registration/Timeline.vue";
import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();
const isRedirecting = ref(false);

// Get user data from logged-in user
const getCurrentUser = () => {
  const userDataCookie = useCookie("userData");
  if (userDataCookie.value) {
    return userDataCookie.value;
  }
  return null;
};

const currentUser = getCurrentUser();

// Handle redirects based on user state and query parameters
if (currentUser && route.query.verified === "true") {
  // User is logged in and email was verified, redirect to proper timeline route
  isRedirecting.value = true;
  const userType =
    currentUser.role === "user"
      ? "individual"
      : currentUser.role === "company"
      ? "company"
      : "individual";
  console.log(
    "Redirecting to verified timeline:",
    `/registration/timeline/${userType}/${currentUser.id}?verified=true`
  );
  router.replace(
    `/registration/timeline/${userType}/${currentUser.id}?verified=true`
  );
} else if (currentUser && !route.query.verified) {
  // User is logged in but no verified parameter, redirect to proper timeline route
  isRedirecting.value = true;
  const userType =
    currentUser.role === "user"
      ? "individual"
      : currentUser.role === "company"
      ? "company"
      : "individual";
  console.log(
    "Redirecting to timeline:",
    `/registration/timeline/${userType}/${currentUser.id}`
  );
  router.replace(`/registration/timeline/${userType}/${currentUser.id}`);
} else if (!currentUser) {
  // If no user data, redirect to login
  isRedirecting.value = true;
  console.log("Redirecting to login");
  router.replace("/login");
}

// Set user type and ID for Timeline component (fallback values)
const userType =
  currentUser?.role === "user"
    ? "individual"
    : currentUser?.role === "company"
    ? "company"
    : "individual";
const userId = currentUser?.id || route.query.id || 1;

// Debug logging
console.log("Timeline page mounted:", {
  currentUser,
  routeQuery: route.query,
  userType,
  userId,
  isRedirecting: isRedirecting.value,
});

definePage({
  meta: {
    layout: "default",
    layoutWrapperClasses: "hide-vertical-nav",
    action: "read",
    subject: "Welcome",
    public: true,
  },
});
</script>
