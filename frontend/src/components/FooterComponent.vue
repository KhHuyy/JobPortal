<template>
  <footer class="site-footer mt-auto border-top">
    <div class="container py-5">
      <div class="row g-4">
        <!-- Brand Info -->
        <div class="col-lg-4 mb-2 mb-lg-0">
          <router-link to="/" class="d-inline-flex align-items-center gap-2 text-white text-decoration-none mb-3">
            <div class="footer-brand-box rounded-3 d-flex align-items-center justify-content-center">
              <i class="bi bi-briefcase-fill text-white"></i>
            </div>
            <span class="fs-5 fw-bold">JobConnect</span>
          </router-link>
          <p class="small text-muted pe-lg-4 mb-3" style="line-height: 1.6;">
            A modern, transparent engineering recruitment platform and Applicant Tracking System (ATS). Connecting top tech talent directly with verified engineering teams.
          </p>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
              <i class="bi bi-shield-check me-1"></i>Verified &amp; Safe
            </span>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 small">
              <i class="bi bi-lightning-charge me-1"></i>Real-Time ATS
            </span>
          </div>
        </div>

        <!-- For Candidates -->
        <div class="col-6 col-md-3 col-lg-2">
          <h6 class="text-white fw-bold mb-3 small text-uppercase" style="letter-spacing:0.05em;">Job Seekers</h6>
          <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
            <li><router-link to="/jobs" class="footer-link">Browse All Jobs</router-link></li>
            <li><router-link to="/jobs?job_type=remote" class="footer-link">Remote Positions</router-link></li>
            <li><router-link to="/companies" class="footer-link">Top Tech Companies</router-link></li>
            <li><router-link to="/register" class="footer-link">Create Account</router-link></li>
          </ul>
        </div>

        <!-- For Employers -->
        <div class="col-6 col-md-3 col-lg-2">
          <h6 class="text-white fw-bold mb-3 small text-uppercase" style="letter-spacing:0.05em;">Employers</h6>
          <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
            <li><router-link to="/register" class="footer-link">Post a Job</router-link></li>
            <li><router-link to="/login" class="footer-link">Recruiter Workspace</router-link></li>
            <li><router-link to="/login" class="footer-link">ATS Dashboard</router-link></li>
            <li><router-link to="/jobs" class="footer-link">Hiring Solutions</router-link></li>
          </ul>
        </div>

        <!-- Contact & Project info -->
        <div class="col-md-6 col-lg-4">
          <h6 class="text-white fw-bold mb-3 small text-uppercase" style="letter-spacing:0.05em;">Project &amp; Support</h6>
          <ul class="list-unstyled small text-muted d-flex flex-column gap-2 mb-3">
            <li class="d-flex align-items-center gap-2">
              <i class="bi bi-envelope text-primary"></i> support@jobconnect.app
            </li>
            <li class="d-flex align-items-center gap-2">
              <i class="bi bi-geo-alt text-primary"></i> Can Tho University, Vietnam
            </li>
            <li class="d-flex align-items-center gap-2">
              <i class="bi bi-terminal text-primary"></i> CT466 Specialized IT Project
            </li>
          </ul>
          <div class="small text-muted">
            Built with modern <strong>Vue 3</strong> + <strong>Laravel 11 Sanctum</strong>.
          </div>
        </div>
      </div>

      <hr class="border-secondary opacity-25 my-4" />

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 small text-muted">
        <div>
          &copy; {{ new Date().getFullYear() }} JobConnect. All rights reserved.
        </div>
        <div class="d-flex gap-3">
          <router-link to="/" class="footer-link">Privacy Policy</router-link>
          <router-link to="/" class="footer-link">Terms of Service</router-link>
          <router-link to="/" class="footer-link">Security</router-link>
        </div>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.site-footer {
  background-color: #0f172a;
  color: #94a3b8;
  border-top-color: #1e293b !important;
}

.footer-brand-box {
  width: 32px;
  height: 32px;
  background-color: #2563eb;
  font-size: 1rem;
}

.footer-link {
  color: #94a3b8;
  text-decoration: none;
  transition: color 0.15s ease;
}
.footer-link:hover {
  color: #ffffff;
}
</style>
