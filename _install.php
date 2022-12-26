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
if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

if (!dcCore::app()->newVersion(basename(__DIR__), dcCore::app()->plugins->moduleInfo(basename(__DIR__), 'version'))) {
    return;
}

try {
    // Default prefs for recently published posts and comments
    dcCore::app()->auth->user_prefs->dmpublished->put('published_posts', false, 'boolean', 'Display recently published posts', false, true);
    dcCore::app()->auth->user_prefs->dmpublished->put('published_posts_nb', 5, 'integer', 'Number of recently published posts displayed', false, true);
    dcCore::app()->auth->user_prefs->dmpublished->put('published_posts_large', true, 'boolean', 'Large display', false, true);

    return true;
} catch (Exception $e) {
    dcCore::app()->error->add($e->getMessage());
}

return false;
