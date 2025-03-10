<?php

/**
 * @brief dmPublished, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul carnet.franck.paul@gmail.com
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
$this->registerModule(
    'Recently Published Posts Dashboard Module',
    'Display recently published posts on dashboard',
    'Franck Paul',
    '5.5',
    [
        'date'        => '2025-03-06T00:25:13+0100',
        'requires'    => [['core', '2.33']],
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
