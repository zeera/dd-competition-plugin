<?php
/**
 * Controller for the Entry List
 */

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\AdminPages\EntryLists;
use WpDigitalDriveCompetitions\Helpers\AdminHelper;
use WpDigitalDriveCompetitions\Models\TicketNumber;

/**
 * Controller for the Entry List
 */
class Controller extends AdminHelper
{
    protected $ticketNumbers;
    protected $product_data;
    /**
     * View
     */
    public function actionView($product_id = null) {
        $ticketNumbersModel = new TicketNumber;
        $product_id = $product_id ?: $this->getValue('product_id');
        $this->product_data = wc_get_product( $product_id );
        $this->ticketNumbers = $ticketNumbersModel->getProductEntryList( $product_id );
        $this->buildPage(dirname(__FILE__) . '/view.php');
    }
}
