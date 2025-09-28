<template>
  <VDialog
    v-model="isOpen"
    max-width="1200"
    scrollable
  >
    <VCard>
      <VCardTitle class="d-flex align-center justify-space-between">
        <span class="text-h5 font-weight-bold">All Your Adventures</span>
        <VBtn
          icon
          variant="text"
          @click="isOpen = false"
        >
          <VIcon icon="tabler-x" />
        </VBtn>
      </VCardTitle>
      
      <VDivider />
      
      <VCardText>
        <VDataTable
          :headers="headers"
          :items="adventures"
          :loading="loading"
          class="elevation-1"
          item-key="id"
        >
          <!-- Event Title Column -->
          <template #item.eventTitle="{ item }">
            <div>
              <div class="font-weight-medium">
                {{ item.eventTitle || item.listing_title || item.title || `Adventure ${item.id}` }}
              </div>
              <div class="text-caption text-medium-emphasis">
                {{ item.location || item.locations || item.address || "No location" }}
              </div>
            </div>
          </template>

          <!-- Status Column -->
          <template #item.status="{ item }">
            <VChip
              :color="getStatusColor(item.status)"
              size="small"
              variant="outlined"
            >
              {{ formatStatus(item.status) }}
            </VChip>
          </template>

          <!-- Price Column -->
          <template #item.price="{ item }">
            <span class="font-weight-medium">
              €{{ item.price ? item.price.toFixed(2) : '0.00' }}
            </span>
          </template>

          <!-- Type Column -->
          <template #item.listing_type="{ item }">
            <VChip
              :color="getListingTypeColor(item.listing_type)"
              size="small"
              variant="outlined"
            >
              {{ formatListingType(item.listing_type) }}
            </VChip>
          </template>

          <!-- Actions Column -->
          <template #item.actions="{ item }">
            <div class="d-flex gap-2">
              <IconBtn size="small" @click="viewAdventure(item)">
                <VIcon icon="tabler-eye" size="18" />
              </IconBtn>
              <IconBtn size="small" @click="editAdventure(item)">
                <VIcon icon="tabler-edit" size="18" />
              </IconBtn>
            </div>
          </template>

          <!-- No Data Template -->
          <template #no-data>
            <div class="text-center w-100 pa-6">
              <div class="mb-4">
                <VIcon icon="tabler-calendar-off" size="48" color="grey" />
              </div>
              <h6 class="text-h6 mb-2">No Adventures Found</h6>
              <p class="text-medium-emphasis mb-4">
                You haven't created any adventures yet. Start by creating your first adventure!
              </p>
              <VBtn
                color="primary"
                @click="createNewAdventure"
                prepend-icon="tabler-plus"
              >
                Create New Adventure
              </VBtn>
            </div>
          </template>
        </VDataTable>
      </VCardText>
    </VCard>
  </VDialog>
</template>

<script setup>
import { $api } from '@/utils/api'
import { computed, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// Props
const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['update:modelValue'])

// Data
const adventures = ref([])
const loading = ref(false)

// Computed
const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

// Headers
const headers = [
  { title: "ADVENTURE TITLE", key: "eventTitle", width: "30%" },
  { title: "ADV. ID", key: "advId", width: "12%" },
  { title: "STATUS", key: "status", width: "18%" },
  { title: "PARTICIPANTS", key: "participants", width: "12%" },
  { title: "PRICE", key: "price", width: "10%" },
  { title: "TYPE", key: "listing_type", width: "8%" },
  { title: "ACTION", key: "actions", sortable: false, width: "10%" },
]

// Methods
const loadAdventures = async () => {
  loading.value = true
  try {
    console.log("Loading user's adventures...")
    const userDataCookie = useCookie("userData")
    const userId = userDataCookie.value?.id || userDataCookie.value?.user_id

    if (!userId) {
      console.error("User ID not found")
      adventures.value = []
      return
    }

    console.log("Loading adventures for user ID:", userId)
    const res = await $api("/listings", { method: "GET" })
    console.log("Listings API response:", res)

    let allListings = []
    if (Array.isArray(res)) {
      allListings = res
    } else if (res?.data && Array.isArray(res.data)) {
      allListings = res.data
    } else if (res?.data?.data && Array.isArray(res.data.data)) {
      allListings = res.data.data
    }

    console.log("All listings:", allListings)

    const userListings = allListings.filter((listing) => {
      return listing.user_id === userId || listing.user?.id === userId
    })

    console.log("User's listings:", userListings)

    adventures.value = userListings.map((listing) => ({
      id: listing.id,
      eventTitle: listing.listing_title || listing.title || "Untitled Adventure",
      location: listing.locations || listing.location || "No location",
      advId: String(listing.id).padStart(5, "0"),
      status: (listing.status || "draft")
        .replace("other_events", "Other Events")
        .replace("edit_review", "Edit Review Pending")
        .replace("_", " "),
      participants: `${listing.min_capacity || 0}/${listing.max_capacity || 0}`,
      price: listing.price || 0,
      listing_type: listing.listing_type || "single-date",
      created_at: listing.created_at,
      updated_at: listing.updated_at,
    }))

    console.log("Mapped adventures:", adventures.value)
  } catch (e) {
    console.error("Failed to load adventures", e)
    adventures.value = []
  } finally {
    loading.value = false
  }
}

const getStatusColor = (status) => {
  const statusColors = {
    'draft': 'grey',
    'pending': 'orange',
    'approved': 'green',
    'rejected': 'red',
    'other events': 'blue',
    'edit review pending': 'orange'
  }
  return statusColors[status?.toLowerCase()] || 'grey'
}

const formatStatus = (status) => {
  if (!status) return 'Draft'
  return status.charAt(0).toUpperCase() + status.slice(1).replace(/_/g, ' ')
}

const getListingTypeColor = (type) => {
  const typeColors = {
    'single-date': 'primary',
    'multi-date': 'success',
    'open-date': 'info'
  }
  return typeColors[type] || 'primary'
}

const formatListingType = (type) => {
  if (!type) return 'Single Date'
  return type.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const viewAdventure = (item) => {
  console.log("Viewing adventure:", item)
  // Navigate to adventure view page
  router.push({ name: 'listing', query: { view: item.id } })
}

const editAdventure = (item) => {
  console.log("Editing adventure:", item)
  // Navigate to adventure edit page
  router.push({ name: 'listing', query: { edit: item.id } })
}

const createNewAdventure = () => {
  console.log("Creating new adventure")
  // Navigate to listing page
  router.push({ name: 'listing' })
  isOpen.value = false
}

// Lifecycle
onMounted(() => {
  if (isOpen.value) {
    loadAdventures()
  }
})

// Watch for dialog open
watch(() => isOpen.value, (newVal) => {
  if (newVal) {
    loadAdventures()
  }
})
</script>

<style scoped>
.v-data-table {
  border-radius: 8px;
}

.v-card-title {
  padding: 20px 24px 16px;
}

.v-card-text {
  padding: 16px 24px 24px;
}
</style>
