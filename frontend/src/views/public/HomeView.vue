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
  if (job.salary_min && job.salary_max) return `$${Number(job.salary_min).toLocaleString()} â€“ $${Number(job.salary_max).toLocaleString()}`
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

    <!-- â”€â”€ Hero Section â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
    <section class="hero-section position-relative overflow-hidden">
      <!-- Single soft ambient glow â€” not overdone -->
      <div class="hero-glow-orb"></div>

      <div class="container position-relative" style="z-index:2;">
        <div class="row align-items-center min-vh-hero g-5">

          <!-- Left: Editorial text block -->
          <div class="col-lg-6">
            <div class="hero-eyebrow">
              <span class="live-dot-sm"></span>
              <span>35+ roles verified today</span>
            </div>

            <h1 class="hero-title mt-3">
              The place where<br>
              great engineers<br>
              <em class="hero-em">find their team.</em>
            </h1>

            <p class="hero-lead mt-4">
              Direct access to verified engineering teams â€” no middlemen, no spam.
              Transparent salaries, real application tracking, genuine hiring conversations.
            </p>

            <!-- Search â€” inline, left-aligned -->
            <form @submit.prevent="searchJobs" class="hero-search-row mt-5">
              <div class="hero-search-input-wrap">
                <i class="bi bi-search"></i>
                <input
                  v-model="keyword"
                  type="text"
                  placeholder="Role, skill, or companyâ€¦"
                  class="hero-search-input"
                />
              </div>
              <button type="submit" class="hero-search-btn">
                Search <i class="bi bi-arrow-right ms-1"></i>
              </button>
            </form>

            <!-- Popular tags â€” casual, not "Popular:" label -->
            <div class="hero-tags mt-3">
              <button @click="quickSearch('Frontend')" class="hero-tag">Frontend</button>
              <button @click="quickSearch('Backend')" class="hero-tag">Backend</button>
              <button @click="quickSearch('DevOps')" class="hero-tag">DevOps</button>
              <button @click="quickSearch('AI & ML')" class="hero-tag">ML / AI</button>
              <button @click="quickSearch('Remote')" class="hero-tag hero-tag--accent">â†— Remote</button>
            </div>
          </div>

          <!-- Right: Stats â€” editorial large numbers -->
          <div class="col-lg-5 offset-lg-1 d-none d-lg-flex flex-column gap-4">
            <div class="stat-card">
              <div class="stat-number">38<span class="stat-unit">+</span></div>
              <div class="stat-desc">Active job openings across<br>software & tech domains</div>
              <div class="stat-bar" style="width:85%"></div>
            </div>
            <div class="stat-card stat-card--offset">
              <div class="stat-number">10</div>
              <div class="stat-desc">Verified employer workspaces<br>with direct recruiter contact</div>
              <div class="stat-bar stat-bar--green" style="width:60%"></div>
            </div>
            <div class="stat-card">
              <div class="stat-number">100<span class="stat-unit">%</span></div>
              <div class="stat-desc">Application status visible â€”<br>no ghosting, ever</div>
              <div class="stat-bar stat-bar--violet" style="width:100%"></div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- â”€â”€ Categories Section â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
    <section class="section-categories">
      <div class="container">
        <div class="section-header">
          <div>
            <p class="section-eyebrow">Tech Domains</p>
            <h2 class="section-title-lg">Browse by specialty</h2>
          </div>
          <router-link to="/jobs" class="section-link-all">
            All categories <i class="bi bi-arrow-right"></i>
          </router-link>
        </div>

        <div class="row g-3">
          <div v-for="cat in masterStore.categories" :key="cat.id" class="col-6 col-md-4 col-lg-3">
            <div
              class="cat-tile"
              :class="getCategoryColorClass(cat.slug)"
              @click="router.push({ name: 'Jobs', query: { category_id: cat.id } })"
            >
              <div class="cat-tile-icon">
                <i :class="'bi ' + (cat.icon || 'bi-folder2')"></i>
              </div>
              <div class="cat-tile-name">{{ cat.name }}</div>
              <div class="cat-tile-arrow"><i class="bi bi-arrow-right"></i></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- â”€â”€ Latest Jobs Section â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
    <section class="section-jobs">
      <div class="container">
        <div class="section-header">
          <div>
            <p class="section-eyebrow">Open Positions</p>
            <h2 class="section-title-lg">Featured roles</h2>
          </div>
          <router-link to="/jobs" class="section-link-all">
            Explore all <i class="bi bi-arrow-right"></i>
          </router-link>
        </div>

        <!-- Skeleton -->
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
            <div class="job-card h-100">
              <!-- Left accent line color per category -->
              <div class="job-card-accent" :class="'accent-' + (job.job_type || 'full_time')"></div>

              <div class="job-card-body">
                <!-- Top: logo + meta -->
                <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                  <div class="d-flex align-items-center gap-3 overflow-hidden">
                    <img v-if="job.company?.logo" :src="job.company.logo" class="job-company-logo" :alt="job.company.name" />
                    <div class="job-company-fallback" v-else>
                      {{ (job.company?.name || 'C')[0].toUpperCase() }}
                    </div>
                    <div class="overflow-hidden">
                      <router-link :to="{ name: 'CompanyDetail', params: { slug: job.company?.slug } }" class="job-company-name d-block text-truncate">
                        {{ job.company?.name }}
                      </router-link>
                      <span class="job-location"><i class="bi bi-geo-alt"></i> {{ job.location }}</span>
                    </div>
                  </div>
                  <span class="job-type-tag" :class="'type-' + (job.job_type || 'full_time')">
                    {{ formatJobType(job.job_type) }}
                  </span>
                </div>

                <!-- Title -->
                <h6 class="job-title mb-2">
                  <router-link :to="{ name: 'JobDetail', params: { slug: job.slug } }">
                    {{ job.title }}
                  </router-link>
                </h6>

                <!-- Skills -->
                <div class="d-flex flex-wrap gap-1 mb-3">
                  <span v-for="skill in (job.skills || []).slice(0, 3)" :key="skill.id" class="job-skill-tag">{{ skill.name }}</span>
                  <span v-if="(job.skills || []).length > 3" class="job-skill-more">+{{ job.skills.length - 3 }}</span>
                  <span v-if="!job.skills?.length && job.experience_level" class="job-skill-tag">{{ job.experience_level }}</span>
                </div>

                <!-- Footer -->
                <div class="job-card-footer">
                  <div>
                    <div class="job-salary-label">Salary</div>
                    <div class="job-salary">{{ formatSalary(job) }}</div>
                  </div>
                  <router-link :to="{ name: 'JobDetail', params: { slug: job.slug } }" class="job-view-btn">
                    View <i class="bi bi-arrow-right"></i>
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- â”€â”€ Top Employers Section â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
    <section class="section-employers">
      <div class="container">
        <div class="section-header">
          <div>
            <p class="section-eyebrow">Who's hiring</p>
            <h2 class="section-title-lg">Featured employers</h2>
          </div>
          <router-link to="/companies" class="section-link-all">
            All companies <i class="bi bi-arrow-right"></i>
          </router-link>
        </div>

        <div class="row g-3">
          <div v-for="company in topCompanies" :key="company.id" class="col-md-4 col-lg-3">
            <div class="employer-card h-100">
              <div class="employer-card-logo">
                <img v-if="company.logo" :src="company.logo" class="employer-logo-img" :alt="company.name" />
                <div v-else class="employer-logo-fallback">{{ company.name[0] }}</div>
              </div>

              <div class="employer-card-info">
                <div class="d-flex align-items-center gap-1 mb-1">
                  <span class="employer-name">{{ company.name }}</span>
                  <i v-if="company.is_verified" class="bi bi-patch-check-fill employer-verified"></i>
                </div>
                <span class="employer-city"><i class="bi bi-geo-alt"></i> {{ company.city || 'Vietnam' }}</span>
              </div>

              <div class="employer-card-footer">
                <span class="employer-roles">{{ company.open_jobs_count || 0 }} open role{{ company.open_jobs_count !== 1 ? 's' : '' }}</span>
                <router-link :to="{ name: 'CompanyDetail', params: { slug: company.slug } }" class="employer-profile-link">
                  View profile â†’
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- â”€â”€ CTA Section â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
    <section class="section-cta">
      <div class="container">
        <div class="cta-block">
          <!-- Left glow -->
          <div class="cta-glow"></div>

          <div class="row align-items-center g-4 position-relative" style="z-index:2">
            <div class="col-lg-7">
              <p class="cta-eyebrow">For employers</p>
              <h2 class="cta-title">Hire engineers who actually fit.</h2>
              <p class="cta-body">
                Post roles, review applications, and move candidates through your pipeline â€” all in one workspace built for technical hiring.
              </p>
            </div>
            <div class="col-lg-5 d-flex flex-wrap gap-3 justify-content-lg-end">
              <router-link to="/register" class="cta-btn-primary">
                Post a job
              </router-link>
              <router-link to="/jobs" class="cta-btn-ghost">
                Browse talent
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </section>

  </div>
