<?php

/**
 * Ajax template
 */

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Helpers;

use WpDigitalDriveCompetitions\Core\Controller;

/**
 * Ajax template
 */
class AjaxHelper extends Controller
{
    public $load_view_file = false;

    public string $base_url = WPDIGITALDRIVE_COMPETITIONS_URL;
}
