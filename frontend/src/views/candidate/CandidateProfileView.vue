<template>
  <div class="container py-4">
    <!-- Welcome Header Strip -->
    <div class="welcome-header-card p-4 mb-4">
      <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
          <div class="header-avatar-circle">{{ userInitials }}</div>
          <div>
            <div class="d-flex align-items-center gap-2 mb-1">
              <h3 class="fw-bold mb-0">Welcome back, {{ authStore.user?.name || 'Candidate' }}</h3>
              <span class="badge" style="background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;font-size:0.7rem;font-weight:700;">Candidate</span>
            </div>
            <p class="mb-0 small" style="color:#6b7280;">
              Manage your personal credentials, resume documents, and profile visibility for employers.
            </p>
          </div>
        </div>
        <div class="d-flex gap-2">
          <router-link to="/candidate/applications" class="btn btn-primary btn-sm fw-semibold px-3">
            <i class="bi bi-file-earmark-text me-1"></i>My Applications
          </router-link>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <!-- Profile Form -->
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
          <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-person-circle text-primary me-2"></i>My Profile</h4>
            <p class="text-muted small mt-1">Keep your profile complete to attract the best opportunities.</p>
          </div>
          <div class="card-body p-4">

            <div v-if="successMsg" class="alert alert-success alert-dismissible fade show">
              <i class="bi bi-check-circle-fill me-2"></i>{{ successMsg }}
              <button type="button" class="btn-close" @click="successMsg = ''"></button>
            </div>
            <div v-if="errorMsg" class="alert alert-danger alert-dismissible fade show">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ errorMsg }}
              <button type="button" class="btn-close" @click="errorMsg = ''"></button>
            </div>

            <div v-if="loading" class="text-center py-5">
              <div class="spinner-border text-primary"></div>
              <p class="mt-2 text-muted">Loading your profile...</p>
            </div>

            <form v-else @submit.prevent="saveProfile" enctype="multipart/form-data">
              <h6 class="text-uppercase text-muted fw-semibold mb-3 small">Personal Information</h6>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Full Name</label>
                  <input v-model="form.name" type="text" class="form-control" required />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Phone Number</label>
                  <input v-model="form.phone" type="tel" class="form-control" placeholder="+1 234 567 8900" />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Date of Birth</label>
                  <input v-model="form.dob" type="date" class="form-control" />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Gender</label>
                  <select v-model="form.gender" class="form-select">
                    <option value="">Select gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other / Prefer not to say</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Address</label>
                  <input v-model="form.address" type="text" class="form-control" placeholder="City, Country" />
                </div>
              </div>

              <hr class="my-4" />
              <h6 class="text-uppercase text-muted fw-semibold mb-3 small">Professional Details</h6>

              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">Years of Experience</label>
                  <input v-model.number="form.experience_years" type="number" class="form-control" min="0" max="50" />
                </div>
                <div class="col-md-8">
                  <label class="form-label">Education</label>
                  <input v-model="form.education" type="text" class="form-control" placeholder="e.g. B.Sc. Computer Science, MIT" />
                </div>
                <div class="col-12">
                  <label class="form-label">Skills <span class="text-muted small">(comma separated)</span></label>
                  <input v-model="form.skills_text" type="text" class="form-control" placeholder="e.g. JavaScript, React, Node.js, SQL" />
                </div>
                <div class="col-12">
                  <label class="form-label">Professional Summary / Bio</label>
                  <textarea v-model="form.bio" class="form-control" rows="4" placeholder="Tell employers about yourself, your experience, and goals..."></textarea>
                </div>
              </div>

              <hr class="my-4" />
              <h6 class="text-uppercase text-muted fw-semibold mb-3 small">Documents & Avatar</h6>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Profile Photo <span class="text-muted small">(jpg/png/webp, max 2MB)</span></label>
                  <input @change="onAvatarChange" type="file" class="form-control" accept="image/jpeg,image/png,image/webp" ref="avatarInput" />
                  <div v-if="avatarPreview" class="mt-2">
                    <img :src="avatarPreview" class="rounded-circle" width="80" height="80" style="object-fit:cover" />
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Default CV / Resume <span class="text-muted small">(PDF, max 5MB)</span></label>
                  <input @change="onCvChange" type="file" class="form-control" accept="application/pdf" ref="cvInput" />
                  <div v-if="profile?.default_cv_path" class="mt-2">
                    <a :href="profile.default_cv_path" target="_blank" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-file-earmark-pdf me-1"></i>View Current CV
                    </a>
                  </div>
                </div>
              </div>

              <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary px-4" :disabled="saving">
                  <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
                  <i v-else class="bi bi-save me-2"></i>
                  {{ saving ? 'Saving...' : 'Save Profile' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <!-- Profile Completion -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
          <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-graph-up text-primary me-2"></i>Profile Strength</h6>
            <div class="progress mb-2" style="height:10px">
              <div
                class="progress-bar"
                :class="completeness >= 80 ? 'bg-success' : completeness >= 40 ? 'bg-warning' : 'bg-danger'"
                :style="{ width: completeness + '%' }"
              ></div>
            </div>
            <p class="text-muted small mb-0">
              <strong>{{ completeness }}%</strong> complete
              <span v-if="completeness < 100" class="ms-1">— keep going!</span>
              <span v-else class="ms-1 text-success">— great job!</span>
            </p>

            <ul class="list-unstyled mt-3 small">
              <li v-for="item in completenessItems" :key="item.label">
                <i :class="item.done ? 'bi bi-check-circle-fill text-success' : 'bi bi-circle text-muted'" class="me-2"></i>
                <span :class="{ 'text-muted': !item.done }">{{ item.label }}</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="card border-0 shadow-sm rounded-4">
          <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-lightning-charge text-warning me-2"></i>Quick Links</h6>
            <div class="d-grid gap-2">
              <router-link to="/candidate/applications" class="btn btn-outline-primary btn-sm text-start">
                <i class="bi bi-file-earmark-text me-2"></i>My Applications
              </router-link>
              <router-link to="/candidate/saved-jobs" class="btn btn-outline-primary btn-sm text-start">
                <i class="bi bi-bookmark-heart me-2"></i>Saved Jobs
              </router-link>
              <router-link to="/jobs" class="btn btn-outline-secondary btn-sm text-start">
                <i class="bi bi-search me-2"></i>Browse Jobs
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'
import { useAuthStore } from '@/stores/authStore'

const authStore = useAuthStore()
const loading = ref(true)
const saving = ref(false)
const successMsg = ref('')
const errorMsg = ref('')
const profile = ref(null)
const avatarPreview = ref(null)
const avatarFile = ref(null)
const cvFile = ref(null)

function showSuccess(msg) {
  successMsg.value = msg
  setTimeout(() => { successMsg.value = '' }, 4000)
}
function showError(msg) {
  errorMsg.value = msg
  setTimeout(() => { errorMsg.value = '' }, 5000)
}

const form = ref({
  name: authStore.user?.name || '',
  phone: '',
  dob: '',
  gender: '',
  address: '',
  bio: '',
  education: '',
  experience_years: 0,
  skills_text: '',
})

onMounted(async () => {
  try {
    const res = await api.get('/candidate/profile')
    // API returns { user: {...}, profile: {...} }
    const data = res.data.data || {}
    const u = data.user || {}
    const p = data.profile || {}
    profile.value = { ...p, user: u }

    form.value.name = u.name || authStore.user?.name || ''
    form.value.phone = u.phone || ''
    form.value.dob = p.dob || ''
    form.value.gender = p.gender || ''
    form.value.address = p.address || ''
    form.value.bio = p.bio || ''
    form.value.education = p.education || ''
    form.value.experience_years = p.experience_years || 0
    form.value.skills_text = p.skills_text || ''
    // Avatar already a full URL from API
    if (u.avatar) avatarPreview.value = u.avatar
  } catch {
    errorMsg.value = 'Failed to load profile data.'
  } finally {
    loading.value = false
  }
})

function onAvatarChange(e) {
  const file = e.target.files[0]
  if (!file) return
  if (file.size > 2 * 1024 * 1024) { showError('Avatar must be under 2MB.'); return }
  avatarFile.value = file
  avatarPreview.value = URL.createObjectURL(file)
}

function onCvChange(e) {
  const file = e.target.files[0]
  if (!file) return
  if (file.size > 5 * 1024 * 1024) { showError('CV must be under 5MB.'); return }
  cvFile.value = file
}

async function saveProfile() {
  saving.value = true
  successMsg.value = ''
  errorMsg.value = ''
  try {
    const fd = new FormData()
    Object.entries(form.value).forEach(([k, v]) => { if (v !== '' && v !== null && v !== undefined) fd.append(k, v) })
    if (avatarFile.value) fd.append('avatar', avatarFile.value)
    if (cvFile.value) fd.append('default_cv', cvFile.value)

    const res = await api.post('/candidate/profile', fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    profile.value = res.data.data
    showSuccess('Profile saved successfully!')
    // Refresh user data from updated response
    const updData = res.data.data || {}
    const updUser = updData.user || {}
    const updProfile = updData.profile || {}
    profile.value = { ...updProfile, user: updUser }
    // Update avatar preview if returned as full URL
    if (updUser.avatar) avatarPreview.value = updUser.avatar
    // Update authStore user name
    if (authStore.user) {
      authStore.user.name = form.value.name
      localStorage.setItem('user', JSON.stringify(authStore.user))
    }
    avatarFile.value = null
    cvFile.value = null
  } catch (err) {
    const errors = err.response?.data?.errors
    errorMsg.value = errors ? Object.values(errors).flat().join(' ') : (err.response?.data?.message || 'Failed to save profile.')
  } finally {
    saving.value = false
  }
}

const completenessItems = computed(() => [
  { label: 'Full name', done: !!form.value.name },
  { label: 'Phone number', done: !!form.value.phone },
  { label: 'Date of birth', done: !!form.value.dob },
  { label: 'Bio / Summary', done: !!form.value.bio },
  { label: 'Education', done: !!form.value.education },
  { label: 'Skills listed', done: !!form.value.skills_text },
  { label: 'CV uploaded', done: !!profile.value?.default_cv_path },
])

const completeness = computed(() => {
  const done = completenessItems.value.filter(i => i.done).length
  return Math.round((done / completenessItems.value.length) * 100)
})

const userInitials = computed(() => {
  const name = authStore.user?.name || form.value?.name || 'Candidate'
  const parts = name.trim().split(' ')
  if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase()
  return name.slice(0, 2).toUpperCase()
})
</script>

<style scoped>
.welcome-header-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
}

.header-avatar-circle {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: #eff6ff;
  color: #2563eb;
  font-weight: 700;
  font-size: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #bfdbfe;
  flex-shrink: 0;
}
</style>

