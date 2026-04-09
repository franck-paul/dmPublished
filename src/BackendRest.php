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

use Dotclear\App;

class BackendRest
{
    /**
     * Gets the published posts count.
     *
     * @return     array<string, mixed>   The payload.
     */
    public static function getPublishedPostsCount(): array
    {
        $count = is_numeric($count = App::blog()->getPosts(['post_status' => App::status()->post()::PUBLISHED], true)->f(0)) ? (int) $count : 0;

        return [
            'ret' => true,
            'nb'  => $count,
        ];
    }

    /**
     * Serve method to check if some entries need to be published.
     *
     * @return     array<string, mixed>   The payload.
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
     * @return     array<string, mixed>   The payload.
     */
    public static function getLastPublishedRows(): array
    {
        $preferences = My::prefs();

        $posts_nb = is_numeric($posts_nb = $preferences->posts_nb) ? (int) $posts_nb : 0;

        $list = BackendBehaviors::getPublishedPosts(
            $posts_nb,
            (bool) $preferences->posts_large
        );

        return [
            'ret'  => true,
            'list' => $list,
        ];
    }
}
