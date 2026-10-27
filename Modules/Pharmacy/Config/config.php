<?php

return [
    'name' => 'Pharmacy',
    'db_prefix' => 'pharma_',

    // Complete standalone menu tree for the Pharmacy Module
    'admin_menus' => [
        [
            'title'  => 'Medicines & Inventory',
            'icon'   => 'bi bi-capsule',
            'route'  => 'admin.pharmacy.medicines.index',
            'active' => 'admin.pharmacy.medicines.*',
        ],
        [
            'title'  => 'Prescriptions & Orders',
            'icon'   => 'bi bi-bag-plus',
            'route'  => 'admin.pharmacy.orders.index',
            'active' => 'admin.pharmacy.orders.*',
        ],
        [
            'title'  => 'Pages',
            'icon'   => 'bi bi-file-earmark-text',
            'route'  => 'admin.pages',
            'params' => ['cms_module' => 'Pharmacy'],
            'active' => 'admin.pages',
        ],
        [
            'title'  => 'Menu Builder',
            'icon'   => 'bi bi-layers',
            'route'  => 'admin.menu-builder',
            'params' => ['cms_module' => 'Pharmacy'],
            'active' => 'admin.menu-builder',
        ],
        [
            'title'  => 'Slider',
            'icon'   => 'bi bi-card-image',
            'route'  => 'admin.slider',
            'params' => ['cms_module' => 'Pharmacy'],
            'active' => 'admin.slider',
        ],
        [
            'title'  => 'Media Library',
            'icon'   => 'bi bi-images',
            'route'  => 'admin.media-library',
            'params' => ['cms_module' => 'Pharmacy'],
            'active' => 'admin.media-library',
        ],
        [
            'title'  => 'Themes',
            'icon'   => 'bi bi-palette',
            'route'  => 'admin.themes',
            'params' => ['cms_module' => 'Pharmacy'],
            'active' => 'admin.themes',
        ],
        [
            'title'  => 'Users',
            'icon'   => 'bi bi-people',
            'route'  => 'admin.users',
            'params' => ['cms_module' => 'Pharmacy'],
            'active' => 'admin.users',
        ],
        [
            'title'  => 'Settings',
            'icon'   => 'bi bi-gear',
            'route'  => 'admin.settings',
            'params' => ['cms_module' => 'Pharmacy'],
            'active' => 'admin.settings',
        ],
    ],
];
