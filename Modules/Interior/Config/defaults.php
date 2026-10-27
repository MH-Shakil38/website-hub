<?php

$assets = file_exists(__DIR__ . '/default_assets.php') ? require __DIR__ . '/default_assets.php' : [];

return [
    'meta' => [
        'page_title' => 'Geotech Interior Design & Civil Consultancy Ltd.',
    ],

    'colors' => [
        'bg' => '#f8f2e9',
        'cream' => '#f1e7d8',
        'card' => '#efe5d6',
        'olive' => '#5b6240',
        'olive_dark' => '#474d31',
        'clay' => '#b26a47',
        'text' => '#3b372f',
        'muted' => '#7b7467',
        'line' => '#ddd0bc',
    ],

    'header' => [
        'logo' => $assets['asset_1'] ?? '',
        'brand_name' => 'Geotech',
        'brand_sub' => 'INTERIORS',
        'cta_text' => 'Book a Consultation',
        'cta_url' => '#contact',
        'menus' => [
            ['title' => 'Home', 'url' => '#home'],
            ['title' => 'About', 'url' => '#about'],
            ['title' => 'Services', 'url' => '#services'],
            ['title' => 'Projects', 'url' => '#projects'],
            ['title' => 'Gallery', 'url' => '#gallery'],
            ['title' => 'Videos', 'url' => '#videos'],
            ['title' => 'Process', 'url' => '#process'],
            ['title' => 'Journal', 'url' => '#journal'],
            ['title' => 'Contact', 'url' => '#contact'],
        ],
    ],

    'hero' => [
        'bg_image' => $assets['asset_2'] ?? '',
        'eyebrow' => 'Organic spaces. Inspired living.',
        'line_1' => 'Beautiful interiors',
        'line_2_prefix' => 'that',
        'line_2_em' => 'restore',
        'line_2_suffix' => 'and',
        'line_3_em' => 'inspire',
        'description' => 'We design wellness-inspired homes that blend natural beauty, function, and soulful living.',
        'btn_primary_text' => 'Explore our work',
        'btn_primary_url' => '#projects',
        'btn_secondary_text' => 'Call 01747783913',
        'btn_secondary_url' => 'tel:01747783913',
        'ring_text' => 'GEOTECH • INTERIOR DESIGN • CHATTOGRAM •',
    ],

    'services' => [
        'display_mode' => 'grid',    // 'grid' (normal row wrap) or 'carousel' (Owl Carousel slider)
        'carousel_mode' => 'auto',   // 'auto' (autoplay slide) or 'click' (manual click slide)
        'eyebrow' => 'What we do',
        'heading_line1' => 'Thoughtful design for',
        'heading_line2_prefix' => 'how',
        'heading_line2_em' => 'you',
        'heading_line2_suffix' => 'live.',
        'items' => [
            [
                'title' => 'Full-Service Interior Design',
                'description' => 'From concept to completion, we handle every detail with care.',
                'link_text' => 'Learn more',
                'link_url' => '#contact',
                'image' => $assets['asset_3'] ?? '',
            ],
            [
                'title' => 'Kitchen & Bath Design',
                'description' => 'Beautiful, functional spaces designed for everyday rituals.',
                'link_text' => 'Learn more',
                'link_url' => '#contact',
                'image' => $assets['asset_4'] ?? '',
            ],
            [
                'title' => 'Styling & Decor',
                'description' => 'Curated pieces and finishing touches that bring your home to life.',
                'link_text' => 'Learn more',
                'link_url' => '#contact',
                'image' => $assets['asset_5'] ?? '',
            ],
            [
                'title' => 'Civil Consultancy',
                'description' => 'Structural planning and civil guidance, wherever you build.',
                'link_text' => 'Learn more',
                'link_url' => '#contact',
                'image' => $assets['asset_6'] ?? '',
            ],
        ],
    ],

    'founder' => [
        'eyebrow' => 'Meet the founder',
        'heading_line1' => 'Designing with intention.',
        'heading_line2' => 'Rooted in nature.',
        'para_1' => 'Hi, I’m Shaamanullah Arman, Owner & CEO of Geotech Interior Design & Civil Consultancy Ltd. Geotech was born from my love of thoughtful design and solid engineering, and the belief that our spaces should support our well-being.',
        'para_2' => 'Every project is a collaboration. I take the time to understand your story, your needs, and your lifestyle to create spaces that are as beautiful as they are meaningful.',
        'name' => 'Shaamanullah Arman',
        'designation' => 'Owner & CEO, Geotech Interior Design & Civil Consultancy Ltd.',
        'link_text' => 'More about us',
        'link_url' => '#contact',
        'photo' => $assets['asset_7'] ?? '',
        'deco_image' => $assets['asset_8'] ?? '',
    ],

    'projects' => [
        'eyebrow' => 'Our projects',
        'heading' => 'Spaces that feel like home.',
        'view_all_text' => 'View all projects',
        'view_all_url' => '#gallery',
        'items' => [
            ['title' => 'Khulshi Residence', 'location' => 'Khulshi, Chattogram', 'image' => $assets['asset_9'] ?? ''],
            ['title' => 'Nasirabad Kitchen', 'location' => 'Nasirabad, Chattogram', 'image' => $assets['asset_10'] ?? ''],
            ['title' => 'GEC Circle Apartment', 'location' => 'GEC, Chattogram', 'image' => $assets['asset_11'] ?? ''],
            ['title' => 'Agrabad Bathroom', 'location' => 'Agrabad, Chattogram', 'image' => $assets['asset_12'] ?? ''],
            ['title' => 'Halishahar Retreat', 'location' => 'Halishahar, Chattogram', 'image' => $assets['asset_13'] ?? ''],
        ],
    ],

    'gallery' => [
        'eyebrow' => 'Gallery',
        'heading_prefix' => 'Rooms we love, details that',
        'heading_em' => 'matter',
        'items' => [
            ['id' => '1618221195710-dd6b41faaea6', 'cat' => 'living', 't' => 'Calm Living Lounge'],
            ['id' => '1556909114-f6e7ad7d3136', 'cat' => 'kitchen', 't' => 'Warm Wood Kitchen'],
            ['id' => '1616594039964-ae9021a400a0', 'cat' => 'bedroom', 't' => 'Serene Master Bedroom'],
            ['id' => '1586023492125-27b2c045efd7', 'cat' => 'living', 't' => 'Natural Light Living'],
            ['id' => '1552321554-5fefe8c9ef14', 'cat' => 'bath', 't' => 'Spa-Style Bathroom'],
            ['id' => '1617806118233-18e1de247200', 'cat' => 'dining', 't' => 'Family Dining Space'],
            ['id' => '1600210492486-724fe5c67fb0', 'cat' => 'living', 't' => 'Open Plan Living'],
            ['id' => '1600566753190-17f0baa2a6c3', 'cat' => 'kitchen', 't' => 'Modern Island Kitchen'],
            ['id' => '1505693416388-ac5ce068fe85', 'cat' => 'bedroom', 't' => 'Soft Neutral Bedroom'],
            ['id' => '1584622650111-993a426fbf0a', 'cat' => 'bath', 't' => 'Minimal Bath Retreat'],
            ['id' => '1493663284031-b7e3aefcae8e', 'cat' => 'living', 't' => 'Cozy Corner Sofa'],
            ['id' => '1600607687939-ce8a6c25118c', 'cat' => 'dining', 't' => 'Styled Interior Details'],
            ['id' => '1484154218962-a197022b5858', 'cat' => 'kitchen', 't' => 'Bright Family Kitchen'],
            ['id' => '1522771739844-6a9f6d5f14af', 'cat' => 'bedroom', 't' => 'Layered Linen Bedroom'],
            ['id' => '1555041469-a586c61ea9bc', 'cat' => 'living', 't' => 'Sculpted Sofa Setting'],
            ['id' => '1600566752355-35792bedcfea', 'cat' => 'bath', 't' => 'Marble Bathroom Suite'],
        ],
    ],

    'videos' => [
        'eyebrow' => 'Film & Walkthroughs',
        'heading_prefix' => 'Experience our spaces in',
        'heading_em' => 'motion',
        'items' => [
            [
                'title' => 'Khulshi Residence — Full Interior Walkthrough',
                'category' => 'walkthrough',
                'duration' => '03:18',
                'thumbnail' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=800&q=80',
                'video_url' => 'https://www.youtube.com/embed/ScMzIvxBSi4',
            ],
            [
                'title' => 'Warm Wood & Brass Kitchen Transformation',
                'category' => '3d_tour',
                'duration' => '02:45',
                'thumbnail' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?auto=format&fit=crop&w=800&q=80',
                'video_url' => 'https://www.youtube.com/embed/ScMzIvxBSi4',
            ],
            [
                'title' => 'Serene Master Suite — Natural Materials Story',
                'category' => 'client_story',
                'duration' => '04:05',
                'thumbnail' => 'https://images.unsplash.com/photo-1616594039964-ae9021a400a0?auto=format&fit=crop&w=800&q=80',
                'video_url' => 'https://www.youtube.com/embed/ScMzIvxBSi4',
            ],
            [
                'title' => 'GEC Duplex Living & Custom Joinery Tour',
                'category' => 'walkthrough',
                'duration' => '03:50',
                'thumbnail' => 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=800&q=80',
                'video_url' => 'https://www.youtube.com/embed/ScMzIvxBSi4',
            ],
        ],
    ],

    'process' => [
        'eyebrow' => 'Our process',
        'heading' => 'A thoughtful process. Effortless experience.',
        'steps' => [
            ['title' => '1. Discover', 'description' => 'We get to know you, your vision, and your space.'],
            ['title' => '2. Design', 'description' => 'We create a tailored design that reflects your lifestyle.'],
            ['title' => '3. Plan', 'description' => 'We refine every detail and bring the plan to life.'],
            ['title' => '4. Execute', 'description' => 'We manage and collaborate with trusted partners.'],
            ['title' => '5. Enjoy', 'description' => 'Move in, exhale, and love where you live.'],
        ],
        'why_eyebrow' => 'Why choose us',
        'why_items' => [
            'Wellness-focused design',
            'Sustainable & natural materials',
            'Personalized, boutique approach',
            'Timeless, soulful spaces',
        ],
    ],

    'testimonials' => [
        'eyebrow' => 'Kind words',
        'heading' => 'Stories from our clients.',
        'items' => [
            [
                'quote' => '“Geotech completely transformed our home into a peaceful retreat. They listened so deeply and brought beauty to every little detail.”',
                'author' => '— Jahid H.',
                'location' => 'Khulshi, Chattogram',
                'initial' => 'J',
            ],
            [
                'quote' => '“The process was seamless and so enjoyable. Our space feels elevated, calm, and truly ours.”',
                'author' => '— Rafiq & Nusrat',
                'location' => 'Nasirabad, Chattogram',
                'initial' => 'R',
            ],
            [
                'quote' => '“Geotech exceeded every expectation. Thoughtful, professional, and incredibly talented.”',
                'author' => '— Sabrina K.',
                'location' => 'Agrabad, Chattogram',
                'initial' => 'S',
            ],
        ],
    ],

    'cta' => [
        'image' => $assets['asset_14'] ?? '',
        'eyebrow' => 'Ready to begin?',
        'heading_line1' => 'Let’s create a space',
        'heading_line2_prefix' => 'that feels like',
        'heading_line2_em' => 'you',
        'btn_text' => 'Book your consultation',
        'btn_url' => 'tel:01747783913',
    ],

    'footer' => [
        'logo' => $assets['asset_15'] ?? '',
        'brand_name' => 'Geotech',
        'brand_sub' => 'INTERIORS',
        'about_text' => 'It is our responsibility to make your dreams come true. Interior design & civil consultancy in Chattogram.',
        'phone' => '01747783913',
        'website' => 'geotechinteriordesign.com',
        'website_url' => 'https://geotechinteriordesign.com',
        'address' => 'Chattogram, Bangladesh',
        'instagram_url' => '#',
        'pinterest_url' => '#',
        'facebook_url' => '#',
        'arch_image' => $assets['asset_16'] ?? '',
        'copyright' => '© 2026 Geotech Interior Design & Civil Consultancy Ltd. All rights reserved.',
    ],
];
