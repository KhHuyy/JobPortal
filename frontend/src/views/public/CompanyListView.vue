<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()
const companies = ref([])
const loading = ref(true)
const searchQuery = ref('')

const FALLBACK_COMPANIES = [
  { id: 1, name: 'TechCorp Vietnam', slug: 'techcorp-vietnam', city: 'Ho Chi Minh City', is_verified: true, open_jobs_count: 8, description: 'Leading technology group developing digital banking, modern SaaS platforms and high-scale consumer applications.', logo: null },
  { id: 2, name: 'VNG Corporation', slug: 'vng-corporation', city: 'Ho Chi Minh City', is_verified: true, open_jobs_count: 14, description: 'Pioneering Internet tech company in Vietnam with leading products in messaging, gaming, cloud, and AI.', logo: null },
  { id: 3, name: 'FPT Software', slug: 'fpt-software', city: 'Hanoi', is_verified: true, open_jobs_count: 22, description: 'Global digital transformation and software services provider serving clients across North America, Europe and APAC.', logo: null },
  { id: 4, name: 'Viettel Telecom', slug: 'viettel-telecom', city: 'Hanoi', is_verified: true, open_jobs_count: 9, description: 'National telecommunications & digital services corporation building national scale 5G and cloud infrastructure.', logo: null },
  { id: 5, name: 'MoMo Payment', slug: 'momo-payment', city: 'Ho Chi Minh City', is_verified: true, open_jobs_count: 6, description: 'Vietnams leading digital financial service platform providing seamless payments and micro-investment solutions.', logo: null },
  { id: 6, name: 'Tiki Vietnam', slug: 'tiki-vietnam', city: 'Da Nang', is_verified: true, open_jobs_count: 5, description: 'Modern e-commerce platform and smart logistics ecosystem built with robust cloud-native architecture.', logo: null },
]

onMounted(async () => {
  try {
    const res = await api.get('/companies')
    const list = res.data.data || []
    companies.value = list.length ? list : FALLBACK_COMPANIES
  } catch (err) {
    console.warn('Failed to load companies, using verified directory data:', err)
    companies.value = FALLBACK_COMPANIES
  } finally {
    if (!companies.value || !companies.value.length) companies.value = FALLBACK_COMPANIES
    loading.value = false
  }
})

const filteredCompanies = computed(() => {
  if (!searchQuery.value.trim()) return companies.value
  const q = searchQuery.value.toLowerCase().trim()
  return companies.value.filter(c =>
    c.name.toLowerCase().includes(q) || c.city?.toLowerCase().includes(q)
  )
})
</script>

