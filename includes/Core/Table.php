<?php
// declare(strict_types=1); //No strict
namespace WpDigitalDriveCompetitions\Core;

use WpDigitalDriveCompetitions\Core\Conversion;
use WpDigitalDriveCompetitions\Helpers\AdminHelper;

/**
 * Table
 */
class Table
{
    public $style = [];

    /**
     *
     * @param array $data
     *            - The data to populate the table
     * @param array $columns
     *            - The reference column data for formatting and display purposes
     * @param string $ajaxid
     *            - The ajax id to use, set to false to disable ajax functions
     * @param boolean $javasearchid
     *            - The java sort id to use, false to disable
     * @param boolean $javasearch
     *            - Whether to have a java search box or not (broad scope search)
     * @param array $postoverride - The override data
     */
    public static function createTable($data, $columns = [], $ajaxid = "ajaxsearch", $javasearchid = false, $javasearch = false, $postoverride = [])
    {
        if ($javasearchid != false) {
?>
            <div id="<?= $javasearchid ?>">
                <?php if ($javasearch != false) { ?>
                    <p style="text-align: left">
                        <input class="search" placeholder="Search"></input>
                    </p>
            <?php
                }
            }
            ?>
            <div class="result-list" id="result-list">
                <div class="progress progress-striped active" id="<?= $ajaxid ?>progressbarbox" style="height: 0.25rem;">
                    <div class="progress-bar" role="progressbar" id="<?= $ajaxid ?>progressbar" style="height: 0.25rem; width: 0%"></div>
                </div>
                <div class="outertable memberdisplaybox">
                    <table class="form-table table table-striped table-bordered table-condensed mt-3">
                        <?php self::showTableHeaders($columns, $ajaxid, $javasearchid) ?>
                        <?php if ($javasearchid != false) { ?>
                            <tbody class="list" id="<?= $javasearchid ?>data">
                            <?php } else { ?>
                            <tbody class="list" id="<?= $ajaxid ?>data">
                            <?php } ?>
                            <?= self::showTableData($data, $columns, $javasearchid, $ajaxid, $postoverride) ?>
                            </tbody>
                    </table>
                </div>

            </div>

            <?php if ($javasearchid != false) { ?>
                <ul class="pagination d-flex align-items-center mb-0"></ul>
            </div>
        <?php
            }
            if ($ajaxid != false) {
        ?>
            <ul class="pagination d-flex align-items-center mb-0" id="<?= $ajaxid ?>paginate" style="padding-top: 4px; margin-top: 0px;">
            </ul>
<?php
            }
        }
        public static function showTableHeaders($columns, $ajaxid = false, $javasearchid = false)
        {
            $columns = $columns ?? [];
            if ($ajaxid != false) {
                echo "<thead id='ajaxtableheader'><tr>";
                foreach ($columns as $key => $value) {
                    echo "<th class='sort result-table-header' id='$key' onclick='$ajaxid.sortBy(\"$key\")'>$value[0]</th>";
                }
                echo "</tr></thead>";
            } else if ($javasearchid != false) {
                echo "<thead id='javatableheader'><tr>";
                foreach ($columns as $key => $value) {
                    echo "<th class='sort result-table-header' data-sort='$key' id='$key' >$value[0]</th>";
                }
                echo "</tr></thead>";
            } else {
                echo "<thead><tr>";
                foreach ($columns as $key => $value) {
                    echo "<th class='sort result-table-header' id='$key' >$value[0]</th>";
                }
                echo "</tr></thead>";
            }
        }

