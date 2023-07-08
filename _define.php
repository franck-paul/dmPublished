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
    '2.3.2',
    [
        'requires'    => [['core', '2.26']],
        'permissions' => dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_ADMIN,
        ]),
        'type'     => 'plugin',
        'settings' => [
            'pref' => '#user-favorites.dmpublished',
        ],

        'details'    => 'https://open-time.net/?q=dmPublished',
        'support'    => 'https://github.com/franck-paul/dmPublished',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/dmPublished/master/dcstore.xml',
    ]
);
