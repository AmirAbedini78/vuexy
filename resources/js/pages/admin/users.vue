<template>
  <VCard>
    <VCardTitle class="d-flex align-center justify-space-between">
      <span>User Management</span>
      <VBtn
        color="primary"
        prepend-icon="tabler-refresh"
        @click="loadUsers"
        :loading="loading"
      >
        Refresh
      </VBtn>
    </VCardTitle>

    <VCardText>
      <!-- Search and Filters -->
      <VRow class="mb-4">
        <VCol cols="12" md="6">
          <VTextField
            v-model="searchQuery"
            label="Search users"
            placeholder="Search by name or email..."
            prepend-inner-icon="tabler-search"
            variant="outlined"
            density="compact"
            @input="debouncedSearch"
          />
        </VCol>
        <VCol cols="12" md="3">
          <VSelect
            v-model="roleFilter"
            label="Filter by role"
            :items="roleOptions"
            variant="outlined"
            density="compact"
            @update:model-value="loadUsers"
          />
        </VCol>
        <VCol cols="12" md="3">
          <VSelect
            v-model="statusFilter"
            label="Filter by status"
            :items="statusOptions"
            variant="outlined"
            density="compact"
            @update:model-value="loadUsers"
          />
        </VCol>
      </VRow>

      <!-- Users Table -->
      <VDataTable
        :headers="headers"
        :items="users"
        :loading="loading"
        :search="searchQuery"
        class="text-no-wrap"
      >
        <!-- Role Column -->
        <template #item.role="{ item }">
          <VChip :color="getRoleColor(item.role)" size="small" variant="tonal">
            {{ formatRole(item.role) }}
          </VChip>
        </template>

        <!-- Status Column -->
        <template #item.status="{ item }">
          <VChip
            :color="getStatusColor(item.status)"
            size="small"
            variant="tonal"
          >
            {{ formatStatus(item.status) }}
          </VChip>
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
            @click="viewUser(item)"
          >
            <VIcon icon="tabler-eye" />
          </VBtn>
          <VBtn
            icon
            variant="text"
            size="small"
            color="warning"
            @click="openEditUser(item)"
          >
            <VIcon icon="tabler-edit" />
          </VBtn>
          <VBtn
            icon
            variant="text"
            size="small"
            color="success"
            :title="'Login as ' + item.name"
            @click="impersonate(item)"
          >
            <VIcon icon="tabler-login" />
          </VBtn>
          <VBtn
            icon
            variant="text"
            size="small"
            color="error"
            @click="deleteUser(item)"
          >
            <VIcon icon="tabler-trash" />
          </VBtn>
        </template>
      </VDataTable>

      <!-- Pagination -->
      <div class="d-flex align-center justify-space-between mt-4">
        <div class="text-body-2 text-medium-emphasis">
          Showing {{ paginationInfo.from }} to {{ paginationInfo.to }} of
          {{ paginationInfo.total }} users
        </div>
        <VPagination
          v-model="currentPage"
          :length="totalPages"
          @update:model-value="loadUsers"
        />
      </div>
    </VCardText>

    <!-- User Details Dialog -->
    <VDialog v-model="showUserDialog" max-width="600">
      <VCard>
        <VCardTitle>User Details</VCardTitle>
        <VCardText v-if="selectedUser">
          <VRow>
            <VCol cols="12" md="6">
              <VTextField
                v-model="selectedUser.name"
                label="Name"
                readonly
                variant="outlined"
                density="compact"
              />
            </VCol>
            <VCol cols="12" md="6">
              <VTextField
                v-model="selectedUser.email"
                label="Email"
                readonly
                variant="outlined"
                density="compact"
              />
            </VCol>
            <VCol cols="12" md="6">
              <VTextField
                v-model="selectedUser.role"
                label="Role"
                readonly
                variant="outlined"
                density="compact"
              />
            </VCol>
            <VCol cols="12" md="6">
              <VTextField
                v-model="selectedUser.created_at"
                label="Created At"
                readonly
                variant="outlined"
                density="compact"
              />
            </VCol>
          </VRow>
        </VCardText>
        <VCardActions>
          <VSpacer />
          <VBtn color="primary" @click="showUserDialog = false"> Close </VBtn>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Create/Edit User Dialog -->
    <VDialog v-model="showUpsertDialog" max-width="520">
      <VCard>
        <VCardTitle class="d-flex align-center justify-space-between">
          <span>{{
            upsertMode === "create" ? "Create User" : "Edit User"
          }}</span>
          <VBtn
            icon
            variant="text"
            size="small"
            @click="showUpsertDialog = false"
          >
            <VIcon icon="tabler-x" />
          </VBtn>
        </VCardTitle>
        <VCardText>
          <VForm ref="upsertFormRef" @submit.prevent="submitUpsert">
            <VTextField
              v-model="upsertForm.name"
              label="Name"
              required
              variant="outlined"
              class="mb-3"
            />
            <VTextField
              v-model="upsertForm.email"
              label="Email"
              type="email"
              required
              variant="outlined"
              class="mb-3"
              :disabled="upsertMode === 'edit'"
            />
            <VSelect
              v-model="upsertForm.role"
              :items="[
                { title: 'User', value: 'user' },
                { title: 'Admin', value: 'admin' },
              ]"
              label="Role"
              required
              variant="outlined"
              class="mb-3"
            />
            <VSelect
              v-model="upsertForm.status"
              :items="statusOptions"
              label="Status"
              required
              variant="outlined"
              class="mb-3"
            />
            <VTextField
              v-model="upsertForm.password"
              label="Password (leave blank to keep)"
              type="password"
              variant="outlined"
              class="mb-3"
            />
            <div class="d-flex justify-end">
              <VBtn color="primary" type="submit" :loading="upserting">{{
                upsertMode === "create" ? "Create" : "Save"
              }}</VBtn>
            </div>
          </VForm>
        </VCardText>
      </VCard>
    </VDialog>
  </VCard>
