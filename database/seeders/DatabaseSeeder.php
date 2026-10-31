<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Page;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Website;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Permissions
        $permissionsData = [
            ['name' => 'Manage Websites', 'slug' => 'manage_websites', 'group' => 'Websites'],
            ['name' => 'Manage Pages', 'slug' => 'manage_pages', 'group' => 'Content'],
            ['name' => 'Manage Menus', 'slug' => 'manage_menus', 'group' => 'Content'],
            ['name' => 'Manage Sliders', 'slug' => 'manage_sliders', 'group' => 'Content'],
            ['name' => 'Manage Media Library', 'slug' => 'manage_media', 'group' => 'Media'],
            ['name' => 'Manage Themes', 'slug' => 'manage_themes', 'group' => 'Appearance'],
            ['name' => 'Manage Users', 'slug' => 'manage_users', 'group' => 'Access Control'],
            ['name' => 'Manage Roles & Permissions', 'slug' => 'manage_roles', 'group' => 'Access Control'],
            ['name' => 'Manage Global Settings', 'slug' => 'manage_settings', 'group' => 'System'],
        ];

        $permissionIds = [];
        foreach ($permissionsData as $perm) {
            $p = Permission::updateOrCreate(['slug' => $perm['slug']], $perm);
            $permissionIds[$perm['slug']] = $p->id;
        }

        // 2. Seed Roles
        $adminRole = Role::updateOrCreate(
            ['slug' => 'administrator'],
            ['name' => 'Administrator', 'description' => 'Full access to all system features and modules.', 'is_default' => false]
        );
        $adminRole->permissions()->sync(array_values($permissionIds));

        $managerRole = Role::updateOrCreate(
            ['slug' => 'manager'],
            ['name' => 'Manager', 'description' => 'Can manage websites, pages, menus, sliders, and media.', 'is_default' => false]
        );
        $managerRole->permissions()->sync([
            $permissionIds['manage_websites'],
            $permissionIds['manage_pages'],
            $permissionIds['manage_menus'],
            $permissionIds['manage_sliders'],
            $permissionIds['manage_media'],
            $permissionIds['manage_themes'],
        ]);

        $editorRole = Role::updateOrCreate(
            ['slug' => 'editor'],
            ['name' => 'Editor', 'description' => 'Can create and edit pages, sliders, and media files.', 'is_default' => true]
        );
        $editorRole->permissions()->sync([
            $permissionIds['manage_pages'],
            $permissionIds['manage_sliders'],
            $permissionIds['manage_media'],
        ]);

        $supportRole = Role::updateOrCreate(
            ['slug' => 'support'],
            ['name' => 'Support', 'description' => 'Read-only & customer support access.', 'is_default' => false]
        );
        $supportRole->permissions()->sync([
            $permissionIds['manage_pages'],
        ]);

        // 3. Seed 12 Users (Primary Admin: John Doe / you@example.com)
        $mainAdmin = User::updateOrCreate(
            ['email' => 'you@example.com'],
            [
                'role_id' => $adminRole->id,
                'name' => 'John Doe',
                'phone' => '01812-100001',
                'status' => 'Active',
                'password' => Hash::make('password'),
                'two_factor_enabled' => true,
                'two_factor_code' => '274916',
                'email_verified_at' => now(),
            ]
        );

        $extraUsers = [
            ['name' => 'Sarah Jenkins', 'email' => 'sarah@example.com', 'phone' => '01712-200893', 'role_id' => $managerRole->id, 'status' => 'Active'],
            ['name' => 'Michael Chen', 'email' => 'michael@example.com', 'phone' => '01844-300672', 'role_id' => $editorRole->id, 'status' => 'Active'],
            ['name' => 'Emily Rodriguez', 'email' => 'emily@example.com', 'phone' => '01675-400309', 'role_id' => $editorRole->id, 'status' => 'Active'],
            ['name' => 'David Kim', 'email' => 'david@example.com', 'phone' => '01798-500421', 'role_id' => $managerRole->id, 'status' => 'Active'],
            ['name' => 'Sophia Patel', 'email' => 'sophia@example.com', 'phone' => '01827-600906', 'role_id' => $supportRole->id, 'status' => 'Active'],
            ['name' => 'James Wilson', 'email' => 'james@example.com', 'phone' => '01911-700778', 'role_id' => $editorRole->id, 'status' => 'Active'],
            ['name' => 'Olivia Martinez', 'email' => 'olivia@example.com', 'phone' => '01631-800554', 'role_id' => $supportRole->id, 'status' => 'Active'],
            ['name' => 'Daniel Taylor', 'email' => 'daniel@example.com', 'phone' => '01890-900233', 'role_id' => $editorRole->id, 'status' => 'Active'],
            ['name' => 'Ava Anderson', 'email' => 'ava@example.com', 'phone' => '01755-110417', 'role_id' => $managerRole->id, 'status' => 'Active'],
            ['name' => 'Lucas Thomas', 'email' => 'lucas@example.com', 'phone' => '01818-220521', 'role_id' => $editorRole->id, 'status' => 'Active'],
            ['name' => 'Mia Jackson', 'email' => 'mia@example.com', 'phone' => '01711-330619', 'role_id' => $supportRole->id, 'status' => 'Active'],
        ];

        foreach ($extraUsers as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                array_merge($u, [
                    'password' => Hash::make('password'),
                    'two_factor_code' => '274916',
                    'email_verified_at' => now(),
                ])
            );
        }

        // 4. Seed 5 Websites (Exact match to Dashboard Screenshot + Dynamic Module Routing)
        $websites = [
            [
                'name' => 'Dream Home',
                'domain' => 'dreamhome.com',
                'thumbnail' => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=400&q=80',
                'module' => 'Real Estate',
                'module_name' => 'RealEstate',
                'slug' => 'real-estate',
                'admin_route' => 'admin.realestate.properties.index',
                'description' => 'Luxury property listings, residential apartments, and real estate inquiry portal.',
                'module_badge' => 'pink',
                'status' => 'Published',
                'is_active' => true,
                'updated_at' => Carbon::parse('2025-03-31 14:00:00'),
                'created_at' => Carbon::parse('2025-03-01 10:00:00'),
            ],
            [
                'name' => 'Modern Interiors',
                'domain' => 'moderninteriors.com',
                'thumbnail' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=400&q=80',
                'module' => 'Interior Design',
                'module_name' => 'Interior',
                'slug' => 'interior-design',
                'admin_route' => 'admin.interior.projects.index',
                'description' => 'Geotech Interior Design & Civil Consultancy Ltd. — Bespoke interior architecture, portfolio, gallery & consultation.',
                'module_badge' => 'purple',
                'status' => 'Published',
                'is_active' => true,
                'updated_at' => Carbon::parse('2025-03-28 14:00:00'),
                'created_at' => Carbon::parse('2025-03-02 10:00:00'),
            ],
            [
                'name' => 'Book My Stay',
                'domain' => 'bookmystay.com',
                'thumbnail' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=400&q=80',
                'module' => 'Booking',
                'module_name' => null,
                'slug' => 'booking',
                'admin_route' => 'admin.transactions',
                'description' => 'Hotel & resort reservation management and payment settlement gateway.',
                'module_badge' => 'blue',
                'status' => 'Published',
                'is_active' => false,
                'updated_at' => Carbon::parse('2025-03-25 14:00:00'),
                'created_at' => Carbon::parse('2025-03-05 10:00:00'),
            ],
            [
                'name' => 'Tech Company',
                'domain' => 'techcompany.com',
                'thumbnail' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=400&q=80',
                'module' => 'Software',
                'module_name' => null,
                'slug' => 'software-company',
                'admin_route' => 'admin.websites',
                'description' => 'Enterprise SaaS & software solutions showcase website.',
                'module_badge' => 'green',
                'status' => 'Private',
                'is_active' => false,
                'updated_at' => Carbon::parse('2025-03-20 14:00:00'),
                'created_at' => Carbon::parse('2025-03-10 10:00:00'),
            ],
            [
                'name' => 'MediCare Plus',
                'domain' => 'medicareplus.com',
                'thumbnail' => 'https://images.unsplash.com/photo-1587854692152-cbe660dbde88?auto=format&fit=crop&w=400&q=80',
                'module' => 'Pharmacy',
                'module_name' => 'Pharmacy',
                'slug' => 'pharmacy',
                'admin_route' => 'admin.pharmacy.medicines.index',
                'description' => 'Online medicine catalog, healthcare products, and prescription order management.',
                'module_badge' => 'green',
                'status' => 'Published',
                'is_active' => true,
                'updated_at' => Carbon::parse('2025-03-15 14:00:00'),
                'created_at' => Carbon::parse('2025-03-12 10:00:00'),
            ],
            [
                'name' => 'FertilityClinic.com',
                'domain' => 'fertilityclinic.com',
                'thumbnail' => '/module-media/Clinic/theme/hero-couple.jpg',
                'module' => 'Fertility Clinic',
                'module_name' => 'Clinic',
                'slug' => 'fertility-clinic',
                'admin_route' => 'admin.pages',
                'description' => 'Advanced fertility care, IVF/IUI treatments, specialist doctors, and appointment booking.',
                'module_badge' => 'green',
                'status' => 'Published',
                'is_active' => true,
                'updated_at' => Carbon::parse('2025-03-31 16:00:00'),
                'created_at' => Carbon::parse('2025-03-14 10:00:00'),
            ],
            [
                'name' => 'AlexMorgan.dev',
                'domain' => 'alexmorgan.dev',
                'thumbnail' => '/module-media/Portfolio/theme/about-workspace.jpg',
                'module' => 'Developer Portfolio',
                'module_name' => 'Portfolio',
                'slug' => 'developer-portfolio',
                'admin_route' => 'admin.portfolio.projects.index',
                'description' => 'Premium Web Developer Portfolio with Light & Dark Theme, Projects, Skills, Services, Career Timeline & Blog.',
                'module_badge' => 'blue',
                'status' => 'Published',
                'is_active' => true,
                'updated_at' => Carbon::parse('2025-03-31 18:00:00'),
                'created_at' => Carbon::parse('2025-03-15 10:00:00'),
            ],
        ];

        foreach ($websites as $site) {
            Website::updateOrCreate(['domain' => $site['domain']], $site);
        }

        Website::syncModuleStatuses();

        // 5. Seed 48 Pages (32 Published, 8 Draft, 5 Private, 3 Scheduled)
        if (Page::count() === 0) {
            $firstSiteId = Website::first()?->id;
            $statusDistribution = [
                'Published' => 32,
                'Draft' => 8,
                'Private' => 5,
                'Scheduled' => 3,
            ];

            $counter = 1;
            foreach ($statusDistribution as $status => $count) {
                for ($i = 1; $i <= $count; $i++) {
                    Page::create([
                        'website_id' => $firstSiteId,
                        'title' => "{$status} Page #{$counter}",
                        'slug' => strtolower($status) . '-page-' . $counter,
                        'status' => $status,
                        'views' => rand(120, 2400),
                    ]);
                    $counter++;
                }
            }
        }

        // 6. Seed 5 Activity Logs (Exact match to Dashboard Screenshot)
        if (ActivityLog::count() === 0) {
            $activities = [
                [
                    'user_id' => $mainAdmin->id,
                    'title' => 'New page created',
                    'subtitle' => 'About Us (dreamhome.com)',
                    'icon' => 'bi-plus-circle-fill',
                    'bg_color' => '#DCFCE7',
                    'text_color' => '#10B981',
                    'time_label' => '2 min ago',
                ],
                [
                    'user_id' => $mainAdmin->id,
                    'title' => 'Image uploaded',
                    'subtitle' => 'living-room.jpg (moderninteriors.com)',
                    'icon' => 'bi-image',
                    'bg_color' => '#FFE4EC',
                    'text_color' => '#EC134A',
                    'time_label' => '15 min ago',
                ],
                [
                    'user_id' => $mainAdmin->id,
                    'title' => 'Menu updated',
                    'subtitle' => 'Main Menu (bookmystay.com)',
                    'icon' => 'bi-pencil-square',
                    'bg_color' => '#DBEAFE',
                    'text_color' => '#3B82F6',
                    'time_label' => '1 hour ago',
                ],
                [
                    'user_id' => $mainAdmin->id,
                    'title' => 'Theme changed',
                    'subtitle' => 'Modern Theme (techcompany.com)',
                    'icon' => 'bi-palette-fill',
                    'bg_color' => '#F3E8FF',
                    'text_color' => '#A855F7',
                    'time_label' => '3 hours ago',
                ],
                [
                    'user_id' => $mainAdmin->id,
                    'title' => 'New user registered',
                    'subtitle' => 'sarah@example.com',
                    'icon' => 'bi-person-fill',
                    'bg_color' => '#FEF3C7',
                    'text_color' => '#F59E0B',
                    'time_label' => '5 hours ago',
                ],
            ];

            foreach ($activities as $act) {
                ActivityLog::create($act);
            }
        }

        // 7. Seed Global Settings
        $defaultSettings = [
            'site_name' => 'SiteBuilder',
            'site_tagline' => 'Website Management',
            'support_email' => 'support@sitebuilder.com',
            'merchant_account' => '01812-XXXXXX',
            'charges_rate' => '1.80% + ৳5.00',
            'available_balance' => '125,482.36',
            'media_files_count' => '1,284',
            'date_range_label' => 'Mar 1, 2025 - Mar 31, 2025',
        ];

        foreach ($defaultSettings as $key => $val) {
            Setting::setValue($key, $val);
        }

        // 8. Seed Transactions (Exact 10 rows from Screenshot 8)
        if (Transaction::count() === 0) {
            $txns = [
                ['2025-10-19 14:32:18', '01818-****521', 1250.00, 50.00, 1200.00, 12.00, 'Payment', 'PGW', 'TXNID202510190001'],
                ['2025-10-19 13:17:45', '01712-****893', 850.00, 0.00, 850.00, 8.50, 'Payment', 'PGW', 'TXNID202510190002'],
                ['2025-10-19 12:05:33', '01844-****672', 2400.00, 100.00, 2300.00, 20.00, 'Payment', 'PGW', 'TXNID202510190003'],
                ['2025-10-19 11:48:02', '01675-****309', 600.00, 0.00, 600.00, 6.00, 'Settlement', 'PGW', 'TXNID202510190004'],
                ['2025-10-19 10:22:17', '01798-****421', 1850.00, 75.00, 1775.00, 17.75, 'Payment', 'PGW', 'TXNID202510190005'],
                ['2025-10-19 09:47:51', '01827-****906', 3200.00, 150.00, 3050.00, 30.50, 'Payment', 'PGW', 'TXNID202510190006'],
                ['2025-10-19 08:32:10', '01911-****778', 920.00, 20.00, 900.00, 9.00, 'Payment', 'PGW', 'TXNID202510190007'],
                ['2025-10-18 20:15:33', '01631-****554', 1450.00, 0.00, 1450.00, 14.50, 'Settlement', 'PGW', 'TXNID202510180008'],
                ['2025-10-18 18:42:21', '01890-****233', 780.00, 30.00, 750.00, 7.50, 'Payment', 'PGW', 'TXNID202510180009'],
                ['2025-10-18 16:12:09', '01755-****417', 5600.00, 200.00, 5400.00, 54.00, 'Payment', 'PGW', 'TXNID202510180010'],
            ];

            foreach ($txns as $t) {
                Transaction::create([
                    'transacted_at' => Carbon::parse($t[0]),
                    'wallet_number' => $t[1],
                    'amount' => $t[2],
                    'coupon_amount' => $t[3],
                    'net_amount' => $t[4],
                    'cashback_amount' => $t[5],
                    'type' => $t[6],
                    'channel' => $t[7],
                    'transaction_id' => $t[8],
                ]);
            }
        }

        // 9. Seed Isolated Per-Module CMS Data (int_*, re_*, pharma_*)
        $this->seedIsolatedModuleData();

        if (class_exists(\Modules\Portfolio\App\Services\PortfolioDataService::class)) {
            app(\Modules\Portfolio\App\Services\PortfolioDataService::class)->ensureSeeded();
        }
    }

    protected function seedIsolatedModuleData(): void
    {
        $modulesSeedConfig = [
            'Interior' => [
                'title' => 'Modern Interiors Studio',
                'tagline' => 'Bespoke Luxury Interior Architecture',
                'email' => 'hello@moderninteriors.com',
                'phone' => '+880 1812-111222',
                'primary' => '#EC134A',
                'secondary' => '#0F172A',
                'layout' => 'Luxury Studio Grid',
                'thumb' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=800&q=80',
                'menus' => [
                    ['title' => 'Home', 'url' => '/', 'location' => 'header', 'sort_order' => 1],
                    ['title' => 'Portfolio', 'url' => '/portfolio', 'location' => 'header', 'sort_order' => 2],
                    ['title' => 'Contact Studio', 'url' => '/contact', 'location' => 'header', 'sort_order' => 3],
                    ['title' => 'Design Packages', 'url' => '/portfolio', 'location' => 'footer', 'sort_order' => 1],
                ],
                'pages' => [
                    ['title' => 'Interior Architecture Overview', 'slug' => 'interior-architecture', 'status' => 'Published', 'views' => 1420],
                    ['title' => 'Residential & Villa Styling', 'slug' => 'residential-styling', 'status' => 'Published', 'views' => 980],
                    ['title' => '2026 Material Lookbook', 'slug' => 'material-lookbook', 'status' => 'Draft', 'views' => 210],
                ],
                'sliders' => [
                    ['title' => 'Bespoke Luxury Interior Design', 'subtitle' => 'Crafting timeless residential and commercial spaces.', 'button_text' => 'View Portfolio', 'button_url' => '/portfolio'],
                ],
                'users' => [
                    ['name' => 'Ariana Rahman (Lead Architect)', 'email' => 'ariana@moderninteriors.com', 'phone' => '01812-111222', 'role_name' => 'Administrator', 'status' => 'Active'],
                    ['name' => 'Tanvir Hasan (3D Visualizer)', 'email' => 'tanvir@moderninteriors.com', 'phone' => '01712-333444', 'role_name' => 'Editor', 'status' => 'Active'],
                ],
            ],
            'RealEstate' => [
                'title' => 'Dream Home Properties',
                'tagline' => 'Premier Luxury Real Estate & Penthouses',
                'email' => 'sales@dreamhome.com',
                'phone' => '+880 1711-555666',
                'primary' => '#2563EB',
                'secondary' => '#0F172A',
                'layout' => 'Property Showcase Pro',
                'thumb' => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80',
                'menus' => [
                    ['title' => 'Home', 'url' => '/', 'location' => 'header', 'sort_order' => 1],
                    ['title' => 'Properties', 'url' => '/properties', 'location' => 'header', 'sort_order' => 2],
                    ['title' => 'Book Viewing', 'url' => '/contact', 'location' => 'header', 'sort_order' => 3],
                    ['title' => 'Featured Villas', 'url' => '/properties', 'location' => 'footer', 'sort_order' => 1],
                ],
                'pages' => [
                    ['title' => 'Gulshan & Banani Luxury Listings', 'slug' => 'gulshan-luxury-listings', 'status' => 'Published', 'views' => 2890],
                    ['title' => 'Commercial Penthouses & Plots', 'slug' => 'commercial-penthouses', 'status' => 'Published', 'views' => 1640],
                    ['title' => 'Home Loan & Legal Guide', 'slug' => 'home-loan-guide', 'status' => 'Scheduled', 'views' => 430],
                ],
                'sliders' => [
                    ['title' => 'Find Your Signature Dream Residence', 'subtitle' => 'Exclusive waterfront villas and smart apartments.', 'button_text' => 'Browse Properties', 'button_url' => '/properties'],
                ],
                'users' => [
                    ['name' => 'Rafiqul Islam (Senior Broker)', 'email' => 'rafiq@dreamhome.com', 'phone' => '01711-555666', 'role_name' => 'Administrator', 'status' => 'Active'],
                    ['name' => 'Nadia Karim (Property Manager)', 'email' => 'nadia@dreamhome.com', 'phone' => '01819-777888', 'role_name' => 'Manager', 'status' => 'Active'],
                ],
            ],
            'Pharmacy' => [
                'title' => 'MediCare Plus Pharmacy',
                'tagline' => '24/7 Authentic Medicines & Healthcare Delivery',
                'email' => 'care@medicareplus.com',
                'phone' => '+880 1911-999000',
                'primary' => '#10B981',
                'secondary' => '#0F172A',
                'layout' => 'Clean Medical Store',
                'thumb' => 'https://images.unsplash.com/photo-1587854692152-cbe660dbde88?auto=format&fit=crop&w=800&q=80',
                'menus' => [
                    ['title' => 'Home', 'url' => '/', 'location' => 'header', 'sort_order' => 1],
                    ['title' => 'Medicine Catalog', 'url' => '/catalog', 'location' => 'header', 'sort_order' => 2],
                    ['title' => 'Upload Prescription', 'url' => '/contact', 'location' => 'header', 'sort_order' => 3],
                    ['title' => 'Emergency Care', 'url' => '/contact', 'location' => 'footer', 'sort_order' => 1],
                ],
                'pages' => [
                    ['title' => 'Prescription Delivery Policy', 'slug' => 'prescription-delivery', 'status' => 'Published', 'views' => 1950],
                    ['title' => 'Cold-Chain Insulin & Vaccines', 'slug' => 'cold-chain-medicines', 'status' => 'Published', 'views' => 1120],
                ],
                'sliders' => [
                    ['title' => '100% Authentic Medicines Delivered Fast', 'subtitle' => 'Order prescription & OTC healthcare essentials online.', 'button_text' => 'View Catalog', 'button_url' => '/catalog'],
                ],
                'users' => [
                    ['name' => 'Dr. Farhan Ahmed (Chief Pharmacist)', 'email' => 'farhan@medicareplus.com', 'phone' => '01911-999000', 'role_name' => 'Administrator', 'status' => 'Active'],
                    ['name' => 'Sadia Afrin (Order Desk)', 'email' => 'sadia@medicareplus.com', 'phone' => '01611-222333', 'role_name' => 'Support', 'status' => 'Active'],
                ],
            ],
        ];

        foreach ($modulesSeedConfig as $mod => $cfg) {
            $pageClass = "Modules\\{$mod}\\App\\Models\\ModulePage";
            $menuClass = "Modules\\{$mod}\\App\\Models\\ModuleMenu";
            $sliderClass = "Modules\\{$mod}\\App\\Models\\ModuleSlider";
            $mediaClass = "Modules\\{$mod}\\App\\Models\\ModuleMedia";
            $themeClass = "Modules\\{$mod}\\App\\Models\\ModuleTheme";
            $userClass = "Modules\\{$mod}\\App\\Models\\ModuleUser";
            $settingClass = "Modules\\{$mod}\\App\\Models\\ModuleSetting";

            if ($pageClass::count() === 0) {
                foreach ($cfg['pages'] as $pg) {
                    $pageClass::create(array_merge($pg, ['content' => "Standalone content for {$pg['title']} ({$mod})."]));
                }
            }

            if ($menuClass::count() === 0) {
                foreach ($cfg['menus'] as $m) {
                    $menuClass::create(array_merge($m, ['target' => '_self', 'is_active' => true]));
                }
            }

            if ($sliderClass::count() === 0) {
                foreach ($cfg['sliders'] as $sl) {
                    $sliderClass::create(array_merge($sl, [
                        'image' => $cfg['thumb'],
                        'sort_order' => 1,
                        'is_active' => true,
                    ]));
                }
            }

            if ($mediaClass::count() === 0) {
                $mediaClass::create([
                    'title' => "{$cfg['title']} Hero Showcase",
                    'file_path' => $cfg['thumb'],
                    'file_type' => 'image/jpeg',
                    'file_size' => '210 KB',
                    'alt_text' => $cfg['title'],
                ]);
            }

            if ($themeClass::count() === 0) {
                $themeClass::create([
                    'name' => "{$mod} Signature Theme",
                    'slug' => strtolower($mod) . '-signature',
                    'primary_color' => $cfg['primary'],
                    'secondary_color' => $cfg['secondary'],
                    'font_family' => 'Inter',
                    'layout_style' => $cfg['layout'],
                    'thumbnail' => $cfg['thumb'],
                    'is_active' => true,
                ]);
            }

            if ($userClass::count() === 0) {
                foreach ($cfg['users'] as $u) {
                    $userClass::create($u);
                }
            }

            $settingClass::setValue('site_title', $cfg['title']);
            $settingClass::setValue('site_tagline', $cfg['tagline']);
            $settingClass::setValue('contact_email', $cfg['email']);
            $settingClass::setValue('contact_phone', $cfg['phone']);
            $settingClass::setValue('footer_copyright', '© ' . date('Y') . ' ' . $cfg['title'] . '. All rights reserved.');
        }
    }
}
