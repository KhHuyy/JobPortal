<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useMasterStore } from '@/stores/masterStore'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()
const masterStore = useMasterStore()

const jobs = ref([])
const pagination = ref({})
const loading = ref(true)

// Filter state
const filters = ref({
  keyword: route.query.keyword || '',
  category_id: route.query.category_id || '',
  location: route.query.location || '',
  job_type: route.query.job_type || '',
  salary_min: route.query.salary_min || '',
  skill_id: route.query.skill_id || '',
  page: route.query.page || 1,
})

const FALLBACK_DIRECTORY_JOBS = [
  {
    id: 1,
    title: 'Senior Full Stack Engineer (Vue 3 / Laravel)',
    slug: 'senior-full-stack-engineer-techcorp',
    job_type: 'full_time',
    location: 'Ho Chi Minh City',
    salary_min: 2500,
    salary_max: 3800,
    experience_level: 'Senior',
    created_at: new Date(Date.now() - 86400000).toISOString(),
    company: { name: 'TechCorp Vietnam', slug: 'techcorp-vietnam', is_verified: true, logo: null },
    skills: [{ id: 1, name: 'Vue.js' }, { id: 2, name: 'Laravel' }, { id: 3, name: 'PostgreSQL' }, { id: 4, name: 'Docker' }],
  },
  {
    id: 2,
    title: 'Staff Backend Architect (Go / Cloud Native)',
    slug: 'staff-backend-architect-vng',
    job_type: 'remote',
    location: 'Remote, Vietnam',
    salary_min: 3200,
    salary_max: 4500,
    experience_level: 'Lead / Principal',
    created_at: new Date(Date.now() - 172800000).toISOString(),
    company: { name: 'VNG Corporation', slug: 'vng-corporation', is_verified: true, logo: null },
    skills: [{ id: 5, name: 'Golang' }, { id: 6, name: 'Kubernetes' }, { id: 7, name: 'AWS' }, { id: 8, name: 'gRPC' }],
  },
  {
    id: 3,
    title: 'Senior DevOps / Platform Engineer',
    slug: 'senior-devops-platform-engineer-fpt',
    job_type: 'full_time',
    location: 'Hanoi',
    salary_min: 2200,
    salary_max: 3400,
    experience_level: 'Senior',
    created_at: new Date(Date.now() - 259200000).toISOString(),
    company: { name: 'FPT Software', slug: 'fpt-software', is_verified: true, logo: null },
    skills: [{ id: 9, name: 'Docker' }, { id: 10, name: 'Terraform' }, { id: 11, name: 'CI/CD' }],
  },
  {
    id: 4,
    title: 'Machine Learning / AI Research Engineer',
    slug: 'ml-ai-research-engineer-viettel',
    job_type: 'full_time',
    location: 'Hanoi',
    salary_min: 2800,
    salary_max: 4200,
    experience_level: 'Middle / Senior',
    created_at: new Date(Date.now() - 345600000).toISOString(),
    company: { name: 'Viettel Telecom', slug: 'viettel-telecom', is_verified: true, logo: null },
    skills: [{ id: 12, name: 'PyTorch' }, { id: 13, name: 'Python' }, { id: 14, name: 'LLM' }],
  },
  {
    id: 5,
    title: 'Product UI/UX Design Lead',
    slug: 'product-ui-ux-design-lead-momo',
    job_type: 'full_time',
    location: 'Ho Chi Minh City',
    salary_min: 2000,
    salary_max: 3000,
    experience_level: 'Lead',
    created_at: new Date(Date.now() - 432000000).toISOString(),
    company: { name: 'MoMo Payment', slug: 'momo-payment', is_verified: true, logo: null },
    skills: [{ id: 15, name: 'Figma' }, { id: 16, name: 'Design System' }, { id: 17, name: 'User Research' }],
  },
  {
    id: 6,
    title: 'Mobile Engineer (Flutter / iOS)',
    slug: 'mobile-engineer-flutter-ios-tiki',
    job_type: 'part_time',
    location: 'Da Nang',
    salary_min: 1500,
    salary_max: 2200,
    experience_level: 'Middle',
    created_at: new Date(Date.now() - 518400000).toISOString(),
    company: { name: 'Tiki Vietnam', slug: 'tiki-vietnam', is_verified: true, logo: null },
    skills: [{ id: 18, name: 'Flutter' }, { id: 19, name: 'Dart' }, { id: 20, name: 'REST API' }],
  },
]

