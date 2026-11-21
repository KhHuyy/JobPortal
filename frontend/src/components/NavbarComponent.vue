<script setup>
import { computed, ref, onMounted, onUnmounted, watch } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useRouter, useRoute } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()

const menuOpen = ref(false)

const dashboardRoute = computed(() => {
  if (auth.isAdmin) return { name: 'AdminDashboard' }
  if (auth.isRecruiter) return { name: 'RecruiterDashboard' }
  return { name: 'CandidateProfile' }
})

const workspaceRoleBadgeClass = computed(() => {
  if (auth.isAdmin) return 'bg-danger-subtle text-danger border border-danger-subtle'
  if (auth.isRecruiter) return 'bg-primary-subtle text-primary border border-primary-subtle'
  return 'bg-success-subtle text-success border border-success-subtle'
})

const workspaceRoleIcon = computed(() => {
  if (auth.isAdmin) return 'bi-shield-check'
  if (auth.isRecruiter) return 'bi-building'
  return 'bi-person-badge'
})

async function handleLogout() {
  await auth.logout()
  router.push({ name: 'Home' })
}

function closeMenu() {
  menuOpen.value = false
}

function onDocumentClick(e) {
  const dropdown = document.getElementById('vue-user-dropdown')
  if (dropdown && !dropdown.contains(e.target)) {
    menuOpen.value = false
  }
}

// Close dropdown on route change
watch(route, () => {
  menuOpen.value = false
})

onMounted(() => {
  document.addEventListener('click', onDocumentClick)
})

onUnmounted(() => {
  document.removeEventListener('click', onDocumentClick)
})
</script>

