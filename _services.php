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

class dmPublishedRest
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
}
