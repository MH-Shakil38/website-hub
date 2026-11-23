<?php

namespace Modules\EventManagement\App\Services;

use Illuminate\Support\Facades\Schema;
use Modules\EventManagement\App\Models\EmMemory;
use Modules\EventManagement\App\Models\EmPricingPlan;
use Modules\EventManagement\App\Models\EmSchedule;
use Modules\EventManagement\App\Models\EmSpeaker;
use Modules\EventManagement\App\Models\EmSponsor;
use Modules\EventManagement\App\Models\EmTicketBooking;
use Modules\EventManagement\App\Models\ModuleMedia;
use Modules\EventManagement\App\Models\ModuleMenu;
use Modules\EventManagement\App\Models\ModulePage;
use Modules\EventManagement\App\Models\ModuleSetting;
use Modules\EventManagement\App\Models\ModuleSlider;
use Modules\EventManagement\App\Models\ModuleTheme;
use Modules\EventManagement\App\Models\ModuleUser;
use Modules\EventManagement\App\Models\ModuleVideo;

class EventManagementDataService
{
    public function getDefaultSettings(): array
    {
        return [
            // Top bar & Brand (Exact match to new screenshot)
            'brand_name'             => 'Eventify',
            'topbar_text'            => 'Are you Planning to Grow Up Conference?',
            'topbar_cta_text'        => "Don't Miss!",
            'topbar_email'           => 'event@conference.com',
            'topbar_phone'           => '(202) 666-0112',

            // Hero section
            'hero_eyebrow'           => 'LEAD PURPOSE. INNOVATE BETTER TOGETHER.',
            'hero_title_line1'       => 'YEARLY BUSINESS',
            'hero_title_highlight'   => 'CONFERENCES',
            'hero_title_edition'     => '“ 13 ”',
            'hero_countdown_days'    => '1',
            'hero_countdown_hours'   => '23',
            'hero_countdown_minutes' => '58',
            'hero_countdown_seconds' => '27',
            'hero_btn_text'          => 'REGISTER for NOW',
            'hero_badge_text'        => 'Play Trailer',
            'hero_speaker_image'     => '/module-media/EventManagement/theme/hero-speaker.jpg',

            // About Our Business Conference
            'about_eyebrow'          => 'ABOUT OUR BUSINESS CONFERENCE',
            'about_title'            => 'Growth Through World Class And Conferences',
            'about_description'      => 'At Business, we bring together brightest minds, industry, and innovators from across industries to explore latest trends, technologies, and strategies shaping the future.',
            'about_stat1_text'       => '30+ Speakers',
            'about_stat2_text'       => '10+ Sponsors',
            'about_btn_text'         => 'REGISTER AN ATTENDEE',
            'about_img_tall'         => '/module-media/EventManagement/theme/about-tall.jpg',
            'about_img_top_right'    => '/module-media/EventManagement/theme/about-top-right.jpg',
            'about_img_bottom_right' => '/module-media/EventManagement/theme/about-bottom-right.jpg',

            // Sponsors Section
            'sponsors_heading'       => 'Join 6,000+ Companies Already Growing',

            // Speakers Section
            'speakers_eyebrow'       => 'OUR KEYNOTE SPEAKERS',
            'speakers_heading'       => 'Our Event Speakers',

            // Schedule Section
            'schedule_eyebrow'       => 'EVENT SCHEDULE',
            'schedule_heading'       => 'Our Events Schedule Plan',
            'day1_title'             => 'All Events',
            'day1_date'              => 'Jan - Dec 2024',
            'day2_title'             => 'Business Event',
            'day2_date'              => 'Nov 12, 2024',
            'day3_title'             => 'Tech Talks',
            'day3_date'              => 'Dec 19, 2024',

            // Recent Memories Section
            'memories_eyebrow'       => 'LAST YEAR MEMORY',
            'memories_heading'       => 'Recent Memories 2024',
            'memories_btn_text'      => 'SEE RECENT PHOTOS',

            // Pricing Section
            'pricing_eyebrow'        => 'PRICING PLAN',
            'pricing_heading'        => 'Get Your Entry Ticket',

            // Direction to Venue Section
            'venue_eyebrow'          => 'HOW TO REACH',
            'venue_heading'          => 'Direction To The Venue',
            'venue_map_image'        => '/module-media/EventManagement/theme/venue-map.jpg',
            'venue_email_label'      => 'OUR EMAIL',
            'venue_email_value'      => 'event@conference.com',
            'venue_phone_label'      => 'CALL NUMBER',
            'venue_phone_value'      => '(202) 666-0112',
            'venue_schedule_label'   => 'SCHEDULE',
            'venue_schedule_value'   => 'Sunday, 08:00 AM - 07 PM',

            // Bottom Countdown Banner
            'banner_btn_text'        => 'BUY A TICKET',
            'banner_date_time'       => '30 January 2025 - 9am to 11.30pm',
            'banner_location'        => 'Secret Location In The UK',

            // Footer
            'footer_about'           => 'We are committed to creating a platform where business leaders, innovators, and professionals can come together to exchange ideas',
            'footer_phone'           => '(202) 666-0112',
            'footer_location'        => 'NYC, USA, Main Event Room',
            'footer_email'           => 'event@conference.com',
            'footer_website'         => 'eventifyevent.com',
            'footer_copyright'       => '© Copyright 2024 - Eventify. All Right Reserved',
        ];
    }