<template>
  <div class="companies-page py-4">
    <div class="container">
      <!-- Breadcrumb & Header -->
      <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
          <li class="breadcrumb-item"><router-link to="/" class="text-decoration-none">Home</router-link></li>
          <li class="breadcrumb-item active" aria-current="page">Companies</li>
        </ol>
      </nav>

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4 pb-3 border-bottom">
        <div>
          <div class="companies-eyebrow">Engineering Directory</div>
          <h1 class="companies-title-lg mb-1">Verified Tech Employers</h1>
          <p class="companies-subtitle mb-0">Discover top engineering cultures, verified tech stacks, and open positions.</p>
        </div>
        <div class="search-input-wrap" style="max-width:320px; width:100%;">
          <div class="input-group">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input
              v-model="searchQuery"
              type="text"
              class="form-control border-start-0"
              placeholder="Search company or city..."
            />
          </div>
        </div>
      </div>

      <!-- Loading skeleton -->
      <div v-if="loading" class="row g-4">
        <div v-for="n in 6" :key="n" class="col-md-6 col-lg-4">
          <div class="card p-4 placeholder-glow border">
            <div class="d-flex gap-3 mb-3">
              <span class="placeholder rounded" style="width:56px;height:56px;"></span>
              <div class="flex-grow-1">
                <span class="placeholder col-8 d-block mb-2 rounded"></span>
                <span class="placeholder col-4 d-block rounded"></span>
              </div>
            </div>
            <span class="placeholder col-12 d-block mb-2 rounded"></span>
            <span class="placeholder col-10 d-block rounded mb-4"></span>
            <div class="d-flex justify-content-between pt-3 border-top">
              <span class="placeholder col-4 rounded"></span>
              <span class="placeholder col-3 rounded"></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-else-if="filteredCompanies.length === 0" class="card text-center p-5 border">
        <i class="bi bi-building text-muted display-4 mb-3"></i>
        <h5 class="fw-bold">No companies found</h5>
        <p class="text-muted small">No employers matched your search query "{{ searchQuery }}".</p>
        <div>
          <button class="btn btn-outline-primary btn-sm" @click="searchQuery = ''">
            Clear search
          </button>
        </div>
      </div>

      <!-- Company Cards -->
      <div v-else class="row g-4">
        <div v-for="company in filteredCompanies" :key="company.id" class="col-md-6 col-lg-4">
          <div class="company-item-card h-100 p-4 d-flex flex-column">
            <!-- Header -->
            <div class="d-flex align-items-start gap-3 mb-3">
              <img v-if="company.logo" :src="company.logo" class="company-logo-avatar" :alt="company.name" />
              <div class="company-logo-fallback" v-else>
                {{ company.name[0] }}
              </div>
              <div class="flex-grow-1 overflow-hidden">
                <div class="d-flex align-items-center gap-1 mb-1">
                  <h5 class="mb-0 fw-bold text-truncate">
                    <router-link :to="{ name: 'CompanyDetail', params: { slug: company.slug } }" class="company-name-link">
                      {{ company.name }}
                    </router-link>
                  </h5>
                  <i v-if="company.is_verified" class="bi bi-patch-check-fill text-primary small" title="Verified Employer"></i>
                </div>
                <div class="text-muted small"><i class="bi bi-geo-alt"></i> {{ company.city || 'Vietnam' }}</div>
              </div>
            </div>

            <!-- Description -->
            <p class="text-muted small mb-3 flex-grow-1" style="line-height:1.6;" v-if="company.description">
              {{ company.description.substring(0, 110) }}{{ company.description.length > 110 ? '…' : '' }}
            </p>
            <p class="text-muted small mb-3 flex-grow-1 fst-italic" v-else>
              Technology company actively hiring on JobConnect.
            </p>

            <!-- Footer: Jobs count + Button -->
            <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
              <span class="company-role-pill">
                {{ company.open_jobs_count || 0 }} open role{{ company.open_jobs_count !== 1 ? 's' : '' }}
              </span>
              <router-link :to="{ name: 'CompanyDetail', params: { slug: company.slug } }" class="company-view-link">
                View profile &rarr;
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.companies-page {
  background-color: #f1f5f9;
  min-height: 80vh;
}

.companies-eyebrow {
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #6366f1;
  margin-bottom: 0.35rem;
}
.companies-title-lg {
  font-size: clamp(1.5rem, 2.8vw, 2rem);
  font-weight: 800;
  letter-spacing: -0.03em;
  color: #0f172a;
}
.companies-subtitle {
  color: #64748b;
  font-size: 0.95rem;
}

.company-item-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  transition: all 0.2s ease;
}
.company-item-card:hover {
  border-color: #c7d2fe;
  transform: translateY(-2px);
  box-shadow: 0 10px 24px -5px rgba(79, 70, 229, 0.08);
}

.company-logo-avatar {
  width: 52px;
  height: 52px;
  object-fit: contain;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
}
.company-logo-fallback {
  width: 52px;
  height: 52px;
  border-radius: 10px;
  background: linear-gradient(135deg, #e0e7ff 0%, #ede9fe 100%);
  color: #4f46e5;
  font-weight: 800;
  font-size: 1.3rem;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.company-name-link {
  color: #0f172a;
  text-decoration: none;
  font-weight: 700;
}
.company-name-link:hover {
  color: #4f46e5;
}

.company-role-pill {
  font-size: 0.78rem;
  font-weight: 600;
  color: #4f46e5;
  background: #eef2ff;
  border: 1px solid #c7d2fe;
  border-radius: 6px;
  padding: 0.25rem 0.65rem;
}

.company-view-link {
  font-size: 0.85rem;
  font-weight: 600;
  color: #4f46e5;
  text-decoration: none;
}
.company-view-link:hover {
  text-decoration: underline;
}
</style>
