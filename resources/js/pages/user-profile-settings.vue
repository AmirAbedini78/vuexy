<script setup>
definePage({
  meta: {
    layout: "default",
    action: "read",
    subject: "Profile",
  },
});

const userProfile = ref({
  firstName: "John",
  lastName: "Doe",
  email: "john.doe@example.com",
  phone: "+1 234 567 8900",
  bio: "Experienced adventure guide with over 5 years of experience leading hiking and outdoor activities.",
  location: "Swiss Alps, Switzerland",
  website: "https://johndoe-adventures.com",
  socialMedia: {
    facebook: "https://facebook.com/johndoe",
    instagram: "https://instagram.com/johndoe",
    linkedin: "https://linkedin.com/in/johndoe",
    twitter: "https://twitter.com/johndoe",
  },
  preferences: {
    emailNotifications: true,
    smsNotifications: false,
    marketingEmails: true,
    publicProfile: true,
  },
});

const profileImage = ref(null);
const loading = ref(false);

const rules = {
  firstName: [(v) => !!v || "First name is required"],
  lastName: [(v) => !!v || "Last name is required"],
  email: [
    (v) => !!v || "Email is required",
    (v) => /.+@.+\..+/.test(v) || "Email must be valid",
  ],
  phone: [
    (v) =>
      !v || /^[\+]?[1-9][\d]{0,15}$/.test(v) || "Phone number must be valid",
  ],
  website: [
    (v) => !v || /^https?:\/\/.+/.test(v) || "Website must be a valid URL",
  ],
};

const refForm = ref();

const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    // Here you would typically upload the image to your server
    console.log("Uploading image:", file);
    profileImage.value = URL.createObjectURL(file);
  }
};

