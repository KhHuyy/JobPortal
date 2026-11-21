import axios from 'axios'
import router from '@/router'

/**
 * Centralized Axios instance for the JobConnect API.
 * - Base URL points to the Laravel backend via Vite proxy.
 * - Request Interceptor: injects Bearer token from localStorage.
 * - Response Interceptor: handles 401/403 by clearing auth and redirecting.
 */
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// Request Interceptor — inject Bearer Token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => Promise.reject(error)
)

// Response Interceptor — handle auth errors globally
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response) {
      const status = error.response.status

      if (status === 401) {
        // Token expired or invalid — clear auth and redirect to login
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        router.push({ name: 'Login' })
      }

      if (status === 403) {
        // Forbidden — redirect to home
        router.push({ name: 'Home' })
      }
    }
    return Promise.reject(error)
  }
)

export default api

