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

use dcBlog;
use dcCore;

class BackendRest
{
    /**
     * Gets the published posts count.
     *
     * @return     array   The payload.
     */
    public static function getPublishedPostsCount(): array
    {
        return [
            'ret' => true,
            'nb'  => dcCore::app()->blog->getPosts(['post_status' => dcBlog::POST_PUBLISHED], true)->f(0),
        ];
    }

    /**
     * Serve method to check if some entries need to be published.
     *
     * @return     array   The payload.
     */
    public static function checkPublished(): array
    {
        return [
            'ret' => true,
        ];
    }

    /**
     * Gets the last scheduled rows.
     *
     * @return     array   The payload.
     */
    public static function getLastPublishedRows(): array
    {
        $preferences = dcCore::app()->auth->user_prefs->get(My::id());
        $list        = BackendBehaviors::getPublishedPosts(
            dcCore::app(),
            $preferences->posts_nb,
            $preferences->posts_large
        );

        return [
            'ret'  => true,
            'list' => $list,
        ];
    }
}
