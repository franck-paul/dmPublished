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

use ArrayObject;
use dcBlog;
use dcCore;
use dcPage;
use dcWorkspace;
use Dotclear\Helper\Date;
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Fieldset;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Legend;
use Dotclear\Helper\Html\Form\Number;
use Dotclear\Helper\Html\Form\Para;
use Exception;

class BackendBehaviors
{
    public static function getPublishedPosts($core, $nb, $large)
    {
        // Get last $nb recently published posts
        $params = ['post_status' => dcBlog::POST_PUBLISHED];
        if ((int) $nb > 0) {
            $params['limit'] = (int) $nb;
        }
        $rs = dcCore::app()->blog->getPosts($params, false);
        if (!$rs->isEmpty()) {
            $ret = '<ul>';
            while ($rs->fetch()) {
                $ret .= '<li class="line" id="dmrp' . $rs->post_id . '">';
                $ret .= '<a href="post.php?id=' . $rs->post_id . '">' . $rs->post_title . '</a>';
                if ($large) {
                    $dt = '<time datetime="' . Date::iso8601(strtotime($rs->post_dt), dcCore::app()->auth->getInfo('user_tz')) . '">%s</time>';
                    $ret .= ' (' .
                    __('by') . ' ' . $rs->user_id . ' ' . sprintf($dt, __('on') . ' ' .
                        Date::dt2str(dcCore::app()->blog->settings->system->date_format, $rs->post_dt) . ' ' .
                        Date::dt2str(dcCore::app()->blog->settings->system->time_format, $rs->post_dt)) .
                    ')';
                }
                $ret .= '</li>';
            }
            $ret .= '</ul>';
            $ret .= '<p><a href="posts.php?status=' . dcBlog::POST_PUBLISHED . '">' . __('See all published posts') . '</a></p>';

            return $ret;
        }

        return '<p>' . ((int) $nb > 0 ? __('No recently published post') : __('No published post')) . '</p>';
    }

    public static function adminDashboardHeaders()
    {
        $preferences = dcCore::app()->auth->user_prefs->get(My::id());

        return
        dcPage::jsJson('dm_published', [
            'dmPublished_Monitor' => $preferences->monitor,
        ]) .
        dcPage::jsModuleLoad(My::id() . '/js/service.js', dcCore::app()->getVersion(My::id()));
    }

    public static function adminDashboardContents($contents)
    {
        $preferences = dcCore::app()->auth->user_prefs->get(My::id());
        // Add large modules to the contents stack
        if ($preferences->active) {
            $class = ($preferences->posts_large ? 'medium' : 'small');
            $ret   = '<div id="published-posts" class="box ' . $class . '">' .
            '<h3>' . '<img src="' . urldecode(dcPage::getPF(My::id() . '/icon.png')) . '" alt="" />' . ' ' . __('Recently Published posts') . '</h3>';
            $ret .= self::getPublishedPosts(
                dcCore::app(),
                $preferences->posts_nb,
                $preferences->posts_large
            );
            $ret .= '</div>';
            $contents[] = new ArrayObject([$ret]);
        }
    }

    public static function adminAfterDashboardOptionsUpdate()
    {
        $preferences = dcCore::app()->auth->user_prefs->get(My::id());
        // Get and store user's prefs for plugin options
        try {
            // Recently published posts
            $preferences->put('active', !empty($_POST['dmpublished_active']), dcWorkspace::WS_BOOL);
            $preferences->put('posts_nb', (int) $_POST['dmpublished_posts_nb'], dcWorkspace::WS_INT);
            $preferences->put('posts_large', empty($_POST['dmpublished_posts_small']), dcWorkspace::WS_BOOL);
            $preferences->put('monitor', !empty($_POST['dmpublished_monitor']), dcWorkspace::WS_BOOL);
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }
    }

    public static function adminDashboardOptionsForm()
    {
        $preferences = dcCore::app()->auth->user_prefs->get(My::id());

        // Add fieldset for plugin options
        echo
        (new Fieldset('dmpublished'))
        ->legend((new Legend(__('Recently published posts on dashboard'))))
        ->fields([
            (new Para())->items([
                (new Checkbox('dmpublished_active', $preferences->active))
                    ->value(1)
                    ->label((new Label(__('Display recently published posts'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Para())->items([
                (new Number('dmpublished_posts_nb', 1, 999, $preferences->posts_nb))
                    ->label((new Label(__('Number of published posts to display:'), Label::INSIDE_TEXT_BEFORE))),
            ]),
            (new Para())->items([
                (new Checkbox('dmpublished_posts_small', !$preferences->posts_large))
                    ->value(1)
                    ->label((new Label(__('Small screen'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Para())->items([
                (new Checkbox('dmpublished_monitor', !$preferences->monitor))
                    ->value(1)
                    ->label((new Label(__('Monitor published'), Label::INSIDE_TEXT_AFTER))),
            ]),
        ])
        ->render();
    }
}
