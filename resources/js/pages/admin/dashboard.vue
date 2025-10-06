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
              {{
                item.listing_title ||
                item.eventTitle ||
                item.title ||
                `Event ${item.id}`
              }}
            </div>
            <div class="text-caption text-medium-emphasis">
              {{
                item.locations || item.location || item.address || "No location"
              }}
            </div>
          </div>
        </template>

        <!-- Provider Column -->
        <template #item.provider="{ item }">
          <div>
            <div class="font-weight-medium">{{ item.user?.name || "Unknown" }}</div>
            <div class="text-caption text-medium-emphasis">
              {{ item.user?.email || "No email provided" }}
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

    <!-- Quick Add Listing Dialog -->
    <VDialog v-model="showQuickAddListing" max-width="520">
      <VCard>
        <VCardTitle class="d-flex align-center justify-space-between">
          <span>Quick Add Event/Listing</span>
          <VBtn
            icon
            variant="text"
            size="small"
            @click="showQuickAddListing = false"
          >
            <VIcon icon="tabler-x" />
          </VBtn>
        </VCardTitle>
        <VCardText>
          <VForm ref="quickListingFormRef" @submit.prevent="submitQuickListing">
            <VTextField
              v-model="quickListingForm.listing_title"
              label="Title"
              required
              variant="outlined"
              class="mb-3"
            />
            <VSelect
              v-model="quickListingForm.listing_type"
              :items="[
                { title: 'Single Date', value: 'single-date' },
                { title: 'Multi Date', value: 'multi-date' },
                { title: 'Open Date', value: 'open-date' },
              ]"
              label="Type"
              required
              variant="outlined"
              class="mb-3"
            />
            <VTextField
              v-model="quickListingForm.locations"
              label="Location"
              variant="outlined"
              class="mb-3"
            />
            <VRow>
              <VCol cols="12" md="6">
                <VTextField
                  v-model="quickListingForm.price"
                  label="Price"
                  type="number"
                  variant="outlined"
                  class="mb-3"
                />
              </VCol>
              <VCol cols="6" md="3">
                <VTextField
                  v-model="quickListingForm.min_capacity"
                  label="Min"
                  type="number"
                  variant="outlined"
                  class="mb-3"
                />
              </VCol>
              <VCol cols="6" md="3">
                <VTextField
                  v-model="quickListingForm.max_capacity"
                  label="Max"
                  type="number"
                  variant="outlined"
                  class="mb-3"
                />
              </VCol>
            </VRow>
            <div class="d-flex justify-end">
              <VBtn color="primary" type="submit" :loading="addingListing"
                >Create</VBtn
              >
            </div>
          </VForm>
        </VCardText>
      </VCard>
    </VDialog>

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
          <VBtn
            color="warning"
            prepend-icon="tabler-plus"
            @click="openAddProvider"
          >
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
            <VBtn icon variant="text" size="small" @click="openViewUser(item)">
              <VIcon icon="tabler-eye" size="18" />
            </VBtn>
            <VBtn
              icon
              variant="text"
              size="small"
              @click="openEditUserFromOrders(item)"
            >
              <VIcon icon="tabler-edit" size="18" />
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

    <!-- Add User Dialog -->
    <VDialog v-model="showAddUser" max-width="520">
      <VCard>
        <VCardTitle class="d-flex align-center justify-space-between">
          <span>Add User</span>
          <VBtn icon variant="text" size="small" @click="showAddUser = false">
            <VIcon icon="tabler-x" />
          </VBtn>
        </VCardTitle>
        <VCardText>
          <VForm ref="addUserFormRef" @submit.prevent="submitAddUser">
            <VTextField
              v-model="addUserForm.name"
              label="Name"
              required
              variant="outlined"
              class="mb-3"
            />
            <VTextField
              v-model="addUserForm.email"
              label="Email"
              type="email"
              required
              variant="outlined"
              class="mb-3"
            />
            <VSelect
              v-model="addUserForm.role"
              :items="[
                { title: 'User', value: 'user' },
                { title: 'Admin', value: 'admin' },
              ]"
              label="Role"
              required
              variant="outlined"
              class="mb-3"
            />
            <VTextField
              v-model="addUserForm.password"
              label="Password (optional)"
              type="password"
              variant="outlined"
              class="mb-3"
            />
            <div class="d-flex justify-end">
              <VBtn color="primary" type="submit" :loading="addingUser"
                >Create</VBtn
              >
            </div>
          </VForm>
        </VCardText>
      </VCard>
    </VDialog>

    <!-- Add Provider Dialog -->
    <VDialog v-model="showAddProvider" max-width="520">
      <VCard>
        <VCardTitle class="d-flex align-center justify-space-between">
          <span>Add Provider</span>
          <VBtn
            icon
            variant="text"
            size="small"
            @click="showAddProvider = false"
          >
            <VIcon icon="tabler-x" />
          </VBtn>
        </VCardTitle>
        <VCardText>
          <VForm ref="addProviderFormRef" @submit.prevent="submitAddProvider">
            <VSelect
              v-model="addProviderForm.type"
              :items="[
                { title: 'Individual', value: 'individual' },
                { title: 'Company', value: 'company' },
              ]"
              label="Provider Type"
              required
              variant="outlined"
              class="mb-3"
            />
            <VTextField
              v-model="addProviderForm.name"
              label="Name"
              required
              variant="outlined"
              class="mb-3"
            />
            <VTextField
              v-model="addProviderForm.email"
              label="Email"
              type="email"
              required
              variant="outlined"
              class="mb-3"
            />
            <VTextField
              v-model="addProviderForm.password"
              label="Password (optional)"
              type="password"
              variant="outlined"
              class="mb-3"
            />
            <div class="d-flex justify-end">
              <VBtn color="primary" type="submit" :loading="addingProvider"
                >Create</VBtn
              >
            </div>
          </VForm>
        </VCardText>
      </VCard>
    </VDialog>
  </div>

  <!-- Provider View Dialog -->
  <VDialog v-model="showProviderViewDialog" max-width="1200" persistent>
    <VCard>
      <VCardTitle class="d-flex align-center justify-space-between">
        <span
          >Provider Details -
          {{ selectedProvider?.provider_name || "Provider" }}</span
        >
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
        <VTabs v-model="activeProviderTab" class="mb-4">
          <VTab value="basic">Basic Information</VTab>
          <VTab value="personal">Personal Details</VTab>
          <VTab value="business">Business Information</VTab>
          <VTab value="contact">Contact & Location</VTab>
          <VTab value="social">Social Media</VTab>
          <VTab value="terms">Terms & Conditions</VTab>
        </VTabs>

        <VTabsWindow v-model="activeProviderTab">
          <!-- Basic Information Tab -->
          <VTabsWindowItem value="basic">
            <VRow>
              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3">
                  Basic Information
                </h6>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Name:</strong>
                {{ selectedProvider.provider_name || "Not specified" }}
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
                <strong>Status:</strong>
                <VChip
                  :color="getProviderStatusColor(selectedProvider.status)"
                  size="small"
                  class="ml-2 text-capitalize"
                >
                  {{ getStatusTitle(selectedProvider.status) }}
                </VChip>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Email:</strong> {{ selectedProvider.email || "Not specified" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Phone:</strong> {{ selectedProvider.phone || "Not specified" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Created:</strong>
                {{ formatDate(selectedProvider.created_at) }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Updated:</strong>
                {{ formatDate(selectedProvider.updated_at) }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Want to be listed:</strong>
                <VChip
                  :color="
                    selectedProvider.want_to_be_listed ? 'success' : 'warning'
                  "
                  size="small"
                  class="ml-2"
                >
                  {{ selectedProvider.want_to_be_listed ? "Yes" : "No" }}
                </VChip>
              </VCol>
            </VRow>
          </VTabsWindowItem>

          <!-- Personal Details Tab -->
          <VTabsWindowItem value="personal">
            <VRow>
              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3">
                  Personal Information
                </h6>
              </VCol>

              <VCol cols="12" md="6">
                <strong>First Name:</strong>
                {{ selectedProvider.first_name || "Not specified" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Last Name:</strong>
                {{ selectedProvider.last_name || "Not specified" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Date of Birth:</strong>
                {{
                  selectedProvider.dob
                    ? formatDate(selectedProvider.dob)
                    : "Not specified"
                }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Nationality:</strong>
                {{ selectedProvider.nationality || "Not specified" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Gender:</strong> {{ selectedProvider.gender || "Not specified" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Languages:</strong>
                {{
                  Array.isArray(selectedProvider.languages)
                    ? selectedProvider.languages.join(", ")
                    : selectedProvider.languages || "Not specified"
                }}
              </VCol>

              <VCol cols="12" v-if="selectedProvider.short_bio">
                <strong>Bio:</strong>
                <div class="mt-1">{{ selectedProvider.short_bio }}</div>
              </VCol>
            </VRow>
          </VTabsWindowItem>

          <!-- Business Information Tab -->
          <VTabsWindowItem value="business">
            <VRow>
              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3">
                  Business Information
                </h6>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Business Type:</strong>
                {{ selectedProvider.business_type || "Not specified" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Years of Experience:</strong>
                {{ selectedProvider.years_of_experience || "Not specified" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Activity Specialization:</strong>
                {{ selectedProvider.activity_specialization || "Not specified" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Country of Operation:</strong>
                {{ selectedProvider.country_of_operation || "Not specified" }}
              </VCol>

              <VCol
                cols="12"
                md="6"
                v-if="selectedProvider.provider_type === 'company'"
              >
                <strong>VAT ID:</strong> {{ selectedProvider.vat_id || "Not specified" }}
              </VCol>

              <VCol
                cols="12"
                md="6"
                v-if="selectedProvider.provider_type === 'company'"
              >
                <strong>Company Website:</strong>
                {{ selectedProvider.company_website || "Not specified" }}
              </VCol>

              <VCol
                cols="12"
                md="6"
                v-if="selectedProvider.provider_type === 'company'"
              >
                <strong>Country of Registration:</strong>
                {{ selectedProvider.country_of_registration || "Not specified" }}
              </VCol>

              <VCol
                cols="12"
                md="6"
                v-if="selectedProvider.provider_type === 'company'"
              >
                <strong>Contact Person:</strong>
                {{ selectedProvider.contact_person || "Not specified" }}
              </VCol>

              <VCol cols="12" v-if="selectedProvider.business_description">
                <strong>Business Description:</strong>
                <div class="mt-1">
                  {{ selectedProvider.business_description }}
                </div>
              </VCol>
            </VRow>
          </VTabsWindowItem>

          <!-- Contact & Location Tab -->
          <VTabsWindowItem value="contact">
            <VRow>
              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3">
                  Contact & Location
                </h6>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Address Line 1:</strong>
                {{ selectedProvider.address1 || "Not specified" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Address Line 2:</strong>
                {{ selectedProvider.address2 || "Not specified" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>City:</strong> {{ selectedProvider.city || "Not specified" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>State/Province:</strong>
                {{ selectedProvider.state || "Not specified" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Postal Code:</strong>
                {{ selectedProvider.postal_code || "Not specified" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Country:</strong>
                {{ selectedProvider.country || "Not specified" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Phone:</strong> {{ selectedProvider.phone || "Not specified" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Mobile:</strong> {{ selectedProvider.mobile || "Not specified" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Email:</strong> {{ selectedProvider.email || "Not specified" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Alternative Email:</strong>
                {{ selectedProvider.alternative_email || "Not specified" }}
              </VCol>
            </VRow>
          </VTabsWindowItem>

          <!-- Social Media Tab -->
          <VTabsWindowItem value="social">
            <VRow>
              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3">
                  Social Media Links
                </h6>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Website:</strong>
                {{ selectedProvider.website || "Not specified" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Facebook:</strong>
                {{
                  selectedProvider.social_media_links?.facebook ||
                  selectedProvider.facebook ||
                  "Not specified"
                }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Twitter:</strong>
                {{
                  selectedProvider.social_media_links?.twitter ||
                  selectedProvider.twitter ||
                  "Not specified"
                }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>LinkedIn:</strong>
                {{
                  selectedProvider.social_media_links?.linkedIn ||
                  selectedProvider.linkedIn ||
                  "Not specified"
                }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Instagram:</strong>
                {{
                  selectedProvider.social_media_links?.instagram ||
                  selectedProvider.instagram ||
                  "Not specified"
                }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>YouTube:</strong>
                {{
                  selectedProvider.social_media_links?.youtube ||
                  selectedProvider.youtube ||
                  "Not specified"
                }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>TikTok:</strong>
                {{
                  selectedProvider.social_media_links?.tiktok ||
                  selectedProvider.tiktok ||
                  "Not specified"
                }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>WhatsApp:</strong>
                {{
                  selectedProvider.social_media_links?.whatsapp ||
                  selectedProvider.whatsapp ||
                  "Not specified"
                }}
              </VCol>
            </VRow>
          </VTabsWindowItem>

          <!-- Terms & Conditions Tab -->
          <VTabsWindowItem value="terms">
            <VRow>
              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3">
                  Terms & Conditions
                </h6>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Terms Accepted:</strong>
                <VChip
                  :color="selectedProvider.terms_accepted ? 'success' : 'error'"
                  size="small"
                  class="ml-2"
                >
                  {{ selectedProvider.terms_accepted ? "Yes" : "No" }}
                </VChip>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Terms Accepted Date:</strong>
                {{
                  selectedProvider.terms_accepted_date
                    ? formatDate(selectedProvider.terms_accepted_date)
                    : "Not specified"
                }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Privacy Policy Accepted:</strong>
                <VChip
                  :color="
                    selectedProvider.privacy_policy_accepted
                      ? 'success'
                      : 'error'
                  "
                  size="small"
                  class="ml-2"
                >
                  {{ selectedProvider.privacy_policy_accepted ? "Yes" : "No" }}
                </VChip>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Marketing Consent:</strong>
                <VChip
                  :color="
                    selectedProvider.marketing_consent ? 'success' : 'warning'
                  "
                  size="small"
                  class="ml-2"
                >
                  {{ selectedProvider.marketing_consent ? "Yes" : "No" }}
                </VChip>
              </VCol>

              <VCol cols="12" v-if="selectedProvider.terms_conditions">
                <strong>Terms & Conditions Text:</strong>
                <div class="mt-1">{{ selectedProvider.terms_conditions }}</div>
              </VCol>

              <VCol cols="12" v-if="selectedProvider.privacy_policy">
                <strong>Privacy Policy Text:</strong>
                <div class="mt-1">{{ selectedProvider.privacy_policy }}</div>
              </VCol>
            </VRow>
          </VTabsWindowItem>
        </VTabsWindow>
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
  <VDialog v-model="showListingViewDialog" max-width="1200" persistent>
    <VCard>
      <VCardTitle class="d-flex align-center justify-space-between">
        <span
          >Listing Details -
          {{ selectedListing?.listing_title || "Event" }}</span
        >
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
        <VTabs v-model="activeListingTab" class="mb-4">
          <VTab value="basic">Basic Information</VTab>
          <VTab value="itinerary">Itinerary Details</VTab>
          <VTab value="addons">Special Addons</VTab>
          <VTab value="packages">Packages</VTab>
          <VTab value="periods" v-if="selectedListing?.periods?.length">
            Availability Periods
          </VTab>
          <VTab value="additional">Additional Info</VTab>
        </VTabs>

        <VTabsWindow v-model="activeListingTab">
          <!-- Basic Information Tab -->
          <VTabsWindowItem value="basic">
            <VRow>
              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3">
                  Basic Information
                </h6>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Title:</strong>
                {{ selectedListing.listing_title || "Not specified" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Subtitle:</strong>
                {{
                  selectedListing.subtitle ||
                  selectedListing.listing_subtitle ||
                  "Not specified"
                }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Type:</strong>
                <VChip
                  :color="getListingTypeColor(selectedListing.listing_type)"
                  size="small"
                  class="ml-2"
                >
                  {{ formatListingType(selectedListing.listing_type) || 'Open Date' }}
                </VChip>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Status:</strong>
                <VChip
                  :color="getEventStatusColor(selectedListing.status)"
                  size="small"
                  class="ml-2 text-capitalize"
                >
                  {{ getEventStatusTitle(selectedListing.status) }}
                </VChip>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Price:</strong> €{{
                  formatCurrency(selectedListing.price || 0)
                }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Currency:</strong>
                {{ selectedListing.currency || "EUR" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Min Capacity:</strong>
                {{ selectedListing.min_capacity || 0 }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Max Capacity:</strong>
                {{ selectedListing.max_capacity || 0 }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Provider:</strong>
                {{ selectedListing.user?.name || "Not specified" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Provider Email:</strong>
                {{ selectedListing.user?.email || "Not specified" }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Location:</strong>
                {{
                  formatDisplayValue(
                    selectedListing.locations ||
                      selectedListing.location ||
                      selectedListing.address
                  ) || "Not specified"
                }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Country:</strong>
                {{
                  selectedListing.country ||
                  ensureArray(selectedListing.locations)[
                    ensureArray(selectedListing.locations).length - 1
                  ] ||
                  "Not specified"
                }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Starting Date:</strong>
                {{
                  selectedListing.starting_date || selectedListing.start_date || selectedListing.startDate
                    ? formatDate(selectedListing.starting_date || selectedListing.start_date || selectedListing.startDate)
                    : "Not specified"
                }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Finishing Date:</strong>
                {{
                  selectedListing.finishing_date || selectedListing.end_date || selectedListing.endDate
                    ? formatDate(selectedListing.finishing_date || selectedListing.end_date || selectedListing.endDate)
                    : "Not specified"
                }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Created:</strong>
                {{ formatDate(selectedListing.created_at) }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Updated:</strong>
                {{ formatDate(selectedListing.updated_at) }}
              </VCol>

              <VCol cols="12" v-if="selectedListing.listing_description">
                <strong>Description:</strong>
                <div class="mt-1">
                  {{ selectedListing.listing_description }}
                </div>
              </VCol>
            </VRow>
          </VTabsWindowItem>

          <!-- Itinerary Details Tab -->
          <VTabsWindowItem value="itinerary">
            <VRow>
              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3">
                  Itinerary Details
                </h6>
              </VCol>

              <VCol
                cols="12"
                v-if="
                  selectedListing.itineraries &&
                  selectedListing.itineraries.length > 0
                "
              >
                <div
                  v-for="(itinerary, index) in selectedListing.itineraries"
                  :key="index"
                  class="mb-4 pa-4"
                  style="border: 1px solid #e0e0e0; border-radius: 8px"
                >
                  <div class="d-flex align-center mb-3">
                    <VIcon
                      icon="tabler-calendar"
                      size="20"
                      color="#ec8d22"
                      class="me-2"
                    />
                    <strong>
                      Day {{ index + 1 }}:
                      {{
                        itinerary.day_title ||
                        itinerary.title ||
                        `Day ${index + 1}`
                      }}
                    </strong>
                  </div>
                  <VRow>
                    <VCol cols="12" md="6">
                      <strong>Day Number:</strong>
                      {{ itinerary.day_number || (index + 1) }}
                    </VCol>
                    <VCol cols="12" md="6">
                      <strong>Location:</strong>
                      {{ itinerary.location || "Not specified" }}
                    </VCol>
                    <VCol cols="12" md="6">
                      <strong>Accommodation:</strong>
                      {{ itinerary.accommodation || "Not specified" }}
                    </VCol>
                    <VCol cols="12" md="6">
                      <strong>Link:</strong>
                      <a 
                        v-if="itinerary.link" 
                        :href="itinerary.link" 
                        target="_blank"
                        class="text-primary"
                      >
                        {{ itinerary.link }}
                      </a>
                      <span v-else>Not available</span>
                    </VCol>
                    <VCol
                      cols="12"
                      v-if="itinerary.description"
                    >
                      <strong>Description:</strong>
                      {{ itinerary.description }}
                    </VCol>
                  </VRow>
                </div>
              </VCol>

              <VCol cols="12" v-else>
                <div class="text-medium-emphasis">
                  No itinerary information available
                </div>
              </VCol>
            </VRow>
          </VTabsWindowItem>

          <!-- Special Addons Tab -->
          <VTabsWindowItem value="addons">
            <VRow>
              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3">Special Addons</h6>
              </VCol>

              <VCol
                cols="12"
                v-if="
                  selectedListing.specialAddons &&
                  selectedListing.specialAddons.length > 0
                "
              >
                <div
                  v-for="(addon, index) in selectedListing.specialAddons"
                  :key="index"
                  class="mb-3 pa-3"
                  style="border: 1px solid #e0e0e0; border-radius: 8px"
                >
                  <div class="d-flex align-center mb-2">
                    <VIcon
                      icon="tabler-star"
                      size="20"
                      color="#ec8d22"
                      class="me-2"
                    />
                    <strong>{{
                      addon.title || `Addon ${index + 1}`
                    }}</strong>
                  </div>
                  <VRow>
                    <VCol cols="12" md="6">
                      <strong>Number:</strong>
                      {{ addon.number || (index + 1) }}
                    </VCol>
                    <VCol cols="12" md="6">
                      <strong>Price:</strong>
                      €{{ formatCurrency(addon.price || 0) }}
                    </VCol>
                    <VCol cols="12" md="6">
                      <strong>Active:</strong>
                      <VChip
                        :color="addon.is_active ? 'success' : 'error'"
                        size="small"
                      >
                        {{ addon.is_active ? 'Yes' : 'No' }}
                      </VChip>
                    </VCol>
                    <VCol cols="12" md="6">
                      <strong>Order:</strong>
                      {{ addon.order || 0 }}
                    </VCol>
                    <VCol
                      cols="12"
                      v-if="addon.description"
                    >
                      <strong>Description:</strong>
                      {{ addon.description }}
                    </VCol>
                  </VRow>
                </div>
              </VCol>

              <VCol cols="12" v-else>
                <div class="text-medium-emphasis">
                  No special addons available
                </div>
              </VCol>
            </VRow>
          </VTabsWindowItem>

          <!-- Packages Tab -->
          <VTabsWindowItem value="packages">
            <VRow>
              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3">
                  Package Information
                </h6>
              </VCol>

              <VCol
                cols="12"
                v-if="
                  selectedListing.packages &&
                  selectedListing.packages.length > 0
                "
              >
                <div
                  v-for="(pkg, index) in selectedListing.packages"
                  :key="index"
                  class="mb-4 pa-4"
                  style="border: 1px solid #e0e0e0; border-radius: 8px"
                >
                  <div class="d-flex align-center mb-2">
                    <VIcon
                      icon="tabler-package"
                      size="20"
                      color="#ec8d22"
                      class="me-2"
                    />
                    <strong>Package {{ index + 1 }}</strong>
                  </div>
                  <VRow>
                    <VCol cols="12" md="6">
                      <strong>Title:</strong> {{ pkg.title || "Not specified" }}
                    </VCol>
                    <VCol cols="12" md="6">
                      <strong>Price:</strong> €{{
                        formatCurrency(pkg.price || 0)
                      }}
                    </VCol>
                    <VCol cols="12" md="6">
                      <strong>Duration:</strong> {{ pkg.duration || "Not specified" }}
                    </VCol>
                    <VCol cols="12" md="6">
                      <strong>Capacity:</strong> {{ pkg.capacity || "Not specified" }}
                    </VCol>
                    <VCol cols="12" v-if="pkg.description">
                      <strong>Description:</strong> {{ pkg.description }}
                    </VCol>
                  </VRow>
                </div>
              </VCol>

              <VCol cols="12" v-else>
                <div class="text-medium-emphasis">
                  No package information available
                </div>
              </VCol>
            </VRow>
          </VTabsWindowItem>

          <!-- Availability Periods Tab (Multi Date listings) -->
          <VTabsWindowItem value="periods" v-if="selectedListing?.periods">
            <VRow>
              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3">
                  Availability Periods
                </h6>
              </VCol>

              <VCol
                cols="12"
                v-if="selectedListing.periods && selectedListing.periods.length"
              >
                <div
                  v-for="(period, index) in selectedListing.periods"
                  :key="index"
                  class="mb-4 pa-4"
                  style="border: 1px solid #e0e0e0; border-radius: 8px"
                >
                  <div class="d-flex align-center mb-2">
                    <VIcon
                      icon="tabler-calendar"
                      size="20"
                      color="#ec8d22"
                      class="me-2"
                    />
                    <strong>
                      Period {{ index + 1 }}:
                      {{ period.title || period.name || period.label || "Not specified" }}
                    </strong>
                  </div>
                  <VRow>
                    <VCol cols="12" md="6">
                      <strong>Start Date:</strong>
                      {{
                        formatDate(
                          period.start_date || period.start || period.from
                        )
                      }}
                    </VCol>
                    <VCol cols="12" md="6">
                      <strong>End Date:</strong>
                      {{
                        formatDate(period.end_date || period.end || period.to)
                      }}
                    </VCol>
                    <VCol cols="12" md="6">
                      <strong>Price:</strong>
                      €{{
                        formatCurrency(period.price || period.period_price || 0)
                      }}
                    </VCol>
                    <VCol cols="12" md="6">
                      <strong>Capacity:</strong>
                      {{ period.capacity || period.max_participants || "Not specified" }}
                    </VCol>
                    <VCol cols="12" v-if="period.available_days">
                      <strong>Available Days:</strong>
                      {{
                        formatDisplayValue(
                          period.available_days ||
                            period.days ||
                            period.availability
                        ) || "Not specified"
                      }}
                    </VCol>
                    <VCol cols="12" v-if="period.description">
                      <strong>Description:</strong>
                      {{ period.description }}
                    </VCol>
                  </VRow>
                </div>
              </VCol>

              <VCol cols="12" v-else>
                <div class="text-medium-emphasis">
                  No availability periods defined
                </div>
              </VCol>
            </VRow>
          </VTabsWindowItem>

          <!-- Additional Information Tab -->
          <VTabsWindowItem value="additional">
            <VRow>
              <VCol cols="12">
                <h6 class="text-h6 font-weight-medium mb-3">
                  Additional Information
                </h6>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Group Languages:</strong>
                <div class="mt-1">
                  <template
                    v-if="
                      ensureArray(
                        selectedListing.group_language ||
                          selectedListing.groupLanguage
                      ).length
                    "
                  >
                    <VChip
                      v-for="lang in ensureArray(
                        selectedListing.group_language ||
                          selectedListing.groupLanguage
                      )"
                      :key="lang"
                      size="small"
                      class="me-1 mb-1"
                      color="primary"
                      variant="outlined"
                    >
                      {{ lang }}
                    </VChip>
                  </template>
                  <span v-else class="text-medium-emphasis">Not available</span>
                </div>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Activities Included:</strong>
                <div class="mt-1">
                  {{
                    formatDisplayValue(
                      selectedListing.activities_included ||
                        selectedListing.activitiesIncluded
                    ) || "Not specified"
                  }}
                </div>
              </VCol>

              <VCol cols="12" md="6">
                <strong>What's Included:</strong>
                <div class="mt-1">
                  {{
                    formatDisplayValue(
                      selectedListing.whats_included ||
                        selectedListing.whatsIncluded
                    ) || "Not specified"
                  }}
                </div>
              </VCol>

              <VCol cols="12" md="6">
                <strong>What's Not Included:</strong>
                <div class="mt-1">
                  {{
                    formatDisplayValue(
                      selectedListing.whats_not_included ||
                        selectedListing.whatsNotIncluded
                    ) || "Not specified"
                  }}
                </div>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Maps & Routes:</strong>
                <div class="mt-1">
                  <template
                    v-if="
                      ensureArray(
                        selectedListing.maps_and_routes ||
                          selectedListing.mapsAndRoutes
                      ).length
                    "
                  >
                    <div
                      v-for="(route, index) in ensureArray(
                        selectedListing.maps_and_routes ||
                          selectedListing.mapsAndRoutes
                      )"
                      :key="index"
                      class="mb-1"
                    >
                      <template v-if="isValidUrl(getDisplayValue(route))">
                        <a
                          :href="getDisplayValue(route)"
                          target="_blank"
                          rel="noopener"
                          class="text-primary"
                        >
                          {{ getDisplayValue(route) }}
                        </a>
                      </template>
                      <template v-else>
                        {{ getDisplayValue(route) }}
                      </template>
                    </div>
                  </template>
                  <span v-else class="text-medium-emphasis">Not available</span>
                </div>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Listing Media:</strong>
                <div class="mt-1">
                  <template
                    v-if="
                      ensureArray(
                        selectedListing.listing_media ||
                          selectedListing.listingMedia
                      ).length
                    "
                  >
                    <div
                      v-for="(media, index) in ensureArray(
                        selectedListing.listing_media ||
                          selectedListing.listingMedia
                      )"
                      :key="index"
                      class="mb-1"
                    >
                      <template v-if="isValidUrl(getDisplayValue(media))">
                        <a
                          :href="getDisplayValue(media)"
                          target="_blank"
                          rel="noopener"
                          class="text-primary"
                        >
                          {{ getDisplayValue(media) }}
                        </a>
                      </template>
                      <template v-else>
                        {{ getDisplayValue(media) }}
                      </template>
                    </div>
                  </template>
                  <span v-else class="text-medium-emphasis">Not available</span>
                </div>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Promotional Videos:</strong>
                <div class="mt-1">
                  <template
                    v-if="
                      ensureArray(
                        selectedListing.promotional_video ||
                          selectedListing.promotionalVideo
                      ).length
                    "
                  >
                    <div
                      v-for="(video, index) in ensureArray(
                        selectedListing.promotional_video ||
                          selectedListing.promotionalVideo
                      )"
                      :key="index"
                      class="mb-1"
                    >
                      <template v-if="isValidUrl(getDisplayValue(video))">
                        <a
                          :href="getDisplayValue(video)"
                          target="_blank"
                          rel="noopener"
                          class="text-primary"
                        >
                          {{ getDisplayValue(video) }}
                        </a>
                      </template>
                      <template v-else>
                        {{ getDisplayValue(video) }}
                      </template>
                    </div>
                  </template>
                  <span v-else class="text-medium-emphasis">Not available</span>
                </div>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Departure Capacity:</strong>
                {{
                  selectedListing.departure_capacity ||
                  selectedListing.max_capacity ||
                  selectedListing.capacity ||
                  "Not specified"
                }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Difficulty Level:</strong>
                {{
                  selectedListing.difficulty_level ||
                  selectedListing.experience_level ||
                  "Not specified"
                }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Equipment Included:</strong>
                {{
                  selectedListing.equipment_included ||
                  ensureArray(selectedListing.activities_included).join(", ") ||
                  "Not specified"
                }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Primary Language:</strong>
                {{
                  selectedListing.language ||
                  ensureArray(selectedListing.group_language).join(", ") ||
                  "Not specified"
                }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Age Group:</strong>
                {{
                  formatDisplayValue(
                    selectedListing.age_group ||
                      selectedListing.target_age_group ||
                      selectedListing.age_range ||
                      selectedListing.age
                  ) || "Not specified"
                }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Available Days:</strong>
                {{
                  formatDisplayValue(
                    selectedListing.available_days ||
                      selectedListing.availableDays ||
                      selectedListing.days_available ||
                      selectedListing.operating_days
                  ) || "Not specified"
                }}
              </VCol>

              <VCol cols="12" md="6">
                <strong>Personal Policies:</strong>
                <div class="mt-1">
                  {{
                    formatDisplayValue(
                      selectedListing.personal_policies ||
                        selectedListing.personalPolicies
                    ) || "Not specified"
                  }}
                </div>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Policy Details:</strong>
                <div class="mt-1">
                  {{
                    formatDisplayValue(
                      selectedListing.personal_policies_text ||
                        selectedListing.personalPoliciesText ||
                        selectedListing.policy_details ||
                        selectedListing.policies_text ||
                        selectedListing.terms_conditions
                    ) || "Not specified"
                  }}
                </div>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Terms Accepted:</strong>
                <VChip
                  size="small"
                  class="ml-2"
                  :color="
                    selectedListing.terms_accepted ? 'success' : 'warning'
                  "
                >
                  {{ selectedListing.terms_accepted ? "Yes" : "No" }}
                </VChip>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Provider's FAQ:</strong>
                <div class="mt-1">
                  {{
                    formatDisplayValue(
                      selectedListing.providers_faq ||
                        selectedListing.providersFaq
                    ) || "Not specified"
                  }}
                </div>
              </VCol>

              <VCol cols="12" md="6">
                <strong>Requirements:</strong>
                <div class="mt-1">
                  {{
                    formatDisplayValue(
                      selectedListing.requirements ||
                        selectedListing.special_requirements ||
                        selectedListing.requirement ||
                        selectedListing.participant_requirements ||
                        selectedListing.booking_requirements
                    ) || "Not specified"
                  }}
                </div>
              </VCol>

              <VCol cols="12">
                <strong>Additional Notes:</strong>
                <div class="mt-1">
                  {{
                    formatDisplayValue(
                      selectedListing.additional_notes ||
                        selectedListing.additionalNotes
                    ) || "Not specified"
                  }}
                </div>
              </VCol>

              <VCol cols="12" v-if="selectedListing.terms_conditions">
                <strong>Terms & Conditions:</strong>
                <div class="mt-1">{{ selectedListing.terms_conditions }}</div>
              </VCol>

              <VCol cols="12" v-if="selectedListing.cancellation_policy">
                <strong>Cancellation Policy:</strong>
                <div class="mt-1">
                  {{ selectedListing.cancellation_policy }}
                </div>
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
const activeListingTab = ref("basic");
const activeProviderTab = ref("basic");

const isPlainObject = (value) =>
  value !== null && typeof value === "object" && !Array.isArray(value);

const parseJsonSafely = (value) => {
  if (typeof value !== "string") return null;
  try {
    return JSON.parse(value);
  } catch (error) {
    return null;
  }
};

const ensureArray = (value) => {
  if (value === null || value === undefined || value === "") {
    return [];
  }

  if (Array.isArray(value)) {
    return value
      .flatMap((entry) => ensureArray(entry))
      .map((entry) => (typeof entry === "string" ? entry.trim() : entry))
      .filter((entry) => entry !== null && entry !== undefined && entry !== "");
  }

  if (typeof value === "string") {
    const trimmed = value.trim();
    if (!trimmed) return [];

    const parsed = parseJsonSafely(trimmed);
    if (Array.isArray(parsed) || isPlainObject(parsed)) {
      return ensureArray(parsed);
    }

    const segments = trimmed.includes("\n")
      ? trimmed.split(/\r?\n/)
      : trimmed.includes(",")
      ? trimmed.split(",")
      : [trimmed];

    return segments.map((segment) => segment.trim()).filter(Boolean);
  }

  if (isPlainObject(value)) {
    if ("value" in value && value.value !== undefined) {
      return ensureArray(value.value);
    }
    if ("label" in value && value.label !== undefined) {
      return ensureArray(value.label);
    }
    if ("title" in value && value.title !== undefined) {
      return ensureArray(value.title);
    }
    if ("name" in value && value.name !== undefined) {
      return ensureArray(value.name);
    }

    const collected = Object.values(value)
      .flatMap((entry) => ensureArray(entry))
      .filter(Boolean);

    if (collected.length) {
      return [
        Object.entries(value)
          .map(([key, entry]) => {
            const formatted = formatDisplayValue(entry);
            return formatted ? `${key}: ${formatted}` : "";
          })
          .filter(Boolean)
          .join("; "),
      ].filter(Boolean);
    }

    return collected;
  }

  const asString = String(value).trim();
  return asString ? [asString] : [];
};

const getDisplayValue = (value) => {
  const arrayValue = ensureArray(value);
  if (arrayValue.length) return arrayValue[0];
  if (typeof value === "string") return value.trim();
  return "";
};

const formatDisplayValue = (value) => {
  const arrayValue = ensureArray(value);
  if (arrayValue.length) return arrayValue.join(", ");
  if (typeof value === "string") return value.trim();
  return "";
};

const isValidUrl = (value) => {
  const candidate = getDisplayValue(value);
  if (!candidate) return false;

  try {
    const url = new URL(candidate);
    return Boolean(url.protocol && url.host);
  } catch (error) {
    return false;
  }
};

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

      // Handle both paginated and non-paginated responses
      const listings = listingsResponse.data || listingsResponse;
      eventsData.value = Array.isArray(listings)
        ? listings
        : listings.data || [];

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

const formatDate = (value) => {
  const candidate = getDisplayValue(value);
  if (!candidate) return "Not specified";

  const parsed = Date.parse(candidate);
  if (Number.isNaN(parsed)) return candidate;

  const date = new Date(parsed);
  return date.toLocaleString("en-US", {
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

// Quick Add Listing state & actions
const showQuickAddListing = ref(false);
const quickListingFormRef = ref();
const addingListing = ref(false);
const quickListingForm = reactive({
  listing_title: "",
  listing_type: "single-date",
  locations: "",
  price: "",
  min_capacity: "",
  max_capacity: "",
});

const addEvent = () => {
  showQuickAddListing.value = true;
};

const submitQuickListing = async () => {
  const { valid } = await quickListingFormRef.value.validate();
  if (!valid) return;
  addingListing.value = true;
  try {
    const uid = useCookie("userData").value?.id;
    const body = { ...quickListingForm, user_id: uid, status: "submitted" };
    const res = await $api("/listings", { method: "POST", body });
    const created = res?.data || res;
    if (created) {
      // نرمال‌سازی برای جدول ادمین
      eventsData.value.unshift({
        id: created.id,
        listing_title: created.listing_title,
        locations: created.locations,
        listing_type: created.listing_type,
        price: created.price,
        min_capacity: created.min_capacity,
        max_capacity: created.max_capacity,
        status: created.status || "submitted",
        user: created.user || userData.value,
      });
      showQuickAddListing.value = false;
      Object.assign(quickListingForm, {
        listing_title: "",
        listing_type: "single-date",
        locations: "",
        price: "",
        min_capacity: "",
        max_capacity: "",
      });
    }
  } catch (e) {
    console.error("Failed to create quick listing", e);
  } finally {
    addingListing.value = false;
  }
};

const viewEvent = async (item) => {
  try {
    console.log("Viewing event, fetching full details:", item.id);
    const res = await $api(`/admin/listings/${item.id}`, { method: "GET" });

    // Merge and normalize response data with fallbacks so UI tabs render consistently
    const combined = { ...item, ...res };

    const periodsData = (() => {
      if (Array.isArray(combined.periods)) return combined.periods;
      if (typeof combined.periods === "string") {
        const parsed = parseJsonSafely(combined.periods);
        if (Array.isArray(parsed)) return parsed;
        if (parsed && isPlainObject(parsed)) return [parsed];
        return ensureArray(combined.periods);
      }
      if (combined.periods && isPlainObject(combined.periods)) {
        return [combined.periods];
      }
      return [];
    })();

    const normalized = {
      ...combined,
      locations: ensureArray(
        combined.locations || combined.location || combined.address || []
      ).join(", "),
      departure_capacity:
        combined.departure_capacity ||
        combined.max_capacity ||
        combined.capacity ||
        null,
      difficulty_level: combined.difficulty_level || combined.experience_level,
      equipment_included:
        combined.equipment_included || combined.activities_included,
      language:
        combined.language ||
        ensureArray(combined.group_language).join(", ") ||
        null,
      age_group: combined.age_group || combined.target_age_group || combined.age_range || combined.age,
      activities_included: ensureArray(combined.activities_included),
      whats_included: ensureArray(combined.whats_included),
      whats_not_included: ensureArray(combined.whats_not_included),
      maps_and_routes: ensureArray(combined.maps_and_routes),
      listing_media: ensureArray(combined.listing_media),
      promotional_video: ensureArray(combined.promotional_video),
      additional_notes: ensureArray(combined.additional_notes),
      providers_faq: ensureArray(combined.providers_faq),
      personal_policies: ensureArray(combined.personal_policies),
      group_language: ensureArray(combined.group_language),
      available_days: ensureArray(combined.available_days || combined.days_available || combined.operating_days),
      requirements:
        combined.requirements ||
        combined.special_requirements ||
        combined.requirement ||
        combined.participant_requirements ||
        combined.booking_requirements ||
        null,
      starting_date:
        combined.starting_date ||
        combined.start_date ||
        combined.startDate ||
        null,
      finishing_date:
        combined.finishing_date ||
        combined.end_date ||
        combined.endDate ||
        null,
      min_capacity:
        combined.min_capacity ??
        combined.min_participants ??
        combined.capacity_min ??
        null,
      max_capacity:
        combined.max_capacity ??
        combined.max_participants ??
        combined.capacity_max ??
        null,
      periods: periodsData,
      packages: ensureArray(combined.packages),
      specialAddons: ensureArray(
        combined.specialAddons || combined.special_addons
      )
        .filter(Boolean)
        .map((addon) => ({
          ...addon,
          price: addon.price ?? addon.addon_price ?? null,
        })),
      itineraries: ensureArray(combined.itineraries || combined.itinerary).map(
        (entry, index) => ({
          ...entry,
          title:
            entry.day_title || entry.title || entry.name || `Day ${index + 1}`,
        })
      ),
      // Add policy details mapping
      personal_policies_text: combined.personal_policies_text || combined.policy_details || combined.policies_text || combined.terms_conditions,
    };

    selectedListing.value = normalized;

    console.log("Selected listing data:", selectedListing.value);
    showListingViewDialog.value = true;
  } catch (error) {
    console.error("Failed to load listing details", error);

    const fallback = { ...item };
    fallback.locations = ensureArray(
      item.locations || item.location || item.address || []
    ).join(", ");
    fallback.departure_capacity =
      item.departure_capacity || item.max_capacity || item.capacity || null;
    fallback.difficulty_level =
      item.difficulty_level || item.experience_level || null;
    fallback.equipment_included =
      item.equipment_included || item.activities_included || null;
    fallback.language =
      item.language || ensureArray(item.group_language).join(", ") || null;
    fallback.age_group = item.age_group || item.target_age_group || item.age_range || item.age || null;
    fallback.activities_included = ensureArray(item.activities_included);
    fallback.whats_included = ensureArray(item.whats_included);
    fallback.whats_not_included = ensureArray(item.whats_not_included);
    fallback.maps_and_routes = ensureArray(item.maps_and_routes);
    fallback.listing_media = ensureArray(item.listing_media);
    fallback.promotional_video = ensureArray(item.promotional_video);
    fallback.additional_notes = ensureArray(item.additional_notes);
    fallback.providers_faq = ensureArray(item.providers_faq);
    fallback.personal_policies = ensureArray(item.personal_policies);
    fallback.group_language = ensureArray(item.group_language);
    fallback.available_days = ensureArray(item.available_days || item.days_available || item.operating_days);
    fallback.requirements =
      item.requirements ||
      item.special_requirements ||
      item.requirement ||
      item.participant_requirements ||
      item.booking_requirements ||
      null;
    fallback.starting_date =
      item.starting_date || item.start_date || item.startDate || null;
    fallback.finishing_date =
      item.finishing_date || item.end_date || item.endDate || null;
    fallback.min_capacity =
      item.min_capacity ?? item.min_participants ?? item.capacity_min ?? null;
    fallback.max_capacity =
      item.max_capacity ?? item.max_participants ?? item.capacity_max ?? null;
    fallback.periods = Array.isArray(item.periods)
      ? item.periods
      : ensureArray(item.periods);
    fallback.packages = ensureArray(item.packages);
    fallback.specialAddons = ensureArray(
      item.specialAddons || item.special_addons
    )
      .filter(Boolean)
      .map((addon) => ({
        ...addon,
        price: addon.price ?? addon.addon_price ?? null,
      }));
    fallback.itineraries = ensureArray(item.itineraries || item.itinerary).map(
      (entry, index) => ({
        ...entry,
        title:
          entry.day_title || entry.title || entry.name || `Day ${index + 1}`,
      })
    );
    
    // Add policy details mapping
    fallback.personal_policies_text = item.personal_policies_text || item.policy_details || item.policies_text || item.terms_conditions;

    selectedListing.value = fallback;
    showListingViewDialog.value = true;
  }
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

// All Users table actions (view/edit)
const openViewUser = (row) => {
  // Re-use admin users page route with query to open details
  router.push({ path: "/admin/users", query: { view: row.id } });
};
const openEditUserFromOrders = (row) => {
  router.push({ path: "/admin/users", query: { edit: row.id } });
};

// Add User dialog state & logic
const showAddUser = ref(false);
const addingUser = ref(false);
const addUserFormRef = ref();
const addUserForm = reactive({
  name: "",
  email: "",
  role: "user",
  password: "",
});

const addUser = () => {
  showAddUser.value = true;
};

const submitAddUser = async () => {
  const { valid } = await addUserFormRef.value.validate();
  if (!valid) return;
  addingUser.value = true;
  try {
    const res = await $api("/admin/users", {
      method: "POST",
      body: addUserForm,
    });
    if (res?.user) {
      // Prepend to All Users table data (ordersData is placeholder list)
      ordersData.value.unshift({
        id: res.user.id,
        name: res.user.name,
        email: res.user.email,
        userId: `#${String(res.user.id).padStart(6, "0")}`,
        country: "-",
        bookings: 0,
        totalSpent: 0,
      });
      showAddUser.value = false;
      Object.assign(addUserForm, {
        name: "",
        email: "",
        role: "user",
        password: "",
      });
    }
  } catch (e) {
    console.error("Failed creating user", e);
  } finally {
    addingUser.value = false;
  }
};

// Add Provider dialog state & logic
const showAddProvider = ref(false);
const addingProvider = ref(false);
const addProviderFormRef = ref();
const addProviderForm = reactive({
  type: "individual",
  name: "",
  email: "",
  password: "",
});

const openAddProvider = () => {
  showAddProvider.value = true;
};

const submitAddProvider = async () => {
  const { valid } = await addProviderFormRef.value.validate();
  if (!valid) return;
  addingProvider.value = true;
  try {
    const res = await $api("/admin/providers", {
      method: "POST",
      body: addProviderForm,
    });
    if (res?.provider) {
      providersData.value.unshift({
        id: res.provider.id,
        provider_name: res.provider.provider_name,
        provider_type: res.provider.provider_type,
        want_to_be_listed: res.provider.want_to_be_listed,
        status: res.provider.status,
        total_listings: 0,
        total_bookings: 0,
        created_at: res.provider.created_at,
      });
      showAddProvider.value = false;
      Object.assign(addProviderForm, {
        type: "individual",
        name: "",
        email: "",
        password: "",
      });
    }
  } catch (e) {
    console.error("Failed creating provider", e);
  } finally {
    addingProvider.value = false;
  }
};

// Provider action functions
const exportProviders = () => {
  console.log("Exporting providers...");
};

const addProvider = () => {
  router.push("/provider/create");
};

const viewProvider = async (item) => {
  console.log(
    "Viewing provider, fetching full details:",
    item.id,
    item.provider_type
  );
  try {
    const res = await $api(
      `/admin/providers/${item.id}/${item.provider_type}`,
      { method: "GET" }
    );
    selectedProvider.value = res || item;
  } catch (e) {
    console.error("Failed to load provider details", e);
    selectedProvider.value = item;
  }
  showProviderViewDialog.value = true;
};

const showProviderMenu = (item) => {
  console.log("Showing provider menu:", item);
  selectedProvider.value = item;
  showProviderEditDialog.value = true;
};

// Helpers to display all fields nicely in view dialogs
const toTitle = (key) =>
  (key || "").replace(/_/g, " ").replace(/\b\w/g, (m) => m.toUpperCase());
const formatValue = (val) => {
  if (val === null || val === undefined || val === "") return "Not specified";
  if (typeof val === "string") {
    // Try JSON parse for stringified arrays/objects
    try {
      const parsed = JSON.parse(val);
      if (Array.isArray(parsed)) return parsed.join(", ");
      if (typeof parsed === "object") return JSON.stringify(parsed);
    } catch (_) {}
    return val;
  }
  if (Array.isArray(val)) return val.length ? JSON.stringify(val) : "Not specified";
  if (typeof val === "object") return JSON.stringify(val);
  return String(val);
};
const getReadableEntries = (obj) => {
  if (!obj) return [];
  const omit = new Set(["password", "remember_token"]);
  return Object.keys(obj)
    .filter((k) => !omit.has(k))
    .map((k) => ({ key: k, label: toTitle(k), value: formatValue(obj[k]) }));
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
  return types[type] || type || "Not specified";
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
    color: #000;
    font-size: 2rem;
    font-weight: unset;
    margin: 0;
    font-family: "Anton", "Inter", "Segoe UI", Tahoma, Geneva, Verdana,
      sans-serif;
  }
}

.section-title {
  h2 {
    color: #000;
    font-size: 1.5rem;
    font-weight: unset;
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

// Details dialog styling
.details-header {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.title-line {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
}
.details-title {
  font-family: "Anton", "Inter", sans-serif;
  font-size: 1.25rem;
  font-weight: unset;
  color: #000;
}
.sub-line {
  color: #6b7280;
}
.details-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 14px;
}
.details-item {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 12px;
  background: #fff;
}
.details-label {
  font-size: 0.75rem;
  color: #6b7280;
  margin-bottom: 6px;
  text-transform: capitalize;
}
.details-value {
  font-size: 0.95rem;
  color: #111827;
  word-break: break-word;
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
