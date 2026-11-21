<template>
  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-briefcase text-primary me-2"></i>My Job Postings</h4>
        <p class="text-muted small mt-1">Create, manage, and track all your job listings.</p>
      </div>
      <button class="btn btn-primary" @click="openCreateModal">
        <i class="bi bi-plus-circle me-2"></i>Post New Job
      </button>
    </div>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary"></div>
    </div>

    <div v-else-if="jobs.length === 0" class="text-center py-5">
      <i class="bi bi-briefcase text-muted" style="font-size:3.5rem"></i>
      <h5 class="mt-3 fw-semibold">No Job Postings Yet</h5>
      <p class="text-muted mb-4">Create your first job listing to start finding the right candidates.</p>
      <button class="btn btn-primary px-4" @click="openCreateModal">
        <i class="bi bi-plus-circle me-2"></i>Post Your First Job
      </button>
    </div>

    <div v-else class="card border-0 shadow-sm rounded-4">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="px-4">Job Title</th>
              <th>Status</th>
              <th>Applications</th>
              <th>Deadline</th>
              <th class="px-4">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="job in jobs" :key="job.id">
              <td class="px-4">
                <div class="fw-semibold">{{ job.title }}</div>
                <div class="text-muted small">{{ formatJobType(job.job_type) }} · {{ job.location }}</div>
              </td>
              <td>
                <span :class="jobStatusBadge(job.status)" class="badge">{{ capitalizeFirst(job.status) }}</span>
              </td>
              <td>
                <router-link :to="{ name: 'JobApplicants', params: { id: job.id } }" class="text-decoration-none">
                  <span class="badge bg-info text-dark">{{ job.applications_count ?? 0 }} applicants</span>
                </router-link>
              </td>
              <td>
                <span class="text-muted small">{{ formatDate(job.deadline) }}</span>
              </td>
              <td class="px-4">
                <div class="d-flex gap-1">
                  <router-link :to="{ name: 'JobApplicants', params: { id: job.id } }" class="btn btn-sm btn-outline-info" title="View Applicants">
                    <i class="bi bi-people"></i>
                  </router-link>
                  <button class="btn btn-sm btn-outline-primary" @click="openEditModal(job)" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-warning" @click="toggleClose(job)" title="Close Job" :disabled="job.status === 'pending' || job.status === 'closed'">
                    <i class="bi bi-x-circle"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-danger" @click="deleteJob(job)" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Job Modal (Create / Edit) -->
    <div class="modal fade" id="jobModal" tabindex="-1" ref="jobModalRef">
      <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0">
          <div class="modal-header">
            <h5 class="modal-title fw-bold">{{ isEditing ? 'Edit Job Posting' : 'Create New Job Posting' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <div v-if="formError" class="alert alert-danger">
              <i class="bi bi-exclamation-triangle me-2"></i>{{ formError }}
            </div>
            <form @submit.prevent="saveJob" id="jobForm">
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label">Job Title <span class="text-danger">*</span></label>
                  <input v-model="jobForm.title" type="text" class="form-control" required placeholder="e.g. Senior Backend Developer" />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Category <span class="text-danger">*</span></label>
                  <select v-model="jobForm.category_id" class="form-select" required>
                    <option value="">Select category</option>
                    <option v-for="cat in masterStore.categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Job Type <span class="text-danger">*</span></label>
                  <select v-model="jobForm.job_type" class="form-select" required>
                    <option value="">Select type</option>
                    <option value="full_time">Full-time</option>
                    <option value="part_time">Part-time</option>
                    <option value="remote">Remote</option>
                    <option value="internship">Internship</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Experience Level</label>
                  <select v-model="jobForm.experience_level" class="form-select">
                    <option value="">Select level</option>
                    <option value="intern">Intern</option>
                    <option value="junior">Junior</option>
                    <option value="mid">Mid-level</option>
                    <option value="senior">Senior</option>
                    <option value="lead">Lead / Manager</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Location (City) <span class="text-danger">*</span></label>
                  <input v-model="jobForm.location" type="text" class="form-control" required placeholder="e.g. Ho Chi Minh City" />
                </div>
                <div class="col-12">
                  <label class="form-label">Work Address</label>
                  <input v-model="jobForm.work_address" type="text" class="form-control" placeholder="Specific office address" />
                </div>
                <div class="col-md-4">
                  <label class="form-label">Min Salary (USD)</label>
                  <input v-model.number="jobForm.salary_min" type="number" class="form-control" min="0" placeholder="0" />
                </div>
                <div class="col-md-4">
                  <label class="form-label">Max Salary (USD)</label>
                  <input v-model.number="jobForm.salary_max" type="number" class="form-control" min="0" placeholder="0" />
                </div>
                <div class="col-md-4">
                  <label class="form-label">Vacancies</label>
                  <input v-model.number="jobForm.vacancies" type="number" class="form-control" min="1" placeholder="1" />
                </div>
                <div class="col-md-6">
                  <div class="form-check mt-4">
                    <input v-model="jobForm.is_negotiable" class="form-check-input" type="checkbox" id="negotiable" />
                    <label class="form-check-label" for="negotiable">Salary is Negotiable</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Application Deadline</label>
                  <input v-model="jobForm.deadline" type="date" class="form-control" />
                </div>
                <div class="col-12">
                  <label class="form-label">Job Description <span class="text-danger">*</span></label>
                  <textarea v-model="jobForm.description" class="form-control" rows="4" required placeholder="Describe the role, responsibilities, and company..."></textarea>
                </div>
                <div class="col-12">
                  <label class="form-label">Requirements</label>
                  <textarea v-model="jobForm.requirements" class="form-control" rows="3" placeholder="List required qualifications, skills, experience..."></textarea>
                </div>
                <div class="col-12">
                  <label class="form-label">Benefits</label>
                  <textarea v-model="jobForm.benefits" class="form-control" rows="3" placeholder="Health insurance, flexible hours, remote work..."></textarea>
                </div>
                <div class="col-12">
                  <label class="form-label">Required Skills</label>
                  <div class="d-flex flex-wrap gap-2">
                    <div v-for="skill in masterStore.skills" :key="skill.id" class="form-check">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        :id="'skill_' + skill.id"
                        :value="skill.id"
                        v-model="jobForm.skill_ids"
                      />
                      <label class="form-check-label" :for="'skill_' + skill.id">{{ skill.name }}</label>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" form="jobForm" class="btn btn-primary" :disabled="formSaving">
              <span v-if="formSaving" class="spinner-border spinner-border-sm me-2"></span>
              {{ isEditing ? 'Save Changes' : 'Post Job' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Modal } from 'bootstrap'
import api from '@/services/api'
import { useMasterStore } from '@/stores/masterStore'

const masterStore = useMasterStore()
const loading = ref(true)
const jobs = ref([])
const jobModalRef = ref(null)
let bsModal = null

const isEditing = ref(false)
const editingId = ref(null)
const formSaving = ref(false)
const formError = ref('')

const defaultForm = () => ({
  title: '',
  category_id: '',
  job_type: '',
  experience_level: '',
  salary_min: '',
  salary_max: '',
  is_negotiable: false,
  location: '',
  work_address: '',
  vacancies: 1,
  description: '',
  requirements: '',
  benefits: '',
  deadline: '',
  skill_ids: [],
})

const jobForm = ref(defaultForm())

onMounted(async () => {
  await fetchJobs()
  if (!masterStore.categories.length) masterStore.fetchMasterData()
})

async function fetchJobs() {
  loading.value = true
  try {
    const res = await api.get('/recruiter/jobs')
    jobs.value = res.data.data || []
  } catch {
    jobs.value = []
  } finally {
    loading.value = false
  }
}

function openCreateModal() {
  isEditing.value = false
  editingId.value = null
  jobForm.value = defaultForm()
  formError.value = ''
  if (!bsModal) bsModal = new Modal(jobModalRef.value)
  bsModal.show()
}

function openEditModal(job) {
  isEditing.value = true
  editingId.value = job.id
  jobForm.value = {
    title: job.title || '',
    category_id: job.category_id || '',
    job_type: job.job_type || '',
    experience_level: job.experience_level || '',
    salary_min: job.salary_min || '',
    salary_max: job.salary_max || '',
    is_negotiable: !!job.is_negotiable,
    location: job.location || '',
    work_address: job.work_address || '',
    vacancies: job.vacancies || 1,
    description: job.description || '',
    requirements: job.requirements || '',
    benefits: job.benefits || '',
    deadline: job.deadline ? job.deadline.substring(0, 10) : '',
    skill_ids: (job.skills || []).map(s => s.id),
  }
  formError.value = ''
  if (!bsModal) bsModal = new Modal(jobModalRef.value)
  bsModal.show()
}

async function saveJob() {
  formSaving.value = true
  formError.value = ''
  try {
    const payload = { ...jobForm.value }
    if (isEditing.value) {
      const res = await api.put(`/recruiter/jobs/${editingId.value}`, payload)
      const idx = jobs.value.findIndex(j => j.id === editingId.value)
      if (idx !== -1) jobs.value[idx] = { ...jobs.value[idx], ...res.data.data }
    } else {
      const res = await api.post('/recruiter/jobs', payload)
      jobs.value.unshift(res.data.data)
    }
    bsModal.hide()
  } catch (err) {
    const errors = err.response?.data?.errors
    formError.value = errors ? Object.values(errors).flat().join(' ') : (err.response?.data?.message || 'Failed to save job.')
  } finally {
    formSaving.value = false
  }
}

async function toggleClose(job) {
  if (job.status !== 'active') return
  if (!confirm(`Close job posting "${job.title}"?`)) return
  try {
    await api.put(`/recruiter/jobs/${job.id}`, { status: 'closed' })
    job.status = 'closed'
  } catch {
    alert('Failed to close job.')
  }
}

async function deleteJob(job) {
  if (!confirm(`Delete "${job.title}"? This cannot be undone.`)) return
  try {
    await api.delete(`/recruiter/jobs/${job.id}`)
    jobs.value = jobs.value.filter(j => j.id !== job.id)
  } catch {
    alert('Failed to delete job.')
  }
}

function jobStatusBadge(status) {
  const map = {
    pending: 'bg-warning text-dark',
    active: 'bg-success',
    closed: 'bg-secondary',
    rejected: 'bg-danger',
  }
  return map[status] || 'bg-secondary'
}
function formatJobType(t) {
  const map = { full_time: 'Full-time', part_time: 'Part-time', remote: 'Remote', internship: 'Internship' }
  return map[t] || (t ? t.replace('_', '-') : '—')
}
function capitalizeFirst(str) {
  return str ? str.charAt(0).toUpperCase() + str.slice(1) : ''
}
function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}
</script>

