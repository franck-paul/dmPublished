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

if (!defined('DC_RC_PATH')) {return;}

$this->registerModule(
    "Recently Published Dashboard Module",           // Name
    "Display recently published posts on dashboard", // Description
    "Franck Paul",                                   // Author
    '0.1',                                           // Version
    [
        'requires'    => [['core', '2.15']],
        'permissions' => 'admin',                                   // Permissions
        'type'        => 'plugin',                                  // Type
        'support'     => 'https://open-time.net/?q=dmPublished',    // Support URL
        'settings'    => ['pref' => '#user-favorites.dmpublished'] // Settings
    ]
);
