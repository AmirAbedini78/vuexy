<script setup>
import { companyUserService, individualUserService } from "@/services/api";
import { onMounted, ref } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();
const loading = ref(false);
const error = ref(null);
const userData = ref({ email: "", whatsapp: "" });
const timelineSteps = ref([]);

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
const linkedinStatus = ref("pending"); // 'pending', 'sent', 'verified', 'error'
const linkedinMessage = ref("");
const linkedinLoading = ref(false);
const showLinkedinModal = ref(false);
const linkedinCode = ref("");
const linkedinCodeLoading = ref(false);

// Profile Completion State
const profileCompleted = ref(false);
const reviewStatus = ref("awaiting_approval"); // 'awaiting_approval', 'verified_contact', 'needs_edit', 'incomplete'

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

      // Set user email if available
      if (data.data.email) {
        email.value = data.data.email;
      }
    } else {
      // If no verification data exists, set to pending
      emailStatus.value = "pending";
      emailMessage.value = "";
    }
  } catch (e) {
    console.error("Error fetching verification status:", e);
    emailStatus.value = "error";
    emailMessage.value =
      "Could not fetch verification status. Please refresh the page.";
  }
};

const fetchWhatsappStatus = async () => {
  try {
    const res = await fetch(`/api/verification/${userType}/${userId}`);
    const data = await res.json();
    if (data.success && data.data) {
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
    }
  } catch (e) {
    whatsappStatus.value = "error";
    whatsappMessage.value = "Could not fetch WhatsApp status.";
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

const connectLinkedin = () => {
  linkedinLoading.value = true;
  linkedinMessage.value = "";
  try {
    // This is a placeholder for LinkedIn OAuth flow
    // In a real application, you would redirect the user to a LinkedIn authorization URL
    // and then handle the callback to verify the code.
    // For now, we'll simulate a successful connection.
    linkedinStatus.value = "verified";
    linkedinMessage.value = "LinkedIn Connected";
    showLinkedinModal.value = false;
  } catch (e) {
    linkedinStatus.value = "error";
    linkedinMessage.value = "Failed to connect LinkedIn.";
  } finally {
    linkedinLoading.value = false;
  }
};

const verifyLinkedinCode = async () => {
  linkedinCodeLoading.value = true;
  linkedinMessage.value = "";
  try {
    const res = await fetch(
      `/api/verification/${userType}/${userId}/linkedin/verify`,
      {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ code: linkedinCode.value }),
      }
    );
    const data = await res.json();
    if (data.success) {
      linkedinStatus.value = "verified";
      linkedinMessage.value = "LinkedIn Connected";
      showLinkedinModal.value = false;
    } else {
      linkedinStatus.value = "error";
      linkedinMessage.value = data.message || "Invalid code.";
    }
  } catch (e) {
    linkedinStatus.value = "error";
    linkedinMessage.value = "Failed to verify code.";
  } finally {
    linkedinCodeLoading.value = false;
  }
};

const completeProfile = () => {
  profileCompleted.value = true;
  reviewStatus.value = "verified_contact";
};

onMounted(async () => {
  try {
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
    await fetchVerificationStatus();
    await fetchWhatsappStatus();
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

const sendEmailVerification = async () => {
  emailLoading.value = true;
  emailMessage.value = "";

  const userEmail = userData.value.email;
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
      }),
    });

    const data = await res.json();
    if (data.success) {
      emailStatus.value = "sent";
      emailMessage.value =
        "Verification email sent successfully! Please check your inbox and click the verification link.";
      await fetchVerificationStatus(); // وضعیت را به‌روز کن
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
  <!-- Header Section -->
  <div class="timeline-header">
    <div class="container-header">
      <h1 class="welcome-title">
        Welcome <span class="account-name">[Account name]</span>
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

  <h2 class="section-title">Complete Following Steps to Verify Your Account</h2>

  <!-- Timeline Steps -->
  <div class="timeline">
    <!-- Step 1: Email Verification -->
    <div class="timeline-row">
      <div class="timeline-left">
        <div class="step-checkbox" :class="{ active: true, done: false }">
          <div class="checkbox-circle">
            <VIcon v-if="true" icon="tabler-check" size="16" color="white" />
          </div>
        </div>
        <div class="step-number" :class="{ active: true }">01</div>
        <div class="step-title">Email<br />Verification</div>
        <div class="vertical-line"></div>
      </div>
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
          />
          <VBtn
            color="orange"
            class="send-link-btn"
            @click="sendEmailVerification"
            :loading="emailLoading"
            :disabled="emailLoading"
          >
            <VIcon left size="20">tabler-mail</VIcon>
            Send Link
          </VBtn>
          <div
            v-if="emailMessage"
            :style="{
              color: emailStatus === 'error' ? 'red' : 'green',
              marginTop: '8px',
            }"
          >
            {{ emailMessage }}
          </div>
        </div>
      </div>
    </div>

    <!-- Step 2: WhatsApp Verification -->
    <div class="timeline-row">
      <div class="timeline-left">
        <div class="step-checkbox" :class="{ active: false, done: false }">
          <div class="checkbox-circle">
            <VIcon v-if="false" icon="tabler-check" size="16" color="white" />
          </div>
        </div>
        <div class="step-number" :class="{ active: false }">02</div>
        <div class="step-title">WhatsApp<br />Verification</div>
        <div class="vertical-line"></div>
      </div>
      <div class="timeline-card">
        <div class="card-title">
          Verify your WhatsApp number for secure communication
        </div>
        <div class="card-desc">
          Provide your WhatsApp number to receive a verification code. Enter the
          code to confirm your number.
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
          <VBtn color="success" class="send-code-btn" @click="sendWhatsappCode">
            <VIcon left size="20">tabler-brand-whatsapp</VIcon>
            Send Code
          </VBtn>
        </div>
      </div>
    </div>

    <!-- Step 3: LinkedIn Connection -->
    <div class="timeline-row">
      <div class="timeline-left">
        <div class="step-checkbox" :class="{ active: false, done: false }">
          <div class="checkbox-circle">
            <VIcon v-if="false" icon="tabler-check" size="16" color="white" />
          </div>
        </div>
        <div class="step-number" :class="{ active: false }">03</div>
        <div class="step-title">LinkedIn<br />Connection</div>
        <div class="vertical-line"></div>
      </div>
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
            class="connect-linkedin-btn"
            @click="connectLinkedin"
          >
            <VIcon left size="20">tabler-brand-linkedin</VIcon>
            Connect LinkedIn
          </VBtn>
        </div>
      </div>
    </div>

    <!-- Step 4: Profile Details -->
    <div class="timeline-row">
      <div class="timeline-left">
        <div class="step-checkbox" :class="{ active: false, done: false }">
          <div class="checkbox-circle">
            <VIcon v-if="false" icon="tabler-check" size="16" color="white" />
          </div>
        </div>
        <div class="step-number" :class="{ active: false }">04</div>
        <div class="step-title">Add your<br />profile details</div>
        <div class="vertical-line"></div>
      </div>
      <div class="timeline-card">
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
            @click="completeProfile"
          >
            <VIcon left size="20">tabler-file-text</VIcon>
            Complete Personal Form
          </VBtn>
          <div class="progress-bar-container">
            <div class="progress-bar" style="width: 0%"></div>
            <span>0%</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Step 5: Review & Activation -->
    <div class="timeline-row">
      <div class="timeline-left">
        <div class="step-checkbox" :class="{ active: false, done: false }">
          <div class="checkbox-circle">
            <VIcon v-if="false" icon="tabler-check" size="16" color="white" />
          </div>
        </div>
        <div class="step-number" :class="{ active: false }">05</div>
        <div class="step-title">Review &<br />Activation</div>
      </div>
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
    </div>
  </div>
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

