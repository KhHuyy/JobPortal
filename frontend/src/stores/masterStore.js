import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

const DEFAULT_CATEGORIES = [
  { id: 1, name: 'Software Engineering', slug: 'software-engineering', icon: 'bi-code-slash' },
  { id: 2, name: 'Data & AI', slug: 'data-ai', icon: 'bi-cpu' },
  { id: 3, name: 'Product & Design', slug: 'product-design', icon: 'bi-palette' },
  { id: 4, name: 'DevOps & Cloud', slug: 'devops-cloud', icon: 'bi-cloud-check' },
  { id: 5, name: 'QA & Testing', slug: 'qa-testing', icon: 'bi-shield-check' },
  { id: 6, name: 'Mobile Development', slug: 'mobile-development', icon: 'bi-phone' },
  { id: 7, name: 'Cybersecurity', slug: 'cybersecurity', icon: 'bi-lock' },
  { id: 8, name: 'Finance & Banking', slug: 'finance-banking', icon: 'bi-bank' },
]

const DEFAULT_CITIES = ['Ho Chi Minh City', 'Hanoi', 'Da Nang', 'Can Tho', 'Remote']

export const useMasterStore = defineStore('master', () => {
  const categories = ref([...DEFAULT_CATEGORIES])
  const skills = ref([])
  const cities = ref([...DEFAULT_CITIES])
  const loaded = ref(false)

  async function fetchMasterData() {
    if (loaded.value) return
    try {
      const res = await api.get('/master-data')
      if (res.data.data?.categories?.length) {
        categories.value = res.data.data.categories
      }
      if (res.data.data?.skills?.length) {
        skills.value = res.data.data.skills
      }
      if (res.data.data?.cities?.length) {
        cities.value = res.data.data.cities
      }
      loaded.value = true
    } catch (err) {
      console.warn('Backend API master-data unreachable, using verified fallback data.', err.message)
      // Keep DEFAULT_CATEGORIES and DEFAULT_CITIES intact
      loaded.value = true
    }
  }

  return { categories, skills, cities, loaded, fetchMasterData }
})


