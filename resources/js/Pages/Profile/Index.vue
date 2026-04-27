<template>
  <div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">User Profile</h1>

    <div class="bg-white shadow rounded-lg p-6">
      <h2 class="text-lg font-semibold mb-4">Database Connection Settings</h2>

      <form @submit.prevent="updateConnection" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Select Database Connection</label>
          <select v-model="form.connection" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="local">Local Database</option>
            <option value="cloud">Cloud Database</option>
          </select>
          <p class="mt-2 text-sm text-gray-600">Current connection: <span class="font-semibold">{{ form.connection.toUpperCase() }}</span></p>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
        >
          {{ loading ? 'Updating...' : 'Update Connection' }}
        </button>
      </form>

      <div v-if="successMessage || flash.success" class="mt-4 p-4 bg-green-100 text-green-700 rounded">
        {{ successMessage || flash.success }}
      </div>
      
      <div v-if="errorMessage" class="mt-4 p-4 bg-red-100 text-red-700 rounded">
        {{ errorMessage }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  currentConnection: String,
  flash: {
    type: Object,
    default: () => ({}),
  },
})

const form = ref({
  connection: props.currentConnection || 'cloud',
})

const loading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const updateConnection = () => {
  loading.value = true
  successMessage.value = ''
  errorMessage.value = ''

  router.post('/profile/connection', form.value, {
    onSuccess: () => {
      successMessage.value = 'Database connection updated successfully.'
      loading.value = false
      // Update form value with the new connection from props
      form.value.connection = props.currentConnection || 'cloud'
    },
    onError: (errors) => {
      loading.value = false
      errorMessage.value = 'Failed to update database connection. Please try again.'
      console.error('Update error:', errors)
    },
  })
}

// Watch for changes in props.currentConnection and update form
watch(() => props.currentConnection, (newConnection) => {
  if (newConnection) {
    form.value.connection = newConnection
  }
})

onMounted(() => {
  // Ensure form is initialized with current connection
  if (props.currentConnection) {
    form.value.connection = props.currentConnection
  }
})
</script>