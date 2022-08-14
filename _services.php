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
     * @param      array   $get    The get
     *
     * @return     xmlTag  The published posts count.
     */
    public static function getPublishedPostsCount($get)
    {
        return [
            'ret' => true,
            'nb'  => dcCore::app()->blog->getPosts(['post_status' => 1], true)->f(0),
        ];
    }
}
