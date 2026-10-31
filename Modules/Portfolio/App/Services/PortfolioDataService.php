<?php

namespace Modules\Portfolio\App\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Modules\Portfolio\App\Models\ModuleMedia;
use Modules\Portfolio\App\Models\ModuleMenu;
use Modules\Portfolio\App\Models\ModulePage;
use Modules\Portfolio\App\Models\ModuleSetting;
use Modules\Portfolio\App\Models\ModuleSlider;
use Modules\Portfolio\App\Models\ModuleTheme;
use Modules\Portfolio\App\Models\ModuleUser;
use Modules\Portfolio\App\Models\PortfolioBlog;
use Modules\Portfolio\App\Models\PortfolioEducation;
use Modules\Portfolio\App\Models\PortfolioExperience;
use Modules\Portfolio\App\Models\PortfolioFaq;
use Modules\Portfolio\App\Models\PortfolioMessage;
use Modules\Portfolio\App\Models\PortfolioProject;
use Modules\Portfolio\App\Models\PortfolioService;
use Modules\Portfolio\App\Models\PortfolioSkill;
use Modules\Portfolio\App\Models\PortfolioSubscriber;
use Modules\Portfolio\App\Models\PortfolioTestimonial;

class PortfolioDataService
{
    /**
     * Get merged settings & section content from Config/defaults.php + port_settings overrides.
     */
    public function getSettingsData(): array
    {
        $defaults = require module_path('Portfolio', 'Config/defaults.php');

        try {
            if (!Schema::hasTable('port_settings')) {
                return $defaults;
            }

            $raw = ModuleSetting::getValue('theme_sections_json', '');
            if (!empty($raw)) {
                $custom = json_decode($raw, true);
                if (is_array($custom)) {
                    $defaults = array_replace_recursive($defaults, $custom);
                }
            }
        } catch (\Throwable $e) {
            // Return defaults safely
        }

        return $defaults;
    }

