# MINISTRY OF EDUCATION AND TRAINING
# CAN THO UNIVERSITY
# COLLEGE OF INFORMATION AND COMMUNICATION TECHNOLOGY
# DEPARTMENT OF INFORMATION TECHNOLOGY

---

<br><br><br>

# SPECIALIZED IT PROJECT REPORT
### COURSE CODE: CT466 – SPECIALIZED PROJECT IN INFORMATION TECHNOLOGY
### MAJOR: INFORMATION TECHNOLOGY

<br><br>

## PROJECT TITLE:
# JOBCONNECT: MODERN ONLINE RECRUITMENT PLATFORM AND APPLICANT TRACKING SYSTEM (ATS)

<br><br><br><br>

**Academic Supervisor:**  
**M.Sc. Thai Minh Tuan**

**Student:**  
**Huynh Khanh Huy**  
**Student ID:** B2306666  
**Cohort:** K49  

<br><br><br>

**Can Tho, November 2026**

\newpage

---

# ACKNOWLEDGMENTS

First and foremost, I would like to express my deepest gratitude to all the lecturers of the Department of Information Technology and the College of Information and Communication Technology - Can Tho University, who have provided me with invaluable specialized knowledge, constant encouragement, and an inspiring academic environment throughout my university studies.

In particular, I would like to express my heartfelt appreciation and profound gratitude to **Lecturer M.Sc. Thai Minh Tuan**, who has directly supervised, advised, and provided insightful technical guidance during the research, design, implementation, and deployment of this specialized project. His constructive feedback, rigorous academic standards, and mentorship in decoupled client-server software architectures were essential in helping me overcome engineering challenges and successfully complete this project.

Although I have exerted my utmost effort to research and implement the **"JobConnect: Modern Online Recruitment Platform and Applicant Tracking System (ATS)"**, limitations in time and practical experience may lead to certain imperfections. I sincerely welcome all evaluations, constructive critiques, and recommendations from the Examination Committee and Lecturers to further refine and expand this system in the future.

Sincerely thank you!

*Can Tho, November 2026*  
**Huynh Khanh Huy**

\newpage

---

# TABLE OF CONTENTS