<template>
  <header class="sticky-top">
    <!-- Main Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
      <div class="container">
        <router-link to="/" class="navbar-brand d-flex align-items-center gap-2">
          <div class="brand-icon-box rounded-3 d-flex align-items-center justify-content-center">
            <i class="bi bi-briefcase-fill"></i>
          </div>
          <span class="brand-text">JobConnect</span>
        </router-link>

        <button class="navbar-toggler border-0" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
          <!-- Left nav -->
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <router-link to="/jobs" class="nav-link px-3" active-class="fw-bold text-primary">
                <i class="bi bi-search me-1"></i> Find Jobs
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/companies" class="nav-link px-3" active-class="fw-bold text-primary">
                <i class="bi bi-building me-1"></i> Companies
              </router-link>
            </li>
          </ul>

          <!-- Right nav -->
          <ul class="navbar-nav ms-auto align-items-center">
            <!-- Guest links -->
            <template v-if="!auth.isAuthenticated">
              <li class="nav-item">
                <router-link to="/login" class="nav-link fw-semibold px-3">Sign In</router-link>
              </li>
              <li class="nav-item">
                <router-link to="/register" class="btn btn-primary btn-sm ms-2 rounded-2 px-3">
                  <i class="bi bi-person-plus me-1"></i> Register
                </router-link>
              </li>
            </template>

            <!-- Authenticated user: Vue-reactive dropdown + always-visible Sign Out -->
            <template v-else>
              <li class="nav-item d-flex align-items-center position-relative" id="vue-user-dropdown">
                <!-- User avatar / name button — toggles dropdown -->
                <button
                  class="btn btn-link nav-link d-flex align-items-center gap-2 pe-1 text-decoration-none"
                  @click.stop="menuOpen = !menuOpen"
                >
                  <div class="user-nav-avatar">
                    {{ (auth.user?.name || 'U')[0].toUpperCase() }}
                  </div>
                  <span class="fw-semibold text-dark">{{ auth.user?.name || 'Account' }}</span>
                  <span class="badge ms-1 text-capitalize rounded-pill px-2" :class="workspaceRoleBadgeClass">
                    {{ auth.role }}
                  </span>
                  <i :class="menuOpen ? 'bi bi-chevron-up' : 'bi bi-chevron-down'" class="ms-1 small text-muted"></i>
                </button>

                <!-- Always-visible Sign Out button -->
                <button
                  class="btn btn-sm btn-outline-danger ms-2 rounded-pill px-3"
                  @click="handleLogout"
                  title="Sign Out"
                >
                  <i class="bi bi-box-arrow-right me-1"></i> Sign Out
                </button>

                <!-- Vue-reactive dropdown menu -->
                <div
                  v-if="menuOpen"
                  class="dropdown-menu dropdown-menu-end shadow-lg border rounded-3 show p-2"
                  style="position: absolute; top: calc(100% + 8px); right: 0; z-index: 1050; min-width: 200px;"
                  @click.stop
                >
                  <router-link :to="dashboardRoute" class="dropdown-item rounded-2 py-2" @click="closeMenu">
                    <i class="bi bi-speedometer2 me-2 text-primary"></i> Dashboard
                  </router-link>

                  <!-- Candidate-specific links -->
                  <template v-if="auth.isCandidate">
                    <router-link to="/candidate/profile" class="dropdown-item rounded-2 py-2" @click="closeMenu">
                      <i class="bi bi-person me-2 text-primary"></i> My Profile
                    </router-link>
                    <router-link to="/candidate/applications" class="dropdown-item rounded-2 py-2" @click="closeMenu">
                      <i class="bi bi-file-earmark-text me-2 text-primary"></i> My Applications
                    </router-link>
                    <router-link to="/candidate/saved-jobs" class="dropdown-item rounded-2 py-2" @click="closeMenu">
                      <i class="bi bi-bookmark me-2 text-primary"></i> Saved Jobs
                    </router-link>
                  </template>

                  <!-- Recruiter-specific links -->
                  <template v-if="auth.isRecruiter">
                    <router-link to="/recruiter/company" class="dropdown-item rounded-2 py-2" @click="closeMenu">
                      <i class="bi bi-building me-2 text-primary"></i> Company Profile
                    </router-link>
                    <router-link to="/recruiter/jobs" class="dropdown-item rounded-2 py-2" @click="closeMenu">
                      <i class="bi bi-briefcase me-2 text-primary"></i> Manage Jobs
                    </router-link>
                  </template>

                  <div class="dropdown-divider my-1"></div>
                  <button class="dropdown-item rounded-2 py-2 text-danger" @click="handleLogout">
                    <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                  </button>
                </div>
              </li>
            </template>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Role-Specific Workspace Sub-Navbar -->
    <div v-if="auth.isAuthenticated" class="workspace-subnav bg-white border-bottom py-2">
      <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
        <!-- Left: Workspace indicator -->
        <div class="d-flex align-items-center gap-2">
          <span class="badge rounded-pill text-uppercase px-2 py-1 small fw-bold" :class="workspaceRoleBadgeClass">
            <i :class="'bi ' + workspaceRoleIcon" class="me-1"></i>{{ auth.role }} Workspace
          </span>
        </div>

        <!-- Right/Center: Navigation links with active route highlighting -->
        <ul class="nav nav-pills workspace-nav-pills gap-1 align-items-center mb-0">
          <!-- Candidate Links -->
          <template v-if="auth.isCandidate">
            <li class="nav-item">
              <router-link to="/candidate/profile" class="nav-link subnav-link" active-class="active">
                <i class="bi bi-person me-1"></i>My Profile
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/candidate/applications" class="nav-link subnav-link" active-class="active">
                <i class="bi bi-file-earmark-text me-1"></i>My Applications
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/candidate/saved-jobs" class="nav-link subnav-link" active-class="active">
                <i class="bi bi-bookmark me-1"></i>Saved Jobs
              </router-link>
            </li>
          </template>

          <!-- Recruiter Links -->
          <template v-if="auth.isRecruiter">
            <li class="nav-item">
              <router-link to="/recruiter/dashboard" class="nav-link subnav-link" active-class="active">
                <i class="bi bi-speedometer2 me-1"></i>Dashboard
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/recruiter/jobs" class="nav-link subnav-link" active-class="active">
                <i class="bi bi-briefcase me-1"></i>Manage Jobs
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/recruiter/company" class="nav-link subnav-link" active-class="active">
                <i class="bi bi-building me-1"></i>Company Profile
              </router-link>
            </li>
          </template>

          <!-- Admin Links & Quick Jump Pills -->
          <template v-if="auth.isAdmin">
            <li class="nav-item">
              <router-link to="/admin/dashboard" class="nav-link subnav-link" :class="{ active: route.name === 'AdminDashboard' && !route.query.tab }">
                <i class="bi bi-shield-lock me-1"></i>Admin Control Center
              </router-link>
            </li>
            <li class="nav-item">
              <router-link :to="{ path: '/admin/dashboard', query: { tab: 'jobs' } }" class="nav-link subnav-pill" :class="{ active: route.name === 'AdminDashboard' && route.query.tab === 'jobs' }">
                <i class="bi bi-check2-circle me-1"></i>Job Approvals
              </router-link>
            </li>
            <li class="nav-item">
              <router-link :to="{ path: '/admin/dashboard', query: { tab: 'users' } }" class="nav-link subnav-pill" :class="{ active: route.name === 'AdminDashboard' && route.query.tab === 'users' }">
                <i class="bi bi-people me-1"></i>User Management
              </router-link>
            </li>
            <li class="nav-item">
              <router-link :to="{ path: '/admin/dashboard', query: { tab: 'categories' } }" class="nav-link subnav-pill" :class="{ active: route.name === 'AdminDashboard' && (route.query.tab === 'categories' || route.query.tab === 'skills') }">
                <i class="bi bi-tags me-1"></i>Categories &amp; Skills
              </router-link>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </header>
</template>

<style scoped>
.brand-icon-box {
  width: 34px;
  height: 34px;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  color: #ffffff;
  font-size: 1rem;
  box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3);
}

.brand-text {
  font-weight: 800;
  font-size: 1.25rem;
  letter-spacing: -0.025em;
  background: linear-gradient(135deg, #0f172a 0%, #312e81 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.user-nav-avatar {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  color: #ffffff;
  font-weight: 700;
  font-size: 0.85rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.workspace-subnav {
  background-color: #ffffff;
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.04);
}

.subnav-link {
  font-size: 0.875rem;
  font-weight: 500;
  color: #475569;
  padding: 0.35rem 0.75rem;
  border-radius: 8px;
  transition: all 0.15s ease;
}

.subnav-link:hover {
  color: #4f46e5;
  background-color: #f1f5f9;
}

.subnav-link.active {
  color: #4f46e5 !important;
  background-color: #eef2ff !important;
  font-weight: 600;
}

.subnav-pill {
  font-size: 0.8rem;
  font-weight: 500;
  color: #64748b;
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  padding: 0.25rem 0.65rem;
  border-radius: 9999px;
  transition: all 0.15s ease;
}

.subnav-pill:hover {
  background-color: #f1f5f9;
  color: #1e293b;
  border-color: #cbd5e1;
}

.subnav-pill.active {
  background-color: #4f46e5 !important;
  color: #ffffff !important;
  border-color: #4f46e5 !important;
  font-weight: 600;
}
</style>
