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

$new_version = dcCore::app()->plugins->moduleInfo('dmPublished', 'version');
$old_version = dcCore::app()->getVersion('dmPublished');

if (version_compare((string) $old_version, $new_version, '>=')) {
    return;
}

try {
    dcCore::app()->auth->user_prefs->addWorkspace('dmpublished');

    // Default prefs for recently published posts and comments
    dcCore::app()->auth->user_prefs->dmpublished->put('published_posts', false, 'boolean', 'Display recently published posts', false, true);
    dcCore::app()->auth->user_prefs->dmpublished->put('published_posts_nb', 5, 'integer', 'Number of recently published posts displayed', false, true);
    dcCore::app()->auth->user_prefs->dmpublished->put('published_posts_large', true, 'boolean', 'Large display', false, true);
    dcCore::app()->setVersion('dmPublished', $new_version);

    return true;
} catch (Exception $e) {
    dcCore::app()->error->add($e->getMessage());
}

return false;
