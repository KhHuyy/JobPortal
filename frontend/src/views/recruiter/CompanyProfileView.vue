<template>
  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-building text-primary me-2"></i>Company Profile</h4>
        <p class="text-muted small mt-1">Manage your company's public presence on JobConnect.</p>
      </div>
      <span v-if="company?.is_verified" class="badge bg-success fs-6">
        <i class="bi bi-patch-check-fill me-1"></i>Verified
      </span>
      <span v-else class="badge bg-warning text-dark fs-6">
        <i class="bi bi-clock me-1"></i>Pending Verification
      </span>
    </div>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary"></div>
    </div>

    <div v-else class="row g-4">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
          <div class="card-body p-4">
            <div v-if="successMsg" class="alert alert-success alert-dismissible fade show">
              <i class="bi bi-check-circle me-2"></i>{{ successMsg }}
              <button type="button" class="btn-close" @click="successMsg = ''"></button>
            </div>
            <div v-if="errorMsg" class="alert alert-danger alert-dismissible fade show">
              <i class="bi bi-exclamation-triangle me-2"></i>{{ errorMsg }}
              <button type="button" class="btn-close" @click="errorMsg = ''"></button>
            </div>

            <form @submit.prevent="saveCompany">
              <h6 class="text-uppercase text-muted fw-semibold mb-3 small">Basic Information</h6>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Company Name <span class="text-danger">*</span></label>
                  <input v-model="form.name" type="text" class="form-control" required placeholder="e.g. TechCorp Inc." />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Tax / Registration Code</label>
                  <input v-model="form.tax_code" type="text" class="form-control" placeholder="e.g. 1234567890" />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Website</label>
                  <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-globe"></i></span>
                    <input v-model="form.website" type="url" class="form-control" placeholder="https://yourcompany.com" />
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Company Size</label>
                  <select v-model="form.company_size" class="form-select">
                    <option value="">Select size</option>
                    <option value="1-10">1–10 employees</option>
                    <option value="11-50">11–50 employees</option>
                    <option value="51-200">51–200 employees</option>
                    <option value="201-500">201–500 employees</option>
                    <option value="501-1000">501–1,000 employees</option>
                    <option value="1000+">1,000+ employees</option>
                  </select>
                </div>
                <div class="col-md-8">
                  <label class="form-label">Address</label>
                  <input v-model="form.address" type="text" class="form-control" placeholder="Street address" />
                </div>
                <div class="col-md-4">
                  <label class="form-label">City</label>
                  <input v-model="form.city" type="text" class="form-control" placeholder="e.g. Ho Chi Minh City" />
                </div>
                <div class="col-12">
                  <label class="form-label">Company Description</label>
                  <textarea v-model="form.description" class="form-control" rows="5" placeholder="Describe your company culture, mission, products..."></textarea>
                </div>
              </div>

              <hr class="my-4" />
              <h6 class="text-uppercase text-muted fw-semibold mb-3 small">Company Logo</h6>
              <div class="d-flex align-items-center gap-3 mb-3">
                <img
                  v-if="logoPreview"
                  :src="logoPreview"
                  width="80" height="80"
                  class="rounded-3"
                  style="object-fit:cover"
                />
                <div v-else class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="width:80px;height:80px">
                  <i class="bi bi-building text-muted fs-2"></i>
                </div>
                <div>
                  <label class="form-label mb-1">Upload Logo <span class="text-muted small">(jpg/png/webp, max 2MB)</span></label>
                  <input @change="onLogoChange" type="file" class="form-control" accept="image/jpeg,image/png,image/webp" />
                </div>
              </div>

              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4" :disabled="saving">
                  <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
                  <i v-else class="bi bi-save me-2"></i>
                  {{ saving ? 'Saving...' : 'Save Company Profile' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
          <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Verification Status</h6>
            <div v-if="company?.is_verified">
              <p class="text-success mb-2"><i class="bi bi-patch-check-fill me-2"></i>Your company is <strong>verified</strong>.</p>
              <p class="text-muted small">Verified companies attract more qualified candidates and get a trust badge on the job board.</p>
            </div>
            <div v-else>
              <p class="text-warning mb-2"><i class="bi bi-clock-fill me-2"></i>Verification <strong>pending</strong>.</p>
              <p class="text-muted small">An admin will review your company registration. This usually takes 1–2 business days.</p>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
          <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-lightning-charge text-warning me-2"></i>Quick Actions</h6>
            <div class="d-grid gap-2">
              <router-link to="/recruiter/jobs" class="btn btn-outline-primary btn-sm text-start">
                <i class="bi bi-briefcase me-2"></i>Manage Job Posts
              </router-link>
              <router-link to="/recruiter/dashboard" class="btn btn-outline-secondary btn-sm text-start">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
              </router-link>
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
const saving = ref(false)
const company = ref(null)
const successMsg = ref('')
const errorMsg = ref('')
const logoPreview = ref(null)
const logoFile = ref(null)

const form = ref({
  name: '',
  tax_code: '',
  website: '',
  company_size: '',
  address: '',
  city: '',
  description: '',
})

onMounted(async () => {
  try {
    const res = await api.get('/recruiter/company')
    company.value = res.data.data
    if (company.value) {
      const c = company.value
      form.value.name = c.name || ''
      form.value.tax_code = c.tax_code || ''
      form.value.website = c.website || ''
      form.value.company_size = c.company_size || ''
      form.value.address = c.address || ''
      form.value.city = c.city || ''
      form.value.description = c.description || ''
      // Logo from show() is now always a full URL from asset()
      if (c.logo) {
        logoPreview.value = c.logo
      }
    }
  } catch {
    // No company yet — fresh form
  } finally {
    loading.value = false
  }
})

function onLogoChange(e) {
  const file = e.target.files[0]
  if (!file) return
  if (file.size > 2 * 1024 * 1024) { errorMsg.value = 'Logo must be under 2MB.'; return }
  logoFile.value = file
  logoPreview.value = URL.createObjectURL(file)
}

async function saveCompany() {
  saving.value = true
  successMsg.value = ''
  errorMsg.value = ''
  try {
    const fd = new FormData()
    Object.entries(form.value).forEach(([k, v]) => { if (v !== '' && v !== null) fd.append(k, v) })
    if (logoFile.value) fd.append('logo', logoFile.value)

    const res = await api.post('/recruiter/company', fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    company.value = res.data.data
    successMsg.value = 'Company profile updated successfully!'
    // After update, logo is returned as full URL from asset()
    if (company.value?.logo) {
      logoPreview.value = company.value.logo
    }
    logoFile.value = null
  } catch (err) {
    const errors = err.response?.data?.errors
    errorMsg.value = errors ? Object.values(errors).flat().join(' ') : (err.response?.data?.message || 'Failed to save.')
  } finally {
    saving.value = false
  }
}
</script>

