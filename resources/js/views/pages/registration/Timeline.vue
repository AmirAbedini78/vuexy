<script setup>
import { onMounted, onUnmounted, ref, watch } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();
const loading = ref(false);
const error = ref(null);
const userData = ref({ email: "", whatsapp: "" });
const timelineSteps = ref([]);

// Get user type and ID from route
const userType = route.params.type; // 'individual' or 'company' - will be removed
const userId = route.params.id; // will be removed

// Get user data from logged-in user instead of route params
const getCurrentUser = () => {
  const userDataCookie = useCookie("userData");
  if (userDataCookie.value) {
    return userDataCookie.value;
  }
  return null;
};

// Get current user data
const currentUser = ref(null);

// Email Verification State
const email = ref("");
const emailStatus = ref("pending"); // 'pending', 'sent', 'verified', 'error'
const emailMessage = ref("");
const emailLoading = ref(false);

// WhatsApp Verification State
const whatsappNumber = ref("");
const whatsappStatus = ref("pending"); // 'pending', 'sent', 'verified', 'error'
const whatsappMessage = ref("");
const whatsappLoading = ref(false);
const showWhatsappModal = ref(false);
const whatsappCode = ref("");
const whatsappCodeLoading = ref(false);

// LinkedIn Verification State
const linkedinStatus = ref("pending"); // 'pending', 'verified', 'error'
const linkedinMessage = ref("");
const linkedinLoading = ref(false);

// Profile Completion State
const profileCompleted = ref(false);
const reviewStatus = ref("awaiting_approval"); // 'awaiting_approval', 'verified_contact', 'needs_edit', 'incomplete'

// Step 4 Progress State
const step4Enabled = ref(false);
const progressPercentage = ref(0);
const completedVerifications = ref(0);
const totalVerifications = ref(3); // email, whatsapp, linkedin

// Real-time verification polling
const pollingInterval = ref(null);
const isPolling = ref(false);
const maxPollingAttempts = ref(10); // Maximum 10 attempts (50 seconds)
const pollingAttempts = ref(0);

// Success notification state
const showSuccessNotification = ref(false);
const successMessage = ref("");

// Debug mode
const showDebugPanel = ref(false);

// Persist step 4 state to cookie for sidebar gating
const step4Cookie = useCookie("step4Enabled");
watch(step4Enabled, (val) => {
  const str = val ? "true" : "";
  step4Cookie.value = str;
  if (typeof window !== "undefined") {
    try {
      localStorage.setItem("step4Enabled", val ? "true" : "");
    } catch (e) {}
  }
});

// Persist a simpler verification flag: any of email/whatsapp/linkedin verified
const verifiedCookie = useCookie("userVerified");
const updateVerifiedFlag = () => {
  const isVerified =
    emailStatus.value === "verified" ||
    whatsappStatus.value === "verified" ||
    linkedinStatus.value === "verified";
  verifiedCookie.value = isVerified ? "true" : "";
  if (typeof window !== "undefined") {
    try {
      localStorage.setItem("userVerified", isVerified ? "true" : "");
    } catch (e) {}
  }
};

watch([emailStatus, whatsappStatus, linkedinStatus], () => {
  updateVerifiedFlag();
});

// Fetch verification status on mount
const fetchVerificationStatus = async () => {
  try {
    // Get current user ID from logged-in user data
    const user = currentUser.value;
    if (!user || !user.id) {
      console.error("No user data available");
      return;
    }

    const res = await fetch(`/api/verification/user/${user.id}`);
    const data = await res.json();

    if (data.success && data.data) {
      // Check email verification status
      if (data.data.email_verified) {
        emailStatus.value = "verified";
        emailMessage.value = "Email verified successfully!";
      } else if (data.data.email_token === "pending") {
        emailStatus.value = "sent";
        emailMessage.value =
          "Verification email sent. Please check your inbox and click the verification link.";
      } else {
        emailStatus.value = "pending";
        emailMessage.value = "";
      }

      // Check WhatsApp verification status
      if (data.data.whatsapp_verified) {
        whatsappStatus.value = "verified";
        whatsappMessage.value = "WhatsApp Verified";
      } else if (data.data.whatsapp_code) {
        whatsappStatus.value = "sent";
        whatsappMessage.value =
          "Verification code sent. Please check WhatsApp.";
      } else {
        whatsappStatus.value = "pending";
        whatsappMessage.value = "";
      }
      if (data.data.whatsapp_number) {
        whatsappNumber.value = data.data.whatsapp_number;
      }

      // Check LinkedIn verification status
      if (data.data.linkedin_verified) {
        linkedinStatus.value = "verified";
        linkedinMessage.value = "LinkedIn Connected";
        linkedinLoading.value = false;
      } else {
        linkedinStatus.value = "pending";
        linkedinMessage.value = "";
        linkedinLoading.value = false;
      }

      // Set user email if available
      if (data.data.email) {
        email.value = data.data.email;
      }
    } else {
      // If no verification data exists, set to pending
      emailStatus.value = "pending";
      emailMessage.value = "";
      whatsappStatus.value = "pending";
      whatsappMessage.value = "";
      linkedinStatus.value = "pending";
      linkedinMessage.value = "";
    }

    // Check if user just came back from email verification
    if (route.query.verified === "true") {
      emailStatus.value = "verified";
      emailMessage.value = "Email verified successfully! Welcome back!";
    }

    // Calculate progress after updating all statuses
    calculateProgress();
  } catch (e) {
    console.error("Error fetching verification status:", e);
    emailStatus.value = "error";
    emailMessage.value =
      "Could not fetch verification status. Please refresh the page.";
  }
};

