<?php

return [
    'name' => 'IvfCenter',
    'db_prefix' => 'ivf_',

    'admin_menus' => [
        [
            'section' => 'IVF CENTER CMS',
            'title'   => 'Consultation Requests',
            'icon'    => 'bi bi-calendar-check',
            'route'   => 'admin.ivfcenter.consultations',
            'active'  => 'admin.ivfcenter.consultations*',
        ],
        [
            'title'  => 'Pages',
            'icon'   => 'bi bi-file-earmark-text',
            'route'  => 'admin.pages',
            'params' => ['cms_module' => 'IvfCenter'],
            'active' => 'admin.pages',
        ],
        [
            'title'  => 'Menu Builder',
            'icon'   => 'bi bi-layers',
            'route'  => 'admin.menu-builder',
            'params' => ['cms_module' => 'IvfCenter'],
            'active' => 'admin.menu-builder',
        ],
        [
            'title'  => 'Theme Settings',
            'icon'   => 'bi bi-palette',
            'route'  => 'admin.ivfcenter.settings',
            'active'  => 'admin.ivfcenter.settings*',
        ],
    ],
];
