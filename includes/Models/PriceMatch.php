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
class PriceMatch extends TableHelper
{

    /** Table name */
    public string $tableName = '#prefix_price_match';

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
            'matchPriceName' => ['name', 'STRING', $required,200],
            'match_price_email_meta_box' => ['email', 'STRING', $required,200],
            'match_price_lifestyle_product_id' => ['product_id', 'INT', $required],
            'variation_id' => ['variation_id', 'INT'],
            'match_price_lifestyle_product_url_meta_box' => ['product_url', 'STRING', $required],
            'match_price_product_price' => ['competitor_price', 'INT', $required],
            'match_price_product_url_meta_box' => ['competitor_price_url', 'STRING', $required],
            'matchPriceMessage' => ['message', 'STRING', $required],
            'status' => ['status', 'STRING', false],
            'discount' => ['discount', 'STRING', false],
            'email_sent' => ['email_sent', 'INT', false],
            'coupon' => ['coupon', 'STRING', false],
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
            'id' => ['ID', 'match', $this->tableName],
            'name' => ['Name', 'like', $this->tableName],
            'email' => ['Email', 'like', $this->tableName],
            'date_created' => ['Created', 'date', $this->tableName],
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
            'id' => ['View','SPRINTFID',admin_url('admin.php?page=' . WPDIGITALDRIVE_COMPETITIONS_NAMESPACE) .'&id=%s'],
            'date_created' => ['Request Date', 'DATETIME'],
            // 'id' => ['Requester Name', 'MODALLINKARRAY', 'view', [
            //     'modelname' => 'requestModal',
            //     'class' => '',
            // ]],
            'name' => ['Name','STRING'],
            'email' => ['Email', 'STRING'],
            'competitor_price' => ['Competitor Price', 'DOLLAR'],
            'competitor_price_url' => ['Competitor Price URL', 'CONCATSTRING'],
            'status' => ['Status', 'STRINGARRAYCSS',['approved' => 'badge rounded-pill bg-success','completed' => 'badge rounded-pill bg-success','declined' => 'badge rounded-pill bg-danger','pending' => 'badge rounded-pill bg-warning',]]
        ]);
    }

    /**
     * get Product Data
     */
    public function getProductData( $productID )
    {
        $productInfo = $this->queryWp("SELECT id,post_title,met.meta_value as price from `#prefix_posts` pst
        LEFT JOIN `#prefix_postmeta` met on pst.ID = met.post_id and met.meta_key = '_price'
        WHERE pst.ID = '%s' and pst.post_type = 'product' and post_status = 'publish' and met.meta_value IS NOT NULL and met.meta_value > 0", [$productID]);

        return $productInfo;
    }


    public function getProductVariationById($product_id, $attributes)
    {
        $variations = array();
        $args = array(
            'post_type'     => 'product_variation',
            'post_status'   => array( 'private', 'publish' ),
            'numberposts'   => -1,
            'orderby'       => 'menu_order',
            'order'         => 'asc',
            'post_parent'   => $product_id
        );
        $variations = get_posts( $args );
        $extractedData = [];
        foreach ($variations as $variation) {
            $variation_id = absint( $variation->ID );
            $variation_data = get_post_meta( $variation_id );
            $variation_data['variation_post_id'] = $variation_id;
            $match_count = 0;
            $variationPluckedData = array_filter($variation_data, fn($value, $key) => strpos($key, 'attribute_pa') !== false || strpos($key, 'variation_post_id') !== false, ARRAY_FILTER_USE_BOTH);
            array_push( $extractedData,  $variationPluckedData);
        }

        $matched = $this->processVariations($extractedData, $attributes);
        return $matched;
    }

    public function processVariations($variations, $attributes)
    {
        $dataFromFormKeys = array_keys($attributes);

        $filtered_var = [];
        $match = [];

        if(count((array) $variations) > 0) {
            foreach($variations as $key => $var) {
                if(count((array) $var)) {
                    foreach($var as $key => $v){
                        if(in_array($key, $dataFromFormKeys)) {
                            $filtered_var[$var['variation_post_id']][$key] = $v[0];
                        }
                    }
                }
            }
        }

        foreach($filtered_var as $key => $var) {
            if($var === $attributes) {
                $var['id'] = $key;
                $match[] = $var;
            } else {
                $var['id'] = $key;
                if(array_intersect($var, $attributes)){
                    if(array_search('', $var)){
                        $match[] = $var;
                    }
                }
            }
        }

        return reset($match);
    }
}
