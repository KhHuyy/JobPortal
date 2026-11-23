<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import api from '@/services/api'

const props = defineProps({ slug: String })
const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const job = ref(null)
const relatedJobs = ref([])
const loading = ref(true)
const error = ref(null)

// Apply modal state
const showApplyModal = ref(false)
const applying = ref(false)
const applySuccess = ref(false)
const applyError = ref(null)
const coverLetter = ref('')
const cvFile = ref(null)
const isSaved = ref(false)
const savingToggle = ref(false)

onMounted(async () => {
  await loadJob()
})

watch(() => route.params.slug, async () => {
  await loadJob()
  window.scrollTo({ top: 0, behavior: 'smooth' })
})

const FALLBACK_DETAIL_MAP = {
  'senior-full-stack-engineer-techcorp': {
    id: 1,
    title: 'Senior Full Stack Engineer (Vue 3 / Laravel)',
    slug: 'senior-full-stack-engineer-techcorp',
    job_type: 'full_time',
    location: 'Ho Chi Minh City',
    salary_min: 2500,
    salary_max: 3800,
    vacancies: 2,
    experience_level: 'Senior',
    views_count: 142,
    deadline: '2026-12-31',
    description: `We are looking for a Senior Full Stack Engineer to lead the architecture and implementation of our high-throughput recruitment and workflow platform.\n\nYou will work closely with product managers, UX designers, and backend engineering teams to deliver responsive, accessible, and high-performance web applications.`,
    requirements: `- 4+ years of professional full-stack development experience.\n- Strong proficiency in modern Vue 3 (Composition API, Pinia, Vue Router) or React.\n- Proven expertise in Laravel 11, RESTful API design, and MySQL/PostgreSQL optimization.\n- Hands-on experience with Docker, CI/CD pipelines, and cloud environments (AWS/GCP).`,
    benefits: `- Competitive compensation up to $3,800/month with annual performance bonuses.\n- Flexible remote/hybrid working model with MacBook Pro provided.\n- Premium private healthcare package for you and your family.\n- Annual professional training budget and international tech conference sponsorship.`,
    company: { name: 'TechCorp Vietnam', slug: 'techcorp-vietnam', is_verified: true, city: 'Ho Chi Minh City', company_size: '250-500 employees', website: 'https://techcorp.vn', logo: null },
    skills: [{ id: 1, name: 'Vue.js' }, { id: 2, name: 'Laravel' }, { id: 3, name: 'PostgreSQL' }, { id: 4, name: 'Docker' }],
    category: { name: 'Software Engineering' },
  },
  'staff-backend-architect-vng': {
    id: 2,
    title: 'Staff Backend Architect (Go / Cloud Native)',
    slug: 'staff-backend-architect-vng',
    job_type: 'remote',
    location: 'Remote, Vietnam',
    salary_min: 3200,
    salary_max: 4500,
    vacancies: 1,
    experience_level: 'Lead / Principal',
    views_count: 210,
    deadline: '2026-12-31',
    description: `As a Staff Backend Architect, you will own the distributed systems architecture handling millions of requests per second across our cloud ecosystem.`,
    requirements: `- 6+ years designing scalable distributed backends.\n- Deep mastery of Go (Golang), gRPC, and event-driven architecture (Kafka/RabbitMQ).\n- Experience with Kubernetes orchestration, service mesh, and observability (Prometheus/Grafana).`,
    benefits: `- Attractive compensation up to $4,500/month with stock options.\n- 100% remote freedom with work-from-anywhere policy.\n- Comprehensive wellness and premium health insurance.`,
    company: { name: 'VNG Corporation', slug: 'vng-corporation', is_verified: true, city: 'Ho Chi Minh City', company_size: '1000+ employees', website: 'https://vng.com.vn', logo: null },
    skills: [{ id: 5, name: 'Golang' }, { id: 6, name: 'Kubernetes' }, { id: 7, name: 'AWS' }, { id: 8, name: 'gRPC' }],
    category: { name: 'Data & AI' },
  },
}

