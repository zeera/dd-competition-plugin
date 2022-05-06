<?php

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Helpers;

use WpDigitalDriveCompetitions\Core\Conversion;
use WpDigitalDriveCompetitions\Core\Model;

/**
 * Table Helper
 */
class TableHelper extends Model
{


    /**
     * Validation logic
     * Expects an associative array with
     * [key] = html "name" of field from $_POST
     * value[0] = database column name
     * value[1] = type of validation to use
     * value[2] = is a required field
     * value[3] = max field length
     * value[4] = extra validation information (some validaiton types such as REGEX require additional info otherwise pass everything)
     */

    /** Validation array */
    public function getValidationArray()
    {
        //     return [
        //         'disabled' => ['disabled', 'INT', false, 1],
        //     ];
        return [];
    }

    /**
     * Overload friendly get search vars, used in search tables or general model data searches
     */
    public function getSearchVars()
    {
        //     return
        // [
        //     'id' => ['ID', 'match', $this->tableName],
        //     'date_created' => ['Created Date', 'date', $this->tableName],
        //     'date_updated' => ['Last Updated', 'date', $this->tableName],
        //     'created_by' => ['Created By', 'like', $this->tableName],
        //     'updated_by' => ['Created By', 'like', $this->tableName],
        //     'disabled' => ['Disabled (0|1)', 'match', $this->tableName],
        // ];
        return [];
    }

    /**
     * Override friendly get query
     */
    public function getQuery()
    {
        return "SELECT * FROM `{$this->tableName}`";
    }


    /**
     * Override friendly get count query
     */
    public function getCountQuery()
    {
        return "SELECT COUNT(*) AS `cnt` FROM `{$this->tableName}`";
    }


    /**
     * Load by ID
     *
     * Can really be any of these, the important thing is to addDataResult('data') and select('data',0)
     * The first adds the data the main search results
     *
     * The second selects the data to be usable/autofillable into any related form field loaded.
     */
    public function loadId(mixed $id): ?array
    {
        $query = $this->getQuery() . " WHERE `{$this->tableName}`.`id` = %s";
        $values = [$id];

        $results = $this->queryWp($query, $values);

        if (count($results) === 0) {
            return null;
        }

        $this->addDataResult('data', $results);
        $this->select('data', 0);

        return $this->selected;
    }

    /**
     * Get search options
     */
    public function getSearchOptions()
    {
        $options = [];

        foreach ($this->getSearchVars() as $key => $var) {
            $options[$key] = $var[0];
        }

        return $options;
    }

    /**
     * Search functionality
     */
    public function search($vars = [], string $data_name = 'data_search')
    {
        $query = $this->getQuery();
        $count_query = $this->getCountQuery();

        $conditions = '';
        $values = [];
        $orderby = $this->default_order_by;
        $orderdir = $this->default_order_dir;
        $recordsperpage = $vars['return_all_results'] ?? $this->recordsperpage;

        $searchvars = $this->getSearchVars();

        $this->searchQueryBuilder($searchvars, $vars, $conditions, $values);

        $query = $query . $conditions;
        $count_query = $count_query . $conditions;

        if (isset($vars['page_orderby']) and isset($searchvars[$vars['page_orderby']])) {
            $orderby = $vars['page_orderby'];
        }
        if (isset($vars['page_orderdirection']) and isset($this->order_dirs[$vars['page_orderdirection']])) {
            $orderdir = $this->order_dirs[$vars['page_orderdirection']];
        }

        $page = isset($vars['page_number']) ? (int) $vars['page_number'] : 1;

        $results = $this->queryWpSort(
            $query,
            $values,
            [$page, $recordsperpage],
            [$orderby, $orderdir]
        );

        $cnt_results = $this->queryWp($count_query, $values);

        $result_numbers = isset($cnt_results[0]) ? $cnt_results[0]['cnt'] : 0;

        $data = [
            'success' => true,
            'results' => $results,
            'result_count' => [
                'results' => $result_numbers,
                'pages' => ceil($this->$result_numbers / $recordsperpage),
            ],
        ];

        $this->addDataResult($data_name, $results);
        return $data;
    }


    /**
     * Build a search query
     */
    public function searchQueryBuilder($searchvars, $vars, &$conditions, &$values, $default_disabled = true)
    {
        foreach ($searchvars as $key => $vals) {
            if (isset($vars[$key])) {
                if (count($values) == 0) {
                    $conditions = $conditions . ' WHERE ';
                } else {
                    $conditions = $conditions . ' AND ';
                }

                //A wrapper for passed var arrays if it isnt an array
                if (is_array($vars[$key]) && !in_array($vals[1], ['in']) && count($vars[$key]) == 1) {
                    //We turn the value from an array into the first result to make it work with other methods, this is mainly to handle filter box's
                    //We escape it back incase there is no first array item
                    $vars[$key] = $vars[$key][0] ?? $vars[$key];
                }


                //If tablename && if Key override (val3)
                $keyname =
                    (isset($vals[2]) ? $vals[2] . '.' : '') .
                    (isset($vals[3]) ? $vals[3] : $key);

                if ($vals[1] == 'match') {
                    $conditions = $conditions . $keyname . "  = %s";
                    $values[] = $vars[$key];
                } else if ($vals[1] == 'like') {
                    $conditions = $conditions . $keyname . "  LIKE %s";
                    $values[] = $vars[$key] . '%';
                } else if ($vals[1] == 'wild') {
                    $conditions = $conditions . $keyname . "  LIKE %s";
                    $values[] = '%' . $vars[$key] . '%';
                } else if ($vals[1] == 'in') {
                    if (is_array($vars[$key]) && count($vars[$key]) > 0) {
                        $conditions = $conditions . $keyname . ' IN (';
                        foreach ($vars[$key] as $val) {
                            $conditions .= "%s,";
                            $values[] = $val;
                        }
                        $conditions = rtrim($conditions, ',') . ')';
                    } else {
                        //We auto fail this condition
                        $conditions = $conditions . ' 1=%s';
                        $values[] = 0;
                    }
                } else if ($vals[1] == 'date') {
                    $datesplit = explode('|', $vars[$key]);
                    if (count($datesplit) == 2) {
                        $conditions =
                            $conditions .
                            $keyname .
                            " >= %s and " .
                            $keyname .
                            " <= %s";
                        $values[] = Conversion::changeDMY($datesplit[0]);
                        $values[] = Conversion::changeDMY($datesplit[1]);
                    } else {
                        $conditions = $conditions . $keyname . " = %s";
                        $values[] = Conversion::changeDMY($vars[$key]);
                    }
                }
            } else if ($key == 'disabled' && $default_disabled == true) {
                if (count($values) == 0) {
                    $conditions = $conditions . ' WHERE ';
                } else {
                    $conditions = $conditions . ' AND ';
                }

                $keyname = $vals[2] . '.' . $key;
                $conditions = $conditions . $keyname . "  = %s";
                $values[] = '0';
            }
        }
        // echo (print_r([$searchvars,$conditions,print_r($values,true)],true));
    }
}