        // Columns are in the form of array('tablecolumnname' => array('plainname', 'type' (type eg date,string,int etc),'url','maxlength')
        public static function showTableData($data, $columns, $javasearchid = false, $ajaxid = 'ajaxsearch', $postoverride = [])
        {
            $returnstring = "";
            // $productDetails = "";
            // If the call has result data
            if (is_array($data) and count($data) > 0) {

                foreach ($data as $row) {
                    $returnstring = $returnstring . "<tr>";
                    $lastid = '';
                    if ($columns === null || count($columns) === 0) {
                        return "No results found.";
                    }

                    foreach ($columns as $key => $value) {
                        $column = '<td valign="middle">';

                        if ($javasearchid != false) {
                            if (!in_array($value[1], array('DATE', 'DATETIME', 'MMYYDATE', 'DOLLAR', 'IDNAMEKEY', 'NAMEIDKEY'))) {
                                $column = "<td class='$key'>";
                            }
                        }
                        if ($row[$key] === null) {
                            $returnstring .= $column . "<small><em class='text-muted'>NULL</em></small></td>";
                            continue;
                        }
                        $column_type_name = 'column' . ($value[1] ?? '');
                        if (method_exists(self::class, $column_type_name)) {
                            $returnstring .= self::$column_type_name($row, $column, $key, $value, $lastid, $javasearchid, $ajaxid, $postoverride);
                        } else {
                            $returnstring = $returnstring . $column . $row[$key] . "</td>";
                        }
                    }
                }
                $returnstring = $returnstring . "</tr>";
            }

            // $returnstring = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($returnstring));
            // $returnstring = array_map(utf8_encode, $returnstring);

            return $returnstring;
        }

        /**
         * SPRINTF
         *
         * Print the values into the given string
         */
        public static function columnSPRINTF($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $format = $value[2];
            $argumentsCount = preg_match_all('/%+[^%]/', $format);
            $values = array_fill(0, $argumentsCount, urlencode(trim($row[$key])));
            $cellValue = sprintf($format, ...$values);
            return $column . $cellValue . "</td>";
        }

        /*
        * SPRINTFID
        * Makes an id based url
        * name,SPRINTFID,sprintf url,(optional) alternate data key
        */
        public static function columnSPRINTFID($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';
            if ($row[$key] !== null && strlen($row[$key]) > 0) {
                $sprintfurl = $value[2];
                // If there is a alt data key use it, other wise use the normal key
                $sprintf_id = isset($value[3]) ? $value[3] : $key;
                if (isset($row[$sprintf_id])) {
                    $sprintfurl = sprintf($sprintfurl, $row[$sprintf_id]);
                }

                $returnstring = $returnstring . $column . "<a href='{$sprintfurl}'>" . trim($row[$key]) . "</a></td>";
                // Get the last key
                $lastid = $row[$key];
            } else {
                $returnstring = $returnstring . $column . "</td>";
            }
            return $returnstring;
        }

        // Gets a passed sprintf url and a substitution id or just uses the default one
        /* SPRINTFID
        * Makes an id based url
        * name,SPRINTFID,sprintf url,name
        */
        public static function columnSPRINTFIDNAME($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';
            if ($row[$key] !== null && strlen($row[$key]) > 0) {
                $sprintfurl = $value[2];
                $sprintfurl = sprintf($sprintfurl, $row[$key]);

                $returnstring = $returnstring . $column . "<a href='{$sprintfurl}'>" . trim($value[3]) . "</a></td>";
                // Get the last key
                $lastid = $row[$key];
            } else {
                $returnstring = $returnstring . $column . "</td>";
            }
            return $returnstring;
        }

        /*
        * ID
        * makes an id based pure url,
        * name,ID,url,(optional) link text
        *
        */
        public static function columnID($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';
            if ($row[$key] !== null && strlen($row[$key]) > 0) {
                if (array_key_exists(3, $value) >= 3 && isset($row[$value[3]]) && strlen($row[$value[3]]) > 0) {
                    $returnstring = $returnstring . $column . "<a href='{$value[2]}/" . urlencode(trim($row[$key])) . "'>" . $value[3] . "</a></td>";
                } else if (array_key_exists(2, $value)) {
                    $returnstring = $returnstring . $column . "<a href='{$value[2]}/" . urlencode(trim($row[$key])) . "'>" . $row[$key] . "</a></td>";
                } else {
                    $returnstring = $returnstring . $column . $row[$key] . "</td>";
                }
                $lastid = $row[$key];
            } else {
                $returnstring = $returnstring . $column . "</td>";
            }
            return $returnstring;
        }

