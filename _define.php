<?php

/**
 * @brief dmPublished, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul contact@open-time.net
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
$this->registerModule(
    'Recently Published Posts Dashboard Module',
    'Display recently published posts on dashboard',
    'Franck Paul',
    '9.0',
    [
        'date'     => '2026-04-29T14:24:33+0200',
        'requires' => [
            ['core', '2.39'],
            ['dmHelper', '5.0'],
        ],
        'permissions' => 'My',
        'type'        => 'plugin',
        'settings'    => [
            'pref' => '#user-favorites.dmpublished',
        ],

        'details'    => 'https://open-time.net/?q=dmPublished',
        'support'    => 'https://github.com/franck-paul/dmPublished',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/dmPublished/main/dcstore.xml',
        'license'    => 'gpl2',
    ]
);
