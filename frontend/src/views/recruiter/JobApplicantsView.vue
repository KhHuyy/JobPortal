<template>
  <div class="container py-5">
    <!-- Back button -->
    <div class="mb-4">
      <router-link to="/recruiter/jobs" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to My Jobs
      </router-link>
    </div>

    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h4 class="fw-bold mb-0">
          <i class="bi bi-people text-primary me-2"></i>
          Applicants for: <span class="text-primary">{{ jobTitle }}</span>
        </h4>
        <p class="text-muted small mt-1">{{ filteredApps.length }} application(s) found</p>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-3 p-3 mb-4">
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
          <input v-model="searchKeyword" type="text" class="form-control form-control-sm" placeholder="Search by candidate name or email..." />
        </div>
      </div>
    </div>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary"></div>
    </div>

    <div v-else-if="filteredApps.length === 0" class="text-center py-5">
      <i class="bi bi-inbox text-muted" style="font-size:3.5rem"></i>
      <h5 class="mt-3 fw-semibold">No Applications Found</h5>
      <p class="text-muted">No applications match the current filters. Try clearing the search or status filter.</p>
    </div>

    <div v-else class="card border-0 shadow-sm rounded-4">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="px-4">Candidate</th>
              <th>Applied On</th>
              <th>Status</th>
              <th>CV</th>
              <th class="px-4">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="app in filteredApps" :key="app.id">
              <td class="px-4">
                <div class="d-flex align-items-center gap-2">
                  <!-- Avatar: show image or colored initials -->
                  <template v-if="app.candidate?.avatar">
                    <img :src="app.candidate.avatar" class="rounded-circle" width="36" height="36" style="object-fit:cover;flex-shrink:0" :alt="app.candidate.name" />
                  </template>
                  <div v-else
                    class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                    :style="{ width: '36px', height: '36px', flexShrink: 0, background: avatarColor(app.candidate?.name) }"
                  >
                    {{ (app.candidate?.name || '?')[0].toUpperCase() }}
                  </div>
                  <div>
                    <div class="fw-semibold">{{ app.candidate?.name || 'Unknown' }}</div>
                    <div class="text-muted small">{{ app.candidate?.email }}</div>
                  </div>
                </div>
              </td>
              <td><span class="text-muted small">{{ formatDate(app.created_at) }}</span></td>
              <td>
                <span :class="statusBadge(app.status)" class="badge">{{ capitalizeFirst(app.status) }}</span>
              </td>
              <td>
                <a
                  v-if="app.cv_file_path"
                  :href="app.cv_file_path"
                  target="_blank"
                  class="btn btn-sm btn-outline-primary"
                  title="View Submitted CV"
                >
                  <i class="bi bi-file-earmark-pdf me-1"></i>CV
                </a>
                <span v-else-if="app.candidate?.profile?.default_cv_path">
                  <a :href="app.candidate.profile.default_cv_path" target="_blank" class="btn btn-sm btn-outline-secondary" title="Default CV">
                    <i class="bi bi-file-earmark me-1"></i>Default CV
                  </a>
                </span>
                <span v-else class="text-muted small">None</span>
              </td>
              <td class="px-4">
                <div class="d-flex gap-1">
                  <button class="btn btn-sm btn-outline-secondary" @click="viewCoverLetter(app)" title="View Cover Letter" :disabled="!app.cover_letter">
                    <i class="bi bi-chat-text"></i>
                  </button>
                  <button class="btn btn-sm btn-primary" @click="openStatusModal(app)" title="Update Status">
                    <i class="bi bi-pencil-square"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Cover Letter Modal -->
    <div class="modal fade" id="coverLetterModal" tabindex="-1" ref="coverLetterModalRef">
      <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0">
          <div class="modal-header">
            <h5 class="modal-title fw-bold">Cover Letter — {{ selectedApp?.candidate?.name }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <div class="bg-light p-4 rounded-3" style="white-space:pre-wrap;min-height:150px">
              {{ selectedApp?.cover_letter || 'No cover letter provided.' }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" ref="statusModalRef">
      <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0">
          <div class="modal-header">
            <h5 class="modal-title fw-bold">Update Application Status</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <div v-if="statusError" class="alert alert-danger mb-3">{{ statusError }}</div>
            <p class="text-muted mb-3">
              Candidate: <strong>{{ selectedApp?.candidate?.name }}</strong>
            </p>
            <div class="mb-3">
              <label class="form-label fw-semibold">New Status</label>
              <select v-model="newStatus" class="form-select">
                <option value="reviewed">Reviewed</option>
                <option value="interview">Interview</option>
                <option value="offered">Offered</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Note to Candidate <span class="text-muted">(optional)</span></label>
              <textarea v-model="recruiterNote" class="form-control" rows="3" placeholder="e.g. We'd like to schedule an interview for next week..."></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" @click="submitStatusUpdate" :disabled="statusSaving">
              <span v-if="statusSaving" class="spinner-border spinner-border-sm me-2"></span>
              Update Status
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { Modal } from 'bootstrap'
import api from '@/services/api'

const route = useRoute()
const jobId = route.params.id

const loading = ref(true)
const applications = ref([])
const jobTitle = ref('')
const filterStatus = ref('')
const searchKeyword = ref('')
const selectedApp = ref(null)

const coverLetterModalRef = ref(null)
const statusModalRef = ref(null)
let bsCoverModal = null
let bsStatusModal = null

const newStatus = ref('reviewed')
const recruiterNote = ref('')
const statusSaving = ref(false)
const statusError = ref('')

onMounted(async () => {
  try {
    const res = await api.get(`/recruiter/jobs/${jobId}/applications`)
    // API returns { job: {...}, applications: [...] }
    const data = res.data.data || {}
    jobTitle.value = data.job?.title || `Job #${jobId}`
    applications.value = data.applications || []
  } catch {
    applications.value = []
  } finally {
    loading.value = false
  }
})

const filteredApps = computed(() => {
  return applications.value.filter(app => {
    const matchStatus = !filterStatus.value || app.status === filterStatus.value
    const kw = searchKeyword.value.toLowerCase()
    const matchKw = !kw ||
      app.candidate?.name?.toLowerCase().includes(kw) ||
      app.candidate?.email?.toLowerCase().includes(kw)
    return matchStatus && matchKw
  })
})

function viewCoverLetter(app) {
  selectedApp.value = app
  if (!bsCoverModal) bsCoverModal = new Modal(coverLetterModalRef.value)
  bsCoverModal.show()
}

function openStatusModal(app) {
  selectedApp.value = app
  newStatus.value = app.status || 'reviewed'
  recruiterNote.value = app.recruiter_note || ''
  statusError.value = ''
  if (!bsStatusModal) bsStatusModal = new Modal(statusModalRef.value)
  bsStatusModal.show()
}

async function submitStatusUpdate() {
  statusSaving.value = true
  statusError.value = ''
  try {
    const res = await api.patch(`/recruiter/applications/${selectedApp.value.id}/status`, {
      status: newStatus.value,
      recruiter_note: recruiterNote.value,
    })
    // Update in-place
    const idx = applications.value.findIndex(a => a.id === selectedApp.value.id)
    if (idx !== -1) {
      applications.value[idx].status = newStatus.value
      applications.value[idx].recruiter_note = recruiterNote.value
    }
    bsStatusModal.hide()
  } catch (err) {
    statusError.value = err.response?.data?.message || 'Failed to update status.'
  } finally {
    statusSaving.value = false
  }
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
function formatDate(d) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}
// Generate a consistent color for candidate initials avatar
function avatarColor(name) {
  if (!name) return '#6c757d'
  const colors = ['#0d6efd','#6610f2','#6f42c1','#d63384','#dc3545','#fd7e14','#198754','#20c997','#0dcaf0']
  const idx = name.charCodeAt(0) % colors.length
  return colors[idx]
}
</script>

<style scoped>
.bg-purple { background-color: #6f42c1; }
</style>