// Start polling for verification status updates
const startVerificationPolling = () => {
  if (isPolling.value) return;

  isPolling.value = true;
  pollingAttempts.value = 0;

  console.log("Starting verification status polling...");

  pollingInterval.value = setInterval(async () => {
    pollingAttempts.value++;

    try {
      const res = await fetch(`/api/verification/user/${currentUser.value.id}`);
      const data = await res.json();

      if (data.success && data.data) {
        const wasEmailVerified = emailStatus.value === "verified";
        const isEmailVerified = data.data.email_verified;

        // Update email status
        if (isEmailVerified && !wasEmailVerified) {
          emailStatus.value = "verified";
          emailMessage.value = "Email verified successfully!";
          console.log("Email verification detected during polling!");

          // Show success notification
          showNotification("🎉 Email verification completed successfully!");

          // Stop polling once email is verified
          stopVerificationPolling();
          return;
        }

        // Update other verification statuses
        if (data.data.whatsapp_verified) {
          whatsappStatus.value = "verified";
          whatsappMessage.value = "WhatsApp Verified";
        }

        if (data.data.linkedin_verified) {
          linkedinStatus.value = "verified";
          linkedinMessage.value = "LinkedIn Connected";
        }

        // Recalculate progress
        calculateProgress();

        console.log(
          `Polling attempt ${pollingAttempts.value}: Email verified = ${isEmailVerified}`
        );
      } else {
        console.log(
          `Polling attempt ${pollingAttempts.value}: No data received`
        );
      }
    } catch (e) {
      console.error(
        `Error during verification polling (attempt ${pollingAttempts.value}):`,
        e
      );
    }

    // Stop polling after max attempts
    if (pollingAttempts.value >= maxPollingAttempts.value) {
      console.log("Stopping verification polling - max attempts reached");
      stopVerificationPolling();

      // Update message to inform user that automatic checking has stopped
      if (emailStatus.value === "sent") {
        emailMessage.value =
          "Verification email sent. Please check your inbox and click the verification link. You can manually check your status using the 'Check Status' button.";
      }
    }
  }, 5000); // Check every 5 seconds
};

// Stop polling for verification status
const stopVerificationPolling = () => {
  if (pollingInterval.value) {
    clearInterval(pollingInterval.value);
    pollingInterval.value = null;
  }
  isPolling.value = false;
  console.log("Verification polling stopped");
};

// Show success notification
const showNotification = (message, duration = 5000) => {
  successMessage.value = message;
  showSuccessNotification.value = true;

  setTimeout(() => {
    showSuccessNotification.value = false;
  }, duration);
};

// Manual refresh verification status
const refreshVerificationStatus = async () => {
  console.log("Manual verification status refresh requested");
  await fetchVerificationStatusWithRetry(false);
};

// Enhanced verification status fetch with immediate retry for email verification
const fetchVerificationStatusWithRetry = async (
  isEmailVerificationReturn = false
) => {
  try {
    const res = await fetch(`/api/verification/user/${currentUser.value.id}`);
    const data = await res.json();

    if (data.success && data.data) {
      const wasEmailVerified = emailStatus.value === "verified";
      const isEmailVerified = data.data.email_verified;

      // Check email verification status
      if (isEmailVerified) {
        emailStatus.value = "verified";
        emailMessage.value = "Email verified successfully!";

        // If this is a return from email verification and status just changed, show success message
        if (isEmailVerificationReturn && !wasEmailVerified) {
          emailMessage.value = "Email verified successfully! Welcome back!";

          // Show success notification
          showNotification("🎉 Email verification completed successfully!");

          // Show a brief success notification
          setTimeout(() => {
            emailMessage.value = "Email verified successfully!";
          }, 3000);
        }
      } else if (data.data.email_token === "pending") {
        emailStatus.value = "sent";
        emailMessage.value =
          "Verification email sent. Please check your inbox and click the verification link.";
      } else {
        emailStatus.value = "pending";
        emailMessage.value = "";
      }

      // Check WhatsApp verification status
      if (data.data.whatsapp_verified) {
        whatsappStatus.value = "verified";
        whatsappMessage.value = "WhatsApp Verified";
      } else if (data.data.whatsapp_code) {
        whatsappStatus.value = "sent";
        whatsappMessage.value =
          "Verification code sent. Please check WhatsApp.";
      } else {
        whatsappStatus.value = "pending";
        whatsappMessage.value = "";
      }
      if (data.data.whatsapp_number) {
        whatsappNumber.value = data.data.whatsapp_number;
      }

      // Check LinkedIn verification status
      if (data.data.linkedin_verified) {
        linkedinStatus.value = "verified";
        linkedinMessage.value = "LinkedIn Connected";
        linkedinLoading.value = false;
      } else {
        linkedinStatus.value = "pending";
        linkedinMessage.value = "";
        linkedinLoading.value = false;
      }

      // Set user email if available
      if (data.data.email) {
        email.value = data.data.email;
      }

      // Calculate progress after updating all statuses
      calculateProgress();

      // If email is verified and we're returning from verification, stop polling
      if (isEmailVerified && isEmailVerificationReturn) {
        stopVerificationPolling();
      }

      return data.data;
    } else {
      // If no verification data exists, set to pending
      emailStatus.value = "pending";
      emailMessage.value = "";
      whatsappStatus.value = "pending";
      whatsappMessage.value = "";
      linkedinStatus.value = "pending";
      linkedinMessage.value = "";
    }
  } catch (e) {
    console.error("Error fetching verification status:", e);
    emailStatus.value = "error";
    emailMessage.value =
      "Could not fetch verification status. Please refresh the page.";
  }
};

