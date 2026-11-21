<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'

const props = defineProps({ slug: String })
const route = useRoute()

const company = ref(null)
const jobs = ref([])
const loading = ref(true)
const error = ref(null)

onMounted(async () => {
  await loadCompany()
})

watch(() => route.params.slug, async () => {
  await loadCompany()
})

async function loadCompany() {
  loading.value = true
  error.value = null
  try {
    const slug = props.slug || route.params.slug
    const res = await api.get(`/companies/${slug}`)
    company.value = res.data.data.company
    jobs.value = res.data.data.jobs || []
  } catch (err) {
    error.value = err.response?.status === 404 ? 'Company profile not found.' : 'Failed to load company information.'
  } finally {
    loading.value = false
  }
}

function formatJobType(type) {
  const map = { full_time: 'Full Time', part_time: 'Part Time', remote: 'Remote', internship: 'Internship' }
  return map[type] || type
}

function formatSalary(j) {
  if (!j.salary_min && !j.salary_max) return 'Negotiable'
  if (j.salary_min && j.salary_max) return `$${Number(j.salary_min).toLocaleString()} – $${Number(j.salary_max).toLocaleString()}`
  return j.salary_min ? `From $${Number(j.salary_min).toLocaleString()}` : `Up to $${Number(j.salary_max).toLocaleString()}`
}
</script>

