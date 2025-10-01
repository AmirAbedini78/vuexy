<template>
  <VCard>
    <VCardTitle class="d-flex align-center justify-space-between">
      <span>Listing Management</span>
      <VBtn
        color="primary"
        prepend-icon="tabler-refresh"
        @click="loadListings"
        :loading="loading"
      >
        Refresh
      </VBtn>
    </VCardTitle>

    <VCardText>
      <!-- Search -->
      <VRow class="mb-4">
        <VCol cols="12" md="6">
          <VTextField
            v-model="searchQuery"
            label="Search listings"
            placeholder="Search by title..."
            prepend-inner-icon="tabler-search"
            variant="outlined"
            density="compact"
            @input="debouncedSearch"
          />
        </VCol>
      </VRow>

      <!-- Listings Table -->
      <VDataTable
        :headers="headers"
        :items="listings"
        :loading="loading"
        :search="searchQuery"
        class="text-no-wrap"
      >
        <!-- Title Column -->
        <template #item.listing_title="{ item }">
          <div>
            <div class="font-weight-medium">
              {{ item.listing_title || "N/A" }}
            </div>
            <div class="text-caption text-medium-emphasis">
              {{ item.subtitle || "No subtitle" }}
            </div>
          </div>
        </template>

        <!-- Type Column -->
        <template #item.listing_type="{ item }">
          <VChip
            :color="getListingTypeColor(item.listing_type)"
            size="small"
            class="font-weight-medium"
          >
            {{ formatListingType(item.listing_type) }}
          </VChip>
        </template>

        <!-- Status Column (combobox like Providers) -->
        <template #item.status="{ item }">
          <VSelect
            :model-value="item.status || 'submitted'"
            :items="eventStatusChoices"
            item-title="title"
            item-value="value"
            variant="outlined"
            density="compact"
            hide-details
            style="min-width: 150px"
            @update:model-value="(val) => changeListingStatus(item, val)"
            @click.stop
            @keydown.stop
            :menu-props="{ closeOnContentClick: true }"
          >
            <template #selection="{ item: sel }">
              <VChip
                :color="getListingStatusColor(sel?.value || item.status)"
                size="small"
                class="font-weight-medium"
              >
                {{ sel?.title || getEventStatusTitle(item.status) }}
              </VChip>
            </template>
            <template #item="{ item: opt }">
              <div class="d-flex align-center gap-2">
                <VChip
                  :color="getListingStatusColor(opt?.value)"
                  size="x-small"
                />
                <span>{{ opt?.title }}</span>
              </div>
            </template>
          </VSelect>
        </template>

        <!-- Price Column -->
        <template #item.price="{ item }">
          <span class="font-weight-medium">
            €{{ formatCurrency(item.price || 0) }}
          </span>
        </template>

        <!-- Capacity Column -->
        <template #item.capacity="{ item }">
          <span class="font-weight-medium">
            {{ item.min_capacity || 0 }} - {{ item.max_capacity || 0 }}
          </span>
        </template>

        <!-- Provider Column -->
        <template #item.provider="{ item }">
          <div>
            <div class="font-weight-medium">{{ item.user?.name || "N/A" }}</div>
            <div class="text-caption text-medium-emphasis">
              {{ item.user?.email || "N/A" }}
            </div>
          </div>
        </template>

        <!-- Created At Column -->
        <template #item.created_at="{ item }">
          {{ formatDate(item.created_at) }}
        </template>

        <!-- Actions Column -->
        <template #item.actions="{ item }">
          <VBtn
            icon
            variant="text"
            size="small"
            color="primary"
            @click="viewListing(item)"
          >
            <VIcon icon="tabler-eye" />
          </VBtn>
          <VBtn
            icon
            variant="text"
            size="small"
            color="warning"
            @click="editListing(item)"
          >
            <VIcon icon="tabler-edit" />
          </VBtn>
          <VBtn
            icon
            variant="text"
            size="small"
            color="error"
            @click="deleteListing(item)"
          >
            <VIcon icon="tabler-trash" />
          </VBtn>
        </template>
      </VDataTable>

      <!-- Pagination -->
      <div class="d-flex align-center justify-space-between mt-4">
        <div class="text-body-2 text-medium-emphasis">
          Showing {{ paginationInfo.from }} to {{ paginationInfo.to }} of
          {{ paginationInfo.total }} listings
        </div>
        <VPagination
          v-model="currentPage"
          :length="totalPages"
          @update:model-value="loadListings"
        />
      </div>
    </VCardText>
  </VCard>

  <!-- Listing View Dialog -->
  <VDialog v-model="showListingViewDialog" max-width="1200" persistent>
    <VCard>
      <VCardTitle class="d-flex align-center justify-space-between">
        <span>Listing Details - {{ selectedListing?.listing_title || 'N/A' }}</span>
        <VBtn
          icon
          variant="text"
          size="small"
          @click="showListingViewDialog = false"
        >
          <VIcon icon="tabler-x" />
        </VBtn>
      </VCardTitle>

      <VCardText v-if="selectedListing">
        <VTabs v-model="activeTab" class="mb-4">
          <VTab value="basic">Basic Information</VTab>
          <VTab value="provider">Provider Details</VTab>
          <VTab value="itinerary">Itinerary</VTab>
          <VTab value="addons">Special Addons</VTab>
          <VTab value="media">Media & Files</VTab>
          <VTab value="policies">Policies & Terms</VTab>
        </VTabs>

        <VTabsWindow v-model="activeTab">
          <!-- Basic Information Tab -->
          <VTabsWindowItem value="basic">
            <VRow>
              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3">Basic Information</h6>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Title:</strong>
                {{ selectedListing.listing_title || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Subtitle:</strong>
                {{ selectedListing.subtitle || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Type:</strong>
                <VChip
                  :color="getListingTypeColor(selectedListing.listing_type)"
                  size="small"
                  class="ml-2"
                >
                  {{ formatListingType(selectedListing.listing_type) }}
                </VChip>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Status:</strong>
                <VChip
                  :color="getListingStatusColor(selectedListing.status)"
                  size="small"
                  class="ml-2"
                >
                  {{ selectedListing.status || "Draft" }}
                </VChip>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Starting Date:</strong>
                {{ selectedListing.starting_date || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Finishing Date:</strong>
                {{ selectedListing.finishing_date || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Price:</strong>
                €{{ formatCurrency(selectedListing.price || 0) }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Capacity:</strong>
                {{ selectedListing.min_capacity || 0 }} -
                {{ selectedListing.max_capacity || 0 }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Experience Level:</strong>
                {{ selectedListing.experience_level || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Fitness Level:</strong>
                {{ selectedListing.fitness_level || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Group Language:</strong>
                {{ selectedListing.group_language || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Locations:</strong>
                {{ selectedListing.locations || "N/A" }}
              </VCol>

              <VCol cols="12">
                <strong>Description:</strong>
                <div class="mt-1">{{ selectedListing.listing_description || "N/A" }}</div>
              </VCol>

              <VCol cols="12">
                <strong>Activities Included:</strong>
                <div class="mt-1">{{ selectedListing.activities_included || "N/A" }}</div>
              </VCol>

              <VCol cols="12">
                <strong>What's Included:</strong>
                <div class="mt-1">{{ selectedListing.whats_included || "N/A" }}</div>
              </VCol>

              <VCol cols="12">
                <strong>What's Not Included:</strong>
                <div class="mt-1">{{ selectedListing.whats_not_included || "N/A" }}</div>
              </VCol>

              <VCol cols="12">
                <strong>Additional Notes:</strong>
                <div class="mt-1">{{ selectedListing.additional_notes || "N/A" }}</div>
              </VCol>

              <VCol cols="12">
                <strong>Provider's FAQ:</strong>
                <div class="mt-1">{{ selectedListing.providers_faq || "N/A" }}</div>
              </VCol>
            </VRow>
          </VTabsWindowItem>

          <!-- Provider Details Tab -->
          <VTabsWindowItem value="provider">
            <VRow>
              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3">Provider Information</h6>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Provider Name:</strong>
                {{ selectedListing.user?.name || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Provider Email:</strong>
                {{ selectedListing.user?.email || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>User ID:</strong>
                {{ selectedListing.user?.id || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>User Role:</strong>
                {{ selectedListing.user?.role || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>User Status:</strong>
                <VChip
                  :color="selectedListing.user?.status === 'active' ? 'success' : 'error'"
                  size="small"
                  class="ml-2"
                >
                  {{ selectedListing.user?.status || "N/A" }}
                </VChip>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Email Verified:</strong>
                <VChip
                  :color="selectedListing.user?.email_verified_at ? 'success' : 'error'"
                  size="small"
                  class="ml-2"
                >
                  {{ selectedListing.user?.email_verified_at ? "Yes" : "No" }}
                </VChip>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Created At:</strong>
                {{ selectedListing.user?.created_at ? formatDate(selectedListing.user.created_at) : "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Updated At:</strong>
                {{ selectedListing.user?.updated_at ? formatDate(selectedListing.user.updated_at) : "N/A" }}
              </VCol>

              <!-- Individual User Details -->
              <template v-if="selectedListing.user?.individualUser">
                <VCol cols="12">
                  <h6 class="text-h6 font-weight-medium mb-3 mt-4">Individual Provider Details</h6>
                </VCol>

                <VCol cols="12" md="6">
                  <strong>Full Name:</strong>
                  {{ selectedListing.user.individualUser.full_name || "N/A" }}
                </VCol>

                <VCol cols="12" md="6">
                  <strong>Nationality:</strong>
                  {{ selectedListing.user.individualUser.nationality || "N/A" }}
                </VCol>

                <VCol cols="12" md="6">
                  <strong>Date of Birth:</strong>
                  {{ selectedListing.user.individualUser.dob ? formatDate(selectedListing.user.individualUser.dob) : "N/A" }}
                </VCol>

                <VCol cols="12" md="6">
                  <strong>Languages:</strong>
                  {{ Array.isArray(selectedListing.user.individualUser.languages) ? selectedListing.user.individualUser.languages.join(", ") : selectedListing.user.individualUser.languages || "N/A" }}
                </VCol>

                <VCol cols="12" md="6">
                  <strong>Activity Specialization:</strong>
                  {{ selectedListing.user.individualUser.activity_specialization || "N/A" }}
                </VCol>

                <VCol cols="12" md="6">
                  <strong>Years of Experience:</strong>
                  {{ selectedListing.user.individualUser.years_of_experience || "N/A" }}
                </VCol>

                <VCol cols="12" md="6">
                  <strong>Country of Operation:</strong>
                  {{ selectedListing.user.individualUser.country_of_operation || "N/A" }}
                </VCol>

                <VCol cols="12" md="6">
                  <strong>Emergency Contact:</strong>
                  {{ selectedListing.user.individualUser.emergency_contact_name || "N/A" }}
                </VCol>

                <VCol cols="12" md="6">
                  <strong>Emergency Phone:</strong>
                  {{ selectedListing.user.individualUser.emergency_contact_phone || "N/A" }}
                </VCol>

                <VCol cols="12" md="6">
                  <strong>Want to be Listed:</strong>
                  <VChip
                    :color="selectedListing.user.individualUser.want_to_be_listed === 'yes' ? 'success' : selectedListing.user.individualUser.want_to_be_listed === 'no' ? 'error' : 'warning'"
                    size="small"
                    class="ml-2"
                  >
                    {{ selectedListing.user.individualUser.want_to_be_listed || "N/A" }}
                  </VChip>
                </VCol>

                <VCol cols="12" md="6">
                  <strong>Terms Accepted:</strong>
                  <VChip
                    :color="selectedListing.user.individualUser.terms_accepted ? 'success' : 'error'"
                    size="small"
                    class="ml-2"
                  >
                    {{ selectedListing.user.individualUser.terms_accepted ? "Yes" : "No" }}
                  </VChip>
                </VCol>

                <VCol cols="12">
                  <strong>Address:</strong>
                  {{ selectedListing.user.individualUser.address1 || "N/A" }}
                  <span v-if="selectedListing.user.individualUser.address2">, {{ selectedListing.user.individualUser.address2 }}</span>
                </VCol>

                <VCol cols="12" md="4">
                  <strong>City:</strong>
                  {{ selectedListing.user.individualUser.city || "N/A" }}
                </VCol>

                <VCol cols="12" md="4">
                  <strong>State:</strong>
                  {{ selectedListing.user.individualUser.state || "N/A" }}
                </VCol>

                <VCol cols="12" md="4">
                  <strong>Postal Code:</strong>
                  {{ selectedListing.user.individualUser.postal_code || "N/A" }}
                </VCol>

                <VCol cols="12" md="6">
                  <strong>Country:</strong>
                  {{ selectedListing.user.individualUser.country || "N/A" }}
                </VCol>

                <VCol cols="12">
                  <strong>Short Bio:</strong>
                  <div class="mt-1">{{ selectedListing.user.individualUser.short_bio || "N/A" }}</div>
                </VCol>

                <VCol cols="12">
                  <strong>Certifications:</strong>
                  <div class="mt-1">{{ selectedListing.user.individualUser.certifications || "N/A" }}</div>
                </VCol>
              </template>

              <!-- Company User Details -->
              <template v-if="selectedListing.user?.companyUser">
                <VCol cols="12">
                  <h6 class="text-h6 font-weight-medium mb-3 mt-4">Company Provider Details</h6>
                </VCol>

                <VCol cols="12" md="6">
                  <strong>Company Name:</strong>
                  {{ selectedListing.user.companyUser.company_name || "N/A" }}
                </VCol>

                <VCol cols="12" md="6">
                  <strong>VAT ID:</strong>
                  {{ selectedListing.user.companyUser.vat_id || "N/A" }}
                </VCol>

                <VCol cols="12" md="6">
                  <strong>Contact Person:</strong>
                  {{ selectedListing.user.companyUser.contact_person || "N/A" }}
                </VCol>

                <VCol cols="12" md="6">
                  <strong>Country of Registration:</strong>
                  {{ selectedListing.user.companyUser.country_of_registration || "N/A" }}
                </VCol>

                <VCol cols="12" md="6">
                  <strong>Business Type:</strong>
                  {{ selectedListing.user.companyUser.business_type || "N/A" }}
                </VCol>

                <VCol cols="12" md="6">
                  <strong>Activity Specialization:</strong>
                  {{ selectedListing.user.companyUser.activity_specialization || "N/A" }}
                </VCol>

                <VCol cols="12" md="6">
                  <strong>Company Website:</strong>
                  <a v-if="selectedListing.user.companyUser.company_website" :href="selectedListing.user.companyUser.company_website" target="_blank" class="text-primary">
                    {{ selectedListing.user.companyUser.company_website }}
                  </a>
                  <span v-else>N/A</span>
                </VCol>

                <VCol cols="12" md="6">
                  <strong>Want to be Listed:</strong>
                  <VChip
                    :color="selectedListing.user.companyUser.want_to_be_listed === 'yes' ? 'success' : selectedListing.user.companyUser.want_to_be_listed === 'no' ? 'error' : 'warning'"
                    size="small"
                    class="ml-2"
                  >
                    {{ selectedListing.user.companyUser.want_to_be_listed || "N/A" }}
                  </VChip>
                </VCol>

                <VCol cols="12" md="6">
                  <strong>Terms Accepted:</strong>
                  <VChip
                    :color="selectedListing.user.companyUser.terms_accepted ? 'success' : 'error'"
                    size="small"
                    class="ml-2"
                  >
                    {{ selectedListing.user.companyUser.terms_accepted ? "Yes" : "No" }}
                  </VChip>
                </VCol>

                <VCol cols="12">
                  <strong>Address:</strong>
                  {{ selectedListing.user.companyUser.address1 || "N/A" }}
                  <span v-if="selectedListing.user.companyUser.address2">, {{ selectedListing.user.companyUser.address2 }}</span>
                </VCol>

                <VCol cols="12" md="4">
                  <strong>City:</strong>
                  {{ selectedListing.user.companyUser.city || "N/A" }}
                </VCol>

                <VCol cols="12" md="4">
                  <strong>State:</strong>
                  {{ selectedListing.user.companyUser.state || "N/A" }}
                </VCol>

                <VCol cols="12" md="4">
                  <strong>Postal Code:</strong>
                  {{ selectedListing.user.companyUser.postal_code || "N/A" }}
                </VCol>

                <VCol cols="12" md="6">
                  <strong>Country:</strong>
                  {{ selectedListing.user.companyUser.country || "N/A" }}
                </VCol>

                <VCol cols="12">
                  <strong>Short Bio:</strong>
                  <div class="mt-1">{{ selectedListing.user.companyUser.short_bio || "N/A" }}</div>
                </VCol>

                <VCol cols="12">
                  <strong>Certifications:</strong>
                  <div class="mt-1">{{ selectedListing.user.companyUser.certifications || "N/A" }}</div>
                </VCol>
              </template>
            </VRow>
          </VTabsWindowItem>

          <!-- Itinerary Tab -->
          <VTabsWindowItem value="itinerary">
            <VRow>
              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3">Itinerary Details</h6>
              </VCol>

              <template v-if="selectedListing.itineraries && selectedListing.itineraries.length > 0">
                <VCol cols="12" v-for="(day, index) in selectedListing.itineraries" :key="index">
                  <VCard variant="outlined" class="mb-3">
                    <VCardTitle class="text-h6">
                      Day {{ day.day_number }}: {{ day.title || 'Untitled' }}
                    </VCardTitle>
                    <VCardText>
                      <VRow>
                        <VCol cols="12" md="6">
                          <strong>Accommodation:</strong>
                          {{ day.accommodation || "N/A" }}
                        </VCol>
                        <VCol cols="12" md="6">
                          <strong>Location:</strong>
                          {{ day.location || "N/A" }}
                        </VCol>
                        <VCol cols="12" v-if="day.link">
                          <strong>Link:</strong>
                          <a :href="day.link" target="_blank" class="text-primary">{{ day.link }}</a>
                        </VCol>
                        <VCol cols="12">
                          <strong>Description:</strong>
                          <div class="mt-1">{{ day.description || "N/A" }}</div>
                        </VCol>
                      </VRow>
                    </VCardText>
                  </VCard>
                </VCol>
              </template>
              <template v-else>
                <VCol cols="12">
                  <VAlert type="info" variant="tonal">
                    No itinerary details available for this listing.
                  </VAlert>
                </VCol>
              </template>
            </VRow>
          </VTabsWindowItem>

          <!-- Special Addons Tab -->
          <VTabsWindowItem value="addons">
            <VRow>
              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3">Special Addons</h6>
              </VCol>

              <template v-if="selectedListing.special_addons && selectedListing.special_addons.length > 0">
                <VCol cols="12" v-for="(addon, index) in selectedListing.special_addons" :key="index">
                  <VCard variant="outlined" class="mb-3">
                    <VCardTitle class="text-h6">
                      {{ addon.title || 'Untitled Addon' }}
                      <VChip
                        :color="addon.is_active ? 'success' : 'error'"
                        size="small"
                        class="ml-2"
                      >
                        {{ addon.is_active ? 'Active' : 'Inactive' }}
                      </VChip>
                    </VCardTitle>
                    <VCardText>
                      <VRow>
                        <VCol cols="12" md="6">
                          <strong>Number:</strong>
                          {{ addon.number || "N/A" }}
                        </VCol>
                        <VCol cols="12" md="6">
                          <strong>Price:</strong>
                          €{{ formatCurrency(addon.price || 0) }}
                        </VCol>
                        <VCol cols="12" md="6">
                          <strong>Order:</strong>
                          {{ addon.order || "N/A" }}
                        </VCol>
                        <VCol cols="12">
                          <strong>Description:</strong>
                          <div class="mt-1">{{ addon.description || "N/A" }}</div>
                        </VCol>
                      </VRow>
                    </VCardText>
                  </VCard>
                </VCol>
              </template>
              <template v-else>
                <VCol cols="12">
                  <VAlert type="info" variant="tonal">
                    No special addons available for this listing.
                  </VAlert>
                </VCol>
              </template>
            </VRow>
          </VTabsWindowItem>

          <!-- Media & Files Tab -->
          <VTabsWindowItem value="media">
            <VRow>
              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3">Media & Files</h6>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Listing Media:</strong>
                <div class="mt-1">
                  <template v-if="selectedListing.listing_media && Array.isArray(selectedListing.listing_media) && selectedListing.listing_media.length > 0">
                    <VChip
                      v-for="(media, index) in selectedListing.listing_media"
                      :key="index"
                      size="small"
                      class="mr-2 mb-2"
                    >
                      {{ media.name || media.filename || `Media ${index + 1}` }}
                    </VChip>
                  </template>
                  <span v-else>N/A</span>
                </div>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Promotional Video:</strong>
                <div class="mt-1">
                  <template v-if="selectedListing.promotional_video && Array.isArray(selectedListing.promotional_video) && selectedListing.promotional_video.length > 0">
                    <VChip
                      v-for="(video, index) in selectedListing.promotional_video"
                      :key="index"
                      size="small"
                      class="mr-2 mb-2"
                    >
                      {{ video.name || video.filename || `Video ${index + 1}` }}
                    </VChip>
                  </template>
                  <span v-else>N/A</span>
                </div>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Maps and Routes:</strong>
                <div class="mt-1">
                  <template v-if="selectedListing.maps_and_routes && Array.isArray(selectedListing.maps_and_routes) && selectedListing.maps_and_routes.length > 0">
                    <VChip
                      v-for="(route, index) in selectedListing.maps_and_routes"
                      :key="index"
                      size="small"
                      class="mr-2 mb-2"
                    >
                      {{ route.name || route.filename || `Route ${index + 1}` }}
                    </VChip>
                  </template>
                  <span v-else>N/A</span>
                </div>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Activities Included (JSON):</strong>
                <div class="mt-1">
                  <template v-if="selectedListing.activities_included && Array.isArray(selectedListing.activities_included) && selectedListing.activities_included.length > 0">
                    <VChip
                      v-for="(activity, index) in selectedListing.activities_included"
                      :key="index"
                      size="small"
                      class="mr-2 mb-2"
                    >
                      {{ activity.name || activity || `Activity ${index + 1}` }}
                    </VChip>
                  </template>
                  <span v-else>N/A</span>
                </div>
              </VCol>
            </VRow>
          </VTabsWindowItem>

          <!-- Policies & Terms Tab -->
          <VTabsWindowItem value="policies">
            <VRow>
              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3">Policies & Terms</h6>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Personal Policies:</strong>
                {{ selectedListing.personal_policies || "N/A" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Terms Accepted:</strong>
                <VChip
                  :color="selectedListing.terms_accepted ? 'success' : 'error'"
                  size="small"
                  class="ml-2"
                >
                  {{ selectedListing.terms_accepted ? "Yes" : "No" }}
                </VChip>
              </VCol>

              <VCol cols="12">
                <strong>Personal Policies Text:</strong>
                <div class="mt-1">{{ selectedListing.personal_policies_text || "N/A" }}</div>
              </VCol>

              <VCol cols="12">
                <strong>Created At:</strong>
                {{ formatDate(selectedListing.created_at) }}
              </VCol>

              <VCol cols="12">
                <strong>Updated At:</strong>
                {{ formatDate(selectedListing.updated_at) }}
              </VCol>
            </VRow>
          </VTabsWindowItem>
        </VTabsWindow>
      </VCardText>

      <VCardActions>
        <VSpacer />
        <VBtn color="primary" @click="showListingViewDialog = false">
          Close
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>

  <!-- Listing Edit Dialog -->
  <VDialog v-model="showListingEditDialog" max-width="1200" persistent>
    <VCard>
      <VCardTitle class="d-flex align-center justify-space-between">
        <span>Edit Listing: {{ selectedListing?.listing_title }}</span>
        <VBtn
          icon
          variant="text"
          size="small"
          @click="showListingEditDialog = false"
        >
          <VIcon icon="tabler-x" />
        </VBtn>
      </VCardTitle>

      <VCardText v-if="selectedListing">
        <!-- Listing Edit Wizard -->
        <ListingEditWizard
          :listing="selectedListing"
          @close="showListingEditDialog = false"
          @updated="handleListingUpdated"
        />
      </VCardText>
    </VCard>
  </VDialog>
</template>

<script setup>
import ListingEditWizard from "@/components/admin/ListingEditWizard.vue";

definePage({
  meta: {
    layout: "default",
    requiresAuth: true,
    requiresAdmin: true,
  },
});

const loading = ref(false);
const listings = ref([]);
const searchQuery = ref("");
const currentPage = ref(1);
const totalPages = ref(1);
const paginationInfo = ref({
  from: 0,
  to: 0,
  total: 0,
});

// Dialogs
const showListingViewDialog = ref(false);
const showListingEditDialog = ref(false);
const selectedListing = ref(null);
const activeTab = ref('basic');

const headers = [
  { title: "Title", key: "listing_title", sortable: true },
  { title: "Type", key: "listing_type", sortable: true },
  { title: "Status", key: "status", sortable: true },
  { title: "Price", key: "price", sortable: true },
  { title: "Capacity", key: "capacity", sortable: true },
  { title: "Provider", key: "provider", sortable: true },
  { title: "Created At", key: "created_at", sortable: true },
  { title: "Actions", key: "actions", sortable: false },
];

// Native debounce function
let searchTimeout = null;
const debouncedSearch = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    loadListings();
  }, 300);
};

const loadListings = async () => {
  loading.value = true;
  try {
    const params = new URLSearchParams({
      page: currentPage.value.toString(),
      search: searchQuery.value,
    });

    const response = await $api(`/admin/listings?${params}`, {
      method: "GET",
    });

    listings.value = response.data;
    paginationInfo.value = {
      from: response.from,
      to: response.to,
      total: response.total,
    };
    totalPages.value = response.last_page;
  } catch (error) {
    console.error("Error loading listings:", error);
  } finally {
    loading.value = false;
  }
};

const formatDate = (dateString) => {
  if (!dateString) return "";
  return new Date(dateString).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat("en-US", {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount);
};

const getListingStatusColor = (status) => {
  const colors = {
    submitted: "secondary",
    approved: "info",
    live: "success",
    denied: "error",
    edit_review: "warning",
    other_events: "grey",
    inactive: "secondary",
  };
  return colors[status] || "secondary";
};
// Event status choices matching the provided image
const eventStatusChoices = [
  { title: "Submitted", value: "submitted" },
  { title: "Approved", value: "approved" },
  { title: "Live", value: "live" },
  { title: "Denied", value: "denied" },
  { title: "Edit Review", value: "edit_review" },
  { title: "Other Events", value: "other_events" },
  { title: "Inactive", value: "inactive" },
];

const getEventStatusTitle = (status) => {
  const map = Object.fromEntries(
    eventStatusChoices.map((s) => [s.value, s.title])
  );
  return map[status] || "Submitted";
};

const changeListingStatus = async (listing, status) => {
  try {
    if (!status) return;
    const res = await $api(`/admin/listings/${listing.id}`, {
      method: "PUT",
      body: { status },
    });
    if (res?.success) {
      listing.status = status;
    } else {
      console.error("Failed to update listing status", res);
    }
  } catch (e) {
    console.error("Error updating listing status", e);
  }
};

const getListingTypeColor = (type) => {
  const colors = {
    "single-date": "primary",
    "multi-date": "info",
    "open-date": "success",
    other: "warning",
  };
  return colors[type] || "secondary";
};

const formatListingType = (type) => {
  const types = {
    "single-date": "Single Date",
    "multi-date": "Multi Date",
    "open-date": "Open Date",
    other: "Other",
  };
  return types[type] || type || "N/A";
};

const viewListing = (listing) => {
  selectedListing.value = listing;
  showListingViewDialog.value = true;
};

const editListing = (listing) => {
  selectedListing.value = listing;
  showListingEditDialog.value = true;
};

const handleListingUpdated = () => {
  console.log("Listing updated, reloading data...");
  loadListings();
  showListingEditDialog.value = false;
};

const deleteListing = async (listing) => {
  if (
    confirm(
      `Are you sure you want to delete listing "${listing.listing_title}"?`
    )
  ) {
    try {
      await $api(`/admin/listings/${listing.id}`, {
        method: "DELETE",
      });
      loadListings();
    } catch (error) {
      console.error("Error deleting listing:", error);
    }
  }
};

// Load listings on mount
onMounted(() => {
  // Check if user is admin
  const userDataCookie = useCookie("userData");
  const userData = userDataCookie.value;

  if (!userData || userData.role !== "admin") {
    console.log("Non-admin user detected, redirecting...");
    router.push("/");
    return;
  }

  loadListings();
});
</script>

<style scoped>
.v-data-table {
  border-radius: 8px;
  overflow: hidden;
}
</style>
