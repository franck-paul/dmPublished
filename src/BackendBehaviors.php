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
use Dotclear\App;
use Dotclear\Core\Backend\Page;
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
    public static function getPublishedPosts(int $nb, bool $large): string
    {
        // Get last $nb recently published posts
        $params = ['post_status' => App::blog()::POST_PUBLISHED];
        if ((int) $nb > 0) {
            $params['limit'] = (int) $nb;
        }

        $rs = App::blog()->getPosts($params, false);
        if (!$rs->isEmpty()) {
            $ret = '<ul>';
            while ($rs->fetch()) {
                $ret .= '<li class="line" id="dmrp' . $rs->post_id . '">';
                $ret .= '<a href="' . App::backend()->url()->get('admin.post', ['id' => $rs->post_id]) . '">' . $rs->post_title . '</a>';
                if ($large) {
                    $dt = '<time datetime="' . Date::iso8601((int) strtotime($rs->post_dt), App::auth()->getInfo('user_tz')) . '">%s</time>';
                    $ret .= ' (' .
                    __('by') . ' ' . $rs->user_id . ' ' . sprintf($dt, __('on') . ' ' .
                        Date::dt2str(App::blog()->settings()->system->date_format, $rs->post_dt) . ' ' .
                        Date::dt2str(App::blog()->settings()->system->time_format, $rs->post_dt)) .
                    ')';
                }

                $ret .= '</li>';
            }

            $ret .= '</ul>';

            return $ret . ('<p><a href="' . App::backend()->url()->get('admin.posts', ['status' => App::blog()::POST_PUBLISHED]) . '">' . __('See all published posts') . '</a></p>');
        }

        return '<p>' . ((int) $nb > 0 ? __('No recently published post') : __('No published post')) . '</p>';
    }

    public static function adminDashboardHeaders(): string
    {
        $preferences = My::prefs();

        return
        Page::jsJson('dm_published', [
            'dmPublished_Monitor'  => $preferences->monitor,
            'dmPublished_Interval' => ($preferences->interval ?? 300),
        ]) .
        My::jsLoad('service.js');
    }

    /**
     * @param      ArrayObject<int, ArrayObject<int, non-falsy-string>>  $contents  The contents
     *
     * @return     string
     */
    public static function adminDashboardContents(ArrayObject $contents): string
    {
        $preferences = My::prefs();
        // Add large modules to the contents stack
        if ($preferences->active) {
            $class = ($preferences->posts_large ? 'medium' : 'small');
            $ret   = '<div id="published-posts" class="box ' . $class . '">' .
            '<h3>' . '<img src="' . urldecode(Page::getPF(My::id() . '/icon.svg')) . '" alt="" class="icon-small">' . ' ' . __('Recently Published posts') . '</h3>';
            $ret .= self::getPublishedPosts(
                $preferences->posts_nb,
                $preferences->posts_large
            );
            $ret .= '</div>';
            $contents->append(new ArrayObject([$ret]));
        }

        return '';
    }

    public static function adminAfterDashboardOptionsUpdate(): string
    {
        $preferences = My::prefs();

        // Get and store user's prefs for plugin options
        try {
            // Recently published posts
            $preferences->put('active', !empty($_POST['dmpublished_active']), App::userWorkspace()::WS_BOOL);
            $preferences->put('posts_nb', (int) $_POST['dmpublished_posts_nb'], App::userWorkspace()::WS_INT);
            $preferences->put('posts_large', empty($_POST['dmpublished_posts_small']), App::userWorkspace()::WS_BOOL);
            $preferences->put('monitor', !empty($_POST['dmpublished_monitor']), App::userWorkspace()::WS_BOOL);
            $preferences->put('interval', (int) $_POST['dmpublished_interval'], App::userWorkspace()::WS_INT);
        } catch (Exception $e) {
            App::error()->add($e->getMessage());
        }

        return '';
    }

    public static function adminDashboardOptionsForm(): string
    {
        $preferences = My::prefs();

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
                (new Checkbox('dmpublished_monitor', $preferences->monitor))
                    ->value(1)
                    ->label((new Label(__('Monitor published'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Para())->items([
                (new Number('dmpublished_interval', 0, 9_999_999, $preferences->interval))
                    ->label((new Label(__('Interval in seconds between two refreshes:'), Label::INSIDE_TEXT_BEFORE))),
            ]),
        ])
        ->render();

        return '';
    }
}
