\# SOFTWARE REQUIREMENTS SPECIFICATION (SRS) \& AI BLUEPRINT



\*\*Project:\*\* Online Job Portal \& Applicant Tracking System (JobConnect)  

\*\*Course:\*\* IT Specialized Project (CT466 - CT501H) | Optimized for AI Coding Agents (Antigravity / Cursor)



| Attribute | Specification Details |

| :--- | :--- |

| \*\*Project Title\*\* | Online Job Portal \& Applicant Tracking System (JobConnect) |

| \*\*System Architecture\*\* | Decoupled Client-Server Architecture (RESTful API Backend \& SPA Frontend) |

| \*\*Backend Tech Stack\*\* | PHP 8.2+, Laravel 10/11, Laravel Sanctum (Token Auth), Eloquent ORM |

| \*\*Frontend Tech Stack\*\* | Vue.js 3 (Composition API, `<script setup>`), Vite, Vue Router, Pinia, Axios, Bootstrap 5.3 |

| \*\*Database\*\* | MySQL 8.0+ (UTF8mb4 Relational Schema) |

| \*\*Language Standard\*\* | 100% English (UI Labels, Validation Messages, Database Seeders, Code Comments, Documentation) |



\---



\## 1. TECHNICAL DIRECTIVES FOR AI CODING AGENT (SYSTEM PROMPT RULES)



When parsing this specification to scaffold and implement the application, the AI Coding Agent must strictly adhere to the following architectural rules:



\* \*\*Monorepo Directory Structure:\*\* Separate the project into two distinct root directories: `/backend` (Laravel RESTful API) and `/frontend` (Vue.js 3 + Vite SPA).

\* \*\*English-Only Requirement:\*\* All UI components, form labels, status badges, validation messages, API responses, and seeded mock data must be written entirely in professional English.

\* \*\*Standardized JSON API Format:\*\* Every Backend API endpoint must return a consistent JSON structure: `{ "success": true/false, "message": "...", "data": {...}, "errors": {...} }`.

\* \*\*Authentication \& RBAC:\*\* Use Laravel Sanctum to issue Bearer Tokens upon login. Implement a custom RoleMiddleware to verify user roles (`admin`, `recruiter`, `candidate`) on protected API routes.

\* \*\*File Upload Handling:\*\* Resumes/CVs must be restricted to `.pdf` files (max 5MB). User avatars and company logos must accept `.jpg`, `.png`, `.webp` (max 2MB). Store files in `storage/app/public` and expose via `php artisan storage:link`.

\* \*\*CORS Configuration:\*\* Configure `config/cors.php` in Laravel to allow requests from the Frontend development server (e.g., `http://localhost:5173`).

\* \*\*Frontend Architecture:\*\* Use Bootstrap 5.3 and Bootstrap Icons for responsive styling. Route all HTTP calls through a centralized Axios instance configured with Request Interceptors (injecting Bearer Token) and Response Interceptors (handling 401/403 redirects).



\---



\## 2. SYSTEM ACTORS \& ROLE-BASED ACCESS CONTROL (RBAC)



| Actor / Role | Role Enum Value | Core Permissions \& Responsibilities |

| :--- | :--- | :--- |

| \*\*Guest (Unauthenticated)\*\* | N/A | Browse home page, search and filter active job listings, view job details, view verified company profiles, register/login. |

| \*\*Candidate (Job Seeker)\*\* | `candidate` | Manage candidate profile, upload/manage PDF resumes, apply for jobs with cover letters, bookmark/save jobs, track application statuses. |

| \*\*Recruiter (Employer)\*\* | `recruiter` | Manage company profile \& logo, create/edit/close job postings, view applicants per job, preview candidate PDF resumes, update application status \& interview notes, view recruitment analytics. |

| \*\*Administrator (Admin)\*\* | `admin` | Manage user accounts (ban/unban), verify company profiles, approve/reject job postings, manage master data (Categories \& Skills), view system-wide analytics. |



\---



\## 3. RELATIONAL DATABASE SCHEMA SPECIFICATION (9 TABLES)



