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
declare(strict_types=1);

namespace Dotclear\Plugin\dmPublished;

use dcCore;
use dcNsProcess;
use dcWorkspace;
use Exception;

class Install extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = My::checkContext(My::INSTALL);

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        try {
            // Update
            $old_version = dcCore::app()->getVersion(My::id());
            if (version_compare((string) $old_version, '2.0', '<')) {
                // Rename settings workspace
                if (dcCore::app()->auth->user_prefs->exists('dmpublished')) {
                    dcCore::app()->auth->user_prefs->delWorkspace(My::id());
                    dcCore::app()->auth->user_prefs->renWorkspace('dmpublished', My::id());
                }
                // Change settings names (remove published_ prefix in them)
                $rename = function (string $name, dcWorkspace $preferences): void {
                    if ($preferences->prefExists('published_' . $name, true)) {
                        $preferences->rename('published_' . $name, $name);
                    }
                };

                $preferences = dcCore::app()->auth->user_prefs->get(My::id());
                foreach (['posts_nb', 'posts_large', 'monitor'] as $pref) {
                    $rename($pref, $preferences);
                }
                $preferences->rename('published_posts', 'active');
            }

            // Default prefs for recently published posts and comments
            $preferences = dcCore::app()->auth->user_prefs->get(My::id());
            $preferences->put('active', false, dcWorkspace::WS_BOOL, 'Display recently published posts', false, true);
            $preferences->put('posts_nb', 5, dcWorkspace::WS_INT, 'Number of recently published posts displayed', false, true);
            $preferences->put('posts_large', true, dcWorkspace::WS_BOOL, 'Large display', false, true);
            $preferences->put('monitor', false, dcWorkspace::WS_BOOL, 'Monitor', false, true);
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }

        return true;
    }
}
