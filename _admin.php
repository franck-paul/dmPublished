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

// dead but useful code, in order to have translations
__('Recently Published Posts Dashboard Module') . __('Display recently published posts on dashboard');

// Dashboard behaviours
dcCore::app()->addBehavior('adminDashboardContents', ['dmPublishedBehaviors', 'adminDashboardContents']);
dcCore::app()->addBehavior('adminDashboardHeaders', ['dmPublishedBehaviors', 'adminDashboardHeaders']);
dcCore::app()->addBehavior('adminDashboardFavsIcon', ['dmPublishedBehaviors', 'adminDashboardFavsIcon']);

dcCore::app()->addBehavior('adminAfterDashboardOptionsUpdate', ['dmPublishedBehaviors', 'adminAfterDashboardOptionsUpdate']);
dcCore::app()->addBehavior('adminDashboardOptionsForm', ['dmPublishedBehaviors', 'adminDashboardOptionsForm']);

# BEHAVIORS
class dmPublishedBehaviors
{
    private static function getPublishedPosts($core, $nb, $large)
    {
        // Get last $nb recently published posts
        $params = ['post_status' => 1];
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
                    $ret .= ' (' .
                    __('by') . ' ' . $rs->user_id . ' ' . __('on') . ' ' .
                    dt::dt2str(dcCore::app()->blog->settings->system->date_format, $rs->post_dt) . ' ' .
                    dt::dt2str(dcCore::app()->blog->settings->system->time_format, $rs->post_dt) . ')';
                }
                $ret .= '</li>';
            }
            $ret .= '</ul>';
            $ret .= '<p><a href="posts.php?status=1">' . __('See all published posts') . '</a></p>';

            return $ret;
        }

        return '<p>' . ((int) $nb > 0 ? __('No recently published post') : __('No published post')) . '</p>';
    }

    public static function adminDashboardHeaders()
    {
        return dcPage::jsModuleLoad('dmPublished/js/service.js', dcCore::app()->getVersion('dmPublished'));
    }

    public static function adminDashboardContents($core, $contents)
    {
        // Add large modules to the contents stack
        dcCore::app()->auth->user_prefs->addWorkspace('dmpublished');
        if (dcCore::app()->auth->user_prefs->dmpublished->published_posts) {
            $class = (dcCore::app()->auth->user_prefs->dmpublished->published_posts_large ? 'medium' : 'small');
            $ret   = '<div id="published-posts" class="box ' . $class . '">' .
            '<h3>' . '<img src="' . urldecode(dcPage::getPF('dmPublished/icon.png')) . '" alt="" />' . ' ' . __('Recently Published posts') . '</h3>';
            $ret .= dmPublishedBehaviors::getPublishedPosts(
                dcCore::app(),
                dcCore::app()->auth->user_prefs->dmpublished->published_posts_nb,
                dcCore::app()->auth->user_prefs->dmpublished->published_posts_large
            );
            $ret .= '</div>';
            $contents[] = new ArrayObject([$ret]);
        }
    }

    public static function adminAfterDashboardOptionsUpdate($userID)
    {
        // Get and store user's prefs for plugin options
        dcCore::app()->auth->user_prefs->addWorkspace('dmpublished');

        try {
            // Recently published posts
            dcCore::app()->auth->user_prefs->dmpublished->put('published_posts', !empty($_POST['dmpublished_posts']), 'boolean');
            dcCore::app()->auth->user_prefs->dmpublished->put('published_posts_nb', (int) $_POST['dmpublished_posts_nb'], 'integer');
            dcCore::app()->auth->user_prefs->dmpublished->put('published_posts_large', empty($_POST['dmpublished_posts_small']), 'boolean');
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }
    }

    public static function adminDashboardOptionsForm($core)
    {
        // Add fieldset for plugin options
        dcCore::app()->auth->user_prefs->addWorkspace('dmpublished');

        echo '<div class="fieldset" id="dmpublished"><h4>' . __('Recently published posts on dashboard') . '</h4>' .

        '<p>' .
        form::checkbox('dmpublished_posts', 1, dcCore::app()->auth->user_prefs->dmpublished->published_posts) . ' ' .
        '<label for="dmpublished_posts" class="classic">' . __('Display recently published posts') . '</label></p>' .

        '<p><label for="dmpublished_posts_nb" class="classic">' . __('Number of published posts to display:') . '</label>' .
        form::number('dmpublished_posts_nb', 1, 999, dcCore::app()->auth->user_prefs->dmpublished->published_posts_nb) .
        '</p>' .

        '<p>' .
        form::checkbox('dmpublished_posts_small', 1, !dcCore::app()->auth->user_prefs->dmpublished->published_posts_large) . ' ' .
        '<label for="dmpublished_posts_small" class="classic">' . __('Small screen') . '</label></p>' .

            '</div>';
    }
}