The AI Agent must generate Laravel Migrations and Eloquent Models with full relationships matching the 9 tables below:



\### 3.1. Table: `users` (System Accounts)

| Field Name | Data Type | Constraints / Default | Description |

| :--- | :--- | :--- | :--- |

| `id` | BIGINT UNSIGNED | PK, Auto Increment | Primary key |

| `name` | VARCHAR(255) | NOT NULL | Full name of the user |

| `email` | VARCHAR(255) | UNIQUE, NOT NULL | Login email address |

| `password` | VARCHAR(255) | NOT NULL | Bcrypt hashed password |

| `role` | ENUM | DEFAULT 'candidate' | Values: `'admin'`, `'recruiter'`, `'candidate'` |

| `phone` | VARCHAR(20) | NULLABLE | Contact phone number |

| `avatar` | VARCHAR(255) | NULLABLE | Storage path to user profile picture |

| `status` | ENUM | DEFAULT 'active' | Values: `'active'`, `'banned'` |

| `created\_at` / `updated\_at` | TIMESTAMP | NULLABLE | Eloquent timestamps |



\### 3.2. Table: `candidate\_profiles` (Job Seeker Details)

| Field Name | Data Type | Constraints / Default | Description |

| :--- | :--- | :--- | :--- |

| `id` | BIGINT UNSIGNED | PK, Auto Increment | Primary key |

| `user\_id` | BIGINT UNSIGNED | FK -> users.id (CASCADE) | One-to-One link with `users` table |

| `dob` | DATE | NULLABLE | Date of birth |

| `gender` | ENUM | NULLABLE | Values: `'male'`, `'female'`, `'other'` |

| `address` | VARCHAR(255) | NULLABLE | Residential address |

| `bio` | TEXT | NULLABLE | Professional summary / bio |

| `education` | TEXT | NULLABLE | Educational background \& degrees |

| `experience\_years` | INT | DEFAULT 0 | Total years of professional experience |

| `skills\_text` | VARCHAR(500) | NULLABLE | Comma-separated personal skills summary |

| `default\_cv\_path` | VARCHAR(255) | NULLABLE | Path to default uploaded PDF resume |

| `created\_at` / `updated\_at` | TIMESTAMP | NULLABLE | Eloquent timestamps |



\### 3.3. Table: `companies` (Employer Profiles)

| Field Name | Data Type | Constraints / Default | Description |

| :--- | :--- | :--- | :--- |

| `id` | BIGINT UNSIGNED | PK, Auto Increment | Primary key |

| `user\_id` | BIGINT UNSIGNED | FK -> users.id (CASCADE) | Recruiter user account owning this company |

| `name` | VARCHAR(255) | NOT NULL | Official company name |

| `slug` | VARCHAR(255) | UNIQUE, NOT NULL | URL-friendly slug |

| `tax\_code` | VARCHAR(50) | NULLABLE | Business registration / tax ID |

| `logo` | VARCHAR(255) | NULLABLE | Storage path to company logo |

| `website` | VARCHAR(255) | NULLABLE | Corporate website URL |

| `company\_size` | VARCHAR(100) | NULLABLE | e.g., '10-50 employees', '100-500 employees' |

| `address` | VARCHAR(255) | NOT NULL | Headquarters street address |

| `city` | VARCHAR(100) | NOT NULL | City / Location (e.g., Can Tho, Ho Chi Minh City, Hanoi) |

| `description` | LONGTEXT | NULLABLE | Detailed company overview \& culture |

| `is\_verified` | BOOLEAN | DEFAULT FALSE | Admin verification flag (false: Pending, true: Verified) |

| `created\_at` / `updated\_at` | TIMESTAMP | NULLABLE | Eloquent timestamps |



\### 3.4. Tables: `categories` \& `skills`

| Table Name | Columns \& Types | Purpose |

| :--- | :--- | :--- |

| `categories` | `id` (PK), `name` (VARCHAR 255), `slug` (UNIQUE), `icon` (VARCHAR 100, nullable), `status` (BOOLEAN default true), timestamps | Classifies jobs by industry. |

