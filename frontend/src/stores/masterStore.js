import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useMasterStore = defineStore('master', () => {
  const categories = ref([])
  const skills = ref([])
  const cities = ref([])
  const loaded = ref(false)

  async function fetchMasterData() {
    if (loaded.value) return
    try {
      const res = await api.get('/master-data')
      categories.value = res.data.data.categories
      skills.value = res.data.data.skills
      cities.value = res.data.data.cities
      loaded.value = true
    } catch (err) {
      console.error('Failed to load master data:', err)
    }
  }

  return { categories, skills, cities, loaded, fetchMasterData }
})

