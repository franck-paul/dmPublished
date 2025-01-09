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
        return [
            'ret' => true,
            'nb'  => (int) App::blog()->getPosts(['post_status' => App::status()->post()::PUBLISHED], true)->f(0),
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

        $list = BackendBehaviors::getPublishedPosts(
            $preferences->posts_nb,
            $preferences->posts_large
        );

        return [
            'ret'  => true,
            'list' => $list,
        ];
    }
}
