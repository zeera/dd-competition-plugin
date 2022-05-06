<?php
declare(strict_types=1);
namespace WpDigitalDriveCompetitions\Core;
/**
 * Database Core
 */
class WpDatabase
{
    /**
     *
     * @param string $string
     *            - The unescaped string in question
     * @return string - Returns the escape string, as processed by wp passthrough to real_esape_string
     */
    public static function verifyDataString($string)
    {
        global $wpdb;
        $string = $wpdb->_real_escape($string);
        return $string;
    }

    /**
     *
     * @param array $array
     *            - The unescaped array
     * @return array - Returns the escaped array
     */
    public static function verifyDataArray($array)
    {
        global $wpdb;
        for ($i = 0; $i < COUNT($array); $i++) {
            $array[$i] =  $wpdb->_real_escape($array[$i]);
        }
        return $array;
    }

    public static function isEmailExists( $email )
    {
        global $wpdb;
        $query = $wpdb->prepare(
                "
                    SELECT COUNT(*) as total
                    FROM `{$wpdb->prefix}price_match`
                    WHERE email = %s
                ",
                $email
            );

        $results = $wpdb->get_results($query, ARRAY_A);
        return $results;
    }

    public static function queryData( $id )
    {
        global $wpdb;
        $query = $wpdb->prepare(
                "
                    SELECT *
                    FROM `{$wpdb->prefix}price_match`
                    WHERE id = %s
                ",
                $id
            );

        $results = $wpdb->get_results($query, ARRAY_A);
        return $results;
    }


    /** Sort an array by a field */
    public static function sortByField($array, $fieldname, $desc = true)
    {
        foreach ($array as $key => $row) {
            $field[$key] = $row[$fieldname];
        }
        if ($desc == true) {
            array_multisort($field, SORT_DESC, $array);
            return $array;
        } else {
            array_multisort($field, SORT_ASC, $array);
            return $array;
        }
    }

    /**
     *
     * @param string $query
     *            - The sql query in question, append #prefix_ to the tablename to handle the wp prefix
     * @param array $value
     *            - Any conditional values in the query
     * @param boolean $debug
     *            - display the resulting query directy for dubugging
     * @return array - Returns an associated array of results
     */
    public static function queryWp($query, $value = [], $debug = false)
    {
        global $wpdb;

        // This replaces the string #prefix_ with the actual wordpress prefix
        $query = str_replace("#prefix_", $wpdb->prefix, $query);

        if (count($value) > 0) {
            $query = $wpdb->prepare($query, $value);
        }

        if ($debug == true) {
            print($query);
        }

        $array = $wpdb->get_results($query, ARRAY_A);

        return $array;
    }

    /**
     *
     * @param string $query
     *            - The sql query in question, append #prefix_ to the tablename to handle the wp prefix
     * @param array $value
     *            - Any conditional values in the query
     * @param array $page
     *            - which page we are on
     * @param array $sort
     *            - what field we are sorting by and in what direction
     * @param string $groupby
     *            - if passed, then groupby this value
     * @return array - Returns an associated array of results
     */
    public static function queryWpSort($query, $values, $page = [], $sort = [], $groupby = false, $debug = false)
    {
        if ($groupby !== false) {
            $query = $query . " GROUP BY $groupby ";
        }

        // Running the query, adding the sort fields and pagination.
        if (count($sort) > 0 and in_array($sort[1], array('ASC', 'DESC'))) {
            $sort[0] = preg_replace('/["\'\[\]\\;\`]/', '', $sort[0]);
            $sort[1] = preg_replace('/["\'\[\]\\;\`]/', '', $sort[1]);
            $query = $query . " ORDER BY `{$sort[0]}` {$sort[1]}";
        }

        if (count($page) > 0) {
            $currentpage = $page[0] - 1;
            $recordsperpage = $page[1];
            $offset = 0;

            if ($currentpage != 0) {
                $offset = $currentpage * $recordsperpage;
            }

            $query = $query . " LIMIT $offset, $recordsperpage;";
        }

        $data = self::queryWp($query, $values, $debug);

        return $data;
    }


    /**
     *
     * @param string $query
     *            - The sql query in question to insert, prefix table name with #prefix_ to handle the wp prefix
     * @param array $value
     *            - Any conditional values in the query
     * @param boolean $return
     *            - Requests a return insert id
     * @param boolean $debug
     *            - if true shows the related query generated
     * @return string - insert id if generated
     */
    public static function insertWp($query, $value = [],$return = false, $debug = false)
    {
        global $wpdb;

        // This replaces the string #prefix_ with the actual wordpress prefix
        $query = str_replace("#prefix_", $wpdb->prefix, $query);
        $value = wp_unslash($value);

        if (count($value) > 0) {
            $query = $wpdb->prepare($query, $value);
        }

        if ($debug == true) {
            print($query);
        }

        $lastid = false;

        $wpdb->get_results($query);
        if ($return == true) {
            $lastid = $wpdb->insert_id;
        }

        return $lastid;
    }


