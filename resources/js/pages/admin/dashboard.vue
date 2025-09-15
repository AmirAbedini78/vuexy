<template>
  <div class="admin-dashboard">
    <!-- Admin Panel Title -->
    <div class="admin-panel-title mb-6">
      <h1 class="text-h3 font-weight-bold">Admin Panel</h1>
    </div>

    <!-- Overview Section -->
    <div class="section-title mb-4">
      <h2 class="text-h4 font-weight-bold">Overview</h2>
    </div>

    <!-- KPI Cards Section - Single Card with Dividers -->
    <VCard class="kpi-container mb-8">
      <VCardText class="pa-0">
        <VRow no-gutters>
          <VCol cols="12" sm="6" md="3" class="kpi-section">
            <div class="kpi-content-wrapper">
              <div class="kpi-content">
                <div class="kpi-header">
                  <div class="kpi-icon-wrapper">
                    <VIcon icon="tabler-eye" size="20" color="#6c757d" />
                  </div>
                  <p class="kpi-title">All Providers</p>
                </div>
                <h3 class="kpi-value">{{ stats.all_providers || 211 }}</h3>
                <p class="kpi-trend positive">This month +15.9%</p>
              </div>
            </div>
          </VCol>

          <VCol cols="12" sm="6" md="3" class="kpi-section">
            <div class="kpi-content-wrapper">
              <div class="kpi-content">
                <div class="kpi-header">
                  <div class="kpi-icon-wrapper">
                    <VIcon icon="tabler-file-text" size="20" color="#6c757d" />
                  </div>
                  <p class="kpi-title">Total Bookings</p>
                </div>
                <h3 class="kpi-value">{{ stats.total_bookings || 345 }}</h3>
                <p class="kpi-trend positive">This month +5.7%</p>
              </div>
            </div>
          </VCol>

          <VCol cols="12" sm="6" md="3" class="kpi-section">
            <div class="kpi-content-wrapper">
              <div class="kpi-content">
                <div class="kpi-header">
                  <div class="kpi-icon-wrapper">
                    <VIcon icon="tabler-chart-bar" size="20" color="#6c757d" />
                  </div>
                  <p class="kpi-title">Total Explorer Passports</p>
                </div>
                <h3 class="kpi-value">
                  {{ stats.total_explorer_passports || 840 }}
                </h3>
                <p class="kpi-trend positive">This month +12.4%</p>
              </div>
            </div>
          </VCol>

          <VCol cols="12" sm="6" md="3" class="kpi-section">
            <div class="kpi-content-wrapper">
              <div class="kpi-content">
                <div class="kpi-header">
                  <div class="kpi-icon-wrapper">
                    <VIcon
                      icon="tabler-currency-euro"
                      size="20"
                      color="#6c757d"
                    />
                  </div>
                  <p class="kpi-title">Total Revenue</p>
                </div>
                <h3 class="kpi-value">
                  €{{ formatCurrency(stats.total_revenue || 590000) }}
                </h3>
                <p class="kpi-trend">This month</p>
              </div>
            </div>
          </VCol>
        </VRow>
      </VCardText>
    </VCard>

    <!-- All Events Section -->
    <div class="section-title mb-4">
      <h2 class="text-h4 font-weight-bold">All Events (Listings)</h2>
    </div>

    <VCard class="mb-8">
      <VCardTitle class="d-flex align-center justify-space-between pa-6">
        <div class="d-flex align-center">
          <VTextField
            v-model="eventsSearch"
            label="Search Events"
            placeholder="Search events..."
            prepend-inner-icon="tabler-search"
            variant="outlined"
            density="compact"
            class="me-4"
            style="width: 300px"
          />
        </div>
        <div class="d-flex gap-2">
          <VBtn
            variant="outlined"
            prepend-icon="tabler-upload"
            @click="exportEvents"
          >
            Export
          </VBtn>
          <VBtn color="warning" prepend-icon="tabler-plus" @click="addEvent">
            Add Event
          </VBtn>
        </div>
      </VCardTitle>

      <VDataTable
        :headers="eventsHeaders"
        :items="eventsData"
        :search="eventsSearch"
        :items-per-page="7"
        class="events-table"
      >
        <!-- Events Column -->
        <template #item.events="{ item }">
          <div>
            <div class="font-weight-medium">
              {{ item.listing_title || item.eventTitle }}
            </div>
            <div class="text-caption text-medium-emphasis">
              {{ item.locations || item.location }}
            </div>
          </div>
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

        <!-- Listing Type Column -->
        <template #item.listing_type="{ item }">
          <VChip
            :color="getListingTypeColor(item.listing_type || item.type)"
            size="small"
            class="font-weight-medium"
          >
            {{ formatListingType(item.listing_type || item.type) }}
          </VChip>
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

        <!-- Status Column -->
        <template #item.status="{ item }">
          <VMenu>
            <template #activator="{ props }">
              <VChip
                v-bind="props"
                :color="getEventStatusColor(item.status || 'submitted')"
                size="small"
                class="font-weight-medium cursor-pointer"
                style="min-width: 120px; justify-content: center"
              >
                {{ getEventStatusTitle(item.status || "submitted") }}
                <VIcon icon="tabler-chevron-down" size="small" class="ml-1" />
              </VChip>
            </template>
            <VList>
              <VListItem
                v-for="choice in eventStatusChoices"
                :key="choice.value"
                @click="changeEventStatus(item, choice.value)"
                :class="{
                  'bg-primary-lighten-5': item.status === choice.value,
                }"
              >
                <template #prepend>
                  <VChip
                    :color="getEventStatusColor(choice.value)"
                    size="x-small"
                  />
                </template>
                <VListItemTitle>{{ choice.title }}</VListItemTitle>
              </VListItem>
            </VList>
          </VMenu>
        </template>

        <!-- Actions Column -->
        <template #item.actions="{ item }">
          <div class="d-flex gap-1">
            <VBtn icon variant="text" size="small" @click="viewEvent(item)">
              <VIcon icon="tabler-eye" size="18" />
            </VBtn>
            <VBtn icon variant="text" size="small" @click="editEvent(item)">
              <VIcon icon="tabler-pencil" size="18" />
            </VBtn>
          </div>
        </template>

        <!-- Pagination -->
        <template #bottom>
          <VCardText class="pt-2">
            <div
              class="d-flex flex-wrap justify-center justify-sm-space-between gap-y-2 mt-2"
            >
              <span class="text-body-2"
                >Showing 1 to 7 of {{ eventsData.length }} entries</span
              >
              <VPagination
                v-model="eventsPage"
                :total-visible="$vuetify.display.smAndDown ? 3 : 5"
                :length="Math.ceil(eventsData.length / 7)"
              />
            </div>
          </VCardText>
        </template>
      </VDataTable>
    </VCard>

    <!-- All Bookings Section -->
    <div class="section-title mb-4">
      <h2 class="text-h4 font-weight-bold">All Bookings</h2>
    </div>

    <VCard class="mb-8">
      <VCardTitle class="d-flex align-center justify-space-between pa-6">
        <div class="d-flex align-center">
          <VTextField
            v-model="usersSearch"
            label="Search Users"
            placeholder="Search users..."
            prepend-inner-icon="tabler-search"
            variant="outlined"
            density="compact"
            class="me-4"
            style="width: 300px"
          />
        </div>
        <div class="d-flex gap-2">
          <VBtn
            variant="outlined"
            prepend-icon="tabler-upload"
            @click="exportUsers"
          >
            Export
          </VBtn>
          <VBtn color="warning" prepend-icon="tabler-plus" @click="addBooking">
            Add Booking
          </VBtn>
        </div>
      </VCardTitle>

      <VDataTable
        :headers="usersHeaders"
        :items="usersData"
        :search="usersSearch"
        :items-per-page="7"
        class="users-table"
      >
        <!-- User Details Column -->
        <template #item.userDetails="{ item }">
          <div>
            <div class="font-weight-medium">{{ item.name }}</div>
            <div class="text-caption text-medium-emphasis">
              {{ item.username }}
            </div>
          </div>
        </template>

        <!-- Status Column -->
        <template #item.status="{ item }">
          <VChip
            :color="getUserStatusColor(item.status)"
            size="small"
            class="font-weight-medium"
          >
            {{ item.status }}
          </VChip>
        </template>

        <!-- Action Column -->
        <template #item.action="{ item }">
          <div class="d-flex gap-1">
            <VBtn icon variant="text" size="small" @click="viewUser(item)">
              <VIcon icon="tabler-eye" size="18" />
            </VBtn>
            <VBtn icon variant="text" size="small" @click="showUserMenu(item)">
              <VIcon icon="tabler-dots-vertical" size="18" />
            </VBtn>
          </div>
        </template>

        <!-- Pagination -->
        <template #bottom>
          <VCardText class="pt-2">
            <div
              class="d-flex flex-wrap justify-center justify-sm-space-between gap-y-2 mt-2"
            >
              <span class="text-body-2">Showing 1 to 7 of 50 entries</span>
              <VPagination
                v-model="usersPage"
                :total-visible="$vuetify.display.smAndDown ? 3 : 5"
                :length="Math.ceil(usersData.length / 7)"
              />
            </div>
          </VCardText>
        </template>
      </VDataTable>
    </VCard>

    <!-- All Providers Section -->
    <div class="section-title mb-4">
      <h2 class="text-h4 font-weight-bold">All Providers</h2>
    </div>

    <VCard class="mb-8">
      <VCardTitle class="d-flex align-center justify-space-between pa-6">
        <div class="d-flex align-center">
          <VTextField
            v-model="providersSearch"
            label="Search Providers"
            placeholder="Search providers..."
            prepend-inner-icon="tabler-search"
            variant="outlined"
            density="compact"
            class="me-4"
            style="width: 300px"
          />
        </div>
        <div class="d-flex gap-2">
          <VBtn
            variant="outlined"
            prepend-icon="tabler-upload"
            @click="exportProviders"
          >
            Export
          </VBtn>
          <VBtn color="warning" prepend-icon="tabler-plus" @click="addProvider">
            Add Provider
          </VBtn>
        </div>
      </VCardTitle>

      <VDataTable
        :headers="providersHeaders"
        :items="providersData"
        :search="providersSearch"
        :items-per-page="7"
        class="providers-table"
      >
        <!-- Provider Column -->
        <template #item.provider="{ item }">
          <div>
            <div class="font-weight-medium">{{ item.provider_name }}</div>
            <div class="text-caption text-medium-emphasis">
              {{
                item.provider_type === "individual"
                  ? "Individual Provider"
                  : "Company Provider"
              }}
            </div>
          </div>
        </template>

        <!-- Provider ID Column -->
        <template #item.provider_id="{ item }">
          <span class="font-weight-medium">{{
            item.id.toString().padStart(6, "0")
          }}</span>
        </template>

        <!-- Status Column -->
        <template #item.status="{ item }">
          <VMenu>
            <template #activator="{ props }">
              <VChip
                v-bind="props"
                :color="getProviderStatusColor(item.status || 'review')"
                size="small"
                class="font-weight-medium cursor-pointer"
                style="min-width: 100px; justify-content: center"
              >
                {{ getStatusTitle(item.status || "review") }}
                <VIcon icon="tabler-chevron-down" size="small" class="ml-1" />
              </VChip>
            </template>
            <VList>
              <VListItem
                v-for="choice in statusChoices"
                :key="choice.value"
                @click="changeProviderStatus(item, choice.value)"
                :class="{
                  'bg-primary-lighten-5': item.status === choice.value,
                }"
              >
                <template #prepend>
                  <VChip
                    :color="getProviderStatusColor(choice.value)"
                    size="x-small"
                  />
                </template>
                <VListItemTitle>{{ choice.title }}</VListItemTitle>
              </VListItem>
            </VList>
          </VMenu>
        </template>

        <!-- Total Listing Column -->
        <template #item.total_listings="{ item }">
          <span class="font-weight-medium">{{ item.total_listings }}</span>
        </template>

        <!-- Total Booking Column -->
        <template #item.total_bookings="{ item }">
          <span class="font-weight-medium">{{ item.total_bookings }}</span>
        </template>

        <!-- Actions Column -->
        <template #item.actions="{ item }">
          <div class="d-flex gap-1">
            <VBtn icon variant="text" size="small" @click="viewProvider(item)">
              <VIcon icon="tabler-eye" size="18" />
            </VBtn>
            <VBtn
              icon
              variant="text"
              size="small"
              @click="showProviderMenu(item)"
            >
              <VIcon icon="tabler-dots-vertical" size="18" />
            </VBtn>
          </div>
        </template>

        <!-- Pagination -->
        <template #bottom>
          <VCardText class="pt-2">
            <div
              class="d-flex flex-wrap justify-center justify-sm-space-between gap-y-2 mt-2"
            >
              <span class="text-body-2">Showing 1 to 7 of 100 entries</span>
              <VPagination
                v-model="providersPage"
                :total-visible="$vuetify.display.smAndDown ? 3 : 5"
                :length="Math.ceil(providersData.length / 7)"
              />
            </div>
          </VCardText>
        </template>
      </VDataTable>
    </VCard>

    <!-- All Users Section -->
    <div class="section-title mb-4">
      <h2 class="text-h4 font-weight-bold">All Users</h2>
    </div>

    <VCard>
      <VCardTitle class="d-flex align-center justify-space-between pa-6">
        <div class="d-flex align-center">
          <VTextField
            v-model="ordersSearch"
            label="Search Order"
            placeholder="Search orders..."
            prepend-inner-icon="tabler-search"
            variant="outlined"
            density="compact"
            class="me-4"
            style="width: 300px"
          />
        </div>
        <div class="d-flex gap-2">
          <VBtn
            variant="outlined"
            prepend-icon="tabler-upload"
            @click="exportOrders"
          >
            Export
          </VBtn>
          <VBtn color="warning" prepend-icon="tabler-plus" @click="addUser">
            Add User
          </VBtn>
        </div>
      </VCardTitle>

      <VDataTable
        :headers="ordersHeaders"
        :items="ordersData"
        :search="ordersSearch"
        :items-per-page="8"
        class="orders-table"
      >
        <!-- Users Column -->
        <template #item.users="{ item }">
          <div>
            <div class="font-weight-medium">{{ item.name }}</div>
            <div class="text-caption text-medium-emphasis">
              {{ item.email }}
            </div>
          </div>
        </template>

        <!-- Total Spent Column -->
        <template #item.totalSpent="{ item }">
          <span class="font-weight-medium"
            >€{{ formatCurrency(item.totalSpent) }}</span
          >
        </template>

        <!-- Action Column -->
        <template #item.action="{ item }">
          <div class="d-flex gap-1">
            <VBtn icon variant="text" size="small" @click="viewOrder(item)">
              <VIcon icon="tabler-eye" size="18" />
            </VBtn>
            <VBtn icon variant="text" size="small" @click="showOrderMenu(item)">
              <VIcon icon="tabler-dots-vertical" size="18" />
            </VBtn>
          </div>
        </template>

        <!-- Pagination -->
        <template #bottom>
          <VCardText class="pt-2">
            <div
              class="d-flex flex-wrap justify-center justify-sm-space-between gap-y-2 mt-2"
            >
              <span class="text-body-2">Showing 1 to 8 of 100 entries</span>
              <VPagination
                v-model="ordersPage"
                :total-visible="$vuetify.display.smAndDown ? 3 : 5"
                :length="Math.ceil(ordersData.length / 8)"
              />
            </div>
          </VCardText>
        </template>
      </VDataTable>
    </VCard>
  </div>

  <!-- Provider View Dialog -->
  <VDialog v-model="showProviderViewDialog" max-width="800" persistent>
    <VCard>
      <VCardTitle class="d-flex align-center justify-space-between">
        <span>Provider Details</span>
        <VBtn
          icon
          variant="text"
          size="small"
          @click="showProviderViewDialog = false"
        >
          <VIcon icon="tabler-x" />
        </VBtn>
      </VCardTitle>

      <VCardText v-if="selectedProvider">
        <VRow>
          <!-- Basic Information -->
          <VCol cols="12">
            <h6 class="text-h6 font-weight-medium mb-3">Basic Information</h6>
          </VCol>

          <VCol cols="12" md="6">
            <strong>Name:</strong> {{ selectedProvider.provider_name }}
          </VCol>

          <VCol cols="12" md="6">
            <strong>Type:</strong>
            <VChip
              :color="
                selectedProvider.provider_type === 'individual'
                  ? 'primary'
                  : 'secondary'
              "
              size="small"
              class="ml-2"
            >
              {{
                selectedProvider.provider_type === "individual"
                  ? "Individual"
                  : "Company"
              }}
            </VChip>
          </VCol>

          <VCol cols="12" md="6">
            <strong>Specialization:</strong>
            {{ selectedProvider.activity_specialization || "N/A" }}
          </VCol>

          <VCol cols="12" md="6">
            <strong>Country:</strong>
            {{
              selectedProvider.country_of_operation ||
              selectedProvider.country ||
              "N/A"
            }}
          </VCol>

          <VCol cols="12" md="6">
            <strong>Experience:</strong>
            {{
              selectedProvider.years_of_experience ||
              selectedProvider.business_type ||
              "N/A"
            }}
          </VCol>

          <VCol cols="12" md="6">
            <strong>Status:</strong>
            <VChip
              :color="getProviderStatusColor(selectedProvider.status)"
              size="small"
              class="ml-2"
            >
              {{ selectedProvider.status }}
            </VChip>
          </VCol>

          <VCol cols="12" md="6">
            <strong>Want to be listed:</strong>
            {{ selectedProvider.want_to_be_listed || "N/A" }}
          </VCol>

          <VCol cols="12" md="6">
            <strong>Created:</strong>
            {{ formatDate(selectedProvider.created_at) }}
          </VCol>

          <!-- Additional fields based on provider type -->
          <template v-if="selectedProvider.provider_type === 'individual'">
            <VCol cols="12">
              <h6 class="text-h6 font-weight-medium mb-3 mt-4">
                Personal Information
              </h6>
            </VCol>

            <VCol cols="12" md="6">
              <strong>Nationality:</strong>
              {{ selectedProvider.nationality || "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>Address:</strong>
              {{ selectedProvider.address1 || "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>City:</strong>
              {{ selectedProvider.city || "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>State:</strong>
              {{ selectedProvider.state || "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>Postal Code:</strong>
              {{ selectedProvider.postal_code || "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>Emergency Contact:</strong>
              {{ selectedProvider.emergency_contact_name || "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>Emergency Phone:</strong>
              {{ selectedProvider.emergency_contact_phone || "N/A" }}
            </VCol>

            <VCol cols="12">
              <strong>Short Bio:</strong>
              {{ selectedProvider.short_bio || "N/A" }}
            </VCol>
          </template>

          <template v-else>
            <VCol cols="12">
              <h6 class="text-h6 font-weight-medium mb-3 mt-4">
                Company Information
              </h6>
            </VCol>

            <VCol cols="12" md="6">
              <strong>VAT ID:</strong>
              {{ selectedProvider.vat_id || "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>Contact Person:</strong>
              {{ selectedProvider.contact_person || "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>Country of Registration:</strong>
              {{ selectedProvider.country_of_registration || "N/A" }}
            </VCol>

            <VCol cols="12" md="6">
              <strong>Business Type:</strong>
              {{ selectedProvider.business_type || "N/A" }}
            </VCol>

            <VCol cols="12">
              <strong>Short Bio:</strong>
              {{ selectedProvider.short_bio || "N/A" }}
            </VCol>
          </template>
        </VRow>
      </VCardText>

      <VCardActions>
        <VSpacer />
        <VBtn color="primary" @click="showProviderViewDialog = false">
          Close
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>

  <!-- Provider Edit Dialog -->
  <VDialog v-model="showProviderEditDialog" max-width="1200" persistent>
    <VCard>
      <VCardTitle class="d-flex align-center justify-space-between">
        <span>Edit Provider: {{ selectedProvider?.provider_name }}</span>
        <VBtn
          icon
          variant="text"
          size="small"
          @click="showProviderEditDialog = false"
        >
          <VIcon icon="tabler-x" />
        </VBtn>
      </VCardTitle>

      <VCardText v-if="selectedProvider">
        <!-- Provider Edit Wizard -->
        <ProviderEditWizard
          :provider="selectedProvider"
          @close="showProviderEditDialog = false"
          @updated="handleProviderUpdated"
        />
      </VCardText>
    </VCard>
  </VDialog>

  <!-- Listing View Dialog -->
  <VDialog v-model="showListingViewDialog" max-width="800" persistent>
    <VCard>
      <VCardTitle class="d-flex align-center justify-space-between">
        <span>Listing Details</span>
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
        <VRow>
          <!-- Basic Information -->
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

          <VCol cols="12">
            <strong>Description:</strong>
            {{ selectedListing.listing_description || "N/A" }}
          </VCol>

          <!-- Provider Information -->
          <VCol cols="12">
            <h6 class="text-h6 font-weight-medium mb-3 mt-4">
              Provider Information
            </h6>
          </VCol>

          <VCol cols="12" md="6">
            <strong>Provider Name:</strong>
            {{ selectedListing.user?.name || "N/A" }}
          </VCol>

          <VCol cols="12" md="6">
            <strong>Provider Email:</strong>
            {{ selectedListing.user?.email || "N/A" }}
          </VCol>

          <!-- Additional Details -->
          <VCol cols="12">
            <h6 class="text-h6 font-weight-medium mb-3 mt-4">
              Additional Details
            </h6>
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
            <strong>Activities Included:</strong>
            {{ selectedListing.activities_included || "N/A" }}
          </VCol>

          <VCol cols="12">
            <strong>What's Included:</strong>
            {{ selectedListing.whats_included || "N/A" }}
          </VCol>

          <VCol cols="12">
            <strong>What's Not Included:</strong>
            {{ selectedListing.whats_not_included || "N/A" }}
          </VCol>

          <VCol cols="12">
            <strong>Additional Notes:</strong>
            {{ selectedListing.additional_notes || "N/A" }}
          </VCol>

          <VCol cols="12">
            <strong>Provider's FAQ:</strong>
            {{ selectedListing.providers_faq || "N/A" }}
          </VCol>

          <VCol cols="12">
            <strong>Personal Policies:</strong>
            {{ selectedListing.personal_policies || "N/A" }}
          </VCol>

          <VCol cols="12">
            <strong>Personal Policies Text:</strong>
            {{ selectedListing.personal_policies_text || "N/A" }}
          </VCol>

          <VCol cols="12">
            <strong>Terms Accepted:</strong>
            <VChip
              :color="selectedListing.terms_accepted ? 'success' : 'error'"
              size="small"
              class="ml-2"
            >
              {{ selectedListing.terms_accepted ? "Yes" : "No" }}
            </VChip>
          </VCol>
        </VRow>
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
import ProviderEditWizard from "@/components/admin/ProviderEditWizard.vue";
import { $api } from "@/utils/api";

definePage({
  meta: {
    layout: "default",
    requiresAuth: true,
    requiresAdmin: true,
  },
});

const router = useRouter();
const loading = ref(false);
const stats = ref({});
const userData = ref(null);

// Search queries
const eventsSearch = ref("");
const usersSearch = ref("");
const ordersSearch = ref("");
const providersSearch = ref("");

// Pagination
const eventsPage = ref(3);
const usersPage = ref(3);
const ordersPage = ref(3);
const providersPage = ref(3);

// Dialogs
const showProviderViewDialog = ref(false);
const showProviderEditDialog = ref(false);
const selectedProvider = ref(null);
const showListingViewDialog = ref(false);
const showListingEditDialog = ref(false);
const selectedListing = ref(null);

// Get user data from cookies
const userDataCookie = useCookie("userData");
if (userDataCookie.value) {
  userData.value = userDataCookie.value;
}

// Events data - will be loaded from API
const eventsData = ref([]);

// Users data - exact data from screenshot
const usersData = ref([
  {
    id: 1,
    name: "UserName",
    username: "User ID",
    advId: "647838",
    accountType: "Platform Access",
    billingType: "Bank wire",
    status: "Booked",
  },
  {
    id: 2,
    name: "Richard Payne",
    username: "richard247",
    advId: "647838",
    accountType: "Subscription",
    billingType: "Auto Debit",
    status: "Booked",
  },
  {
    id: 3,
    name: "Jennifer Summers",
    username: "summers.45",
    advId: "647838",
    accountType: "Platform Access",
    billingType: "Auto Debit",
    status: "Booked",
  },
  {
    id: 4,
    name: "Mr. Justin Richardson",
    username: "jr.3734",
    advId: "647838",
    accountType: "Subscription",
    billingType: "Manual Paypal",
    status: "Booked",
  },
  {
    id: 5,
    name: "Nicholas Tanner",
    username: "nicholas.t",
    advId: "647838",
    accountType: "Platform Access",
    billingType: "Bank Wire",
    status: "Pending",
  },
  {
    id: 6,
    name: "Mary Garcia",
    username: "mary.garcia",
    advId: "647838",
    accountType: "Subscription",
    billingType: "Auto Debit",
    status: "Failed",
  },
  {
    id: 7,
    name: "Joseph Oliver",
    username: "joseph.87",
    advId: "647838",
    accountType: "Platform Access",
    billingType: "Manual Cash",
    status: "Booked",
  },
]);

// Orders data - exact data from screenshot
const ordersData = ref([
  {
    id: 1,
    name: "Jordan Stevenson",
    email: "dayna19@yahoo.com",
    userId: "#345889",
    country: "United States",
    bookings: 41,
    totalSpent: 32907,
  },
  {
    id: 2,
    name: "Benedetto Rossiter",
    email: "lorena@yahoo.com",
    userId: "#234875",
    country: "Brazil",
    bookings: 12,
    totalSpent: 5347,
  },
  {
    id: 3,
    name: "Bentlee Emblin",
    email: "bemblinf@gmail.com",
    userId: "#783645",
    country: "India",
    bookings: 36,
    totalSpent: 12458,
  },
  {
    id: 4,
    name: "Bertha Biner",
    email: "bbinerh@gmail.com",
    userId: "#234876",
    country: "Australia",
    bookings: 7,
    totalSpent: 1345,
  },
  {
    id: 5,
    name: "Beverlie Krabbe",
    email: "bkrabbeld@gmail.com",
    userId: "#234598",
    country: "France",
    bookings: 34,
    totalSpent: 24435,
  },
  {
    id: 6,
    name: "Bradan Rosebotham",
    email: "brosebothamz@gmail.com",
    userId: "#345978",
    country: "China",
    bookings: 67,
    totalSpent: 72345,
  },
  {
    id: 7,
    name: "Bree Kilday",
    email: "bkildayr@gmail.com",
    userId: "#987623",
    country: "United States",
    bookings: 28,
    totalSpent: 18458,
  },
  {
    id: 8,
    name: "Breena Gallemore",
    email: "bgallemore6@gmail.com",
    userId: "#873645",
    country: "India",
    bookings: 127,
    totalSpent: 142349,
  },
]);

// Providers data - will be loaded from API
const providersData = ref([]);

// Status choices for provider status combobox
const statusChoices = [
  { title: "Review", value: "review" },
  { title: "Active", value: "active" },
  { title: "Rejected", value: "rejected" },
];

// Debug: Log status choices
console.log("Status choices defined:", statusChoices);

// Table headers
const eventsHeaders = [
  { title: "EVENTS", key: "events", sortable: false },
  { title: "PROVIDER", key: "provider", sortable: false },
  { title: "TYPE", key: "listing_type", sortable: false },
  { title: "PRICE", key: "price", sortable: false },
  { title: "CAPACITY", key: "capacity", sortable: false },
  { title: "STATUS", key: "status", sortable: false },
  { title: "ACTIONS", key: "actions", sortable: false, width: "120px" },
];

const usersHeaders = [
  { title: "USER DETAILS", key: "userDetails", sortable: false },
  { title: "ADV ID", key: "advId", sortable: false },
  { title: "ACCOUNT TYPE", key: "accountType", sortable: false },
  { title: "BILLING TYPE", key: "billingType", sortable: false },
  { title: "STATUS", key: "status", sortable: false },
  { title: "ACTION", key: "action", sortable: false, width: "120px" },
];

const ordersHeaders = [
  { title: "USERS", key: "users", sortable: false },
  { title: "USER ID", key: "userId", sortable: false },
  { title: "COUNTRY", key: "country", sortable: false },
  { title: "BOOKINGS", key: "bookings", sortable: false },
  { title: "TOTAL SPENT", key: "totalSpent", sortable: false },
  { title: "ACTION", key: "action", sortable: false, width: "120px" },
];

const providersHeaders = [
  { title: "PROVIDER", key: "provider", sortable: false },
  { title: "PROVIDER ID", key: "provider_id", sortable: false },
  { title: "TOTAL LISTING", key: "total_listings", sortable: false },
  { title: "TOTAL BOOKING", key: "total_bookings", sortable: false },
  { title: "STATUS", key: "status", sortable: false },
  { title: "ACTIONS", key: "actions", sortable: false, width: "120px" },
];

// Additional protection - redirect non-admin users
onMounted(() => {
  if (!userData.value || userData.value.role !== "admin") {
    console.log("Non-admin user detected, redirecting...");
    router.push("/");
    return;
  }
  loadDashboardData();
});

const loadDashboardData = async () => {
  loading.value = true;
  try {
    console.log("Loading dashboard data...");

    const response = await $api("/admin/dashboard", {
      method: "GET",
    });
    console.log("Dashboard response:", response);
    stats.value = response;

    // Load providers data
    console.log("Loading providers data...");
    try {
      const providersResponse = await $api("/admin/providers", {
        method: "GET",
      });
      console.log("Providers response:", providersResponse);
      providersData.value = providersResponse.data || [];
      console.log("Providers data set:", providersData.value);

      // Debug: Log each provider's status
      providersData.value.forEach((provider, index) => {
        console.log(`Provider ${index}:`, {
          id: provider.id,
          name: provider.provider_name,
          status: provider.status,
          want_to_be_listed: provider.want_to_be_listed,
        });
      });

      // Force reactivity by ensuring all providers have a status
      providersData.value = providersData.value.map((provider) => {
        if (!provider.status) {
          provider.status = "review"; // Default status changed to review
        }
        return provider;
      });
    } catch (error) {
      console.error("Error loading providers:", error);
      providersData.value = [];
    }

    // Load listings data directly from admin endpoint
    console.log("Loading listings data...");
    try {
      const listingsResponse = await $api("/admin/listings", {
        method: "GET",
      });
      console.log("Admin listings response:", listingsResponse);
      eventsData.value = listingsResponse.data || [];

      // Auto-determine status for each event
      eventsData.value = eventsData.value.map((event) => {
        if (!event.status) {
          event.status = determineEventStatus(event);
        }
        return event;
      });

      console.log("Admin listings data set:", eventsData.value);
    } catch (listingsError) {
      console.error("Failed to load listings:", listingsError);
      eventsData.value = [];
    }
  } catch (error) {
    console.error("Error loading dashboard data:", error);
    console.error("Error details:", {
      message: error.message,
      status: error.response?.status,
      data: error.response?.data,
    });

    // Show fallback data instead of redirecting
    stats.value = {
      all_providers: 211,
      total_bookings: 345,
      total_explorer_passports: 840,
      total_revenue: 590000,
    };

    // Only redirect if it's an authentication error
    if (error.response?.status === 401 || error.response?.status === 403) {
      console.log("Authentication error, redirecting to login");
      router.push("/login");
    }
  } finally {
    loading.value = false;
  }
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat("en-US", {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount);
};

const formatDate = (dateString) => {
  if (!dateString) return "N/A";
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    month: "numeric",
    day: "numeric",
    hour: "numeric",
    minute: "numeric",
  });
};

const getStatusColor = (status) => {
  const colors = {
    Submitted: "warning",
    "Other Events": "secondary",
    Denied: "error",
    "Edit Review Pending": "warning",
    Approved: "info",
    Live: "success",
  };
  return colors[status] || "secondary";
};

const getUserStatusColor = (status) => {
  const colors = {
    Booked: "success",
    Pending: "warning",
    Failed: "error",
  };
  return colors[status] || "secondary";
};

// Action functions
const exportEvents = () => {
  console.log("Exporting events...");
};

const addEvent = () => {
  console.log("Adding event...");
};

const viewEvent = (item) => {
  console.log("Viewing event:", item);
  selectedListing.value = item;
  showListingViewDialog.value = true;
};

const editEvent = (item) => {
  console.log("Editing event:", item);
  selectedListing.value = item;
  showListingEditDialog.value = true;
};

const exportUsers = () => {
  console.log("Exporting users...");
};

const addBooking = () => {
  console.log("Adding booking...");
};

const viewUser = (item) => {
  console.log("Viewing user:", item);
};

const showUserMenu = (item) => {
  console.log("Showing user menu:", item);
};

const exportOrders = () => {
  console.log("Exporting orders...");
};

const addUser = () => {
  console.log("Adding user...");
};

const viewOrder = (item) => {
  console.log("Viewing order:", item);
};

const showOrderMenu = (item) => {
  console.log("Showing order menu:", item);
};

// Provider action functions
const exportProviders = () => {
  console.log("Exporting providers...");
};

const addProvider = () => {
  console.log("Adding provider...");
};

const viewProvider = (item) => {
  console.log("Viewing provider:", item);
  selectedProvider.value = item;
  showProviderViewDialog.value = true;
};

const showProviderMenu = (item) => {
  console.log("Showing provider menu:", item);
  selectedProvider.value = item;
  showProviderEditDialog.value = true;
};

const changeProviderStatus = async (provider, status) => {
  try {
    console.log("Changing provider status:", {
      providerId: provider.id,
      providerType: provider.provider_type,
      currentStatus: provider.status,
      newStatus: status,
    });

    // Show loading state
    console.log("Status change initiated for provider:", provider.id);

    // Validate status
    if (!status || !["active", "review", "rejected"].includes(status)) {
      console.error("Invalid status:", status);
      return;
    }

    const response = await $api(
      `/admin/providers/${provider.id}/${provider.provider_type}/status`,
      {
        method: "PUT",
        body: { status },
      }
    );

    console.log("Status change response:", response);

    if (response?.success || response?.message) {
      // Update the provider status in the local array
      const providerIndex = providersData.value.findIndex(
        (p) => p.id === provider.id
      );
      if (providerIndex !== -1) {
        providersData.value[providerIndex].status = status;
        providersData.value[providerIndex].want_to_be_listed =
          response.provider?.want_to_be_listed || provider.want_to_be_listed;
      }

      // Also update the provider object passed to the function
      provider.status = status;
      provider.want_to_be_listed =
        response.provider?.want_to_be_listed || provider.want_to_be_listed;

      // If this is the current user's provider, update their localStorage
      const userDataCookie = useCookie("userData");
      const userData = userDataCookie.value;
      if (userData && provider.user_id === userData.id) {
        localStorage.setItem("providerStatus", status);
        localStorage.setItem("providerType", provider.provider_type);
        localStorage.setItem("providerId", provider.id);

        console.log(
          "Updated current user's provider status in localStorage:",
          status
        );

        // Trigger a custom event to notify other components
        window.dispatchEvent(
          new CustomEvent("providerStatusChanged", {
            detail: {
              status,
              providerType: provider.provider_type,
              providerId: provider.id,
            },
          })
        );
      }

      console.log("Provider status updated successfully to:", status);

      // Show success message (optional)
      // You can add a toast notification here if needed
    } else {
      console.error("Status update failed:", response);
    }
  } catch (error) {
    console.error("Error updating provider status:", error);
    // You can add error toast notification here if needed
  }
};

const getProviderStatusColor = (status) => {
  const colors = {
    active: "success",
    review: "warning",
    rejected: "error",
    Live: "success",
    Denied: "error",
  };
  return colors[status] || "secondary";
};

const getStatusTitle = (status) => {
  const titles = {
    active: "Active",
    review: "Review",
    rejected: "Rejected",
  };
  const result = titles[status] || "Review";
  console.log(`Status title for "${status}": "${result}"`);
  return result;
};

const getListingStatusColor = (status) => {
  const colors = {
    draft: "warning",
    published: "success",
    archived: "error",
  };
  return colors[status] || "secondary";
};

// Event status management functions
const getEventStatusColor = (status) => {
  const colors = {
    submitted: "warning", // Yellow - default when created
    approved: "primary", // Blue - admin approved
    live: "success", // Green - admin set to live
    denied: "error", // Red - admin denied
    adr_event: "grey", // Grey background, dark text - created via Add Event
    inactive: "grey-lighten-2", // Grey background, dark text - when finish date passed
    review_edit: "warning", // Orange - when user edits
  };
  return colors[status] || "warning";
};

const getEventStatusTitle = (status) => {
  const titles = {
    submitted: "Submitted",
    approved: "Approved",
    live: "Live",
    denied: "Denied",
    adr_event: "Adr Event",
    inactive: "Inactive",
    review_edit: "Review Edit",
  };
  return titles[status] || "Submitted";
};

// Event status choices for admin
const eventStatusChoices = [
  { title: "Approved", value: "approved" },
  { title: "Live", value: "live" },
  { title: "Denied", value: "denied" },
];

// Change event status function is defined below with API integration

// Auto-determine event status based on creation method and dates
const determineEventStatus = (event) => {
  // If created via Add Event wizard (not full listing wizard)
  if (event.created_via === "add_event_wizard") {
    return "adr_event";
  }

  // If finish date has passed
  if (event.finishing_date && new Date(event.finishing_date) < new Date()) {
    return "inactive";
  }

  // If user has edited the event
  if (event.last_edited_by_user) {
    return "review_edit";
  }

  // Default status when created via full listing wizard
  return "submitted";
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

// Event status (for All Events table) — shared map with listings page
const allEventStatusChoices = [
  { title: "Submitted", value: "submitted" },
  { title: "Approved", value: "approved" },
  { title: "Live", value: "live" },
  { title: "Denied", value: "denied" },
  { title: "Edit Review", value: "edit_review" },
  { title: "Other Events", value: "other_events" },
  { title: "Inactive", value: "inactive" },
];

// This function is already defined above with more comprehensive status handling

const changeEventStatus = async (eventItem, status) => {
  try {
    if (!status) return;
    const res = await $api(`/admin/listings/${eventItem.id}`, {
      method: "PUT",
      body: { status },
    });
    if (res?.success) {
      eventItem.status = status;
    } else {
      console.error("Failed to update event status", res);
    }
  } catch (e) {
    console.error("Error updating event status", e);
  }
};

const handleProviderUpdated = () => {
  console.log("Provider updated, reloading data...");
  loadDashboardData();
  showProviderEditDialog.value = false;
};

const handleListingUpdated = () => {
  console.log("Listing updated, reloading data...");
  loadDashboardData();
  showListingEditDialog.value = false;
};
</script>

<style lang="scss" scoped>
.admin-dashboard {
  padding: 24px;
  min-height: 100vh;
  font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

.admin-panel-title {
  h1 {
    color: #2c3e50;
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    font-family: "Anton", "Inter", "Segoe UI", Tahoma, Geneva, Verdana,
      sans-serif;
  }
}

.section-title {
  h2 {
    color: #2c3e50;
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0;
    font-family: "Anton", "Inter", "Segoe UI", Tahoma, Geneva, Verdana,
      sans-serif;
  }
}

.kpi-container {
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  background-color: #ffffff;
  overflow: hidden;

  .kpi-section {
    position: relative;
    padding: 24px;

    &:not(:last-child)::after {
      content: "";
      position: absolute;
      top: 20%;
      right: 0;
      bottom: 20%;
      width: 1px;
      background-color: #e9ecef;
    }

    .kpi-content-wrapper {
      display: flex;
      align-items: center;
      height: 100%;
    }

    .kpi-header {
      display: flex;
      align-items: center;
      margin-bottom: 8px;
    }

    .kpi-icon-wrapper {
      width: 40px;
      height: 40px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 16px;
      background-color: #f8f9fa;
      border: 1px solid #e9ecef;
    }

    .kpi-content {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      height: 100%;

      .kpi-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 4px 0;
        color: #2c3e50;
        font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      }

      .kpi-title {
        font-size: 0.875rem;
        color: #6c757d;
        margin: 0;
        font-weight: 500;
        font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      }

      .kpi-trend {
        font-size: 0.75rem;
        margin: 0;
        font-weight: 500;
        font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;

        &.positive {
          color: #28c76f;
        }

        &:not(.positive) {
          color: #6c757d;
        }
      }
    }
  }
}

.events-table,
.users-table,
.orders-table {
  .v-data-table {
    border-radius: 8px;
    overflow: hidden;
  }

  .v-data-table-header {
    background-color: #f8f9fa;
  }

  .v-data-table-header th {
    font-weight: 600;
    color: #495057;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  }
}

.v-card {
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  background-color: #ffffff;
}

.v-chip {
  font-weight: 500;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

.v-btn {
  border-radius: 6px;
  text-transform: none;
  font-weight: 500;
  font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

.v-text-field {
  .v-field {
    border-radius: 6px;
  }
}

.v-pagination {
  .v-btn {
    border-radius: 4px;
  }
}

// Global font family for all text elements
* {
  font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

// Custom event status styles
.v-chip {
  &.v-chip--color-grey {
    background-color: #6c757d !important;
    color: white !important;
  }

  &.v-chip--color-grey-lighten-2 {
    background-color: #e9ecef !important;
    color: #495057 !important;
  }
}
</style>