.welcome-section {
  text-align: left;
  margin-bottom: 40px;
  padding: 20px;
  border-bottom: 1px solid #eee;
}

.welcome-section h1 {
  font-size: 2.5em;
  color: #333;
  margin-bottom: 15px;
}

.account-name {
  color: #ff8c00;
  font-family: "Anton", Arial, sans-serif;
}

.welcome-section p {
  font-size: 1.1em;
  line-height: 1.6;
  color: #666;
  margin-bottom: 25px;
}

.welcome-actions .btn {
  padding: 12px 25px;
  border-radius: 6px;
  font-size: 1em;
  cursor: pointer;
  margin-right: 15px;
}

.section-title {
  text-align: center;
  font-size: 2em;
  color: #333;
  margin: 60px 0 40px 0;
  position: relative;
}

/* --- Timeline --- */
.timeline {
  padding: 20px 0;
}

.timeline-row {
  display: flex;
  align-items: flex-start;
  margin-bottom: 48px;
  margin-top: 32px;
}

.timeline-left {
  width: 120px;
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
}

/* --- Step Checkbox --- */
.step-checkbox {
  margin-bottom: 8px;
}

.checkbox-circle {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid #e0e0e0;
  background-color: #fff;
  transition: all 0.3s ease;
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
  font-size: 2.6em;
  font-weight: bold;
  color: #666;
  line-height: 1;
  font-family: "Anton", Arial, sans-serif;
  margin-bottom: 4px;
  transition: color 0.3s ease;
}

.step-number.active {
  color: #000;
}

/* --- Step Title --- */
.step-title {
  font-size: 1.1em;
  color: #444;
  text-align: center;
  font-weight: 500;
  margin-bottom: 8px;
  line-height: 1.2;
}

/* --- Vertical Line --- */
.vertical-line {
  width: 3px;
  height: 120px;
  background: #ff8c00;
  border-radius: 2px;
  margin-top: 8px;
}

/* --- Timeline Card --- */
.timeline-card {
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  padding: 32px 32px 24px 32px;
  margin-left: 32px;
  min-width: 380px;
  max-width: 540px;
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.card-title {
  font-size: 1.15em;
  font-weight: bold;
  color: #444;
  margin-bottom: 10px;
}

.card-desc {
  font-size: 1em;
  color: #666;
  margin-bottom: 18px;
}

.card-actions {
  display: flex;
  align-items: center;
  gap: 12px;
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
  .timeline-row {
    flex-direction: column;
    align-items: stretch;
  }

  .timeline-left {
    flex-direction: row;
    width: 100%;
    justify-content: flex-start;
    margin-bottom: 12px;
  }

  .timeline-card {
    margin-left: 0;
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

.registration-timeline-page {
  min-height: 100vh;
  background: #f8f8fb;
  padding: 0;
}
.timeline-header {
  padding-top: 48px;
  padding-bottom: 24px;
  background: #f8f8fb;
}
.container-header {
  max-width: 700px;
  margin: 0 auto;
  text-align: left;
}
.welcome-title {
  font-family: "Anton", Arial, sans-serif;
  font-size: 2.5rem;
  font-weight: 700;
  color: #222;
  margin-bottom: 12px;
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
</style>