    /**
     * Ensure all port_* tables are seeded with the default reference data if empty.
     */
    public function ensureSeeded(): void
    {
        try {
            if (!Schema::hasTable('port_projects')) {
                return;
            }

            // 1. Seed Skills (15 skills across 5 categories, first 8 match reference image)
            if (PortfolioSkill::count() === 0) {
                $skills = [
                    ['name' => 'Laravel', 'subtitle' => 'PHP Framework', 'category' => 'Backend', 'icon_class' => 'fa-brands fa-laravel', 'icon_color' => '#FF2D20', 'badge_text' => 'L', 'level_percent' => 98, 'sort_order' => 1],
                    ['name' => 'Next.js', 'subtitle' => 'React Framework', 'category' => 'Frontend', 'icon_class' => 'fa-solid fa-n', 'icon_color' => '#FFFFFF', 'badge_text' => 'N', 'level_percent' => 94, 'sort_order' => 2],
                    ['name' => 'Tailwind CSS', 'subtitle' => 'Utility CSS', 'category' => 'Frontend', 'icon_class' => 'fa-solid fa-wind', 'icon_color' => '#38BDF8', 'badge_text' => 'TW', 'level_percent' => 96, 'sort_order' => 3],
                    ['name' => 'JavaScript', 'subtitle' => 'Programming', 'category' => 'Frontend', 'icon_class' => 'fa-brands fa-js', 'icon_color' => '#FACC15', 'badge_text' => 'JS', 'level_percent' => 95, 'sort_order' => 4],
                    ['name' => 'MySQL', 'subtitle' => 'Database', 'category' => 'Database', 'icon_class' => 'fa-solid fa-database', 'icon_color' => '#00A3E0', 'badge_text' => 'SQL', 'level_percent' => 95, 'sort_order' => 5],
                    ['name' => 'Git & GitHub', 'subtitle' => 'Version Control', 'category' => 'Tools', 'icon_class' => 'fa-brands fa-github', 'icon_color' => '#FFFFFF', 'badge_text' => 'GIT', 'level_percent' => 96, 'sort_order' => 6],
                    ['name' => 'Docker', 'subtitle' => 'Containerization', 'category' => 'Tools', 'icon_class' => 'fa-brands fa-docker', 'icon_color' => '#2496ED', 'badge_text' => 'DK', 'level_percent' => 88, 'sort_order' => 7],
                    ['name' => 'UI/UX Design', 'subtitle' => 'Web Design', 'category' => 'Design', 'icon_class' => 'fa-brands fa-figma', 'icon_color' => '#F24E1E', 'badge_text' => 'UX', 'level_percent' => 92, 'sort_order' => 8],
                    ['name' => 'PHP', 'subtitle' => 'Backend Language', 'category' => 'Backend', 'icon_class' => 'fa-brands fa-php', 'icon_color' => '#777BB4', 'badge_text' => 'PHP', 'level_percent' => 98, 'sort_order' => 9],
                    ['name' => 'React', 'subtitle' => 'Frontend Library', 'category' => 'Frontend', 'icon_class' => 'fa-brands fa-react', 'icon_color' => '#61DAFB', 'badge_text' => 'RC', 'level_percent' => 94, 'sort_order' => 10],
                    ['name' => 'PostgreSQL', 'subtitle' => 'Relational Database', 'category' => 'Database', 'icon_class' => 'fa-solid fa-server', 'icon_color' => '#336791', 'badge_text' => 'PG', 'level_percent' => 90, 'sort_order' => 11],
                    ['name' => 'HTML5', 'subtitle' => 'Semantic Web', 'category' => 'Frontend', 'icon_class' => 'fa-brands fa-html5', 'icon_color' => '#E34F26', 'badge_text' => 'H5', 'level_percent' => 99, 'sort_order' => 12],
                    ['name' => 'CSS3', 'subtitle' => 'Visual Styling', 'category' => 'Frontend', 'icon_class' => 'fa-brands fa-css3-alt', 'icon_color' => '#1572B6', 'badge_text' => 'C3', 'level_percent' => 98, 'sort_order' => 13],
                    ['name' => 'Bootstrap', 'subtitle' => 'UI Toolkit', 'category' => 'Frontend', 'icon_class' => 'fa-brands fa-bootstrap', 'icon_color' => '#7952B3', 'badge_text' => 'BS', 'level_percent' => 97, 'sort_order' => 14],
                    ['name' => 'REST API', 'subtitle' => 'API Architecture', 'category' => 'Backend', 'icon_class' => 'fa-solid fa-network-wired', 'icon_color' => '#10B981', 'badge_text' => 'API', 'level_percent' => 96, 'sort_order' => 15],
                ];
                foreach ($skills as $s) {
                    PortfolioSkill::create(array_merge($s, ['is_active' => true]));
                }
            }

            // 2. Seed Services (10 Services, first 5 match reference image cards)
            if (PortfolioService::count() === 0) {
                $services = [
                    [
                        'title' => 'Web Development',
                        'slug' => 'web-development',
                        'subtitle' => 'Custom Websites & Web Apps',
                        'short_description' => 'Custom Websites & Web Apps engineered for speed, scalability, and high conversion.',
                        'description' => 'End-to-end full-stack web development tailored to your business needs. From high-converting corporate landing pages to complex multi-tenant platforms.',
                        'icon_class' => 'fa-solid fa-code',
                        'features' => ['Responsive Across All Devices', 'SEO & Core Web Vitals Optimized', 'Clean Modular Codebase', 'Fast Cloud Deployment'],
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'E-commerce',
                        'slug' => 'ecommerce-development',
                        'subtitle' => 'Online Store Solutions',
                        'short_description' => 'Online Store Solutions with multi-vendor, inventory, and secure payment gateways.',
                        'description' => 'High-converting online stores with cart optimization, SSL payment gateways (Stripe, SSLCommerz, bKash), real-time inventory tracking, and order management.',
                        'icon_class' => 'fa-solid fa-cart-shopping',
                        'features' => ['Payment Gateway Integration', 'Inventory & Order Tracking', 'Customer Loyalty & Coupons', 'Mobile-First Checkout'],
                        'sort_order' => 2,
                    ],
                    [
                        'title' => 'CMS Development',
                        'slug' => 'cms-development',
                        'subtitle' => 'WordPress & Laravel',
                        'short_description' => 'WordPress & Laravel custom content management systems with intuitive admin panels.',
                        'description' => 'Dynamic, easy-to-manage CMS solutions built with Laravel or WordPress so your team can update pages, media, menus, and blogs without writing code.',
                        'icon_class' => 'fa-brands fa-wordpress-simple',
                        'features' => ['Custom Admin Dashboards', 'Role-Based Access Control', 'Drag & Drop Media Library', 'Dynamic SEO Controls'],
                        'sort_order' => 3,
                    ],
                    [
                        'title' => 'API Integration',
                        'slug' => 'api-integration',
                        'subtitle' => 'Third Party APIs',
                        'short_description' => 'Third Party APIs, payment gateways, CRM, SMS, and microservices architecture.',
                        'description' => 'RESTful and GraphQL API development and seamless integration with payment processors, shipping carriers, ERPs, and mobile apps.',
                        'icon_class' => 'fa-solid fa-gear',
                        'features' => ['RESTful & Webhook Architecture', 'OAuth2 & JWT Authentication', 'Payment & SMS Gateways', 'Comprehensive API Docs'],
                        'sort_order' => 4,
                    ],
                    [
                        'title' => 'UI/UX Design',
                        'slug' => 'ui-ux-design',
                        'subtitle' => 'Modern & Responsive',
                        'short_description' => 'Modern & Responsive user interfaces crafted with glassmorphism and clean hierarchy.',
                        'description' => 'User-centric interface design combining modern typography, accessible color contrast, interactive Figma prototypes, and pixel-perfect frontend implementation.',
                        'icon_class' => 'fa-solid fa-palette',
                        'features' => ['Interactive Figma Prototypes', 'Design Systems & Tokens', 'Light & Dark Mode UI', 'Conversion Rate Optimization'],
                        'sort_order' => 5,
                    ],
                    [
                        'title' => 'Laravel Development',
                        'slug' => 'laravel-development',
                        'subtitle' => 'Enterprise PHP Framework',
                        'short_description' => 'Enterprise Laravel applications, modular SaaS platforms, and queue-driven backends.',
                        'description' => 'Specialized Laravel architecture using HMVC modules, Eloquent optimization, Horizon queues, and automated testing.',
                        'icon_class' => 'fa-brands fa-laravel',
                        'features' => ['Modular HMVC Architecture', 'Database Query Optimization', 'Real-Time Broadcasting', 'Automated PHPUnit Tests'],
                        'sort_order' => 6,
                    ],
                    [
                        'title' => 'Custom Web Application',
                        'slug' => 'custom-web-application',
                        'subtitle' => 'Bespoke SaaS & Portals',
                        'short_description' => 'Tailor-made web portals, booking engines, and multi-tenant SaaS products.',
                        'description' => 'Custom-engineered web applications built from scratch to solve complex operational workflows.',
                        'icon_class' => 'fa-solid fa-layer-group',
                        'features' => ['Multi-Tenant Architecture', 'Custom Reporting & Analytics', 'Cloud Storage Integration', 'High Concurrency Ready'],
                        'sort_order' => 7,
                    ],
                    [
                        'title' => 'POS & ERP Software',
                        'slug' => 'pos-erp-software',
                        'subtitle' => 'Retail & Enterprise ERP',
                        'short_description' => 'Cloud POS, accounting, HRM, and supply-chain management systems.',
                        'description' => 'Complete business management software for retail, pharmacies, and enterprises with barcode scanning, invoicing, and financial ledgers.',
                        'icon_class' => 'fa-solid fa-cash-register',
                        'features' => ['Barcode & Thermal Receipt', 'Multi-Warehouse Stock', 'Profit & Loss Reports', 'Staff Role Permissions'],
                        'sort_order' => 8,
                    ],
                    [
                        'title' => 'Website Maintenance',
                        'slug' => 'website-maintenance',
                        'subtitle' => '24/7 Care & Security',
                        'short_description' => 'Ongoing security patches, automated cloud backups, and speed tuning.',
                        'description' => 'Keep your web applications fast, secure, and always online with proactive monitoring and SLA support.',
                        'icon_class' => 'fa-solid fa-shield-halved',
                        'features' => ['Daily Offsite Backups', 'Malware & Firewall Protection', 'Uptime Monitoring', 'Priority Bug Fixes'],
                        'sort_order' => 9,
                    ],
                    [
                        'title' => 'Business Automation',
                        'slug' => 'business-automation',
                        'subtitle' => 'Workflow & AI Automation',
                        'short_description' => 'Automate repetitive tasks, invoicing, notifications, and CRM pipelines.',
                        'description' => 'Eliminate manual bottlenecks by connecting your business tools with automated cron jobs, webhooks, and custom dashboards.',
                        'icon_class' => 'fa-solid fa-wand-magic-sparkles',
                        'features' => ['Automated Email & WhatsApp Alerts', 'PDF Invoice Generation', 'CRM Pipeline Sync', 'Custom Scheduled Reports'],
                        'sort_order' => 10,
                    ],
                ];
                foreach ($services as $srv) {
                    PortfolioService::create(array_merge($srv, ['is_active' => true]));
                }
            }

            // 3. Seed Portfolio Projects (8 Projects across all categories, first 4 match reference image 100%)
            if (PortfolioProject::count() === 0) {
                $projects = [
                    [
                        'title' => 'Real Estate Website',
                        'slug' => 'real-estate-website',
                        'category' => 'Real Estate Website',
                        'subtitle' => 'Property & Home',
                        'thumbnail' => '/module-media/Portfolio/theme/project-1.jpg',
                        'gallery_images' => [
                            '/module-media/Portfolio/theme/project-1.jpg',
                            '/module-media/Portfolio/theme/project-3.jpg',
                            '/module-media/Portfolio/theme/blog-1.jpg',
                        ],
                        'short_description' => 'Luxury property listing portal with interactive map search, virtual tours, and agent booking.',
                        'description' => "A comprehensive luxury Real Estate platform built with Laravel and Next.js. Features advanced property filtering by location, price, and amenities, interactive map integration, 360-degree virtual tour embeds, mortgage calculators, and a dedicated real-estate agent CRM dashboard.",
                        'technologies' => ['Laravel', 'React', 'MySQL', 'Tailwind CSS', 'REST API'],
                        'client_name' => 'Dream Home Properties Ltd.',
                        'live_url' => 'https://example.com/real-estate',
                        'github_url' => 'https://github.com',
                        'completion_date' => 'October 2025',
                        'is_featured' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'Fashion Store',
                        'slug' => 'fashion-store-ecommerce',
                        'category' => 'E-commerce Website',
                        'subtitle' => 'Ecommerce',
                        'thumbnail' => '/module-media/Portfolio/theme/project-2.jpg',
                        'gallery_images' => [
                            '/module-media/Portfolio/theme/project-2.jpg',
                            '/module-media/Portfolio/theme/project-4.jpg',
                        ],
                        'short_description' => 'Modern multi-category fashion e-commerce platform with instant checkout and live inventory.',
                        'description' => "An ultra-fast fashion e-commerce store featuring dynamic lookbooks, size/color variant matrices, wishlist synchronization, instant AJAX cart drawer, multi-currency checkout, and automated order tracking notifications.",
                        'technologies' => ['Next.js', 'Laravel', 'Tailwind CSS', 'PostgreSQL', 'Docker'],
                        'client_name' => 'VogueThreads Apparel',
                        'live_url' => 'https://example.com/fashion-store',
                        'github_url' => 'https://github.com',
                        'completion_date' => 'September 2025',
                        'is_featured' => true,
                        'sort_order' => 2,
                    ],
                    [
                        'title' => 'Company Website',
                        'slug' => 'company-corporate-website',
                        'category' => 'Corporate Website',
                        'subtitle' => 'Business & Corporate',
                        'thumbnail' => '/module-media/Portfolio/theme/project-3.jpg',
                        'gallery_images' => [
                            '/module-media/Portfolio/theme/project-3.jpg',
                            '/module-media/Portfolio/theme/project-1.jpg',
                        ],
                        'short_description' => 'Enterprise corporate website with modular CMS, investor relations portal, and case studies.',
                        'description' => "Designed and engineered a flagship corporate web presence for a multinational consultancy. Includes a custom drag-and-drop page builder, multi-language localization, interactive service configurator, and high-speed CDN asset delivery.",
                        'technologies' => ['Laravel', 'PHP', 'Bootstrap', 'MySQL', 'JavaScript'],
                        'client_name' => 'NexaCorp Global Consulting',
                        'live_url' => 'https://example.com/company-website',
                        'github_url' => 'https://github.com',
                        'completion_date' => 'August 2025',
                        'is_featured' => true,
                        'sort_order' => 3,
                    ],
                    [
                        'title' => 'Restaurant Website',
                        'slug' => 'restaurant-booking-website',
                        'category' => 'Business Website',
                        'subtitle' => 'Food & Restaurant',
                        'thumbnail' => '/module-media/Portfolio/theme/project-4.jpg',
                        'gallery_images' => [
                            '/module-media/Portfolio/theme/project-4.jpg',
                            '/module-media/Portfolio/theme/project-2.jpg',
                        ],
                        'short_description' => 'Fine-dining digital menu, online food ordering, and real-time table reservation system.',
                        'description' => "A sleek digital experience for a premier restaurant group. Customers can browse interactive chef menus with dietary filters, reserve tables in real time, and order delivery directly with live kitchen status updates.",
                        'technologies' => ['Laravel', 'React', 'Tailwind CSS', 'MySQL', 'REST API'],
                        'client_name' => 'Artisan Bistro & Grill',
                        'live_url' => 'https://example.com/restaurant',
                        'github_url' => 'https://github.com',
                        'completion_date' => 'July 2025',
                        'is_featured' => true,
                        'sort_order' => 4,
                    ],
                    [
                        'title' => 'MediCare Pharmacy ERP',
                        'slug' => 'medicare-pharmacy-management',
                        'category' => 'Pharmacy Management',
                        'subtitle' => 'Healthcare & Medicine',
                        'thumbnail' => '/module-media/Portfolio/theme/project-5.jpg',
                        'gallery_images' => ['/module-media/Portfolio/theme/project-5.jpg'],
                        'short_description' => 'Online medicine catalog, prescription upload, expiry alerts, and pharmacy billing software.',
                        'description' => "Complete pharmacy management and online medicine delivery portal with batch expiry tracking, prescription verification workflow, and POS thermal billing.",
                        'technologies' => ['Laravel', 'PHP', 'MySQL', 'Bootstrap', 'JavaScript'],
                        'client_name' => 'MediCare Plus Healthcare',
                        'live_url' => 'https://example.com/pharmacy',
                        'github_url' => 'https://github.com',
                        'completion_date' => 'June 2025',
                        'is_featured' => false,
                        'sort_order' => 5,
                    ],
                    [
                        'title' => 'OmniPOS & Inventory ERP',
                        'slug' => 'omnipos-inventory-erp',
                        'category' => 'POS & ERP Software',
                        'subtitle' => 'POS & ERP Software',
                        'thumbnail' => '/module-media/Portfolio/theme/project-6.jpg',
                        'gallery_images' => ['/module-media/Portfolio/theme/project-6.jpg'],
                        'short_description' => 'Multi-branch cloud POS, barcode invoicing, supplier ledger, and automated financial reporting.',
                        'description' => "Enterprise resource planning and point-of-sale software engineered for high-volume retail chains with real-time warehouse synchronization.",
                        'technologies' => ['Laravel', 'React', 'PostgreSQL', 'Docker', 'REST API'],
                        'client_name' => 'FinEdge Retail Systems',
                        'live_url' => 'https://example.com/pos-erp',
                        'github_url' => 'https://github.com',
                        'completion_date' => 'May 2025',
                        'is_featured' => false,
                        'sort_order' => 6,
                    ],
                    [
                        'title' => 'MetroChronicle News Portal',
                        'slug' => 'metrochronicle-news-portal',
                        'category' => 'News Portal',
                        'subtitle' => 'Digital Media & News',
                        'thumbnail' => '/module-media/Portfolio/theme/project-7.jpg',
                        'gallery_images' => ['/module-media/Portfolio/theme/project-7.jpg'],
                        'short_description' => 'High-traffic online newspaper with breaking news ticker, epaper viewer, and journalist workflow.',
                        'description' => "Built for millions of monthly pageviews with Redis full-page caching, automated Open Graph card generation, and multi-editor publishing queues.",
                        'technologies' => ['Next.js', 'Laravel', 'MySQL', 'Tailwind CSS'],
                        'client_name' => 'Metro Media Group',
                        'live_url' => 'https://example.com/news-portal',
                        'github_url' => 'https://github.com',
                        'completion_date' => 'April 2025',
                        'is_featured' => false,
                        'sort_order' => 7,
                    ],
                    [
                        'title' => 'FlowSync SaaS Workspace',
                        'slug' => 'flowsync-custom-web-app',
                        'category' => 'Custom Web Application',
                        'subtitle' => 'SaaS Web Application',
                        'thumbnail' => '/module-media/Portfolio/theme/project-8.jpg',
                        'gallery_images' => ['/module-media/Portfolio/theme/project-8.jpg'],
                        'short_description' => 'Multi-tenant team collaboration, Kanban boards, time tracking, and client invoicing SaaS.',
                        'description' => "A full-featured SaaS application with real-time WebSockets, Kanban task boards, automated recurring billing, and team analytics.",
                        'technologies' => ['Laravel', 'Next.js', 'Tailwind CSS', 'PostgreSQL', 'Docker'],
                        'client_name' => 'FlowSync Inc.',
                        'live_url' => 'https://example.com/flowsync',
                        'github_url' => 'https://github.com',
                        'completion_date' => 'March 2025',
                        'is_featured' => false,
                        'sort_order' => 8,
                    ],
                ];

                foreach ($projects as $p) {
                    PortfolioProject::create(array_merge($p, ['is_active' => true]));
                }
            }

            // 4. Seed Career Experience Timeline (Exact 4 steps from reference image)
            if (PortfolioExperience::count() === 0) {
                $experiences = [
                    [
                        'period' => '2019 - 2020',
                        'job_title' => 'Junior Developer',
                        'company_name' => 'CodeCraft Digital',
                        'employment_type' => 'Full-Time',
                        'description' => 'Started my career in web development.',
                        'icon_class' => 'fa-solid fa-code',
                        'sort_order' => 1,
                    ],
                    [
                        'period' => '2020 - 2022',
                        'job_title' => 'Full Stack Developer',
                        'company_name' => 'WebPro Studio',
                        'employment_type' => 'Full-Time',
                        'description' => 'Worked on various Laravel and React projects.',
                        'icon_class' => 'fa-solid fa-laptop-code',
                        'sort_order' => 2,
                    ],
                    [
                        'period' => '2022 - 2024',
                        'job_title' => 'Senior Developer',
                        'company_name' => 'DreamTech Ltd.',
                        'employment_type' => 'Full-Time',
                        'description' => 'Led projects and mentored junior developers.',
                        'icon_class' => 'fa-solid fa-briefcase',
                        'sort_order' => 3,
                    ],
                    [
                        'period' => '2024 - Present',
                        'job_title' => 'Freelance Developer',
                        'company_name' => 'Global Clients',
                        'employment_type' => 'Self-Employed',
                        'description' => 'Building innovative solutions for global clients.',
                        'icon_class' => 'fa-solid fa-globe',
                        'sort_order' => 4,
                    ],
                ];
                foreach ($experiences as $exp) {
                    PortfolioExperience::create(array_merge($exp, ['is_active' => true]));
                }
            }

            // 5. Seed Education & Certifications
            if (PortfolioEducation::count() === 0) {
                $educations = [
                    [
                        'type' => 'Education',
                        'title' => 'B.Sc. in Computer Science & Engineering',
                        'institution' => 'University of Science & Technology',
                        'completion_date' => '2019',
                        'description' => 'Graduated with honors specializing in Software Engineering, Database Systems, and Web Technologies.',
                        'certificate_url' => '#',
                        'sort_order' => 1,
                    ],
                    [
                        'type' => 'Certification',
                        'title' => 'Certified Laravel Senior Architect',
                        'institution' => 'Laravel Certification Program',
                        'completion_date' => '2023',
                        'description' => 'Verified expertise in enterprise Laravel architecture, security, testing, and Eloquent performance.',
                        'certificate_url' => '#',
                        'sort_order' => 2,
                    ],
                    [
                        'type' => 'Certification',
                        'title' => 'Full-Stack Next.js & React Professional',
                        'institution' => 'Meta / Coursera',
                        'completion_date' => '2024',
                        'description' => 'Advanced certification in Server Components, TypeScript, Tailwind CSS, and modern frontend architecture.',
                        'certificate_url' => '#',
                        'sort_order' => 3,
                    ],
                ];
                foreach ($educations as $edu) {
                    PortfolioEducation::create(array_merge($edu, ['is_active' => true]));
                }
            }

            // 6. Seed Testimonials (Exact 3 cards from reference image)
            if (PortfolioTestimonial::count() === 0) {
                $testimonials = [
                    [
                        'client_name' => 'Sarah Khan',
                        'designation' => 'CEO',
                        'company' => 'DreamTech',
                        'review_text' => 'Alex delivered our project beyond expectations. His communication and code quality are outstanding!',
                        'rating' => 5,
                        'avatar' => '/module-media/Portfolio/theme/avatar-1.jpg',
                        'sort_order' => 1,
                    ],
                    [
                        'client_name' => 'David Miller',
                        'designation' => 'Founder',
                        'company' => 'WebPro',
                        'review_text' => 'Professional, skilled and always on time. Highly recommended!',
                        'rating' => 5,
                        'avatar' => '/module-media/Portfolio/theme/avatar-2.jpg',
                        'sort_order' => 2,
                    ],
                    [
                        'client_name' => 'Rashed Karim',
                        'designation' => 'Product Manager',
                        'company' => 'NexaCorp',
                        'review_text' => 'A true professional. Great work and very supportive.',
                        'rating' => 5,
                        'avatar' => '/module-media/Portfolio/theme/avatar-3.jpg',
                        'sort_order' => 3,
                    ],
                ];
                foreach ($testimonials as $testi) {
                    PortfolioTestimonial::create(array_merge($testi, ['is_active' => true]));
                }
            }

            // 7. Seed Blog Articles (Exact 3 articles from reference image)
            if (PortfolioBlog::count() === 0) {
                $blogs = [
                    [
                        'title' => '10 Laravel Tips for Better Performance',
                        'slug' => '10-laravel-tips-for-better-performance',
                        'category' => 'Laravel',
                        'published_date' => 'Oct 20, 2025',
                        'short_description' => 'Discover proven Eloquent optimization techniques, eager loading strategies, Redis caching, and Octane tips for sub-50ms responses.',
                        'content' => "<p>Building high-performance Laravel applications requires careful attention to database queries, caching layers, and memory management. In this comprehensive guide, we explore 10 battle-tested techniques used in production enterprise systems.</p><h4>1. Prevent N+1 Queries with Strict Eloquent</h4><p>Always enable <code>Model::preventLazyLoading(!app()->isProduction())</code> and use eager loading with specific column selection to minimize memory overhead.</p><h4>2. Index Foreign Keys & Composite Filters</h4><p>Analyze slow queries with <code>EXPLAIN</code> and add composite indexes matching your most frequent <code>WHERE</code> and <code>ORDER BY</code> clauses.</p><h4>3. Leverage Redis for Query & View Caching</h4><p>Cache expensive dashboard aggregations using tagged Redis caches and invalidate them cleanly via Model Observers.</p>",
                        'image' => '/module-media/Portfolio/theme/blog-1.jpg',
                        'views' => 1420,
                        'is_featured' => true,
                    ],
                    [
                        'title' => "Next.js 15: What's New?",
                        'slug' => 'nextjs-15-whats-new',
                        'category' => 'Next.js',
                        'published_date' => 'Oct 20, 2025',
                        'short_description' => 'A deep dive into Next.js 15 Server Actions, Turbopack stability, React 19 support, and refined caching semantics.',
                        'content' => "<p>Next.js 15 introduces game-changing improvements for full-stack React developers. From lightning-fast Turbopack builds to explicit caching controls and enhanced Server Actions, here is everything you need to know.</p><h4>1. Turbopack in Development & Production</h4><p>Compile times are up to 76% faster, making local iteration instantaneous even on large monorepos.</p><h4>2. Predictable Caching Semantics</h4><p>Fetch requests and Route Handlers are now uncached by default, giving developers full control over static vs dynamic rendering.</p>",
                        'image' => '/module-media/Portfolio/theme/blog-2.jpg',
                        'views' => 980,
                        'is_featured' => true,
                    ],
                    [
                        'title' => 'Modern UI Design Trends 2026',
                        'slug' => 'modern-ui-design-trends-2026',
                        'category' => 'UI/UX Design',
                        'published_date' => 'Oct 12, 2025',
                        'short_description' => 'Exploring electric glow aesthetics, glassmorphism, dual dark/light adaptive themes, and serif + sans-serif typography pairings.',
                        'content' => "<p>Great user interface design in 2026 blends high-contrast clarity with atmospheric depth. Combining deep navy canvases, electric blue accents, and organic wave transitions creates memorable digital experiences that stand out.</p><h4>1. Adaptive Dark & Light Theme Systems</h4><p>Modern users expect seamless switching between high-contrast dark modes and crisp daylight themes powered by CSS custom properties.</p><h4>2. Editorial Serif + Geometric Sans Pairing</h4><p>Pairing expressive serif display headlines with clean geometric body typography elevates authority and readability.</p>",
                        'image' => '/module-media/Portfolio/theme/blog-3.jpg',
                        'views' => 1150,
                        'is_featured' => true,
                    ],
                ];
                foreach ($blogs as $b) {
                    PortfolioBlog::create(array_merge($b, ['is_active' => true]));
                }
            }

            // 8. Seed FAQs (Exact 6 questions from user prompt)
            if (PortfolioFaq::count() === 0) {
                $faqs = [
                    [
                        'question' => 'What services do you provide?',
                        'answer' => 'I provide end-to-end web development services including Custom Laravel Development, E-commerce Websites, Next.js/React Applications, POS & ERP Software, CMS Development, API Integration, UI/UX Design, and Ongoing Website Maintenance.',
                        'category' => 'General',
                        'sort_order' => 1,
                    ],
                    [
                        'question' => 'Do you develop custom Laravel applications?',
                        'answer' => 'Yes! Laravel is my primary backend specialty. I build modular, scalable, and secure custom Laravel applications, SaaS platforms, multi-website hubs, and enterprise ERPs with clean architecture.',
                        'category' => 'Technical',
                        'sort_order' => 2,
                    ],
                    [
                        'question' => 'Can you develop e-commerce websites?',
                        'answer' => 'Absolutely. I build high-converting single-vendor and multi-vendor e-commerce platforms with secure payment gateways (Stripe, SSLCommerz, bKash), real-time inventory, and fast mobile checkout.',
                        'category' => 'Services',
                        'sort_order' => 3,
                    ],
                    [
                        'question' => 'Do you provide ongoing maintenance?',
                        'answer' => 'Yes, I offer dedicated long-term support and maintenance packages covering security updates, daily backups, performance optimization, server monitoring, and new feature development.',
                        'category' => 'Support',
                        'sort_order' => 4,
                    ],
                    [
                        'question' => 'How can I start a project?',
                        'answer' => 'Simply fill out the contact form below or click the "Let\'s Talk" button to share your project idea. We will schedule a free discovery call to discuss your requirements, timeline, and budget.',
                        'category' => 'Process',
                        'sort_order' => 5,
                    ],
                    [
                        'question' => 'What is your project development process?',
                        'answer' => 'I follow a structured 7-step agile workflow: 1) Requirement Analysis, 2) Planning & Architecture, 3) UI/UX Design, 4) Clean Development, 5) QA & Testing, 6) Deployment & Handover, and 7) Ongoing Support.',
                        'category' => 'Process',
                        'sort_order' => 6,
                    ],
                ];
                foreach ($faqs as $faq) {
                    PortfolioFaq::create(array_merge($faq, ['is_active' => true]));
                }
            }

            // 9. Seed Sample Contact Messages & Subscribers
            if (PortfolioMessage::count() === 0) {
                PortfolioMessage::create([
                    'name' => 'Sarah Khan',
                    'email' => 'sarah@dreamtech.io',
                    'phone' => '+880 1711-223344',
                    'subject' => 'Enterprise Laravel SaaS Development Inquiry',
                    'message' => 'Hi Alex, we loved your recent portfolio projects and would like to discuss building a custom multi-tenant SaaS platform.',
                    'status' => 'Read',
                ]);
            }

            if (PortfolioSubscriber::count() === 0) {
                PortfolioSubscriber::create([
                    'email' => 'david@webpro.dev',
                    'status' => 'Subscribed',
                ]);
            }

            // 10. Seed Standard Global CMS Tables (port_pages, port_menus, port_sliders, port_media, port_themes, port_users, port_settings)
            if (ModulePage::count() === 0) {
                ModulePage::create(['title' => 'Home Portfolio', 'slug' => 'home', 'content' => 'Developer Portfolio Homepage', 'status' => 'Published', 'views' => 2450]);
                ModulePage::create(['title' => 'All Portfolio Projects', 'slug' => 'portfolio', 'content' => 'Complete showcase of web development projects.', 'status' => 'Published', 'views' => 1680]);
                ModulePage::create(['title' => 'Developer Blog & Articles', 'slug' => 'blog', 'content' => 'Technical articles on Laravel, Next.js, and UI/UX.', 'status' => 'Published', 'views' => 1190]);
            }

            if (ModuleMenu::count() === 0) {
                $menus = [
                    ['title' => 'Home', 'url' => '#home', 'location' => 'header', 'sort_order' => 1],
                    ['title' => 'About', 'url' => '#about', 'location' => 'header', 'sort_order' => 2],
                    ['title' => 'Services', 'url' => '#services', 'location' => 'header', 'sort_order' => 3],
                    ['title' => 'Portfolio', 'url' => '#portfolio', 'location' => 'header', 'sort_order' => 4],
                    ['title' => 'Blog', 'url' => '#blog', 'location' => 'header', 'sort_order' => 5],
                    ['title' => 'Testimonials', 'url' => '#testimonials', 'location' => 'header', 'sort_order' => 6],
                    ['title' => 'Contact', 'url' => '#contact', 'location' => 'header', 'sort_order' => 7],
                ];
                foreach ($menus as $m) {
                    ModuleMenu::create(array_merge($m, ['target' => '_self', 'is_active' => true]));
                }
            }

            if (ModuleSlider::count() === 0) {
                ModuleSlider::create([
                    'title' => 'Web Developer Crafting Premium Digital Experiences',
                    'subtitle' => 'I build modern, fast and scalable web applications that help businesses grow and make an impact.',
                    'button_text' => 'View My Work',
                    'button_url' => '#portfolio',
                    'image' => '/module-media/Portfolio/theme/hero-developer-cutout.png',
                    'sort_order' => 1,
                    'is_active' => true,
                ]);
            }

            if (ModuleMedia::count() === 0) {
                ModuleMedia::create([
                    'title' => 'Developer Hero Cutout Portrait',
                    'category' => 'hero',
                    'file_path' => '/module-media/Portfolio/theme/hero-developer-cutout.png',
                    'file_type' => 'image/png',
                    'file_size' => '320 KB',
                    'alt_text' => 'Alex Morgan Web Developer Portrait',
                ]);
                ModuleMedia::create([
                    'title' => 'Developer Workspace & Clean Code Books',
                    'category' => 'about',
                    'file_path' => '/module-media/Portfolio/theme/about-workspace.jpg',
                    'file_type' => 'image/jpeg',
                    'file_size' => '210 KB',
                    'alt_text' => 'Developer Workspace',
                ]);
            }

            if (ModuleTheme::count() === 0) {
                ModuleTheme::create([
                    'name' => 'Electric Blue Dark & Wave Signature',
                    'slug' => 'electric-blue-dark-signature',
                    'primary_color' => '#078BFF',
                    'secondary_color' => '#020817',
                    'font_family' => 'Playfair Display & Plus Jakarta Sans',
                    'layout_style' => 'Dark & Light Alternating Waves (Reference)',
                    'thumbnail' => '/module-media/Portfolio/theme/about-workspace.jpg',
                    'is_active' => true,
                ]);
                ModuleTheme::create([
                    'name' => 'Electric Blue Clean Light Mode',
                    'slug' => 'electric-blue-light-mode',
                    'primary_color' => '#078BFF',
                    'secondary_color' => '#F5F8FC',
                    'font_family' => 'Playfair Display & Plus Jakarta Sans',
                    'layout_style' => 'Full Daylight Crisp Theme',
                    'thumbnail' => '/module-media/Portfolio/theme/project-1.jpg',
                    'is_active' => false,
                ]);
            }

            if (ModuleUser::count() === 0) {
                ModuleUser::create([
                    'name' => 'Alex Morgan (Senior Web Developer)',
                    'email' => 'hello@alexmorgan.dev',
                    'phone' => '+880 1712 345678',
                    'role_name' => 'Administrator',
                    'status' => 'Active',
                ]);
            }

            if (ModuleSetting::count() === 0) {
                ModuleSetting::setValue('site_title', 'Alex Morgan — Web Developer Portfolio');
                ModuleSetting::setValue('site_tagline', 'Web Developer Crafting Premium Digital Experiences');
                ModuleSetting::setValue('contact_email', 'hello@alexmorgan.dev');
                ModuleSetting::setValue('contact_phone', '+880 1712 345678');
                ModuleSetting::setValue('contact_address', 'Dhaka, Bangladesh');
                ModuleSetting::setValue('footer_copyright', '© 2025 Alex Morgan. All rights reserved.');
            }
        } catch (\Throwable $e) {
            // Ignore seeding errors before migration runs
        }
    }
}