| `skills` | `id` (PK), `name` (VARCHAR 100), `slug` (UNIQUE), timestamps | Skill tags associated with jobs (PHP, Laravel, Vue.js, etc.). |



\### 3.5. Tables: `jobs` \& `job\_skill` (Pivot Table)

| Field Name | Data Type | Constraints / Default | Description |

| :--- | :--- | :--- | :--- |

| `id` | BIGINT UNSIGNED | PK, Auto Increment | Primary key |

| `company\_id` | BIGINT UNSIGNED | FK -> companies.id (CASCADE) | Hiring company |

| `category\_id` | BIGINT UNSIGNED | FK -> categories.id | Industry category |

| `title` | VARCHAR(255) | NOT NULL | Job title (e.g., Junior Full-Stack Developer) |

| `slug` | VARCHAR(255) | UNIQUE, NOT NULL | Unique URL slug |

| `job\_type` | ENUM | DEFAULT 'full\_time' | Values: `'full\_time'`, `'part\_time'`, `'remote'`, `'internship'` |

| `experience\_level` | VARCHAR(100) | NOT NULL | e.g., 'Entry Level', '1-2 Years', 'Senior (5+ Years)' |

| `salary\_min` | DECIMAL(12,2) | NULLABLE | Minimum monthly salary (USD) |

| `salary\_max` | DECIMAL(12,2) | NULLABLE | Maximum monthly salary (USD) |

| `is\_negotiable` | BOOLEAN | DEFAULT FALSE | True if salary is negotiable |

| `location` | VARCHAR(100) | NOT NULL | City / Region |

| `work\_address` | VARCHAR(255) | NOT NULL | Specific office address |

| `vacancies` | INT | DEFAULT 1 | Number of open positions |

| `description` | LONGTEXT | NOT NULL | Detailed job responsibilities |

| `requirements` | LONGTEXT | NOT NULL | Candidate qualifications \& skills required |

| `benefits` | LONGTEXT | NOT NULL | Perks, compensation \& benefits |

| `deadline` | DATE | NOT NULL | Application closing date |

| `status` | ENUM | DEFAULT 'pending' | Values: `'pending'`, `'active'`, `'closed'`, `'rejected'` |

| `views\_count` | INT | DEFAULT 0 | Total page views counter |

| `created\_at` / `updated\_at` | TIMESTAMP | NULLABLE | Eloquent timestamps |



\* \*\*Pivot Table `job\_skill`:\*\* `id`, `job\_id` (FK -> `jobs.id` onDelete cascade), `skill\_id` (FK -> `skills.id` onDelete cascade). Represents Many-to-Many relationship between Jobs and Skills.



\### 3.6. Tables: `applications` \& `saved\_jobs`

| Field Name | Data Type | Constraints / Default | Description |

| :--- | :--- | :--- | :--- |

| `id` | BIGINT UNSIGNED | PK, Auto Increment | Primary key |

| `job\_id` | BIGINT UNSIGNED | FK -> jobs.id (CASCADE) | Target job posting |

| `candidate\_id` | BIGINT UNSIGNED | FK -> users.id (CASCADE) | Candidate user submitting the application |

| `cv\_file\_path` | VARCHAR(255) | NOT NULL | Path to the submitted PDF resume file |

| `cover\_letter` | TEXT | NULLABLE | Candidate cover letter message |

| `status` | ENUM | DEFAULT 'pending' | Values: `'pending'`, `'reviewed'`, `'interview'`, `'offered'`, `'rejected'` |

| `recruiter\_note` | TEXT | NULLABLE | Feedback or interview schedule note from Recruiter |

| `created\_at` / `updated\_at` | TIMESTAMP | NULLABLE | Submission and status update timestamps |



\* \*\*Table `saved\_jobs`:\*\* `id`, `user\_id` (FK -> `users.id`), `job\_id` (FK -> `jobs.id`), `created\_at`. Unique constraint on `(user\_id, job\_id)`.



\---



\## 4. RESTFUL API ENDPOINTS SPECIFICATION (LARAVEL BACKEND)



