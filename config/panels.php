<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Unified SaaS Dashboard Role Panels (Super Admin, Reseller, Client)
    |--------------------------------------------------------------------------
    |
    | All three panels render inside the exact same master layout
    | (resources/views/admin/layouts/master.blade.php) while dynamically
    | adjusting the sidebar navigation and data scope based on user->panelType().
    |
    */

    'roles' => [
        'super_admin' => [
            'label' => 'Super Admin Panel',
            'badge_bg' => '#FFE4EC',
            'badge_color' => '#EC134A',
            'show_all_theme_modules' => true,
            'platform_menus' => [
                [
                    'title' => 'Dashboard',
                    'icon' => 'bi bi-grid-fill',
                    'route' => 'admin.dashboard',
                    'active' => 'admin.dashboard',
                ],
                [
                    'title' => 'SaaS Homepage Builder',
                    'icon' => 'bi bi-layout-text-window-reverse',
                    'route' => 'admin.saas.homepage',
                    'active' => 'admin.saas.homepage*',
                ],
                [
                    'title' => 'Tenants (Client Sites)',
                    'icon' => 'bi bi-hdd-network-fill',
                    'route' => 'admin.tenants.index',
                    'active' => 'admin.tenants.*',
                ],
                [
                    'title' => 'Domains & SSL',
                    'icon' => 'bi bi-link-45deg',
                    'route' => 'admin.domains.index',
                    'active' => 'admin.domains.*',
                ],
                [
                    'title' => 'Resellers & Clients',
                    'icon' => 'bi bi-people-fill',
                    'route' => 'admin.saas.accounts',
                    'active' => 'admin.saas.accounts*',
                ],
                [
                    'title' => 'Reseller Hub & Payouts',
                    'icon' => 'bi bi-wallet2',
                    'route' => 'admin.reseller.hub',
                    'active' => 'admin.reseller.*',
                ],
                [
                    'title' => 'Themes & Modules',
                    'icon' => 'bi bi-globe2',
                    'route' => 'admin.websites',
                    'active' => 'admin.websites*',
                ],
                [
                    'title' => 'Plans & Packages',
                    'icon' => 'bi bi-box-seam-fill',
                    'route' => 'admin.plans.index',
                    'active' => 'admin.plans.*',
                ],
                [
                    'title' => 'Subscriptions & Invoices',
                    'icon' => 'bi bi-receipt-cutoff',
                    'route' => 'admin.billing.index',
                    'active' => 'admin.billing.*',
                ],
                [
                    'title' => 'Transactions Ledger',
                    'icon' => 'bi bi-credit-card-2-front-fill',
                    'route' => 'admin.transactions',
                    'active' => 'admin.transactions*',
                ],
                [
                    'title' => 'DB Backups & Restore',
                    'icon' => 'bi bi-cloud-arrow-down-fill',
                    'route' => 'admin.backups.index',
                    'active' => 'admin.backups.*',
                ],
                [
                    'title' => 'Support Tickets',
                    'icon' => 'bi bi-life-preserver',
                    'route' => 'admin.support.tickets',
                    'active' => 'admin.support.*',
                ],
            ],
        ],

        'reseller' => [
            'label' => 'Reseller Partner Panel',
            'badge_bg' => '#DBEAFE',
            'badge_color' => '#2563EB',
            'show_all_theme_modules' => false,
            'platform_menus' => [
                [
                    'title' => 'Reseller Dashboard',
                    'icon' => 'bi bi-grid-fill',
                    'route' => 'admin.dashboard',
                    'active' => 'admin.dashboard',
                ],
                [
                    'title' => 'My Clients',
                    'icon' => 'bi bi-people-fill',
                    'route' => 'admin.saas.accounts',
                    'active' => 'admin.saas.accounts*',
                ],
                [
                    'title' => 'Client Websites',
                    'icon' => 'bi bi-hdd-network-fill',
                    'route' => 'admin.tenants.index',
                    'active' => 'admin.tenants.*',
                ],
                [
                    'title' => 'Domains & SSL',
                    'icon' => 'bi bi-link-45deg',
                    'route' => 'admin.domains.index',
                    'active' => 'admin.domains.*',
                ],
                [
                    'title' => 'Reseller Plans',
                    'icon' => 'bi bi-box-seam-fill',
                    'route' => 'admin.plans.index',
                    'active' => 'admin.plans.*',
                ],
                [
                    'title' => 'Billing & Invoices',
                    'icon' => 'bi bi-receipt-cutoff',
                    'route' => 'admin.billing.index',
                    'active' => 'admin.billing.*',
                ],
                [
                    'title' => 'Wallet, Referral & Payouts',
                    'icon' => 'bi bi-wallet2',
                    'route' => 'admin.reseller.hub',
                    'active' => 'admin.reseller.*',
                ],
                [
                    'title' => 'DB Backups & Restore',
                    'icon' => 'bi bi-cloud-arrow-down-fill',
                    'route' => 'admin.backups.index',
                    'active' => 'admin.backups.*',
                ],
                [
                    'title' => 'Support Tickets',
                    'icon' => 'bi bi-life-preserver',
                    'route' => 'admin.support.tickets',
                    'active' => 'admin.support.*',
                ],
            ],
        ],

        'client' => [
            'label' => 'Client Website Panel',
            'badge_bg' => '#DCFCE7',
            'badge_color' => '#10B981',
            'show_all_theme_modules' => false,
            'platform_menus' => [
                [
                    'title' => 'Overview',
                    'icon' => 'bi bi-grid-fill',
                    'route' => 'admin.dashboard',
                    'active' => 'admin.dashboard',
                ],
                [
                    'title' => 'My Websites & Domains',
                    'icon' => 'bi bi-globe2',
                    'route' => 'admin.tenants.index',
                    'active' => 'admin.tenants.*',
                ],
                [
                    'title' => 'Custom Domains & SSL',
                    'icon' => 'bi bi-link-45deg',
                    'route' => 'admin.domains.index',
                    'active' => 'admin.domains.*',
                ],
                [
                    'title' => 'Plans & Upgrade',
                    'icon' => 'bi bi-box-seam-fill',
                    'route' => 'admin.plans.index',
                    'active' => 'admin.plans.*',
                ],
                [
                    'title' => 'Billing & Invoices',
                    'icon' => 'bi bi-receipt',
                    'route' => 'admin.billing.index',
                    'active' => 'admin.billing.*',
                ],
                [
                    'title' => 'Website Backups',
                    'icon' => 'bi bi-cloud-arrow-down-fill',
                    'route' => 'admin.backups.index',
                    'active' => 'admin.backups.*',
                ],
                [
                    'title' => 'Support Tickets',
                    'icon' => 'bi bi-life-preserver',
                    'route' => 'admin.support.tickets',
                    'active' => 'admin.support.*',
                ],
            ],
        ],
    ],

];