const sendWhatsappCode = async () => {
  whatsappLoading.value = true;
  whatsappMessage.value = "";
  try {
    const res = await fetch(
      `/api/verification/user/${currentUser.value.id}/whatsapp`,
      {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ whatsapp_number: whatsappNumber.value }),
      }
    );
    const data = await res.json();
    if (data.success) {
      whatsappStatus.value = "sent";
      whatsappMessage.value = "Verification code sent. Please check WhatsApp.";
      showWhatsappModal.value = true;
    } else {
      whatsappStatus.value = "error";
      whatsappMessage.value = data.message || "Failed to send WhatsApp code.";
    }
  } catch (e) {
    whatsappStatus.value = "error";
    whatsappMessage.value = "Failed to send WhatsApp code.";
  } finally {
    whatsappLoading.value = false;
  }
};

const connectLinkedin = async () => {
  try {
    linkedinLoading.value = true;
    linkedinMessage.value = "Connecting to LinkedIn...";

    // 1) If an explicit frontend auth URL is provided, use it directly
    const configuredUrl = import.meta.env.VITE_LINKEDIN_AUTH_URL;
    if (configuredUrl && typeof configuredUrl === "string") {
      window.location.href = configuredUrl;
      return;
    }

    // 2) Build the correct endpoint URL
    let endpoint;
    const apiBase = import.meta.env.VITE_API_BASE_URL;

    if (apiBase && apiBase.includes("/api")) {
      // If VITE_API_BASE_URL already contains /api, use it directly
      endpoint = `${apiBase}/verification/user/${currentUser.value.id}/linkedin`;
    } else if (apiBase) {
      // If VITE_API_BASE_URL doesn't contain /api, add it
      endpoint = `${apiBase}/api/verification/user/${currentUser.value.id}/linkedin`;
    } else {
      // Fallback: use relative URL
      endpoint = `/api/verification/user/${currentUser.value.id}/linkedin`;
    }

    console.log("LinkedIn endpoint:", endpoint);

    const res = await fetch(endpoint, {
      method: "GET",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
    });

    if (!res.ok) {
      throw new Error(`HTTP error! status: ${res.status}`);
    }

    const data = await res.json();

    if (data.success && data.authorization_url) {
      linkedinMessage.value = "Redirecting to LinkedIn...";
      // Redirect to LinkedIn OAuth
      window.location.href = data.authorization_url;
      return;
    } else {
      throw new Error(
        data.message || "Failed to get LinkedIn authorization URL"
      );
    }
  } catch (e) {
    console.error("LinkedIn connection error:", e);
    linkedinStatus.value = "error";
    linkedinMessage.value = "Failed to connect to LinkedIn. Please try again.";
    linkedinLoading.value = false;
  }
};

const verifyWhatsappCode = async () => {
  whatsappCodeLoading.value = true;
  whatsappMessage.value = "";
  try {
    const res = await fetch(
      `/api/verification/user/${currentUser.value.id}/whatsapp/verify`,
      {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ code: whatsappCode.value }),
      }
    );
    const data = await res.json();
    if (data.success) {
      whatsappStatus.value = "verified";
      whatsappMessage.value = "WhatsApp Verified";
      showWhatsappModal.value = false;
      calculateProgress(); // Calculate progress after successful verification
    } else {
      whatsappStatus.value = "error";
      whatsappMessage.value = data.message || "Invalid code.";
    }
  } catch (e) {
    whatsappStatus.value = "error";
    whatsappMessage.value = "Failed to verify code.";
  } finally {
    whatsappCodeLoading.value = false;
  }
};

const completeProfile = async () => {
  try {
    const res = await fetch(
      `/api/verification/user/${currentUser.value.id}/profile`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN":
            document
              .querySelector('meta[name="csrf-token"]')
              ?.getAttribute("content") || "",
        },
        body: JSON.stringify({
          completed_verifications: completedVerifications.value,
          total_verifications: totalVerifications.value,
          progress_percentage: progressPercentage.value,
        }),
      }
    );

    const data = await res.json();
    if (data.success) {
      profileCompleted.value = true;
      reviewStatus.value = "verified_contact";

      const redirectUrl = data.redirect_url || "/access-control";
      // Direct, silent redirect (no alert, no delay)
      window.location.href = redirectUrl;
      return;
    }
    // If backend responds with failure, still navigate user to access-control silently
    window.location.href = "/access-control";
  } catch (e) {
    // On error, proceed to access-control silently (no alert)
    window.location.href = "/access-control";
  }
};

// Calculate progress and update step 4 state
const calculateProgress = () => {
  let completed = 0;

  if (emailStatus.value === "verified") completed++;
  if (whatsappStatus.value === "verified") completed++;
  if (linkedinStatus.value === "verified") completed++;

  completedVerifications.value = completed;
  progressPercentage.value = Math.round(
    (completed / totalVerifications.value) * 100
  );

  // Enable step 4 if at least one verification is completed
  step4Enabled.value = completed > 0;
  // Also update simple verified flag
  updateVerifiedFlag();

  // Debug logging for progress calculation
  console.log("Progress calculation:", {
    emailStatus: emailStatus.value,
    whatsappStatus: whatsappStatus.value,
    linkedinStatus: linkedinStatus.value,
    completed: completed,
    total: totalVerifications.value,
    percentage: progressPercentage.value,
    step4Enabled: step4Enabled.value,
  });
};