</template>

<style scoped>
/* ══════════════════════════════════════
   HERO
══════════════════════════════════════ */
.hero-section {
  background: #ffffff;
  border-bottom: 1px solid #e2e8f0;
  padding-top: 5rem;
  padding-bottom: 5rem;
}

/* Single soft orb — top-left only */
.hero-glow-orb {
  position: absolute;
  width: 560px;
  height: 560px;
  top: -160px;
  left: -120px;
  background: radial-gradient(circle, rgba(99, 102, 241, 0.09) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
  z-index: 0;
}

.min-vh-hero {
  min-height: calc(100vh - 70px);
}

.hero-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  font-size: 0.8rem;
  font-weight: 600;
  color: #64748b;
  letter-spacing: 0.01em;
}

.live-dot-sm {
  width: 7px;
  height: 7px;
  background: #10b981;
  border-radius: 50%;
  box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
  animation: livePulse 2s infinite;
  display: inline-block;
  flex-shrink: 0;
}
@keyframes livePulse {
  0%   { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.5); }
  70%  { box-shadow: 0 0 0 5px rgba(16, 185, 129, 0); }
  100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

.hero-title {
  font-size: clamp(2.6rem, 5vw, 3.8rem);
  font-weight: 800;
  letter-spacing: -0.04em;
  line-height: 1.1;
  color: #0f172a;
}