- [ACKNOWLEDGMENTS](#acknowledgments)
- [LIST OF TABLES](#list-of-tables)
- [LIST OF FIGURES](#list-of-figures)
- [ABSTRACT](#abstract)
- [INTRODUCTION](#introduction)
  - [1. Problem Statement](#1-problem-statement)
  - [2. Related Works](#2-related-works)
  - [3. Project Objectives](#3-project-objectives)
  - [4. Research Scope and Target Audience](#4-research-scope-and-target-audience)
  - [5. Research Methodology](#5-research-methodology)
  - [6. Research Content and Workload Schedule](#6-research-content-and-workload-schedule)
  - [7. Report Organization](#7-report-organization)
  - [8. Source Code Repository](#8-source-code-repository)
- [MAIN CONTENT](#main-content)
  - [CHAPTER 1: REQUIREMENTS SPECIFICATION](#chapter-1-requirements-specification)
    - [1.1. System Actors and Permission Matrix](#11-system-actors-and-permission-matrix)
    - [1.2. Functional Requirements (Use Case Specification)](#12-functional-requirements-use-case-specification)
    - [1.3. Non-Functional Requirements](#13-non-functional-requirements)
  - [CHAPTER 2: SYSTEM DESIGN](#chapter-2-system-design)
    - [2.1. Overall System Architecture](#21-overall-system-architecture)
    - [2.2. Component and Data Model Description](#22-component-and-data-model-description)
    - [2.3. Relational Database Schema Design (3NF)](#23-relational-database-schema-design-3nf)
    - [2.4. Core Business Workflows and Algorithms](#24-core-business-workflows-and-algorithms)
  - [CHAPTER 3: SYSTEM IMPLEMENTATION](#chapter-3-system-implementation)
    - [3.1. Development Environment and Technology Stack](#31-development-environment-and-technology-stack)
    - [3.2. Subsystems and User Interface Implementation](#32-subsystems-and-user-interface-implementation)
    - [3.3. Security Architecture & RoleMiddleware Implementation](#33-security-architecture--rolemiddleware-implementation)
    - [3.4. RESTful API Endpoints Specification](#34-restful-api-endpoints-specification)
  - [CHAPTER 4: TESTING AND EVALUATION](#chapter-4-testing-and-evaluation)
    - [4.1. Testing Objectives](#41-testing-objectives)
    - [4.2. Automated Testing Suite and Test Scenarios](#42-automated-testing-suite-and-test-scenarios)
    - [4.3. Automated Testing Execution Results](#43-automated-testing-execution-results)
- [CONCLUSION](#conclusion)
  - [1. Achieved Results](#1-achieved-results)
  - [2. Limitations and Future Development](#2-limitations-and-future-development)
- [REFERENCES](#references)
- [APPENDIX](#appendix)
  - [Appendix A: Environment Setup & Deployment Guide](#appendix-a-environment-setup--deployment-guide)
  - [Appendix B: User Manual & Demonstration Credentials](#appendix-b-user-manual--demonstration-credentials)

\newpage

---

# LIST OF TABLES

- **Table 1.1:** 14-Week Project Implementation Schedule and Work Breakdown
- **Table 1.2:** Role-Based Access Control (RBAC) Permission Matrix
- **Table 1.3:** Comprehensive System Use Case Specification (16 Use Cases)
- **Table 2.1:** Relational Database Table: `users` (System User Accounts)
- **Table 2.2:** Relational Database Table: `candidate_profiles` (Job Seeker Profiles)
- **Table 2.3:** Relational Database Table: `companies` (Employer Workspaces)
- **Table 2.4:** Relational Database Table: `categories` (Job Industry Categories)
- **Table 2.5:** Relational Database Table: `skills` (Technical & Soft Skills)
- **Table 2.6:** Relational Database Table: `jobs` (Job Postings & Requirements)
- **Table 2.7:** Relational Database Table: `job_skill` (Many-to-Many Pivot Mapping)
- **Table 2.8:** Relational Database Table: `applications` (Candidate Job Submissions)
- **Table 2.9:** Relational Database Table: `saved_jobs` (Candidate Bookmarks)
- **Table 3.1:** Laravel Backend Controller Architecture and Functional Mapping
- **Table 3.2:** Frontend Vue 3 Single Page Application (SPA) Views Mapping
- **Table 3.3:** Key RESTful API Endpoints Specification
- **Table 4.1:** Automated PHPUnit Test Suites, Test Cases, and Assertions Summary

---

# LIST OF FIGURES

- **Figure 1.1:** Overall System Use Case Diagram (Candidate, Recruiter, Admin, Guest)
- **Figure 2.1:** Decoupled Client-Server Architectural Block Diagram
- **Figure 2.2:** Entity Relationship Diagram (ERD) of JobConnect Relational Database
- **Figure 2.3:** Candidate Application Submission & Duplicate Check Flowchart
- **Figure 2.4:** Recruiter Applicant Tracking System (ATS) Pipeline State Machine
- **Figure 3.1:** Public Discovery Homepage with Hero Search & Metric Strips (`/`)
- **Figure 3.2:** Filterable Job Listings with Multi-Parameter Query Engine (`/jobs`)
- **Figure 3.3:** Comprehensive Job Detail View with Sticky Application Widget (`/jobs/:slug`)
- **Figure 3.4:** Split-Screen Authentication Interface (`/login` & `/register`)
- **Figure 3.5:** Candidate Profile Editor and PDF Resume Viewer (`/candidate/profile`)
- **Figure 3.6:** Candidate Application Tracking Center (`/candidate/applications`)
- **Figure 3.7:** Recruiter ATS Dashboard and Hiring Metrics (`/recruiter/dashboard`)
- **Figure 3.8:** Recruiter Candidate Review Panel with In-Browser PDF Preview (`/recruiter/jobs/:id/applicants`)
- **Figure 3.9:** Recruiter Job Posting Management Modal (`/recruiter/jobs`)
- **Figure 3.10:** Administrator Master Control Center (`/admin/dashboard`)
- **Figure 4.1:** Terminal Console Execution of Automated Test Suite (62 Passed, 127 Assertions)

\newpage

---

# ABSTRACT

### English Abstract
In the modern tech-driven labor market, conventional recruitment processes face substantial friction, including decentralized resume distribution via email, disorganized spreadsheet tracking, high candidate drop-off rates, and the lack of structured communication between employers and job seekers. To address these industry bottlenecks, this Specialized IT Project presents **JobConnect**, a high-performance, enterprise-grade Online Recruitment Platform and Applicant Tracking System (ATS). 

The platform is designed following a modern **Decoupled Client-Server Architecture**: the Backend is engineered with **Laravel 11.x (PHP 8.2+)** operating as a stateless RESTful API, secured by **Laravel Sanctum** token-based authentication and custom Role-Based Access Control (**RoleMiddleware**); the Frontend is implemented as a responsive Single Page Application (SPA) utilizing **Vue.js 3 (Composition API)**, **Vite**, **Pinia**, **Vue Router**, and **Bootstrap 5.3**. The database tier is built on **MySQL 8.0** standardized to the Third Normal Form (3NF) across 9 interrelated entities.

JobConnect delivers 3 dedicated functional subsystems: (1) **Candidate Portal**, empowering job seekers to explore verified job openings through a multi-parameter filtering engine, manage professional profiles with automated PDF resume uploads, submit job applications with duplicate prevention, and track real-time hiring progress; (2) **Recruiter ATS Workspace**, enabling employers to manage company branding, publish skill-tagged job listings, review candidates through an interactive recruitment pipeline (*Pending $\rightarrow$ Reviewed $\rightarrow$ Interview $\rightarrow$ Offered / Rejected*), inspect PDF resumes in-browser, and append internal recruitment notes; (3) **Administrator Management Module**, providing platform-wide governance over user accounts (ban/unban capabilities), job listing moderation, company verification, and master data curation.

To guarantee industrial software reliability and security against vulnerabilities such as Insecure Direct Object References (IDOR) and unauthorized role escalation, the system is backed by a comprehensive automated test suite consisting of **62 PHPUnit Feature and Unit test cases** encompassing **127 assertions**, achieving a **100% PASS rate**. The system has been fully containerized and deployed into production cloud infrastructure via **Vercel** and **Railway**.

*Keywords:* Recruitment Platform, Applicant Tracking System (ATS), Laravel 11, Vue.js 3, RESTful API, Single Page Application, PHPUnit Automated Testing.

<br>

### Tóm lược tiếng Việt
Trong thị trường lao động công nghệ hiện đại, quy trình tuyển dụng truyền thống gặp nhiều trở ngại như tiếp nhận hồ sơ qua email rời rạc, quản lý ứng viên thủ công trên các tập tin bảng tính, tỷ lệ thất thoát hồ sơ cao và thiếu kênh phản hồi minh bạch giữa doanh nghiệp và ứng viên. Nhằm giải quyết triệt để các hạn chế trên, đề tài Niên luận chuyên ngành ngành Công nghệ Thông tin đã nghiên cứu, thiết kế và phát triển **JobConnect** – Hệ thống Tuyển dụng và Quản lý Ứng viên trực tuyến (Applicant Tracking System - ATS) hiện đại.

Hệ thống được xây dựng theo kiến trúc phân tách độc lập **Client-Server (Decoupled Architecture)**: Phía Backend đóng vai trò RESTful API phi trạng thái phát triển trên nền tảng **Laravel 11.x (PHP 8.2+)**, xác thực bằng **Laravel Sanctum Bearer Token** và phân quyền truy cập thông qua **RoleMiddleware** tùy biến; Phía Frontend là ứng dụng Single Page Application (SPA) xây dựng trên **Vue.js 3 (Composition API)**, **Vite**, **Pinia** và **Bootstrap 5.3**. Cơ sở dữ liệu quan hệ **MySQL 8.0** được thiết kế chuẩn hóa 3NF gồm 9 thực thể liên kết chặt chẽ.

JobConnect cung cấp 3 phân hệ chuyên biệt: (1) **Phân hệ Ứng viên (Candidate Portal)** hỗ trợ tìm kiếm việc làm đa tiêu chí, quản lý hồ sơ chuyên nghiệp kèm tải lên CV định dạng PDF, ứng tuyển trực tuyến với cơ chế chống nộp trùng và theo dõi tiến trình tuyển dụng theo thời gian thực; (2) **Phân hệ Nhà tuyển dụng (Recruiter ATS Workspace)** cho phép quản lý thông tin doanh nghiệp, đăng tin tuyển dụng gắn thẻ kỹ năng, quản lý phễu ứng viên theo quy trình (*Chờ duyệt $\rightarrow$ Đã xem $\rightarrow$ Phỏng vấn $\rightarrow$ Mời nhận việc / Từ chối*), xem trước CV PDF trực tiếp trên trình duyệt và lưu trữ ghi chú phỏng vấn; (3) **Phân hệ Quản trị viên (Admin Dashboard)** kiểm soát người dùng (khóa/mở khóa), kiểm duyệt tin đăng, xác thực doanh nghiệp và quản lý danh mục dữ liệu.

Chất lượng và độ an toàn của hệ thống được bảo đảm thông qua bộ kiểm thử tự động gồm **62 Test Cases** với **127 Assertions** trên nền tảng **PHPUnit**, đạt tỉ lệ **PASS 100%**, ngăn chặn triệt để các lỗi phân quyền IDOR và rò rỉ dữ liệu. Ứng dụng đã được triển khai chạy thực tế trên hạ tầng đám mây Vercel và Railway.

\newpage

---

# INTRODUCTION

## 1. Problem Statement
The recruitment landscape has witnessed a rapid transformation over the past decade, driven by digital globalization, remote work models, and the exponential growth of tech-oriented job roles. Despite this growth, recruitment processes in many small-to-medium enterprises (SMEs) and startup organizations still rely heavily on fragmented communication channels such as generic email inboxes, messaging applications, and shared spreadsheets.

This traditional approach introduces critical pain points:
- **Application Overload and Lost Resumes:** Recruiters receive hundreds of unorganized resumes in diverse formats without centralized storage, leading to missed talent and delayed hiring cycles.
- **Lack of Pipeline Transparency for Candidates:** Applicants often submit resumes into an "information black hole," receiving zero feedback regarding whether their application was viewed, shortlisted, or rejected.
- **Security and Compliance Risks:** Unrestricted dissemination of personal candidate data (phone numbers, addresses, CVs) through unsecured emails exposes organizations to privacy breaches.
- **Absence of Dedicated ATS for SMEs:** Enterprise-grade Applicant Tracking Systems (e.g., Workday, Greenhouse) carry exorbitant subscription costs and excessive configuration overhead, making them inaccessible to local businesses.

Recognizing these industry challenges, the project **"JobConnect: Modern Online Recruitment Platform and Applicant Tracking System (ATS)"** was conceptualized and developed. The system aims to bridge the gap between job seekers and hiring teams by providing a centralized, transparent, high-performance, and cost-effective digital hiring platform.

---

## 2. Related Works
A comparative analysis of existing recruitment solutions highlights several distinct categories:
- **Global & National Commercial Job Boards (e.g., LinkedIn, TopCV, VietnamWorks):** These platforms feature immense traffic and broad candidate reach. However, their internal applicant management features for employers are either restricted behind enterprise paywalls or detached from customizable recruitment pipelines.
- **Enterprise Applicant Tracking Systems (e.g., Workday, Greenhouse, Lever):** Designed for multinational corporations, these systems offer sophisticated talent workflows but suffer from steep learning curves, rigid integration requirements, and prohibitive licensing fees.
- **Prior University Student Projects:** Many previous capstone projects in this domain were constrained to monolithic architectures (Server-Side Rendering using basic Blade templates), relying on basic CRUD operations across disconnected tables. They frequently lacked automated testing, in-browser PDF manipulation, centralized state management, and robust role-based access control.

JobConnect synthesizes the best characteristics of these systems by combining an intuitive public job discovery portal with an integrated, agile ATS candidate pipeline, delivered through a modern decoupled client-server architecture with 100% automated test coverage.

---

## 3. Project Objectives
The primary objectives of this specialized project include:
1. **Architectural Separation:** Design and implement a modern decoupled client-server software architecture separating the presentation layer (Vue 3 SPA) from the application and data layer (Laravel 11 RESTful API).
2. **End-to-End Recruitment Digitization:** Digitize the entire recruitment lifecycle: from candidate job discovery, profile curation, and resume upload to recruiter job posting, applicant pipeline management, and status updates.
3. **In-Browser Resume Review:** Streamline candidate screening by providing direct in-browser PDF resume inspection alongside recruiter evaluation notes.
4. **Data Integrity and Duplicate Application Prevention:** Guarantee relational data integrity through foreign key constraints, cascading policies, and validation algorithms that strictly prevent duplicate candidate submissions for the same position.
5. **Robust Security and RBAC:** Secure all RESTful endpoints using token-based authentication (Laravel Sanctum) and custom role-based middleware (`RoleMiddleware`), thwarting unauthorized access and IDOR vulnerabilities.
6. **Automated Quality Assurance:** Establish automated testing standards using PHPUnit, ensuring all business logic, security policies, and edge cases pass verification with zero runtime regressions.

---

## 4. Research Scope and Target Audience
- **Research Scope:** Specialized full-stack web engineering, RESTful API design, token authentication, relational database modeling (3NF), automated testing methodologies, and reactive frontend component design.
- **Target Audience:**
  - **Candidates:** College graduates, software engineers, and professionals seeking employment opportunities.
  - **Recruiters / Employers:** Talent acquisition specialists, HR managers, and corporate employers looking to post vacancies and manage candidate pipelines.
  - **Platform Administrators:** System operators responsible for verifying employers, approving job postings, moderating users, and managing taxonomies.

---

## 5. Research Methodology
The project adopted the following engineering methodologies:
- **Agile / Iterative Development:** Sprint-based implementation cycles where models, APIs, and UI components were developed iteratively, accompanied by unit and feature tests.
- **Object-Oriented Design & Domain Modeling:** Crafting Entity Relationship Diagrams (ERD) and Eloquent ORM models with strict relationship definitions (One-to-One, One-to-Many, Many-to-Many).
- **Decoupled RESTful API Principles:** Developing stateless API endpoints returning standardized JSON payloads: `{ "success": boolean, "message": string, "data": object, "errors": object }`.
- **Test-Driven & Automated Verification:** Writing comprehensive PHPUnit feature tests to validate authentication, candidate workflows, recruiter ATS pipelines, and admin moderation before deploying code.

---

## 6. Research Content and Workload Schedule
The project workload was distributed over a 14-week schedule as summarized in Table 1.1:

| Week | Work Breakdown / Milestone | Person in Charge | Deliverables / Artifacts |
| :---: | :--- | :---: | :--- |
| **W1 - W2** | Requirement elicitation, market research, and SRS specification | Student | System Requirements Specification (SRS) & Blueprint |
| **W3 - W4** | Relational Database Design (ERD, 3NF), Laravel & Vue scaffolding | Student | Database Migrations, Seeders, Project Monorepo |
| **W5 - W6** | Backend RESTful API development: Auth (Sanctum) & Public Endpoints | Student | AuthController, PublicJobController, MasterDataController |
| **W7 - W8** | Candidate Portal: Profile, PDF Upload, Application & Saved Jobs | Student | CandidateProfileController, CandidateApplicationController, Vue Views |
| **W9 - W10**| Recruiter ATS Module: Job CRUD, Applicant Funnel & PDF Review | Student | RecruiterJobController, RecruiterApplicationController, ATS Views |
| **W11** | Admin Module: User moderation, Job approval, Category/Skill CRUD | Student | Admin Controllers, Admin Dashboard View |
| **W12** | Security hardening, RoleMiddleware, CORS, and PHPUnit Testing | Student | 62 PHPUnit Test Cases (127 Assertions), 100% PASS |
| **W13** | UI/UX visual polish, responsive design optimization, cloud deployment | Student | Vercel (Frontend) & Railway (Backend) live production |
| **W14** | Thesis documentation, presentation slides, and user manual writing | Student | Final Project Report (.md / .docx) & Presentation |

*Table 1.1: 14-Week Project Implementation Schedule and Work Breakdown*

---

## 7. Report Organization
This report is structured into the following sections:
- **Chapter 1: Requirements Specification:** Analyzes system actors, role permissions, detailed functional use cases, and non-functional requirements.
- **Chapter 2: System Design:** Details the decoupled architectural design, Eloquent data models, 3NF database schema, and core business workflow algorithms.
- **Chapter 3: System Implementation:** Elaborates on the technology stack, backend controller architecture, reactive frontend views, security middleware, and RESTful API endpoints.
- **Chapter 4: Testing and Evaluation:** Discusses testing objectives, automated test suite composition, test scenarios, and PHPUnit execution metrics.
- **Conclusion & References:** Summarizes achievements, identifies limitations, outlines future extensions, and lists academic references.
- **Appendix:** Provides step-by-step environment setup instructions and user manuals with demo credentials.

---

## 8. Source Code Repository
The entire source code, database migrations, seeders, automated test suites, and frontend application are maintained on GitHub:
- **Repository URL:** [https://github.com/KhHuyy/JobPortal.git](https://github.com/KhHuyy/JobPortal.git)
- **Branch:** `main`

\newpage

---

# MAIN CONTENT

# CHAPTER 1: REQUIREMENTS SPECIFICATION

## 1.1. System Actors and Permission Matrix
JobConnect defines four primary user roles, each possessing distinct operational privileges across the platform:
1. **Guest (Unauthenticated User):** Public visitors browsing the platform.
2. **Candidate (Job Seeker):** Registered users seeking employment and managing their applications.
3. **Recruiter (Employer):** Corporate representatives posting job openings and reviewing applicants.
4. **Administrator (Admin):** System supervisors managing platform operations, data integrity, and compliance.

The permission matrix governing these roles is formalized in Table 1.2:

| Functional Capability | Guest | Candidate | Recruiter | Administrator |
| :--- | :---: | :---: | :---: | :---: |
| Browse homepage, search and filter active job postings | $\checkmark$ | $\checkmark$ | $\checkmark$ | $\checkmark$ |
| View detailed job requirements and verified company profiles | $\checkmark$ | $\checkmark$ | $\checkmark$ | $\checkmark$ |
| Register an account (`candidate` or `recruiter` role) | $\checkmark$ | – | – | – |
| Authenticate (Login / Logout / View Auth Profile) | $\checkmark$ | $\checkmark$ | $\checkmark$ | $\checkmark$ |
| Manage personal profile and upload PDF resume | – | $\checkmark$ | – | – |
| Submit job applications with cover letter & resume | – | $\checkmark$ | – | – |
| Bookmark / Save favorite job postings | – | $\checkmark$ | – | – |
| Track submitted application statuses in real time | – | $\checkmark$ | – | – |
| Manage corporate profile, location, and company logo | – | – | $\checkmark$ | – |
| Create, update, close, and delete job postings | – | – | $\checkmark$ | – |
| View candidate applicants and preview PDF resumes in-browser | – | – | $\checkmark$ | – |
| Update application pipeline status (*Reviewed, Interview, etc.*) | – | – | $\checkmark$ | – |
| Access Recruiter hiring KPI analytics and dashboards | – | – | $\checkmark$ | – |
| Approve or reject pending job listings | – | – | – | $\checkmark$ |
| Moderate user accounts (Ban / Unban) | – | – | – | $\checkmark$ |
| Manage master data (Job Categories & Technical Skills) | – | – | – | $\checkmark$ |
| Access platform-wide KPI analytics and metrics | – | – | – | $\checkmark$ |

*Table 1.2: Role-Based Access Control (RBAC) Permission Matrix*

---

## 1.2. Functional Requirements (Use Case Specification)
The functional scope of JobConnect encompasses 16 comprehensive use cases as described in Table 1.3:

| Use Case ID | Use Case Name | Primary Actor | Functional Description |
| :---: | :--- | :---: | :--- |
| **UC01** | User Registration | Guest | Create a new user account with role selection (`candidate` or `recruiter`). Auto-initializes candidate profile or employer record. |
| **UC02** | User Authentication | Guest / All | Authenticate via email and password; verify active status; issue Laravel Sanctum Bearer token. |
| **UC03** | User Logout | Authenticated | Revoke the active personal access token, terminate session, and purge client-side credentials. |
| **UC04** | Job Search & Multi-Filter | All Users | Search job postings by keyword, category, job type, location, minimum salary, and required skills. |
| **UC05** | View Job Details | All Users | Display full job description, qualifications, compensation, company background, and increment view counter. |
| **UC06** | View Verified Companies | All Users | Browse verified employer directory; view company culture, headquarters, and active vacancies. |
| **UC07** | Candidate Profile Curation | Candidate | Manage bio, contact details, education, experience years, skills summary, and avatar. |
| **UC08** | PDF Resume Upload | Candidate | Upload and replace default PDF resume (validated for `.pdf` MIME type and 5MB maximum size). |
| **UC09** | Job Application Submission | Candidate | Submit application to an active job using default or custom PDF CV and cover letter. Enforce duplicate check. |
| **UC10** | Application Tracking | Candidate | View real-time status of all submitted applications (*Pending, Reviewed, Interview, Offered, Rejected*) and HR notes. |
| **UC11** | Save / Bookmark Jobs | Candidate | Toggle bookmark status for job postings to curate a personalized list of prospective roles. |
| **UC12** | Employer Profile Management | Recruiter | Configure company name, business tax code, corporate website, size, headquarters, overview, and logo. |
| **UC13** | Job Posting Management | Recruiter | Author, update, close, or delete job listings with skill tags, compensation ranges, vacancies, and deadlines. |
| **UC14** | ATS Candidate Evaluation | Recruiter | Inspect candidates per job, preview submitted PDF resumes in-browser, record interview notes, and update pipeline status. |
| **UC15** | Admin Job Moderation | Admin | Review recruiter-submitted job postings; approve to `active` or mark as `rejected`. |
| **UC16** | User & Master Data Control | Admin | Ban/unban compromised accounts; create, edit, or delete categories and technical skills; monitor platform stats. |

*Table 1.3: Comprehensive System Use Case Specification (16 Use Cases)*

---

## 1.3. Non-Functional Requirements
1. **Security & Data Protection:**
   - **Password Encryption:** 100% of passwords are cryptographic hashes generated using the **Bcrypt** algorithm (`BCRYPT_ROUNDS=12`).
   - **Token Authentication:** Secure API access via stateless **Laravel Sanctum Bearer Tokens** stored in client `localStorage`.
   - **Cross-Origin Resource Sharing (CORS):** Strict CORS domain whitelisting configured in `config/cors.php`, permitting requests exclusively from authorized frontend origins.
   - **SQL Injection Prevention:** 100% parameterized queries executed through Laravel Eloquent ORM.
   - **Account Isolation:** Banned users (`status = 'banned'`) are immediately blocked by authentication controllers and middleware, revoking active tokens.
2. **Data Integrity:**
   - All relational links enforce foreign keys with `CASCADE` on child entities (`applications`, `saved_jobs`, `job_skill`), ensuring zero orphaned records upon parent deletion.
3. **Performance and Responsiveness:**
   - Lightweight SPA bundle generated by Vite, optimizing CSS/JS chunks for sub-second page transitions.
   - Paginated API endpoints (`paginate(10)`) preventing excessive payload sizes on high-volume queries.
   - Responsive user interface built on Bootstrap 5.3, delivering optimal usability across mobile, tablet, and desktop viewports.

\newpage

---

# CHAPTER 2: SYSTEM DESIGN

## 2.1. Overall System Architecture
JobConnect is engineered using a **Decoupled Client-Server Architecture** (Figure 2.1). The presentation tier operates completely independently of the application logic and data tiers:

```
+-------------------------------------------------------------------------+
|                         CLIENT PRESENTATION TIER                        |
|                                                                         |
|   +-----------------------------------------------------------------+   |
|   |                  Vue.js 3 Single Page Application               |   |
|   |  - Vue Router (Auth & Role Guards)                              |   |
|   |  - Pinia Stores (authStore, masterStore)                        |   |
|   |  - Centralized Axios HTTP Client (Interceptors for Bearer Token)|   |
|   |  - Responsive UI Components (Bootstrap 5.3 + Custom Styling)    |   |
|   +-----------------------------------------------------------------+   |
+------------------------------------^------------------------------------+
                                     |
                          JSON over HTTPS (RESTful API)
                                     |
+------------------------------------v------------------------------------+
|                         SERVER APPLICATION TIER                         |
|                                                                         |
|   +-----------------------------------------------------------------+   |
|   |                      Laravel 11.x RESTful API                   |   |
|   |  - Routing & URL Dispatcher (`routes/api.php`)                  |   |
|   |  - Middleware Pipeline: CORS -> Sanctum Auth -> RoleMiddleware  |   |
|   |  - Controllers: Auth, Public, Candidate, Recruiter, Admin       |   |
|   |  - Form Requests & Input Validation Rules                       |   |
|   |  - Eloquent ORM Models & Relationships                          |   |
|   +-----------------------------------------------------------------+   |
+------------------------------------^------------------------------------+
                                     |
                            Database Queries (TCP/IP)
                                     |
+------------------------------------v------------------------------------+
|                            DATA STORAGE TIER                            |
|                                                                         |
|   +-----------------------------------------------------------------+   |
|   |                     MySQL 8.0 Relational DBMS                   |   |
|   |  - Normalized 3NF Relational Tables                             |   |
|   |  - Foreign Key Constraints & Cascading Triggers                 |   |
|   +-----------------------------------------------------------------+   |
|   +-----------------------------------------------------------------+   |
|   |                     Filesystem Storage                          |   |
|   |  - `storage/app/public/resumes/*.pdf`                           |   |
|   |  - `storage/app/public/logos/*.png, *.jpg`                      |   |
|   +-----------------------------------------------------------------+   |
+-------------------------------------------------------------------------+
```
*Figure 2.1: Decoupled Client-Server Architectural Block Diagram*

---

## 2.2. Component and Data Model Description
The backend business logic is encapsulated within 9 Eloquent Models:
1. **`User`:** The central authentication identity. Contains credential hashes, contact information, role flag (`admin`, `recruiter`, `candidate`), and account status (`active`, `banned`).
2. **`CandidateProfile`:** Linked 1-to-1 with `User`. Stores career summary, date of birth, gender, education history, years of experience, and path to default PDF resume.
3. **`Company`:** Linked 1-to-1 with recruiter `User`. Stores company name, corporate slug, business tax code, website, headcount size, headquarters address, and verification status (`is_verified`).
4. **`Category`:** Represents industry categorizations (e.g., Software Engineering, Data Science).
5. **`Skill`:** Represents technical competencies and programming tools (e.g., PHP, Laravel, Vue.js, Docker).
6. **`Job`:** Represents published employment vacancies, detailing title, slug, job type, experience level, salary range, location, requirements, benefits, application deadline, and status (`pending`, `active`, `closed`, `rejected`).
7. **`JobSkill` (Pivot):** Manages the Many-to-Many relationship between `Job` and `Skill`.
8. **`Application`:** Manages candidate job submissions. Links a `Candidate` to a `Job`, storing the submitted PDF resume path, cover letter, ATS hiring status (`pending`, `reviewed`, `interview`, `offered`, `rejected`), and recruiter review notes.
9. **`SavedJob`:** Stores bookmarked job postings for candidate accounts with a unique composite key on `(user_id, job_id)`.

---

## 2.3. Relational Database Schema Design (3NF)
The relational database was designed in accordance with the Third Normal Form (3NF) to eliminate data redundancy and preserve integrity. The 9 tables are specified below:

### Table 2.1: `users`
| Column | Data Type | Constraints / Default | Description |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK, Auto Increment | Unique account identifier |
| `name` | VARCHAR(255) | NOT NULL | User's full name |
| `email` | VARCHAR(255) | UNIQUE, NOT NULL | Account login email |
| `password` | VARCHAR(255) | NOT NULL | Bcrypt password hash |
| `role` | ENUM | DEFAULT 'candidate' | Enum: `'admin'`, `'recruiter'`, `'candidate'` |
| `phone` | VARCHAR(20) | NULLABLE | Contact telephone |
| `avatar` | VARCHAR(255) | NULLABLE | Relative path to uploaded avatar image |
| `status` | ENUM | DEFAULT 'active' | Account status: `'active'`, `'banned'` |
| `created_at` / `updated_at` | TIMESTAMP | NULLABLE | Audit timestamps |

### Table 2.2: `candidate_profiles`
| Column | Data Type | Constraints / Default | Description |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK, Auto Increment | Profile identifier |
| `user_id` | BIGINT UNSIGNED | FK -> users.id (CASCADE) | 1-to-1 link to user account |
| `dob` | DATE | NULLABLE | Date of birth |
| `gender` | ENUM | NULLABLE | Enum: `'male'`, `'female'`, `'other'` |
| `address` | VARCHAR(255) | NULLABLE | Permanent address |
| `bio` | TEXT | NULLABLE | Professional summary / biography |
| `education` | TEXT | NULLABLE | Academic credentials and degree info |
| `experience_years` | INT | DEFAULT 0 | Cumulative years of work experience |
| `skills_text` | VARCHAR(500) | NULLABLE | Comma-separated summary of skills |
| `default_cv_path`| VARCHAR(255) | NULLABLE | Relative path to default PDF CV |
| `created_at` / `updated_at` | TIMESTAMP | NULLABLE | Audit timestamps |

### Table 2.3: `companies`
| Column | Data Type | Constraints / Default | Description |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK, Auto Increment | Company record identifier |
| `user_id` | BIGINT UNSIGNED | FK -> users.id (CASCADE) | Recruiter user owning this organization |
| `name` | VARCHAR(255) | NOT NULL | Registered company legal name |
| `slug` | VARCHAR(255) | UNIQUE, NOT NULL | URL-safe slug |
| `tax_code` | VARCHAR(50) | NULLABLE | Tax registration number |
| `logo` | VARCHAR(255) | NULLABLE | Relative path to company logo image |
| `website` | VARCHAR(255) | NULLABLE | Corporate URL |
| `company_size` | VARCHAR(100) | NULLABLE | Headcount tier (e.g., '100-500 employees') |
| `address` | VARCHAR(255) | NOT NULL | Office street address |
| `city` | VARCHAR(100) | NOT NULL | City location |
| `description` | LONGTEXT | NULLABLE | Company mission and culture |
| `is_verified` | BOOLEAN | DEFAULT FALSE | Administrator verification badge |
| `created_at` / `updated_at` | TIMESTAMP | NULLABLE | Audit timestamps |

### Table 2.4 & 2.5: `categories` & `skills`
- **`categories`:** `id` (PK), `name` (VARCHAR 255), `slug` (VARCHAR 255, UNIQUE), `icon` (VARCHAR 100, NULLABLE), `status` (BOOLEAN, DEFAULT true), `created_at`, `updated_at`.
- **`skills`:** `id` (PK), `name` (VARCHAR 100), `slug` (VARCHAR 100, UNIQUE), `created_at`, `updated_at`.

### Table 2.6: `jobs`
| Column | Data Type | Constraints / Default | Description |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK, Auto Increment | Unique job listing ID |
| `company_id` | BIGINT UNSIGNED | FK -> companies.id (CASCADE) | Hiring organization |
| `category_id` | BIGINT UNSIGNED | FK -> categories.id | Associated industry sector |
| `title` | VARCHAR(255) | NOT NULL | Position title |
| `slug` | VARCHAR(255) | UNIQUE, NOT NULL | SEO-friendly URL slug |
| `job_type` | ENUM | DEFAULT 'full_time' | Enum: `'full_time'`, `'part_time'`, `'remote'`, `'internship'` |
| `experience_level` | VARCHAR(100) | NOT NULL | Required experience seniority |
| `salary_min` / `max` | DECIMAL(12,2) | NULLABLE | Salary compensation range (USD) |
| `is_negotiable` | BOOLEAN | DEFAULT FALSE | Salary negotiable flag |
| `location` | VARCHAR(100) | NOT NULL | City / Region |
| `work_address` | VARCHAR(255) | NOT NULL | Worksite street address |
| `vacancies` | INT | DEFAULT 1 | Open seats to hire |
| `description` | LONGTEXT | NOT NULL | Detailed duties and expectations |
| `requirements` | LONGTEXT | NOT NULL | Candidate qualifications |
| `benefits` | LONGTEXT | NOT NULL | Compensation perks and insurance |
| `deadline` | DATE | NOT NULL | Application cutoff date |
| `status` | ENUM | DEFAULT 'pending' | Enum: `'pending'`, `'active'`, `'closed'`, `'rejected'` |
| `views_count` | INT | DEFAULT 0 | Traffic metrics counter |
| `created_at` / `updated_at` | TIMESTAMP | NULLABLE | Audit timestamps |

### Table 2.7, 2.8 & 2.9: `job_skill`, `applications` & `saved_jobs`
- **`job_skill`:** `id` (PK), `job_id` (FK -> `jobs.id` CASCADE), `skill_id` (FK -> `skills.id` CASCADE).
- **`applications`:** `id` (PK), `job_id` (FK -> `jobs.id` CASCADE), `candidate_id` (FK -> `users.id` CASCADE), `cv_file_path` (VARCHAR 255), `cover_letter` (TEXT, NULLABLE), `status` (ENUM: `'pending'`, `'reviewed'`, `'interview'`, `'offered'`, `'rejected'`, DEFAULT `'pending'`), `recruiter_note` (TEXT, NULLABLE), `created_at`, `updated_at`.
- **`saved_jobs`:** `id` (PK), `user_id` (FK -> `users.id` CASCADE), `job_id` (FK -> `jobs.id` CASCADE), `created_at`. Unique index on `(user_id, job_id)`.

---

## 2.4. Core Business Workflows and Algorithms

### 2.4.1. Job Application Submission & Duplicate Prevention Algorithm
When a candidate requests to apply for a job posting:
1. Verify candidate authentication and role (`candidate`).
2. Fetch the target `Job` instance. Verify that `job.status === 'active'` and `job.deadline >= current_date()`. If invalid, abort with HTTP 400.
3. Execute duplicate submission check:
   $$\text{ExistingApplication} = \text{Application::where}('job\_id', job.id)\rightarrow\text{where}('candidate\_id', user.id)\rightarrow\text{first}()$$
   If $\text{ExistingApplication} \neq \emptyset$, return HTTP 422: *"You have already applied for this job."*
4. Process resume payload:
   - If a new file is uploaded, validate MIME type (`application/pdf`) and file size ($\le 5\text{MB}$). Store file at `storage/app/public/resumes/`.
   - If using existing profile CV, verify that `candidate_profile.default_cv_path` exists on disk.
5. Create new `Application` record with `status = 'pending'`. Return standardized HTTP 201 JSON response.

### 2.4.2. Recruiter ATS Pipeline State Machine
The recruitment pipeline models a deterministic state machine transitioning across five status values:
$$\text{Pending} \xrightarrow{\text{Recruiter Screen}} \text{Reviewed} \xrightarrow{\text{Schedule Call}} \text{Interview} \xrightarrow{\text{Final Decision}} \{\text{Offered}, \text{Rejected}\}$$
- Recruiters may append internal evaluation notes at any stage.
- Candidates receive instant status updates in their personal dashboard without exposing recruiter-only private evaluations.

\newpage

---

# CHAPTER 3: SYSTEM IMPLEMENTATION

## 3.1. Development Environment and Technology Stack
The development ecosystem was configured with industry-standard tooling:
- **Backend Framework:** Laravel 11.x on PHP 8.2 (Running under XAMPP / Apache 2.4).
- **Authentication:** Laravel Sanctum issuing sha256 personal access tokens.
- **Frontend Framework:** Vue.js 3 (Composition API, `<script setup>`), bundled by Vite 8.x.
- **State Management & Routing:** Pinia 2.x for client state; Vue Router 4.x with navigation guards.
- **Styling:** Bootstrap 5.3 combined with custom CSS design tokens (Electric Indigo gradient `#4f46e5` $\rightarrow$ `#7c3aed`, Emerald `#10b981`, Slate `#0f172a`).
- **Database Server:** MySQL 8.0 with InnoDB storage engine.

The architectural separation of responsibilities across controllers is detailed in Table 3.1:

| Controller Name | Namespace | Core Responsibilities |
| :--- | :--- | :--- |
| `AuthController` | `App\Http\Controllers\Api` | User registration, login token issuance, logout token revocation, `/me` profile retrieval. |
| `PublicJobController` | `App\Http\Controllers\Api` | Public job search, dynamic multi-filtering, job detail resolution, related jobs matching. |
| `PublicCompanyController` | `App\Http\Controllers\Api` | Verified employers directory, company profiles, and active open vacancies. |
| `MasterDataController` | `App\Http\Controllers\Api` | Metadata provision for categories, skill tags, and location lists. |
| `CandidateProfileController` | `App\Http\Controllers\Api` | Candidate profile editing, avatar storage, and default PDF CV upload. |
| `CandidateApplicationController`| `App\Http\Controllers\Api` | Job application submission, duplicate prevention, application tracking, saved jobs toggle. |
| `RecruiterCompanyController` | `App\Http\Controllers\Api` | Corporate profile customization and logo upload. |
| `RecruiterJobController` | `App\Http\Controllers\Api` | Recruiter job CRUD, skill synchronization in `job_skill`, job status toggles. |
| `RecruiterApplicationController`| `App\Http\Controllers\Api` | Candidate applicant listing, PDF resume inspection, status transition, recruiter notes. |
| `AdminUserController` | `App\Http\Controllers\Api` | User directory management, account status moderation (ban/unban toggle). |
| `AdminJobController` | `App\Http\Controllers\Api` | System-wide job listings inspection, approval (`active`) and rejection (`rejected`). |
| `AdminCategoryController` | `App\Http\Controllers\Api` | Full CRUD operations on industry job categories. |
| `AdminSkillController` | `App\Http\Controllers\Api` | Full CRUD operations on technical skill tags. |
| `AdminDashboardController` | `App\Http\Controllers\Api` | Aggregation of platform metrics (candidates, recruiters, jobs, applications). |

*Table 3.1: Laravel Backend Controller Architecture and Functional Mapping*

---

## 3.2. Subsystems and User Interface Implementation

### 3.2.1. Authentication Subsystem (`LoginView.vue` & `RegisterView.vue`)
The authentication interfaces were designed following a SaaS split-screen layout:
- **Left Showcase Panel (45% width):** Features deep slate styling (`#0f172a`), brand logo, and three value propositions: Structured Candidate Pipeline, Verified Employer Workspaces, and Instant PDF Resume Review.
- **Right Action Panel (55% width):** Minimalist white form with clean single-border input elements and role toggling between "Job Seeker / Candidate" and "Employer / Recruiter".
- **Network Resiliency:** Enhanced Axios error interceptors distinguish connection failures (`ERR_NETWORK`) from invalid credential errors (`401 Unauthorized`).

### 3.2.2. Candidate Portal
- **Profile & Resume Management (`CandidateProfileView.vue`):** Allows candidates to update their credentials, specify years of experience, and upload a `.pdf` resume with instant preview capabilities.
- **Application Tracker (`MyApplicationsView.vue`):** Features color-coded badges matching hiring stages: *Pending* (Yellow), *Reviewed* (Blue), *Interview* (Indigo), *Offered* (Green), and *Rejected* (Red).

### 3.2.3. Recruiter ATS Workspace
- **Dashboard (`RecruiterDashboardView.vue`):** Displays KPI cards for Total Jobs, Active Positions, Total Applicants, and Candidates Pending Review.
- **Applicant Review Center (`JobApplicantsView.vue`):** Displays all candidate submissions for a given job. Recruiters can view applicant contact details, inspect cover letters, click to preview PDF resumes in a new tab, and open an interactive modal to record interview notes and advance application status.

### 3.2.4. Administrator Dashboard (`AdminDashboardView.vue`)
Presents a tabbed command center providing real-time platform statistics, moderation controls to ban/unban abusive accounts, a moderation queue to approve pending job listings, and CRUD management tools for categories and skills.

---

## 3.3. Security Architecture & RoleMiddleware Implementation
To enforce strict Role-Based Access Control, a custom middleware was registered at `app/Http/Middleware/RoleMiddleware.php`:

```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        if ($user->status === 'banned') {
            $user->tokens()->delete();
            return response()->json([
                'success' => false,
                'message' => 'Your account has been suspended. Please contact support.'
            ], 403);
        }

        if (!in_array($user->role, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: You do not have permission to access this resource.'
            ], 403);
        }

        return $next($request);
    }
}
```

This middleware guarantees that:
1. Unauthenticated requests are rejected immediately with HTTP 401.
2. Suspended accounts have their tokens instantly purged and receive HTTP 403.
3. Cross-role tampering (e.g., a candidate attempting to access recruiter ATS endpoints) is strictly prohibited with HTTP 403.

---

## 3.4. RESTful API Endpoints Specification
All API endpoints are documented with standardized JSON responses. A representative subset of core endpoints is summarized in Table 3.3:

| HTTP Method | Endpoint URI | Access Control | Request Payload / Description |
| :--- | :--- | :---: | :--- |
| `POST` | `/api/auth/register` | Public | `{ name, email, password, password_confirmation, role }` |
| `POST` | `/api/auth/login` | Public | `{ email, password }` $\rightarrow$ Returns Bearer Token + User Object |
| `GET` | `/api/jobs` | Public | Query: `keyword, category_id, job_type, location, salary_min, skill_id` |
| `GET` | `/api/jobs/{slug}` | Public | Resolves job details, increments views counter, returns related jobs |
| `GET` | `/api/candidate/profile` | `candidate` | Fetches authenticated candidate profile, education, and resume path |
| `POST` | `/api/candidate/profile` | `candidate` | Multipart form: profile fields + optional `default_cv` (.pdf file) |
| `POST` | `/api/candidate/jobs/{id}/apply` | `candidate` | `{ cover_letter, cv_file }` $\rightarrow$ Checks duplicate, submits application |
| `GET` | `/api/candidate/applications` | `candidate` | Returns candidate's application history with statuses and HR notes |
| `GET` | `/api/recruiter/jobs` | `recruiter` | Lists all company jobs with applicant counts |
| `POST` | `/api/recruiter/jobs` | `recruiter` | Creates job posting and synchronizes skill IDs in `job_skill` |
| `GET` | `/api/recruiter/jobs/{id}/applications` | `recruiter` | Retrieves all candidate submissions for specified job |
| `PATCH` | `/api/recruiter/applications/{id}/status` | `recruiter` | Updates ATS status (`reviewed`, `interview`, etc.) + `recruiter_note` |
| `GET` | `/api/admin/dashboard-stats` | `admin` | Aggregates platform metrics (total candidates, recruiters, jobs) |
| `PATCH` | `/api/admin/users/{id}/toggle-ban` | `admin` | Toggles user status between `active` and `banned` |
| `PATCH` | `/api/admin/jobs/{id}/status` | `admin` | Sets job status to `active` (approved) or `rejected` |

*Table 3.3: Key RESTful API Endpoints Specification*

\newpage

---

# CHAPTER 4: TESTING AND EVALUATION

## 4.1. Testing Objectives
The quality assurance strategy aimed to systematically verify:
1. **Functional Correctness:** Ensure all application features operate strictly in alignment with the SRS requirements.
2. **Security & Boundary Isolation:** Confirm that `RoleMiddleware` properly blocks unauthorized cross-role requests and rejects banned user tokens.
3. **Data Integrity & Edge Cases:** Verify that duplicate job applications are rejected, invalid file extensions (non-PDF) are blocked, and cascading deletions operate cleanly.
4. **Zero Regression:** Ensure automated test suites pass with 100% reliability.

---

## 4.2. Automated Testing Suite and Test Scenarios
The automated testing suite was authored using **PHPUnit** and integrated into the Laravel testing framework. The 62 test cases are categorized across 7 test suites as summarized in Table 4.1:

| Suite # | Test Suite File | Test Cases | Assertions | Testing Focus & Scope |
| :---: | :--- | :---: | :---: | :--- |
| **1** | `AdminManagementTest.php` | 17 | 34 | Platform stats, user listing/filtering, ban/unban toggle, job approval/rejection, Category and Skill CRUD. |
| **2** | `AuthTest.php` | 10 | 22 | Candidate/Recruiter registration, password hashing, login token generation, banned user rejection, `/me` profile. |
| **3** | `CandidateWorkflowTest.php` | 11 | 24 | Profile updating, PDF resume upload, application submission, duplicate prevention, application listing, job bookmarking. |
| **4** | `RecruiterAtsTest.php` | 10 | 21 | Company profile updates, logo storage, job creation/update/deletion, applicant listing, ATS status updates with notes. |
| **5** | `RoleAuthorizationTest.php` | 12 | 22 | Role-based permission boundaries, unauthorized access rejection (HTTP 403), unauthenticated token checks (HTTP 401). |
| **6** | `ExampleTest.php` (Feature) | 1 | 2 | API availability and root endpoint verification. |
| **7** | `ExampleTest.php` (Unit) | 1 | 2 | Fundamental unit assertion verification. |
| **TOTAL** | **7 Test Suite Files** | **62 Tests** | **127 Assertions** | **100% PASS Rate Across Entire System** |

*Table 4.1: Automated PHPUnit Test Suites, Test Cases, and Assertions Summary*

---

## 4.3. Automated Testing Execution Results
The test suite was executed via the PHPUnit runner on the backend application:

```bash
cd backend
php artisan test
```

### Execution Output:
```text
   PASS  Tests\Feature\AdminManagementTest
  ✓ admin can retrieve dashboard stats                                      0.12s  
  ✓ admin can list users with pagination                                    0.04s  
  ✓ admin can filter users by role                                          0.04s  
  ✓ admin can toggle user ban status                                        0.04s  
  ✓ admin can list all jobs                                                 0.04s  
  ✓ admin can approve a job                                                 0.04s  
  ✓ admin can reject a job                                                  0.04s  
  ✓ admin can create category                                               0.04s  
  ✓ admin can update category                                               0.04s  
  ✓ admin can delete category                                               0.04s  
  ✓ admin can create skill                                                  0.04s  
  ✓ admin can update skill                                                  0.04s  
  ✓ admin can delete skill                                                  0.04s  
  ✓ non admin cannot access admin routes                                    0.04s  
  ...

   PASS  Tests\Feature\AuthTest
  ✓ candidate can register successfully                                     0.08s  
  ✓ recruiter can register successfully                                     0.05s  
  ✓ user can login with valid credentials                                   0.05s  
  ✓ user cannot login with invalid password                                 0.04s  
  ✓ banned user cannot login                                                0.04s  
  ✓ authenticated user can fetch profile                                    0.04s  
  ✓ authenticated user can logout                                           0.04s  
  ...

   PASS  Tests\Feature\CandidateWorkflowTest
  ✓ candidate can update profile                                            0.05s  
  ✓ candidate can upload default cv                                         0.05s  
  ✓ candidate can apply for job with uploaded cv                            0.06s  
  ✓ candidate cannot apply twice for same job                               0.05s  
  ✓ candidate can view applications                                         0.04s  
  ✓ candidate can save and unsave job                                       0.04s  
  ...

   PASS  Tests\Feature\RecruiterAtsTest
  ✓ recruiter can update company profile                                    0.05s  
  ✓ recruiter can create job with skills                                    0.06s  
  ✓ recruiter can update own job                                            0.05s  
  ✓ recruiter can view applicants for job                                   0.05s  
  ✓ recruiter can update application status and note                        0.05s  
  ...

   PASS  Tests\Feature\RoleAuthorizationTest
  ✓ candidate cannot access recruiter endpoints                             0.04s  
  ✓ recruiter cannot access candidate apply endpoints                       0.04s  
  ✓ guest cannot access protected endpoints                                 0.03s  
  ...

  Tests:    62 passed (127 assertions)
  Duration: 2.85s
```

The execution confirmed that all **62 test cases** and **127 assertions passed flawlessly**, confirming total system stability and specification adherence.

\newpage

---

# CONCLUSION

## 1. Achieved Results
Through the execution of this specialized project, the stated objectives have been successfully realized:
1. **Modern Decoupled Architecture:** Built a clean, maintainable, decoupled system combining a Laravel 11 RESTful API backend with a responsive Vue.js 3 Single Page Application frontend.
2. **Complete Digital Hiring Cycle:** Successfully implemented all core recruitment workflows, enabling candidates to discover jobs and apply with PDF resumes, while equipping recruiters with an intuitive ATS pipeline to track, inspect, and evaluate applicants.
3. **Enterprise Security & Reliability:** Implemented token-based authentication via Laravel Sanctum and role-based access control via custom `RoleMiddleware`, eliminating unauthorized access and IDOR vulnerabilities.
4. **Rigorous Quality Assurance:** Developed an automated testing suite comprising 62 PHPUnit test cases (127 assertions) achieving a 100% PASS rate.
5. **Production Deployment:** Successfully containerized and deployed the system to global cloud platforms (Frontend on Vercel, Backend and MySQL on Railway) accessible 24/7 over HTTPS.

---

## 2. Limitations and Future Development
While JobConnect demonstrates high operational viability, future enhancements could further augment its enterprise value:
- **AI-Powered Resume Matching:** Integrate Large Language Model (LLM) APIs to automatically parse PDF resumes, calculate candidate-to-job semantic compatibility scores, and generate interview question suggestions.
- **Real-Time Notification Engine:** Integrate WebSockets (via Laravel Reverb / Pusher) to deliver real-time notifications to candidates when their application advances to the interview stage.
- **Automated Video Interviews:** Integrate WebRTC or third-party meeting APIs (Zoom, Google Meet) directly into the recruiter ATS workspace.
- **Corporate Subscription Billing:** Integrate payment gateways (Stripe, VNPay) to support premium job listing tiers and employer branding packages.

\newpage

---

# REFERENCES

1. **Laravel Documentation (v11.x):** *The PHP Framework for Web Artisans*. https://laravel.com/docs
2. **Vue.js 3 Documentation:** *The Progressive JavaScript Framework*. https://vuejs.org/guide/introduction.html
3. **Laravel Sanctum Documentation:** *Lightweight Token-Based Authentication*. https://laravel.com/docs/sanctum
4. **MySQL 8.0 Reference Manual:** *Relational Database Management System*. https://dev.mysql.com/doc/refman/8.0/en/
5. **Bootstrap 5.3 Documentation:** *Responsive Frontend Component Library*. https://getbootstrap.com/docs/5.3/
6. **PHPUnit Testing Framework Documentation:** *The PHP Testing Framework*. https://phpunit.de/documentation.html
7. **Martin Fowler (2014):** *Patterns of Enterprise Application Architecture & Microservices*. Addison-Wesley.
8. **Fielding, Roy Thomas (2000):** *Architectural Styles and the Design of Network-based Software Architectures (REST)*. Doctoral dissertation, University of California, Irvine.

\newpage

---

# APPENDIX

## Appendix A: Environment Setup & Deployment Guide

### A.1. System Requirements
- PHP 8.2 or higher with extensions: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`.
- Node.js 18.x or higher and npm package manager.
- MySQL 8.0+ DBMS (or XAMPP).
- Git version control.

### A.2. Local Development Startup Steps

1. **Clone the repository:**
   ```bash
   git clone https://github.com/KhHuyy/JobPortal.git
   cd JobPortal
   ```

2. **Backend Setup (Laravel 11):**
   ```bash
   cd backend
   composer install
   cp .env.example .env
   php artisan key:generate
   ```
   *Configure `.env` database parameters (`DB_DATABASE=jobconnect`, `DB_USERNAME=root`, `DB_PASSWORD=`).*
   ```bash
   php artisan migrate:fresh --seed
   php artisan storage:link
   php artisan serve --port=8000
   ```

3. **Frontend Setup (Vue 3 + Vite):**
   ```bash
   cd ../frontend
   npm install
   npm run dev
   ```
   *Open browser and access the application at: `http://localhost:5173`.*

---

## Appendix B: User Manual & Demonstration Credentials

### B.1. Default Demonstration Accounts (Password: `password123`)

| Role | Email Address | Access Scope & Responsibilities |
| :--- | :--- | :--- |
| **System Administrator** | `admin@jobconnect.com` | Full master control: Moderate jobs, ban/unban users, manage categories & skills. |
| **Recruiter 1 (TechCorp)** | `hr@techcorp.com` | Manage TechCorp workspace, publish engineering jobs, review applicants, update ATS pipeline. |
| **Recruiter 2 (CloudNine)** | `talent@cloudnine.io` | Manage CloudNine workspace and cloud architecture job postings. |
| **Candidate 1** | `john.doe@gmail.com` | Full candidate profile with uploaded default PDF resume, multiple job applications. |
| **Candidate 2** | `jane.smith@gmail.com` | Candidate profile, active job applications, and saved bookmarks. |

### B.2. Core User Workflows
- **For Candidates:**
  1. Access the homepage, search for jobs by title or skill tag.
  2. Click **Apply Now** on a job posting to submit a cover letter and upload a `.pdf` resume.
  3. Navigate to **My Applications** to monitor application status (*Pending $\rightarrow$ Reviewed $\rightarrow$ Interview*).
- **For Recruiters:**
  1. Sign in with employer credentials.
  2. Navigate to **Recruiter Jobs** to create a vacancy with salary ranges and required skills.
  3. Navigate to **Applicants** for a job to preview candidate resumes in-browser, record interview notes, and update applicant stage.
- **For Administrators:**
  1. Sign in to `/admin/dashboard`.
  2. Review submitted jobs in the **Job Approvals** tab and approve them for public listing.
  3. Inspect user lists in the **User Management** tab to ban or restore accounts.
