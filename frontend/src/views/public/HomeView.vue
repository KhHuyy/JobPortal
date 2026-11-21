<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useMasterStore } from '@/stores/masterStore'
import api from '@/services/api'

const router = useRouter()
const masterStore = useMasterStore()

const keyword = ref('')
const selectedLocation = ref('')
const latestJobs = ref([])
const topCompanies = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    await masterStore.fetchMasterData()
    const [jobsRes, companiesRes] = await Promise.all([
      api.get('/jobs', { params: { per_page: 6 } }),
      api.get('/companies'),
    ])
    latestJobs.value = (jobsRes.data.data?.data || jobsRes.data.data || []).slice(0, 6)
    topCompanies.value = (companiesRes.data.data || []).slice(0, 6)
  } catch (err) {
    console.error('Failed to load home data:', err)
  } finally {
    loading.value = false
  }
})

function searchJobs() {
  const query = {}
  if (keyword.value.trim()) query.keyword = keyword.value.trim()
  if (selectedLocation.value) query.location = selectedLocation.value
  router.push({ name: 'Jobs', query })
}

function quickSearch(tag) {
  keyword.value = tag
  searchJobs()
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

// Color palette mapping for category tiles
function getCategoryColorClass(slug) {
  const map = {
    'software-engineering': 'cat-indigo',
    'data-ai':             'cat-violet',
    'product-design':      'cat-rose',
    'devops-cloud':        'cat-cyan',
    'qa-testing':          'cat-amber',
    'mobile-development':  'cat-teal',
    'cybersecurity':       'cat-slate',
    'finance-banking':     'cat-emerald',
    'marketing-growth':    'cat-orange',
    'hr-operations':       'cat-fuchsia',
  }
  return map[slug] || 'cat-indigo'
}
</script>

<template>
  <div class="home-page">

    <!-- ── Hero Section ─────────────────────────── -->
    <section class="hero-section position-relative overflow-hidden">
      <!-- Ambient colorful glow orbs -->
      <div class="ambient-glow glow-1"></div>
      <div class="ambient-glow glow-2"></div>
      <div class="ambient-glow glow-3"></div>

      <div class="container position-relative z-1">
        <!-- Live status pill -->
        <div class="d-flex justify-content-center mb-3">
          <div class="live-status-pill shadow-sm">
            <span class="live-indicator"></span>
            <span>Over 35+ verified tech roles open today</span>
          </div>
        </div>

        <!-- Headline & Subtitle -->
        <div class="row justify-content-center text-center">
          <div class="col-lg-9 col-xl-8">
            <h1 class="hero-title">
              Find Your Next Role in <br class="d-none d-md-block" />
              <span class="hero-highlight">Software &amp; Technology</span>
            </h1>
            <p class="hero-lead mt-3">
              Connect directly with verified tech engineering teams. Transparent salary ranges, curated openings, and instant ATS application tracking.
            </p>
          </div>
        </div>

        <!-- Multi-segment Floating Search Bar -->
        <div class="row justify-content-center mt-4">
          <div class="col-lg-10 col-xl-9">
            <div class="hero-search-card">
              <form @submit.prevent="searchJobs" class="d-flex flex-column flex-md-row align-items-stretch gap-2">
                <!-- Keyword Segment -->
                <div class="search-input-segment flex-grow-1 d-flex align-items-center px-3 py-2">
                  <i class="bi bi-search text-primary me-2 fs-6"></i>
                  <input
                    v-model="keyword"
                    type="text"
                    class="form-control border-0 bg-transparent shadow-none p-0"
                    placeholder="Role, tech stack, or keyword..."
                  />
                  <button v-if="keyword" type="button" class="btn btn-sm btn-link text-muted p-0 ms-2" @click="keyword = ''">
                    <i class="bi bi-x-circle-fill"></i>
                  </button>
                </div>

                <div class="vr d-none d-md-block my-1 text-muted opacity-25"></div>

                <!-- Location Segment -->
                <div class="search-location-segment d-flex align-items-center px-3 py-2">
                  <i class="bi bi-geo-alt text-danger me-2 fs-6"></i>
                  <select v-model="selectedLocation" class="form-select border-0 bg-transparent shadow-none p-0 pe-4">
                    <option value="">All Locations</option>
                    <option v-for="city in masterStore.cities" :key="city" :value="city">{{ city }}</option>
                    <option value="Remote">Remote</option>
                  </select>
                </div>

                <!-- Search Action Button -->
                <button type="submit" class="btn btn-primary px-4 py-2 d-flex align-items-center justify-content-center gap-2">
                  <span>Search Jobs</span>
                  <i class="bi bi-arrow-right"></i>
                </button>
              </form>

              <!-- Quick Filter Chips -->
              <div class="d-flex align-items-center flex-wrap gap-2 pt-3 px-1 border-top mt-2">
                <span class="text-muted small fw-semibold">Popular:</span>
                <button type="button" class="quick-tag-chip chip-frontend" @click="quickSearch('Frontend')">Frontend</button>
                <button type="button" class="quick-tag-chip chip-backend"  @click="quickSearch('Backend')">Backend</button>
                <button type="button" class="quick-tag-chip chip-devops"   @click="quickSearch('DevOps')">DevOps</button>
                <button type="button" class="quick-tag-chip chip-ai"       @click="quickSearch('AI & ML')">AI &amp; ML</button>
                <button type="button" class="quick-tag-chip chip-remote"   @click="quickSearch('Remote')">Remote</button>
                <button type="button" class="quick-tag-chip chip-golang"   @click="quickSearch('Golang')">Golang</button>
                <button type="button" class="quick-tag-chip chip-java"     @click="quickSearch('Java')">Java</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Trust Metrics Bar -->
        <div class="row justify-content-center mt-5 pt-2">
          <div class="col-lg-9">
            <div class="metrics-strip shadow-sm d-flex align-items-center justify-content-around flex-wrap gap-3">
              <div class="metric-item d-flex align-items-center gap-2">
                <div class="metric-icon-box box-primary"><i class="bi bi-patch-check-fill"></i></div>
                <div>
                  <div class="metric-value">100% Verified</div>
                  <div class="metric-label">Direct Employer Jobs</div>
                </div>
              </div>
              <div class="metric-divider d-none d-md-block"></div>
              <div class="metric-item d-flex align-items-center gap-2">
                <div class="metric-icon-box box-success"><i class="bi bi-cash-stack"></i></div>
                <div>
                  <div class="metric-value">Transparent Pay</div>
                  <div class="metric-label">Upfront Salary Ranges</div>
                </div>
              </div>
              <div class="metric-divider d-none d-md-block"></div>
              <div class="metric-item d-flex align-items-center gap-2">
                <div class="metric-icon-box box-warning"><i class="bi bi-lightning-charge-fill"></i></div>
                <div>
                  <div class="metric-value">Real-Time ATS</div>
                  <div class="metric-label">Direct Pipeline Tracking</div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- ── Categories Section ────────────────────── -->
    <section class="py-5 bg-white border-bottom">
      <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
          <div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-2">SPECIALIZATIONS</span>
            <h2 class="section-title mb-0">Browse by Tech Domain</h2>
            <p class="text-muted small mb-0 mt-1">Explore opportunities across specialized technical fields</p>
          </div>
          <router-link to="/jobs" class="btn btn-sm btn-outline-primary mt-3 mt-md-0 d-inline-flex align-items-center gap-1">
            <span>All categories</span>
            <i class="bi bi-arrow-right"></i>
          </router-link>
        </div>

        <div class="row g-3">
          <div v-for="cat in masterStore.categories" :key="cat.id" class="col-6 col-md-4 col-lg-3">
            <div
              class="category-card-modern p-3 d-flex align-items-center gap-3"
              :class="getCategoryColorClass(cat.slug)"
              @click="router.push({ name: 'Jobs', query: { category_id: cat.id } })"
            >
              <div class="cat-icon-wrapper">
                <i :class="'bi ' + (cat.icon || 'bi-folder2')"></i>
              </div>
              <div class="overflow-hidden">
                <div class="cat-title fw-semibold text-truncate">{{ cat.name }}</div>
                <div class="cat-action small text-muted">Browse roles <i class="bi bi-chevron-right micro-icon"></i></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ── Latest Jobs Section ───────────────────── -->
    <section class="py-5 border-bottom bg-canvas">
      <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
          <div>
            <div class="badge badge-curated mb-2">CURATED OPENINGS</div>
            <h2 class="section-title mb-0">Featured Software &amp; Tech Jobs</h2>
            <p class="text-muted small mb-0 mt-1">Recently posted positions waiting for candidates like you</p>
          </div>
          <router-link to="/jobs" class="btn btn-sm btn-outline-primary mt-3 mt-md-0 d-inline-flex align-items-center gap-1">
            <span>Explore all jobs</span>
            <i class="bi bi-arrow-right"></i>
          </router-link>
        </div>

        <!-- Skeleton Loading -->
        <div v-if="loading" class="row g-3">
          <div v-for="n in 6" :key="n" class="col-md-6 col-lg-4">
            <div class="card p-4 h-100 placeholder-glow border">
              <div class="d-flex gap-3 align-items-start mb-3">
                <span class="placeholder rounded" style="width:44px;height:44px;flex-shrink:0"></span>
                <div class="flex-grow-1">
                  <span class="placeholder col-8 d-block mb-2 rounded"></span>
                  <span class="placeholder col-5 rounded"></span>
                </div>
              </div>
              <span class="placeholder col-10 rounded mb-2 d-inline-block"></span>
              <span class="placeholder col-6 rounded mb-4 d-inline-block"></span>
              <div class="pt-3 border-top mt-auto d-flex justify-content-between align-items-center">
                <span class="placeholder col-4 rounded"></span>
                <span class="placeholder col-3 rounded"></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Real Job Cards -->
        <div v-else class="row g-3">
          <div v-for="job in latestJobs" :key="job.id" class="col-md-6 col-lg-4">
            <div class="card job-card-premium h-100 p-4 d-flex flex-column">
              <!-- Top Row: Logo + Company + Type Pill -->
              <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                <div class="d-flex align-items-center gap-3 overflow-hidden">
                  <img v-if="job.company?.logo" :src="job.company.logo" class="company-logo-avatar" :alt="job.company.name" />
                  <div class="company-logo-fallback" v-else>
                    {{ (job.company?.name || 'C')[0].toUpperCase() }}
                  </div>
                  <div class="overflow-hidden">
                    <router-link :to="{ name: 'CompanyDetail', params: { slug: job.company?.slug } }" class="company-name-link text-truncate d-block">
                      {{ job.company?.name }}
                    </router-link>
                    <span class="text-muted small d-inline-flex align-items-center gap-1">
                      <i class="bi bi-geo-alt text-primary"></i> {{ job.location }}
                    </span>
                  </div>
                </div>

                <span class="job-type-pill" :class="'type-' + (job.job_type || 'full_time')">
                  {{ formatJobType(job.job_type) }}
                </span>
              </div>

              <!-- Job Title -->
              <h6 class="job-title-heading fw-bold mb-2">
                <router-link :to="{ name: 'JobDetail', params: { slug: job.slug } }" class="text-dark text-decoration-none">
                  {{ job.title }}
                </router-link>
              </h6>

              <!-- Skills & Tags row -->
              <div class="d-flex flex-wrap gap-1 mb-3">
                <span v-for="skill in (job.skills || []).slice(0, 3)" :key="skill.id" class="skill-tag">
                  {{ skill.name }}
                </span>
                <span v-if="(job.skills || []).length > 3" class="skill-tag-more">
                  +{{ job.skills.length - 3 }}
                </span>
                <span v-if="!job.skills?.length && job.experience_level" class="skill-tag">
                  {{ job.experience_level }}
                </span>
              </div>

              <!-- Footer: Compensation + View button -->
              <div class="pt-3 border-top mt-auto d-flex align-items-center justify-content-between">
                <div>
                  <div class="salary-label">Compensation</div>
                  <div class="salary-highlight">{{ formatSalary(job) }}</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <small class="text-muted" style="font-size:0.75rem;">{{ timeAgo(job.created_at) }}</small>
                  <router-link :to="{ name: 'JobDetail', params: { slug: job.slug } }" class="btn btn-sm btn-primary-soft">
                    <span>View</span>
                    <i class="bi bi-arrow-right"></i>
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ── Top Employers Section ─────────────────── -->
    <section class="py-5 bg-white border-bottom">
      <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
          <div>
            <span class="badge bg-success-subtle text-success border border-success-subtle mb-2">HIRING PARTNERS</span>
            <h2 class="section-title mb-0">Featured Tech Employers</h2>
            <p class="text-muted small mb-0 mt-1">Leading software companies and innovative product teams</p>
          </div>
          <router-link to="/companies" class="btn btn-sm btn-outline-primary mt-3 mt-md-0 d-inline-flex align-items-center gap-1">
            <span>All employers</span>
            <i class="bi bi-arrow-right"></i>
          </router-link>
        </div>

        <div class="row g-3">
          <div v-for="company in topCompanies" :key="company.id" class="col-md-4 col-lg-3">
            <div class="card company-card-modern h-100 p-4 text-center d-flex flex-column">
              <div class="company-badge-icon mx-auto mb-3">
                <img v-if="company.logo" :src="company.logo" class="company-logo-avatar-lg" :alt="company.name" />
                <div v-else class="company-logo-fallback-lg">
                  {{ company.name[0] }}
                </div>
              </div>

              <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                <h6 class="fw-bold mb-0 text-truncate">
                  <router-link :to="{ name: 'CompanyDetail', params: { slug: company.slug } }" class="text-dark text-decoration-none">
                    {{ company.name }}
                  </router-link>
                </h6>
                <i v-if="company.is_verified" class="bi bi-patch-check-fill text-primary small" title="Verified Employer"></i>
              </div>

              <small class="text-muted mb-3 d-block">
                <i class="bi bi-geo-alt text-primary"></i> {{ company.city || 'Vietnam' }}
              </small>

              <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                  {{ company.open_jobs_count || 0 }} role{{ company.open_jobs_count !== 1 ? 's' : '' }}
                </span>
                <router-link :to="{ name: 'CompanyDetail', params: { slug: company.slug } }" class="text-primary small fw-semibold text-decoration-none">
                  Profile &rarr;
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ── Modern Employer CTA Banner ────────────── -->
    <section class="py-5 bg-canvas">
      <div class="container">
        <div class="cta-banner-wrapper position-relative overflow-hidden shadow-lg">
          <div class="cta-ambient-glow"></div>
          <div class="row align-items-center g-4 position-relative z-1">
            <div class="col-lg-8">
              <div class="cta-pill mb-3">FOR EMPLOYERS &amp; RECRUITERS</div>
              <h2 class="cta-heading mb-2">Build Your High-Performance Engineering Team</h2>
              <p class="cta-sub mb-0">
                Post technical jobs, screen candidates with structured ATS workflows, and manage applications seamlessly from one clean workspace.
              </p>
            </div>
            <div class="col-lg-4 d-flex flex-wrap gap-2 justify-content-lg-end">
              <router-link to="/register" class="btn btn-cta-primary btn-lg px-4 shadow">
                <i class="bi bi-plus-circle me-1"></i> Post a Job
              </router-link>
              <router-link to="/jobs" class="btn btn-cta-secondary btn-lg px-4">
                Browse Talent
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </section>

  </div>
</template>

<style scoped>
/* ── Canvas Background ────────────────── */
.bg-canvas {
  background-color: #f8fafc;
}

/* ── Hero Section ─────────────────────── */
.hero-section {
  background: radial-gradient(ellipse 90% 65% at 50% -15%, #e0e7ff 0%, #ede9fe 40%, #f8fafc 85%);
  border-bottom: 1px solid #e2e8f0;
  padding-top: 5rem;
  padding-bottom: 5rem;
}

/* Ambient glow orbs behind Hero */
.ambient-glow {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  pointer-events: none;
  z-index: 0;
}
.glow-1 {
  width: 450px;
  height: 450px;
  top: -100px;
  left: 10%;
  background: rgba(99, 102, 241, 0.18);
}
.glow-2 {
  width: 400px;
  height: 400px;
  top: -80px;
  right: 12%;
  background: rgba(168, 85, 247, 0.15);
}
.glow-3 {
  width: 320px;
  height: 320px;
  bottom: 0px;
  left: 45%;
  background: rgba(14, 165, 233, 0.12);
}

.live-status-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.825rem;
  font-weight: 600;
  color: #4338ca;
  background: rgba(238, 242, 255, 0.95);
  border: 1px solid #c7d2fe;
  border-radius: 9999px;
  padding: 0.35rem 0.95rem;
  backdrop-filter: blur(8px);
}

.live-indicator {
  width: 8px;
  height: 8px;
  background-color: #10b981;
  border-radius: 50%;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.5); }
  70% { transform: scale(1); box-shadow: 0 0 0 7px rgba(16, 185, 129, 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

.hero-title {
  font-size: clamp(2.2rem, 4.8vw, 3.25rem);
  font-weight: 800;
  letter-spacing: -0.035em;
  line-height: 1.15;
  color: #0f172a;
}

.hero-highlight {
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.hero-lead {
  max-width: 580px;
  margin-inline: auto;
  color: #475569;
  font-size: 1.05rem;
  line-height: 1.6;
}

/* ── Hero Search Box ──────────────────── */
.hero-search-card {
  background: #ffffff;
  border: 1px solid #c7d2fe;
  border-radius: 14px;
  padding: 0.9rem;
  box-shadow: 0 16px 36px -8px rgba(79, 70, 229, 0.12), 0 4px 12px -2px rgba(15, 23, 42, 0.04);
}

.search-input-segment,
.search-location-segment {
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  background: #f8fafc;
  transition: all 0.2s ease;
}
.search-input-segment:focus-within,
.search-location-segment:focus-within {
  border-color: #4f46e5;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
}

/* Popular Tag Chips with individual color accents */
.quick-tag-chip {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.7rem;
  font-size: 0.775rem;
  font-weight: 600;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.18s ease;
  border: 1px solid transparent;
}
.chip-frontend { background: #eef2ff; color: #4338ca; border-color: #c7d2fe; }
.chip-frontend:hover { background: #4f46e5; color: #ffffff; }

.chip-backend  { background: #f5f3ff; color: #6d28d9; border-color: #ddd6fe; }
.chip-backend:hover  { background: #7c3aed; color: #ffffff; }

.chip-devops   { background: #ecfeff; color: #0e7490; border-color: #a5f3fc; }
.chip-devops:hover   { background: #0891b2; color: #ffffff; }

.chip-ai       { background: #fdf2f8; color: #be185d; border-color: #fbcfe8; }
.chip-ai:hover       { background: #db2777; color: #ffffff; }

.chip-remote   { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
.chip-remote:hover   { background: #059669; color: #ffffff; }

.chip-golang   { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
.chip-golang:hover   { background: #2563eb; color: #ffffff; }

.chip-java     { background: #fff7ed; color: #c2410c; border-color: #ffedd5; }
.chip-java:hover     { background: #ea580c; color: #ffffff; }

/* ── Metrics Strip ────────────────────── */
.metrics-strip {
  padding: 1.25rem 2.25rem;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
}
.metric-icon-box {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
}
.box-primary { background: #eef2ff; color: #4f46e5; }
.box-success { background: #ecfdf5; color: #059669; }
.box-warning { background: #fffbeb; color: #d97706; }

.metric-value {
  font-weight: 700;
  font-size: 0.95rem;
  color: #0f172a;
  line-height: 1.2;
}
.metric-label {
  font-size: 0.775rem;
  color: #64748b;
}
.metric-divider {
  width: 1px;
  height: 32px;
  background-color: #e2e8f0;
}

/* ── Category Cards with Harmonious Color Palettes ───────────────────── */
.category-card-modern {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s ease;
}
.category-card-modern:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px -4px rgba(79, 70, 229, 0.1);
}

.cat-icon-wrapper {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  font-size: 1.25rem;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: transform 0.2s ease;
}
.category-card-modern:hover .cat-icon-wrapper {
  transform: scale(1.08);
}

/* Specific Domain Color Themes */
.cat-indigo .cat-icon-wrapper  { background: #eef2ff; color: #4f46e5; }
.cat-indigo:hover              { border-color: #c7d2fe; }

.cat-violet .cat-icon-wrapper  { background: #f5f3ff; color: #7c3aed; }
.cat-violet:hover              { border-color: #ddd6fe; }

.cat-rose .cat-icon-wrapper    { background: #fff1f2; color: #e11d48; }
.cat-rose:hover                { border-color: #fecdd3; }

.cat-cyan .cat-icon-wrapper    { background: #ecfeff; color: #0891b2; }
.cat-cyan:hover                { border-color: #a5f3fc; }

.cat-amber .cat-icon-wrapper   { background: #fffbeb; color: #d97706; }
.cat-amber:hover               { border-color: #fde68a; }

.cat-teal .cat-icon-wrapper    { background: #f0fdfa; color: #0d9488; }
.cat-teal:hover                { border-color: #99f6e4; }

.cat-slate .cat-icon-wrapper   { background: #f1f5f9; color: #334155; }
.cat-slate:hover               { border-color: #cbd5e1; }

.cat-emerald .cat-icon-wrapper { background: #ecfdf5; color: #059669; }
.cat-emerald:hover             { border-color: #a7f3d0; }

.cat-orange .cat-icon-wrapper  { background: #fff7ed; color: #ea580c; }
.cat-orange:hover              { border-color: #fed7aa; }

.cat-fuchsia .cat-icon-wrapper { background: #fdf4ff; color: #c026d3; }
.cat-fuchsia:hover             { border-color: #f5d0fe; }

.cat-title {
  font-size: 0.925rem;
  color: #0f172a;
}
.cat-action {
  font-size: 0.75rem;
}
.micro-icon {
  font-size: 0.65rem;
}

/* ── Job Cards ────────────────────────── */
.badge-curated {
  background: linear-gradient(135deg, #eef2ff 0%, #ede9fe 100%);
  color: #4f46e5;
  border: 1px solid #c7d2fe;
  font-weight: 700;
  letter-spacing: 0.05em;
}

.job-card-premium {
  border: 1px solid #e2e8f0 !important;
  border-radius: 12px !important;
  background: #ffffff;
  position: relative;
  overflow: hidden;
  transition: all 0.22s ease;
}
.job-card-premium::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, #4f46e5, #7c3aed);
  opacity: 0;
  transition: opacity 0.2s ease;
}
.job-card-premium:hover {
  border-color: #c7d2fe !important;
  transform: translateY(-3px);
  box-shadow: 0 14px 28px -6px rgba(79, 70, 229, 0.12) !important;
}
.job-card-premium:hover::before {
  opacity: 1;
}

.company-logo-avatar {
  width: 46px;
  height: 46px;
  object-fit: contain;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
}
.company-logo-fallback {
  width: 46px;
  height: 46px;
  border-radius: 10px;
  background: linear-gradient(135deg, #e0e7ff 0%, #ede9fe 100%);
  color: #4f46e5;
  font-weight: 800;
  font-size: 1.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.company-name-link {
  font-size: 0.85rem;
  font-weight: 600;
  color: #64748b;
  text-decoration: none;
}
.company-name-link:hover { color: #4f46e5; }

.job-title-heading {
  font-size: 1.05rem;
  line-height: 1.35;
}
.job-title-heading a:hover {
  color: #4f46e5 !important;
}

/* Rich Vibrant Type Pills */
.job-type-pill {
  font-size: 0.725rem;
  font-weight: 600;
  padding: 0.25rem 0.6rem;
  border-radius: 6px;
  white-space: nowrap;
}
.job-type-pill.type-full_time { background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }
.job-type-pill.type-remote    { background: #ecfeff; color: #0e7490; border: 1px solid #a5f3fc; }
.job-type-pill.type-internship{ background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
.job-type-pill.type-part_time { background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; }

.skill-tag {
  font-size: 0.75rem;
  font-weight: 500;
  color: #475569;
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 0.2rem 0.55rem;
}
.skill-tag-more {
  font-size: 0.725rem;
  color: #64748b;
  padding: 0.2rem 0.35rem;
}

.salary-label {
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-weight: 600;
  color: #64748b;
}

.salary-highlight {
  font-weight: 800;
  color: #059669;
  font-size: 0.95rem;
}

.btn-primary-soft {
  background-color: #eef2ff;
  color: #4f46e5;
  border: 1px solid #c7d2fe;
  font-weight: 600;
  font-size: 0.825rem;
  padding: 0.35rem 0.8rem;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  text-decoration: none;
  transition: all 0.18s ease;
}
.btn-primary-soft:hover {
  background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
  color: #ffffff;
  border-color: #4f46e5;
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

/* ── Employer Cards ───────────────────── */
.company-card-modern {
  border: 1px solid #e2e8f0 !important;
  border-radius: 12px !important;
  background: #ffffff;
  transition: all 0.2s ease;
}
.company-card-modern:hover {
  border-color: #c7d2fe !important;
  transform: translateY(-2px);
  box-shadow: 0 10px 24px -5px rgba(79, 70, 229, 0.1);
}
.company-logo-avatar-lg {
  width: 58px;
  height: 58px;
  object-fit: contain;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
}
.company-logo-fallback-lg {
  width: 58px;
  height: 58px;
  border-radius: 12px;
  background: linear-gradient(135deg, #e0e7ff 0%, #ede9fe 100%);
  color: #4f46e5;
  font-weight: 800;
  font-size: 1.6rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* ── CTA Banner ───────────────────────── */
.cta-banner-wrapper {
  background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 55%, #312e81 100%);
  border-radius: 16px;
  padding: 3.5rem 2.75rem;
  color: #ffffff;
  border: 1px solid rgba(199, 210, 254, 0.2);
}
.cta-ambient-glow {
  position: absolute;
  width: 380px;
  height: 380px;
  right: -50px;
  top: -50px;
  background: radial-gradient(circle, rgba(99, 102, 241, 0.35) 0%, rgba(124, 58, 237, 0) 70%);
  border-radius: 50%;
  pointer-events: none;
}
.cta-pill {
  display: inline-block;
  font-size: 0.725rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  color: #c7d2fe;
  background: rgba(79, 70, 229, 0.25);
  border: 1px solid rgba(199, 210, 254, 0.35);
  border-radius: 6px;
  padding: 0.3rem 0.75rem;
}
.cta-heading {
  font-size: clamp(1.5rem, 3.2vw, 2rem);
  font-weight: 800;
  color: #ffffff;
  letter-spacing: -0.025em;
}
.cta-sub {
  color: #cbd5e1;
  font-size: 0.975rem;
  max-width: 580px;
  line-height: 1.65;
}

.btn-cta-primary {
  background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
  color: #ffffff;
  border: 1px solid rgba(255, 255, 255, 0.2);
  font-weight: 700;
  border-radius: 8px;
  box-shadow: 0 4px 16px rgba(79, 70, 229, 0.4);
  transition: all 0.2s ease;
}
.btn-cta-primary:hover {
  background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
  color: #ffffff;
  box-shadow: 0 6px 20px rgba(79, 70, 229, 0.6);
  transform: translateY(-1px);
}

.btn-cta-secondary {
  background: rgba(255, 255, 255, 0.08);
  color: #ffffff;
  border: 1px solid rgba(255, 255, 255, 0.25);
  font-weight: 600;
  border-radius: 8px;
  backdrop-filter: blur(8px);
  transition: all 0.2s ease;
}
.btn-cta-secondary:hover {
  background: rgba(255, 255, 255, 0.18);
  color: #ffffff;
  border-color: rgba(255, 255, 255, 0.4);
}
</style>