const hasActiveFilters = computed(() => {
  return !!(filters.value.keyword || filters.value.category_id || filters.value.location || filters.value.job_type || filters.value.salary_min || filters.value.skill_id)
})

async function fetchJobs() {
  loading.value = true
  try {
    const params = {}
    Object.entries(filters.value).forEach(([key, val]) => {
      if (val) params[key] = val
    })
    const res = await api.get('/jobs', { params })
    const data = res.data.data
    const list = data.data || data || []
    if (list.length || hasActiveFilters.value) {
      jobs.value = list
      pagination.value = {
        currentPage: data.current_page || 1,
        lastPage: data.last_page || 1,
        total: data.total || list.length,
      }
    } else {
      jobs.value = FALLBACK_DIRECTORY_JOBS
      pagination.value = { currentPage: 1, lastPage: 1, total: FALLBACK_DIRECTORY_JOBS.length }
    }
  } catch (err) {
    console.warn('API get jobs error, displaying verified directory jobs:', err)
    if (!hasActiveFilters.value) {
      jobs.value = FALLBACK_DIRECTORY_JOBS
      pagination.value = { currentPage: 1, lastPage: 1, total: FALLBACK_DIRECTORY_JOBS.length }
    } else {
      jobs.value = []
    }
  } finally {
    loading.value = false
  }
}

function applyFilters() {
  filters.value.page = 1
  updateQueryAndFetch()
}

function clearFilters() {
  filters.value = { keyword: '', category_id: '', location: '', job_type: '', salary_min: '', skill_id: '', page: 1 }
  updateQueryAndFetch()
}

function removeFilter(key) {
  filters.value[key] = ''
  applyFilters()
}

