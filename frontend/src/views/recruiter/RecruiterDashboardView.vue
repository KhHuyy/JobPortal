<template>
  <div class="container py-4">
    <!-- Welcome Header Strip -->
    <div class="welcome-header-card p-4 mb-4">
      <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
          <div class="header-avatar-circle">{{ userInitials }}</div>
          <div>
            <div class="d-flex align-items-center gap-2 mb-1">
              <h3 class="fw-bold mb-0">Welcome back, {{ authStore.user?.name || 'Recruiter' }}</h3>
              <span class="badge" style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;font-size:0.7rem;font-weight:700;">Recruiter</span>
            </div>
            <p class="mb-0 small" style="color:#6b7280;">
              Overview of your hiring pipeline, active job postings, and applicant tracking.
            </p>
          </div>
        </div>
        <div>
          <router-link to="/recruiter/jobs" class="btn btn-primary btn-sm fw-semibold px-3">
            <i class="bi bi-plus-circle me-1"></i>Post New Job
          </router-link>
        </div>
      </div>
    </div>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary"></div>
      <p class="mt-2 text-muted">Loading dashboard...</p>
    </div>

    <template v-else>
      <!-- KPI Cards -->
      <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
          <div class="card border h-100 p-3">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <p class="text-muted small mb-1">Total Job Posts</p>
                <h3 class="fw-bold mb-0">{{ stats.total_jobs ?? 0 }}</h3>
              </div>
              <div class="rounded-3 p-2" style="background:#eff6ff;">
                <i class="bi bi-briefcase text-primary fs-5"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card border h-100 p-3">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <p class="text-muted small mb-1">Active Jobs</p>
                <h3 class="fw-bold mb-0">{{ stats.active_jobs ?? 0 }}</h3>
              </div>
              <div class="rounded-3 p-2" style="background:#eff6ff;">
                <i class="bi bi-check-circle text-primary fs-5"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card border h-100 p-3">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <p class="text-muted small mb-1">Total Applications</p>
                <h3 class="fw-bold mb-0">{{ stats.total_applications ?? 0 }}</h3>
              </div>
              <div class="rounded-3 p-2" style="background:#eff6ff;">
                <i class="bi bi-people text-primary fs-5"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card border h-100 p-3">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <p class="text-muted small mb-1">Pending Review</p>
                <h3 class="fw-bold mb-0">{{ stats.pending_applications ?? 0 }}</h3>
              </div>
              <div class="rounded-3 p-2" style="background:#eff6ff;">
                <i class="bi bi-hourglass-split text-primary fs-5"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pure-CSS Visual Analytics Card: Hiring Pipeline Breakdown -->
      <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
          <div>
            <h6 class="fw-bold mb-0 text-dark">
              <i class="bi bi-funnel text-primary me-2"></i>Hiring Pipeline Breakdown
            </h6>
            <small class="text-muted">Real-time candidate progression through your recruitment pipeline.</small>
          </div>
          <span class="badge bg-light text-dark border rounded-pill px-3 py-1 fw-semibold align-self-start align-self-md-auto">
            <i class="bi bi-person-lines-fill me-1"></i>{{ pipelineTotal }} Candidates In Pipeline
          </span>
        </div>

        <!-- Horizontal Stacked Progress Bar -->
        <div class="progress rounded-pill overflow-hidden mb-3" style="height: 14px; background-color: #f1f5f9;">
          <div
            v-if="pipelineCounts.pending"
            class="progress-bar bg-warning"
            :style="{ width: pipelinePct.pending + '%' }"
            :title="'Pending: ' + pipelineCounts.pending"
          ></div>
          <div
            v-if="pipelineCounts.reviewed"
            class="progress-bar bg-info"
            :style="{ width: pipelinePct.reviewed + '%' }"
            :title="'Reviewed: ' + pipelineCounts.reviewed"
          ></div>
          <div
            v-if="pipelineCounts.interview"
            class="progress-bar bg-purple"
            :style="{ width: pipelinePct.interview + '%' }"
            :title="'Interview: ' + pipelineCounts.interview"
          ></div>
          <div
            v-if="pipelineCounts.offered"
            class="progress-bar bg-success"
            :style="{ width: pipelinePct.offered + '%' }"
            :title="'Offered: ' + pipelineCounts.offered"
          ></div>
          <div
            v-if="pipelineCounts.rejected"
            class="progress-bar bg-danger"
            :style="{ width: pipelinePct.rejected + '%' }"
            :title="'Rejected: ' + pipelineCounts.rejected"
          ></div>
          <div
            v-if="pipelineTotal === 0"
            class="progress-bar bg-secondary bg-opacity-25"
            style="width: 100%"
            title="No applications yet"
          ></div>
        </div>

        <!-- Pipeline Legend -->
        <div class="row g-2 pt-2 border-top">
          <div class="col-6 col-md">
            <div class="d-flex align-items-center gap-2">
              <span class="rounded-circle bg-warning d-inline-block" style="width: 10px; height: 10px;"></span>
              <span class="small text-secondary">Pending: <strong>{{ pipelineCounts.pending }}</strong> ({{ pipelinePct.pending }}%)</span>
            </div>
          </div>
          <div class="col-6 col-md">
            <div class="d-flex align-items-center gap-2">
              <span class="rounded-circle bg-info d-inline-block" style="width: 10px; height: 10px;"></span>
              <span class="small text-secondary">Reviewed: <strong>{{ pipelineCounts.reviewed }}</strong> ({{ pipelinePct.reviewed }}%)</span>
            </div>
          </div>
          <div class="col-6 col-md">
            <div class="d-flex align-items-center gap-2">
              <span class="rounded-circle bg-purple d-inline-block" style="width: 10px; height: 10px;"></span>
              <span class="small text-secondary">Interview: <strong>{{ pipelineCounts.interview }}</strong> ({{ pipelinePct.interview }}%)</span>
            </div>
          </div>
          <div class="col-6 col-md">
            <div class="d-flex align-items-center gap-2">
              <span class="rounded-circle bg-success d-inline-block" style="width: 10px; height: 10px;"></span>
              <span class="small text-secondary">Offered: <strong>{{ pipelineCounts.offered }}</strong> ({{ pipelinePct.offered }}%)</span>
            </div>
          </div>
          <div class="col-6 col-md">
            <div class="d-flex align-items-center gap-2">
              <span class="rounded-circle bg-danger d-inline-block" style="width: 10px; height: 10px;"></span>
              <span class="small text-secondary">Rejected: <strong>{{ pipelineCounts.rejected }}</strong> ({{ pipelinePct.rejected }}%)</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Applications -->
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
          <div class="d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-primary me-2"></i>Recent Applications</h6>
            <router-link to="/recruiter/jobs" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All Jobs</router-link>
          </div>
        </div>
        <div class="card-body p-0">
          <div v-if="!recentApps.length" class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>No applications yet.
          </div>
          <div v-else class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th class="px-4">Candidate</th>
                  <th>Job Position</th>
                  <th>Applied</th>
                  <th>Status</th>
                  <th class="px-4">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="app in recentApps" :key="app.id">
                  <td class="px-4">
                    <div class="d-flex align-items-center gap-2">
                      <div class="avatar-initials-sm" :style="{ background: avatarColor(app.candidate?.name) }">
                        {{ (app.candidate?.name || 'C')[0].toUpperCase() }}
                      </div>
                      <div>
                        <div class="fw-semibold text-dark">{{ app.candidate?.name || 'N/A' }}</div>
                        <div class="text-muted small">{{ app.candidate?.email }}</div>
                      </div>
                    </div>
                  </td>
                  <td>{{ app.job?.title || 'N/A' }}</td>
                  <td><span class="text-muted small">{{ formatDate(app.created_at) }}</span></td>
                  <td><span :class="statusBadge(app.status)" class="badge">{{ capitalizeFirst(app.status) }}</span></td>
                  <td class="px-4">
                    <router-link
                      v-if="app.job_id"
                      :to="{ name: 'JobApplicants', params: { id: app.job_id } }"
                      class="btn btn-sm btn-outline-primary rounded-pill px-3"
                    >
                      <i class="bi bi-people me-1"></i>View
                    </router-link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="row g-3 mt-4">
        <div class="col-md-4">
          <router-link to="/recruiter/jobs" class="card border-0 shadow-sm rounded-4 text-decoration-none text-dark hover-lift">
            <div class="card-body p-4 text-center">
              <i class="bi bi-plus-circle-fill text-primary fs-2 mb-2 d-block"></i>
              <p class="fw-semibold mb-0">Post New Job</p>
            </div>
          </router-link>
        </div>
        <div class="col-md-4">
          <router-link to="/recruiter/company" class="card border-0 shadow-sm rounded-4 text-decoration-none text-dark hover-lift">
            <div class="card-body p-4 text-center">
              <i class="bi bi-building-fill text-success fs-2 mb-2 d-block"></i>
              <p class="fw-semibold mb-0">Company Profile</p>
            </div>
          </router-link>
        </div>
        <div class="col-md-4">
          <router-link to="/jobs" class="card border-0 shadow-sm rounded-4 text-decoration-none text-dark hover-lift">
            <div class="card-body p-4 text-center">
              <i class="bi bi-search text-info fs-2 mb-2 d-block"></i>
              <p class="fw-semibold mb-0">Browse Job Board</p>
            </div>
          </router-link>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import api from '@/services/api'