All routes are prefixed with `/api` in `routes/api.php`:



| Method | Endpoint | Access Role | Controller Logic \& Payload Specification |

| :--- | :--- | :--- | :--- |

| `POST` | `/api/auth/register` | Public | Register user (`name`, `email`, `password`, `role`: `candidate`\\|`recruiter`). Auto-create empty profile/draft company. |

| `POST` | `/api/auth/login` | Public | Authenticate credentials, check status != 'banned', return Sanctum token + user object. |

| `POST` | `/api/auth/logout` | Authenticated | Revoke `currentAccessToken()`. |

| `GET` | `/api/auth/me` | Authenticated | Return logged-in user details with candidate\_profile or company relation. |

| `GET` | `/api/jobs` | Public | List active jobs (`status='active'`, `deadline >= today`). Filters: `keyword`, `category\_id`, `location`, `job\_type`, `salary\_min`, `skill\_id`. Paginate(10). |

| `GET` | `/api/jobs/{slug}` | Public | Get full job details with company info, skills array, related jobs, and increment `views\_count`. |

| `GET` | `/api/companies` | Public | List verified companies with open jobs count. |

| `GET` | `/api/companies/{slug}` | Public | Get company profile details along with its active job postings. |

| `GET` | `/api/master-data` | Public | Return all active categories, skills, and predefined cities for dropdown filters. |

| `GET` | `/api/candidate/profile` | candidate | Retrieve current candidate profile and user info. |

| `POST` | `/api/candidate/profile` | candidate | Update personal info, bio, education, experience\_years, avatar, and upload `default\_cv\_path` (.pdf). |

| `POST` | `/api/candidate/jobs/{id}/apply` | candidate | Submit job application. Prevent duplicate submission. Accept new uploaded `.pdf` CV or use `default\_cv\_path` + `cover\_letter`. |

| `GET` | `/api/candidate/applications` | candidate | List all submitted applications by current candidate with status and `recruiter\_note`. |

| `POST` | `/api/candidate/jobs/{id}/toggle-save` | candidate | Toggle bookmark (save/unsave) for a job posting. |

| `GET` | `/api/candidate/saved-jobs` | candidate | List all bookmarked jobs for current candidate. |

| `GET` | `/api/recruiter/company` | recruiter | Retrieve recruiter's company profile. |

| `POST` | `/api/recruiter/company` | recruiter | Update company details and upload company logo. |

| `GET` | `/api/recruiter/jobs` | recruiter | List all jobs posted by recruiter's company with `applications\_count`. |

| `POST` | `/api/recruiter/jobs` | recruiter | Create a new job posting and sync `skill\_ids` in `job\_skill` pivot table. |

| `PUT` | `/api/recruiter/jobs/{id}` | recruiter | Update job posting details or change status to `'closed'`. |

| `DELETE` | `/api/recruiter/jobs/{id}` | recruiter | Delete a job posting owned by the recruiter. |

| `GET` | `/api/recruiter/jobs/{id}/applications` | recruiter | List all candidate applications for a specific job, filterable by status. |

| `PATCH` | `/api/recruiter/applications/{id}/status` | recruiter | Update application status (`'reviewed'`, `'interview'`, `'offered'`, `'rejected'`) and save `recruiter\_note`. |

| `GET` | `/api/recruiter/dashboard-stats` | recruiter | Return summary metrics: total jobs, active jobs, total applications, pending applications. |

| `GET` | `/api/admin/dashboard-stats` | admin | Return platform metrics: total candidates, total recruiters, total jobs, total applications. |

| `GET` | `/api/admin/jobs` | admin | List all jobs across platform, filterable by status (`'pending'`, `'active'`, `'rejected'`). |

| `PATCH` | `/api/admin/jobs/{id}/status` | admin | Approve (`'active'`) or reject (`'rejected'`) a job posting. |

| `GET` | `/api/admin/users` | admin | List all user accounts with filtering by role and keyword. |

| `PATCH` | `/api/admin/users/{id}/toggle-ban` | admin | Ban or unban a user account (status: `'active'` \\| `'banned'`). |