.hero-em {
  font-style: italic;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero-lead {
  max-width: 480px;
  color: #475569;
  font-size: 1.05rem;
  line-height: 1.65;
}

.hero-search-row {
  display: flex;
  align-items: stretch;
  gap: 0.5rem;
  max-width: 500px;
}
.hero-search-input-wrap {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0 0.9rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background: #f8fafc;
  color: #64748b;
  transition: all 0.2s ease;
}
.hero-search-input-wrap:focus-within {
  border-color: #4f46e5;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
}
.hero-search-input {
  flex: 1;
  border: none;
  background: transparent;
  outline: none;
  font-size: 0.95rem;
  color: #0f172a;
  padding: 0.65rem 0;
}
.hero-search-input::placeholder { color: #94a3b8; }
.hero-search-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.65rem 1.4rem;
  background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
  color: #ffffff;
  font-size: 0.9rem;
  font-weight: 700;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}
.hero-search-btn:hover {
  background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
  box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
}

.hero-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
}
.hero-tag {
  display: inline-flex;
  align-items: center;
  padding: 0.3rem 0.75rem;
  font-size: 0.8rem;
  font-weight: 500;
  color: #475569;
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.18s ease;
}
.hero-tag:hover {
  color: #4f46e5;
  border-color: #c7d2fe;
  background: #eef2ff;
}
.hero-tag--accent {
  color: #4f46e5;
  background: #eef2ff;
  border-color: #c7d2fe;
}

/* ══════════════════════════════════════
   STAT CARDS
══════════════════════════════════════ */
.stat-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 1.5rem;
  transition: box-shadow 0.2s ease;
}
.stat-card:hover {
  box-shadow: 0 8px 24px -4px rgba(79, 70, 229, 0.1);
}
.stat-card--offset { margin-left: 2rem; }