    /**
     *
     * @param string $query
     * @param string $fieldname
     *            - The fieldname to key the results by.
     * @param array $value
     * @return array - return an array keyed by the fieldname
     */
    public static function queryWpKeybyField($query, $fieldname, $value = [])
    {
        global $wpdb;

        // This replaces the string #prefix_ with the actual wordpress prefix
        $query = str_replace("#prefix_", $wpdb->prefix, $query);

        if (count($value) > 0) {
            $query = $wpdb->prepare($query, $value);
        }

        $results = $wpdb->get_results($query, ARRAY_A);

        $array = [];

        foreach ($results as $row) {
            $array[$row[$fieldname]] = $row;
        }

        return $array;
    }



    /**
     *
     * @param string $query
     * @param string $keyname
     *            - the key field of the return array
     * @param string $valuename
     *            - the value field of the return array
     * @param boolean $firstblank
     *            - (optional) if the first result of the return array should be blank
     * @param array $value
     *            - (optional) query variables if needed
     * @return array - an associated array keyed by keyname and with data from valuename
     */
    public static function queryWpReturnOption($query, $keyname, $valuename = '', $firstblank = false, $value = [])
    {
        global $wpdb;

        // This replaces the string #prefix_ with the actual wordpress prefix
        $query = str_replace("#prefix_", $wpdb->prefix, $query);

        if (count($value) > 0) {
            $query = $wpdb->prepare($query, $value);
        }

        $results = $wpdb->get_results($query, ARRAY_A);

        $array = [];

        if ($firstblank === true) {
            $array[''] = '';
        }

        foreach ($results as $row) {
            $array[$row[$keyname]] = $row[$valuename];
        }

        return $array;
    }

    /**
     *
     * @param array $validationarray
     * @param array $data
     * @return array $errorarray
     *         This is a simple error verification based on the criteria, it will return an error array.
     *         It uses the same methods that the validateData function does but does no other processing
     */
    public static function validateDataOnly(array $validationarray, array $data): array
    {
        return Validation::validateDataOnly($validationarray, $data);
    }