</template>

<script setup>
definePage({
  meta: {
    layout: "default",
    requiresAuth: true,
    requiresAdmin: true,
  },
});

const loading = ref(false);
const users = ref([]);
const searchQuery = ref("");
const roleFilter = ref("all");
const statusFilter = ref("all");
const currentPage = ref(1);
const totalPages = ref(1);
const paginationInfo = ref({
  from: 0,
  to: 0,
  total: 0,
});

const showUserDialog = ref(false);
const selectedUser = ref(null);
const showUpsertDialog = ref(false);
const upsertMode = ref("create");
const upsertFormRef = ref();
const upserting = ref(false);
const upsertForm = reactive({
  name: "",
  email: "",
  role: "user",
  status: "active",
  password: "",
});

const headers = [
  { title: "Name", key: "name", sortable: true },
  { title: "Email", key: "email", sortable: true },
  { title: "Role", key: "role", sortable: true },
  { title: "Status", key: "status", sortable: true },
  { title: "Created At", key: "created_at", sortable: true },
  { title: "Actions", key: "actions", sortable: false },
];

const roleOptions = [
  { title: "All Roles", value: "all" },
  { title: "User", value: "user" },
  { title: "Admin", value: "admin" },
];

const statusOptions = [
  { title: "All Statuses", value: "all" },
  { title: "Active", value: "active" },
  { title: "Inactive", value: "inactive" },
  { title: "Suspended", value: "suspended" },
];

// Native debounce function
let searchTimeout = null;
const debouncedSearch = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    loadUsers();
  }, 300);
};

const loadUsers = async () => {
  loading.value = true;
  try {
    const params = new URLSearchParams({
      page: currentPage.value.toString(),
      search: searchQuery.value,
      role: roleFilter.value !== "all" ? roleFilter.value : "",
      status: statusFilter.value !== "all" ? statusFilter.value : "",
    });

    const response = await $api(`/admin/users?${params}`, {
      method: "GET",
    });

    users.value = response.data;
    paginationInfo.value = {
      from: response.from,
      to: response.to,
      total: response.total,
    };
    totalPages.value = response.last_page;
  } catch (error) {
    console.error("Error loading users:", error);
  } finally {
    loading.value = false;
  }
};