.stat-number {
  font-size: 3rem;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.04em;
  line-height: 1;
}
.stat-unit {
  font-size: 1.5rem;
  font-weight: 800;
  color: #0f172a;
}
.stat-desc {
  font-size: 0.85rem;
  color: #64748b;
  line-height: 1.55;
  margin-top: 0.4rem;
}
.stat-bar {
  height: 3px;
  background: #4f46e5;
  border-radius: 2px;
  margin-top: 1rem;
}
.stat-bar--green  { background: #059669; }
.stat-bar--violet { background: #7c3aed; }

/* ══════════════════════════════════════
   SECTIONS
══════════════════════════════════════ */
.section-categories { padding: 5rem 0; background: #ffffff; }
.section-jobs       { padding: 5rem 0; background: #f8fafc; }
.section-employers  { padding: 5rem 0; background: #ffffff; }
.section-cta        { padding: 5rem 0; background: #f8fafc; }

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 2.5rem;
}
.section-eyebrow {
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #6366f1;
  margin-bottom: 0.4rem;
}
.section-title-lg {
  font-size: clamp(1.6rem, 3vw, 2.2rem);
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.03em;
  margin: 0;
}
.section-link-all {
  font-size: 0.875rem;
  font-weight: 600;
  color: #4f46e5;
  text-decoration: none;
  white-space: nowrap;
}
.section-link-all:hover { text-decoration: underline; }

/* ══════════════════════════════════════
   CATEGORY TILES
══════════════════════════════════════ */
.cat-tile {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.9rem 1.1rem;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  cursor: pointer;
  background: #ffffff;
  transition: all 0.2s ease;
}
.cat-tile:hover {
  border-color: #c7d2fe;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px -4px rgba(79, 70, 229, 0.1);
}
.cat-tile-icon {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.15rem;
  flex-shrink: 0;
}
.cat-tile-name {
  font-size: 0.9rem;
  font-weight: 600;
  color: #0f172a;
  flex: 1;
}
.cat-tile-arrow {
  margin-left: auto;
  color: #94a3b8;
  opacity: 0;
  transition: opacity 0.2s ease;
}
.cat-tile:hover .cat-tile-arrow { opacity: 1; }

.cat-indigo .cat-tile-icon  { background: #eef2ff; color: #4f46e5; }
.cat-indigo:hover           { border-color: #c7d2fe; }
.cat-violet .cat-tile-icon  { background: #f5f3ff; color: #7c3aed; }
.cat-violet:hover           { border-color: #ddd6fe; }
.cat-rose .cat-tile-icon    { background: #fff1f2; color: #e11d48; }
.cat-rose:hover             { border-color: #fecdd3; }
.cat-cyan .cat-tile-icon    { background: #ecfeff; color: #0891b2; }
.cat-cyan:hover             { border-color: #a5f3fc; }
.cat-amber .cat-tile-icon   { background: #fffbeb; color: #d97706; }
.cat-amber:hover            { border-color: #fde68a; }
.cat-teal .cat-tile-icon    { background: #f0fdfa; color: #0d9488; }
.cat-teal:hover             { border-color: #99f6e4; }
.cat-slate .cat-tile-icon   { background: #f1f5f9; color: #334155; }
.cat-slate:hover            { border-color: #cbd5e1; }
.cat-emerald .cat-tile-icon { background: #ecfdf5; color: #059669; }
.cat-emerald:hover          { border-color: #a7f3d0; }
.cat-orange .cat-tile-icon  { background: #fff7ed; color: #ea580c; }
.cat-orange:hover           { border-color: #fed7aa; }
.cat-fuchsia .cat-tile-icon { background: #fdf4ff; color: #c026d3; }
.cat-fuchsia:hover          { border-color: #f5d0fe; }

/* ══════════════════════════════════════
   JOB CARDS
══════════════════════════════════════ */
.job-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  display: flex;
  position: relative;
  overflow: hidden;
  transition: all 0.22s ease;
}
.job-card:hover {
  border-color: #c7d2fe;
  transform: translateY(-2px);
  box-shadow: 0 10px 24px -6px rgba(79, 70, 229, 0.12);
}

.job-card-accent {
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

.job-card-body {
  padding: 1.4rem 1.4rem 1.4rem 1.65rem;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.job-company-logo {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  object-fit: contain;
  background: #fff;
  flex-shrink: 0;
}
.job-company-fallback {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  background: linear-gradient(135deg, #e0e7ff 0%, #ede9fe 100%);
  color: #4f46e5;
  font-weight: 800;
  font-size: 1.1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.job-company-name {
  font-size: 0.85rem;
  font-weight: 600;
  color: #64748b;
  text-decoration: none;
}
.job-company-name:hover { color: #4f46e5; }
.job-location {
  font-size: 0.75rem;
  color: #94a3b8;
}

.job-type-tag {
  font-size: 0.72rem;
  font-weight: 600;
  padding: 0.22rem 0.6rem;
  border-radius: 6px;
  white-space: nowrap;
  flex-shrink: 0;
}
.job-type-tag.type-full_time  { background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }
.job-type-tag.type-remote     { background: #ecfeff; color: #0e7490; border: 1px solid #a5f3fc; }
.job-type-tag.type-internship { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
.job-type-tag.type-part_time  { background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; }

.job-title {
  font-size: 1rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}
.job-title a { color: inherit; text-decoration: none; }
.job-title a:hover { color: #4f46e5; }

.job-skill-tag {
  font-size: 0.75rem;
  font-weight: 500;
  color: #475569;
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  border-radius: 5px;
  padding: 0.18rem 0.5rem;
}
.job-skill-more {
  font-size: 0.72rem;
  color: #94a3b8;
  padding: 0.18rem 0.3rem;
}

.job-card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 1rem;
  border-top: 1px solid #f1f5f9;
  margin-top: auto;
}
.job-salary-label {
  font-size: 0.68rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #94a3b8;
  font-weight: 600;
}
.job-salary {
  font-size: 0.95rem;
  font-weight: 800;
  color: #059669;
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
  padding: 0.3rem 0.75rem;
  text-decoration: none;
  transition: all 0.18s ease;
}
.job-view-btn:hover {
  background: #4f46e5;
  color: #ffffff;
  border-color: #4f46e5;
}

/* ══════════════════════════════════════
   EMPLOYER CARDS
══════════════════════════════════════ */
.employer-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  transition: all 0.2s ease;
}
.employer-card:hover {
  border-color: #c7d2fe;
  transform: translateY(-2px);
  box-shadow: 0 10px 24px -5px rgba(79, 70, 229, 0.1);
}
.employer-card-logo { margin-bottom: 0.9rem; }
.employer-logo-img {
  width: 56px;
  height: 56px;
  border-radius: 10px;
  object-fit: contain;
  border: 1px solid #e2e8f0;
  background: #fff;
}
.employer-logo-fallback {
  width: 56px;
  height: 56px;
  border-radius: 10px;
  background: linear-gradient(135deg, #e0e7ff 0%, #ede9fe 100%);
  color: #4f46e5;
  font-weight: 800;
  font-size: 1.4rem;
  display: flex;
  align-items: center;
  justify-content: center;
}
.employer-card-info { flex: 1; }
.employer-name {
  font-weight: 700;
  color: #0f172a;
  font-size: 0.95rem;
}
.employer-verified {
  color: #4f46e5;
  font-size: 0.85rem;
}
.employer-city {
  font-size: 0.8rem;
  color: #64748b;
  margin-top: 0.2rem;
  display: block;
}
.employer-card-footer {
  margin-top: auto;
  padding-top: 1rem;
  border-top: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.employer-roles {
  font-size: 0.78rem;
  font-weight: 600;
  color: #4f46e5;
  background: #eef2ff;
  border: 1px solid #c7d2fe;
  border-radius: 5px;
  padding: 0.2rem 0.6rem;
}
.employer-profile-link {
  font-size: 0.85rem;
  font-weight: 600;
  color: #4f46e5;
  text-decoration: none;
}
.employer-profile-link:hover { text-decoration: underline; }

/* ══════════════════════════════════════
   CTA BLOCK
══════════════════════════════════════ */
.cta-block {
  background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 60%, #312e81 100%);
  border-radius: 16px;
  padding: 4rem 3rem;
  position: relative;
  overflow: hidden;
  border: 1px solid rgba(199, 210, 254, 0.15);
}
.cta-glow {
  position: absolute;
  width: 400px;
  height: 400px;
  right: -80px;
  top: -80px;
  background: radial-gradient(circle, rgba(99, 102, 241, 0.3) 0%, transparent 65%);
  border-radius: 50%;
  pointer-events: none;
}
.cta-eyebrow {
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #a5b4fc;
  margin-bottom: 0.6rem;
}
.cta-title {
  font-size: clamp(1.6rem, 3vw, 2.2rem);
  font-weight: 800;
  color: #ffffff;
  letter-spacing: -0.03em;
  margin-bottom: 0.9rem;
}
.cta-body {
  color: #cbd5e1;
  font-size: 0.975rem;
  line-height: 1.65;
  max-width: 520px;
}
.cta-btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.75rem 1.75rem;
  background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
  color: #ffffff;
  font-weight: 700;
  font-size: 0.95rem;
  border-radius: 8px;
  text-decoration: none;
  box-shadow: 0 4px 16px rgba(79, 70, 229, 0.4);
  transition: all 0.2s ease;
}
.cta-btn-primary:hover {
  background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
  color: #ffffff;
  box-shadow: 0 6px 20px rgba(79, 70, 229, 0.6);
  transform: translateY(-1px);
}
.cta-btn-ghost {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.75rem 1.75rem;
  background: rgba(255, 255, 255, 0.07);
  color: #ffffff;
  font-weight: 600;
  font-size: 0.95rem;
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.22);
  text-decoration: none;
  backdrop-filter: blur(8px);
  transition: all 0.2s ease;
}
.cta-btn-ghost:hover {
  background: rgba(255, 255, 255, 0.15);
  color: #ffffff;
  border-color: rgba(255, 255, 255, 0.4);
}
</style>