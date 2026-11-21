<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\Category;
use App\Models\Company;
use App\Models\Job;
use App\Models\SavedJob;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =====================================================================
        // 1. CATEGORIES
        // =====================================================================
        $categoriesData = [
            ['name' => 'Software Engineering',    'slug' => 'software-engineering',    'icon' => 'bi-code-slash',      'status' => true],
            ['name' => 'Data & AI',               'slug' => 'data-ai',                 'icon' => 'bi-bar-chart-line',  'status' => true],
            ['name' => 'Product & Design',        'slug' => 'product-design',          'icon' => 'bi-palette',         'status' => true],
            ['name' => 'DevOps & Cloud',          'slug' => 'devops-cloud',            'icon' => 'bi-cloud',           'status' => true],
            ['name' => 'QA & Testing',            'slug' => 'qa-testing',              'icon' => 'bi-bug',             'status' => true],
            ['name' => 'Mobile Development',      'slug' => 'mobile-development',      'icon' => 'bi-phone',           'status' => true],
            ['name' => 'Cybersecurity',           'slug' => 'cybersecurity',           'icon' => 'bi-shield-lock',     'status' => true],
            ['name' => 'Finance & Banking',       'slug' => 'finance-banking',         'icon' => 'bi-bank',            'status' => true],
            ['name' => 'Marketing & Growth',      'slug' => 'marketing-growth',        'icon' => 'bi-megaphone',       'status' => true],
            ['name' => 'HR & Operations',         'slug' => 'hr-operations',           'icon' => 'bi-people',          'status' => true],
        ];
        foreach ($categoriesData as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // =====================================================================
        // 2. SKILLS
        // =====================================================================
        $skillNames = [
            'JavaScript', 'TypeScript', 'React', 'Vue.js', 'Node.js',
            'Python', 'Java', 'Go', 'PHP', 'Laravel',
            'Docker', 'Kubernetes', 'AWS', 'Azure', 'GCP',
            'PostgreSQL', 'MySQL', 'MongoDB', 'Redis', 'Git',
            'CI/CD', 'Agile', 'Scrum', 'iOS', 'Android',
            'Flutter', 'React Native', 'Figma', 'SQL', 'Machine Learning',
            'TensorFlow', 'PyTorch', 'Spring Boot', '.NET', 'C#',
            'C++', 'Rust', 'GraphQL', 'REST API', 'Terraform',
        ];
        foreach ($skillNames as $name) {
            Skill::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'slug' => Str::slug($name)]);
        }
        $allSkills = Skill::pluck('id', 'slug')->toArray();

        // =====================================================================
        // 3. CORE DEMO ACCOUNTS (preserved exactly)
        // =====================================================================
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@jobconnect.com'],
            ['name' => 'Admin User', 'password' => 'password123', 'role' => 'admin', 'status' => 'active', 'phone' => '+84-900-000-001']
        );

        $hrUser = User::firstOrCreate(
            ['email' => 'hr@techcorp.com'],
            ['name' => 'Sarah Johnson', 'password' => 'password123', 'role' => 'recruiter', 'status' => 'active', 'phone' => '+84-900-100-001']
        );

        $johnUser = User::firstOrCreate(
            ['email' => 'john.doe@gmail.com'],
            ['name' => 'John Doe', 'password' => 'password123', 'role' => 'candidate', 'status' => 'active', 'phone' => '+84-900-200-001']
        );

        // John Doe's candidate profile
        CandidateProfile::firstOrCreate(['user_id' => $johnUser->id], [
            'user_id'          => $johnUser->id,
            'dob'              => '1995-03-15',
            'gender'           => 'male',
            'address'          => '123 Main Street, District 1, Ho Chi Minh City',
            'bio'              => 'Passionate full-stack developer with 4 years of experience building scalable web applications. Proficient in PHP, Laravel, Vue.js, and MySQL. I love open-source and tech meetups. Currently seeking challenging opportunities at innovative companies.',
            'education'        => "Bachelor of Science in Computer Science\nCan Tho University (2013–2017) | GPA: 3.6/4.0\n\nCertifications:\n- AWS Certified Solutions Architect (2023)\n- Laravel Certified Developer (2022)",
            'experience_years' => 4,
            'skills_text'      => 'PHP, Laravel, Vue.js, MySQL, Docker, REST API, Git',
        ]);

        // =====================================================================
        // 4. COMPANIES & THEIR RECRUITERS (10 companies total)
        // =====================================================================
        $companiesData = [
            // Index 0 — demo account company
            [
                'user'    => null, // use $hrUser
                'company' => [
                    'name'         => 'TechCorp Solutions',
                    'slug'         => 'techcorp-solutions',
                    'tax_code'     => 'TC-2024-88901',
                    'website'      => 'https://www.techcorp-solutions.com',
                    'company_size' => '100-500 employees',
                    'address'      => '456 Innovation Drive, Suite 300, District 7',
                    'city'         => 'Ho Chi Minh City',
                    'description'  => "TechCorp Solutions is a leading enterprise software company specializing in cloud-based business solutions. Founded in 2015, we serve 200+ clients across Southeast Asia with our ERP and CRM platforms.\n\nBenefits: flexible hours, \$500 annual learning budget, premium health insurance, rooftop garden office.",
                    'is_verified'  => true,
                ],
            ],
            // Index 1
            [
                'user' => [
                    'name' => 'David Park', 'email' => 'talent@cloudnine.io',
                    'phone' => '+84-900-100-002',
                ],
                'company' => [
                    'name'         => 'CloudNine Technologies',
                    'slug'         => 'cloudnine-technologies',
                    'tax_code'     => 'CN-2024-55672',
                    'website'      => 'https://www.cloudnine.io',
                    'company_size' => '50-200 employees',
                    'address'      => '789 Startup Lane, Floor 12, Cau Giay District',
                    'city'         => 'Hanoi',
                    'description'  => "CloudNine Technologies is a fast-growing startup focused on cloud infrastructure and DevOps automation. Our flagship product CloudNine Deploy is used by 500+ dev teams worldwide.\n\nRemote-first, transparent culture, team across 8 countries.",
                    'is_verified'  => true,
                ],
            ],
            // Index 2
            [
                'user' => [
                    'name' => 'Minh Tuan Pham', 'email' => 'hr@vietteldigital.vn',
                    'phone' => '+84-900-100-003',
                ],
                'company' => [
                    'name'         => 'Viettel Digital',
                    'slug'         => 'viettel-digital',
                    'tax_code'     => 'VD-2024-11223',
                    'website'      => 'https://digital.viettel.vn',
                    'company_size' => '1000+ employees',
                    'address'      => '1 Giang Van Minh Street, Ba Dinh District',
                    'city'         => 'Hanoi',
                    'description'  => "Viettel Digital is the digital services arm of Viettel Group — Vietnam's largest telecommunications conglomerate. We build fintech apps, smart city solutions, e-government platforms, and AI products serving millions of Vietnamese users.\n\nStrong R&D culture, government-backed projects, national scale.",
                    'is_verified'  => true,
                ],
            ],
            // Index 3
            [
                'user' => [
                    'name' => 'Lan Anh Nguyen', 'email' => 'talent@fpt-software.com',
                    'phone' => '+84-900-100-004',
                ],
                'company' => [
                    'name'         => 'FPT Software',
                    'slug'         => 'fpt-software',
                    'tax_code'     => 'FPT-2024-33445',
                    'website'      => 'https://www.fpt-software.com',
                    'company_size' => '1000+ employees',
                    'address'      => 'FPT Tower, 10 Pham Van Bach Street, Cau Giay',
                    'city'         => 'Hanoi',
                    'description'  => "FPT Software is Vietnam's largest software outsourcing company, delivering IT services and digital transformation to global clients in Japan, Korea, the USA, and Europe. With 25,000+ employees worldwide, we offer world-class training programs and global career paths.",
                    'is_verified'  => true,
                ],
            ],
            // Index 4
            [
                'user' => [
                    'name' => 'Quoc Bao Tran', 'email' => 'careers@vng.com.vn',
                    'phone' => '+84-900-100-005',
                ],
                'company' => [
                    'name'         => 'VNG Corporation',
                    'slug'         => 'vng-corporation',
                    'tax_code'     => 'VNG-2024-55667',
                    'website'      => 'https://www.vng.com.vn',
                    'company_size' => '1000+ employees',
                    'address'      => '182 Le Dai Hanh Street, District 11',
                    'city'         => 'Ho Chi Minh City',
                    'description'  => "VNG Corporation is Vietnam's first tech unicorn, known for Zalo — the leading messaging app with 75M users. VNG operates in gaming, fintech, cloud services, and AI, with offices in Vietnam, Singapore, and the US.",
                    'is_verified'  => true,
                ],
            ],
            // Index 5
            [
                'user' => [
                    'name' => 'Hoa Thi Nguyen', 'email' => 'hr@momo.vn',
                    'phone' => '+84-900-100-006',
                ],
                'company' => [
                    'name'         => 'MoMo',
                    'slug'         => 'momo',
                    'tax_code'     => 'MM-2024-77889',
                    'website'      => 'https://www.momo.vn',
                    'company_size' => '500-1000 employees',
                    'address'      => 'eTown 2, 364 Cong Hoa Street, Tan Binh District',
                    'city'         => 'Ho Chi Minh City',
                    'description'  => "MoMo is Vietnam's #1 e-wallet and super-app with 31M+ users. We build financial services including payments, money transfer, lending, insurance, and investment products. Fast-paced fintech culture with significant impact on financial inclusion.",
                    'is_verified'  => true,
                ],
            ],
            // Index 6
            [
                'user' => [
                    'name' => 'Duc Long Vo', 'email' => 'recruitment@tmasolutions.com',
                    'phone' => '+84-900-100-007',
                ],
                'company' => [
                    'name'         => 'TMA Solutions',
                    'slug'         => 'tma-solutions',
                    'tax_code'     => 'TMA-2024-99001',
                    'website'      => 'https://www.tmasolutions.com',
                    'company_size' => '500-1000 employees',
                    'address'      => 'TMA Building, 9 Tran Nhat Duat, Phu Nhuan District',
                    'city'         => 'Ho Chi Minh City',
                    'description'  => "TMA Solutions is one of Vietnam's top software outsourcing companies with 4,000+ engineers. We serve clients in the US, Europe, and Australia across telecom, healthcare, fintech, and IoT verticals. Strong mentorship programs and competitive salaries.",
                    'is_verified'  => true,
                ],
            ],
            // Index 7
            [
                'user' => [
                    'name' => 'Phuong Mai Le', 'email' => 'hiring@kms-technology.com',
                    'phone' => '+84-900-100-008',
                ],
                'company' => [
                    'name'         => 'KMS Technology',
                    'slug'         => 'kms-technology',
                    'tax_code'     => 'KMS-2024-11223',
                    'website'      => 'https://www.kms-technology.com',
                    'company_size' => '500-1000 employees',
                    'address'      => 'Viettel Complex, 285 Cach Mang Thang 8 Street, District 10',
                    'city'         => 'Ho Chi Minh City',
                    'description'  => "KMS Technology is a US-based software services and product company with a major R&D center in Vietnam. We specialize in agile software development, QA automation, and DevOps consulting. Great work-life balance and clear promotion paths.",
                    'is_verified'  => true,
                ],
            ],
            // Index 8
            [
                'user' => [
                    'name' => 'Thu Hang Phan', 'email' => 'jobs@axonactive.com',
                    'phone' => '+84-900-100-009',
                ],
                'company' => [
                    'name'         => 'Axon Active',
                    'slug'         => 'axon-active',
                    'tax_code'     => 'AA-2024-33445',
                    'website'      => 'https://www.axonactive.com',
                    'company_size' => '200-500 employees',
                    'address'      => '28 Mach Kieu Street, Hai Chau District',
                    'city'         => 'Da Nang',
                    'description'  => "Axon Active is a Swiss-Vietnamese software company known for its people-centric culture and Agile excellence. We develop enterprise software for Swiss and international clients in insurance, logistics, and banking. Regular Swiss-Vietnam knowledge exchange programs.",
                    'is_verified'  => true,
                ],
            ],
            // Index 9
            [
                'user' => [
                    'name' => 'Bao Chau Dinh', 'email' => 'hr@nashtech.com',
                    'phone' => '+84-900-100-010',
                ],
                'company' => [
                    'name'         => 'NashTech',
                    'slug'         => 'nashtech',
                    'tax_code'     => 'NT-2024-55667',
                    'website'      => 'https://www.nashtechglobal.com',
                    'company_size' => '200-500 employees',
                    'address'      => 'Softech Park, 15 Le Van Hien Street, Ngu Hanh Son District',
                    'city'         => 'Da Nang',
                    'description'  => "NashTech is a UK-headquartered global technology company with a large Vietnam delivery center. We provide software development, cloud, data, and digital transformation services to clients across the UK, Australia, and Asia-Pacific.",
                    'is_verified'  => true,
                ],
            ],
        ];

        $companies = [];
        $recruiterUsers = [];

        foreach ($companiesData as $i => $data) {
            if ($i === 0) {
                $rUser = $hrUser;
            } else {
                $rUser = User::firstOrCreate(
                    ['email' => $data['user']['email']],
                    array_merge($data['user'], [
                        'password' => 'password123',
                        'role'     => 'recruiter',
                        'status'   => 'active',
                    ])
                );
            }
            $company = Company::firstOrCreate(
                ['slug' => $data['company']['slug']],
                array_merge($data['company'], ['user_id' => $rUser->id])
            );
            $companies[]      = $company;
            $recruiterUsers[] = $rUser;
        }

        // =====================================================================
        // 5. CANDIDATES (25–30 total, including John Doe at index 0)
        // =====================================================================
        $candidatesData = [
            // 0 — John Doe (demo account, already created above)
            null,
            // 1
            ['name' => 'Jane Smith',       'email' => 'jane.smith@gmail.com',       'phone' => '+84-900-200-002',
             'profile' => ['dob' => '1997-07-22','gender' => 'female','address' => '456 Oak Avenue, Cau Giay District, Hanoi',
                           'bio' => 'Creative UI/UX designer with 3 years building intuitive enterprise interfaces using Figma and React.',
                           'education' => "BA Digital Media Design, HUST (2015–2019) | GPA 3.8\nGoogle UX Design Certificate (2022)",
                           'experience_years' => 3,'skills_text' => 'Figma, React, JavaScript, Tailwind CSS, User Research']],
            // 2
            ['name' => 'Alex Nguyen',      'email' => 'alex.nguyen@gmail.com',      'phone' => '+84-939-555-1234',
             'profile' => ['dob' => '1993-11-08','gender' => 'male','address' => '78 Le Loi Blvd, Ninh Kieu, Can Tho',
                           'bio' => 'Senior backend engineer, 6 years distributed systems. Expert Python, Django, Kubernetes advocate.',
                           'education' => "MSc Software Engineering, VNU-HCMC (2015–2017)\nBEng IT, Can Tho University (2011–2015)\nCKA Certified",
                           'experience_years' => 6,'skills_text' => 'Python, Django, Docker, Kubernetes, PostgreSQL, AWS, Terraform']],
            // 3
            ['name' => 'Emily Tran',       'email' => 'emily.tran@gmail.com',       'phone' => '+84-900-200-004',
             'profile' => ['dob' => '1999-01-30','gender' => 'female','address' => '200 Pham Van Dong, Thu Duc, HCMC',
                           'bio' => 'Recent CS grad eager to grow as junior developer. Shipped REST APIs and React Native apps during internships.',
                           'education' => "BSc Computer Science, UIT-VNUHCM (2017–2021) | GPA 3.5 | Dean's List 2019–2020",
                           'experience_years' => 1,'skills_text' => 'JavaScript, Node.js, React, React Native, MongoDB, Git']],
            // 4
            ['name' => 'Michael Le',       'email' => 'michael.le@gmail.com',       'phone' => '+84-900-200-005',
             'profile' => ['dob' => '1991-05-12','gender' => 'male','address' => '55 Tran Hung Dao, Hai Chau, Da Nang',
                           'bio' => 'PMP-certified DevOps/PM with 8 years, led 15-engineer teams, reduced deployment time by 85%.',
                           'education' => "MBA, Da Nang University of Economics (2018–2020)\nBEng Computer Networks, Da Nang UT (2009–2013)\nPMP, CSM, AWS DevOps Pro",
                           'experience_years' => 8,'skills_text' => 'Docker, Kubernetes, AWS, Jenkins, Terraform, Agile, Scrum, JIRA']],
            // 5
            ['name' => 'Thanh Huong Vo',  'email' => 'thanh.huong@gmail.com',      'phone' => '+84-900-200-006',
             'profile' => ['dob' => '1996-09-03','gender' => 'female','address' => '10 Ly Thuong Kiet, Hoan Kiem, Hanoi',
                           'bio' => 'Data scientist with 4 years building ML models for fintech fraud detection and credit scoring at a leading bank.',
                           'education' => "BSc Applied Mathematics, Hanoi University of Science (2014–2018)\nDeepLearning.AI TensorFlow Developer Certificate",
                           'experience_years' => 4,'skills_text' => 'Python, Machine Learning, TensorFlow, SQL, PostgreSQL, Git']],
            // 6
            ['name' => 'Duc Khoa Pham',   'email' => 'khoa.pham@gmail.com',        'phone' => '+84-900-200-007',
             'profile' => ['dob' => '1994-02-18','gender' => 'male','address' => '33 Nguyen Hue, District 1, HCMC',
                           'bio' => 'Full-stack developer specializing in Java Spring Boot and Angular. 5 years building enterprise banking systems.',
                           'education' => "BEng Software Engineering, HCMUT (2012–2016)\nOracle Certified Java Professional",
                           'experience_years' => 5,'skills_text' => 'Java, Spring Boot, Angular, PostgreSQL, Docker, REST API, Git']],
            // 7
            ['name' => 'Ngoc Anh Bui',    'email' => 'ngoc.anh@gmail.com',         'phone' => '+84-900-200-008',
             'profile' => ['dob' => '1998-11-25','gender' => 'female','address' => '88 Dien Bien Phu, Thanh Khe, Da Nang',
                           'bio' => 'Mobile developer with 3 years building iOS and Android apps using Flutter and React Native.',
                           'education' => "BSc Information Technology, Da Nang University (2016–2020)\nGoogle Flutter Certification",
                           'experience_years' => 3,'skills_text' => 'Flutter, React Native, Dart, JavaScript, Firebase, Git']],
            // 8
            ['name' => 'Minh Tien Do',    'email' => 'tien.do@gmail.com',           'phone' => '+84-900-200-009',
             'profile' => ['dob' => '1992-07-04','gender' => 'male','address' => '12 Phan Boi Chau, Hai Ba Trung, Hanoi',
                           'bio' => 'Senior DevOps engineer, 7 years managing Kubernetes on GCP/AWS. Terraform and Helm expert.',
                           'education' => "BEng Computer Science, HUST (2010–2014)\nCKA, CKAD, AWS Solutions Architect Pro",
                           'experience_years' => 7,'skills_text' => 'Kubernetes, Docker, Terraform, AWS, GCP, CI/CD, Python, Go']],
            // 9
            ['name' => 'Kim Oanh Dang',   'email' => 'oanh.dang@gmail.com',        'phone' => '+84-900-200-010',
             'profile' => ['dob' => '1997-04-14','gender' => 'female','address' => '56 Nguyen Thi Minh Khai, District 3, HCMC',
                           'bio' => 'QA automation engineer with 3 years building Cypress/Selenium test suites for SaaS products.',
                           'education' => "BSc Information Systems, HCMUIT (2015–2019)\nISTQB Certified Tester",
                           'experience_years' => 3,'skills_text' => 'Selenium, Cypress, Python, Java, REST API, Git, Agile']],
            // 10
            ['name' => 'Van Hoa Truong',  'email' => 'hoa.truong@gmail.com',        'phone' => '+84-900-200-011',
             'profile' => ['dob' => '1995-12-20','gender' => 'male','address' => '44 Ly Thai To, Ninh Kieu, Can Tho',
                           'bio' => 'Product manager with 4 years driving B2B SaaS roadmaps. Former engineer turned PM with deep technical empathy.',
                           'education' => "BEng Software Engineering, Can Tho University (2013–2017)\nProduct School Certificate",
                           'experience_years' => 4,'skills_text' => 'Product Management, Agile, Scrum, JIRA, SQL, Figma']],
            // 11
            ['name' => 'Phuong Linh Cao', 'email' => 'linh.cao@gmail.com',          'phone' => '+84-900-200-012',
             'profile' => ['dob' => '2000-03-08','gender' => 'female','address' => '99 Nguyen Van Linh, Hai Chau, Da Nang',
                           'bio' => 'Fresh computer science graduate passionate about Go and distributed systems. Looking for first full-time role.',
                           'education' => "BSc Computer Science, Da Nang University (2018–2022) | GPA 3.7",
                           'experience_years' => 0,'skills_text' => 'Go, Python, Docker, SQL, Git']],
            // 12
            ['name' => 'Tri Dung Hoang',  'email' => 'dung.hoang@gmail.com',        'phone' => '+84-900-200-013',
             'profile' => ['dob' => '1990-08-17','gender' => 'male','address' => '15 Bach Dang, Hai Chau, Da Nang',
                           'bio' => 'Cybersecurity specialist with 9 years in pentesting, cloud security, and SOC operations.',
                           'education' => "BEng Networks & Communications, Da Nang UT (2008–2012)\nCISSP, CEH, AWS Security Specialty",
                           'experience_years' => 9,'skills_text' => 'Penetration Testing, AWS, Python, Network Security, SIEM, ISO 27001']],
            // 13
            ['name' => 'My Linh Phan',    'email' => 'my.linh@gmail.com',           'phone' => '+84-900-200-014',
             'profile' => ['dob' => '1996-06-11','gender' => 'female','address' => '7 Tran Phu, Ngu Hanh Son, Da Nang',
                           'bio' => 'Frontend developer 4 years, Vue.js and React expert. Passionate about accessibility and performance.',
                           'education' => "BSc Information Technology, Da Nang University (2014–2018)\nGoogle Associate Android Developer",
                           'experience_years' => 4,'skills_text' => 'Vue.js, React, TypeScript, CSS, Figma, Git']],
            // 14
            ['name' => 'Quang Vinh Ly',   'email' => 'vinh.ly@gmail.com',           'phone' => '+84-900-200-015',
             'profile' => ['dob' => '1993-01-29','gender' => 'male','address' => '100 Hung Vuong, Ninh Kieu, Can Tho',
                           'bio' => 'Golang backend engineer, 6 years building high-throughput APIs and microservices for fintech.',
                           'education' => "BEng Computer Science, Can Tho University (2011–2015)\nCloud Native Foundation KCNA",
                           'experience_years' => 6,'skills_text' => 'Go, Kubernetes, Docker, gRPC, PostgreSQL, Redis, AWS']],
            // 15
            ['name' => 'Thu Thao Nguyen', 'email' => 'thao.nguyen@gmail.com',       'phone' => '+84-900-200-016',
             'profile' => ['dob' => '1998-05-19','gender' => 'female','address' => '22 Ngo Quyen, Son Tra, Da Nang',
                           'bio' => 'UX researcher with 3 years conducting user studies for fintech and e-commerce products.',
                           'education' => "BA Psychology, Da Nang University of Social Sciences (2016–2020)\nNielsen Norman UX Certificate",
                           'experience_years' => 3,'skills_text' => 'User Research, Figma, Miro, Usability Testing, SQL']],
            // 16
            ['name' => 'Bao Long Nguyen', 'email' => 'bao.long@gmail.com',          'phone' => '+84-900-200-017',
             'profile' => ['dob' => '1994-10-05','gender' => 'male','address' => '5 Le Hong Phong, Ba Dinh, Hanoi',
                           'bio' => '.NET and Azure specialist, 7 years building enterprise HR and ERP systems for multinational clients.',
                           'education' => "BEng Software Engineering, HUST (2012–2016)\nMicrosoft Certified: Azure Solutions Architect Expert",
                           'experience_years' => 7,'skills_text' => '.NET, C#, Azure, SQL Server, Docker, REST API, Git']],
            // 17
            ['name' => 'Hong Nhung Tran', 'email' => 'nhung.tran@gmail.com',        'phone' => '+84-900-200-018',
             'profile' => ['dob' => '1999-09-01','gender' => 'female','address' => '14 Hai Ba Trung, Hoan Kiem, Hanoi',
                           'bio' => 'Junior mobile developer building Flutter apps. 1 year experience, strong Dart and Firebase skills.',
                           'education' => "BSc Computer Science, VNU Hanoi (2017–2021) | GPA 3.6",
                           'experience_years' => 1,'skills_text' => 'Flutter, Dart, Firebase, React Native, Git']],
            // 18
            ['name' => 'Thanh Dat Vo',    'email' => 'dat.vo@gmail.com',            'phone' => '+84-900-200-019',
             'profile' => ['dob' => '1992-03-22','gender' => 'male','address' => '7 Truong Chinh, Tan Binh, HCMC',
                           'bio' => 'Big data engineer with 7 years on Spark, Kafka, and Hadoop clusters processing terabyte-scale datasets.',
                           'education' => "BEng Information Technology, HCMUT (2010–2014)\nCloudera Certified Data Engineer",
                           'experience_years' => 7,'skills_text' => 'Apache Spark, Kafka, Python, SQL, AWS, GCP, Docker']],
            // 19
            ['name' => 'Quynh Chi Ngo',   'email' => 'chi.ngo@gmail.com',           'phone' => '+84-900-200-020',
             'profile' => ['dob' => '1997-11-14','gender' => 'female','address' => '30 Dien Bien Phu, Binh Thanh, HCMC',
                           'bio' => 'Growth marketer with 4 years driving SEO, paid ads, and analytics for tech startups.',
                           'education' => "BA Marketing, HCMC University of Economics (2015–2019)\nGoogle Analytics Certified, HubSpot Content Marketing",
                           'experience_years' => 4,'skills_text' => 'SEO, Google Analytics, SQL, Content Marketing, Figma']],
            // 20
            ['name' => 'Huy Hoang Duong', 'email' => 'huy.duong@gmail.com',         'phone' => '+84-900-200-021',
             'profile' => ['dob' => '1995-08-30','gender' => 'male','address' => '19 Vo Thi Sau, District 3, HCMC',
                           'bio' => 'React and TypeScript front-end developer with 5 years building dashboards and design systems at scale.',
                           'education' => "BEng Computer Science, HCMUT (2013–2017)\nMeta Front-End Developer Certificate",
                           'experience_years' => 5,'skills_text' => 'React, TypeScript, JavaScript, CSS, Redux, Figma, Git']],
            // 21
            ['name' => 'Kim Ngan Nguyen', 'email' => 'kim.ngan@gmail.com',          'phone' => '+84-900-200-022',
             'profile' => ['dob' => '2001-02-14','gender' => 'female','address' => '25 Phan Chu Trinh, Hai Chau, Da Nang',
                           'bio' => 'Computer science junior with strong Python skills seeking internship in data science or backend development.',
                           'education' => "BSc Computer Science, Da Nang University (2019–present) — 3rd year | GPA 3.5",
                           'experience_years' => 0,'skills_text' => 'Python, SQL, JavaScript, HTML, CSS, Git']],
            // 22
            ['name' => 'Thanh Trung Pham','email' => 'trung.pham@gmail.com',        'phone' => '+84-900-200-023',
             'profile' => ['dob' => '1991-06-07','gender' => 'male','address' => '88 Ly Chinh Thang, District 3, HCMC',
                           'bio' => 'Staff engineer with 10 years shipping high-scale backend systems. Currently exploring CTO opportunities.',
                           'education' => "MSc Computer Science, VNU-HCMC (2013–2015)\nBEng Software Engineering, HCMUT (2009–2013)",
                           'experience_years' => 10,'skills_text' => 'Java, Go, Python, AWS, Kubernetes, PostgreSQL, Redis, Kafka']],
            // 23
            ['name' => 'Phuong Nam Le',   'email' => 'nam.le@gmail.com',            'phone' => '+84-900-200-024',
             'profile' => ['dob' => '1996-04-23','gender' => 'male','address' => '41 Tran Phu, Hai Chau, Da Nang',
                           'bio' => 'Scrum Master and Agile coach, 5 years transforming engineering teams at outsourcing companies.',
                           'education' => "BEng Industrial Engineering, Da Nang UT (2014–2018)\nCSM, SAFe Agilist",
                           'experience_years' => 5,'skills_text' => 'Scrum, Agile, JIRA, Confluence, Coaching, SQL']],
            // 24
            ['name' => 'Dieu Linh Truong','email' => 'dieu.linh@gmail.com',         'phone' => '+84-900-200-025',
             'profile' => ['dob' => '1998-12-01','gender' => 'female','address' => '3 Ngo Duc Ke, District 1, HCMC',
                           'bio' => 'Backend developer focused on PHP and Node.js. 3 years building APIs for e-commerce and logistics startups.',
                           'education' => "BSc Information Technology, HCMUIT (2016–2020) | GPA 3.4",
                           'experience_years' => 3,'skills_text' => 'PHP, Laravel, Node.js, MySQL, Docker, REST API, Git']],
        ];

        $candidateUsers = [];

        foreach ($candidatesData as $i => $data) {
            if ($i === 0) {
                // John Doe is the demo account
                $candidateUsers[] = $johnUser;
                continue;
            }
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'email'    => $data['email'],
                    'phone'    => $data['phone'],
                    'password' => 'password123',
                    'role'     => 'candidate',
                    'status'   => 'active',
                ]
            );
            CandidateProfile::firstOrCreate(['user_id' => $user->id], array_merge($data['profile'], ['user_id' => $user->id]));
            $candidateUsers[] = $user;
        }

        // =====================================================================
        // 6. JOBS (38 total — mixed types, statuses, companies)
        // =====================================================================
        $categories = Category::pluck('id', 'slug')->toArray();

        $jobsData = [
            // ---- TechCorp Solutions (company 0) ----
            [
                'ci' => 0, 'cat' => 'software-engineering', 'title' => 'Junior Full-Stack Developer',
                'slug' => 'junior-full-stack-developer-techcorp', 'type' => 'full_time', 'level' => 'Entry Level',
                'smin' => 800, 'smax' => 1200, 'neg' => true, 'loc' => 'Ho Chi Minh City',
                'addr' => '456 Innovation Drive, Suite 300, District 7, HCMC', 'vac' => 3,
                'desc' => "Join our engineering team to build our cloud ERP platform.\n\n- Develop Laravel backend APIs\n- Build Vue.js 3 frontends\n- Write unit tests\n- Code reviews and standups",
                'req' => "- BSc Computer Science\n- Basic PHP/Laravel knowledge\n- Familiarity with Vue.js\n- MySQL basics\n- Eager to learn",
                'ben' => "- Competitive salary\n- 15 days annual leave\n- Health insurance\n- \$500 learning budget\n- Mentorship program",
                'deadline' => 30, 'status' => 'active', 'views' => 142,
                'skills' => ['php', 'laravel', 'vue-js', 'mysql', 'git'],
            ],
            [
                'ci' => 0, 'cat' => 'software-engineering', 'title' => 'Senior PHP Backend Engineer',
                'slug' => 'senior-php-backend-engineer-techcorp', 'type' => 'full_time', 'level' => 'Senior (5+ Years)',
                'smin' => 2500, 'smax' => 4000, 'neg' => true, 'loc' => 'Ho Chi Minh City',
                'addr' => '456 Innovation Drive, Suite 300, District 7, HCMC', 'vac' => 1,
                'desc' => "Lead architecture and development of our next-generation microservices platform.\n\n- Design scalable Laravel APIs\n- Lead monolith-to-microservices migration\n- Mentor junior developers\n- Establish coding standards",
                'req' => "- 5+ years PHP/Laravel\n- Strong architecture patterns (MVC, Repository, CQRS)\n- MySQL optimization\n- Docker, Redis, CI/CD\n- English proficiency",
                'ben' => "- Premium salary + stock options\n- 20 days leave + 5 personal days\n- Health and dental\n- Quarterly retreats\n- 2 days WFH/week",
                'deadline' => 45, 'status' => 'active', 'views' => 89,
                'skills' => ['php', 'laravel', 'mysql', 'docker', 'rest-api'],
            ],
            [
                'ci' => 0, 'cat' => 'data-ai', 'title' => 'Data Analyst',
                'slug' => 'data-analyst-techcorp', 'type' => 'full_time', 'level' => '1-2 Years',
                'smin' => 1000, 'smax' => 1800, 'neg' => false, 'loc' => 'Ho Chi Minh City',
                'addr' => '456 Innovation Drive, Suite 300, District 7, HCMC', 'vac' => 2,
                'desc' => "Transform raw data into actionable insights for enterprise clients.\n\n- Analyze datasets for trends\n- Build Metabase/Power BI dashboards\n- Write complex SQL queries\n- Present findings to stakeholders",
                'req' => "- BSc Statistics/Math/CS\n- 1-2 years data analysis\n- SQL proficiency\n- Python a plus\n- Strong communication",
                'ben' => "- Competitive salary + bonuses\n- 15 days leave\n- Health insurance\n- Data science learning platforms\n- Tech talks",
                'deadline' => 21, 'status' => 'active', 'views' => 56,
                'skills' => ['python', 'mysql', 'postgresql', 'sql'],
            ],
            [
                'ci' => 0, 'cat' => 'product-design', 'title' => 'UI/UX Designer',
                'slug' => 'ui-ux-designer-techcorp', 'type' => 'full_time', 'level' => '2-3 Years',
                'smin' => 1200, 'smax' => 2000, 'neg' => true, 'loc' => 'Ho Chi Minh City',
                'addr' => '456 Innovation Drive, Suite 300, District 7, HCMC', 'vac' => 1,
                'desc' => "Create beautiful, intuitive interfaces for our enterprise SaaS products.\n\n- Conduct user research\n- Create Figma prototypes\n- Design responsive interfaces\n- Maintain component library",
                'req' => "- 2-3 years UI/UX design\n- Expert Figma skills\n- Strong portfolio\n- HTML/CSS basics\n- User research experience",
                'ben' => "- Competitive salary\n- MacBook Pro + design tool licenses\n- Budget for conferences\n- 15 days leave\n- Flexible hours",
                'deadline' => 25, 'status' => 'active', 'views' => 73,
                'skills' => ['figma', 'javascript'],
            ],
            [
                'ci' => 0, 'cat' => 'hr-operations', 'title' => 'Technical Project Manager',
                'slug' => 'technical-project-manager-techcorp', 'type' => 'full_time', 'level' => 'Senior (5+ Years)',
                'smin' => 2000, 'smax' => 3500, 'neg' => true, 'loc' => 'Ho Chi Minh City',
                'addr' => '456 Innovation Drive, Suite 300, District 7, HCMC', 'vac' => 1,
                'desc' => "Lead cross-functional teams to deliver complex software projects.\n\n- Full SDLC lifecycle management\n- Coordinate engineering, design, and product\n- Run Agile ceremonies\n- Risk mitigation",
                'req' => "- 5+ years technical PM\n- PMP/CSM certification preferred\n- Agile/Scrum expert\n- JIRA/Confluence proficiency\n- Technical software background a plus",
                'ben' => "- Premium salary + quarterly bonuses\n- 20 days leave\n- Health and life insurance\n- Executive leadership training\n- 3 office / 2 remote",
                'deadline' => 35, 'status' => 'pending', 'views' => 0,
                'skills' => ['agile', 'scrum'],
            ],
            [
                'ci' => 0, 'cat' => 'qa-testing', 'title' => 'QA Automation Engineer',
                'slug' => 'qa-automation-engineer-techcorp', 'type' => 'full_time', 'level' => '2-4 Years',
                'smin' => 1200, 'smax' => 2200, 'neg' => false, 'loc' => 'Ho Chi Minh City',
                'addr' => '456 Innovation Drive, Suite 300, District 7, HCMC', 'vac' => 2,
                'desc' => "Build and maintain automated test suites for our ERP platform.\n\n- Develop Cypress/Selenium test frameworks\n- Create API test suites using Postman/REST-assured\n- Integrate tests into CI/CD pipeline\n- Maintain test documentation",
                'req' => "- 2-4 years QA automation\n- Proficiency in Cypress or Selenium\n- API testing experience\n- CI/CD basics\n- ISTQB certification a plus",
                'ben' => "- Competitive salary\n- 15 days leave\n- Health insurance\n- Tool certifications sponsored\n- Flexible hours",
                'deadline' => -1, 'status' => 'closed', 'views' => 201,
                'skills' => ['javascript', 'python', 'git', 'rest-api'],
            ],
            // ---- CloudNine Technologies (company 1) ----
            [
                'ci' => 1, 'cat' => 'devops-cloud', 'title' => 'DevOps Engineer',
                'slug' => 'devops-engineer-cloudnine', 'type' => 'remote', 'level' => '3-5 Years',
                'smin' => 2000, 'smax' => 3500, 'neg' => true, 'loc' => 'Remote',
                'addr' => 'Fully Remote', 'vac' => 2,
                'desc' => "Build and maintain cloud infrastructure and deployment pipelines.\n\n- Design CI/CD pipelines (GitHub Actions, ArgoCD)\n- Manage Kubernetes clusters on AWS EKS\n- Infrastructure as Code (Terraform, Helm)\n- Prometheus/Grafana monitoring\n- Maintain 99.9% uptime SLA",
                'req' => "- 3-5 years DevOps/SRE\n- Strong AWS expertise\n- Docker and Kubernetes hands-on\n- Terraform proficiency\n- Python/Bash scripting",
                'ben' => "- Fully remote + flexible hours\n- Top-tier USD salary\n- \$2,000 equipment allowance\n- 25 days PTO\n- Stock options",
                'deadline' => 40, 'status' => 'active', 'views' => 210,
                'skills' => ['docker', 'kubernetes', 'aws', 'python', 'terraform'],
            ],
            [
                'ci' => 1, 'cat' => 'software-engineering', 'title' => 'Backend Developer (Node.js)',
                'slug' => 'backend-developer-nodejs-cloudnine', 'type' => 'remote', 'level' => '2-4 Years',
                'smin' => 1500, 'smax' => 2800, 'neg' => false, 'loc' => 'Remote',
                'addr' => 'Fully Remote', 'vac' => 2,
                'desc' => "Build the next version of CloudNine Deploy.\n\n- Develop RESTful/GraphQL APIs in Node.js/TypeScript\n- Build real-time features via WebSockets\n- Optimize API performance\n- Architecture design reviews",
                'req' => "- 2-4 years Node.js\n- Strong TypeScript\n- PostgreSQL and Redis\n- Message queues (RabbitMQ, Kafka)\n- GraphQL is a plus",
                'ben' => "- Fully remote across any timezone\n- USD salary\n- \$1,500 learning budget\n- 25 days PTO\n- Stock options",
                'deadline' => 35, 'status' => 'active', 'views' => 167,
                'skills' => ['node-js', 'typescript', 'postgresql', 'docker', 'graphql'],
            ],
            [
                'ci' => 1, 'cat' => 'cybersecurity', 'title' => 'Security Engineer',
                'slug' => 'security-engineer-cloudnine', 'type' => 'full_time', 'level' => '3-5 Years',
                'smin' => 2500, 'smax' => 4000, 'neg' => true, 'loc' => 'Hanoi',
                'addr' => '789 Startup Lane, Floor 12, Cau Giay District, Hanoi', 'vac' => 1,
                'desc' => "Strengthen platform security and protect clients' cloud infrastructure.\n\n- Conduct security assessments and pentesting\n- Design cloud security controls\n- Develop security automation tools\n- SOC 2, ISO 27001 compliance\n- Train engineers on secure coding",
                'req' => "- 3-5 years cybersecurity\n- Strong AWS security knowledge\n- Pentesting tools (Burp Suite, OWASP ZAP)\n- CISSP/CEH preferred\n- Python/Go scripting",
                'ben' => "- Competitive security salary\n- Hybrid 3 office / 2 remote\n- 20 days leave\n- Certification budget\n- Conference sponsorship",
                'deadline' => 50, 'status' => 'active', 'views' => 45,
                'skills' => ['python', 'docker', 'aws', 'kubernetes'],
            ],
            [
                'ci' => 1, 'cat' => 'software-engineering', 'title' => 'Frontend Developer (React)',
                'slug' => 'frontend-developer-react-cloudnine', 'type' => 'remote', 'level' => '2-3 Years',
                'smin' => 1200, 'smax' => 2200, 'neg' => true, 'loc' => 'Remote',
                'addr' => 'Fully Remote', 'vac' => 1,
                'desc' => "Build responsive dashboards for the CloudNine Deploy platform.\n\n- Build complex React/TypeScript applications\n- Develop component libraries\n- Implement real-time data visualizations\n- Optimize bundle size and performance",
                'req' => "- 2-3 years frontend development\n- React + TypeScript proficiency\n- State management (Redux, Zustand)\n- Tailwind CSS or CSS-in-JS\n- Jest testing",
                'ben' => "- Remote-first async culture\n- USD salary\n- \$1,000 learning budget\n- 25 days PTO\n- MacBook provided",
                'deadline' => 28, 'status' => 'active', 'views' => 98,
                'skills' => ['react', 'typescript', 'javascript', 'git'],
            ],
            [
                'ci' => 1, 'cat' => 'data-ai', 'title' => 'Machine Learning Engineer',
                'slug' => 'machine-learning-engineer-cloudnine', 'type' => 'full_time', 'level' => 'Senior (5+ Years)',
                'smin' => 3000, 'smax' => 5000, 'neg' => true, 'loc' => 'Hanoi',
                'addr' => '789 Startup Lane, Floor 12, Cau Giay District, Hanoi', 'vac' => 1,
                'desc' => "Build intelligent infrastructure management powered by ML.\n\n- Build cost prediction and anomaly detection models\n- Design ML pipelines (Python, AWS SageMaker)\n- Integrate ML features into the platform\n- Publish internal tech talks",
                'req' => "- 5+ years ML/data science\n- Python (NumPy, Pandas, PyTorch)\n- ML infrastructure (MLflow, SageMaker)\n- Statistical modeling and time-series\n- AWS experience",
                'ben' => "- Top-tier compensation + equity\n- Hybrid work\n- 25 days PTO\n- Annual research conference budget\n- GPU workstations",
                'deadline' => 60, 'status' => 'pending', 'views' => 0,
                'skills' => ['python', 'machine-learning', 'pytorch', 'aws', 'docker'],
            ],
            // ---- Viettel Digital (company 2) ----
            [
                'ci' => 2, 'cat' => 'software-engineering', 'title' => 'Java Spring Boot Developer',
                'slug' => 'java-spring-boot-developer-viettel', 'type' => 'full_time', 'level' => '2-4 Years',
                'smin' => 1500, 'smax' => 2800, 'neg' => false, 'loc' => 'Hanoi',
                'addr' => '1 Giang Van Minh Street, Ba Dinh District, Hanoi', 'vac' => 5,
                'desc' => "Build critical backend services for Viettel's national fintech platform serving millions of users.\n\n- Develop microservices with Spring Boot\n- Design high-availability APIs\n- Integrate with payment gateways and telco systems\n- Write and maintain comprehensive tests",
                'req' => "- 2-4 years Java/Spring Boot\n- Microservices architecture\n- MySQL or PostgreSQL\n- Docker/Kubernetes basics\n- Strong problem-solving skills",
                'ben' => "- Competitive government-linked salary\n- 18 days leave\n- Viettel health insurance\n- Subsidized accommodation for relocating candidates\n- National-scale impact projects",
                'deadline' => 20, 'status' => 'active', 'views' => 320,
                'skills' => ['java', 'spring-boot', 'mysql', 'docker', 'git'],
            ],
            [
                'ci' => 2, 'cat' => 'data-ai', 'title' => 'AI/ML Research Engineer',
                'slug' => 'ai-ml-research-engineer-viettel', 'type' => 'full_time', 'level' => 'Senior (5+ Years)',
                'smin' => 2800, 'smax' => 5000, 'neg' => true, 'loc' => 'Hanoi',
                'addr' => '1 Giang Van Minh Street, Ba Dinh District, Hanoi', 'vac' => 2,
                'desc' => "Research and deploy AI models for Vietnam's largest telco ecosystem.\n\n- Research NLP and Computer Vision models\n- Build AI features for Viettel smart city projects\n- Publish research papers\n- Collaborate with global AI labs",
                'req' => "- MSc/PhD in CS or related AI field preferred\n- 5+ years ML engineering\n- TensorFlow or PyTorch expertise\n- Strong math and statistics background\n- Research publication record is a plus",
                'ben' => "- Highly competitive research salary\n- R&D budget and GPU clusters\n- International conference sponsorship\n- Flexible research schedule\n- Publication bonuses",
                'deadline' => 45, 'status' => 'active', 'views' => 155,
                'skills' => ['python', 'machine-learning', 'tensorflow', 'pytorch', 'sql'],
            ],
            [
                'ci' => 2, 'cat' => 'devops-cloud', 'title' => 'Cloud Infrastructure Engineer',
                'slug' => 'cloud-infrastructure-engineer-viettel', 'type' => 'full_time', 'level' => '3-5 Years',
                'smin' => 2000, 'smax' => 3500, 'neg' => true, 'loc' => 'Hanoi',
                'addr' => '1 Giang Van Minh Street, Ba Dinh District, Hanoi', 'vac' => 3,
                'desc' => "Design and operate Viettel's private cloud infrastructure at national scale.\n\n- Manage OpenStack and Kubernetes clusters\n- Automate infrastructure with Terraform and Ansible\n- Ensure 99.99% uptime for critical telco services\n- Collaborate with network and security teams",
                'req' => "- 3-5 years cloud/infrastructure engineering\n- OpenStack or VMware expertise\n- Kubernetes and Docker\n- Networking fundamentals\n- Linux administration",
                'ben' => "- Stable government-related employment\n- 18 days leave\n- National pension benefits\n- Professional certification support\n- Career advancement program",
                'deadline' => -15, 'status' => 'closed', 'views' => 189,
                'skills' => ['kubernetes', 'docker', 'terraform', 'python', 'aws'],
            ],
            // ---- FPT Software (company 3) ----
            [
                'ci' => 3, 'cat' => 'software-engineering', 'title' => 'React.js Frontend Developer',
                'slug' => 'reactjs-frontend-developer-fpt', 'type' => 'full_time', 'level' => '1-3 Years',
                'smin' => 1000, 'smax' => 2000, 'neg' => false, 'loc' => 'Hanoi',
                'addr' => 'FPT Tower, 10 Pham Van Bach Street, Cau Giay, Hanoi', 'vac' => 10,
                'desc' => "Build responsive web applications for FPT's global outsourcing clients.\n\n- Develop React.js applications for Japanese and Korean clients\n- Collaborate in cross-cultural agile teams\n- Code reviews and documentation\n- Participate in Japanese language training if needed",
                'req' => "- 1-3 years React.js development\n- JavaScript/TypeScript proficiency\n- CSS and responsive design\n- Git/JIRA experience\n- N4/N5 Japanese is a strong plus",
                'ben' => "- Competitive salary with JLPT bonuses\n- 15 days leave + public holidays\n- International project exposure\n- Japan rotation program\n- English and Japanese training",
                'deadline' => 30, 'status' => 'active', 'views' => 445,
                'skills' => ['react', 'javascript', 'typescript', 'css', 'git'],
            ],
            [
                'ci' => 3, 'cat' => 'qa-testing', 'title' => 'QA Engineer (Manual & Automation)',
                'slug' => 'qa-engineer-fpt', 'type' => 'full_time', 'level' => '1-3 Years',
                'smin' => 800, 'smax' => 1600, 'neg' => false, 'loc' => 'Hanoi',
                'addr' => 'FPT Tower, 10 Pham Van Bach Street, Cau Giay, Hanoi', 'vac' => 8,
                'desc' => "Ensure product quality for FPT's outsourcing projects.\n\n- Write and execute manual test cases\n- Develop automated test scripts with Selenium/Cypress\n- Track defects in JIRA\n- Collaborate with client QA teams in Japan",
                'req' => "- 1-3 years QA experience\n- Manual testing fundamentals\n- Basic automation skills (Selenium or Cypress)\n- ISTQB Foundation preferred\n- English communication required",
                'ben' => "- Competitive entry salary\n- ISTQB certification sponsored\n- Global project exposure\n- 15 days leave\n- Annual overseas trips",
                'deadline' => 25, 'status' => 'active', 'views' => 312,
                'skills' => ['javascript', 'python', 'git'],
            ],
            [
                'ci' => 3, 'cat' => 'software-engineering', 'title' => '.NET Developer',
                'slug' => 'dotnet-developer-fpt', 'type' => 'full_time', 'level' => '2-5 Years',
                'smin' => 1200, 'smax' => 2500, 'neg' => false, 'loc' => 'Hanoi',
                'addr' => 'FPT Tower, 10 Pham Van Bach Street, Cau Giay, Hanoi', 'vac' => 6,
                'desc' => "Develop enterprise solutions for European and American clients.\n\n- Build C#/.NET 6/8 backend services\n- Design SQL Server databases\n- RESTful API development\n- Code review and technical documentation",
                'req' => "- 2-5 years .NET/C# development\n- SQL Server or PostgreSQL\n- ASP.NET Web API\n- Azure DevOps is a plus\n- Good English communication",
                'ben' => "- Competitive salary scale\n- Azure certification support\n- International project experience\n- 15 days leave\n- English training program",
                'deadline' => -30, 'status' => 'closed', 'views' => 278,
                'skills' => ['c-', 'net', 'sql', 'docker', 'git'],
            ],
            // ---- VNG Corporation (company 4) ----
            [
                'ci' => 4, 'cat' => 'software-engineering', 'title' => 'Go Backend Engineer',
                'slug' => 'go-backend-engineer-vng', 'type' => 'full_time', 'level' => '3-6 Years',
                'smin' => 2200, 'smax' => 4500, 'neg' => true, 'loc' => 'Ho Chi Minh City',
                'addr' => '182 Le Dai Hanh Street, District 11, HCMC', 'vac' => 3,
                'desc' => "Build high-throughput backend systems for Zalo — Vietnam's leading messaging platform.\n\n- Design scalable Go microservices handling millions of concurrent users\n- Optimize database queries and caching strategies\n- Build real-time messaging infrastructure\n- Collaborate on open-source projects",
                'req' => "- 3-6 years backend engineering\n- Proficiency in Go or strong Java/Python with desire to switch to Go\n- Distributed systems experience\n- MySQL, Redis, Kafka\n- Strong algorithms and data structures",
                'ben' => "- Top-of-market salary\n- VNG stock options\n- 20 days leave\n- Premium health insurance\n- Annual company trip abroad",
                'deadline' => 35, 'status' => 'active', 'views' => 530,
                'skills' => ['go', 'docker', 'kubernetes', 'redis', 'mysql'],
            ],
            [
                'ci' => 4, 'cat' => 'mobile-development', 'title' => 'iOS Developer (Swift)',
                'slug' => 'ios-developer-swift-vng', 'type' => 'full_time', 'level' => '2-4 Years',
                'smin' => 1800, 'smax' => 3500, 'neg' => true, 'loc' => 'Ho Chi Minh City',
                'addr' => '182 Le Dai Hanh Street, District 11, HCMC', 'vac' => 2,
                'desc' => "Develop and maintain iOS features for Zalo with 75M users.\n\n- Build native iOS features in Swift\n- Optimize app performance and battery usage\n- Collaborate with backend teams on API integrations\n- Release and maintain App Store deployments",
                'req' => "- 2-4 years iOS development with Swift\n- Strong UIKit or SwiftUI\n- Networking and API integration\n- App Store deployment experience\n- Performance optimization skills",
                'ben' => "- Competitive market salary\n- Stock options\n- Latest Apple hardware\n- 20 days leave\n- Flexible hours",
                'deadline' => 25, 'status' => 'active', 'views' => 287,
                'skills' => ['ios', 'git', 'rest-api'],
            ],
            [
                'ci' => 4, 'cat' => 'data-ai', 'title' => 'Data Engineer',
                'slug' => 'data-engineer-vng', 'type' => 'full_time', 'level' => '3-5 Years',
                'smin' => 2000, 'smax' => 4000, 'neg' => false, 'loc' => 'Ho Chi Minh City',
                'addr' => '182 Le Dai Hanh Street, District 11, HCMC', 'vac' => 2,
                'desc' => "Build and operate VNG's data platform processing billions of events daily.\n\n- Design and maintain Spark/Kafka data pipelines\n- Build data warehouses on GCP BigQuery\n- Collaborate with ML teams for feature engineering\n- Monitor data quality and pipeline health",
                'req' => "- 3-5 years data engineering\n- Apache Spark, Kafka, Flink experience\n- SQL and Python proficiency\n- GCP or AWS data services\n- Streaming and batch processing expertise",
                'ben' => "- Highly competitive data salary\n- GCP certification support\n- Flexible hours\n- 20 days leave\n- Access to massive scale data",
                'deadline' => 40, 'status' => 'pending', 'views' => 0,
                'skills' => ['python', 'sql', 'gcp', 'docker', 'git'],
            ],
            // ---- MoMo (company 5) ----
            [
                'ci' => 5, 'cat' => 'software-engineering', 'title' => 'Senior Backend Engineer (Payments)',
                'slug' => 'senior-backend-engineer-payments-momo', 'type' => 'full_time', 'level' => 'Senior (5+ Years)',
                'smin' => 3000, 'smax' => 6000, 'neg' => true, 'loc' => 'Ho Chi Minh City',
                'addr' => 'eTown 2, 364 Cong Hoa Street, Tan Binh District, HCMC', 'vac' => 2,
                'desc' => "Build the payment infrastructure powering Vietnam's #1 e-wallet.\n\n- Design high-availability payment processing services\n- Integrate with banking APIs, Napas, and international payment networks\n- Ensure PCI-DSS compliance\n- Handle millions of daily transactions with zero downtime",
                'req' => "- 5+ years backend engineering\n- Experience with payment systems or fintech strongly preferred\n- Java, Go, or Python expertise\n- Distributed transactions and consistency patterns\n- Security-first mindset",
                'ben' => "- Best-in-class fintech salary\n- MoMo stock options\n- 20 days leave\n- Premium health + dental\n- Canteen and shuttle service",
                'deadline' => 30, 'status' => 'active', 'views' => 402,
                'skills' => ['java', 'go', 'postgresql', 'redis', 'docker'],
            ],
            [
                'ci' => 5, 'cat' => 'finance-banking', 'title' => 'Risk Analyst (Fintech)',
                'slug' => 'risk-analyst-fintech-momo', 'type' => 'full_time', 'level' => '2-4 Years',
                'smin' => 1500, 'smax' => 3000, 'neg' => false, 'loc' => 'Ho Chi Minh City',
                'addr' => 'eTown 2, 364 Cong Hoa Street, Tan Binh District, HCMC', 'vac' => 2,
                'desc' => "Analyze and mitigate financial risk for MoMo's lending and insurance products.\n\n- Build credit scoring and fraud detection models\n- Analyze transaction data for suspicious patterns\n- Collaborate with regulatory and compliance teams\n- Report risk metrics to leadership",
                'req' => "- 2-4 years risk analysis or fintech\n- SQL and Python proficiency\n- Understanding of credit risk principles\n- SAS or R experience is a plus\n- Strong analytical mindset",
                'ben' => "- Competitive fintech salary\n- Fintech industry exposure\n- 15 days leave\n- Health insurance\n- MoMo internal product discounts",
                'deadline' => 20, 'status' => 'active', 'views' => 189,
                'skills' => ['python', 'sql', 'postgresql', 'machine-learning'],
            ],
            [
                'ci' => 5, 'cat' => 'marketing-growth', 'title' => 'Growth Marketing Manager',
                'slug' => 'growth-marketing-manager-momo', 'type' => 'full_time', 'level' => '3-5 Years',
                'smin' => 1500, 'smax' => 3000, 'neg' => true, 'loc' => 'Ho Chi Minh City',
                'addr' => 'eTown 2, 364 Cong Hoa Street, Tan Binh District, HCMC', 'vac' => 1,
                'desc' => "Drive user acquisition and retention for MoMo's 31M+ user base.\n\n- Plan and execute growth campaigns across digital channels\n- Analyze campaign performance using SQL and analytics tools\n- A/B test user acquisition funnels\n- Collaborate with product and engineering teams",
                'req' => "- 3-5 years digital/growth marketing\n- Strong data analytics (SQL, Google Analytics)\n- Mobile marketing expertise\n- Creative campaign ideation\n- Excellent written Vietnamese and English",
                'ben' => "- Competitive salary + performance bonus\n- Budget for creative experiments\n- 15 days leave\n- Health insurance\n- Super-app product impact",
                'deadline' => 15, 'status' => 'active', 'views' => 234,
                'skills' => ['sql', 'python'],
            ],
            // ---- TMA Solutions (company 6) ----
            [
                'ci' => 6, 'cat' => 'software-engineering', 'title' => 'Full-Stack Developer (Laravel + React)',
                'slug' => 'full-stack-developer-laravel-react-tma', 'type' => 'full_time', 'level' => '2-4 Years',
                'smin' => 1200, 'smax' => 2500, 'neg' => false, 'loc' => 'Ho Chi Minh City',
                'addr' => 'TMA Building, 9 Tran Nhat Duat, Phu Nhuan District, HCMC', 'vac' => 4,
                'desc' => "Work on US healthcare and telecom projects as part of TMA's outsourcing teams.\n\n- Build Laravel APIs and React.js frontends\n- Collaborate with US-based client architects\n- Participate in agile sprints and code reviews\n- Technical documentation in English",
                'req' => "- 2-4 years full-stack experience\n- PHP/Laravel backend skills\n- React.js or similar frontend framework\n- MySQL database design\n- Good English communication",
                'ben' => "- Competitive salary scale\n- 15 days leave\n- Project performance bonuses\n- English language courses\n- Overseas client visit opportunities",
                'deadline' => 28, 'status' => 'active', 'views' => 198,
                'skills' => ['php', 'laravel', 'react', 'mysql', 'git'],
            ],
            [
                'ci' => 6, 'cat' => 'mobile-development', 'title' => 'Android Developer (Kotlin)',
                'slug' => 'android-developer-kotlin-tma', 'type' => 'full_time', 'level' => '1-3 Years',
                'smin' => 1000, 'smax' => 2000, 'neg' => false, 'loc' => 'Ho Chi Minh City',
                'addr' => 'TMA Building, 9 Tran Nhat Duat, Phu Nhuan District, HCMC', 'vac' => 3,
                'desc' => "Build Kotlin Android applications for TMA's US clients.\n\n- Develop native Android apps using Kotlin and Jetpack Compose\n- Integrate REST APIs and real-time push notifications\n- Publish and manage Google Play releases\n- Write unit and UI tests",
                'req' => "- 1-3 years Android development with Kotlin\n- Jetpack Compose or XML UI\n- API integration experience\n- Google Play publishing\n- Good problem-solving skills",
                'ben' => "- Competitive entry salary\n- Android certification support\n- 15 days leave\n- Learning stipend\n- Mentorship from senior engineers",
                'deadline' => 22, 'status' => 'active', 'views' => 145,
                'skills' => ['android', 'java', 'git', 'rest-api'],
            ],
            [
                'ci' => 6, 'cat' => 'devops-cloud', 'title' => 'AWS DevOps Engineer',
                'slug' => 'aws-devops-engineer-tma', 'type' => 'full_time', 'level' => '3-5 Years',
                'smin' => 1800, 'smax' => 3500, 'neg' => true, 'loc' => 'Ho Chi Minh City',
                'addr' => 'TMA Building, 9 Tran Nhat Duat, Phu Nhuan District, HCMC', 'vac' => 2,
                'desc' => "Manage AWS cloud infrastructure for TMA's US enterprise clients.\n\n- Design and implement AWS infrastructure with Terraform\n- Build and maintain CI/CD pipelines\n- Monitor services with CloudWatch and PagerDuty\n- Security hardening and compliance",
                'req' => "- 3-5 years DevOps with AWS\n- AWS Solutions Architect or DevOps certification preferred\n- Terraform and CloudFormation\n- Docker and Kubernetes\n- Scripting in Python or Bash",
                'ben' => "- Strong salary\n- AWS certification sponsorship\n- 15 days leave\n- Overseas project travel\n- Technical leadership opportunities",
                'deadline' => -10, 'status' => 'closed', 'views' => 267,
                'skills' => ['aws', 'docker', 'kubernetes', 'terraform', 'python'],
            ],
            // ---- KMS Technology (company 7) ----
            [
                'ci' => 7, 'cat' => 'qa-testing', 'title' => 'Senior QA Automation Engineer',
                'slug' => 'senior-qa-automation-engineer-kms', 'type' => 'full_time', 'level' => 'Senior (5+ Years)',
                'smin' => 2000, 'smax' => 4000, 'neg' => true, 'loc' => 'Ho Chi Minh City',
                'addr' => 'Viettel Complex, 285 CMT8, District 10, HCMC', 'vac' => 2,
                'desc' => "Lead QA automation initiatives for KMS's US clients.\n\n- Architect and maintain Selenium/Playwright test frameworks\n- Implement API and performance testing\n- Mentor junior QA engineers\n- Define QA strategy and best practices",
                'req' => "- 5+ years QA automation\n- Selenium, Playwright, or Cypress expertise\n- API testing (Postman, REST Assured)\n- CI/CD integration experience\n- ISTQB Advanced preferred",
                'ben' => "- Senior-level salary\n- ISTQB/advanced certification sponsored\n- 20 days leave\n- Technical leadership path\n- Flexible hours",
                'deadline' => 33, 'status' => 'active', 'views' => 167,
                'skills' => ['javascript', 'python', 'java', 'git', 'rest-api'],
            ],
            [
                'ci' => 7, 'cat' => 'software-engineering', 'title' => 'Python Backend Developer',
                'slug' => 'python-backend-developer-kms', 'type' => 'full_time', 'level' => '2-4 Years',
                'smin' => 1500, 'smax' => 3000, 'neg' => false, 'loc' => 'Ho Chi Minh City',
                'addr' => 'Viettel Complex, 285 CMT8, District 10, HCMC', 'vac' => 3,
                'desc' => "Build Python backend services for KMS's US SaaS clients.\n\n- Develop Django/FastAPI REST services\n- Design PostgreSQL schemas and migrations\n- Write comprehensive unit and integration tests\n- Participate in architecture reviews",
                'req' => "- 2-4 years Python backend development\n- Django or FastAPI proficiency\n- PostgreSQL design experience\n- Docker and CI/CD basics\n- Good English communication",
                'ben' => "- Competitive salary\n- Python certification support\n- 15 days leave\n- Performance bonuses\n- Technical mentorship",
                'deadline' => 20, 'status' => 'active', 'views' => 201,
                'skills' => ['python', 'postgresql', 'docker', 'git', 'rest-api'],
            ],
            // ---- Axon Active (company 8) ----
            [
                'ci' => 8, 'cat' => 'software-engineering', 'title' => 'Agile Software Developer',
                'slug' => 'agile-software-developer-axon-active', 'type' => 'full_time', 'level' => '2-5 Years',
                'smin' => 1200, 'smax' => 2800, 'neg' => false, 'loc' => 'Da Nang',
                'addr' => '28 Mach Kieu Street, Hai Chau District, Da Nang', 'vac' => 4,
                'desc' => "Work on enterprise insurance and logistics systems for Swiss clients.\n\n- Develop Java or C# enterprise applications\n- Practice genuine XP and Scrum: TDD, pair programming, CI\n- Bi-annual knowledge exchange trips to Switzerland\n- Collaborate in English with Swiss architects",
                'req' => "- 2-5 years software development\n- Java or C# with enterprise frameworks\n- Strong TDD and clean code practices\n- Agile/Scrum experience\n- English proficiency (B2+)",
                'ben' => "- Switzerland exchange program\n- Competitive salary for Da Nang\n- 15 days leave + Swiss public holidays\n- Health insurance\n- English and German training",
                'deadline' => 25, 'status' => 'active', 'views' => 189,
                'skills' => ['java', 'c-', 'git', 'agile', 'scrum'],
            ],
            [
                'ci' => 8, 'cat' => 'product-design', 'title' => 'Product Designer (UX Focus)',
                'slug' => 'product-designer-ux-axon-active', 'type' => 'full_time', 'level' => '3-5 Years',
                'smin' => 1500, 'smax' => 3000, 'neg' => true, 'loc' => 'Da Nang',
                'addr' => '28 Mach Kieu Street, Hai Chau District, Da Nang', 'vac' => 1,
                'desc' => "Design user experiences for complex enterprise insurance and logistics applications.\n\n- Conduct user research with Swiss enterprise users\n- Create information architecture and wireframes\n- Prototype and test with real users\n- Maintain and evolve design systems",
                'req' => "- 3-5 years UX/product design\n- Strong research and prototyping skills\n- Figma and Adobe XD proficiency\n- Experience with complex enterprise UX\n- English B2+ for client communication",
                'ben' => "- Switzerland exchange trips\n- Competitive salary\n- 15 days leave\n- Design tool budgets\n- International enterprise UX experience",
                'deadline' => 18, 'status' => 'pending', 'views' => 0,
                'skills' => ['figma', 'javascript'],
            ],
            [
                'ci' => 8, 'cat' => 'software-engineering', 'title' => 'Software Development Intern',
                'slug' => 'software-development-intern-axon', 'type' => 'internship', 'level' => 'Entry Level',
                'smin' => 300, 'smax' => 600, 'neg' => false, 'loc' => 'Da Nang',
                'addr' => '28 Mach Kieu Street, Hai Chau District, Da Nang', 'vac' => 5,
                'desc' => "6-month internship working on real Swiss client projects with Axon's award-winning Agile teams.\n\n- Work on real projects from day one under mentorship\n- Learn Agile/XP practices in a professional setting\n- Attend weekly tech talks and coding dojos\n- Complete internship with a live feature shipped",
                'req' => "- 3rd or 4th year CS/IT student\n- Basic OOP and data structures\n- Any programming language\n- Eagerness to learn Agile practices\n- English B1+",
                'ben' => "- Monthly stipend \$300–\$600\n- Mentorship from senior engineers\n- Real project experience\n- Full-time job offer consideration\n- English language support",
                'deadline' => 10, 'status' => 'active', 'views' => 234,
                'skills' => ['java', 'python', 'git'],
            ],
            // ---- NashTech (company 9) ----
            [
                'ci' => 9, 'cat' => 'software-engineering', 'title' => 'Full-Stack Engineer (.NET + Angular)',
                'slug' => 'full-stack-engineer-dotnet-angular-nashtech', 'type' => 'full_time', 'level' => '3-6 Years',
                'smin' => 1800, 'smax' => 3500, 'neg' => false, 'loc' => 'Da Nang',
                'addr' => 'Softech Park, 15 Le Van Hien, Ngu Hanh Son, Da Nang', 'vac' => 3,
                'desc' => "Deliver full-stack solutions for NashTech's UK and Australian enterprise clients.\n\n- Build C#/.NET 8 backend services\n- Develop Angular 17 frontend applications\n- Design SQL Server and PostgreSQL databases\n- Participate in client workshops and technical reviews",
                'req' => "- 3-6 years full-stack experience\n- .NET/C# backend proficiency\n- Angular or React frontend skills\n- SQL database design\n- Professional English (B2+)",
                'ben' => "- Competitive salary for Da Nang\n- UK/Australia project exposure\n- 15 days leave\n- Microsoft certification support\n- Bi-annual salary reviews",
                'deadline' => 28, 'status' => 'active', 'views' => 234,
                'skills' => ['c-', 'net', 'sql', 'docker', 'git'],
            ],
            [
                'ci' => 9, 'cat' => 'devops-cloud', 'title' => 'Azure Cloud Engineer',
                'slug' => 'azure-cloud-engineer-nashtech', 'type' => 'full_time', 'level' => '2-5 Years',
                'smin' => 1500, 'smax' => 3200, 'neg' => true, 'loc' => 'Da Nang',
                'addr' => 'Softech Park, 15 Le Van Hien, Ngu Hanh Son, Da Nang', 'vac' => 2,
                'desc' => "Manage Azure cloud environments for NashTech's UK enterprise clients.\n\n- Design and deploy Azure infrastructure using ARM/Bicep and Terraform\n- Implement Azure DevOps CI/CD pipelines\n- Monitor with Azure Monitor and Application Insights\n- Cloud cost optimization",
                'req' => "- 2-5 years Azure cloud engineering\n- AZ-104 or AZ-204 certification preferred\n- Terraform or Azure Bicep\n- Docker and Kubernetes\n- Good English",
                'ben' => "- Competitive salary\n- Azure certification fully sponsored\n- 15 days leave\n- UK project travel opportunities\n- Technical leadership path",
                'deadline' => 32, 'status' => 'active', 'views' => 189,
                'skills' => ['azure', 'docker', 'kubernetes', 'terraform', 'git'],
            ],
            [
                'ci' => 9, 'cat' => 'hr-operations', 'title' => 'HR Business Partner (Tech)',
                'slug' => 'hr-business-partner-nashtech', 'type' => 'full_time', 'level' => '3-5 Years',
                'smin' => 1200, 'smax' => 2500, 'neg' => false, 'loc' => 'Da Nang',
                'addr' => 'Softech Park, 15 Le Van Hien, Ngu Hanh Son, Da Nang', 'vac' => 1,
                'desc' => "Support NashTech's engineering teams through strategic HR partnership.\n\n- Partner with engineering managers on talent acquisition and retention\n- Run onboarding, performance management, and culture programs\n- Manage UK client staff expectations and reporting\n- Drive DEI initiatives across the Da Nang delivery center",
                'req' => "- 3-5 years HR business partnering in tech companies\n- Talent acquisition and retention experience\n- Excellent Vietnamese and English communication\n- HR systems (HRIS, ATS) proficiency\n- Strong analytical and interpersonal skills",
                'ben' => "- Competitive HR salary\n- UK client exposure\n- 15 days leave\n- Professional HR certifications\n- Flexible work arrangement",
                'deadline' => -20, 'status' => 'closed', 'views' => 145,
                'skills' => ['agile', 'scrum'],
            ],
        ];

        $allJobs = [];

        foreach ($jobsData as $jd) {
            $skillSlugs = $jd['skills'];
            $ci = $jd['ci'];
            $catSlug = $jd['cat'];
            $days = $jd['deadline'];

            $job = Job::create([
                'company_id'       => $companies[$ci]->id,
                'category_id'      => $categories[$catSlug],
                'title'            => $jd['title'],
                'slug'             => $jd['slug'],
                'job_type'         => $jd['type'],
                'experience_level' => $jd['level'],
                'salary_min'       => $jd['smin'],
                'salary_max'       => $jd['smax'],
                'is_negotiable'    => $jd['neg'],
                'location'         => $jd['loc'],
                'work_address'     => $jd['addr'],
                'vacancies'        => $jd['vac'],
                'description'      => $jd['desc'],
                'requirements'     => $jd['req'],
                'benefits'         => $jd['ben'],
                'deadline'         => now()->addDays($days)->toDateString(),
                'status'           => $jd['status'],
                'views_count'      => $jd['views'],
            ]);

            $skillIds = [];
            foreach ($skillSlugs as $slug) {
                if (isset($allSkills[$slug])) {
                    $skillIds[] = $allSkills[$slug];
                }
            }
            if (!empty($skillIds)) {
                $job->skills()->attach($skillIds);
            }

            $allJobs[] = $job;
        }

        // =====================================================================
        // 7. APPLICATIONS (40–50 applications across all ATS stages)
        // Job indices in $allJobs:
        //  0  = Junior Full-Stack (TechCorp)         active
        //  1  = Senior PHP BE (TechCorp)             active
        //  2  = Data Analyst (TechCorp)              active
        //  3  = UI/UX Designer (TechCorp)            active
        //  4  = Tech PM (TechCorp)                   pending
        //  5  = QA Automation (TechCorp)             closed
        //  6  = DevOps (CloudNine)                   active
        //  7  = Node.js Backend (CloudNine)          active
        //  8  = Security Engineer (CloudNine)        active
        //  9  = Frontend React (CloudNine)           active
        //  10 = ML Engineer (CloudNine)              pending
        //  11 = Java Spring (Viettel)                active
        //  12 = AI/ML Research (Viettel)             active
        //  13 = Cloud Infra (Viettel)                closed
        //  14 = React Frontend (FPT)                 active
        //  15 = QA Engineer (FPT)                    active
        //  16 = .NET Developer (FPT)                 closed
        //  17 = Go Backend (VNG)                     active
        //  18 = iOS Developer (VNG)                  active
        //  19 = Data Engineer (VNG)                  pending
        //  20 = Senior BE Payments (MoMo)            active
        //  21 = Risk Analyst (MoMo)                  active
        //  22 = Growth Marketing (MoMo)              active
        //  23 = Full-Stack (TMA)                     active
        //  24 = Android Dev (TMA)                    active
        //  25 = AWS DevOps (TMA)                     closed
        //  26 = Senior QA (KMS)                      active
        //  27 = Python BE (KMS)                      active
        //  28 = Agile Dev (Axon)                     active
        //  29 = Product Designer (Axon)              pending
        //  30 = Intern (Axon)                        active
        //  31 = Full-Stack .NET+Angular (NashTech)   active
        //  32 = Azure Cloud (NashTech)               active
        //  33 = HR BP (NashTech)                     closed
        //
        // Candidate indices:
        //  0  = John Doe        (PHP/Laravel/Vue)
        //  1  = Jane Smith      (UI/UX, React)
        //  2  = Alex Nguyen     (Python/DevOps/BE)
        //  3  = Emily Tran      (JS/Node/React Native)
        //  4  = Michael Le      (DevOps/PM)
        //  5  = Thanh Huong Vo  (ML/Data Science)
        //  6  = Duc Khoa Pham   (Java/Spring Boot)
        //  7  = Ngoc Anh Bui    (Flutter/Mobile)
        //  8  = Minh Tien Do    (DevOps/K8s)
        //  9  = Kim Oanh Dang   (QA Automation)
        //  10 = Van Hoa Truong  (Product Manager)
        //  11 = Phuong Linh Cao (Go/BE)
        //  12 = Tri Dung Hoang  (Cybersecurity)
        //  13 = My Linh Phan    (Frontend Vue/React)
        //  14 = Quang Vinh Ly   (Go/Fintech BE)
        //  15 = Thu Thao Nguyen (UX Research)
        //  16 = Bao Long Nguyen (.NET/Azure)
        //  17 = Hong Nhung Tran (Flutter/Junior)
        //  18 = Thanh Dat Vo    (Big Data/Spark)
        //  19 = Quynh Chi Ngo   (Marketing)
        //  20 = Huy Hoang Duong (React/TypeScript)
        //  21 = Kim Ngan Nguyen (Junior/Python intern)
        //  22 = Thanh Trung Pham(Staff Engineer)
        //  23 = Phuong Nam Le   (Scrum/PM)
        //  24 = Dieu Linh Truong(PHP/Node BE)
        // =====================================================================

        $appsData = [
            // John Doe (0) — Full-Stack/PHP
            ['ji' => 0,  'ci' => 0,  'cv' => 'cvs/john_doe_resume.pdf',    'status' => 'reviewed',
             'note' => 'Strong Laravel skills. Good cultural fit. Schedule technical interview.',
             'letter' => "Dear Hiring Manager,\n\nI am writing to express my interest in the Junior Full-Stack Developer position at TechCorp Solutions. With 4 years of Laravel and Vue.js experience, I am confident my skills align with your ERP platform needs.\n\nBest regards, John Doe"],
            ['ji' => 1,  'ci' => 0,  'cv' => 'cvs/john_doe_resume.pdf',    'status' => 'pending',
             'note' => null,
             'letter' => "Dear TechCorp,\n\nI am applying for the Senior PHP Backend Engineer role. Though primarily mid-level, I have led features in production Laravel systems and am eager to step up.\n\nBest, John Doe"],
            ['ji' => 23, 'ci' => 0,  'cv' => 'cvs/john_doe_resume.pdf',    'status' => 'interview',
             'note' => 'Good full-stack profile matching TMA requirements. Technical interview scheduled for next week.',
             'letter' => "Dear TMA Solutions,\n\nI am excited to apply for the Full-Stack Developer position. My Laravel/React experience maps directly to your US healthcare client requirements.\n\nBest, John Doe"],

            // Jane Smith (1) — UI/UX, React
            ['ji' => 3,  'ci' => 1,  'cv' => 'cvs/jane_smith_resume.pdf',  'status' => 'interview',
             'note' => 'Impressive Figma portfolio. Increased user engagement 40% in prior role. Invited for onsite design exercise on Dec 5.',
             'letter' => "Dear TechCorp,\n\nI am thrilled to apply for the UI/UX Designer role. My 3 years designing enterprise SaaS interfaces and strong Figma skills make me a great fit.\n\nWarm regards, Jane Smith"],
            ['ji' => 9,  'ci' => 1,  'cv' => 'cvs/jane_smith_resume.pdf',  'status' => 'pending',
             'note' => null,
             'letter' => "Hello CloudNine,\n\nI am applying for the Frontend React Developer position. While my background is UI/UX design, I've shipped React applications and am excited by remote-first culture.\n\nBest, Jane Smith"],
            ['ji' => 29, 'ci' => 1,  'cv' => 'cvs/jane_smith_resume.pdf',  'status' => 'offered',
             'note' => 'Excellent enterprise UX portfolio. Offered \$2,800/month. Awaiting acceptance.',
             'letter' => "Dear Axon Active,\n\nI am passionate about complex enterprise UX and would love to bring my skills to Swiss enterprise projects. My background in design systems matches your needs precisely.\n\nBest, Jane Smith"],

            // Alex Nguyen (2) — Python/DevOps/BE
            ['ji' => 1,  'ci' => 2,  'cv' => 'cvs/alex_nguyen_resume.pdf', 'status' => 'offered',
             'note' => 'Exceptional candidate. 6 years distributed systems. Offered \$3,500/month + stock. Awaiting by Dec 10.',
             'letter' => "Dear TechCorp,\n\nI apply for Senior PHP Backend Engineer. My 6 years in distributed Python/Django systems and earlier PHP/Laravel experience make me a strong fit for your microservices migration.\n\nBest, Alex Nguyen"],
            ['ji' => 6,  'ci' => 2,  'cv' => 'cvs/alex_nguyen_resume.pdf', 'status' => 'reviewed',
             'note' => 'Very strong DevOps profile. CKA certified. Schedule deep-dive technical interview.',
             'letter' => "Dear CloudNine,\n\nI am excited to apply for the DevOps Engineer role. My Kubernetes, Terraform, and AWS expertise aligns perfectly with your infrastructure needs.\n\nBest, Alex Nguyen"],
            ['ji' => 7,  'ci' => 2,  'cv' => 'cvs/alex_nguyen_resume.pdf', 'status' => 'pending',
             'note' => null,
             'letter' => "Dear CloudNine,\n\nWhile my primary language is Python, I am proficient in Node.js and excited by the opportunity to work on CloudNine Deploy's GraphQL API.\n\nBest, Alex Nguyen"],

            // Emily Tran (3) — JS/Node/junior
            ['ji' => 0,  'ci' => 3,  'cv' => 'cvs/emily_tran_resume.pdf',  'status' => 'rejected',
             'note' => 'Good potential but lacks Laravel experience. Encouraged to reapply after 6 months of PHP/Laravel practice.',
             'letter' => "Dear TechCorp,\n\nAs a recent UIT graduate with Node.js and React Native internship experience, I am eager to transition to Laravel full-stack development.\n\nSincerely, Emily Tran"],
            ['ji' => 30, 'ci' => 3,  'cv' => 'cvs/emily_tran_resume.pdf',  'status' => 'interview',
             'note' => 'Enthusiastic and strong JavaScript background. Interview Monday 10 AM.',
             'letter' => "Dear Axon Active,\n\nI am a recent CS grad excited to begin my career in an Agile environment. I learn fast and am eager to contribute to real projects from day one.\n\nBest, Emily Tran"],
            ['ji' => 14, 'ci' => 3,  'cv' => 'cvs/emily_tran_resume.pdf',  'status' => 'pending',
             'note' => null,
             'letter' => "Dear FPT Software,\n\nI apply for the React Frontend Developer role. My UIT React and JavaScript skills align with your global project requirements.\n\nBest, Emily Tran"],

            // Michael Le (4) — DevOps/PM
            ['ji' => 4,  'ci' => 4,  'cv' => 'cvs/michael_le_resume.pdf',  'status' => 'pending',
             'note' => null,
             'letter' => "Dear TechCorp,\n\nWith 8 years in IT including 4 as a PMP-certified PM managing 15-engineer fintech teams, I am a strong candidate for your Technical PM role.\n\nBest, Michael Le"],
            ['ji' => 6,  'ci' => 4,  'cv' => 'cvs/michael_le_resume.pdf',  'status' => 'reviewed',
             'note' => 'Strong DevOps profile with PM background. Unusual combo, could lead infra teams. Invite for culture fit call.',
             'letter' => "Dear CloudNine,\n\nMy AWS DevOps Pro certification and 8 years IT experience, including significant Kubernetes and CI/CD work, make me a strong DevOps candidate.\n\nBest, Michael Le"],
            ['ji' => 25, 'ci' => 4,  'cv' => 'cvs/michael_le_resume.pdf',  'status' => 'rejected',
             'note' => 'Role already closed. Good candidate — referred to open DevOps req.',
             'letter' => "Dear TMA,\n\nI am applying for the AWS DevOps Engineer role. My extensive AWS infrastructure management experience across fintech projects is a strong match.\n\nBest, Michael Le"],

            // Thanh Huong Vo (5) — ML/Data Science
            ['ji' => 2,  'ci' => 5,  'cv' => 'cvs/thanh_huong_resume.pdf', 'status' => 'interview',
             'note' => 'Strong Python and ML background. Scheduled Data Analyst SQL test for Dec 3.',
             'letter' => "Dear TechCorp,\n\nWith 4 years building ML fraud detection models in fintech, I bring strong Python and SQL analytical skills to your Data Analyst role.\n\nBest, Thanh Huong Vo"],
            ['ji' => 12, 'ci' => 5,  'cv' => 'cvs/thanh_huong_resume.pdf', 'status' => 'reviewed',
             'note' => 'Solid ML credentials. Invite for research presentation round.',
             'letter' => "Dear Viettel Digital,\n\nI am excited by the AI/ML Research Engineer role. My TensorFlow expertise and fintech ML deployment experience align with your national-scale AI ambitions.\n\nBest, Thanh Huong Vo"],
            ['ji' => 21, 'ci' => 5,  'cv' => 'cvs/thanh_huong_resume.pdf', 'status' => 'offered',
             'note' => 'Excellent fintech risk background. Offered \$2,500/month. Awaiting decision.',
             'letter' => "Dear MoMo,\n\nMy 4 years building credit scoring and fraud detection models at a leading bank make me a natural fit for your Risk Analyst role.\n\nBest, Thanh Huong Vo"],

            // Duc Khoa Pham (6) — Java/Spring
            ['ji' => 11, 'ci' => 6,  'cv' => 'cvs/khoa_pham_resume.pdf',   'status' => 'reviewed',
             'note' => 'Strong Spring Boot background. 5 years enterprise experience matches requirements. Schedule system design interview.',
             'letter' => "Dear Viettel Digital,\n\nWith 5 years of Java/Spring Boot experience building enterprise banking systems, I am excited to work on your national fintech platform.\n\nBest, Duc Khoa Pham"],
            ['ji' => 20, 'ci' => 6,  'cv' => 'cvs/khoa_pham_resume.pdf',   'status' => 'interview',
             'note' => 'Java payment systems experience is exactly what we need. Payment gateway design interview scheduled.',
             'letter' => "Dear MoMo,\n\nBuilding payment systems is my specialty. My 5 years in Java enterprise banking systems, including payment gateway integrations, is a strong match for your Senior Backend (Payments) role.\n\nBest, Duc Khoa Pham"],
            ['ji' => 28, 'ci' => 6,  'cv' => 'cvs/khoa_pham_resume.pdf',   'status' => 'pending',
             'note' => null,
             'letter' => "Dear Axon Active,\n\nYour Switzerland exchange program and genuine XP practices appeal strongly to me. My Java enterprise skills are a solid foundation for your Agile developer role.\n\nBest, Duc Khoa Pham"],

            // Ngoc Anh Bui (7) — Flutter/Mobile
            ['ji' => 24, 'ci' => 7,  'cv' => 'cvs/ngoc_anh_resume.pdf',    'status' => 'pending',
             'note' => null,
             'letter' => "Dear TMA Solutions,\n\nI am applying for the Android Developer (Kotlin) role. While my primary experience is Flutter, I have shipped production Android apps using native Kotlin and Jetpack Compose.\n\nBest, Ngoc Anh Bui"],
            ['ji' => 18, 'ci' => 7,  'cv' => 'cvs/ngoc_anh_resume.pdf',    'status' => 'reviewed',
             'note' => 'Primarily Flutter background. Some native iOS experience. Needs Swift assessment.',
             'letter' => "Dear VNG,\n\nI apply for the iOS Developer role. Though Flutter-first, I have worked on native iOS features and am committed to deepening my Swift expertise.\n\nBest, Ngoc Anh Bui"],

            // Minh Tien Do (8) — DevOps/K8s
            ['ji' => 6,  'ci' => 8,  'cv' => 'cvs/tien_do_resume.pdf',     'status' => 'offered',
             'note' => 'Top-tier DevOps profile. CKA + CKAD certified. Offered \$3,200/month USD. Response by Dec 15.',
             'letter' => "Dear CloudNine,\n\nWith 7 years as a senior DevOps engineer and CKA/CKAD certifications, I am exactly aligned with your DevOps Engineer requirements. I specialize in Kubernetes on GCP and AWS.\n\nBest, Minh Tien Do"],
            ['ji' => 32, 'ci' => 8,  'cv' => 'cvs/tien_do_resume.pdf',     'status' => 'reviewed',
             'note' => 'Strong multi-cloud engineer. Azure experience is secondary to AWS/GCP. Invite for Azure skills assessment.',
             'letter' => "Dear NashTech,\n\nI bring 7 years multi-cloud DevOps experience. While primarily AWS/GCP focused, I am AZ-104 certified and have managed Azure DevOps pipelines for enterprise clients.\n\nBest, Minh Tien Do"],

            // Kim Oanh Dang (9) — QA Automation
            ['ji' => 5,  'ci' => 9,  'cv' => 'cvs/oanh_dang_resume.pdf',   'status' => 'reviewed',
             'note' => 'Role now closed but excellent QA profile. Referred to Senior QA at KMS.',
             'letter' => "Dear TechCorp,\n\nI apply for the QA Automation Engineer role with 3 years building Cypress and Selenium test suites for SaaS products.\n\nBest, Kim Oanh Dang"],
            ['ji' => 26, 'ci' => 9,  'cv' => 'cvs/oanh_dang_resume.pdf',   'status' => 'interview',
             'note' => 'Strong Cypress expertise and CI/CD test integration. Senior QA interview scheduled.',
             'letter' => "Dear KMS Technology,\n\nI am excited to apply for the Senior QA Automation Engineer role. My 3 years architecting Cypress suites and integrating them into GitHub Actions CI aligns well with your requirements.\n\nBest, Kim Oanh Dang"],
            ['ji' => 15, 'ci' => 9,  'cv' => 'cvs/oanh_dang_resume.pdf',   'status' => 'pending',
             'note' => null,
             'letter' => "Dear FPT Software,\n\nI apply for the QA Engineer role, bringing 3 years manual and automation experience across SaaS products.\n\nBest, Kim Oanh Dang"],

            // Van Hoa Truong (10) — Product Manager
            ['ji' => 4,  'ci' => 10, 'cv' => 'cvs/van_hoa_resume.pdf',     'status' => 'pending',
             'note' => null,
             'letter' => "Dear TechCorp,\n\nAs a Product Manager with engineering roots, I bring 4 years of B2B SaaS roadmap experience. My technical background ensures strong collaboration with your engineering team.\n\nBest, Van Hoa Truong"],
            ['ji' => 22, 'ci' => 10, 'cv' => 'cvs/van_hoa_resume.pdf',     'status' => 'rejected',
             'note' => 'Strong PM background but Growth Marketing requires deep performance marketing expertise beyond product scope.',
             'letter' => "Dear MoMo,\n\nWhile my background is product management, I have worked closely with growth teams driving user activation campaigns and would love to transition to a growth marketing role.\n\nBest, Van Hoa Truong"],

            // Phuong Linh Cao (11) — Go/junior
            ['ji' => 17, 'ci' => 11, 'cv' => 'cvs/linh_cao_resume.pdf',    'status' => 'pending',
             'note' => null,
             'letter' => "Dear VNG,\n\nAs a recent CS graduate passionate about Go and distributed systems, working on Zalo's backend would be a dream opportunity. I am a fast learner eager to grow.\n\nBest, Phuong Linh Cao"],
            ['ji' => 7,  'ci' => 11, 'cv' => 'cvs/linh_cao_resume.pdf',    'status' => 'pending',
             'note' => null,
             'letter' => "Dear CloudNine,\n\nI apply for the Node.js Backend Developer role. My Go and Python background and deep interest in distributed systems make me a strong entry-level candidate.\n\nBest, Phuong Linh Cao"],

            // Tri Dung Hoang (12) — Cybersecurity
            ['ji' => 8,  'ci' => 12, 'cv' => 'cvs/tri_dung_resume.pdf',    'status' => 'offered',
             'note' => 'Best cybersecurity candidate we have seen. 9 years, CISSP + CEH. Offered \$3,800/month. Highest priority hire.',
             'letter' => "Dear CloudNine,\n\nWith 9 years in penetration testing, cloud security, and SOC operations, plus CISSP and CEH certifications, I am uniquely qualified for your Security Engineer role.\n\nBest, Tri Dung Hoang"],

            // My Linh Phan (13) — Frontend
            ['ji' => 9,  'ci' => 13, 'cv' => 'cvs/my_linh_resume.pdf',     'status' => 'interview',
             'note' => 'Strong React + Vue dual expertise. Accessibility focus is valuable. Technical UI test scheduled.',
             'letter' => "Dear CloudNine,\n\nI apply for the Frontend React Developer role. My 4 years with both Vue.js and React, plus strong TypeScript skills, make me a versatile frontend engineer.\n\nBest, My Linh Phan"],
            ['ji' => 14, 'ci' => 13, 'cv' => 'cvs/my_linh_resume.pdf',     'status' => 'reviewed',
             'note' => 'Good frontend profile. React skills confirmed. Consider for client-facing projects.',
             'letter' => "Dear FPT Software,\n\nI apply for the React Frontend Developer role to gain global project exposure. My 4 years building Vue.js and React applications is a strong match.\n\nBest, My Linh Phan"],

            // Quang Vinh Ly (14) — Go/Fintech
            ['ji' => 20, 'ci' => 14, 'cv' => 'cvs/vinh_ly_resume.pdf',     'status' => 'reviewed',
             'note' => 'Excellent Go fintech background. High-throughput API experience ideal for payments. Schedule system design interview.',
             'letter' => "Dear MoMo,\n\nWith 6 years building high-throughput Go fintech APIs processing millions of transactions, I am a natural fit for your Senior Backend (Payments) role.\n\nBest, Quang Vinh Ly"],
            ['ji' => 17, 'ci' => 14, 'cv' => 'cvs/vinh_ly_resume.pdf',     'status' => 'interview',
             'note' => 'Top Go candidate for Zalo. 6 years fintech Go experience. System design interview next Friday.',
             'letter' => "Dear VNG,\n\nBuilding Zalo's backend in Go is a dream role. My 6 years developing high-concurrency Go APIs for fintech platforms perfectly aligns with your requirements.\n\nBest, Quang Vinh Ly"],

            // Bao Long Nguyen (16) — .NET/Azure
            ['ji' => 31, 'ci' => 16, 'cv' => 'cvs/bao_long_resume.pdf',    'status' => 'pending',
             'note' => null,
             'letter' => "Dear NashTech,\n\nWith 7 years in .NET and Azure enterprise solutions serving multinational clients, I am highly aligned with your Full-Stack .NET+Angular requirements.\n\nBest, Bao Long Nguyen"],
            ['ji' => 32, 'ci' => 16, 'cv' => 'cvs/bao_long_resume.pdf',    'status' => 'offered',
             'note' => 'Azure Solutions Architect Expert certified. Exactly what we need. Offered \$2,800/month. Awaiting by Dec 20.',
             'letter' => "Dear NashTech,\n\nMy Azure Solutions Architect Expert certification and 7 years managing enterprise Azure environments for multinational clients align perfectly with your Azure Cloud Engineer role.\n\nBest, Bao Long Nguyen"],

            // Hong Nhung Tran (17) — Flutter/junior
            ['ji' => 24, 'ci' => 17, 'cv' => 'cvs/hong_nhung_resume.pdf',  'status' => 'pending',
             'note' => null,
             'letter' => "Dear TMA Solutions,\n\nI am a junior mobile developer with 1 year Flutter experience, eager to transition to native Android Kotlin development. I am a fast learner with strong fundamentals.\n\nBest, Hong Nhung Tran"],
            ['ji' => 30, 'ci' => 17, 'cv' => 'cvs/hong_nhung_resume.pdf',  'status' => 'interview',
             'note' => 'Enthusiastic junior developer with solid Flutter/Dart skills. Fit well for Axon internship. Interview Monday.',
             'letter' => "Dear Axon Active,\n\nI am a passionate junior developer excited by Axon's Agile culture and real project experience. I would thrive in your internship program.\n\nBest, Hong Nhung Tran"],

            // Thanh Dat Vo (18) — Big Data
            ['ji' => 19, 'ci' => 18, 'cv' => 'cvs/thanh_dat_resume.pdf',   'status' => 'reviewed',
             'note' => 'Exactly the Spark/Kafka expertise we need. Schedule data pipeline architecture interview.',
             'letter' => "Dear VNG,\n\nWith 7 years building Spark and Kafka data pipelines processing terabyte-scale datasets, I am a strong candidate for your Data Engineer role.\n\nBest, Thanh Dat Vo"],

            // Quynh Chi Ngo (19) — Marketing
            ['ji' => 22, 'ci' => 19, 'cv' => 'cvs/chi_ngo_resume.pdf',     'status' => 'pending',
             'note' => null,
             'letter' => "Dear MoMo,\n\nWith 4 years driving growth through SEO, paid ads, and analytics at tech startups, I am excited by the opportunity to grow MoMo's 31M+ user base further.\n\nBest, Quynh Chi Ngo"],

            // Huy Hoang Duong (20) — React/TS
            ['ji' => 9,  'ci' => 20, 'cv' => 'cvs/huy_duong_resume.pdf',   'status' => 'reviewed',
             'note' => 'Strong React/TypeScript dashboard experience. 5 years — good fit. Invite for coding challenge.',
             'letter' => "Dear CloudNine,\n\nI apply for the Frontend React Developer role. My 5 years building data dashboards and design systems in React/TypeScript makes me a great fit for your CloudNine Deploy UI.\n\nBest, Huy Hoang Duong"],
            ['ji' => 14, 'ci' => 20, 'cv' => 'cvs/huy_duong_resume.pdf',   'status' => 'interview',
             'note' => 'Excellent React skills. Meta Front-End certificate confirmed. UI component test scheduled.',
             'letter' => "Dear FPT Software,\n\nMy 5 years React/TypeScript experience and Meta Front-End Developer certification make me a strong candidate for your global projects.\n\nBest, Huy Hoang Duong"],

            // Kim Ngan Nguyen (21) — Junior/intern
            ['ji' => 30, 'ci' => 21, 'cv' => 'cvs/kim_ngan_resume.pdf',    'status' => 'pending',
             'note' => null,
             'letter' => "Dear Axon Active,\n\nAs a 3rd year CS student with strong Python and SQL skills, I am eager to gain real-world experience in your internship program.\n\nBest, Kim Ngan Nguyen"],

            // Thanh Trung Pham (22) — Staff Engineer
            ['ji' => 1,  'ci' => 22, 'cv' => 'cvs/trung_pham_resume.pdf',  'status' => 'pending',
             'note' => null,
             'letter' => "Dear TechCorp,\n\nWith 10 years shipping high-scale backend systems across Java, Go, and Python, I am targeting a senior/staff engineering role that offers technical leadership.\n\nBest, Thanh Trung Pham"],
            ['ji' => 20, 'ci' => 22, 'cv' => 'cvs/trung_pham_resume.pdf',  'status' => 'interview',
             'note' => 'Staff-level candidate with payment systems background. High caliber. CTO has been looped in.',
             'letter' => "Dear MoMo,\n\nAs a staff engineer with Java/Go payment system expertise, I am excited to build the next generation of MoMo's payment infrastructure at scale.\n\nBest, Thanh Trung Pham"],

            // Dieu Linh Truong (24) — PHP/Node
            ['ji' => 23, 'ci' => 24, 'cv' => 'cvs/dieu_linh_resume.pdf',   'status' => 'reviewed',
             'note' => 'Good Laravel + Node combination. Matches TMA requirements. Schedule coding round.',
             'letter' => "Dear TMA Solutions,\n\nWith 3 years building Laravel and Node.js APIs for e-commerce and logistics startups, I am a strong match for your Full-Stack Developer role.\n\nBest, Dieu Linh Truong"],
            ['ji' => 0,  'ci' => 24, 'cv' => 'cvs/dieu_linh_resume.pdf',   'status' => 'pending',
             'note' => null,
             'letter' => "Dear TechCorp,\n\nI apply for the Junior Full-Stack Developer role. My 3 years building Laravel/Node APIs in startup environments has prepared me well for enterprise product development.\n\nBest, Dieu Linh Truong"],
        ];

        foreach ($appsData as $app) {
            // Skip if job or candidate index is out of bounds
            if (!isset($allJobs[$app['ji']]) || !isset($candidateUsers[$app['ci']])) {
                continue;
            }
            // Avoid duplicate applications (same job + candidate)
            $exists = Application::where('job_id', $allJobs[$app['ji']]->id)
                ->where('candidate_id', $candidateUsers[$app['ci']]->id)
                ->exists();
            if ($exists) {
                continue;
            }
            Application::create([
                'job_id'         => $allJobs[$app['ji']]->id,
                'candidate_id'   => $candidateUsers[$app['ci']]->id,
                'cv_file_path'   => $app['cv'],
                'cover_letter'   => $app['letter'],
                'status'         => $app['status'],
                'recruiter_note' => $app['note'],
            ]);
        }

        // =====================================================================
        // 8. SAVED JOBS
        // =====================================================================
        $savedJobsData = [
            // John Doe
            ['ci' => 0, 'ji' => 1],  // Senior PHP
            ['ci' => 0, 'ji' => 6],  // DevOps CloudNine
            ['ci' => 0, 'ji' => 7],  // Node.js CloudNine
            ['ci' => 0, 'ji' => 11], // Java Viettel
            // Jane Smith
            ['ci' => 1, 'ji' => 3],  // UI/UX TechCorp
            ['ci' => 1, 'ji' => 9],  // Frontend React CloudNine
            ['ci' => 1, 'ji' => 29], // Product Designer Axon
            ['ci' => 1, 'ji' => 15], // QA FPT
            // Alex Nguyen
            ['ci' => 2, 'ji' => 6],  // DevOps CloudNine
            ['ci' => 2, 'ji' => 10], // ML CloudNine
            ['ci' => 2, 'ji' => 12], // AI Viettel
            ['ci' => 2, 'ji' => 27], // Python KMS
            // Emily Tran
            ['ci' => 3, 'ji' => 0],  // Junior TechCorp
            ['ci' => 3, 'ji' => 30], // Intern Axon
            ['ci' => 3, 'ji' => 14], // React FPT
            // Michael Le
            ['ci' => 4, 'ji' => 4],  // Tech PM TechCorp
            ['ci' => 4, 'ji' => 6],  // DevOps CloudNine
            ['ci' => 4, 'ji' => 32], // Azure NashTech
            // Thanh Huong Vo
            ['ci' => 5, 'ji' => 2],  // Data Analyst TechCorp
            ['ci' => 5, 'ji' => 10], // ML CloudNine
            ['ci' => 5, 'ji' => 19], // Data Engineer VNG
            // Duc Khoa Pham
            ['ci' => 6, 'ji' => 11], // Java Viettel
            ['ci' => 6, 'ji' => 20], // Senior BE MoMo
            // Minh Tien Do
            ['ci' => 8, 'ji' => 6],  // DevOps CloudNine
            ['ci' => 8, 'ji' => 13], // Cloud Infra Viettel
            ['ci' => 8, 'ji' => 32], // Azure NashTech
            // Kim Oanh Dang
            ['ci' => 9, 'ji' => 26], // Senior QA KMS
            ['ci' => 9, 'ji' => 5],  // QA TechCorp
            // Quang Vinh Ly
            ['ci' => 14, 'ji' => 17], // Go VNG
            ['ci' => 14, 'ji' => 20], // Senior BE MoMo
        ];

        foreach ($savedJobsData as $sv) {
            if (!isset($allJobs[$sv['ji']]) || !isset($candidateUsers[$sv['ci']])) {
                continue;
            }
            $exists = SavedJob::where('user_id', $candidateUsers[$sv['ci']]->id)
                ->where('job_id', $allJobs[$sv['ji']]->id)
                ->exists();
            if (!$exists) {
                SavedJob::create([
                    'user_id' => $candidateUsers[$sv['ci']]->id,
                    'job_id'  => $allJobs[$sv['ji']]->id,
                ]);
            }
        }
    }
}