onMounted(async () => {
  try {
    // Add keyboard listeners for development
    addKeyboardListeners();

    // Initialize step4Enabled to false first
    step4Enabled.value = false;
    progressPercentage.value = 0;
    completedVerifications.value = 0;

    // Get current user data from logged-in user
    currentUser.value = getCurrentUser();
    if (!currentUser.value) {
      console.error("No user data found - redirecting to login");
      window.location.href = "/login";
      return;
    }

    // Set user data from current user
    userData.value = {
      email: currentUser.value.email || "",
      whatsapp: currentUser.value.whatsapp || "",
      full_name: currentUser.value.full_name || currentUser.value.name || "",
      company_name: currentUser.value.company_name || "",
    };

    // Initialize timeline steps
    initializeTimeline();

    // Check if user just came back from email verification
    const isEmailVerified = route.query.verified === "true";

    if (isEmailVerified) {
      console.log(
        "User returned from email verification - starting enhanced verification check"
      );

      // Use enhanced verification fetch with retry logic
      await fetchVerificationStatusWithRetry(true);

      // If email is still not verified, start polling
      if (emailStatus.value !== "verified") {
        console.log("Email not yet verified, starting polling...");
        startVerificationPolling();
      }
    } else {
      // Regular verification status fetch
      await fetchVerificationStatus();
    }

    // Check LinkedIn verification status from backend or query
    if (route.query.linkedin === "success") {
      linkedinStatus.value = "verified";
      linkedinMessage.value = "LinkedIn verified successfully!";
      linkedinLoading.value = false;
    } else if (route.query.linkedin === "error") {
      linkedinStatus.value = "error";
      const errorMessage = route.query.message;
      switch (errorMessage) {
        case "oauth_denied":
          linkedinMessage.value =
            "LinkedIn access was denied. Please try again.";
          break;
        case "session_expired":
          linkedinMessage.value =
            "Session expired. Please try connecting again.";
          break;
        case "user_not_found":
          linkedinMessage.value = "User not found. Please contact support.";
          break;
        case "callback_error":
          linkedinMessage.value =
            "LinkedIn verification failed. Please try again.";
          break;
        default:
          linkedinMessage.value =
            "LinkedIn verification failed. Please try again.";
      }
      linkedinLoading.value = false;
    }

    // Auto-send email verification if not already verified and user has email
    if (emailStatus.value === "pending" && currentUser.value?.email) {
      console.log("Auto-sending email verification...");
      await sendEmailVerification();
    }

    // Calculate initial progress - this should now properly enable step 4
    calculateProgress();

    // Debug logging
    console.log("Verification statuses:", {
      email: emailStatus.value,
      whatsapp: whatsappStatus.value,
      linkedin: linkedinStatus.value,
      step4Enabled: step4Enabled.value,
      completedVerifications: completedVerifications.value,
      progressPercentage: progressPercentage.value,
      isPolling: isPolling.value,
    });
  } catch (err) {
    console.error("Error fetching user data:", err);
    error.value = err.message || "Failed to load user data";
  } finally {
    loading.value = false;
  }
});

// Keyboard shortcuts for development
const handleKeydown = (event) => {
  // Ctrl+Shift+D to toggle debug panel
  if (event.ctrlKey && event.shiftKey && event.key === "D") {
    event.preventDefault();
    showDebugPanel.value = !showDebugPanel.value;
    console.log("Debug panel toggled:", showDebugPanel.value);
  }
};

// Add keyboard event listener
const addKeyboardListeners = () => {
  document.addEventListener("keydown", handleKeydown);
};

const removeKeyboardListeners = () => {
  document.removeEventListener("keydown", handleKeydown);
};

// Cleanup polling on component unmount
onUnmounted(() => {
  stopVerificationPolling();
  removeKeyboardListeners();
});

// Watch for route changes to handle email verification returns
watch(
  () => route.query.verified,
  async (newValue) => {
    if (newValue === "true") {
      console.log("Route query changed - email verification detected");

      // Stop any existing polling
      stopVerificationPolling();

      // Immediately check verification status
      await fetchVerificationStatusWithRetry(true);

      // If still not verified, start polling
      if (emailStatus.value !== "verified") {
        console.log(
          "Email not yet verified after route change, starting polling..."
        );
        startVerificationPolling();
      }
    }
  }
);

const initializeTimeline = () => {
  timelineSteps.value = [
    {
      id: 1,
      title: "Registration Completed",
      subtitle: "Your registration has been successfully submitted",
      description: `Welcome ${
        userData.value?.full_name || userData.value?.company_name
      }! Your registration has been completed and is now being processed.`,
      icon: "tabler-user-check",
      color: "success",
      completed: true,
      time: "Just now",
    },
    {
      id: 2,
      title: "Email Verification",
      subtitle: "Verify your email address",
      description:
        "Please check your email and click the verification link to confirm your account. This step is required to proceed with the next steps.",
      icon: "tabler-mail",
      color: "info",
      completed: false,
      time: "Pending",
      action: "Resend Email",
    },
    {
      id: 3,
      title: "LinkedIn Connection",
      subtitle: "Connect your LinkedIn profile",
      description:
        "Connect your LinkedIn profile to enhance your professional network and increase your visibility.",
      icon: "tabler-brand-linkedin",
      color: "primary",
      completed: false,
      time: "Pending",
      action: "Connect LinkedIn",
    },
    {
      id: 4,
      title: "WhatsApp Verification",
      subtitle: "Verify your WhatsApp number",
      description:
        "Verify your WhatsApp number to receive important updates and notifications about your account.",
      icon: "tabler-brand-whatsapp",
      color: "success",
      completed: false,
      time: "Pending",
      action: "Verify WhatsApp",
    },
    {
      id: 5,
      title: "Account Activation",
      subtitle: "Your account will be activated",
      description:
        "Once all verification steps are completed, your account will be activated and you can start using all features.",
      icon: "tabler-check-circle",
      color: "warning",
      completed: false,
      time: "Pending",
    },
  ];
};

const handleAction = (step) => {
  // Handle different actions based on step
  switch (step.id) {
    case 2:
      // Resend email verification
      sendEmailVerification();
      break;
    case 3:
      // LinkedIn connection
      connectLinkedin();
      break;
    case 4:
      // WhatsApp verification
      alert("WhatsApp verification initiated!");
      break;
  }
};