     // This function gets a passed validation array in the format
    // 'formitemid' => array('tablecolumnname','validationmethod',isnotnull(true or false))
    // It takes the tablename and type (from array) and produces the query required to insert the data. Saves time.
    /** TODO Document */
    public static function validateData(array $triggersarray, array $validationarray, array $data, array $additional = array())
    {
        $tablename = $triggersarray['tablename'];
        $type = isset($triggersarray['type']) ? $triggersarray['type'] : 'insert';
        $conditions = isset($triggersarray['conditions']) ? $triggersarray['conditions'] : array();
        $dynamicconditions = isset($triggersarray['dynamicconditions']) ? $triggersarray['dynamicconditions'] : array();
        $hasdata = isset($triggersarray['defaultdata']) ? $triggersarray['defaultdata'] : false;
        $errorarray = array();
        $query = '';
        $itemarray = array();
        $valuearray = array();
        $returnvaluearray = array();

        // $nodelete = DATABASE_REPRESS_DELETE; //WTF is this, nodelete is for dynamic conditions.
        $nodelete = false;

        // Check that we have all required fields
        /**
         * value[0] = database column name
         * value[1] = type of validation to use
         * value[2] = is a required field
         * value[3] = max field length
         * value[4] = extra validation information (some validaiton types such as REGEX require additional info otherwise pass everything)
         */
        foreach ($validationarray as $key => $value) {
            // Check that the validation array has a required field
            if (!isset($value[2])) {
                continue;
            }

            $isRequired = $value[2];

            // Not required
            if (!$isRequired) {
                continue;
            }

            // We have field
            if (isset($data[$key]) && $data[$key] !== null && strlen(strval($data[$key])) > 0) {
                continue;
            }

            // Error, missing required field
            $errorarray[$key] = "Missing required field";
        }

        // Validating passed data
        foreach ($data as $k => $v) {
            if (isset($validationarray[$k])) {
                $item = null;
                if (isset($data[$k])) {
                    if (strlen(strval($v)) > 0) {
                        $item = $v;
                        $hasdata = true;
                    }
                }
                $testitem = Validation::validateItem($validationarray[$k], $item);

                if (isset($testitem['error'])) {
                    $errorarray[$k] = $testitem['error'];
                } else {
                    $itemarray[] = $validationarray[$k][0]; // table columnname
                    $valuearray[] = $testitem['value']; // this returns an array with zero being the value and one being the validation method.
                    $returnvaluearray[] = $testitem['value'][0];
                    foreach ($dynamicconditions as $condat) {
                        if ($condat == $validationarray[$k][0])
                            // If the dynamic conditions have no data then stop any delete action
                            if ($testitem['value'][0] === null) {
                                $nodelete = true;
                            } else {
                                $conditions[$condat] = $testitem['value'][0];
                            }
                    }
                }
            }
        }

        // Insert update only uses conditions for the update section and only uses additional for the insert section?
        // Has data determines if there is any valid data for the item, if not look at deleting existing data.
        if ($hasdata === true) {

            if ($type == 'insertupdate') {
                $query = 'INSERT INTO ' . $tablename . ' (';
                foreach ($itemarray as $item) {
                    $query = $query . "`$item`,";
                }
                foreach ($additional as $k => $v) {
                    $query = $query . "`$k`,";
                }
                $query = substr($query, 0, -1) . ")";

                // Adding the insert values of query
                $query = $query . ' VALUES(';
                foreach ($valuearray as $item) {
                        $query = $query . "%s,";
                }
                foreach ($additional as $k => $v) {
                    $query = $query . "$v,";
                }
                $query = substr($query, 0, -1) . ")";

                $query = $query . " ON DUPLICATE KEY UPDATE ";
                for ($i = 0; $i < count($itemarray); $i++) {
                    $item = $itemarray[$i];
                        $query = $query . "`$item` = %s,";
                }
                foreach ($additional as $k => $v) {
                    $query = $query . $k . " = $v,";
                }
                $query = substr($query, 0, -1);

                /**
                 * ! Where Conditions are invalid for Insert/Update Queries
                 **/

                // Increasing the number of values to cover both queries
                $returnvaluearray = array_merge($returnvaluearray, $returnvaluearray);
            }

            // Creating the query //BAH insert update needed....
            if ($type == 'insert') {
                $query = 'INSERT INTO ' . $tablename . ' (';
                foreach ($itemarray as $item) {
                    $query = $query . "`$item`,";
                }
                foreach ($additional as $k => $v) {
                    $query = $query . "`$k`,";
                }
                $query = substr($query, 0, -1) . ")";

                // Adding the insert values of query
                $query = $query . ' VALUES(';
                foreach ($valuearray as $item) {
                        $query = $query . "%s,";
                }
                foreach ($additional as $k => $v) {
                    $query = $query . "$v,";
                }
                $query = substr($query, 0, -1) . ")";

                if (count($conditions) > 0) {
                    $query = $query . ' WHERE ';
                    foreach ($conditions as $k => $v) {
                        $query = $query . " `$k` = %s AND";
                        $returnvaluearray[] = $v;
                    }
                    $query = substr($query, 0, -3);
                }
                // UPDATE
            } elseif ($type == 'update') {
                $query = 'UPDATE ' . $tablename . ' SET ';
                for ($i = 0; $i < count($itemarray); $i++) {
                    $item = $itemarray[$i];
                        $query = $query . "`$item` = %s,";
                }
                foreach ($additional as $k => $v) {
                    $query = $query . $k . " = $v,";
                }
                $query = substr($query, 0, -1);

                if (count($conditions) > 0) {
                    $query = $query . ' WHERE ';
                    foreach ($conditions as $k => $v) {
                        $query = $query . " `$k` = %s AND";
                        $returnvaluearray[] = $v;
                    }
                    $query = substr($query, 0, -3);
                }
            }
        } elseif ($type == 'delete' and count($conditions) > 0 and $nodelete == false) {
            $query = "DELETE FROM $tablename WHERE ";

            foreach ($conditions as $k => $v) {
                $query = $query . " `$k` = %s AND";
                $returnvaluearray[] = $v;
            }
            $query = substr($query, 0, -3);
        } // Only create delete statement for insert update items.
        else {
            // Blanking error array as nothing is being added.

            if ($type == 'insertupdate' and count($conditions) > 0 and $nodelete == false) {

                $query = "DELETE FROM $tablename WHERE ";

                foreach ($conditions as $k => $v) {
                    $query = $query . " `$k` = %s AND";
                    $returnvaluearray[] = $v;
                }
                $query = substr($query, 0, -3);

                $errorarray = array();
            } else {
            }
        }

        return array(
            'query' => $query,
            'values' => $returnvaluearray,
            'errors' => $errorarray
        );
    }
}
