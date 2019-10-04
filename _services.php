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

if (!defined('DC_CONTEXT_ADMIN')) {return;}

class dmPublishedRest
{
    /**
     * Serve method to get number of recently published posts for current blog.
     *
     * @param     core     <b>dcCore</b>     dcCore instance
     * @param     get     <b>array</b>     cleaned $_GET
     */
    public static function getPublishedPostsCount($core, $get)
    {
        $count = $core->blog->getPosts(['post_status' => 1], true)->f(0);
        $str   = ($count ? sprintf(__('(%d recently published post)', '(%d recenlty published posts)', $count), $count) : '');

        $rsp      = new xmlTag('count');
        $rsp->ret = $str;
        $rsp->nb  = $count;

        return $rsp;
    }
}
