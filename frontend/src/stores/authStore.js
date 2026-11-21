import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'))
  const token = ref(localStorage.getItem('token') || null)

  // Getters
  const isAuthenticated = computed(() => !!token.value)
  const role = computed(() => user.value?.role || null)
  const isAdmin = computed(() => role.value === 'admin')
  const isRecruiter = computed(() => role.value === 'recruiter')
  const isCandidate = computed(() => role.value === 'candidate')

  // Actions
  async function login(credentials) {
    const res = await api.post('/auth/login', credentials)
    setAuth(res.data.data)
    return res.data
  }

  async function register(data) {
    const res = await api.post('/auth/register', data)
    setAuth(res.data.data)
    return res.data
  }

  async function logout() {
    try {
      await api.post('/auth/logout')
    } catch {
      // Ignore errors — token may already be invalid
    }
    clearAuth()
  }

  async function fetchUser() {
    try {
      const res = await api.get('/auth/me')
      user.value = res.data.data
      localStorage.setItem('user', JSON.stringify(user.value))
    } catch {
      clearAuth()
    }
  }

  function setAuth(data) {
    token.value = data.token
    user.value = data.user
    localStorage.setItem('token', data.token)
    localStorage.setItem('user', JSON.stringify(data.user))
  }

  function clearAuth() {
    token.value = null
    user.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }

  return {
    user, token,
    isAuthenticated, role, isAdmin, isRecruiter, isCandidate,
    login, register, logout, fetchUser, clearAuth,
  }
})