        /*
        * Acts like a normal STRING but flags the key data as an id
        */
        public static function columnIDSTRING($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';
            if (isset($value[2])) {
                $returnstring = $returnstring . $column . $row[$value[2]] . "</td>";
                $lastid = $row[$key];
            } else {
                $returnstring = $returnstring . $column . $row[$key] . "</td>";
                $lastid = $row[$key];
            }
            $lastid = $row[$key];
            return $returnstring;
        }

        public static function columnIDNAMEKEY($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';
            if ($row[$key] !== null && strlen($row[$key]) > 0) {
                if ($javasearchid != false) {
                    $returnstring = $returnstring . $column . "<a href='{$value[2]}/" . urlencode(trim($row[$key])) . "'>" . trim($row[$value[3]]) . "</a><span class='$key' style='display:none;'>" . urlencode(trim($row[$value[3]])) . "</span></td>";
                } else {
                    $returnstring = $returnstring . $column . "<a href='{$value[2]}/" . urlencode(trim($row[$key])) . "'>" . trim($row[$value[3]]) . "</a></td>";
                }
                $lastid = $row[$key];
            } else {
                $returnstring = $returnstring . $column . $row[$key] . "</td>";
            }
            return $returnstring;
        }

        public static function columnNAMEIDKEY($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';
            if ($row[$key] !== null && strlen($row[$value[3]]) > 0) {
                if ($javasearchid != false) {
                    $returnstring = $returnstring . $column . "<a href='{$value[2]}/" . urlencode(trim($row[$value[3]])) . "'>" . trim($row[$key]) . "</a><span class='$key' style='display:none;'>$row[$key]</span></td>";
                } else {
                    $returnstring = $returnstring . $column . "<a href='{$value[2]}/" . urlencode(trim($row[$value[3]])) . "'>" . trim($row[$key]) . "</a></td>";
                }
                $lastid = $row[$value[3]];
            } else {
                $returnstring = $returnstring . $column . $row[$key] . "</td>";
            }
            return $returnstring;
        }

        /*
        * With a passed array 2, adds a css class based on the text matches vs the $key value, accepts a default
        * name,STRINGARRAYCSS,array()
        */
        public static function columnSTRINGARRAYCSS($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';
            $cssclass = '';
            if ($row[$key] !== null && strlen($row[$key]) > 0 and isset($value[2][$row[$key]])) {
                $cssclass = $value[2][$row[$key]];
            } elseif (isset($value[2][$row['default']])) {
                $cssclass = $value[2][$row['default']];
            }

            $returnstring = $returnstring . $column . "<span class='$cssclass'>" . $row[$key] . "</span></td>";
            return $returnstring;
        }


        /*
        * With a passed array 2, adds a css class based on the text matches vs the $key value, accepts a default
        * name,ACTIONBTNARR,array()
        */
        public static function columnACTIONBTNARR($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';

            $id = $lastid != ''?$lastid:($row['id'] ?? 0);
            $adminHelper = new AdminHelper;
            if( $adminHelper->isAdmin() ) {
                $returnstring = "<a class='showDetails' href='".admin_url('admin.php?page=' . WPDIGITALDRIVE_COMPETITIONS_NAMESPACE . '&id='. $id)."'>".$row[$key]."</a>";
            } else {
                $returnstring = $row[$key];
            }

            return $column . $returnstring . "</td>";
        }

        /*
        * With a passed array 2, adds a css class based on the text matches vs the $key value, accepts a default
        * name,CONCATSTRING,array()
        */
        public static function columnCONCATSTRING($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';
            $data = strlen( $row[$key]) > 30 ? substr( $row[$key], 0, 50) . "..." :  $row[$key];

            $returnstring = $data;

            return $column . $returnstring . "</td>";
        }

        /*
        * With a passed array 3, returns text based on the keyed result between this and the $key value
        * name,IDNAMEKEYARRAY,url,array()
        */

