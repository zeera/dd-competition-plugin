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
class TicketNumber extends TableHelper
{

    /** Table name */
    public string $tableName = '#prefix_ticket_numbers';

    /**
     * Create a new model
     */
    public function construct()
    {
        $this->adminHelper = New AdminHelper();
    }

    //There are default getquery and getcount query functions in the tableHelper
    //overload the functions here to customise

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
            'full_name' => ['full_name', 'STRING', false],
            'club_name' => ['club_name', 'STRING', false],
            'email' => ['email', 'STRING', false],
            'phone_number' => ['phone_number', 'STRING', false],
            'order_id' => ['order_id', 'INT', $required],
            'cash_sale' => ['cash_sale', 'INT', false],
            'ticket_number' => ['ticket_number', 'INT', $required],
            'answer' => ['answer', 'STRING', false],
            'product_id' => ['product_id', 'INT', $required],
            'item_id' => ['item_id', 'INT', $required],
            'last_updated_by' => ['last_updated_by', 'STRING', false],
            'created_by' => ['created_by', 'STRING', false],
        ];

    }

    public function getEmailCount( $email ) {
        $emailCount = $this->isEmailExists( $email );
        return $emailCount;
    }

    public function getModelData( $id )
    {
        $modelData = $this->queryData( $id );
        return $modelData[0];
    }

    /** Search vars
     *
     * These vars are used for the search functions on the model as well
     * as auto populating the default search page.
     *
     * [key] = "name" of field from the query/data
     * value[0] = The human readable name, to populate the headings
     * value[1] = The method of searching on this field: 'match','like','wild','date' and 'in'
     * value[2] = The table name in question, you will need to use different table names if the source query has a join
     * value[3] = the search key override, this is when using alias "as" etc in the search conditions
     *
     * //See searchQueryBuilder which this creates data for
     *
     */
    public function getSearchVars()
    {
        return [
            'order_id' => ['Order ID', 'like', $this->tableName],
            'email' => ['Email', 'like', $this->tableName],
        ];
    }

    /** Insert function
     *
     * $postdata should be the passed post data for the given model update/insert/etc
     *
     * This function should be overloaded in the model if your table structure doesnt work with this function
     */

    public function store($formData)
    {
        $validation = $this->getValidationArray();
        $newId = null;

        $current_user = \wp_get_current_user();

        $formData['created_by'] = $current_user->user_login;
        $formData['last_updated_by'] = $current_user->user_login;

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
            $formData,
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
            // $errors = $this->errors;
            // $err = [];
            // foreach ($errors as $key => $value) {
            //     $err['errors'][] = [
            //         'name' => $key,
            //         'value' => $value
            //     ];
            // }
            return false;
        }

        return $newId;
    }


    /** Update function
     *
     * $id is the unique id of this table
     * $postdata should be the passed post data for the given model update/insert/etc
     *
     *
     * This function should be overloaded in the model if your table structure doesnt work with this function
     */
    public function update(mixed $id, mixed $formData): bool
    {
        $validation = $this->getValidationArray(true);

        if (!isset($id)) {
            return false;
        }
        $current_user = \wp_get_current_user();


        $formData['last_updated_by'] = $current_user->user_login;


        $criteriaArray = [
            'tablename' => $this->tableName,
            'type' => 'update',
            'conditions' => [
                'id' => $id,
            ],
        ];

        $resultarray = $this->validateData(
            $criteriaArray,
            $validation,
            $formData,
            ['last_updated' => 'now()'] //Additional data is not escaped, so if you set data here make sure to escape it properly first. Avoid using it unless you need to call a stored procedure or such
        );

        //We are setting the errors to the $this->errors array to make it easy to check on these from the calling controller
        if (count($resultarray['errors']) > 0) {
            $this->errors = $resultarray['errors'];
        }

        if (count($this->errors) == 0) {
            $this->insertWp(
                $resultarray['query'],
                $resultarray['values'],
            );
        }

        // var_dump( $this->errors );

        if (count($this->errors) > 0) {
            return false;
        }

        return true;
    }

    /** Options
     *
     * Options are auto loaded, and are mostly used to populate table column definitions relating to the table
     *
     * And for dropdown options relating to the column
     */
    public function loadOptions()
    {
        //Adding some drop down options
        $this->addOption('falsetrue', [
            '0' => 'No',
            '1' => 'Yes',
        ]);

        $this->addOption('mp_status', [
            'approved' => 'Approve',
            'declined' => 'Decline',
        ]);

        //Setting search options
        $this->addOption('search_options', $this->getSearchOptions());

        //Definition of ajax search table,
        /**
         * [field_name] => ['human readable column name','Type of table column',[... extra info based on table column requirements]]
         * See Table.php for options
         */
        $this->addOption("columns", [
            'id' => ['View', 'SPRINTFID', admin_url('admin.php?page=WpDigitalDriveCompetitions_cash_sales') .'&id=%s'],
            'date_created' => ['Request Date', 'DATETIME'],
            // 'id' => ['Requester Name', 'MODALLINKARRAY', 'view', [
            //     'modelname' => 'requestModal',
            //     'class' => '',
            // ]],
            'email' => ['Email', 'STRING'],
        ]);
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

    public function getAllTickets( $productID )
    {
        if( $productID ) {
            $query = "SELECT * FROM `#prefix_ticket_numbers` WHERE `product_id` = '%s'";
        } else {
            $productID = 1;
            $query = "SELECT * FROM `#prefix_ticket_numbers` WHERE `cash_sale` = '%s'";
        }
        $ticketData = $this->queryWp($query, [$productID]);
        return $ticketData;
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
        $query = "SELECT MAX(ticket_number) as 'latest' FROM `#prefix_ticket_numbers` WHERE `product_id` = '%s'";
        $ticketData = $this->queryWp($query, [$productID]);
        $total = ($ticketData[0]['latest'] == NULL) ? 0 : $ticketData[0]['latest'];
        $total += 1;
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