<template>
  <div class="company-detail-page py-4">
    <div class="container">

      <!-- Breadcrumbs -->
      <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
          <li class="breadcrumb-item"><router-link to="/" class="text-decoration-none">Home</router-link></li>
          <li class="breadcrumb-item"><router-link to="/companies" class="text-decoration-none">Companies</router-link></li>
          <li class="breadcrumb-item active text-truncate" aria-current="page" style="max-width:320px;">
            {{ company?.name || 'Company Profile' }}
          </li>
        </ol>
      </nav>

      <!-- Loading skeleton -->
      <div v-if="loading" class="row g-4">
        <div class="col-12">
          <div class="card p-4 placeholder-glow border">
            <div class="d-flex gap-3 align-items-center">
              <span class="placeholder rounded" style="width:72px;height:72px;"></span>
              <div class="flex-grow-1">
                <span class="placeholder col-5 d-block mb-2 rounded"></span>
                <span class="placeholder col-3 rounded"></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Error message -->
      <div v-else-if="error" class="card text-center p-5 border">
        <i class="bi bi-exclamation-circle text-danger display-4 mb-3"></i>
        <h4 class="fw-bold">{{ error }}</h4>
        <div class="mt-3">
          <router-link to="/companies" class="btn btn-outline-primary btn-sm px-4">
            <i class="bi bi-arrow-left me-1"></i> Back to Companies
          </router-link>
        </div>
      </div>

      <!-- Real Company Profile -->
      <div v-else-if="company">
        <!-- Company Header Card -->
        <div class="card company-header-card p-4 mb-4">
          <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
              <img v-if="company.logo" :src="company.logo" class="company-header-logo" :alt="company.name" />
              <div v-else class="company-header-fallback">
                {{ company.name[0] }}
              </div>

              <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                  <h1 class="h3 fw-bold mb-0 text-dark">{{ company.name }}</h1>
                  <span v-if="company.is_verified" class="badge bg-success-subtle text-success border border-success-subtle small">
                    <i class="bi bi-patch-check-fill me-1"></i>Verified Employer
                  </span>
                </div>
                <div class="d-flex flex-wrap gap-3 text-muted small mt-2">
                  <span v-if="company.city"><i class="bi bi-geo-alt text-primary"></i> {{ company.city }}</span>
                  <span v-if="company.company_size"><i class="bi bi-people text-primary"></i> {{ company.company_size }}</span>
                  <a v-if="company.website" :href="company.website" target="_blank" class="text-decoration-none text-primary">
                    <i class="bi bi-globe me-1"></i> {{ company.website.replace('https://', '').replace('http://', '') }}
                  </a>
                </div>
              </div>
            </div>

            <div>
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fs-6">
                {{ jobs.length }} Open Position{{ jobs.length !== 1 ? 's' : '' }}
              </span>
            </div>
          </div>
        </div>

        <div class="row g-4">
          <!-- Left Column (68%): About + Open Jobs -->
          <main class="col-lg-8">
            <!-- About Section -->
            <div class="card p-4 mb-4" v-if="company.description">
              <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-info-circle text-primary"></i> About {{ company.name }}
              </h5>
              <div class="company-prose">{{ company.description }}</div>
            </div>

            <!-- Open Positions List -->
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="fw-bold mb-0">Open Technical Roles</h5>
              <span class="text-muted small">Updated in real-time</span>
            </div>

            <div v-if="jobs.length === 0" class="card text-center p-5 border text-muted">
              <i class="bi bi-inbox display-4 d-block mb-2"></i>
              <p class="mb-0">No active job openings right now. Check back soon!</p>
            </div>

            <div v-else class="d-flex flex-column gap-3">
              <div v-for="job in jobs" :key="job.id" class="card company-job-card p-3 p-md-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                  <div>
                    <h5 class="fw-bold mb-1">
                      <router-link :to="{ name: 'JobDetail', params: { slug: job.slug } }" class="text-dark text-decoration-none">
                        {{ job.title }}
                      </router-link>
                    </h5>
                    <div class="d-flex flex-wrap gap-2 small">
                      <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ formatJobType(job.job_type) }}</span>
                      <span class="badge bg-light text-dark border"><i class="bi bi-geo-alt"></i> {{ job.location }}</span>
                      <span class="badge bg-light text-dark border" v-if="job.experience_level">{{ job.experience_level }}</span>
                    </div>
                  </div>

                  <div class="text-md-end pt-2 pt-md-0 border-top border-md-0 w-100 w-md-auto d-flex justify-content-between align-items-center flex-md-column align-items-md-end">
                    <div class="salary-highlight mb-md-1">{{ formatSalary(job) }}</div>
                    <router-link :to="{ name: 'JobDetail', params: { slug: job.slug } }" class="btn btn-sm btn-outline-primary px-3">
                      View Position &rarr;
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </main>

          <!-- Right Sidebar (32%): Quick Metadata -->
          <aside class="col-lg-4">
            <div class="card p-4">
              <h6 class="fw-bold text-uppercase text-muted small mb-3" style="letter-spacing:0.05em;">Company Profile</h6>
              <ul class="list-unstyled d-flex flex-column gap-3 mb-0 small">
                <li v-if="company.address">
                  <div class="text-muted">Headquarters</div>
                  <div class="fw-semibold text-dark mt-1">{{ company.address }}</div>
                </li>
                <li v-if="company.city">
                  <div class="text-muted">Primary City</div>
                  <div class="fw-semibold text-dark mt-1">{{ company.city }}</div>
                </li>
                <li v-if="company.company_size">
                  <div class="text-muted">Company Size</div>
                  <div class="fw-semibold text-dark mt-1">{{ company.company_size }}</div>
                </li>
                <li v-if="company.tax_code">
                  <div class="text-muted">Tax Identification</div>
                  <div class="fw-semibold text-dark mt-1">{{ company.tax_code }}</div>
                </li>
                <li v-if="company.website">
                  <div class="text-muted">Official Website</div>
                  <div class="mt-1">
                    <a :href="company.website" target="_blank" class="text-primary fw-semibold text-decoration-none">
                      {{ company.website }} <i class="bi bi-box-arrow-up-right small"></i>
                    </a>
                  </div>
                </li>
              </ul>
            </div>
          </aside>
        </div>

      </div>
    </div>
  </div>
</template>

<style scoped>
.company-detail-page {
  background-color: #f9fafb;
  min-height: 80vh;
}

.company-header-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
}

.company-header-logo {
  width: 68px;
  height: 68px;
  object-fit: contain;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
}
.company-header-fallback {
  width: 68px;
  height: 68px;
  border-radius: 10px;
  background-color: #eff6ff;
  color: #2563eb;
  font-weight: 700;
  font-size: 1.6rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.company-prose {
  white-space: pre-line;
  line-height: 1.7;
  color: #334155;
  font-size: 0.95rem;
}

.company-job-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  transition: all 0.2s ease;
}
.company-job-card:hover {
  border-color: #93c5fd;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px -4px rgba(37, 99, 235, 0.08);
}

.salary-highlight {
  font-weight: 700;
  color: #059669;
  font-size: 0.95rem;
}
</style>