const getUserDisplayName = () => {
  if (!userData.value) return "User";
  return (
    userData.value.full_name ||
    userData.value.company_name ||
    currentUser.value?.name ||
    "User"
  );
};

// Remove the old getLoggedInUser function since we're using currentUser now

const sendEmailVerification = async () => {
  emailLoading.value = true;
  emailMessage.value = "";

  // Use current user's email
  const userEmail = currentUser.value?.email || userData.value.email;
  if (!userEmail) {
    emailStatus.value = "error";
    emailMessage.value = "Email address not found. Please contact support.";
    emailLoading.value = false;
    return;
  }

  try {
    const res = await fetch(
      `/api/verification/user/${currentUser.value.id}/email`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN":
            document
              .querySelector('meta[name="csrf-token"]')
              ?.getAttribute("content") || "",
        },
        body: JSON.stringify({
          email: userEmail,
          name: getUserDisplayName(),
          redirect_url: `${window.location.origin}/timeline?verified=true`,
        }),
      }
    );

    const data = await res.json();
    if (data.success) {
      emailStatus.value = "sent";
      emailMessage.value =
        "Verification email sent successfully! Please check your inbox and click the verification link.";

      // Start polling for verification status updates
      console.log("Email sent successfully, starting verification polling...");
      startVerificationPolling();

      await fetchVerificationStatus(); // Update status
    } else {
      emailStatus.value = "error";
      emailMessage.value =
        data.message || "Failed to send verification email. Please try again.";
    }
  } catch (e) {
    console.error("Email verification error:", e);
    emailStatus.value = "error";
    emailMessage.value =
      "Network error. Please check your connection and try again.";
  } finally {
    emailLoading.value = false;
  }
};
</script>

