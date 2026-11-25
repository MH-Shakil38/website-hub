<?php

return [
    'name' => 'EventManagement',
    'db_prefix' => 'em_',

    'admin_menus' => [
        [
            'title'  => 'Eventify Dashboard',
            'icon'   => 'bi bi-speedometer2',
            'route'  => 'admin.eventmanagement.dashboard',
            'active' => 'admin.eventmanagement.dashboard',
        ],
        [
            'title'  => 'Event Speakers',
            'icon'   => 'bi bi-mic-fill',
            'route'  => 'admin.eventmanagement.speakers.index',
            'active' => 'admin.eventmanagement.speakers.*',
        ],
        [
            'title'  => 'Event Schedule Plan',
            'icon'   => 'bi bi-calendar2-week-fill',
            'route'  => 'admin.eventmanagement.schedules.index',
            'active' => 'admin.eventmanagement.schedules.*',
        ],
        [
            'title'  => 'Ticket Pricing Plans',
            'icon'   => 'bi bi-ticket-perforated-fill',
            'route'  => 'admin.eventmanagement.pricing.index',
            'active' => 'admin.eventmanagement.pricing.*',
        ],
        [
            'title'  => 'Ticket Bookings',
            'icon'   => 'bi bi-person-badge-fill',
            'route'  => 'admin.eventmanagement.bookings.index',
            'active' => 'admin.eventmanagement.bookings.*',
        ],
        [
            'title'  => 'Sponsors & Partners',
            'icon'   => 'bi bi-building-check',
            'route'  => 'admin.eventmanagement.sponsors.index',
            'active' => 'admin.eventmanagement.sponsors.*',
        ],
        [
            'title'  => 'Recent Memories',
            'icon'   => 'bi bi-camera-fill',
            'route'  => 'admin.eventmanagement.memories.index',
            'active' => 'admin.eventmanagement.memories.*',
        ],
        [
            'title'  => 'Hero, Countdown & Venue',
            'icon'   => 'bi bi-sliders2',
            'route'  => 'admin.eventmanagement.settings',
            'active' => 'admin.eventmanagement.settings*',
        ],
        [
            'title'  => 'Menu Builder',
            'icon'   => 'bi bi-layers',
            'route'  => 'admin.menu-builder',
            'params' => ['cms_module' => 'EventManagement'],
            'active' => 'admin.menu-builder',
        ],
        [
            'title'  => 'Sliders & Banners',
            'icon'   => 'bi bi-images',
            'route'  => 'admin.slider',
            'params' => ['cms_module' => 'EventManagement'],
            'active' => 'admin.slider',
        ],
        [
            'title'  => 'Gallery & Media',
            'icon'   => 'bi bi-collection-play',
            'route'  => 'admin.media-library',
            'params' => ['cms_module' => 'EventManagement'],
            'active' => 'admin.media-library',
        ],
        [
            'title'  => 'Event Videos',
            'icon'   => 'bi bi-play-btn-fill',
            'route'  => 'admin.video-gallery',
            'params' => ['cms_module' => 'EventManagement'],
            'active' => 'admin.video-gallery',
        ],
        [
            'title'  => 'Pages',
            'icon'   => 'bi bi-file-earmark-text',
            'route'  => 'admin.pages',
            'params' => ['cms_module' => 'EventManagement'],
            'active' => 'admin.pages',
        ],
        [
            'title'  => 'Themes',
            'icon'   => 'bi bi-palette',
            'route'  => 'admin.themes',
            'params' => ['cms_module' => 'EventManagement'],
            'active' => 'admin.themes',
        ],
        [
            'title'  => 'Users',
            'icon'   => 'bi bi-people',
            'route'  => 'admin.users',
            'params' => ['cms_module' => 'EventManagement'],
            'active' => 'admin.users',
        ],
        [
            'title'  => 'General Settings',
            'icon'   => 'bi bi-gear',
            'route'  => 'admin.settings',
            'params' => ['cms_module' => 'EventManagement'],
            'active' => 'admin.settings',
        ],
    ],
];
