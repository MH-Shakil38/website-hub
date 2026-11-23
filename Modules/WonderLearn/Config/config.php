<?php

return [
    'name' => 'WonderLearn',
    'db_prefix' => 'wl_',

    'admin_menus' => [
        [
            'title'  => 'WonderLearn Dashboard',
            'icon'   => 'bi bi-speedometer2',
            'route'  => 'admin.wonderlearn.dashboard',
            'active' => 'admin.wonderlearn.dashboard',
        ],
        [
            'title'  => 'Explore by Topic',
            'icon'   => 'bi bi-grid-3x3-gap-fill',
            'route'  => 'admin.wonderlearn.topics.index',
            'active' => 'admin.wonderlearn.topics.*',
        ],
        [
            'title'  => 'Magical Stories',
            'icon'   => 'bi bi-book-half',
            'route'  => 'admin.wonderlearn.stories.index',
            'active' => 'admin.wonderlearn.stories.*',
        ],
        [
            'title'  => 'Play & Learn Games',
            'icon'   => 'bi bi-controller',
            'route'  => 'admin.wonderlearn.games.index',
            'active' => 'admin.wonderlearn.games.*',
        ],
        [
            'title'  => 'Featured Adventures',
            'icon'   => 'bi bi-compass-fill',
            'route'  => 'admin.wonderlearn.adventures.index',
            'active' => 'admin.wonderlearn.adventures.*',
        ],
        [
            'title'  => 'Parent Testimonials',
            'icon'   => 'bi bi-chat-heart-fill',
            'route'  => 'admin.wonderlearn.testimonials.index',
            'active' => 'admin.wonderlearn.testimonials.*',
        ],
        [
            'title'  => 'Kid Enrollments',
            'icon'   => 'bi bi-person-hearts',
            'route'  => 'admin.wonderlearn.enrollments.index',
            'active' => 'admin.wonderlearn.enrollments.*',
        ],
        [
            'title'  => 'Theme & Hero Settings',
            'icon'   => 'bi bi-palette2',
            'route'  => 'admin.wonderlearn.settings',
            'active' => 'admin.wonderlearn.settings*',
        ],
        [
            'title'  => 'Menu Builder',
            'icon'   => 'bi bi-layers',
            'route'  => 'admin.menu-builder',
            'params' => ['cms_module' => 'WonderLearn'],
            'active' => 'admin.menu-builder',
        ],
        [
            'title'  => 'Sliders & Banners',
            'icon'   => 'bi bi-images',
            'route'  => 'admin.slider',
            'params' => ['cms_module' => 'WonderLearn'],
            'active' => 'admin.slider',
        ],
        [
            'title'  => 'Gallery & Media',
            'icon'   => 'bi bi-collection-play',
            'route'  => 'admin.media-library',
            'params' => ['cms_module' => 'WonderLearn'],
            'active' => 'admin.media-library',
        ],
        [
            'title'  => 'Learning Videos',
            'icon'   => 'bi bi-play-btn-fill',
            'route'  => 'admin.video-gallery',
            'params' => ['cms_module' => 'WonderLearn'],
            'active' => 'admin.video-gallery',
        ],
        [
            'title'  => 'Pages',
            'icon'   => 'bi bi-file-earmark-text',
            'route'  => 'admin.pages',
            'params' => ['cms_module' => 'WonderLearn'],
            'active' => 'admin.pages',
        ],
        [
            'title'  => 'Themes',
            'icon'   => 'bi bi-palette',
            'route'  => 'admin.themes',
            'params' => ['cms_module' => 'WonderLearn'],
            'active' => 'admin.themes',
        ],
        [
            'title'  => 'Users',
            'icon'   => 'bi bi-people',
            'route'  => 'admin.users',
            'params' => ['cms_module' => 'WonderLearn'],
            'active' => 'admin.users',
        ],
        [
            'title'  => 'General Settings',
            'icon'   => 'bi bi-gear',
            'route'  => 'admin.settings',
            'params' => ['cms_module' => 'WonderLearn'],
            'active' => 'admin.settings',
        ],
    ],
];