async function loadJob() {
  loading.value = true
  error.value = null
  const slug = props.slug || route.params.slug
  try {
    const res = await api.get(`/jobs/${slug}`)
    job.value = res.data.data.job
    relatedJobs.value = res.data.data.related_jobs || []
  } catch (err) {
    if (FALLBACK_DETAIL_MAP[slug]) {
      job.value = FALLBACK_DETAIL_MAP[slug]
      relatedJobs.value = [
        { id: 2, title: 'Staff Backend Architect (Go / Cloud)', slug: 'staff-backend-architect-vng', salary_min: 3200, salary_max: 4500, company: { name: 'VNG Corporation' } },
        { id: 3, title: 'Senior DevOps / Platform Engineer', slug: 'senior-devops-platform-engineer-fpt', salary_min: 2200, salary_max: 3400, company: { name: 'FPT Software' } },
      ]
    } else {
      error.value = err.response?.status === 404 ? 'Job opening not found or has been closed.' : 'Failed to load job details.'
    }
  } finally {
    loading.value = false
  }
}

async function toggleSave() {
  if (!auth.isAuthenticated) return router.push({ name: 'Login' })
  if (!auth.isCandidate) return
  savingToggle.value = true
  try {
    await api.post(`/candidate/jobs/${job.value.id}/toggle-save`)
    isSaved.value = !isSaved.value
  } finally {
    savingToggle.value = false
  }
}

function handleCvFile(e) {
  cvFile.value = e.target.files[0] || null
}

