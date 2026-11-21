import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

// Public Views
import HomeView from '@/views/public/HomeView.vue'
import JobListView from '@/views/public/JobListView.vue'
import JobDetailView from '@/views/public/JobDetailView.vue'
import CompanyListView from '@/views/public/CompanyListView.vue'
import CompanyDetailView from '@/views/public/CompanyDetailView.vue'

// Auth Views
import LoginView from '@/views/auth/LoginView.vue'
import RegisterView from '@/views/auth/RegisterView.vue'

const routes = [
  // ── Public ──
  { path: '/', name: 'Home', component: HomeView },
  { path: '/jobs', name: 'Jobs', component: JobListView },
  { path: '/jobs/:slug', name: 'JobDetail', component: JobDetailView, props: true },
  { path: '/companies', name: 'Companies', component: CompanyListView },
  { path: '/companies/:slug', name: 'CompanyDetail', component: CompanyDetailView, props: true },

  // ── Auth ──
  { path: '/login', name: 'Login', component: LoginView, meta: { guest: true, hideChrome: true } },
  { path: '/register', name: 'Register', component: RegisterView, meta: { guest: true, hideChrome: true } },

  // ── Candidate (lazy-loaded) ──
  {
    path: '/candidate/profile',
    name: 'CandidateProfile',
    component: () => import('@/views/candidate/CandidateProfileView.vue'),
    meta: { requiresAuth: true, role: 'candidate' },
  },
  {
    path: '/candidate/applications',
    name: 'MyApplications',
    component: () => import('@/views/candidate/MyApplicationsView.vue'),
    meta: { requiresAuth: true, role: 'candidate' },
  },
  {
    path: '/candidate/saved-jobs',
    name: 'SavedJobs',
    component: () => import('@/views/candidate/SavedJobsView.vue'),
    meta: { requiresAuth: true, role: 'candidate' },
  },

  // ── Recruiter (lazy-loaded) ──
  {
    path: '/recruiter/dashboard',
    name: 'RecruiterDashboard',
    component: () => import('@/views/recruiter/RecruiterDashboardView.vue'),
    meta: { requiresAuth: true, role: 'recruiter' },
  },
  {
    path: '/recruiter/company',
    name: 'RecruiterCompany',
    component: () => import('@/views/recruiter/CompanyProfileView.vue'),
    meta: { requiresAuth: true, role: 'recruiter' },
  },
  {
    path: '/recruiter/jobs',
    name: 'RecruiterJobs',
    component: () => import('@/views/recruiter/RecruiterJobsView.vue'),
    meta: { requiresAuth: true, role: 'recruiter' },
  },
  {
    path: '/recruiter/jobs/:id/applicants',
    name: 'JobApplicants',
    component: () => import('@/views/recruiter/JobApplicantsView.vue'),
    meta: { requiresAuth: true, role: 'recruiter' },
    props: true,
  },

  // ── Admin (lazy-loaded) ──
  {
    path: '/admin/dashboard',
    name: 'AdminDashboard',
    component: () => import('@/views/admin/AdminDashboardView.vue'),
    meta: { requiresAuth: true, role: 'admin' },
  },

  // ── Catch-all 404 ──
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: { template: `
      <div class="container text-center py-5">
        <h1 class="display-1 text-muted">404</h1>
        <p class="lead">Page not found.</p>
        <router-link to="/" class="btn btn-primary">Back to Home</router-link>
      </div>
    `},
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  },
})

// Navigation Guard
router.beforeEach((to) => {
  const auth = useAuthStore()

  // Redirect guests away from protected routes
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'Login', query: { redirect: to.fullPath } }
  }

  // Redirect authenticated users away from guest-only routes (login/register)
  if (to.meta.guest && auth.isAuthenticated) {
    return { name: 'Home' }
  }

  // Role check
  if (to.meta.role && auth.role !== to.meta.role) {
    return { name: 'Home' }
  }
})

export default router