<template>
  <!-- Success Notification -->
  <VSnackbar
    v-model="showSuccessNotification"
    :timeout="5000"
    color="success"
    location="top"
    class="success-notification"
  >
    <div style="display: flex; align-items: center; gap: 8px">
      <VIcon icon="tabler-check-circle" size="20" />
      {{ successMessage }}
    </div>
  </VSnackbar>

  <!-- Header Section - Outside Timeline Context -->
  <div class="timeline-header">
    <div class="container-header">
      <h1 class="welcome-title">
        Welcome <span class="account-name">{{ getUserDisplayName() }}</span>
      </h1>
      <p class="welcome-desc">
        here is where you can manage all your experience listings and
        participants, add bookings, connect with businesses and people in
        adventure industry. to access all the features in this platform, there
        are a few steps to complete. It will only take about 15 minutes to
        finish! You can watch the intro video and in case you need help, we are
        one message away!
      </p>
      <div class="header-btns">
        <VBtn variant="outlined" color="black" class="intro-btn">
          <VIcon left size="20">tabler-player-play</VIcon>
          Watch Intro Video
        </VBtn>
        <VBtn color="orange" class="support-btn"> Get Support </VBtn>
      </div>
    </div>
  </div>

  <!-- Timeline Section Title -->
  <h2 class="section-title">Complete Following Steps to Verify Your Account</h2>

  <!-- Debug Panel (Development Only) -->
  <div v-if="showDebugPanel" class="debug-panel">
    <h3>Debug Information</h3>
    <div class="debug-info">
      <p><strong>Email Status:</strong> {{ emailStatus }}</p>
      <p><strong>WhatsApp Status:</strong> {{ whatsappStatus }}</p>
      <p><strong>LinkedIn Status:</strong> {{ linkedinStatus }}</p>
      <p><strong>Is Polling:</strong> {{ isPolling }}</p>
      <p>
        <strong>Polling Attempts:</strong> {{ pollingAttempts }}/{{
          maxPollingAttempts
        }}
      </p>
      <p><strong>Step 4 Enabled:</strong> {{ step4Enabled }}</p>
      <p>
        <strong>Progress:</strong> {{ progressPercentage }}% ({{
          completedVerifications
        }}/{{ totalVerifications }})
      </p>
      <p><strong>Route Query:</strong> {{ JSON.stringify(route.query) }}</p>
    </div>
    <VBtn @click="refreshVerificationStatus" size="small" color="primary">
      Refresh Status
    </VBtn>
  </div>

  <!-- Timeline Container -->
  <VCard class="registration-timeline-page" elevation="0">
    <!-- Timeline Steps -->
    <div class="timeline">
      <!-- Step 1: Email Verification -->
      <div class="timeline-row reverse">
        <div class="timeline-card">
          <div class="card-title">
            Confirm your email address to start the registration process
          </div>
          <div class="card-desc">
            Enter your email to receive a verification link. Click the link to
            confirm your Email.
          </div>
          <div class="card-actions">
            <VTextField
              v-model="userData.email"
              label="Email"
              placeholder="Sam@email.com"
              variant="outlined"
              density="compact"
              class="email-input"
              :disabled="true"
              :readonly="true"
              :hint="
                currentUser?.email
                  ? 'Your login email'
                  : 'Email will be auto-filled'
              "
              persistent-hint
            />
            <VBtn
              color="orange"
              class="send-link-btn"
              @click="sendEmailVerification"
              :loading="emailLoading"
              :disabled="emailLoading || emailStatus === 'verified'"
            >
              <VIcon left size="20">tabler-mail</VIcon>
              {{ emailStatus === "verified" ? "Verified" : "Send Link" }}
            </VBtn>
            <!-- Refresh button for manual status check -->
            <VBtn
              v-if="emailStatus === 'sent'"
              variant="outlined"
              color="primary"
              size="small"
              @click="refreshVerificationStatus"
              :loading="isPolling"
              style="margin-left: 8px"
            >
              <VIcon left size="16">tabler-refresh</VIcon>
              Check Status
            </VBtn>
            <div
              v-if="emailMessage"
              :style="{
                color:
                  emailStatus === 'error'
                    ? 'red'
                    : emailStatus === 'verified'
                    ? 'green'
                    : 'blue',
                marginTop: '8px',
                fontWeight: 'bold',
              }"
            >
              {{ emailMessage }}
            </div>
            <!-- Polling indicator -->
            <div
              v-if="isPolling && emailStatus === 'sent'"
              :style="{
                color: 'orange',
                marginTop: '8px',
                fontSize: '14px',
                display: 'flex',
                alignItems: 'center',
                gap: '8px',
              }"
            >
              <VIcon
                icon="tabler-refresh"
                size="16"
                style="animation: spin 2s linear infinite"
              />
              Checking for verification updates...
            </div>
          </div>
        </div>
        <div class="timeline-left step-left-reverse-fixed">
          <div class="step-line-col">
            <div class="step-checkbox" :class="{ active: true, done: false }">
              <div class="checkbox-circle">
                <VIcon
                  v-if="true"
                  icon="tabler-check"
                  size="16"
                  color="white"
                />
              </div>
            </div>
            <div class="vertical-line"></div>
          </div>
          <div class="step-info-col">
            <div class="step-number" :class="{ active: true }">01</div>
            <div class="step-title">Email<br />Verification</div>
          </div>
        </div>
      </div>

      <!-- Step 2: WhatsApp Verification -->
      <div class="timeline-row reverse-alt">
        <div class="timeline-card">
          <div class="card-title">
            Verify your WhatsApp number for secure communication
          </div>
          <div class="card-desc">
            Provide your WhatsApp number to receive a verification code. Enter
            the code to confirm your number.
          </div>
          <div class="card-actions">
            <VTextField
              v-model="userData.whatsapp"
              label="Your WhatsApp Number"
              placeholder="09xxxxxxxxx"
              variant="outlined"
              density="compact"
              class="whatsapp-input"
            />
            <VBtn
              color="success"
              class="send-code-btn"
              @click="sendWhatsappCode"
            >
              <VIcon left size="20">tabler-brand-whatsapp</VIcon>
              Send Code
            </VBtn>
          </div>
        </div>
        <div class="timeline-left step-left-reverse-alt">
          <div class="step-line-col-alt">
            <div class="step-checkbox" :class="{ active: false, done: false }">
              <div class="checkbox-circle">
                <VIcon
                  v-if="false"
                  icon="tabler-check"
                  size="16"
                  color="white"
                />
              </div>
            </div>
            <div class="vertical-line"></div>
          </div>
          <div class="step-info-col-alt">
            <div class="step-number" :class="{ active: false }">02</div>
            <div class="step-title">WhatsApp<br />Verification</div>
          </div>
        </div>
      </div>

      <!-- Step 3: LinkedIn Connection -->
      <div class="timeline-row reverse">
        <div class="timeline-card">
          <div class="card-title">
            Connect your LinkedIn profile to validate your professional identity
          </div>
          <div class="card-desc">
            Log in to LinkedIn to link your profile. This ensures your
            professional credentials are verified.
          </div>
          <div class="card-actions">
            <VBtn
              color="primary"
              :disabled="linkedinStatus === 'verified' || linkedinLoading"
              @click="connectLinkedin"
              :loading="linkedinLoading"
            >
              <VIcon left size="20">tabler-brand-linkedin</VIcon>
              {{
                linkedinStatus === "verified"
                  ? "Verified"
                  : linkedinLoading
                  ? "Connecting..."
                  : "Connect to LinkedIn"
              }}
            </VBtn>
            <div
              v-if="linkedinMessage"
              :style="{
                color:
                  linkedinStatus === 'verified'
                    ? 'green'
                    : linkedinStatus === 'error'
                    ? 'red'
                    : linkedinLoading
                    ? 'blue'
                    : 'gray',
                marginTop: '8px',
                fontWeight: 'bold',
              }"
            >
              {{ linkedinMessage }}
            </div>
          </div>
        </div>
        <div class="timeline-left step-left-reverse-fixed">
          <div class="step-line-col">
            <div
              class="step-checkbox"
              :class="{
                active: linkedinStatus === 'verified',
                done: linkedinStatus === 'verified',
              }"
            >
              <div class="checkbox-circle">
                <VIcon
                  v-if="linkedinStatus === 'verified'"
                  icon="tabler-check"
                  size="16"
                  color="white"
                />
              </div>
            </div>
            <div class="vertical-line"></div>
          </div>
          <div class="step-info-col">
            <div
              class="step-number"
              :class="{ active: linkedinStatus === 'verified' }"
            >
              03
            </div>
            <div class="step-title">LinkedIn<br />Connection</div>
          </div>
        </div>
      </div>

      <!-- Step 4: Profile Details -->
      <div class="timeline-row reverse-alt">
        <div class="timeline-card" :class="{ 'disabled-card': !step4Enabled }">
          <div class="card-title">
            Fill out your profile details to personalize your account
          </div>
          <div class="card-desc">
            Provide your personal and professional information. Complete all
            required fields to finalize your profile.
          </div>
          <div class="card-actions">
            <VBtn
              color="purple"
              variant="outlined"
              class="complete-profile-btn"
              :disabled="!step4Enabled"
              @click="completeProfile"
            >
              <VIcon left size="20">tabler-file-text</VIcon>
              Complete Personal Form
            </VBtn>
            <div class="progress-bar-container">
              <div
                class="progress-bar"
                :style="{ width: progressPercentage + '%' }"
              ></div>
              <span>{{ progressPercentage }}%</span>
            </div>
            <div v-if="!step4Enabled" class="step4-disabled-message">
              Complete at least one verification step above to proceed
            </div>
          </div>
        </div>
        <div class="timeline-left step-left-reverse-alt">
          <div class="step-line-col-alt">
            <div class="step-checkbox" :class="{ active: step4Enabled }">
              <div class="checkbox-circle">
                <VIcon
                  v-if="step4Enabled"
                  icon="tabler-check"
                  size="16"
                  color="white"
                />
              </div>
            </div>
            <div class="vertical-line"></div>
          </div>
          <div class="step-info-col-alt">
            <div class="step-number" :class="{ active: step4Enabled }">04</div>
            <div class="step-title">Add your<br />profile details</div>
          </div>
        </div>
      </div>

      <!-- Step 5: Review & Activation -->
      <div class="timeline-row reverse">
        <div class="timeline-card">
          <div class="card-title">
            Your account is under review and will be activated soon
          </div>
          <div class="card-desc">
            Our admins will review your submission. You'll be notified once your
            account is activated.
          </div>
          <div class="current-status">
            <h5>Current Status</h5>
            <div class="status-badge status-awaiting-approval">
              <VIcon size="18" color="#28a745">tabler-check-circle</VIcon>
              Awaiting Approval
            </div>
          </div>
        </div>
        <div class="timeline-left step-left-reverse-fixed">
          <div class="step-line-col">
            <div
              class="step-checkbox"
              :class="{ active: profileCompleted, done: false }"
            >
              <div class="checkbox-circle">
                <VIcon
                  v-if="profileCompleted"
                  icon="tabler-check"
                  size="16"
                  color="white"
                />
              </div>
            </div>
            <div class="vertical-line"></div>
          </div>
          <div class="step-info-col">
            <div class="step-number" :class="{ active: profileCompleted }">
              05
            </div>
            <div class="step-title">Review &<br />Activation</div>
          </div>
        </div>
      </div>
    </div>
  </VCard>
