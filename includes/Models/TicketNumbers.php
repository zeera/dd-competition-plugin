<?php

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Models;

use WpDigitalDriveCompetitions\Helpers\AdminHelper;
use WpDigitalDriveCompetitions\Helpers\TableHelper;

/**
 * Influencer Model
 *
 * it extends tablehelper which has a lot of boilerplate/default functions useful for a model that uses a table
 * as its main data source.
 */
class TicketNumbers extends TableHelper
{
    /** Table name */
    public string $tableName = '#prefix_ticket_numbers';

    /**
     * Create a new model
     */
    public function __construct()
    {
        $this->adminHelper = New AdminHelper();
    }


    /** Validation array
     *
     * Validation logic
     * Expects an associative array with
     * [key] = html "name" of field from $_POST
     * value[0] = database column name
     * value[1] = type of validation to use
     * value[2] = is a required field
     * value[3] = max field length
     * value[4] = extra validation information (some validation types such as REGEX require additional info otherwise pass everything)
     *
     * Validation list is inside validation.php
     *
     * This is also passed to auto create insert/update or other calls
     *
     * Now, if a update is made with blank data for a field, it will submit it with a NULL result.
     * if that update field is not submitted at all, it will skip the field in the submission
     *
     */
    public function getValidationArray( $update = false )
    {
        $required = ($update == false) ? true : false;
        return [
            // 'id' => ['id', 'INT', true], //We dont need the id as its auto incremented
            'userid' => ['userid', 'INT', $required],
            'email' => ['email', 'STRING', false],
            'order_id' => ['order_id', 'INT', $required],
            'ticket_number' => ['ticket_number', 'INT', $required, 200],
            'answer' => ['answer', 'STRING', false],
            'product_id' => ['product_id', 'INT', $required],
            'item_id' => ['item_id', 'INT', $required],
            'last_updated_by' => ['last_updated_by', 'STRING', false],
            'created_by' => ['created_by', 'STRING', false],
        ];
    }

    public function getModelData( $id )
    {
        $modelData = $this->queryData( $id );
        return $modelData[0];
    }

    public function store( $request ) {


        // $this->adminHelper->dd($request);
        $validation = $this->getValidationArray();
        $newId = null;

        $current_user = \wp_get_current_user();

        $request['created_by'] = $current_user->user_login;
        $request['last_updated_by'] = $current_user->user_login;

        $criteriaArray = [
            'tablename' => $this->tableName,
            'type' => 'insert',
        ];

        /**
         * The validateDAta function grabs the criteria, the validation array
         * then returns a list of errors and the sql generated query and values.
         *
         * The sql generated is really only suitable for mysql/mariadb
         */
        $resultarray = $this->validateData(
            $criteriaArray,
            $validation,
            $request,
            []
        );

        //We are setting the errors to the $this->errors array to make it easy to check on these from the calling controller
        if (count($resultarray['errors']) > 0) {
            $this->errors = $resultarray['errors'];
        }

        //Firing the query via the wordpress sql function
        if (count($this->errors) == 0) {
            //We can make the call automatically grab the creation id if the primary key is an auto increment one.
            $newId = $this->insertWp(
                $resultarray['query'],
                $resultarray['values'],
                true
            );
        }

        if (count($this->errors) > 0) {
            return false;
        }

        return $newId;
    }

    /**
     * Returns a Integer of strlen()==$length
     * of url-safe charachters.
     */
    public function generateTicketNumber($length = 8)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        $ticketNumber = $randomString;
        $count = $this->isTicketNumberExist($ticketNumber);

        if( $count > 0 ) {
            $this->generateTicketNumber();
        } else {
            return $ticketNumber;
        }
    }

    /**
     * Validate Ticket Number
     */
    public function isTicketNumberExist( $ticketNumber, $productID )
    {
        $ticketData = $this->queryWp("SELECT COUNT(*) as `total` FROM `#prefix_ticket_numbers` WHERE `ticket_number` = '%s' AND `product_id` = '%s'", [$ticketNumber, $productID]);
        return $ticketData[0]['total'];
    }

    public function generateTicketNumberByProduct($productID)
    {
        $ticketData = $this->queryWp("SELECT MAX(ticket_number) as 'latest' FROM `#prefix_ticket_numbers` WHERE `product_id` = '%s'", [$productID]);
        $total = ($ticketData[0]['latest'] == NULL) ? 1 : $ticketData[0]['latest'];
        if( $total > 0 ) {
            $total += 1;
        }
        $ticketNumber = $total;
        $count = $this->isTicketNumberExist($ticketNumber, $productID);
        if( $count > 0 ) {
            $this->generateTicketNumberByProduct($productID);
        } else {
            return $ticketNumber;
        }
    }

    public function getTicketNumbersOnEachProduct($product_id, $order_id, $item_id) {
        $ticketData = $this->queryWp("SELECT * FROM `#prefix_ticket_numbers` WHERE `product_id` = '%s' AND `order_id` = '%s' AND `item_id` = '%s'", [$product_id, $order_id, $item_id]);
        return $ticketData;
    }

    public function getTotalBoughtPerUser($product_id, $user, $guest = false) {
        $condition = '';
        if( $guest ) {
            $condition = 'userid';
        } else {
            $condition = 'email';
        }

        $query = "SELECT COUNT(*) as `total` FROM `#prefix_ticket_numbers` WHERE `product_id` = '%s' AND `$condition` = '%s'";
        $ticketData = $this->queryWp($query, [$product_id, $user]);
        return $ticketData[0]['total'];
    }

    public function getProductEntryList($product_id) {
        $ticketData = $this->queryWp("SELECT * FROM `#prefix_ticket_numbers` WHERE `product_id` = '%s'", [$product_id]);
        return $ticketData;
    }
}
