<?php

return [
    'meta' => [
        'page_title'       => 'Alex Morgan — Senior Full-Stack Web Developer Portfolio',
        'meta_description' => 'Web Developer Crafting Premium Digital Experiences. Building modern, fast, and scalable web applications using Laravel, PHP, Next.js, React, and Tailwind CSS.',
        'meta_keywords'    => 'Web Developer, Laravel Developer, Full Stack Developer, Portfolio, Next.js, React, PHP, Bangladesh',
        'og_title'         => 'Alex Morgan — Web Developer Crafting Premium Digital Experiences',
        'default_theme'    => 'dark', // 'dark' (Signature Dark/Wave Reference) or 'light' (Full Light Mode)
    ],

    'visibility' => [
        'hero'         => '1',
        'about'        => '1',
        'skills'       => '1',
        'services'     => '1',
        'portfolio'    => '1',
        'why_me'       => '1',
        'process'      => '1',
        'experience'   => '1',
        'education'    => '1',
        'achievements' => '1',
        'clients'      => '1',
        'testimonials' => '1',
        'blog'         => '1',
        'faq'          => '1',
        'cta'          => '1',
        'contact'      => '1',
        'newsletter'   => '1',
    ],

    'header' => [
        'brand_name' => 'Alex Morgan',
        'brand_sub'  => 'Web Developer',
        'cta_text'   => "Let's Talk",
        'cta_url'    => '#contact',
        'logo'       => '',
    ],

    'hero' => [
        'badge'           => "Hello, I'm a Professional",
        'title_line_1'    => 'Web Developer',
        'title_line_2'    => 'Crafting Premium',
        'title_line_3'    => 'Digital',
        'title_highlight' => 'Experiences',
        'description'     => 'I build modern, fast and scalable web applications that help businesses grow and make an impact.',
        'btn_work_text'   => 'View My Work',
        'btn_work_url'    => '#portfolio',
        'btn_cv_text'     => 'Download CV',
        'btn_cv_url'      => '/module-media/Portfolio/cv/Alex-Morgan-CV.pdf',
        'portrait_image'  => '/module-media/Portfolio/theme/hero-developer-cutout.png',
        'script_line_1'   => 'Code',
        'script_line_2'   => 'Innovate',
        'script_line_3'   => 'Create',
    ],

    'socials' => [
        'facebook' => 'https://facebook.com',
        'github'   => 'https://github.com',
        'linkedin' => 'https://linkedin.com',
        'youtube'  => 'https://youtube.com',
        'twitter'  => 'https://x.com',
    ],

    'about' => [
        'tag'               => 'ABOUT ME',
        'heading_line_1'    => 'Code is my medium.',
        'heading_line_2'    => 'Empathy is my',
        'heading_highlight' => 'superpower.',
        'biography'         => "I'm a passionate web developer with 3+ years of experience building modern web applications using Laravel, PHP, and more. I love turning ideas into reality with clean code and great UX.",
        'philosophy'        => 'Every line of code should solve a real business problem while delivering a fast, intuitive, and accessible experience for every user.',
        'workspace_image'   => '/module-media/Portfolio/theme/about-workspace.jpg',
        'stat_1_number'     => '3+',
        'stat_1_label'      => 'Years Experience',
        'stat_2_number'     => '50+',
        'stat_2_label'      => 'Projects Delivered',
        'stat_3_number'     => '100%',
        'stat_3_label'      => 'Client Satisfaction',
        'highlights'        => [
            [
                'icon'  => 'fa-solid fa-bolt',
                'title' => 'Clean & Efficient Code',
                'sub'   => 'Performance First',
            ],
            [
                'icon'  => 'fa-solid fa-heart',
                'title' => 'User-Centric Design',
                'sub'   => 'Better User Experience',
            ],
            [
                'icon'  => 'fa-solid fa-trophy',
                'title' => 'On-Time Delivery',
                'sub'   => 'Always Committed',
            ],
            [
                'icon'  => 'fa-solid fa-headset',
                'title' => 'Ongoing Support',
                'sub'   => 'Long-Term Partnership',
            ],
        ],
    ],

    'skills_section' => [
        'tag'               => 'MY SKILLS',
        'heading_line_1'    => 'Expertise with',
        'heading_highlight' => 'Passion.',
        'description'       => 'I work with modern technologies to build innovative solutions.',
        'btn_text'          => 'Explore All Skills',
    ],

    'services_section' => [
        'tag'         => 'MY SERVICES',
        'heading'     => 'What I Do Best',
        'description' => 'I offer a wide range of web development services to meet your business needs.',
        'btn_text'    => 'View All Services',
    ],

    'portfolio_section' => [
        'tag'         => 'MY PORTFOLIO',
        'heading'     => 'My Recent Works',
        'description' => 'A collection of some of my best projects. Feel free to explore.',
        'btn_text'    => 'View All Projects',
    ],

    'why_me' => [
        'tag'         => 'WHY WORK WITH ME',
        'heading'     => 'Engineering Excellence Meets Business Value',
        'description' => 'Partner with a dedicated developer focused on scalable architecture, speed, and measurable business growth.',
        'items'       => [
            ['icon' => 'fa-solid fa-code', 'title' => 'Clean & Maintainable Code', 'desc' => 'Modular, PSR-compliant, well-documented codebases built to scale effortlessly.'],
            ['icon' => 'fa-solid fa-microchip', 'title' => 'Modern Technology', 'desc' => 'Powered by Laravel, Next.js, React, Tailwind CSS, and cloud-ready Docker stacks.'],
            ['icon' => 'fa-solid fa-clock-rotate-left', 'title' => 'On-Time Delivery', 'desc' => 'Milestone-driven agile execution ensuring every sprint is delivered on schedule.'],
            ['icon' => 'fa-solid fa-gauge-high', 'title' => 'Performance Optimization', 'desc' => 'Sub-second load times, query optimization, Redis caching, and Core Web Vitals 95+.'],
            ['icon' => 'fa-solid fa-wand-magic-sparkles', 'title' => 'User-Friendly Design', 'desc' => 'Pixel-perfect responsive interfaces crafted for conversion and accessibility.'],
            ['icon' => 'fa-solid fa-shield-halved', 'title' => 'Long-Term Support', 'desc' => 'Proactive security updates, monitoring, backups, and post-launch feature evolution.'],
        ],
    ],

    'process' => [
        'tag'         => 'WORKING PROCESS',
        'heading'     => 'My 7-Step Development Workflow',
        'description' => 'A transparent, structured engineering lifecycle from initial discovery to long-term support.',
        'steps'       => [
            ['step' => '01', 'icon' => 'fa-solid fa-comments', 'title' => 'Discussion & Requirement Analysis', 'desc' => 'Understanding business goals, target users, and functional requirements.'],
            ['step' => '02', 'icon' => 'fa-solid fa-compass-drafting', 'title' => 'Planning & Strategy', 'desc' => 'Database schema design, system architecture, and milestone roadmap.'],
            ['step' => '03', 'icon' => 'fa-solid fa-bezier-curve', 'title' => 'UI/UX Design', 'desc' => 'Interactive wireframes, design systems, and responsive visual prototypes.'],
            ['step' => '04', 'icon' => 'fa-solid fa-laptop-code', 'title' => 'Development', 'desc' => 'Clean backend APIs, dynamic frontend components, and third-party integrations.'],
            ['step' => '05', 'icon' => 'fa-solid fa-vial-circle-check', 'title' => 'Testing & Quality Assurance', 'desc' => 'Automated testing, security auditing, cross-device & performance benchmarks.'],
            ['step' => '06', 'icon' => 'fa-solid fa-rocket', 'title' => 'Deployment & Delivery', 'desc' => 'Zero-downtime CI/CD deployment, SSL setup, and production handover.'],
            ['step' => '07', 'icon' => 'fa-solid fa-headset', 'title' => 'Ongoing Support', 'desc' => 'Continuous monitoring, feature upgrades, and dedicated technical care.'],
        ],
    ],

    'experience_section' => [
        'tag'         => 'MY EXPERIENCE',
        'heading'     => 'My Journey So Far',
        'description' => 'A journey of learning, building and growing in the tech world.',
    ],

    'achievements' => [
        'tag'   => 'ACHIEVEMENTS & IMPACT',
        'items' => [
            ['number' => '3+', 'label' => 'Years of Experience', 'icon' => 'fa-solid fa-calendar-check'],
            ['number' => '50+', 'label' => 'Projects Completed', 'icon' => 'fa-solid fa-layer-group'],
            ['number' => '45+', 'label' => 'Happy Clients', 'icon' => 'fa-solid fa-face-smile-beam'],
            ['number' => '15+', 'label' => 'Technologies Used', 'icon' => 'fa-solid fa-cubes'],
            ['number' => '100%', 'label' => 'Successful Deliveries', 'icon' => 'fa-solid fa-award'],
        ],
    ],

    'clients' => [
        'tag'   => 'TRUSTED BY INNOVATIVE BRANDS',
        'logos' => [
            ['name' => 'DreamTech', 'icon' => 'fa-solid fa-cloud-bolt'],
            ['name' => 'WebPro Studio', 'icon' => 'fa-solid fa-cubes-stacked'],
            ['name' => 'NexaCorp', 'icon' => 'fa-solid fa-atom'],
            ['name' => 'FinEdge ERP', 'icon' => 'fa-solid fa-chart-line'],
            ['name' => 'MediCare Hub', 'icon' => 'fa-solid fa-shield-heart'],
            ['name' => 'UrbanEstates', 'icon' => 'fa-solid fa-building-columns'],
        ],
    ],

    'testimonials_section' => [
        'tag'         => 'TESTIMONIALS',
        'heading'     => 'What Clients Say',
        'description' => 'Trusted by clients worldwide for delivering exceptional work.',
    ],

    'blog_section' => [
        'tag'         => 'FROM BLOG',
        'heading'     => 'Latest Articles',
        'description' => 'Insights, tips and updates from my development journey.',
        'btn_text'    => 'View All Posts',
    ],

    'cta' => [
        'tag'         => 'READY TO COLLABORATE?',
        'heading'     => 'Have a Project in Mind?',
        'description' => "Let's turn your vision into a high-performing, scalable web application. Reach out today for a free technical consultation.",
        'btn_1_text'  => "Let's Talk",
        'btn_1_url'   => '#contact',
        'btn_2_text'  => 'Contact Me',
        'btn_2_url'   => '#contact',
    ],

    'contact' => [
        'tag'         => 'GET IN TOUCH',
        'heading'     => "Let's Work Together",
        'description' => 'Have a project in mind? Feel free to reach out anytime.',
        'btn_text'    => 'Contact Me',
        'email'       => 'hello@alexmorgan.dev',
        'phone'       => '+880 1712 345678',
        'location'    => 'Dhaka, Bangladesh',
    ],

    'footer' => [
        'description' => 'Full-Stack Web Developer specializing in Laravel, Next.js, and enterprise web applications.',
        'copyright'   => '© 2025 Alex Morgan. All rights reserved.',
    ],
];