const formatRole = (role) => {
  const roleMap = {
    user: "User",
    admin: "Admin",
  };
  return roleMap[role] || role;
};

const formatStatus = (status) => {
  const statusMap = {
    active: "Active",
    inactive: "Inactive",
    suspended: "Suspended",
  };
  return statusMap[status] || status;
};

const formatDate = (dateString) => {
  if (!dateString) return "";
  return new Date(dateString).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

const getRoleColor = (role) => {
  const colorMap = {
    user: "primary",
    admin: "warning",
  };
  return colorMap[role] || "default";
};

const getStatusColor = (status) => {
  const colorMap = {
    active: "success",
    inactive: "warning",
    suspended: "error",
  };
  return colorMap[status] || "default";
};

const viewUser = (user) => {
  selectedUser.value = user;
  showUserDialog.value = true;
};

const openCreateUser = () => {
  upsertMode.value = "create";
  Object.assign(upsertForm, {
    name: "",
    email: "",
    role: "user",
    status: "active",
    password: "",
  });
  showUpsertDialog.value = true;
};

const openEditUser = (user) => {
  upsertMode.value = "edit";
  Object.assign(upsertForm, {
    name: user.name,
    email: user.email,
    role: user.role,
    status: user.status ?? "active",
    password: "",
  });
  selectedUser.value = user;
  showUpsertDialog.value = true;
};

const submitUpsert = async () => {
  const { valid } = await upsertFormRef.value.validate();
  if (!valid) return;
  upserting.value = true;
  try {
    if (upsertMode.value === "create") {
      await $api("/admin/users", { method: "POST", body: upsertForm });
    } else if (selectedUser.value) {
      await $api(`/admin/users/${selectedUser.value.id}`, {
        method: "PUT",
        body: upsertForm,
      });
    }
    showUpsertDialog.value = false;
    await loadUsers();
  } catch (e) {
    console.error("Failed to save user", e);
  } finally {
    upserting.value = false;
  }
};

const deleteUser = async (user) => {
  if (confirm(`Are you sure you want to delete user ${user.name}?`)) {
    try {
      await $api(`/admin/users/${user.id}`, {
        method: "DELETE",
      });
      loadUsers();
    } catch (error) {
      console.error("Error deleting user:", error);
    }
  }
};

const impersonate = async (user) => {
  try {
    const res = await $api(`/admin/users/${user.id}/impersonate`, {
      method: "POST",
    });
    if (res?.access_token) {
      // Store token & userData, then redirect to app root as provider/user
      useCookie("accessToken").value = res.access_token;
      useCookie("userData").value = res.user;
      const type = res.user.role === "company" ? "company" : "individual";
      await router.push(`/registration/timeline/${type}/${res.user.id}`);
      location.reload();
    }
  } catch (e) {
    console.error("Failed to impersonate", e);
  }
};

// Load users on mount
onMounted(() => {
  // Check if user is admin
  const userDataCookie = useCookie("userData");
  const userData = userDataCookie.value;

  if (!userData || userData.role !== "admin") {
    console.log("Non-admin user detected, redirecting...");
    router.push("/");
    return;
  }

  loadUsers();

  // If navigated from dashboard with query to open view/edit
  const route = useRoute();
  if (route.query.view) {
    const id = Number(route.query.view);
    const found = users.value.find((u) => u.id === id);
    if (found) viewUser(found);
  }
  if (route.query.edit) {
    const id = Number(route.query.edit);
    const found = users.value.find((u) => u.id === id);
    if (found) openEditUser(found);
  }
});
</script>

<style scoped>
.v-data-table {
  border-radius: 8px;
  overflow: hidden;
}
</style>