</template>

<style scoped>
.timeline-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.registration-timeline-page {
  background: #f8f8fb;
  padding: 0;
  margin: 20px;
  border-radius: 12px;
  overflow: hidden;
}
.timeline-header {
  padding-top: 48px;
  padding-bottom: 24px;
  background: #f8f8fb;
  margin-bottom: 0;
}
.container-header {
  max-width: 700px;
  margin: 0 auto;
  text-align: left;
  padding: 0 24px;
  direction: ltr;
}
.welcome-title {
  font-family: "Anton", Arial, sans-serif;
  font-size: 2.5rem;
  font-weight: 700;
  color: #222;
  margin-bottom: 12px;
  direction: ltr;
  text-align: left;
}
.account-name {
  color: #ffa726;
}
.welcome-desc {
  font-family: "Karla", Arial, sans-serif;
  font-size: 1.1rem;
  color: #444;
  margin-bottom: 28px;
  line-height: 1.7;
  direction: ltr;
  text-align: left;
}
.header-btns {
  display: flex;
  gap: 16px;
}
.intro-btn {
  font-weight: 600;
  border-radius: 8px;
  border: 2px solid #222;
  background: #fff;
  color: #222;
}
.support-btn {
  font-weight: 600;
  border-radius: 8px;
  background: #ffa726;
  color: #fff;
}

.section-title {
  text-align: center;
  font-size: 2.2em;
  color: #333;
  margin: 40px 0 40px 0;
  position: relative;
  padding: 0 24px;
  font-weight: 700;
  font-family: "Anton", Arial, sans-serif;
}

/* --- Timeline --- */
.timeline {
  padding: 20px 24px 40px 24px;
  position: relative;
  max-width: 1200px;
  margin: 0 auto;
}

.timeline-row {
  display: flex;
  align-items: flex-start;
  margin-bottom: 60px;
  position: relative;
  z-index: 2;
}

/* Step 1, 3, 5: Right side cards */
.timeline-row.reverse {
  justify-content: flex-end;
}

.timeline-row.reverse .timeline-card {
  margin-right: 40px;
  margin-left: 0;
  flex: 1;
  max-width: 500px;
}

.timeline-row.reverse .timeline-left {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100px;
}

.timeline-row.reverse .step-info-col {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 12px;
  margin-right: 40px;
  position: absolute;
  left: -50px;
  top: 0;
}

.timeline-row.reverse .step-line-col {
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
}

.timeline-row.reverse .step-checkbox {
  position: absolute;
  top: 8px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 3;
}

.timeline-row.reverse .vertical-line {
  width: 2px;
  height: 140px;
  background: #ff8c00;
  border-radius: 1px;
  margin-top: 40px;
}

/* Step 2, 4: Left side cards */
.timeline-row.reverse-alt {
  justify-content: flex-start;
}

.timeline-row.reverse-alt .timeline-card {
  margin-left: 40px;
  margin-right: 0;
  flex: 1;
  max-width: 500px;
}

.timeline-row.reverse-alt .timeline-left {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100px;
}

.timeline-row.reverse-alt .step-info-col-alt {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 12px;
  margin-left: 40px;
  position: absolute;
  right: -50px;
  top: 0;
}

.timeline-row.reverse-alt .step-line-col-alt {
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
}

.timeline-row.reverse-alt .step-checkbox {
  position: absolute;
  top: 8px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 3;
}

.timeline-row.reverse-alt .vertical-line {
  width: 2px;
  height: 140px;
  background: #ff8c00;
  border-radius: 1px;
  margin-top: 40px;
}

/* --- Step Checkbox --- */
.step-checkbox {
  margin-bottom: 0;
}

.checkbox-circle {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid #e0e0e0;
  background-color: #fff;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.step-checkbox.active .checkbox-circle {
  background-color: #ff8c00;
  border-color: #ff8c00;
}

.step-checkbox.done .checkbox-circle {
  background-color: #28a745;
  border-color: #28a745;
}

