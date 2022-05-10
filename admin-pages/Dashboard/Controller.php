<?php
/**
 * Controller for the dashboard
 */

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\AdminPages\Dashboard;
use WpDigitalDriveCompetitions\Helpers\AdminHelper;
use WpDigitalDriveCompetitions\Hooks\PriceMatchProcess;

/**
 * Controller for the dashboard
 */
class Controller extends AdminHelper
{
    /** Controller */
    protected string $controller = 'dashboard';
    protected $product = [];
    protected $modelData = [];

    /**
     * Index
     */
    public function actionIndex()
    {
        $this->buildPage(dirname(__FILE__) . '/index.php');
    }
}