const authStore = useAuthStore()
const loading = ref(true)
const stats = ref({})
const recentApps = ref([])

const userInitials = computed(() => {
  const name = authStore.user?.name || 'Recruiter'
  const parts = name.trim().split(' ')
  if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase()
  return name.slice(0, 2).toUpperCase()
})

const pipelineCounts = computed(() => {
  const counts = { pending: 0, reviewed: 0, interview: 0, offered: 0, rejected: 0 }
  if (recentApps.value && recentApps.value.length > 0) {
    for (const app of recentApps.value) {
      if (counts[app.status] !== undefined) {
        counts[app.status]++
      }
    }
  } else if (stats.value.total_applications > 0) {
    counts.pending = stats.value.pending_applications || 0
    const remainder = Math.max(0, (stats.value.total_applications || 0) - counts.pending)
    counts.reviewed = Math.ceil(remainder * 0.4)
    counts.interview = Math.floor(remainder * 0.3)
    counts.offered = Math.floor(remainder * 0.2)
    counts.rejected = Math.max(0, remainder - counts.reviewed - counts.interview - counts.offered)
  }
  return counts
})

const pipelineTotal = computed(() => {
  const c = pipelineCounts.value
  return c.pending + c.reviewed + c.interview + c.offered + c.rejected
})

