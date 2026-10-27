<?php

return [
    'name' => 'RealEstate',
    'db_prefix' => 're_',

    // Complete standalone menu tree for the Real Estate Module
    'admin_menus' => [
        [
            'title'  => 'Properties & Listings',
            'icon'   => 'bi bi-houses',
            'route'  => 'admin.realestate.properties.index',
            'active' => 'admin.realestate.properties.*',
        ],
        [
            'title'  => 'Buyer Inquiries',
            'icon'   => 'bi bi-envelope-paper',
            'route'  => 'admin.realestate.inquiries.index',
            'active' => 'admin.realestate.inquiries.*',
        ],
        [
            'title'  => 'Pages',
            'icon'   => 'bi bi-file-earmark-text',
            'route'  => 'admin.pages',
            'params' => ['cms_module' => 'RealEstate'],
            'active' => 'admin.pages',
        ],
        [
            'title'  => 'Menu Builder',
            'icon'   => 'bi bi-layers',
            'route'  => 'admin.menu-builder',
            'params' => ['cms_module' => 'RealEstate'],
            'active' => 'admin.menu-builder',
        ],
        [
            'title'  => 'Slider',
            'icon'   => 'bi bi-card-image',
            'route'  => 'admin.slider',
            'params' => ['cms_module' => 'RealEstate'],
            'active' => 'admin.slider',
        ],
        [
            'title'  => 'Media Library',
            'icon'   => 'bi bi-images',
            'route'  => 'admin.media-library',
            'params' => ['cms_module' => 'RealEstate'],
            'active' => 'admin.media-library',
        ],
        [
            'title'  => 'Themes',
            'icon'   => 'bi bi-palette',
            'route'  => 'admin.themes',
            'params' => ['cms_module' => 'RealEstate'],
            'active' => 'admin.themes',
        ],
        [
            'title'  => 'Users',
            'icon'   => 'bi bi-people',
            'route'  => 'admin.users',
            'params' => ['cms_module' => 'RealEstate'],
            'active' => 'admin.users',
        ],
        [
            'title'  => 'Settings',
            'icon'   => 'bi bi-gear',
            'route'  => 'admin.settings',
            'params' => ['cms_module' => 'RealEstate'],
            'active' => 'admin.settings',
        ],
    ],
];