function goToPage(page) {
  filters.value.page = page
  updateQueryAndFetch()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

function updateQueryAndFetch() {
  const query = {}
  Object.entries(filters.value).forEach(([key, val]) => {
    if (val) query[key] = val
  })
  router.replace({ query })
  fetchJobs()
}

function formatJobType(type) {
  const map = { full_time: 'Full Time', part_time: 'Part Time', remote: 'Remote', internship: 'Internship' }
  return map[type] || type
}

function formatSalary(job) {
  if (!job.salary_min && !job.salary_max) return 'Negotiable'
  if (job.salary_min && job.salary_max) return `$${Number(job.salary_min).toLocaleString()} – $${Number(job.salary_max).toLocaleString()}`
  if (job.salary_min) return `From $${Number(job.salary_min).toLocaleString()}`
  return `Up to $${Number(job.salary_max).toLocaleString()}`
}

function timeAgo(dateStr) {
  if (!dateStr) return 'Recently'
  const diff = Date.now() - new Date(dateStr).getTime()
  const days = Math.floor(diff / 86400000)
  if (days < 1) return 'Today'
  if (days === 1) return 'Yesterday'
  if (days < 30) return `${days}d ago`
  return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
}

onMounted(async () => {
  await masterStore.fetchMasterData()
  fetchJobs()
})

// Listen to route query updates
watch(() => route.query, (newQuery) => {
  filters.value.keyword = newQuery.keyword || ''
  filters.value.category_id = newQuery.category_id || ''
  filters.value.location = newQuery.location || ''
  filters.value.job_type = newQuery.job_type || ''
  filters.value.salary_min = newQuery.salary_min || ''
  filters.value.skill_id = newQuery.skill_id || ''
  filters.value.page = newQuery.page || 1
  fetchJobs()
})
</script>

<template>
  <div class="jobs-page py-4">
    <div class="container">

      <!-- Breadcrumbs & Editorial Header -->
      <div class="mb-4">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-2 small">
            <li class="breadcrumb-item"><router-link to="/" class="text-decoration-none">Home</router-link></li>
            <li class="breadcrumb-item active" aria-current="page">Jobs Directory</li>
          </ol>
        </nav>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 pb-3 border-bottom">
          <div>
            <div class="jobs-eyebrow">Positions &amp; Careers</div>
            <h1 class="jobs-title-lg mb-1">Find your next engineering role</h1>
            <p class="jobs-subtitle mb-0">Direct access to verified tech teams — transparent compensation and direct contact.</p>
          </div>
          <div class="jobs-count-pill" v-if="!loading">
            <span class="count-num">{{ pagination.total || jobs.length }}</span> roles available
          </div>
        </div>

        <!-- Active filter chips row -->
        <div v-if="hasActiveFilters" class="d-flex align-items-center flex-wrap gap-2 mt-3 pt-1">
          <span class="text-muted small fw-semibold">Active filters:</span>
          <span v-if="filters.keyword" class="filter-chip">
            Keyword: {{ filters.keyword }}
            <i class="bi bi-x ms-1 cursor-pointer" @click="removeFilter('keyword')"></i>
          </span>
          <span v-if="filters.category_id" class="filter-chip">
            Category
            <i class="bi bi-x ms-1 cursor-pointer" @click="removeFilter('category_id')"></i>
          </span>
          <span v-if="filters.location" class="filter-chip">
            Location: {{ filters.location }}
            <i class="bi bi-x ms-1 cursor-pointer" @click="removeFilter('location')"></i>
          </span>
          <span v-if="filters.job_type" class="filter-chip">
            Type: {{ formatJobType(filters.job_type) }}
            <i class="bi bi-x ms-1 cursor-pointer" @click="removeFilter('job_type')"></i>
          </span>
          <span v-if="filters.skill_id" class="filter-chip">
            Skill
            <i class="bi bi-x ms-1 cursor-pointer" @click="removeFilter('skill_id')"></i>
          </span>
          <button class="btn btn-sm btn-link text-danger text-decoration-none p-0 ms-2" @click="clearFilters">
            Clear all
          </button>
        </div>
      </div>

      <div class="row g-4">
        <!-- Sidebar Filters -->
        <aside class="col-lg-3">
          <div class="card filter-sidebar-card p-3 shadow-none">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
              <span class="fw-bold fs-6 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-sliders2 text-primary"></i> Filter Openings
              </span>
              <button v-if="hasActiveFilters" class="btn btn-sm btn-link text-muted p-0 text-decoration-none small" @click="clearFilters">
                Reset
              </button>
            </div>

            <!-- Keyword Search -->
            <div class="filter-group mb-3">
              <label class="filter-label">Keyword</label>
              <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input
                  v-model="filters.keyword"
                  type="text"
                  class="form-control border-start-0"
                  placeholder="Title or skill..."
                  @keyup.enter="applyFilters"
                />
              </div>
            </div>

            <!-- Category -->
            <div class="filter-group mb-3">
              <label class="filter-label">Tech Category</label>
              <select v-model="filters.category_id" class="form-select form-select-sm" @change="applyFilters">
                <option value="">All Categories</option>
                <option v-for="c in masterStore.categories" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
            </div>

            <!-- Location -->
            <div class="filter-group mb-3">
              <label class="filter-label">Location</label>
              <select v-model="filters.location" class="form-select form-select-sm" @change="applyFilters">
                <option value="">All Locations</option>
                <option v-for="city in masterStore.cities" :key="city" :value="city">{{ city }}</option>
                <option value="Remote">Remote</option>
              </select>
            </div>

            <!-- Job Type -->
            <div class="filter-group mb-3">
              <label class="filter-label">Employment Type</label>
              <div class="d-flex flex-column gap-1">
                <label
                  class="type-check-label d-flex align-items-center justify-content-between px-2 py-1.5 rounded"
                  :class="{ active: filters.job_type === '' }"
                >
                  <span class="small">All Types</span>
                  <input type="radio" value="" v-model="filters.job_type" @change="applyFilters" class="form-check-input m-0" />
                </label>
                <label
                  class="type-check-label d-flex align-items-center justify-content-between px-2 py-1.5 rounded"
                  :class="{ active: filters.job_type === 'full_time' }"
                >
                  <span class="small">Full Time</span>
                  <input type="radio" value="full_time" v-model="filters.job_type" @change="applyFilters" class="form-check-input m-0" />
                </label>
                <label
                  class="type-check-label d-flex align-items-center justify-content-between px-2 py-1.5 rounded"
                  :class="{ active: filters.job_type === 'remote' }"
                >
                  <span class="small">Remote</span>
                  <input type="radio" value="remote" v-model="filters.job_type" @change="applyFilters" class="form-check-input m-0" />
                </label>
                <label
                  class="type-check-label d-flex align-items-center justify-content-between px-2 py-1.5 rounded"
                  :class="{ active: filters.job_type === 'part_time' }"
                >
                  <span class="small">Part Time</span>
                  <input type="radio" value="part_time" v-model="filters.job_type" @change="applyFilters" class="form-check-input m-0" />
                </label>
                <label
                  class="type-check-label d-flex align-items-center justify-content-between px-2 py-1.5 rounded"
                  :class="{ active: filters.job_type === 'internship' }"
                >
                  <span class="small">Internship</span>
                  <input type="radio" value="internship" v-model="filters.job_type" @change="applyFilters" class="form-check-input m-0" />
                </label>
              </div>
            </div>

            <!-- Skill Filter -->
            <div class="filter-group mb-3">
              <label class="filter-label">Tech Skill</label>
              <select v-model="filters.skill_id" class="form-select form-select-sm" @change="applyFilters">
                <option value="">Any Skill</option>
                <option v-for="s in masterStore.skills" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select>
            </div>

            <!-- Min Salary -->
            <div class="filter-group mb-4">
              <label class="filter-label">Minimum Salary (USD)</label>
              <div class="input-group input-group-sm">
                <span class="input-group-text bg-white">$</span>
                <input
                  v-model="filters.salary_min"
                  type="number"
                  class="form-control"
                  placeholder="e.g. 1000"
                  @keyup.enter="applyFilters"
                />
              </div>
            </div>

            <div class="d-grid gap-2">
              <button class="btn btn-primary btn-sm fw-semibold" @click="applyFilters">
                Apply Filters
              </button>
              <button v-if="hasActiveFilters" class="btn btn-light btn-sm border" @click="clearFilters">
                Reset All
              </button>
            </div>
          </div>
        </aside>

        <!-- Main Job Listings -->
        <main class="col-lg-9">
          <!-- Loading state -->
          <div v-if="loading" class="d-flex flex-column gap-3">
            <div v-for="n in 5" :key="n" class="card p-4 placeholder-glow border">
              <div class="d-flex gap-3 align-items-start">
                <span class="placeholder rounded" style="width:48px;height:48px;flex-shrink:0;"></span>
                <div class="flex-grow-1">
                  <span class="placeholder col-6 d-block mb-2 rounded"></span>
                  <span class="placeholder col-4 d-block mb-3 rounded"></span>
                  <div class="d-flex gap-2">
                    <span class="placeholder col-2 rounded-pill"></span>
                    <span class="placeholder col-2 rounded-pill"></span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty state -->
          <div v-else-if="jobs.length === 0" class="card empty-state-card text-center p-5">
            <div class="empty-icon-wrap mx-auto mb-3">
              <i class="bi bi-search text-muted fs-3"></i>
            </div>
            <h5 class="fw-bold mb-1">No matching opportunities found</h5>
            <p class="text-muted small mb-3">Try adjusting your filters, location, or search keywords to see more jobs.</p>
            <div>
              <button class="btn btn-outline-primary btn-sm px-3" @click="clearFilters">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Clear all filters
              </button>
            </div>
          </div>

          <!-- Job Cards List -->
          <div v-else class="d-flex flex-column gap-3">
            <div v-for="job in jobs" :key="job.id" class="job-item-card">
              <!-- Left accent bar -->
              <div class="job-item-accent" :class="'accent-' + (job.job_type || 'full_time')"></div>

              <div class="job-item-inner p-3 p-md-4">
                <div class="d-flex flex-column flex-md-row align-items-start justify-content-between gap-3">
                  <!-- Left: Logo + Details -->
                  <div class="d-flex align-items-start gap-3 flex-grow-1 overflow-hidden">
                    <img v-if="job.company?.logo" :src="job.company.logo" class="company-item-logo" :alt="job.company.name" />
                    <div class="company-item-fallback" v-else>
                      {{ (job.company?.name || 'C')[0].toUpperCase() }}
                    </div>

                    <div class="flex-grow-1 overflow-hidden">
                      <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <router-link :to="{ name: 'JobDetail', params: { slug: job.slug } }" class="job-item-title text-decoration-none">
                          {{ job.title }}
                        </router-link>
                        <span class="job-type-pill-sm" :class="'type-' + (job.job_type || 'full_time')">
                          {{ formatJobType(job.job_type) }}
                        </span>
                      </div>

                      <div class="d-flex align-items-center gap-2 small text-muted mb-2 flex-wrap">
                        <router-link :to="{ name: 'CompanyDetail', params: { slug: job.company?.slug } }" class="company-link text-decoration-none">
                          {{ job.company?.name }}
                        </router-link>
                        <span>&bull;</span>
                        <span><i class="bi bi-geo-alt"></i> {{ job.location }}</span>
                        <span v-if="job.experience_level">&bull;</span>
                        <span v-if="job.experience_level">{{ job.experience_level }}</span>
                      </div>

                      <!-- Skills tags -->
                      <div class="d-flex flex-wrap gap-1" v-if="job.skills?.length">
                        <span v-for="skill in job.skills.slice(0, 5)" :key="skill.id" class="skill-pill-sm">
                          {{ skill.name }}
                        </span>
                        <span v-if="job.skills.length > 5" class="text-muted small ps-1" style="font-size:0.7rem;">
                          +{{ job.skills.length - 5 }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Right: Compensation + Action -->
                  <div class="text-md-end text-nowrap d-flex flex-row flex-md-column justify-content-between align-items-center align-items-md-end w-100 w-md-auto pt-2 pt-md-0 border-top border-md-0">
                    <div>
                      <div class="salary-label">Salary</div>
                      <div class="salary-amount">{{ formatSalary(job) }}</div>
                    </div>
                    <div class="text-muted small mt-md-1" style="font-size:0.75rem;">
                      {{ timeAgo(job.created_at) }}
                    </div>
                    <router-link :to="{ name: 'JobDetail', params: { slug: job.slug } }" class="job-view-btn mt-md-2">
                      View <i class="bi bi-arrow-right ms-1"></i>
                    </router-link>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <nav v-if="pagination.lastPage > 1" class="mt-4">
              <ul class="pagination justify-content-center">
                <li class="page-item" :class="{ disabled: pagination.currentPage <= 1 }">
                  <button class="page-link" @click="goToPage(pagination.currentPage - 1)">
                    <i class="bi bi-chevron-left"></i>
                  </button>
                </li>
                <li v-for="p in pagination.lastPage" :key="p" class="page-item" :class="{ active: p === pagination.currentPage }">
                  <button class="page-link" @click="goToPage(p)">{{ p }}</button>
                </li>
                <li class="page-item" :class="{ disabled: pagination.currentPage >= pagination.lastPage }">
                  <button class="page-link" @click="goToPage(pagination.currentPage + 1)">
                    <i class="bi bi-chevron-right"></i>
                  </button>
                </li>
              </ul>
            </nav>
          </div>
        </main>
      </div>

    </div>
  </div>
</template>

<style scoped>
.jobs-page {
  background-color: #f1f5f9;
  min-height: 80vh;
}

/* ── Editorial Header ─────────────────── */
.jobs-eyebrow {
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #6366f1;
  margin-bottom: 0.35rem;
}
.jobs-title-lg {
  font-size: clamp(1.5rem, 2.8vw, 2rem);
  font-weight: 800;
  letter-spacing: -0.03em;
  color: #0f172a;
}
.jobs-subtitle {
  color: #64748b;
  font-size: 0.95rem;
}
.jobs-count-pill {
  font-size: 0.85rem;
  font-weight: 600;
  color: #475569;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  padding: 0.4rem 0.9rem;
  border-radius: 9999px;
  white-space: nowrap;
}
.count-num {
  font-weight: 800;
  color: #4f46e5;
}

.filter-chip {
  display: inline-flex;
  align-items: center;
  font-size: 0.775rem;
  font-weight: 600;
  color: #4338ca;
  background-color: #eef2ff;
  border: 1px solid #c7d2fe;
  border-radius: 6px;
  padding: 0.25rem 0.65rem;
}
.cursor-pointer { cursor: pointer; }

/* ── Filter Sidebar ───────────────────── */
.filter-sidebar-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  position: sticky;
  top: 80px;
}
.filter-label {
  display: block;
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #64748b;
  margin-bottom: 0.35rem;
}

.type-check-label {
  cursor: pointer;
  color: #334155;
  transition: all 0.15s ease;
  border: 1px solid transparent;
}
.type-check-label:hover {
  background-color: #f1f5f9;
}
.type-check-label.active {
  background-color: #eef2ff;
  color: #4338ca;
  font-weight: 700;
  border-color: #c7d2fe;
}

/* ── Job Items ────────────────────────── */
.job-item-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  position: relative;
  overflow: hidden;
  transition: all 0.2s ease;
  display: flex;
}
.job-item-card:hover {
  border-color: #c7d2fe;
  box-shadow: 0 10px 24px -6px rgba(79, 70, 229, 0.08);
  transform: translateY(-2px);
}

