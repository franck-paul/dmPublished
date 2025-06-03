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
use Dotclear\Database\MetaRecord;
use Dotclear\Helper\Date;
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Div;
use Dotclear\Helper\Html\Form\Fieldset;
use Dotclear\Helper\Html\Form\Img;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Legend;
use Dotclear\Helper\Html\Form\Li;
use Dotclear\Helper\Html\Form\Link;
use Dotclear\Helper\Html\Form\Note;
use Dotclear\Helper\Html\Form\Number;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Set;
use Dotclear\Helper\Html\Form\Text;
use Dotclear\Helper\Html\Form\Timestamp;
use Dotclear\Helper\Html\Form\Ul;
use Exception;

class BackendBehaviors
{
    public static function getPublishedPosts(int $nb, bool $large): string
    {
        // Get last $nb recently published posts
        $params = ['post_status' => App::status()->post()::PUBLISHED];
        if ($nb > 0) {
            $params['limit'] = $nb;
        }

        $rs = App::blog()->getPosts($params, false);

        if (!$rs->isEmpty()) {
            $lines = function (MetaRecord $rs, bool $large) {
                while ($rs->fetch()) {
                    $infos = [];
                    if ($large) {
                        $details = __('on') . ' ' .
                            Date::dt2str(App::blog()->settings()->system->date_format, $rs->post_dt) . ' ' .
                            Date::dt2str(App::blog()->settings()->system->time_format, $rs->post_dt);
                        $infos[] = (new Text(null, __('by') . ' ' . $rs->user_id));
                        $infos[] = (new Timestamp($details))
                            ->datetime(Date::iso8601((int) strtotime($rs->post_dt), App::auth()->getInfo('user_tz')));
                    }
                    yield (new Li('dmrp' . $rs->post_id))
                        ->class('line')
                        ->separator(' ')
                        ->items([
                            (new Link())
                                ->href(App::backend()->url()->get('admin.post', ['id' => $rs->post_id]))
                                ->text($rs->post_title),
                            ... $infos,
                        ]);
                }
            };

            return (new Set())
                ->items([
                    (new Ul())
                        ->items([
                            ... $lines($rs, $large),
                        ]),
                    (new Para())
                        ->items([
                            (new Link())
                                ->href(App::backend()->url()->get('admin.posts', ['status' => App::status()->post()::PUBLISHED]))
                                ->text(__('See all published posts')),
                        ]),
                ])
            ->render();
        }

        return (new Note())
            ->text($nb > 0 ? __('No recently published post') : __('No published post'))
        ->render();
    }

    public static function adminDashboardHeaders(): string
    {
        $preferences = My::prefs();

        return
        Page::jsJson('dm_published', [
            'monitor'  => $preferences->monitor,
            'interval' => ($preferences->interval ?? 300),
        ]) .
        My::jsLoad('service.js');
    }

    /**
     * @param      ArrayObject<int, ArrayObject<int, string>>  $contents  The contents
     */
    public static function adminDashboardContents(ArrayObject $contents): string
    {
        $preferences = My::prefs();
        // Add large modules to the contents stack
        if ($preferences->active) {
            $class = ($preferences->posts_large ? 'medium' : 'small');

            $ret = (new Div('published-posts'))
                ->class(['box', $class])
                ->items([
                    (new Text(
                        'h3',
                        (new Img(urldecode(Page::getPF(My::id() . '/icon.svg'))))
                            ->class('icon-small')
                        ->render() . ' ' . __('Recently Published posts')
                    )),
                    (new Text(null, self::getPublishedPosts(
                        $preferences->posts_nb,
                        $preferences->posts_large
                    ))),
                ])
            ->render();

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
