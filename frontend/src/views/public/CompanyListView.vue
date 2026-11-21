<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()
const companies = ref([])
const loading = ref(true)
const searchQuery = ref('')

onMounted(async () => {
  try {
    const res = await api.get('/companies')
    companies.value = res.data.data || []
  } catch (err) {
    console.error('Failed to load companies:', err)
  } finally {
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

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-2 border-bottom">
        <div>
          <h1 class="h3 fw-bold mb-1 text-dark">Verified Tech Employers</h1>
          <p class="text-muted small mb-0">Discover top engineering cultures, tech stacks, and team open positions</p>
        </div>
        <div class="search-input-wrap" style="max-width:320px; width:100%;">
          <div class="input-group">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input
              v-model="searchQuery"
              type="text"
              class="form-control border-start-0"
              placeholder="Search by company or city..."
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
          <div class="card company-item-card h-100 p-4 d-flex flex-column">
            <!-- Header -->
            <div class="d-flex align-items-start gap-3 mb-3">
              <img v-if="company.logo" :src="company.logo" class="company-logo-avatar" :alt="company.name" />
              <div class="company-logo-fallback" v-else>
                {{ company.name[0] }}
              </div>
              <div class="flex-grow-1 overflow-hidden">
                <div class="d-flex align-items-center gap-1 mb-1">
                  <h5 class="mb-0 fw-bold text-truncate">
                    <router-link :to="{ name: 'CompanyDetail', params: { slug: company.slug } }" class="text-dark text-decoration-none">
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
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                <i class="bi bi-briefcase me-1"></i> {{ company.open_jobs_count || 0 }} open role{{ company.open_jobs_count !== 1 ? 's' : '' }}
              </span>
              <router-link :to="{ name: 'CompanyDetail', params: { slug: company.slug } }" class="btn btn-sm btn-outline-primary">
                View Profile &rarr;
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
  background-color: #f9fafb;
  min-height: 80vh;
}

.company-item-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  transition: all 0.2s ease;
}
.company-item-card:hover {
  border-color: #93c5fd;
  transform: translateY(-2px);
  box-shadow: 0 6px 18px -4px rgba(37, 99, 235, 0.08);
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
  background-color: #eff6ff;
  color: #2563eb;
  font-weight: 700;
  font-size: 1.3rem;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
</style>