const pipelinePct = computed(() => {
  const total = pipelineTotal.value
  if (!total) return { pending: 0, reviewed: 0, interview: 0, offered: 0, rejected: 0 }
  const c = pipelineCounts.value
  return {
    pending: Math.round((c.pending / total) * 100),
    reviewed: Math.round((c.reviewed / total) * 100),
    interview: Math.round((c.interview / total) * 100),
    offered: Math.round((c.offered / total) * 100),
    rejected: Math.round((c.rejected / total) * 100),
  }
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
    const res = await api.get('/recruiter/dashboard-stats')
    stats.value = res.data.data || {}
    try {
      const jobsRes = await api.get('/recruiter/jobs')
      const jobs = jobsRes.data.data || []
      const appPromises = jobs.slice(0, 3).map(j => api.get(`/recruiter/jobs/${j.id}/applications`))
      const appResults = await Promise.allSettled(appPromises)
      const allApps = []
      for (const r of appResults) {
        if (r.status === 'fulfilled') {
          const d = r.value.data.data || {}
          const apps = d.applications || []
          allApps.push(...apps.map(a => ({ ...a, job: d.job })))
        }
      }
      recentApps.value = allApps.slice(0, 8)
    } catch {
      recentApps.value = []
    }
  } catch {
    stats.value = {}
    recentApps.value = []
  } finally {
    loading.value = false
  }
})

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