        public static function columnIDNAMEKEYARRAY($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        { // sets the name based on the passed array 3
            $returnstring = '';
            if ($row[$key] !== null && strlen($row[$key]) > 0 and isset($value[3][$row[$key]])) {
                $returnstring = $returnstring . $column . "<a href='{$value[2]}/" . urlencode(trim($row[$key])) . "'>" . trim($value[3][$row[$key]]) . "</a></td>";
                $lastid = $row[$key];
            } else {
                $returnstring = $returnstring . $column . $row[$key] . "</td>";
            }
            return $returnstring;
        }

        /*
                       * With a passed array 3, determins the BASE_URL based on this
                       * name,LINKARRAY,url,array()
                       */
        public static function columnIDLINKARRAY($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        { // sets the name based on the passed array 3
            $returnstring = '';
            if ($row[$key] !== null && strlen($row[$key]) > 0 and isset($row[$value[2]]) and isset($value[3][$row[$value[2]]])) {
                $returnstring = $returnstring . $column . "<a href='{$value[3][$row[$value[2]]]}/" . urlencode(trim($row[$key])) . "'>" . $row[$key] . "</a></td>";
                $lastid = $row[$key];
            } else {
                $returnstring = $returnstring . $column . "</td>";
            }
            return $returnstring;
        }

        /*
                       * Has a second passed id, id2 which grabs the data of column named by variable 3
                       *
                       * name,ID2,url,secondary id column
                       */
        public static function columnID2($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        { // sets the name based on the passed array 3
            $returnstring = '';
            if ($row[$key] !== null && strlen($row[$key]) > 0) {
                $returnstring = $returnstring . $column . "<a href='{$value[2]}/" . urlencode(trim($row[$key])) . "&id2=" . urlencode(trim($row[$value[3]])) . "'>" . trim($row[$value[3]]) . "</a></td>";
                $lastid = $row[$key];
            } else {
                $returnstring = $returnstring . $column . "</td>";
            }
            return $returnstring;
        }

        /*
                       * A generic url reference which doesnt store the last id,
                       * name,ID,url,(optional) link text
                       */
        public static function columnURLID($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        { // sets the name based on the passed array 3
            $returnstring = '';
            if ($row[$key] !== null && strlen($row[$key]) > 0) {
                if (isset($row[$value[3]]) and strlen($row[$value[3]]) > 0) {
                    $returnstring = $returnstring . $column . "<a href='{$value[2]}/" . urlencode(trim($row[$key])) . "'>" . $value[3] . "</a></td>";
                } else {
                    $returnstring = $returnstring . $column . "<a href='{$value[2]}/" . urlencode(trim($row[$key])) . "'>" . $row[$key] . "</a></td>";
                }
            } else {
                $returnstring = $returnstring . $column . "</td>";
            }
            $returnstring = $returnstring . $column . "<a href='{$value[2]}/" . urlencode(trim($row[$key])) . "'>" . $row[$key] . "</a></td>";
            return $returnstring;
        }

        public static function columnURLIDPOPUP($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        { // For external links
            $returnstring = '';
            $returnstring = $returnstring . $column . "<a href='{$value[2]}/" . urlencode(trim($row[$key])) . "' target='_blank'>" . $row[$key] . "</a></td>";

            return $returnstring;
        }


        public static function columnNAMEURLKEY($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        { // For external links
            $returnstring = '';
            if ($row[$key] !== null && strlen($row[$value[3]]) > 0) {
                $returnstring = $returnstring . $column . "<a href='{$value[2]}/" . urlencode(trim($row[$value[3]])) . "'>" . trim($row[$key]) . "</a></td>";
            } else {
                $returnstring = $returnstring . $column . $row[$key] . "</td>";
            }
            return $returnstring;
        }

        public static function columnDOLLAR($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';
            if ($javasearchid != false) {
                $returnstring = $returnstring . $column . "\$" . number_format($row[$key], 2) . "<span class='$key' style='display:none;'>$row[$key]</span></td>";
            } else {
                $returnstring = $returnstring . $column . "\$" . number_format($row[$key], 2) . '</td>';
            }
            return $returnstring;
        }
        //If the dollar value is negative make it red
        public static function columnREDDOLLAR($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';
            if ($row[$key] > 0.1) {
                $returnstring = $returnstring . $column . "\$" . number_format($row[$key], 2) . "</td>";
            } else {
                $returnstring = $returnstring . "<td style='color:red' class='$key'>\$" . number_format($row[$key], 2) . "</td>";
            }
            return $returnstring;
        }

        public static function columnMMYYDATE($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';
            if ($javasearchid != false) {
                $returnstring = $returnstring . $column . Conversion::changeYMD($row[$key], 'm/y') . "<span class='$key' style='display:none;'>$row[$key]</span></td>";
            } else {
                $returnstring = $returnstring . $column . Conversion::changeYMD($row[$key], 'm/y') . "</td>";
            }
            return $returnstring;
        }


        public static function columnDATE($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';
            if ($javasearchid != false) {
                $returnstring = $returnstring . $column . Conversion::changeYMD($row[$key], 'd/m/Y') . "<span class='$key' style='display:none;'>$row[$key]</span></td>";
            } else {
                $returnstring = $returnstring . $column . Conversion::changeYMD($row[$key], 'd/m/Y') . "</td>";
            }
            return $returnstring;
        }

        public static function columnDATETIME($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';
            if ($javasearchid != false) {
                $returnstring = $returnstring . $column . Conversion::changeYMD($row[$key], 'd/m/Y', true) . "<span class='$key' style='display:none;'>$row[$key]</span></td>";
            } else {
                $returnstring = $returnstring . $column . Conversion::changeYMD($row[$key], 'd/m/Y', true) . "</td>";
            }
            return $returnstring;
        }


        public static function columnMODALLINK($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';
            $returnstring = $returnstring . $column . "<a href='#{$value[5]}'
                        data-id='{$row[$key]}'";
            if (isset($row[$value[2]]))
                $returnstring .= "data-name='{$row[$value[2]]}'";
            if (isset($row[$value[3]]))
                $returnstring .= "data-folder='{$row[$value[3]]}'";

            $returnstring .= "type='text' data-toggle='modal' class='pal-modallink open-{$value[5]} {$value[6]}'> $value[4] </a>";
            return $returnstring;
        }

        public static function columnMODALLINKARRAY($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';
            if (isset($value[3]) and is_array($value[3])) {
                $dataid = $row[$key];
                $modelname = $value[3]['modelname'] ?? 'model';
                $extra_class = $value[3]['class'] ?? '';
                $data_attributes = $value[3]['data_attributes'] ?? [];
                $data_extra = '';
                foreach ($data_attributes as $key => $attribute) {
                    $data_extra .= 'data-' . $key . "='" . rawurlencode(($row[$attribute] ?? '')) . "' ";
                }
                $text = $value[2];

                $returnstring = $returnstring . $column . "<a href='#{$modelname}' data-id='{$dataid}' type='text' data-toggle='modal' {$data_extra} class='pal-modallink open-{$modelname} {$extra_class}'> {$text} </a>";
            } else {
                $returnstring = $returnstring . $column . "</td>";
            }
            return $returnstring;
        }


        public static function columnINPUTCHECK($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';
            $inputname = $ajaxid . "[$key][$lastid]";
            $inputvalue = isset($row[$key]) ? $row[$key] : '';
            if (isset($value[2]))
                $inputvalue = $value[2];
            if (isset($postoverride[$inputname])) {
                $inputvalue = $postoverride[$inputname];
            }
            $checked = '';
            if ($inputvalue == 1) {
                $checked = 'checked';
            }
            $returnstring = $returnstring . $column . "<input type='checkbox' class='$key' name='$inputname' value='1' $checked /></td>";
            return $returnstring;
        }
        public static function columnSIMPLECHECKKEY($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';
            $inputname = $ajaxid . "[{$value[2]}][" . $row[$key] . "]";
            $inputvalue = $row[$key];
            $checked = $value[3] ?? false;

            if ($checked !== false) {
                $checked = 'checked';
            } else {
                $checked = '';
            }

            if (isset($postoverride[$inputname])) {
                $inputvalue = $postoverride[$inputname];
            }
            if ($inputvalue != null) {
                $returnstring = $returnstring . $column . "<input type='checkbox' class='$value[2]' name='$inputname' value='$inputvalue' $checked /></td>";
            } else {
                $returnstring = $returnstring . '<td></td>';
            }
            return $returnstring;
        }

        public static function columnCHECKFLAG($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';
            $inputname = $ajaxid . "[{$value[2]}][" . $row[$key] . "]";
            $inputvalue = $row[$value[3]];

            if (isset($postoverride[$inputname])) {
                $inputvalue = $postoverride[$inputname];
            }
            if ($inputvalue == 1)
                $returnstring = $returnstring . $column . "<input type='checkbox' class='$value[2]' name='$inputname' value=1 checked /></td>";
            else
                $returnstring = $returnstring . $column . "<input type='checkbox' class='$value[2]' name='$inputname' value=1 /></td>";
            return $returnstring;
        }

        public static function columnINPUT($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';
            $inputname = $ajaxid . "[$lastid][$key]";
            $inputvalue = $row[$key];
            if (isset($postoverride[$inputname])) {
                $inputvalue = $postoverride[$inputname];
            }
            $returnstring = $returnstring . $column . "<input type='text' class='$key' name='$inputname' value='$inputvalue' /></td>";
            return $returnstring;
        }
        //Input Section
        public static function columnINPUTDATE($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';
            $inputname = $ajaxid . "[$lastid][$key]";
            $inputvalue = conversion::changeYMD($row[$key]);
            if (isset($postoverride[$inputname])) {
                $inputvalue = $postoverride[$inputname];
            }
            $returnstring = $returnstring . $column . "<input type='text' class='$key' name='$inputname' value='$inputvalue' /></td>";
            return $returnstring;
        }
        public static function columnINPUTAREA($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';
            $inputname = $ajaxid . "[$lastid][$key]";
            $inputvalue = $row[$key];
            if (isset($postoverride[$inputname])) {
                $inputvalue = $postoverride[$inputname];
            }
            $returnstring = $returnstring . $column . "<textarea name='$inputname' rows='2' class='$key'>$inputvalue</textarea>";
            return $returnstring;
        }
        public static function columnINPUTSELECT($row, $column, $key, $value, &$lastid, $javasearchid, $ajaxid, $postoverride)
        {
            $returnstring = '';
            $inputname = $ajaxid . "[$lastid][$key]";
            $inputvalue = $row[$key];
            if (isset($postoverride[$inputname])) {
                $inputvalue = $postoverride[$inputname];
            }
            $returnstring = $returnstring . $column . "<select class='$key' name='$inputname' id='$inputname'>";
            foreach ($value[2] as $opt_key => $opt_value) {
                if ($inputvalue == $opt_key) {
                    $returnstring = $returnstring . "<option value='$opt_key' selected='true'>$opt_value</option>";
                } else {
                    $returnstring = $returnstring . "<option value='$opt_key'>$opt_value</option>";
                }
            }
            $returnstring = $returnstring . "</select></td>";
            return $returnstring;
        }


        public static function columnIDBUTTON($row, $column, $key, $value, &$lastid, $postoverride = [])
        {
            $returnstring = '';
            $returnstring = $returnstring . $column . "<a href='{$value[2]}?id=" . urlencode(trim($row[$key]));
            // We want to add all the additional items from here
            foreach ($value[4] as $sub_id => $sub_value) {
                $returnstring .= "&{$sub_id}=" . urlencode($row[$sub_value]);
            }
            $returnstring .= "'>" . $value[3] . "</a></td>";
            return $returnstring;
        }
    }
