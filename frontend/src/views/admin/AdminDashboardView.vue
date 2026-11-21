<template>
  <div class="container py-4">
    <!-- Welcome Header Strip -->
    <div class="welcome-header-card p-4 mb-4">
      <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
          <div class="header-avatar-circle">{{ userInitials }}</div>
          <div>
            <div class="d-flex align-items-center gap-2 mb-1">
              <h3 class="fw-bold mb-0">Welcome back, {{ authStore.user?.name || 'Administrator' }}</h3>
              <span class="badge" style="background:#fee2e2;color:#b91c1c;border:1px solid #fecaca;font-size:0.7rem;font-weight:700;">Admin</span>
            </div>
            <p class="mb-0 small" style="color:#6b7280;">
              Platform administration, job moderation queue, and user management control center.
            </p>
          </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
          <button class="btn btn-primary btn-sm fw-semibold px-3" @click="switchTab('jobs')">
            <i class="bi bi-check2-circle me-1"></i>Moderate Jobs ({{ stats.pending_jobs ?? 0 }})
          </button>
          <button class="btn btn-light btn-sm fw-semibold px-3" @click="switchTab('users')">
            <i class="bi bi-people me-1"></i>Users Directory
          </button>
        </div>
      </div>
    </div>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary"></div>
    </div>

    <template v-else>
      <div class="row g-3 mb-4">
        <div class="col-6 col-lg-2">
          <div class="card border h-100 p-3">
            <i class="bi bi-people-fill text-primary fs-4 mb-1"></i>
            <h4 class="fw-bold mb-0">{{ stats.total_candidates ?? 0 }}</h4>
            <p class="text-muted small mb-0">Candidates</p>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <div class="card border h-100 p-3">
            <i class="bi bi-building-fill text-primary fs-4 mb-1"></i>
            <h4 class="fw-bold mb-0">{{ stats.total_recruiters ?? 0 }}</h4>
            <p class="text-muted small mb-0">Recruiters</p>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <div class="card border h-100 p-3">
            <i class="bi bi-briefcase-fill text-primary fs-4 mb-1"></i>
            <h4 class="fw-bold mb-0">{{ stats.total_jobs ?? 0 }}</h4>
            <p class="text-muted small mb-0">Total Jobs</p>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <div class="card border h-100 p-3">
            <i class="bi bi-check-circle-fill text-primary fs-4 mb-1"></i>
            <h4 class="fw-bold mb-0">{{ stats.active_jobs ?? 0 }}</h4>
            <p class="text-muted small mb-0">Active Jobs</p>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <div class="card border h-100 p-3">
            <i class="bi bi-clock-fill text-primary fs-4 mb-1"></i>
            <h4 class="fw-bold mb-0">{{ stats.pending_jobs ?? 0 }}</h4>
            <p class="text-muted small mb-0">Pending Jobs</p>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <div class="card border h-100 p-3">
            <i class="bi bi-file-earmark-fill text-primary fs-4 mb-1"></i>
            <h4 class="fw-bold mb-0">{{ stats.total_applications ?? 0 }}</h4>
            <p class="text-muted small mb-0">Applications</p>
          </div>
        </div>
      </div>

      <!-- Pure-CSS Visual Analytics Card: Platform Ecosystem Overview -->
      <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div>
            <h6 class="fw-bold mb-0 text-dark">
              <i class="bi bi-bar-chart-line text-primary me-2"></i>Platform Ecosystem Overview
            </h6>
            <small class="text-muted">Visual distribution between moderation queues and user communities.</small>
          </div>
          <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-semibold">
            <i class="bi bi-activity me-1"></i>Live Ratio
          </span>
        </div>

        <div class="row g-4">
          <!-- Job Moderation Health: Active vs Pending -->
          <div class="col-md-6">
            <div class="p-3 rounded-3 bg-light border">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-bold text-secondary">Job Moderation Ratio</span>
                <span class="small text-muted">{{ totalModerationJobs }} Jobs Tracked</span>
              </div>

              <div class="progress rounded-pill overflow-hidden mb-2" style="height: 12px;">
                <div class="progress-bar bg-success" :style="{ width: activeJobsPct + '%' }" :title="'Active: ' + (stats.active_jobs ?? 0)"></div>
                <div class="progress-bar bg-warning text-dark" :style="{ width: pendingJobsPct + '%' }" :title="'Pending: ' + (stats.pending_jobs ?? 0)"></div>
              </div>

              <div class="d-flex justify-content-between small text-muted">
                <span class="d-flex align-items-center gap-1">
                  <span class="rounded-circle d-inline-block bg-success" style="width: 8px; height: 8px;"></span>
                  Active: <strong>{{ stats.active_jobs ?? 0 }}</strong> ({{ activeJobsPct }}%)
                </span>
                <span class="d-flex align-items-center gap-1">
                  <span class="rounded-circle d-inline-block bg-warning" style="width: 8px; height: 8px;"></span>
                  Pending Moderation: <strong>{{ stats.pending_jobs ?? 0 }}</strong> ({{ pendingJobsPct }}%)
                </span>
              </div>
            </div>
          </div>

          <!-- User Base Composition: Candidates vs Recruiters -->
          <div class="col-md-6">
            <div class="p-3 rounded-3 bg-light border">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-bold text-secondary">Community Demographics</span>
                <span class="small text-muted">{{ totalPlatformUsers }} Total Users</span>
              </div>

              <div class="progress rounded-pill overflow-hidden mb-2" style="height: 12px;">
                <div class="progress-bar bg-primary" :style="{ width: candidatesPct + '%' }" :title="'Candidates: ' + (stats.total_candidates ?? 0)"></div>
                <div class="progress-bar bg-info" :style="{ width: recruitersPct + '%' }" :title="'Recruiters: ' + (stats.total_recruiters ?? 0)"></div>
              </div>

              <div class="d-flex justify-content-between small text-muted">
                <span class="d-flex align-items-center gap-1">
                  <span class="rounded-circle d-inline-block bg-primary" style="width: 8px; height: 8px;"></span>
                  Candidates: <strong>{{ stats.total_candidates ?? 0 }}</strong> ({{ candidatesPct }}%)
                </span>
                <span class="d-flex align-items-center gap-1">
                  <span class="rounded-circle d-inline-block bg-info" style="width: 8px; height: 8px;"></span>
                  Recruiters: <strong>{{ stats.total_recruiters ?? 0 }}</strong> ({{ recruitersPct }}%)
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <ul class="nav nav-tabs mb-4" id="adminTabs">
        <li class="nav-item">
          <button :class="['nav-link', activeTab === 'jobs' ? 'active fw-semibold' : '']" @click="switchTab('jobs')">
            <i class="bi bi-briefcase me-1"></i>Job Approvals
            <span v-if="pendingJobs.length" class="badge bg-warning text-dark ms-1">{{ pendingJobs.length }}</span>
          </button>
        </li>
        <li class="nav-item">
          <button :class="['nav-link', activeTab === 'users' ? 'active fw-semibold' : '']" @click="switchTab('users')">
            <i class="bi bi-people me-1"></i>Users
          </button>
        </li>
        <li class="nav-item">
          <button :class="['nav-link', activeTab === 'categories' ? 'active fw-semibold' : '']" @click="switchTab('categories')">
            <i class="bi bi-tags me-1"></i>Categories
          </button>
        </li>
        <li class="nav-item">
          <button :class="['nav-link', activeTab === 'skills' ? 'active fw-semibold' : '']" @click="switchTab('skills')">
            <i class="bi bi-tools me-1"></i>Skills
          </button>
        </li>
      </ul>

      <!-- ── TAB: Job Approvals ── -->
      <div v-if="activeTab === 'jobs'">
        <div v-if="tabLoading" class="text-center py-4"><div class="spinner-border text-primary"></div></div>
        <div v-else-if="pendingJobs.length === 0" class="text-center py-5 text-muted">
          <i class="bi bi-check-all fs-1 d-block mb-2"></i>All caught up! No pending jobs.
        </div>
        <div v-else class="card border-0 shadow-sm rounded-4">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th class="px-4">Job Title</th>
                  <th>Company</th>
                  <th>Category</th>
                  <th>Posted</th>
                  <th class="px-4">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="job in pendingJobs" :key="job.id">
                  <td class="px-4">
                    <div class="fw-semibold">{{ job.title }}</div>
                    <div class="text-muted small">{{ formatJobType(job.job_type) }} · {{ job.location }}</div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="avatar-initials-sm" :style="{ background: avatarColor(job.company?.name) }">
                        {{ (job.company?.name || 'C')[0].toUpperCase() }}
                      </div>
                      <span class="fw-semibold">{{ job.company?.name || '—' }}</span>
                    </div>
                  </td>
                  <td>{{ job.category?.name || '—' }}</td>
                  <td><span class="text-muted small">{{ formatDate(job.created_at) }}</span></td>
                  <td class="px-4">
                    <div class="d-flex gap-1">
                      <button class="btn btn-sm btn-success" @click="approveJob(job)">
                        <i class="bi bi-check-lg me-1"></i>Approve
                      </button>
                      <button class="btn btn-sm btn-danger" @click="rejectJob(job)">
                        <i class="bi bi-x-lg me-1"></i>Reject
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ── TAB: Users ── -->
      <div v-if="activeTab === 'users'">
        <div class="row g-2 mb-3">
          <div class="col-md-4">
            <select v-model="userRoleFilter" class="form-select form-select-sm" @change="fetchUsers">
              <option value="">All Roles</option>
              <option value="candidate">Candidates</option>
              <option value="recruiter">Recruiters</option>
              <option value="admin">Admins</option>
            </select>
          </div>
          <div class="col-md-5">
            <input v-model="userSearch" type="text" class="form-control form-control-sm" placeholder="Search by name or email..." />
          </div>
        </div>
        <div v-if="tabLoading" class="text-center py-4"><div class="spinner-border text-primary"></div></div>
        <div v-else class="card border-0 shadow-sm rounded-4">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th class="px-4">Name</th>
                  <th>Role</th>
                  <th>Joined</th>
                  <th>Status</th>
                  <th class="px-4">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="u in filteredUsers" :key="u.id">
                  <td class="px-4">
                    <div class="d-flex align-items-center gap-2">
                      <div class="avatar-initials-sm" :style="{ background: avatarColor(u.name) }">
                        {{ (u.name || 'U')[0].toUpperCase() }}
                      </div>
                      <div>
                        <div class="fw-semibold text-dark">{{ u.name }}</div>
                        <div class="text-muted small">{{ u.email }}</div>
                      </div>
                    </div>
                  </td>
                  <td><span :class="roleBadge(u.role)" class="badge">{{ capitalizeFirst(u.role) }}</span></td>
                  <td><span class="text-muted small">{{ formatDate(u.created_at) }}</span></td>
                  <td>
                    <span class="d-flex align-items-center gap-1">
                      <span
                        class="rounded-circle d-inline-block"
                        :style="{ width: '8px', height: '8px', background: u.status === 'banned' ? '#dc3545' : '#198754' }"
                      ></span>
                      <span :class="u.status === 'banned' ? 'text-danger fw-semibold' : 'text-success fw-semibold'">
                        {{ u.status === 'banned' ? 'Banned' : 'Active' }}
                      </span>
                    </span>
                  </td>
                  <td class="px-4">
                    <button
                      v-if="u.role !== 'admin'"
                      :class="u.status === 'banned' ? 'btn-outline-success' : 'btn-outline-danger'"
                      class="btn btn-sm"
                      @click="toggleBan(u)"
                    >
                      <i :class="u.status === 'banned' ? 'bi bi-unlock' : 'bi bi-lock'"></i>
                      {{ u.status === 'banned' ? 'Unban' : 'Ban' }}
                    </button>
                    <span v-else class="text-muted small">—</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ── TAB: Categories ── -->
      <div v-if="activeTab === 'categories'">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-muted small">{{ categories.length }} categories</span>
          <button class="btn btn-primary btn-sm" @click="openCatModal()">
            <i class="bi bi-plus-circle me-1"></i>Add Category
          </button>
        </div>
        <div v-if="tabLoading" class="text-center py-4"><div class="spinner-border text-primary"></div></div>
        <div v-else class="card border-0 shadow-sm rounded-4">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th class="px-4">Name</th>
                  <th>Slug</th>
                  <th>Jobs</th>
                  <th class="px-4">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="cat in categories" :key="cat.id">
                  <td class="px-4 fw-semibold">{{ cat.name }}</td>
                  <td><code class="text-muted small">{{ cat.slug }}</code></td>
                  <td><span class="badge bg-info text-dark">{{ cat.jobs_count ?? 0 }}</span></td>
                  <td class="px-4">
                    <div class="d-flex gap-1">
                      <button class="btn btn-sm btn-outline-primary" @click="openCatModal(cat)"><i class="bi bi-pencil"></i></button>
                      <button class="btn btn-sm btn-outline-danger" @click="deleteCat(cat)"><i class="bi bi-trash"></i></button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ── TAB: Skills ── -->
      <div v-if="activeTab === 'skills'">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-muted small">{{ skills.length }} skills</span>
          <button class="btn btn-primary btn-sm" @click="openSkillModal()">
            <i class="bi bi-plus-circle me-1"></i>Add Skill
          </button>
        </div>
        <div v-if="tabLoading" class="text-center py-4"><div class="spinner-border text-primary"></div></div>
        <div v-else class="row g-2">
          <div v-for="skill in skills" :key="skill.id" class="col-auto">
            <div class="badge bg-primary bg-opacity-10 text-primary d-flex align-items-center gap-2 p-2 fs-6">
              {{ skill.name }}
              <button class="btn btn-sm p-0 border-0 bg-transparent text-primary" @click="openSkillModal(skill)" title="Edit">
                <i class="bi bi-pencil-fill" style="font-size:0.65rem"></i>
              </button>
              <button class="btn btn-sm p-0 border-0 bg-transparent text-danger" @click="deleteSkill(skill)" title="Delete">
                <i class="bi bi-x-circle-fill" style="font-size:0.75rem"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Category Modal -->
    <div class="modal fade" id="catModal" tabindex="-1" ref="catModalRef">
      <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0">
          <div class="modal-header">
            <h5 class="modal-title fw-bold">{{ catEditing ? 'Edit Category' : 'Add Category' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <div v-if="catError" class="alert alert-danger">{{ catError }}</div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Category Name</label>
              <input v-model="catForm.name" type="text" class="form-control" required placeholder="e.g. Software Engineering" />
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" @click="saveCat" :disabled="catSaving">
              <span v-if="catSaving" class="spinner-border spinner-border-sm me-2"></span>
              {{ catEditing ? 'Save Changes' : 'Add Category' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Skill Modal -->
    <div class="modal fade" id="skillModal" tabindex="-1" ref="skillModalRef">
      <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0">
          <div class="modal-header">
            <h5 class="modal-title fw-bold">{{ skillEditing ? 'Edit Skill' : 'Add Skill' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <div v-if="skillError" class="alert alert-danger">{{ skillError }}</div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Skill Name</label>
              <input v-model="skillForm.name" type="text" class="form-control" required placeholder="e.g. TypeScript" />
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" @click="saveSkill" :disabled="skillSaving">
              <span v-if="skillSaving" class="spinner-border spinner-border-sm me-2"></span>
              {{ skillEditing ? 'Save Changes' : 'Add Skill' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Modal } from 'bootstrap'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const loading = ref(true)
const tabLoading = ref(false)
const stats = ref({})
const activeTab = ref('jobs')

const userInitials = computed(() => {
  const name = authStore.user?.name || 'Admin'
  const parts = name.trim().split(' ')
  if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase()
  return name.slice(0, 2).toUpperCase()
})

// Visual Distribution Computeds
const totalModerationJobs = computed(() => {
  return (stats.value.active_jobs ?? 0) + (stats.value.pending_jobs ?? 0)
})

const activeJobsPct = computed(() => {
  if (!totalModerationJobs.value) return 50
  return Math.round(((stats.value.active_jobs ?? 0) / totalModerationJobs.value) * 100)
})

const pendingJobsPct = computed(() => {
  if (!totalModerationJobs.value) return 50
  return 100 - activeJobsPct.value
})

const totalPlatformUsers = computed(() => {
  return (stats.value.total_candidates ?? 0) + (stats.value.total_recruiters ?? 0)
})

const candidatesPct = computed(() => {
  if (!totalPlatformUsers.value) return 50
  return Math.round(((stats.value.total_candidates ?? 0) / totalPlatformUsers.value) * 100)
})

const recruitersPct = computed(() => {
  if (!totalPlatformUsers.value) return 50
  return 100 - candidatesPct.value
})

function avatarColor(name) {
  if (!name) return '#4f46e5'
  const colors = ['#4f46e5', '#0284c7', '#059669', '#d97706', '#dc2626', '#7c3aed', '#db2777', '#0d9488']
  let hash = 0
  for (let i = 0; i < name.length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash)
  return colors[Math.abs(hash) % colors.length]
}

// Watch route query for tab switches from workspace subnav
watch(() => route.query.tab, (newTab) => {
  if (newTab && ['jobs', 'users', 'categories', 'skills'].includes(newTab)) {
    switchTab(newTab)
  }
})

// --- Jobs ---
const pendingJobs = ref([])

// --- Users ---
const users = ref([])
const userRoleFilter = ref('')
const userSearch = ref('')

// --- Categories ---
const categories = ref([])
const catModalRef = ref(null)
let bsCatModal = null
const catEditing = ref(null)
const catForm = ref({ name: '' })
const catSaving = ref(false)
const catError = ref('')

// --- Skills ---
const skills = ref([])
const skillModalRef = ref(null)
let bsSkillModal = null
const skillEditing = ref(null)
const skillForm = ref({ name: '' })
const skillSaving = ref(false)
const skillError = ref('')

onMounted(async () => {
  try {
    const res = await api.get('/admin/dashboard-stats')
    stats.value = res.data.data || {}
  } catch {
    stats.value = {}
  } finally {
    loading.value = false
  }
  const initialTab = route.query.tab && ['jobs', 'users', 'categories', 'skills'].includes(route.query.tab)
    ? route.query.tab
    : 'jobs'
  activeTab.value = initialTab
  await fetchTabData(initialTab)
})

async function switchTab(tab) {
  activeTab.value = tab
  await fetchTabData(tab)
}

async function fetchTabData(tab) {
  tabLoading.value = true
  try {
    if (tab === 'jobs') {
      const res = await api.get('/admin/jobs', { params: { status: 'pending' } })
      // Laravel paginator wraps items in .data
      pendingJobs.value = res.data.data?.data || res.data.data || []
    } else if (tab === 'users') {
      await fetchUsers()
    } else if (tab === 'categories') {
      const res = await api.get('/admin/categories')
      categories.value = res.data.data || []
    } else if (tab === 'skills') {
      const res = await api.get('/admin/skills')
      skills.value = res.data.data || []
    }
  } catch {
    // ignore
  } finally {
    tabLoading.value = false
  }
}

async function fetchUsers() {
  tabLoading.value = true
  try {
    const params = userRoleFilter.value ? { role: userRoleFilter.value } : {}
    const res = await api.get('/admin/users', { params })
    // Laravel paginator wraps items in .data
    const payload = res.data.data
    users.value = payload?.data || payload || []
  } catch {
    users.value = []
  } finally {
    tabLoading.value = false
  }
}

const filteredUsers = computed(() => {
  const kw = userSearch.value.toLowerCase()
  if (!kw) return users.value
  return users.value.filter(u =>
    u.name?.toLowerCase().includes(kw) || u.email?.toLowerCase().includes(kw)
  )
})

// Job Actions
async function approveJob(job) {
  try {
    await api.patch(`/admin/jobs/${job.id}/status`, { status: 'active' })
    pendingJobs.value = pendingJobs.value.filter(j => j.id !== job.id)
    if (stats.value.pending_jobs) stats.value.pending_jobs--
    if (stats.value.active_jobs !== undefined) stats.value.active_jobs++
  } catch {
    alert('Failed to approve job.')
  }
}

async function rejectJob(job) {
  if (!confirm(`Reject "${job.title}"?`)) return
  try {
    await api.patch(`/admin/jobs/${job.id}/status`, { status: 'rejected' })
    pendingJobs.value = pendingJobs.value.filter(j => j.id !== job.id)
    if (stats.value.pending_jobs) stats.value.pending_jobs--
  } catch {
    alert('Failed to reject job.')
  }
}

// User Actions
async function toggleBan(user) {
  const action = user.status === 'banned' ? 'unban' : 'ban'
  if (!confirm(`${capitalizeFirst(action)} user "${user.name}"?`)) return
  try {
    const res = await api.patch(`/admin/users/${user.id}/toggle-ban`)
    user.status = res.data.data?.status || (user.status === 'banned' ? 'active' : 'banned')
  } catch {
    alert('Failed to update user status.')
  }
}

// Category Actions
function openCatModal(cat = null) {
  catEditing.value = cat
  catForm.value.name = cat ? cat.name : ''
  catError.value = ''
  if (!bsCatModal) bsCatModal = new Modal(catModalRef.value)
  bsCatModal.show()
}

async function saveCat() {
  catSaving.value = true
  catError.value = ''
  try {
    if (catEditing.value) {
      const res = await api.put(`/admin/categories/${catEditing.value.id}`, catForm.value)
      const idx = categories.value.findIndex(c => c.id === catEditing.value.id)
      if (idx !== -1) categories.value[idx] = { ...categories.value[idx], ...res.data.data }
    } else {
      const res = await api.post('/admin/categories', catForm.value)
      categories.value.unshift(res.data.data)
    }
    bsCatModal.hide()
  } catch (err) {
    catError.value = err.response?.data?.message || 'Failed to save category.'
  } finally {
    catSaving.value = false
  }
}

async function deleteCat(cat) {
  if (!confirm(`Delete category "${cat.name}"?`)) return
  try {
    await api.delete(`/admin/categories/${cat.id}`)
    categories.value = categories.value.filter(c => c.id !== cat.id)
  } catch {
    alert('Failed to delete category.')
  }
}

// Skill Actions
function openSkillModal(skill = null) {
  skillEditing.value = skill
  skillForm.value.name = skill ? skill.name : ''
  skillError.value = ''
  if (!bsSkillModal) bsSkillModal = new Modal(skillModalRef.value)
  bsSkillModal.show()
}

async function saveSkill() {
  skillSaving.value = true
  skillError.value = ''
  try {
    if (skillEditing.value) {
      const res = await api.put(`/admin/skills/${skillEditing.value.id}`, skillForm.value)
      const idx = skills.value.findIndex(s => s.id === skillEditing.value.id)
      if (idx !== -1) skills.value[idx] = { ...skills.value[idx], ...res.data.data }
    } else {
      const res = await api.post('/admin/skills', skillForm.value)
      skills.value.unshift(res.data.data)
    }
    bsSkillModal.hide()
  } catch (err) {
    skillError.value = err.response?.data?.message || 'Failed to save skill.'
  } finally {
    skillSaving.value = false
  }
}

async function deleteSkill(skill) {
  if (!confirm(`Delete skill "${skill.name}"?`)) return
  try {
    await api.delete(`/admin/skills/${skill.id}`)
    skills.value = skills.value.filter(s => s.id !== skill.id)
  } catch {
    alert('Failed to delete skill.')
  }
}

function roleBadge(role) {
  const map = { admin: 'bg-danger', recruiter: 'bg-info text-dark', candidate: 'bg-success' }
  return map[role] || 'bg-secondary'
}
function capitalizeFirst(str) {
  return str ? str.charAt(0).toUpperCase() + str.slice(1) : ''
}
function formatJobType(t) {
  const map = { full_time: 'Full-time', part_time: 'Part-time', remote: 'Remote', internship: 'Internship' }
  return map[t] || (t ? t.replace('_', '-') : '—')
}
function formatDate(d) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}
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

.avatar-initials-sm {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  color: #ffffff;
  font-weight: 700;
  font-size: 0.85rem;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}
</style>

