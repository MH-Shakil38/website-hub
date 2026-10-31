<?php

return [
    'name' => 'Clinic',
    'db_prefix' => 'clinic_',

    'admin_menus' => [
        [
            'title'  => 'Pages',
            'icon'   => 'bi bi-file-earmark-text',
            'route'  => 'admin.pages',
            'params' => ['cms_module' => 'Clinic'],
            'active' => 'admin.pages',
        ],
        [
            'title'  => 'Menu Builder',
            'icon'   => 'bi bi-layers',
            'route'  => 'admin.menu-builder',
            'params' => ['cms_module' => 'Clinic'],
            'active' => 'admin.menu-builder',
        ],
        [
            'title'  => 'Slider',
            'icon'   => 'bi bi-card-image',
            'route'  => 'admin.slider',
            'params' => ['cms_module' => 'Clinic'],
            'active' => 'admin.slider',
        ],
        [
            'title'  => 'Media Library',
            'icon'   => 'bi bi-images',
            'route'  => 'admin.media-library',
            'params' => ['cms_module' => 'Clinic'],
            'active' => 'admin.media-library',
        ],
        [
            'title'  => 'Video Gallery',
            'icon'   => 'bi bi-play-btn',
            'route'  => 'admin.video-gallery',
            'params' => ['cms_module' => 'Clinic'],
            'active' => 'admin.video-gallery',
        ],
        [
            'title'  => 'Themes',
            'icon'   => 'bi bi-palette',
            'route'  => 'admin.themes',
            'params' => ['cms_module' => 'Clinic'],
            'active' => 'admin.themes',
        ],
        [
            'title'  => 'Users',
            'icon'   => 'bi bi-people',
            'route'  => 'admin.users',
            'params' => ['cms_module' => 'Clinic'],
            'active' => 'admin.users',
        ],
        [
            'title'  => 'Settings',
            'icon'   => 'bi bi-gear',
            'route'  => 'admin.settings',
            'params' => ['cms_module' => 'Clinic'],
            'active' => 'admin.settings',
        ],
    ],
];
