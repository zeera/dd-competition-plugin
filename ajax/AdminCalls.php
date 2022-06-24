<?php

/**
 * User info AJAX calls class file.
 */

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Ajax;

use WpDigitalDriveCompetitions\Helpers\AjaxHelper;

/**
 * User info AJAX calls
 *
 * You should logically group these classes then make one per call.
 */
class AdminCalls extends AjaxHelper
{
    public function ticketNumberAjax()
    {
        header('Content-type: application/json; charset=utf-8');

        /** @var \WpDigitalDriveCompetitions\Models\TicketNumber $model */
        $model = $this->loadModel('TicketNumber', '\WpDigitalDriveCompetitions\Models\TicketNumber');
        $vars = $_POST;

        //Mapping changed criteria
        if (isset($vars['sort_by']))
            $vars['page_orderby'] = $vars['sort_by'];

        if (isset($vars['sort_order']))
            $vars['page_orderdirection'] = $vars['sort_order'];

        if (isset($vars['return_all_results']) or isset($vars['return_csv']))
            $vars['return_all_results'] =   99999999999999999;

        $search = $model->search($vars);
        if (!isset($vars['return_csv'])) {
            $returnarray = array(
                'result_count' => $search['result_count']['results'] ?? 0,
                'tabledata' => $this->showTableData($search['results'], $this->getOption('columns'))
            );

            echo json_encode($returnarray);
        } else {
            $data = $search['results'] ?? [];
            $keyarray = array();
            foreach ($data[0] ?? [] as $key => $value) {
                $keyarray[] = $key;
            }
            $this->excelExport('Logs', $keyarray, $data, ['brand_text' => 'TicketNumber']);
        }
        wp_die(); //important to call after a response
    }

    public function report()
    {
        /** @var \WpDigitalDriveCompetitions\Models\TicketNumber $model */
        $model = $this->loadModel('TicketNumber', '\WpDigitalDriveCompetitions\Models\TicketNumber');

        $data = [];
        $datacols = ['ID','post_type','post_parent','post_title','post_name','post_status'];
        $results = $model->queryWp("SELECT p.ID,p.post_type,p.post_parent, p.post_title,p.post_name,p.post_status from `#prefix_posts` p
        WHERE (p.`post_type` = 'product' or p.`post_type` = 'product_variation') AND (p.`post_status` = 'publish' OR p.`post_status` = 'draft')");
        $datacol_keys = array_flip($datacols);

        foreach ($results as $res) {
            if (!isset($data[$res['ID']])) {
                $data[$res['ID']] = [$res['ID'],$res['post_type'],$res['post_parent'],$res['post_title'],$res['post_name'],$res['post_status']];
            }
        }

        $results = $model->queryWp("SELECT p.ID, pm.meta_key,pm.meta_value from `#prefix_posts` p
        LEFT JOIN `#prefix_postmeta` pm ON p.ID = pm.post_id
        WHERE (p.`post_type` = 'product' or p.`post_type` = 'product_variation') AND (p.`post_status` = 'publish' OR p.`post_status` = 'draft')
        AND pm.meta_key IN('_price','_regular_price','_sale_price','_sku','_stock','_stock_status','_weight','_length','_width','_height','_visibility','_featured','_sold_individually','_tax_status','_tax_class','_sale_price_dates_from','_sale_price_dates_to')

        ");
        $datacol_keys = array_flip($datacols);
        foreach ($results as $res) {
            if (!isset($datacol_keys['meta_' . $res['meta_key']])) {
                $datacols[] = 'meta_' . $res['meta_key'];
                $datacol_keys = array_flip($datacols);
            }
            for ($i=count($data[$res['ID']]);$i < ($datacol_keys['meta_' . $res['meta_key']]);$i++){
                $data[$res['ID']][] = '';
            }
            $data[$res['ID']][$datacol_keys['meta_' . $res['meta_key']]] = $res['meta_value'];
        }


        $results = $model->queryWp("SELECT p.`ID`, p.`post_title` AS 'Product Name', t.`term_id` AS 'Attribute Value ID', REPLACE(REPLACE(tt.`taxonomy`, 'pa_', ''), '-', ' ') AS 'Attribute Name', t.`name` AS 'Attribute Value' FROM `wp_posts` AS p INNER JOIN `wp_term_relationships` AS tr ON p.`ID` = tr.`object_id` INNER JOIN `wp_term_taxonomy` AS tt ON tr.`term_taxonomy_id` = tt.`term_id` AND tt.`taxonomy` LIKE 'pa_%' INNER JOIN `wp_terms` AS t ON tr.`term_taxonomy_id` = t.`term_id`
        WHERE (p.`post_type` = 'product' or p.`post_type` = 'product_variation') AND (p.`post_status` = 'publish' OR p.`post_status` = 'draft')");


        $datacol_keys = array_flip($datacols);
        foreach ($results as $res) {
            if (!isset($datacol_keys['attribute_' . $res['Attribute Name']])) {
                $datacols[] = 'attribute_' . $res['Attribute Name'];
                $datacol_keys = array_flip($datacols);
            }

            for ($i=count($data[$res['ID']]);$i < ($datacol_keys['attribute_' . $res['Attribute Name']]);$i++){
                $data[$res['ID']][] = '';
            }
            $data[$res['ID']][$datacol_keys['attribute_' . $res['Attribute Name']]] = $res['Attribute Value'];
        }

        array_unshift($data,$datacols);

        //export $data to csv
        $this->csvExport($data, 'wooattributes.csv');

        wp_die();
    }
}
