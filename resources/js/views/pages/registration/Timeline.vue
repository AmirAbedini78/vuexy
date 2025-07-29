<script setup>
import { companyUserService, individualUserService } from "@/services/api";
import { onMounted, ref } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();
const loading = ref(false);
const error = ref(null);
const userData = ref({ email: "", whatsapp: "" });
const timelineSteps = ref([]);

// Get logged-in user data
const loggedInUser = ref(null);

// Get user type and ID from route
const userType = route.params.type; // 'individual' or 'company'
const userId = route.params.id;

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

// Fetch verification status on mount
const fetchVerificationStatus = async () => {
  try {
    const res = await fetch(`/api/verification/${userType}/${userId}`);
    const data = await res.json();

    if (data.success && data.data) {
      // Check email verification status
      if (data.data.email_verified) {
        emailStatus.value = "verified";
        emailMessage.value = "Email verified successfully!";
      } else if (data.data.email_token || data.data.email_sent) {
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
      } else {
        linkedinStatus.value = "pending";
        linkedinMessage.value = "";
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

const sendWhatsappCode = async () => {
  whatsappLoading.value = true;
  whatsappMessage.value = "";
  try {
    const res = await fetch(
      `/api/verification/${userType}/${userId}/whatsapp`,
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

const connectLinkedin = () => {
  window.location.href = `/api/verification/${userType}/${userId}/linkedin`;
};

const verifyWhatsappCode = async () => {
  whatsappCodeLoading.value = true;
  whatsappMessage.value = "";
  try {
    const res = await fetch(
      `/api/verification/${userType}/${userId}/whatsapp/verify`,
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
    const res = await fetch(`/api/verification/${userType}/${userId}/profile`, {
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
    });

    const data = await res.json();
    if (data.success) {
      profileCompleted.value = true;
      reviewStatus.value = "verified_contact";
      // Update step 5 checkbox to show active state
    } else {
      console.error("Failed to complete profile:", data.message);
    }
  } catch (e) {
    console.error("Error completing profile:", e);
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
};

onMounted(async () => {
  try {
    // Initialize step4Enabled to false first
    step4Enabled.value = false;
    progressPercentage.value = 0;
    completedVerifications.value = 0;

    // Fetch user data based on type
    if (userType === "individual") {
      const response = await individualUserService.getById(userId);
      userData.value = response.data;
    } else if (userType === "company") {
      const response = await companyUserService.getById(userId);
      userData.value = response.data;
    } else {
      throw new Error("Invalid user type");
    }

    // Initialize timeline steps
    initializeTimeline();
    getLoggedInUser(); // Call the new function here

    // Fetch all verification statuses
    await fetchVerificationStatus();

    // Check if user just came back from email verification
    if (route.query.verified === "true") {
      emailStatus.value = "verified";
      emailMessage.value = "Email verified successfully! Welcome back!";
      // Fetch latest verification status from server
      await fetchVerificationStatus();
    }

    // Check LinkedIn verification status from backend or query
    if (route.query.linkedin === "success") {
      linkedinStatus.value = "verified";
      linkedinMessage.value = "LinkedIn verified successfully!";
    } else if (route.query.linkedin === "error") {
      linkedinStatus.value = "error";
      linkedinMessage.value = "LinkedIn verification failed. Please try again.";
    }

    // Calculate initial progress
    calculateProgress();
  } catch (err) {
    console.error("Error fetching user data:", err);
    error.value = err.message || "Failed to load user data";
  } finally {
    loading.value = false;
  }
});

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
      alert("Email verification link has been resent!");
      break;
    case 3:
      // LinkedIn connection
      window.open("https://linkedin.com", "_blank");
      break;
    case 4:
      // WhatsApp verification
      alert("WhatsApp verification initiated!");
      break;
  }
};

const getUserDisplayName = () => {
  if (!userData.value) return "User";
  return userData.value.full_name || userData.value.company_name || "User";
};

// Get logged-in user data from cookies
const getLoggedInUser = () => {
  const userDataCookie = useCookie("userData");
  if (userDataCookie.value) {
    loggedInUser.value = userDataCookie.value;
    // Auto-fill email field with logged-in user's email
    if (loggedInUser.value.email) {
      userData.value.email = loggedInUser.value.email;
    }
  }
};

const sendEmailVerification = async () => {
  emailLoading.value = true;
  emailMessage.value = "";

  // Use logged-in user's email if available, otherwise use userData email
  const userEmail = loggedInUser.value?.email || userData.value.email;
  if (!userEmail) {
    emailStatus.value = "error";
    emailMessage.value = "Email address not found. Please contact support.";
    emailLoading.value = false;
    return;
  }

  try {
    const res = await fetch(`/api/verification/${userType}/${userId}/email`, {
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
        redirect_url: `${window.location.origin}/registration/timeline/${userType}/${userId}?verified=true`,
      }),
    });

    const data = await res.json();
    if (data.success) {
      emailStatus.value = "sent";
      emailMessage.value =
        "Verification email sent successfully! Please check your inbox and click the verification link.";
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
              :disabled="linkedinStatus === 'verified'"
              @click="connectLinkedin"
            >
              <VIcon left size="20">tabler-brand-linkedin</VIcon>
              {{
                linkedinStatus === "verified"
                  ? "Verified"
                  : "Connect to LinkedIn"
              }}
            </VBtn>
            <div
              v-if="linkedinMessage"
              :style="{
                color: linkedinStatus === 'verified' ? 'green' : 'red',
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
          <div class="step-info-col">
            <div class="step-number" :class="{ active: false }">03</div>
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
</style>