const saveProfile = async () => {
  const { valid } = await refForm.value.validate();

  if (!valid) return;

  loading.value = true;

  try {
    // Here you would typically send the data to your API
    console.log("Saving profile:", userProfile.value);

    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Show success message
    // You can use a toast notification here
  } catch (error) {
    console.error("Error saving profile:", error);
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div>
    <VCard>
      <VCardTitle class="pa-6">
        <div>
          <h2 class="text-h4 font-weight-bold">User Profile Settings</h2>
          <p class="text-body-2 text-medium-emphasis mt-1">
            Manage your profile information and preferences
          </p>
        </div>
      </VCardTitle>

      <VDivider />

      <VCardText class="pa-6">
        <VForm ref="refForm" @submit.prevent="saveProfile">
          <VRow>
            <!-- Profile Image -->
            <VCol cols="12" class="text-center mb-6">
              <div class="d-flex flex-column align-center">
                <VAvatar :image="profileImage" size="120" class="mb-4">
                  <VIcon size="48" icon="tabler-user" />
                </VAvatar>
                <VBtn
                  variant="outlined"
                  prepend-icon="tabler-upload"
                  @click="$refs.fileInput.click()"
                >
                  Upload Photo
                </VBtn>
                <input
                  ref="fileInput"
                  type="file"
                  accept="image/*"
                  style="display: none"
                  @change="handleImageUpload"
                />
              </div>
            </VCol>

            <!-- Basic Information -->
            <VCol cols="12">
              <h3 class="text-h6 font-weight-bold mb-4">Basic Information</h3>
            </VCol>

            <VCol cols="12" md="6">
              <VTextField
                v-model="userProfile.firstName"
                label="First Name"
                placeholder="Enter your first name"
                :rules="rules.firstName"
                variant="outlined"
                required
              />
            </VCol>

            <VCol cols="12" md="6">
              <VTextField
                v-model="userProfile.lastName"
                label="Last Name"
                placeholder="Enter your last name"
                :rules="rules.lastName"
                variant="outlined"
                required
              />
            </VCol>

            <VCol cols="12" md="6">
              <VTextField
                v-model="userProfile.email"
                label="Email Address"
                placeholder="Enter your email address"
                :rules="rules.email"
                variant="outlined"
                required
              />
            </VCol>

            <VCol cols="12" md="6">
              <VTextField
                v-model="userProfile.phone"
                label="Phone Number"
                placeholder="Enter your phone number"
                :rules="rules.phone"
                variant="outlined"
              />
            </VCol>

            <VCol cols="12">
              <VTextarea
                v-model="userProfile.bio"
                label="Bio"
                placeholder="Tell us about yourself..."
                variant="outlined"
                rows="4"
              />
            </VCol>

            <VCol cols="12" md="6">
              <VTextField
                v-model="userProfile.location"
                label="Location"
                placeholder="Enter your location"
                variant="outlined"
              />
            </VCol>

            <VCol cols="12" md="6">
              <VTextField
                v-model="userProfile.website"
                label="Website"
                placeholder="https://your-website.com"
                :rules="rules.website"
                variant="outlined"
              />
            </VCol>

            <!-- Social Media -->
            <VCol cols="12">
              <h3 class="text-h6 font-weight-bold mb-4">Social Media</h3>
            </VCol>

            <VCol cols="12" md="6">
              <VTextField
                v-model="userProfile.socialMedia.facebook"
                label="Facebook"
                placeholder="https://facebook.com/your-profile"
                variant="outlined"
                prepend-inner-icon="tabler-brand-facebook"
              />
            </VCol>

            <VCol cols="12" md="6">
              <VTextField
                v-model="userProfile.socialMedia.instagram"
                label="Instagram"
                placeholder="https://instagram.com/your-profile"
                variant="outlined"
                prepend-inner-icon="tabler-brand-instagram"
              />
            </VCol>

            <VCol cols="12" md="6">
              <VTextField
                v-model="userProfile.socialMedia.linkedin"
                label="LinkedIn"
                placeholder="https://linkedin.com/in/your-profile"
                variant="outlined"
                prepend-inner-icon="tabler-brand-linkedin"
              />
            </VCol>

            <VCol cols="12" md="6">
              <VTextField
                v-model="userProfile.socialMedia.twitter"
                label="Twitter"
                placeholder="https://twitter.com/your-profile"
                variant="outlined"
                prepend-inner-icon="tabler-brand-twitter"
              />
            </VCol>

            <!-- Preferences -->
            <VCol cols="12">
              <h3 class="text-h6 font-weight-bold mb-4">Preferences</h3>
            </VCol>

            <VCol cols="12">
              <VCard variant="outlined" class="pa-4">
                <VRow>
                  <VCol cols="12" md="6">
                    <VSwitch
                      v-model="userProfile.preferences.emailNotifications"
                      label="Email Notifications"
                      color="primary"
                    />
                    <p class="text-caption text-medium-emphasis mt-1">
                      Receive notifications about bookings and events via email
                    </p>
                  </VCol>

                  <VCol cols="12" md="6">
                    <VSwitch
                      v-model="userProfile.preferences.smsNotifications"
                      label="SMS Notifications"
                      color="primary"
                    />
                    <p class="text-caption text-medium-emphasis mt-1">
                      Receive notifications via SMS
                    </p>
                  </VCol>

                  <VCol cols="12" md="6">
                    <VSwitch
                      v-model="userProfile.preferences.marketingEmails"
                      label="Marketing Emails"
                      color="primary"
                    />
                    <p class="text-caption text-medium-emphasis mt-1">
                      Receive promotional emails and updates
                    </p>
                  </VCol>

                  <VCol cols="12" md="6">
                    <VSwitch
                      v-model="userProfile.preferences.publicProfile"
                      label="Public Profile"
                      color="primary"
                    />
                    <p class="text-caption text-medium-emphasis mt-1">
                      Make your profile visible to other users
                    </p>
                  </VCol>
                </VRow>
              </VCard>
            </VCol>

            <!-- Save Button -->
            <VCol cols="12" class="d-flex justify-end">
              <VBtn
                type="submit"
                color="primary"
                :loading="loading"
                prepend-icon="tabler-device-floppy"
              >
                Save Changes
              </VBtn>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </VCard>
  </div>
</template>

<style scoped>
.v-card {
  border-radius: 8px;
}
</style>