| `CRUD` | `/api/admin/categories \& /api/admin/skills` | admin | Create, update, and delete job categories and technical skills. |



\---



\## 5. FRONTEND SPA ARCHITECTURE (VUE.JS 3 + BOOTSTRAP 5)



The Frontend (`/frontend/src`) must be structured with clean reusable components and English UI labels:



\* \*\*State Management (Pinia):\*\* `authStore` (user profile, token, role, login/logout actions) and `masterStore` (categories, skills, locations).

\* \*\*Router \& Navigation Guards:\*\* Configure Vue Router with `meta.requiresAuth` and `meta.role` checks to protect Candidate, Recruiter, and Admin routes.



| Module | Route Path | Vue View Component | UI Features \& Functional Elements |

| :--- | :--- | :--- | :--- |

| \*\*Public\*\* | `/` | `HomeView.vue` | Hero search bar, featured categories grid, latest active job cards, top verified companies. |

| \*\*Public\*\* | `/jobs` | `JobListView.vue` | Sidebar filters (Keyword, Category, Location, Job Type, Min Salary, Skill), paginated job cards. |

| \*\*Public\*\* | `/jobs/:slug` | `JobDetailView.vue` | Full job description, requirements, benefits, company sidebar, 'Apply Now' modal (upload PDF or use default CV + Cover Letter), 'Save Job' button. |

| \*\*Public\*\* | `/companies \& /companies/:slug` | `CompanyList / DetailView.vue` | Directory of verified employers and detailed company page showing open positions. |

| \*\*Auth\*\* | `/login \& /register` | `LoginView / RegisterView.vue` | Clean authentication forms with role selector ('Job Seeker / Candidate' vs 'Employer / Recruiter'). |

| \*\*Candidate\*\* | `/candidate/profile` | `CandidateProfileView.vue` | Profile editor form, skills input, experience years, PDF Resume upload \& preview link. |

| \*\*Candidate\*\* | `/candidate/applications` | `MyApplicationsView.vue` | Application tracker table with color-coded status badges (Pending, Reviewed, Interview, Offered, Rejected) and HR notes modal. |

| \*\*Candidate\*\* | `/candidate/saved-jobs` | `SavedJobsView.vue` | Grid of bookmarked jobs with quick apply/remove buttons. |

| \*\*Recruiter\*\* | `/recruiter/dashboard` | `RecruiterDashboardView.vue` | KPI stat cards (Total Jobs, Active Jobs, Total Applicants, Pending Review) and recent applicants table. |

| \*\*Recruiter\*\* | `/recruiter/company` | `CompanyProfileView.vue` | Company profile editor with logo upload and verification badge display. |

| \*\*Recruiter\*\* | `/recruiter/jobs` | `RecruiterJobsView.vue` | Job management table with Create/Edit Job modal, status toggle, and link to view applicants. |

| \*\*Recruiter\*\* | `/recruiter/jobs/:id/applicants` | `JobApplicantsView.vue` | ATS view: Candidate info, Cover Letter viewer, direct PDF CV preview/download, and Status Update modal with HR notes. |

| \*\*Admin\*\* | `/admin/dashboard` | `AdminDashboardView.vue` | System KPI cards, tabbed interface for Job Approvals, User Management (Ban/Unban), and Category/Skill CRUD. |



\---



\## 6. AUTOMATED MOCK DATA SEEDER (ENGLISH DATASET)



To ensure immediate demonstration readiness, `DatabaseSeeder.php` must populate realistic English data:



\* \*\*Demo Accounts (Default Password: `password123`):\*\* 1 Admin (`admin@jobconnect.com`), 3 Recruiters (`hr@techcorp.com`, `talent@cloudnine.io`, `careers@mekongdigital.vn`), and 5 Candidates (`john.doe@gmail.com`, `jane.smith@gmail.com`, `alex.nguyen@gmail.com`, `emily.tran@gmail.com`, `michael.le@gmail.com`).

\* \*\*Categories \& Skills:\*\* 8 IT/Business

