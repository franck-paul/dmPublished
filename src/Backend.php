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

use dcCore;
use dcNsProcess;

class Backend extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = My::checkContext(My::BACKEND);

        // dead but useful code, in order to have translations
        __('Recently Published Posts Dashboard Module') . __('Display recently published posts on dashboard');

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        // Dashboard behaviours
        dcCore::app()->addBehaviors([
            'adminDashboardContentsV2' => [BackendBehaviors::class, 'adminDashboardContents'],
            'adminDashboardHeaders'    => [BackendBehaviors::class, 'adminDashboardHeaders'],

            'adminAfterDashboardOptionsUpdate' => [BackendBehaviors::class, 'adminAfterDashboardOptionsUpdate'],
            'adminDashboardOptionsFormV2'      => [BackendBehaviors::class, 'adminDashboardOptionsForm'],
        ]);

        // Register REST methods
        dcCore::app()->rest->addFunction('dmPublishedPostsCount', [BackendRest::class, 'getPublishedPostsCount']);
        dcCore::app()->rest->addFunction('dmPublishedCheck', [BackendRest::class, 'checkPublished']);
        dcCore::app()->rest->addFunction('dmPublisheduledRows', [BackendRest::class, 'getLastPublishedRows']);

        return true;
    }
}
