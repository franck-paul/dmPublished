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
use Dotclear\Core\Process;

class Backend extends Process
{
    public static function init(): bool
    {
        // dead but useful code, in order to have translations
        __('Recently Published Posts Dashboard Module');
        __('Display recently published posts on dashboard');

        return self::status(My::checkContext(My::BACKEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        // Dashboard behaviours
        App::behavior()->addBehaviors([
            'adminDashboardContentsV2' => BackendBehaviors::adminDashboardContents(...),
            'adminDashboardHeaders'    => BackendBehaviors::adminDashboardHeaders(...),

            'adminAfterDashboardOptionsUpdate' => BackendBehaviors::adminAfterDashboardOptionsUpdate(...),
            'adminDashboardOptionsFormV2'      => BackendBehaviors::adminDashboardOptionsForm(...),
        ]);

        // Register REST methods
        App::rest()->addFunction('dmPublishedPostsCount', BackendRest::getPublishedPostsCount(...));
        App::rest()->addFunction('dmPublishedCheck', BackendRest::checkPublished(...));
        App::rest()->addFunction('dmPublisheduledRows', BackendRest::getLastPublishedRows(...));

        return true;
    }
}