async function submitApplication() {
  if (!auth.isAuthenticated) return router.push({ name: 'Login' })
  applying.value = true
  applyError.value = null
  try {
    const formData = new FormData()
    if (cvFile.value) formData.append('cv_file', cvFile.value)
    if (coverLetter.value) formData.append('cover_letter', coverLetter.value)
    await api.post(`/candidate/jobs/${job.value.id}/apply`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    applySuccess.value = true
  } catch (err) {
    applyError.value = err.response?.data?.message || 'Application submission failed. Please try again.'
  } finally {
    applying.value = false
  }
}

function openApplyModal() {
  if (!auth.isAuthenticated) return router.push({ name: 'Login' })
  if (!auth.isCandidate) return
  showApplyModal.value = true
  applySuccess.value = false
  applyError.value = null
  coverLetter.value = ''
  cvFile.value = null
}

function formatJobType(type) {
  const map = { full_time: 'Full Time', part_time: 'Part Time', remote: 'Remote', internship: 'Internship' }
  return map[type] || type
}

function formatSalary(j) {
  if (!j) return ''
  if (!j.salary_min && !j.salary_max) return 'Negotiable'
  if (j.salary_min && j.salary_max) return `$${Number(j.salary_min).toLocaleString()} – $${Number(j.salary_max).toLocaleString()}`
  if (j.salary_min) return `From $${Number(j.salary_min).toLocaleString()}`
  return `Up to $${Number(j.salary_max).toLocaleString()}`
}

const deadlineFormatted = computed(() => {
  if (!job.value?.deadline) return 'Open until filled'
  return new Date(job.value.deadline).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
})
</script>

<template>
  <div class="job-detail-page py-4">
    <div class="container">

      <!-- Breadcrumbs -->
      <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
          <li class="breadcrumb-item"><router-link to="/" class="text-decoration-none">Home</router-link></li>
          <li class="breadcrumb-item"><router-link to="/jobs" class="text-decoration-none">Jobs</router-link></li>
          <li class="breadcrumb-item active text-truncate" aria-current="page" style="max-width: 320px;">
            {{ job?.title || 'Job Detail' }}
          </li>
        </ol>
      </nav>

      <!-- Loading skeleton -->
      <div v-if="loading" class="row g-4">
        <div class="col-lg-8">
          <div class="card p-4 mb-4 placeholder-glow border">
            <div class="d-flex gap-3 mb-3">
              <span class="placeholder rounded" style="width:64px;height:64px;"></span>
              <div class="flex-grow-1">
                <span class="placeholder col-8 d-block mb-2 rounded"></span>
                <span class="placeholder col-4 d-block mb-2 rounded"></span>
              </div>
            </div>
            <div class="row pt-3 border-top g-3">
              <div class="col-3" v-for="n in 4" :key="n"><span class="placeholder col-12 rounded"></span></div>
            </div>
          </div>
          <div class="card p-4 placeholder-glow border">
            <span class="placeholder col-4 d-block mb-3 rounded"></span>
            <span class="placeholder col-12 d-block mb-2 rounded"></span>
            <span class="placeholder col-10 d-block rounded"></span>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card p-4 placeholder-glow border">
            <span class="placeholder col-6 d-block mb-3 rounded mx-auto"></span>
            <span class="placeholder rounded mx-auto d-block mb-3" style="width:72px;height:72px;"></span>
          </div>
        </div>
      </div>

      <!-- Error message -->
      <div v-else-if="error" class="card text-center p-5 border">
        <i class="bi bi-exclamation-circle text-danger display-4 mb-3"></i>
        <h4 class="fw-bold">{{ error }}</h4>
        <p class="text-muted small">This job opening may have expired or was removed by the employer.</p>
        <div class="mt-2">
          <router-link to="/jobs" class="btn btn-primary btn-sm px-4">
            <i class="bi bi-arrow-left me-1"></i> Back to Jobs Directory
          </router-link>
        </div>
      </div>

      <!-- Real Job Detail -->
      <div v-else-if="job" class="row g-4">

        <!-- Left Main Column (68%) -->
        <main class="col-lg-8">
          <!-- Job Header Card -->
          <div class="job-header-card position-relative overflow-hidden p-4 mb-4">
            <div class="job-detail-accent" :class="'accent-' + (job.job_type || 'full_time')"></div>

            <div class="d-flex align-items-start gap-3">
              <img v-if="job.company?.logo" :src="job.company.logo" class="detail-logo" :alt="job.company.name" />
              <div class="detail-logo-fallback" v-else>
                {{ (job.company?.name || 'C')[0].toUpperCase() }}
              </div>

              <div class="flex-grow-1 overflow-hidden">
                <h1 class="job-detail-title mb-1">{{ job.title }}</h1>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                  <router-link :to="{ name: 'CompanyDetail', params: { slug: job.company?.slug } }" class="fw-semibold text-primary text-decoration-none">
                    {{ job.company?.name }}
                  </router-link>
                  <span v-if="job.company?.is_verified" class="badge bg-primary-subtle text-primary border border-primary-subtle small">
                    <i class="bi bi-patch-check-fill me-1"></i>Verified
                  </span>
                  <span class="text-muted">&bull;</span>
                  <span class="text-muted small"><i class="bi bi-geo-alt"></i> {{ job.location }}</span>
                </div>

                <div class="d-flex flex-wrap gap-2">
                  <span class="job-type-pill" :class="'type-' + (job.job_type || 'full_time')">{{ formatJobType(job.job_type) }}</span>
                  <span class="badge bg-light text-dark border" v-if="job.experience_level">{{ job.experience_level }}</span>
                  <span class="badge bg-light text-dark border" v-if="job.category?.name">{{ job.category.name }}</span>
                  <span class="badge bg-success-subtle text-success border border-success-subtle" v-if="job.vacancies > 1">
                    {{ job.vacancies }} Openings
                  </span>
                </div>
              </div>
            </div>

            <!-- Key Metrics Strip -->
            <div class="metrics-grid mt-4 pt-3 border-top">
              <div class="metric-cell">
                <div class="metric-label">Compensation</div>
                <div class="metric-val text-success">{{ formatSalary(job) }}</div>
              </div>
              <div class="metric-cell">
                <div class="metric-label">Deadline</div>
                <div class="metric-val text-dark">{{ deadlineFormatted }}</div>
              </div>
              <div class="metric-cell">
                <div class="metric-label">Work Mode</div>
                <div class="metric-val text-dark">{{ job.job_type === 'remote' ? '100% Remote' : job.location }}</div>
              </div>
              <div class="metric-cell">
                <div class="metric-label">Activity</div>
                <div class="metric-val text-dark">{{ job.views_count || 0 }} views</div>
              </div>
            </div>
          </div>

          <!-- Job Description -->
          <div class="content-card p-4 mb-4">
            <h5 class="content-heading mb-3">
              <i class="bi bi-file-earmark-text text-primary"></i> Job Overview &amp; Responsibilities
            </h5>
            <div class="prose-content">{{ job.description }}</div>
          </div>

          <!-- Requirements -->
          <div class="content-card p-4 mb-4">
            <h5 class="content-heading mb-3">
              <i class="bi bi-check2-circle text-primary"></i> Candidate Qualifications &amp; Skills
            </h5>
            <div class="prose-content">{{ job.requirements }}</div>
          </div>

          <!-- Benefits -->
          <div class="content-card p-4 mb-4" v-if="job.benefits">
            <h5 class="content-heading mb-3">
              <i class="bi bi-gift text-primary"></i> Benefits &amp; Working Perks
            </h5>
            <div class="prose-content">{{ job.benefits }}</div>
          </div>

          <!-- Required Skills -->
          <div class="content-card p-4 mb-4" v-if="job.skills?.length">
            <h5 class="content-heading mb-3">
              <i class="bi bi-tags text-primary"></i> Required Tech Stack
            </h5>
            <div class="d-flex flex-wrap gap-2">
              <span v-for="skill in job.skills" :key="skill.id" class="badge bg-light text-dark border px-3 py-2 fs-6 fw-semibold">
                {{ skill.name }}
              </span>
            </div>
          </div>
        </main>

        <!-- Right Sticky Sidebar (32%) -->
        <aside class="col-lg-4">
          <!-- Sticky Apply Box -->
          <div class="apply-widget-card p-4 mb-4">
            <h5 class="fw-bold mb-1 text-dark">Ready to Apply?</h5>
            <p class="text-muted small mb-3">Send your application directly to the hiring manager.</p>

            <div class="d-grid gap-2 mb-3">
              <!-- If Candidate -->
              <template v-if="auth.isCandidate">
                <button class="btn btn-primary btn-lg shadow-sm fw-bold" @click="openApplyModal">
                  <i class="bi bi-send me-1"></i> Apply for this position
                </button>
                <button
                  class="btn btn-outline-secondary"
                  :class="{ 'btn-warning border-warning text-dark': isSaved }"
                  @click="toggleSave"
                  :disabled="savingToggle"
                >
                  <i :class="isSaved ? 'bi-bookmark-fill' : 'bi-bookmark'" class="bi me-1"></i>
                  {{ isSaved ? 'Saved in My Jobs' : 'Save Job' }}
                </button>
              </template>

              <!-- If Guest -->
              <template v-else-if="!auth.isAuthenticated">
                <router-link :to="{ name: 'Login', query: { redirect: route.fullPath } }" class="btn btn-primary btn-lg shadow-sm fw-bold">
                  Sign in to Apply
                </router-link>
                <router-link to="/register" class="btn btn-outline-secondary">
                  Create candidate account
                </router-link>
              </template>

              <!-- If Recruiter or Admin -->
              <template v-else>
                <div class="alert alert-info small mb-0">
                  <i class="bi bi-info-circle me-1"></i> You are logged in as a <strong>{{ auth.role }}</strong>.
                </div>
              </template>
            </div>

            <div class="d-flex align-items-center gap-2 text-muted small pt-2 border-top">
              <i class="bi bi-shield-check text-success fs-5"></i>
              <span>Direct application &bull; No middleman &bull; Free</span>
            </div>
          </div>

          <!-- Company Info Card -->
          <div class="content-card p-4 mb-4">
            <h6 class="fw-bold text-uppercase text-muted small mb-3" style="letter-spacing:0.06em; font-size: 0.72rem;">About the Employer</h6>
            <div class="text-center mb-3">
              <img v-if="job.company?.logo" :src="job.company.logo" class="company-sidebar-logo mx-auto mb-2" :alt="job.company.name" />
              <div v-else class="company-sidebar-fallback mx-auto mb-2">
                {{ (job.company?.name || 'C')[0] }}
              </div>
              <h6 class="fw-bold mb-1 text-dark">{{ job.company?.name }}</h6>
              <span v-if="job.company?.is_verified" class="badge bg-success-subtle text-success border border-success-subtle small">
                <i class="bi bi-patch-check-fill me-1"></i>Verified Company
              </span>
            </div>

            <ul class="list-unstyled small text-muted mb-3 d-flex flex-column gap-2">
              <li v-if="job.company?.city" class="d-flex align-items-center gap-2">
                <i class="bi bi-geo-alt text-primary"></i> {{ job.company.city }}
              </li>
              <li v-if="job.company?.company_size" class="d-flex align-items-center gap-2">
                <i class="bi bi-people text-primary"></i> {{ job.company.company_size }}
              </li>
              <li v-if="job.company?.website" class="d-flex align-items-center gap-2">
                <i class="bi bi-globe text-primary"></i>
                <a :href="job.company.website" target="_blank" class="text-truncate text-decoration-none">
                  {{ job.company.website }}
                </a>
              </li>
            </ul>

            <router-link :to="{ name: 'CompanyDetail', params: { slug: job.company?.slug } }" class="btn btn-outline-primary btn-sm w-100 fw-semibold">
              View Company Profile &rarr;
            </router-link>
          </div>

          <!-- Related Jobs -->
          <div class="content-card p-4" v-if="relatedJobs.length">
            <h6 class="fw-bold text-uppercase text-muted small mb-3" style="letter-spacing:0.06em; font-size: 0.72rem;">Similar Opportunities</h6>
            <div class="d-flex flex-column gap-3">
              <div v-for="rj in relatedJobs" :key="rj.id" class="related-job-item pb-2 border-bottom">
                <router-link :to="{ name: 'JobDetail', params: { slug: rj.slug } }" class="fw-semibold text-dark text-decoration-none d-block">
                  {{ rj.title }}
                </router-link>
                <div class="d-flex justify-content-between align-items-center small text-muted mt-1">
                  <span>{{ rj.company?.name }}</span>
                  <span class="text-success fw-semibold">{{ formatSalary(rj) }}</span>
                </div>
              </div>
            </div>
          </div>
        </aside>

      </div>
    </div>

    <!-- Apply Modal -->
    <div v-if="showApplyModal" class="modal d-block" style="background:rgba(15,23,42,0.6);backdrop-filter:blur(4px);">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
          <div class="modal-header border-bottom">
            <div>
              <h5 class="modal-title fw-bold">Apply for {{ job?.title }}</h5>
              <div class="text-muted small">At {{ job?.company?.name }}</div>
            </div>
            <button type="button" class="btn-close" @click="showApplyModal = false"></button>
          </div>

          <div class="modal-body p-4">
            <div v-if="applySuccess" class="text-center py-4">
              <i class="bi bi-check-circle-fill text-success display-3 mb-3 d-block"></i>
              <h4 class="fw-bold">Application Sent Successfully!</h4>
              <p class="text-muted small max-w-sm mx-auto">
                Your application has been received by {{ job?.company?.name }}. You can track the status in your Candidate Workspace.
              </p>
              <div class="d-flex justify-content-center gap-2 mt-3">
                <router-link to="/candidate/applications" class="btn btn-primary btn-sm px-4">
                  Go to My Applications
                </router-link>
                <button class="btn btn-outline-secondary btn-sm" @click="showApplyModal = false">
                  Close
                </button>
              </div>
            </div>

            <div v-else>
              <div v-if="applyError" class="alert alert-danger small">{{ applyError }}</div>

              <div class="mb-3">
                <label class="form-label fw-bold small text-uppercase" style="letter-spacing:0.04em;">
                  Upload Resume / CV (PDF) <span class="text-danger">*</span>
                </label>
                <input type="file" class="form-control" accept=".pdf" @change="handleCvFile" />
                <div class="form-text small">
                  Upload a PDF version (max 5MB). If left empty, your default profile CV will be submitted automatically.
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-bold small text-uppercase" style="letter-spacing:0.04em;">
                  Cover Letter / Note to Recruiter <span class="text-muted fw-normal">(Optional)</span>
                </label>
                <textarea
                  v-model="coverLetter"
                  class="form-control"
                  rows="5"
                  placeholder="Introduce yourself, your technical background, and why you are excited about this position..."
                ></textarea>
              </div>
            </div>
          </div>

          <div class="modal-footer border-top" v-if="!applySuccess">
            <button class="btn btn-light border btn-sm px-3" @click="showApplyModal = false">Cancel</button>
            <button class="btn btn-primary btn-sm px-4" @click="submitApplication" :disabled="applying">
              <span v-if="applying" class="spinner-border spinner-border-sm me-1"></span>
              {{ applying ? 'Submitting Application...' : 'Send Application' }}
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<style scoped>
.job-detail-page {
  background-color: #f1f5f9;
  min-height: 80vh;
}

.job-header-card {
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  background: #ffffff;
}

.job-detail-accent {
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

.job-detail-title {
  font-size: clamp(1.4rem, 2.5vw, 1.85rem);
  font-weight: 800;
  letter-spacing: -0.03em;
  color: #0f172a;
}

.job-type-pill {
  font-size: 0.75rem;
  font-weight: 600;
  padding: 0.25rem 0.65rem;
  border-radius: 6px;
  white-space: nowrap;
}
.job-type-pill.type-full_time { background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }
.job-type-pill.type-remote    { background: #ecfeff; color: #0e7490; border: 1px solid #a5f3fc; }
.job-type-pill.type-internship{ background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
.job-type-pill.type-part_time { background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; }

.detail-logo {
  width: 64px;
  height: 64px;
  object-fit: contain;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
}
.detail-logo-fallback {
  width: 64px;
  height: 64px;
  border-radius: 10px;
  background: linear-gradient(135deg, #e0e7ff 0%, #ede9fe 100%);
  color: #4f46e5;
  font-weight: 800;
  font-size: 1.6rem;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* ── Metrics Grid ─────────────────────── */
.metrics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
  gap: 1rem;
}
.metric-cell {
  background: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 8px;
  padding: 0.75rem 0.9rem;
}
.metric-label {
  font-size: 0.68rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #64748b;
  margin-bottom: 0.2rem;
}
.metric-val {
  font-size: 0.95rem;
  font-weight: 800;
}

/* ── Content Cards ────────────────────── */
.content-card {
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  background: #ffffff;
}
.content-heading {
  font-size: 1.05rem;
  font-weight: 700;
  color: #0f172a;
}
.prose-content {
  white-space: pre-line;
  line-height: 1.7;
  color: #334155;
  font-size: 0.95rem;
}

/* ── Sticky Apply Widget ──────────────── */
.apply-widget-card {
  position: sticky;
  top: 80px;
  border: 1px solid #c7d2fe;
  border-radius: 14px;
  background: #ffffff;
  box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.08);
}

.company-sidebar-logo {
  width: 56px;
  height: 56px;
  object-fit: contain;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}
.company-sidebar-fallback {
  width: 56px;
  height: 56px;
  border-radius: 8px;
  background: linear-gradient(135deg, #e0e7ff 0%, #ede9fe 100%);
  color: #4f46e5;
  font-weight: 800;
  font-size: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.related-job-item:last-child {
  border-bottom: none !important;
  padding-bottom: 0 !important;
}
</style>