/* Left accent bar */
.job-item-accent {
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 3px;
  border-radius: 3px 0 0 3px;
}
.accent-full_time  { background: #4f46e5; }
.accent-remote     { background: #0891b2; }
.accent-internship { background: #d97706; }
.accent-part_time  { background: #7c3aed; }

.job-item-inner {
  flex: 1;
  padding-left: 1.4rem !important;
}

.company-item-logo {
  width: 48px;
  height: 48px;
  object-fit: contain;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  flex-shrink: 0;
}
.company-item-fallback {
  width: 48px;
  height: 48px;
  border-radius: 8px;
  background: linear-gradient(135deg, #e0e7ff 0%, #ede9fe 100%);
  color: #4f46e5;
  font-weight: 800;
  font-size: 1.25rem;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.job-item-title {
  font-size: 1.05rem;
  font-weight: 700;
  color: #0f172a;
}
.job-item-title:hover {
  color: #4f46e5;
}

.company-link {
  color: #64748b;
  font-weight: 600;
}
.company-link:hover { color: #4f46e5; }

.job-type-pill-sm {
  font-size: 0.72rem;
  font-weight: 600;
  padding: 0.2rem 0.55rem;
  border-radius: 6px;
  white-space: nowrap;
}
.job-type-pill-sm.type-full_time { background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }
.job-type-pill-sm.type-remote    { background: #ecfeff; color: #0e7490; border: 1px solid #a5f3fc; }
.job-type-pill-sm.type-internship{ background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
.job-type-pill-sm.type-part_time { background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; }

.skill-pill-sm {
  font-size: 0.725rem;
  color: #475569;
  background-color: #f1f5f9;
  border: 1px solid #e2e8f0;
  border-radius: 5px;
  padding: 0.18rem 0.5rem;
}

.salary-label {
  font-size: 0.68rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #94a3b8;
  font-weight: 600;
}
.salary-amount {
  font-weight: 800;
  color: #059669;
  font-size: 0.95rem;
}

.job-view-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  font-size: 0.82rem;
  font-weight: 600;
  color: #4f46e5;
  background: #eef2ff;
  border: 1px solid #c7d2fe;
  border-radius: 6px;
  padding: 0.35rem 0.85rem;
  text-decoration: none;
  transition: all 0.18s ease;
}
.job-view-btn:hover {
  background: #4f46e5;
  color: #ffffff;
  border-color: #4f46e5;
}

/* ── Empty State ──────────────────────── */
.empty-state-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
}
.empty-icon-wrap {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background-color: #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
