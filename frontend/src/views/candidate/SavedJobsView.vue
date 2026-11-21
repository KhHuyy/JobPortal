<template>
  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-bookmark-heart text-primary me-2"></i>Saved Jobs</h4>
        <p class="text-muted small mt-1">Jobs you've bookmarked for later.</p>
      </div>
      <router-link to="/jobs" class="btn btn-primary btn-sm">
        <i class="bi bi-search me-1"></i>Browse More Jobs
      </router-link>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary"></div>
      <p class="mt-2 text-muted">Loading saved jobs...</p>
    </div>

    <!-- Empty state -->
    <div v-else-if="savedJobs.length === 0" class="text-center py-5">
      <i class="bi bi-bookmark text-muted" style="font-size:3rem"></i>
      <p class="mt-3 text-muted">You haven't saved any jobs yet.</p>
      <router-link to="/jobs" class="btn btn-primary">Find Jobs to Save</router-link>
    </div>

    <!-- Job Cards Grid -->
    <!-- API returns flat job objects directly in the array -->
    <div v-else class="row g-4">
      <div v-for="job in savedJobs" :key="job.id" class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
          <div class="card-body p-4 d-flex flex-column">
            <!-- Company Logo + Info -->
            <div class="d-flex align-items-start gap-3 mb-3">
              <img
                v-if="job.company?.logo"
                :src="job.company.logo"
                width="50" height="50"
                class="rounded-3"
                style="object-fit:cover;flex-shrink:0"
              />
              <div v-else class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="width:50px;height:50px;flex-shrink:0">
                <i class="bi bi-building text-muted fs-5"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-1 lh-sm">{{ job.title || 'Job Removed' }}</h6>
                <p class="text-muted small mb-0">{{ job.company?.name }}</p>
                <p v-if="job.company?.city" class="text-muted small mb-0">
                  <i class="bi bi-geo-alt me-1"></i>{{ job.company.city }}
                </p>
              </div>
            </div>

            <!-- Tags -->
            <div class="d-flex flex-wrap gap-1 mb-3">
              <span v-if="job.job_type" class="badge bg-primary bg-opacity-10 text-primary">
                {{ job.job_type }}
              </span>
              <span v-if="job.location" class="badge bg-secondary bg-opacity-10 text-secondary">
                <i class="bi bi-geo-alt me-1"></i>{{ job.location }}
              </span>
              <span v-if="job.experience_level" class="badge bg-info bg-opacity-10 text-info">
                {{ job.experience_level }}
              </span>
            </div>

            <!-- Skills -->
            <div v-if="job.skills?.length" class="d-flex flex-wrap gap-1 mb-2">
              <span v-for="skill in job.skills.slice(0, 3)" :key="skill.id" class="badge bg-light text-dark border small">
                {{ skill.name }}
              </span>
              <span v-if="job.skills.length > 3" class="badge bg-light text-muted border small">
                +{{ job.skills.length - 3 }} more
              </span>
            </div>

            <!-- Salary -->
            <p v-if="job.salary_min" class="text-success fw-semibold small mb-2">
              <i class="bi bi-currency-dollar me-1"></i>
              {{ formatSalary(job.salary_min) }} – {{ formatSalary(job.salary_max) }}
              <span v-if="job.is_negotiable" class="text-muted"> (Negotiable)</span>
            </p>
            <p v-else-if="job.is_negotiable" class="text-success fw-semibold small mb-2">
              <i class="bi bi-currency-dollar me-1"></i>Negotiable
            </p>

            <!-- Deadline -->
            <p v-if="job.deadline" class="text-muted small mb-3">
              <i class="bi bi-calendar-event me-1"></i>Deadline: {{ formatDate(job.deadline) }}
            </p>

            <div class="mt-auto d-flex gap-2">
              <router-link
                :to="{ name: 'JobDetail', params: { slug: job.slug } }"
                class="btn btn-primary btn-sm flex-grow-1"
              >
                <i class="bi bi-send me-1"></i>Apply Now
              </router-link>
              <button
                class="btn btn-outline-danger btn-sm"
                @click="removeSaved(job)"
                :disabled="removing === job.id"
                title="Remove from saved"
              >
                <span v-if="removing === job.id" class="spinner-border spinner-border-sm"></span>
                <i v-else class="bi bi-trash"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const loading = ref(true)
const savedJobs = ref([])
const removing = ref(null)

onMounted(async () => {
  await fetchSaved()
})

async function fetchSaved() {
  loading.value = true
  try {
    const res = await api.get('/candidate/saved-jobs')
    // API returns mapped job objects directly (flat array)
    savedJobs.value = res.data.data || []
  } catch {
    savedJobs.value = []
  } finally {
    loading.value = false
  }
}

async function removeSaved(job) {
  removing.value = job.id
  try {
    await api.post(`/candidate/jobs/${job.id}/toggle-save`)
    savedJobs.value = savedJobs.value.filter(j => j.id !== job.id)
  } catch {
    // ignore
  } finally {
    removing.value = null
  }
}

function formatSalary(val) {
  if (!val) return '—'
  return new Intl.NumberFormat('en-US').format(val)
}

function formatDate(dateStr) {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}
</script>

