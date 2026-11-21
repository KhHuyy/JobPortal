<template>
  <div class="auth-split">
    <!-- Left Panel: Brand + Feature list -->
    <div class="auth-left">
      <router-link to="/" class="auth-brand">
        <i class="bi bi-briefcase-fill me-2"></i>JobConnect
      </router-link>

      <div class="auth-left-body">
        <h2 class="auth-headline">Modern Hiring &amp;<br>Applicant Tracking</h2>
        <p class="auth-subline">The focused ATS built for technical teams.</p>

        <ul class="auth-features">
          <li>
            <i class="bi bi-check-circle-fill"></i>
            <span>Structured Candidate Pipeline</span>
          </li>
          <li>
            <i class="bi bi-check-circle-fill"></i>
            <span>Verified Employer Workspaces</span>
          </li>
          <li>
            <i class="bi bi-check-circle-fill"></i>
            <span>Instant PDF Resume Review</span>
          </li>
        </ul>
      </div>

      <p class="auth-left-footer">
        © {{ new Date().getFullYear() }} JobConnect
      </p>
    </div>

    <!-- Right Panel: Form -->
    <div class="auth-right">
      <div class="auth-form-wrap">
        <h1 class="auth-form-title">Sign in to JobConnect</h1>
        <p class="auth-form-sub">
          No account?
          <router-link to="/register" class="auth-link">Create one free</router-link>
        </p>

        <div v-if="errorMsg" class="auth-error">
          <i class="bi bi-exclamation-circle me-2"></i>{{ errorMsg }}
          <button type="button" class="auth-error-close" @click="errorMsg = ''">&times;</button>
        </div>

        <form @submit.prevent="handleLogin" novalidate>
          <div class="field-group">
            <label class="field-label" for="email">Email address</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              class="field-input"
              placeholder="you@example.com"
              required
              autocomplete="email"
            />
          </div>

          <div class="field-group">
            <label class="field-label" for="password">Password</label>
            <div class="field-password">
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                class="field-input"
                placeholder="Enter your password"
                required
                autocomplete="current-password"
              />
              <button
                type="button"
                class="field-eye"
                @click="showPassword = !showPassword"
                :aria-label="showPassword ? 'Hide password' : 'Show password'"
              >
                <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
              </button>
            </div>
          </div>

          <button type="submit" class="auth-submit" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
            {{ loading ? 'Signing in…' : 'Sign in' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({ email: '', password: '' })
const loading = ref(false)
const errorMsg = ref('')
const showPassword = ref(false)

async function handleLogin() {
  loading.value = true
  errorMsg.value = ''
  try {
    await authStore.login(form.value)
    const role = authStore.role
    if (role === 'admin') {
      router.push({ name: 'AdminDashboard' })
    } else if (role === 'recruiter') {
      router.push({ name: 'RecruiterDashboard' })
    } else {
      router.push({ name: 'CandidateProfile' })
    }
  } catch (err) {
    if (err.response?.data?.message) {
      errorMsg.value = err.response.data.message
    } else if (!err.response || err.code === 'ERR_NETWORK') {
      errorMsg.value = 'Không thể kết nối đến máy chủ Backend (Port 8000). Vui lòng kiểm tra lệnh: php artisan serve'
    } else {
      errorMsg.value = 'Email hoặc mật khẩu không chính xác. Vui lòng thử lại.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* ── Layout ───────────────────────────────────────────── */
.auth-split {
  display: flex;
  min-height: 100vh;
}

/* ── Left Panel ───────────────────────────────────────── */
.auth-left {
  width: 45%;
  background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 60%, #312e81 100%);
  color: #f1f5f9;
  display: flex;
  flex-direction: column;
  padding: 2.5rem 3rem;
  position: relative;
  overflow: hidden;
}

.auth-brand {
  font-size: 1.1rem;
  font-weight: 700;
  color: #f1f5f9;
  text-decoration: none;
  letter-spacing: -0.01em;
  display: inline-flex;
  align-items: center;
}
.auth-brand:hover { color: #94a3b8; }

.auth-left-body {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 2rem 0;
}

.auth-headline {
  font-size: 1.9rem;
  font-weight: 700;
  line-height: 1.25;
  color: #f8fafc;
  letter-spacing: -0.025em;
  margin-bottom: 0.75rem;
}

.auth-subline {
  color: #94a3b8;
  font-size: 0.95rem;
  margin-bottom: 2rem;
}

.auth-features {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
}
.auth-features li {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  font-size: 0.925rem;
  color: #cbd5e1;
}
.auth-features .bi-check-circle-fill {
  color: #818cf8;
  font-size: 0.95rem;
  flex-shrink: 0;
}

.auth-left-footer {
  font-size: 0.75rem;
  color: #475569;
  margin: 0;
}

/* ── Right Panel ──────────────────────────────────────── */
.auth-right {
  width: 55%;
  background-color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 3rem 2rem;
}

.auth-form-wrap {
  width: 100%;
  max-width: 400px;
}

.auth-form-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
  letter-spacing: -0.02em;
  margin-bottom: 0.4rem;
}

.auth-form-sub {
  font-size: 0.9rem;
  color: #6b7280;
  margin-bottom: 1.75rem;
}

.auth-link {
  color: #4f46e5;
  font-weight: 600;
  text-decoration: none;
}
.auth-link:hover { text-decoration: underline; color: #4338ca; }

/* Error banner */
.auth-error {
  display: flex;
  align-items: center;
  background: #fef2f2;
  border: 1px solid #fecaca;
  color: #b91c1c;
  border-radius: 8px;
  padding: 0.65rem 0.875rem;
  font-size: 0.875rem;
  margin-bottom: 1.25rem;
}
.auth-error-close {
  background: none;
  border: none;
  margin-left: auto;
  color: #b91c1c;
  font-size: 1.1rem;
  cursor: pointer;
  line-height: 1;
  padding: 0 0 0 0.5rem;
}

/* Fields */
.field-group {
  margin-bottom: 1.1rem;
}
.field-label {
  display: block;
  font-size: 0.85rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.4rem;
}
.field-input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.925rem;
  color: #0f172a;
  background: #ffffff;
  outline: none;
  transition: all 0.18s ease;
  box-sizing: border-box;
}
.field-input:focus {
  border-color: #4f46e5;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
}
.field-input::placeholder { color: #9ca3af; }

.field-password {
  position: relative;
}
.field-password .field-input {
  padding-right: 2.75rem;
}
.field-eye {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 0;
  font-size: 0.95rem;
  line-height: 1;
}
.field-eye:hover { color: #374151; }

/* Submit */
.auth-submit {
  width: 100%;
  padding: 12px;
  background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
  color: #ffffff;
  border: 1px solid #4f46e5;
  border-radius: 8px;
  font-size: 0.925rem;
  font-weight: 700;
  cursor: pointer;
  margin-top: 0.5rem;
  box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
  transition: all 0.18s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}
.auth-submit:hover:not(:disabled) {
  background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
  box-shadow: 0 6px 18px rgba(79, 70, 229, 0.5);
  transform: translateY(-1px);
}
.auth-submit:disabled { opacity: 0.6; cursor: not-allowed; }

/* ── Responsive ───────────────────────────────────────── */
@media (max-width: 768px) {
  .auth-split { flex-direction: column; }
  .auth-left  { width: 100%; padding: 2rem 1.5rem; min-height: auto; }
  .auth-right { width: 100%; padding: 2rem 1.5rem; }
  .auth-left-body { padding: 1.5rem 0; }
  .auth-headline { font-size: 1.5rem; }
}
</style>
