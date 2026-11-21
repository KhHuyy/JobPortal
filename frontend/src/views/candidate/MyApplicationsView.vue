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
              Track your submitted job applications, interview requests, and recruiter review notes in real-time.
            </p>
          </div>
        </div>
        <div>
          <router-link to="/jobs" class="btn btn-primary btn-sm fw-semibold px-3">
            <i class="bi bi-search me-1"></i>Find More Jobs
          </router-link>
        </div>
      </div>
    </div>

    <!-- Filters & Stats Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
      <div class="row g-2 align-items-center">
        <div class="col-md-4">
          <select v-model="filterStatus" class="form-select form-select-sm">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="reviewed">Reviewed</option>
            <option value="interview">Interview</option>
            <option value="offered">Offered</option>
            <option value="rejected">Rejected</option>
          </select>
        </div>
        <div class="col-md-5">
          <input v-model="searchKeyword" type="text" class="form-control form-control-sm" placeholder="Search by job title or company..." />
        </div>
        <div class="col-md-3 text-md-end">
          <span class="badge bg-light text-secondary border rounded-pill px-3 py-2 small">
            {{ filteredApplications.length }} application(s) found
          </span>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary"></div>
      <p class="mt-2 text-muted">Loading applications...</p>
    </div>

    <!-- Empty state -->
    <div v-else-if="filteredApplications.length === 0" class="text-center py-5">
      <i class="bi bi-inbox text-muted" style="font-size:3.5rem"></i>
      <h5 class="mt-3 fw-semibold">No Applications Found</h5>
      <p class="text-muted mb-4">You haven't submitted any applications matching this filter.</p>
      <router-link to="/jobs" class="btn btn-primary rounded-pill px-4">Browse Active Jobs</router-link>
    </div>

    <!-- Applications table -->
    <div v-else class="card border-0 shadow-sm rounded-4">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="px-4">Job Position</th>
              <th>Company</th>
              <th>Applied On</th>
              <th>Status</th>
              <th class="px-4">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="app in filteredApplications" :key="app.id">
              <td class="px-4">
                <div class="fw-semibold text-dark">{{ app.job?.title || 'N/A' }}</div>
                <div class="text-muted small">{{ formatJobType(app.job?.job_type) }} · {{ app.job?.location || '' }}</div>
              </td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <img
                    v-if="app.job?.company?.logo"
                    :src="app.job.company.logo"
                    width="32" height="32"
                    class="rounded-2"
                    style="object-fit:cover"
                  />
                  <div v-else class="avatar-initials-sm" :style="{ background: avatarColor(app.job?.company?.name) }">
                    {{ (app.job?.company?.name || 'C')[0].toUpperCase() }}
                  </div>
                  <span class="fw-semibold">{{ app.job?.company?.name || 'N/A' }}</span>
                </div>
              </td>
              <td>
                <span class="text-muted small">{{ formatDate(app.created_at) }}</span>
              </td>
              <td>
                <span :class="statusBadge(app.status)" class="badge">
                  {{ capitalizeFirst(app.status) }}
                </span>
              </td>
              <td class="px-4">
                <div class="d-flex gap-1">
                  <button class="btn btn-sm btn-outline-primary rounded-pill px-2" @click="viewDetail(app)" title="View Application Note">
                    <i class="bi bi-eye me-1"></i>View
                  </button>
                  <router-link :to="{ name: 'JobDetail', params: { slug: app.job?.slug } }" class="btn btn-sm btn-outline-secondary rounded-pill px-2" title="View Job Posting">
                    <i class="bi bi-box-arrow-up-right"></i>
                  </router-link>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="appDetailModal" tabindex="-1" ref="modalRef">
      <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0">
          <div class="modal-header">
            <h5 class="modal-title fw-bold">Application Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4" v-if="selectedApp">
            <div class="row g-3">
              <div class="col-md-6">
                <p class="text-muted small mb-1">Job Title</p>
                <p class="fw-semibold text-dark">{{ selectedApp.job?.title }}</p>
              </div>
              <div class="col-md-6">
                <p class="text-muted small mb-1">Company</p>
                <p class="fw-semibold text-dark">{{ selectedApp.job?.company?.name }}</p>
              </div>
              <div class="col-md-6">
                <p class="text-muted small mb-1">Applied On</p>
                <p class="fw-semibold">{{ formatDate(selectedApp.created_at) }}</p>
              </div>
              <div class="col-md-6">
                <p class="text-muted small mb-1">Current Status</p>
                <span :class="statusBadge(selectedApp.status)" class="badge fs-6">
                  {{ capitalizeFirst(selectedApp.status) }}
                </span>
              </div>
              <div class="col-12" v-if="selectedApp.cover_letter">
                <p class="text-muted small mb-1">Submitted Cover Letter</p>
                <div class="bg-light p-3 rounded-3" style="white-space:pre-wrap">{{ selectedApp.cover_letter }}</div>
              </div>
              <div class="col-12" v-if="selectedApp.recruiter_note">
                <p class="text-muted small mb-1">Recruiter Review Feedback / Note</p>
                <div class="bg-info bg-opacity-10 border border-info rounded-3 p-3" style="white-space:pre-wrap">{{ selectedApp.recruiter_note }}</div>
              </div>
              <div class="col-12" v-if="selectedApp.cv_file_path">
                <a :href="selectedApp.cv_file_path" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                  <i class="bi bi-file-earmark-pdf me-1"></i>Download Submitted CV
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Modal } from 'bootstrap'
import { useAuthStore } from '@/stores/authStore'
import api from '@/services/api'

const authStore = useAuthStore()
const loading = ref(true)
const applications = ref([])
const filterStatus = ref('')
const searchKeyword = ref('')
const selectedApp = ref(null)
const modalRef = ref(null)
let bsModal = null

const userInitials = computed(() => {
  const name = authStore.user?.name || 'Candidate'
  const parts = name.trim().split(' ')
  if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase()
  return name.slice(0, 2).toUpperCase()
})

function avatarColor(name) {
  if (!name) return '#4f46e5'
  const colors = ['#4f46e5', '#0284c7', '#059669', '#d97706', '#dc2626', '#7c3aed', '#db2777', '#0d9488']
  let hash = 0
  for (let i = 0; i < name.length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash)
  return colors[Math.abs(hash) % colors.length]
}

onMounted(async () => {
  try {
    const res = await api.get('/candidate/applications')
    applications.value = res.data.data || []
  } catch {
    applications.value = []
  } finally {
    loading.value = false
  }
})

const filteredApplications = computed(() => {
  return applications.value.filter(app => {
    const matchStatus = !filterStatus.value || app.status === filterStatus.value
    const kw = searchKeyword.value.toLowerCase()
    const matchKw = !kw ||
      app.job?.title?.toLowerCase().includes(kw) ||
      app.job?.company?.name?.toLowerCase().includes(kw)
    return matchStatus && matchKw
  })
})

function viewDetail(app) {
  selectedApp.value = app
  if (!bsModal) bsModal = new Modal(modalRef.value)
  bsModal.show()
}

function statusBadge(status) {
  const map = {
    pending: 'bg-warning text-dark',
    reviewed: 'bg-info text-dark',
    interview: 'bg-purple text-white',
    offered: 'bg-success',
    rejected: 'bg-danger',
  }
  return map[status] || 'bg-secondary'
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