/* --- Step Number --- */
.step-number {
  font-size: 2.2em;
  font-weight: bold;
  color: #666;
  line-height: 1;
  font-family: "Anton", Arial, sans-serif;
  margin-bottom: 2px;
  transition: color 0.3s ease;
}

.step-number.active {
  color: #000;
}

/* --- Step Title --- */
.step-title {
  font-size: 1em;
  color: #444;
  text-align: center;
  font-weight: 500;
  margin-bottom: 0;
  line-height: 1.2;
}

/* Step 1, 3, 5: Right to left text */
.timeline-row.reverse .step-title {
  direction: rtl;
  text-align: right;
}

/* Step 2, 4: Left to right text */
.timeline-row.reverse-alt .step-title {
  direction: ltr;
  text-align: left;
}

/* --- Timeline Card --- */
.timeline-card {
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  padding: 28px 28px 20px 28px;
  min-width: 360px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  transition: all 0.3s ease;
}

.timeline-card.disabled-card {
  background: #f8f8f8;
  opacity: 0.7;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.step4-disabled-message {
  color: #999;
  font-size: 0.9em;
  margin-top: 12px;
  text-align: center;
  font-style: italic;
}

.card-title {
  font-size: 1.1em;
  font-weight: bold;
  color: #444;
  margin-bottom: 8px;
}

.card-desc {
  font-size: 0.95em;
  color: #666;
  margin-bottom: 16px;
}

.card-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

/* --- Input Fields --- */
.email-input,
.whatsapp-input {
  min-width: 220px;
  max-width: 260px;
}

/* --- Buttons --- */
.send-link-btn {
  background: #ff8c00 !important;
  color: #fff !important;
  font-weight: bold;
  box-shadow: 0 2px 8px rgba(255, 140, 0, 0.08);
  border-radius: 6px;
  padding: 0 22px;
  height: 40px;
}

.send-code-btn {
  background: #25d366 !important;
  color: #fff !important;
  font-weight: bold;
  box-shadow: 0 2px 8px rgba(37, 211, 102, 0.08);
  border-radius: 6px;
  padding: 0 22px;
  height: 40px;
}

.connect-linkedin-btn {
  background: #0077b5 !important;
  color: #fff !important;
  font-weight: bold;
  box-shadow: 0 2px 8px rgba(0, 119, 181, 0.08);
  border-radius: 6px;
  padding: 0 22px;
  height: 40px;
}

.complete-profile-btn {
  background: #f0f0ff !important;
  color: #6a0dad !important;
  border: 1px solid #dcdcfe !important;
  font-weight: bold;
  border-radius: 6px;
  padding: 0 22px;
  height: 40px;
}

/* --- Progress Bar --- */
.progress-bar-container {
  width: 100%;
  background-color: #e0e0e0;
  border-radius: 5px;
  height: 10px;
  margin-top: 20px;
  position: relative;
}

.progress-bar {
  height: 100%;
  background-color: #ff8c00;
  border-radius: 5px;
  width: 0%;
  transition: width 0.5s ease-in-out;
}

.progress-bar-container span {
  position: absolute;
  right: 0;
  top: -25px;
  font-size: 0.9em;
  color: #777;
}

/* --- Current Status --- */
.current-status {
  margin-top: 25px;
  padding-top: 20px;
  border-top: 1px solid #eee;
}

.current-status h5 {
  font-size: 1em;
  color: #444;
  margin-bottom: 10px;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 8px 15px;
  border-radius: 20px;
  font-weight: bold;
  font-size: 0.9em;
  gap: 8px;
}

.status-awaiting-approval {
  background-color: #e6ffe6;
  color: #28a745;
}

/* --- Responsive --- */
@media (max-width: 900px) {
  .timeline::before {
    left: 30px;
  }

  .timeline-row {
    flex-direction: column;
    align-items: stretch;
    margin-left: 60px;
  }

  .timeline-row.reverse,
  .timeline-row.reverse-alt {
    justify-content: flex-start;
  }

  .timeline-row.reverse .timeline-left,
  .timeline-row.reverse-alt .timeline-left {
    position: relative;
    left: -60px;
    transform: none;
    flex-direction: row;
    width: auto;
    margin-bottom: 20px;
  }

  .timeline-row.reverse .step-info-col,
  .timeline-row.reverse-alt .step-info-col-alt {
    flex-direction: row;
    align-items: center;
    margin-bottom: 0;
    margin-right: 20px;
  }

  .timeline-row.reverse .step-line-col,
  .timeline-row.reverse-alt .step-line-col-alt {
    flex-direction: row;
    align-items: center;
  }

  .timeline-row.reverse .step-checkbox,
  .timeline-row.reverse-alt .step-checkbox {
    position: relative;
    top: 0;
    left: 0;
    transform: none;
    margin-right: 20px;
  }

  .timeline-row.reverse .vertical-line,
  .timeline-row.reverse-alt .vertical-line {
    width: 80px;
    height: 3px;
    margin-top: 0;
  }

  .timeline-card {
    margin-left: 0 !important;
    margin-right: 0 !important;
    min-width: unset;
    max-width: unset;
    width: 100%;
  }

  .card-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .email-input,
  .whatsapp-input {
    min-width: unset;
    max-width: unset;
  }
}

/* Spinning animation for polling indicator */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* Success notification styling */
.success-notification {
  z-index: 9999;
}

.success-notification .v-snackbar__content {
  font-weight: 600;
  font-size: 16px;
}

/* Debug panel styling */
.debug-panel {
  background: #f5f5f5;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 20px;
  font-family: monospace;
  font-size: 14px;
}

.debug-panel h3 {
  margin-top: 0;
  margin-bottom: 12px;
  color: #333;
}

.debug-info p {
  margin: 4px 0;
  color: #666;
}

.debug-info strong {
  color: #333;
}
</style>