    public function getSettingsData(): array
    {
        $defaults = $this->getDefaultSettings();

        if (!Schema::connection('tenant')->hasTable('em_settings')) {
            return $defaults;
        }

        $saved = ModuleSetting::pluck('value', 'key')->toArray();

        foreach ($defaults as $key => $val) {
            if (!empty($saved[$key])) {
                $defaults[$key] = $saved[$key];
            }
        }

        return $defaults;
    }

    public function ensureSeeded(): void
    {
        if (!Schema::connection('tenant')->hasTable('em_speakers')) {
            return;
        }

        // 1. Seed Default Settings
        if (ModuleSetting::count() === 0) {
            foreach ($this->getDefaultSettings() as $k => $v) {
                ModuleSetting::setValue($k, $v);
            }
            ModuleSetting::setValue('site_title', 'Eventify — Yearly Business Conferences "13"');
            ModuleSetting::setValue('site_tagline', 'Lead Purpose. Innovate Better Together.');
            ModuleSetting::setValue('contact_email', 'event@conference.com');
            ModuleSetting::setValue('contact_phone', '(202) 666-0112');
        }

        // 2. Seed 4 Sponsors (Join 6,000+ Companies Already Growing: Netztech, Epicurious, BataShift, Lightbox)
        if (EmSponsor::count() === 0) {
            $sponsors = [
                [
                    'name'        => 'Netztech',
                    'icon_class'  => 'bi-gear-wide-connected',
                    'website_url' => '#sponsors',
                    'sort_order'  => 1,
                ],
                [
                    'name'        => 'Epicurious',
                    'icon_class'  => 'bi-distribute-vertical',
                    'website_url' => '#sponsors',
                    'sort_order'  => 2,
                ],
                [
                    'name'        => 'BataShift',
                    'icon_class'  => 'bi-lightning-charge-fill',
                    'website_url' => '#sponsors',
                    'sort_order'  => 3,
                ],
                [
                    'name'        => 'Lightbox',
                    'icon_class'  => 'bi-box-seam-fill',
                    'website_url' => '#sponsors',
                    'sort_order'  => 4,
                ],
            ];

            foreach ($sponsors as $sp) {
                EmSponsor::create(array_merge($sp, ['is_active' => true]));
            }
        }

        // 3. Seed 4 Event Speakers (Exact match to new screenshot: Alex R. Robertson, Alexey Benkone, Andrew Raymonds, Ben Blakee)
        if (EmSpeaker::count() === 0) {
            $speakers = [
                [
                    'name'        => 'Alex R. Robertson',
                    'slug'        => 'alex-r-robertson',
                    'designation' => 'Finance Consultant',
                    'company'     => 'Eventify Global',
                    'avatar'      => '/module-media/EventManagement/theme/speaker-alex-robertson.jpg',
                    'bio'         => 'Keynote speaker on corporate strategy, capital markets, and future-ready business leadership.',
                    'sort_order'  => 1,
                ],
                [
                    'name'        => 'Alexey Benkone',
                    'slug'        => 'alexey-benkone',
                    'designation' => 'HR Consultant',
                    'company'     => 'TalentPulse Inc.',
                    'avatar'      => '/module-media/EventManagement/theme/speaker-alexy-sammo.jpg',
                    'bio'         => 'Global workplace transformation leader specializing in culture innovation and executive coaching.',
                    'sort_order'  => 2,
                ],
                [
                    'name'        => 'Andrew Raymonds',
                    'slug'        => 'andrew-raymonds',
                    'designation' => 'Finance Consultant',
                    'company'     => 'BataShift Advisory',
                    'avatar'      => '/module-media/EventManagement/theme/speaker-andrew-symonds.jpg',
                    'bio'         => 'Senior financial advisor helping high-growth enterprises scale internationally.',
                    'sort_order'  => 3,
                ],
                [
                    'name'        => 'Ben Blakee',
                    'slug'        => 'ben-blakee',
                    'designation' => 'Finance Consultant',
                    'company'     => 'Lightbox Capital',
                    'avatar'      => '/module-media/EventManagement/theme/speaker-ben-stokes.jpg',
                    'bio'         => 'Fintech strategist and keynote presenter on sustainable business models.',
                    'sort_order'  => 4,
                ],
            ];

            foreach ($speakers as $speaker) {
                EmSpeaker::create(array_merge($speaker, ['is_active' => true]));
            }
        }

        // 4. Seed Event Schedule Plan (Exact match to new screenshot: Unlock the World Event Introduction + tabs)
        if (EmSchedule::count() === 0) {
            $schedules = [
                // Tab 1: All Events (Jan - Dec 2024)
                [
                    'day_key'         => 'day1',
                    'day_label'       => 'All Events',
                    'day_date'        => 'Jan - Dec 2024',
                    'session_number'  => '01',
                    'title'           => 'Unlock the World Event Introduction',
                    'time_range'      => '10:00 AM – 12:00 PM',
                    'venue_location'  => 'NYC, USA, Main Event Room',
                    'image'           => '/module-media/EventManagement/theme/schedule-01.jpg',
                    'speaker1_name'   => 'Alex R. Robertson',
                    'speaker1_role'   => 'Finance Consultant',
                    'speaker1_avatar' => '/module-media/EventManagement/theme/speaker-mini-1.jpg',
                    'speaker2_name'   => 'Alexey Ankeior',
                    'speaker2_role'   => 'HR Manager',
                    'speaker2_avatar' => '/module-media/EventManagement/theme/speaker-mini-2.jpg',
                    'button_text'     => 'View Details',
                    'sort_order'      => 1,
                ],
                [
                    'day_key'         => 'day1',
                    'day_label'       => 'All Events',
                    'day_date'        => 'Jan - Dec 2024',
                    'session_number'  => '02',
                    'title'           => 'Workshops Keynote on Elevate Craft',
                    'time_range'      => '01:00 PM – 03:00 PM',
                    'venue_location'  => 'NYC, USA, Main Event Room',
                    'image'           => '/module-media/EventManagement/theme/schedule-02.jpg',
                    'speaker1_name'   => 'Alex R. Robertson',
                    'speaker1_role'   => 'Finance Consultant',
                    'speaker1_avatar' => '/module-media/EventManagement/theme/speaker-mini-1.jpg',
                    'speaker2_name'   => 'Alexey Ankeior',
                    'speaker2_role'   => 'HR Manager',
                    'speaker2_avatar' => '/module-media/EventManagement/theme/speaker-mini-2.jpg',
                    'button_text'     => 'View Details',
                    'sort_order'      => 2,
                ],
                [
                    'day_key'         => 'day1',
                    'day_label'       => 'All Events',
                    'day_date'        => 'Jan - Dec 2024',
                    'session_number'  => '03',
                    'title'           => 'Sustainability the Future Business',
                    'time_range'      => '03:30 PM – 05:30 PM',
                    'venue_location'  => 'NYC, USA, Main Event Room',
                    'image'           => '/module-media/EventManagement/theme/schedule-03.jpg',
                    'speaker1_name'   => 'Alex R. Robertson',
                    'speaker1_role'   => 'Finance Consultant',
                    'speaker1_avatar' => '/module-media/EventManagement/theme/speaker-mini-1.jpg',
                    'speaker2_name'   => 'Alexey Ankeior',
                    'speaker2_role'   => 'HR Manager',
                    'speaker2_avatar' => '/module-media/EventManagement/theme/speaker-mini-2.jpg',
                    'button_text'     => 'View Details',
                    'sort_order'      => 3,
                ],

                // Tab 2: Business Event (Nov 12, 2024)
                [
                    'day_key'         => 'day2',
                    'day_label'       => 'Business Event',
                    'day_date'        => 'Nov 12, 2024',
                    'session_number'  => '01',
                    'title'           => 'Global Leadership & Growth Summit',
                    'time_range'      => '10:00 AM – 12:00 PM',
                    'venue_location'  => 'NYC, USA, Main Event Room',
                    'image'           => '/module-media/EventManagement/theme/schedule-02.jpg',
                    'speaker1_name'   => 'Alex R. Robertson',
                    'speaker1_role'   => 'Finance Consultant',
                    'speaker1_avatar' => '/module-media/EventManagement/theme/speaker-mini-1.jpg',
                    'speaker2_name'   => 'Alexey Ankeior',
                    'speaker2_role'   => 'HR Manager',
                    'speaker2_avatar' => '/module-media/EventManagement/theme/speaker-mini-2.jpg',
                    'button_text'     => 'View Details',
                    'sort_order'      => 4,
                ],

                // Tab 3: Tech Talks (Dec 19, 2024)
                [
                    'day_key'         => 'day3',
                    'day_label'       => 'Tech Talks',
                    'day_date'        => 'Dec 19, 2024',
                    'session_number'  => '01',
                    'title'           => 'AI & Digital Enterprise Transformation',
                    'time_range'      => '10:00 AM – 12:00 PM',
                    'venue_location'  => 'NYC, USA, Main Event Room',
                    'image'           => '/module-media/EventManagement/theme/schedule-01.jpg',
                    'speaker1_name'   => 'Alex R. Robertson',
                    'speaker1_role'   => 'Finance Consultant',
                    'speaker1_avatar' => '/module-media/EventManagement/theme/speaker-mini-1.jpg',
                    'speaker2_name'   => 'Alexey Ankeior',
                    'speaker2_role'   => 'HR Manager',
                    'speaker2_avatar' => '/module-media/EventManagement/theme/speaker-mini-2.jpg',
                    'button_text'     => 'View Details',
                    'sort_order'      => 5,
                ],
            ];

            foreach ($schedules as $sch) {
                EmSchedule::create(array_merge($sch, ['is_active' => true]));
            }
        }

        // 5. Seed Recent Memories 2024 & Footer Gallery
        if (EmMemory::count() === 0) {
            $memories = [
                [
                    'title'             => 'Executive Leadership Panel 2024',
                    'year_label'        => '2024',
                    'image'             => '/module-media/EventManagement/theme/memory-2024-1.jpg',
                    'is_footer_gallery' => false,
                    'sort_order'        => 1,
                ],
                [
                    'title'             => 'Attendees Networking & Celebration 2024',
                    'year_label'        => '2024',
                    'image'             => '/module-media/EventManagement/theme/memory-2024-2.jpg',
                    'is_footer_gallery' => false,
                    'sort_order'        => 2,
                ],
                [
                    'title'             => 'Main Auditorium Session',
                    'year_label'        => '2024',
                    'image'             => '/module-media/EventManagement/theme/about-top-right.jpg',
                    'is_footer_gallery' => true,
                    'sort_order'        => 3,
                ],
                [
                    'title'             => 'Keynote Applause Moment',
                    'year_label'        => '2024',
                    'image'             => '/module-media/EventManagement/theme/about-bottom-right.jpg',
                    'is_footer_gallery' => true,
                    'sort_order'        => 4,
                ],
                [
                    'title'             => 'VIP Lounge Networking',
                    'year_label'        => '2024',
                    'image'             => '/module-media/EventManagement/theme/about-tall.jpg',
                    'is_footer_gallery' => true,
                    'sort_order'        => 5,
                ],
                [
                    'title'             => 'Workshop Craft Breakout',
                    'year_label'        => '2024',
                    'image'             => '/module-media/EventManagement/theme/schedule-02.jpg',
                    'is_footer_gallery' => true,
                    'sort_order'        => 6,
                ],
                [
                    'title'             => 'Future Business Roundtable',
                    'year_label'        => '2024',
                    'image'             => '/module-media/EventManagement/theme/schedule-03.jpg',
                    'is_footer_gallery' => true,
                    'sort_order'        => 7,
                ],
                [
                    'title'             => 'Conference Team Portrait',
                    'year_label'        => '2024',
                    'image'             => '/module-media/EventManagement/theme/memory-2024-1.jpg',
                    'is_footer_gallery' => true,
                    'sort_order'        => 8,
                ],
            ];

            foreach ($memories as $mem) {
                EmMemory::create(array_merge($mem, ['is_active' => true]));
            }
        }

        // 6. Seed 3 Pricing Plans (REGULAR $49, PREMIUM $69, PLATINUM $99)
        if (EmPricingPlan::count() === 0) {
            $plans = [
                [
                    'name'           => 'REGULAR',
                    'slug'           => 'regular',
                    'price'          => 49,
                    'currency'       => '$',
                    'subtitle'       => 'ATTENDEE',
                    'features_text'  => 'Full Day One Conference Access, Main Hall Seating & Digital Certificate',
                    'button_text'    => 'BUY TICKET',
                    'is_highlighted' => false,
                    'sort_order'     => 1,
                ],
                [
                    'name'           => 'PREMIUM',
                    'slug'           => 'premium',
                    'price'          => 69,
                    'currency'       => '$',
                    'subtitle'       => 'ATTENDEE',
                    'features_text'  => 'All 3 Days Access, Front Row Priority Seating, VIP Lunch & Workshop Kit',
                    'button_text'    => 'BUY TICKET',
                    'is_highlighted' => true,
                    'sort_order'     => 2,
                ],
                [
                    'name'           => 'PLATINUM',
                    'slug'           => 'platinum',
                    'price'          => 99,
                    'currency'       => '$',
                    'subtitle'       => 'ATTENDEE',
                    'features_text'  => 'All-Access Executive Pass, 1-on-1 Speaker Dinner, VIP Lounge & Recording Vault',
                    'button_text'    => 'BUY TICKET',
                    'is_highlighted' => false,
                    'sort_order'     => 3,
                ],
            ];

            foreach ($plans as $plan) {
                EmPricingPlan::create(array_merge($plan, ['is_active' => true]));
            }
        }

        // 7. Seed Sample Ticket Bookings
        if (EmTicketBooking::count() === 0) {
            EmTicketBooking::create([
                'ticket_code'    => 'EVT-2025-8841',
                'attendee_name'  => 'Marcus Vance',
                'email'          => 'marcus.vance@batashift.com',
                'phone'          => '(202) 666-0112',
                'company'        => 'BataShift Corp',
                'plan_name'      => 'PREMIUM',
                'unit_price'     => 69.00,
                'quantity'       => 2,
                'total_amount'   => 138.00,
                'session_choice' => 'Unlock the World Event Introduction',
                'status'         => 'Confirmed',
            ]);
            EmTicketBooking::create([
                'ticket_code'    => 'EVT-2025-9104',
                'attendee_name'  => 'Elena Rostova',
                'email'          => 'elena@epicurious.io',
                'phone'          => '(202) 666-0147',
                'company'        => 'Epicurious Media',
                'plan_name'      => 'PLATINUM',
                'unit_price'     => 99.00,
                'quantity'       => 1,
                'total_amount'   => 99.00,
                'session_choice' => 'Workshops Keynote on Elevate Craft',
                'status'         => 'Confirmed',
            ]);
        }

        // 8. Seed Standard Module CMS Data
        if (ModuleMenu::count() === 0) {
            $menus = [
                ['title' => 'Home', 'url' => '#home', 'location' => 'header', 'sort_order' => 1],
                ['title' => 'About Event', 'url' => '#about', 'location' => 'header', 'sort_order' => 2],
                ['title' => 'Speakers', 'url' => '#speakers', 'location' => 'header', 'sort_order' => 3],
                ['title' => 'Schedule', 'url' => '#schedule', 'location' => 'header', 'sort_order' => 4],
                ['title' => 'Blog', 'url' => '#memories', 'location' => 'header', 'sort_order' => 5],
                ['title' => 'Pages', 'url' => '#pricing', 'location' => 'header', 'sort_order' => 6],
            ];
            foreach ($menus as $m) {
                ModuleMenu::create(array_merge($m, ['target' => '_self', 'is_active' => true]));
            }
        }

        if (ModulePage::count() === 0) {
            ModulePage::create([
                'title'   => 'Yearly Business Conferences "13" Overview',
                'slug'    => 'yearly-business-conferences-13',
                'content' => 'Lead Purpose. Innovate Better Together. Join 30+ world-class speakers and 10+ global sponsors at Eventify Business Conferences.',
                'status'  => 'Published',
                'views'   => 2450,
            ]);
            ModulePage::create([
                'title'   => 'Speaker Guidelines & Venue Information',
                'slug'    => 'speaker-guidelines-venue',
                'content' => 'Complete schedule, hall locations at NYC, USA Main Event Room, and attendee pass perks.',
                'status'  => 'Published',
                'views'   => 1620,
            ]);
        }

        if (ModuleSlider::count() === 0) {
            ModuleSlider::create([
                'title'       => 'YEARLY BUSINESS CONFERENCES "13"',
                'subtitle'    => 'LEAD PURPOSE. INNOVATE BETTER TOGETHER.',
                'button_text' => 'REGISTER for NOW',
                'button_url'  => '#pricing',
                'image'       => '/module-media/EventManagement/theme/hero-speaker.jpg',
                'sort_order'  => 1,
                'is_active'   => true,
            ]);
        }

        if (ModuleMedia::count() === 0) {
            $mediaFiles = [
                ['title' => 'Eventify Keynote Speaker Hero', 'category' => 'hero', 'file_path' => '/module-media/EventManagement/theme/hero-speaker.jpg'],
                ['title' => 'Business Conference Networking', 'category' => 'about', 'file_path' => '/module-media/EventManagement/theme/about-tall.jpg'],
                ['title' => 'Recent Memories 2024 Team', 'category' => 'memories', 'file_path' => '/module-media/EventManagement/theme/memory-2024-1.jpg'],
                ['title' => 'Recent Memories 2024 Celebration', 'category' => 'memories', 'file_path' => '/module-media/EventManagement/theme/memory-2024-2.jpg'],
            ];
            foreach ($mediaFiles as $mf) {
                ModuleMedia::create(array_merge($mf, [
                    'file_type' => 'image/jpeg',
                    'file_size' => '220 KB',
                    'alt_text'  => $mf['title'],
                ]));
            }
        }

        if (ModuleVideo::count() === 0) {
            ModuleVideo::create([
                'title'       => 'Eventify Yearly Business Conferences Official Trailer',
                'description' => 'Watch highlights from our 30+ keynote speakers and executive workshops.',
                'category'    => 'Conference Trailer',
                'video_url'   => 'https://www.youtube.com/embed/ScMzIvxBSi4',
                'thumbnail'   => '/module-media/EventManagement/theme/about-top-right.jpg',
                'duration'    => '03:45',
                'sort_order'  => 1,
                'is_active'   => true,
            ]);
        }

        if (ModuleTheme::count() === 0) {
            ModuleTheme::create([
                'name'            => 'Eventify Dark Teal & Neon Lime Theme',
                'slug'            => 'eventify-dark-teal-neon',
                'primary_color'   => '#BAF92C',
                'secondary_color' => '#012F39',
                'font_family'     => 'Plus Jakarta Sans & Inter',
                'layout_style'    => 'Conference Split Hero & Timeline Schedule',
                'thumbnail'       => '/module-media/EventManagement/theme/eventify-full-preview.jpg',
                'is_active'       => true,
            ]);
        }

        if (ModuleUser::count() === 0) {
            ModuleUser::create([
                'name'      => 'Alex R. Robertson (Conference Chair)',
                'email'     => 'alex@conference.com',
                'phone'     => '(202) 666-0112',
                'role_name' => 'Administrator',
                'status'    => 'Active',
            ]);
            ModuleUser::create([
                'name'      => 'Alexey Benkone (Speaker Coordinator)',
                'email'     => 'alexey@conference.com',
                'phone'     => '(202) 666-0119',
                'role_name' => 'Manager',
                'status'    => 'Active',
            ]);
        }
    }
}
