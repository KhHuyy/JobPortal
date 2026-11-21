<template>
  <footer class="site-footer mt-auto border-top">
    <div class="container py-5">
      <div class="row g-4">
        <!-- Brand Info -->
        <div class="col-lg-4 mb-2 mb-lg-0">
          <router-link to="/" class="d-inline-flex align-items-center gap-2 text-white text-decoration-none mb-3">
            <div class="footer-brand-box rounded-3 d-flex align-items-center justify-content-center shadow-sm">
              <i class="bi bi-briefcase-fill text-white"></i>
            </div>
            <span class="fs-5 fw-bold text-white">JobConnect</span>
          </router-link>
          <p class="small footer-desc pe-lg-4 mb-3" style="line-height: 1.6;">
            A modern, transparent engineering recruitment platform and Applicant Tracking System (ATS). Connecting top tech talent directly with verified engineering teams.
          </p>
          <div class="d-flex align-items-center gap-2">
            <span class="badge verified-badge px-2.5 py-1 small">
              <i class="bi bi-shield-check me-1"></i>Verified &amp; Safe
            </span>
            <span class="badge ats-badge px-2.5 py-1 small">
              <i class="bi bi-lightning-charge me-1"></i>Real-Time ATS
            </span>
          </div>
        </div>

        <!-- For Candidates -->
        <div class="col-6 col-md-3 col-lg-2">
          <h6 class="text-white fw-bold mb-3 small text-uppercase" style="letter-spacing:0.06em;">Job Seekers</h6>
          <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
            <li><router-link to="/jobs" class="footer-link">Browse All Jobs</router-link></li>
            <li><router-link to="/jobs?job_type=remote" class="footer-link">Remote Positions</router-link></li>
            <li><router-link to="/companies" class="footer-link">Top Tech Companies</router-link></li>
            <li><router-link to="/register" class="footer-link">Create Account</router-link></li>
          </ul>
        </div>

        <!-- For Employers -->
        <div class="col-6 col-md-3 col-lg-2">
          <h6 class="text-white fw-bold mb-3 small text-uppercase" style="letter-spacing:0.06em;">Employers</h6>
          <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
            <li><router-link to="/register" class="footer-link">Post a Job</router-link></li>
            <li><router-link to="/login" class="footer-link">Recruiter Workspace</router-link></li>
            <li><router-link to="/login" class="footer-link">ATS Dashboard</router-link></li>
            <li><router-link to="/jobs" class="footer-link">Hiring Solutions</router-link></li>
          </ul>
        </div>

        <!-- Contact & Project info -->
        <div class="col-md-6 col-lg-4">
          <h6 class="text-white fw-bold mb-3 small text-uppercase" style="letter-spacing:0.06em;">Project &amp; Support</h6>
          <ul class="list-unstyled small footer-contact d-flex flex-column gap-2 mb-3">
            <li class="d-flex align-items-center gap-2">
              <i class="bi bi-envelope text-indigo-bright"></i> <span>support@jobconnect.app</span>
            </li>
            <li class="d-flex align-items-center gap-2">
              <i class="bi bi-geo-alt text-indigo-bright"></i> <span>Can Tho University, Vietnam</span>
            </li>
            <li class="d-flex align-items-center gap-2">
              <i class="bi bi-terminal text-indigo-bright"></i> <span>CT466 Specialized IT Project</span>
            </li>
          </ul>
          <div class="small footer-subtext">
            Built with modern <strong class="text-white">Vue 3</strong> + <strong class="text-white">Laravel 11 Sanctum</strong>.
          </div>
        </div>
      </div>

      <hr class="footer-divider my-4" />

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 small footer-subtext">
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
  background: linear-gradient(180deg, #0f172a 0%, #090d16 100%);
  color: #cbd5e1;
  border-top: 1px solid #1e293b !important;
}

.footer-brand-box {
  width: 34px;
  height: 34px;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  font-size: 1.05rem;
}

.footer-desc {
  color: #cbd5e1;
}

.footer-link {
  color: #94a3b8;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.18s ease;
}
.footer-link:hover {
  color: #ffffff;
  transform: translateX(2px);
}

.footer-contact {
  color: #cbd5e1;
}

.text-indigo-bright {
  color: #818cf8;
}

.footer-subtext {
  color: #94a3b8;
}

.footer-divider {
  border-color: #1e293b;
  opacity: 0.8;
}

.verified-badge {
  background: rgba(16, 185, 129, 0.16);
  color: #34d399;
  border: 1px solid rgba(16, 185, 129, 0.35);
  font-weight: 600;
}

.ats-badge {
  background: rgba(99, 102, 241, 0.16);
  color: #a5b4fc;
  border: 1px solid rgba(99, 102, 241, 0.35);
  font-weight: 600;
}
</style>
