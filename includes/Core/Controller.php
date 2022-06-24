<?php

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Core;

/**
 * Controller Core
 */
class Controller extends Conversion
{
    /** ID (first url argument of action) */
    public ?string $id = null;
    /** Controller name */
    protected string $controller = '';
    /** Action to call */
    public string $action = 'index';
    /** Page title */
    public string $title = '';
    /** Breadcrumbs */
    public array $breadcrumbs = [];
    /** Page header */
    public ?string $header_file = 'header.php';
    /** Page footer */
    public ?string $footer_file = 'footer.php';
    /** Page menu */
    public ?string $menu_file = null;
    // models ?
    public $models = [];
    // lists ?
    public $lists = [];
    // default modelname
    public $default_modelname = '';
    // /** Additional JS files to add to head */
    public $extra_jsfiles = [];
    // /** Additional JS to add inline */
    public $inpage_js = '';

    // /** Additional CSS files to add to head */
    public $extra_cssfiles = [];
    // /** Additional CSS to add inline */
    public $inpage_css = '';

    public $ajax_search = [];

    //whether to find and load the view file
    public $load_view_file = false;
    //The path of the view file;
    public $view_file_path = '';

    /** Alerts */
    public array $alerts = [
        'errors' => [],
        'warnings' => [],
        'successes' => [],
        'infos' => [],
    ];
    public $auto_javascript = [];
    public string $base_url = '';
    public string $base_path = '';


    /**
     * Constructor for controllers
     */
    public function __construct(string $passaction = '', array $arguments = [])
    {
        if ($passaction != '' && strlen($passaction) > 0)
            $this->action = $passaction;

        $this->arguments = $arguments;

        // Parse first argument as id eg /user/123/sponsor -> args ['123', 'sponsor']
        if (count($arguments) > 0) {
            $this->id = $arguments[0];
        }

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        };

        // Show redirect success message if this was after a success redirect
        if (isset($_SESSION['success_redirect'])) {
            $this->alerts['successes'] = [
                'Success!' => $_SESSION['success_message'],
            ];
            unset($_SESSION['success_message']);
            unset($_SESSION['success_redirect']);
        }

        if (isset($_SESSION['error_redirect'])) {
            $this->alerts['errors'] = [
                "Error!" => $_SESSION['error_message'],
            ];
            unset($_SESSION['error_message']);
            unset($_SESSION['error_redirect']);
        }

        // $this->checkNonce();

        session_write_close();

        if ($this->base_path == '')
            $this->base_path = dirname(__DIR__, 2) . '/'; //Two directories up


        // Call controller pre action
        $this->construct();

        if ($this->action !== false) {
            $this->checkCallFunction($this->action);
        }

        //Build Page?
    }


    /**
     * Called before any of the controller's actions, for overloading purposes from the controller
     */
    public function construct()
    {
    }

    public function url(string $urlpath)
    {
        echo $this->base_url . $urlpath;
    }

    public function addAlert(string $type, string $error_code, string $error_details)
    {
        if (!isset($this->alerts[$type])) {
            // fallback
            $type = 'errors';
        }
        $this->alerts[$type][$error_code] = $error_details;
        return true;
    }

    /**
     * Get the current url
     */
    public function getRequestUrl()
    {
        //https://www.php.net/manual/en/url.constants.php
        return rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    }

    /**
     * Check the nonce
     */
    function checkNonce()
    {
        $in_session = false;
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        } else {
            $in_session = true;
        }

        if (isset($_POST) and count($_POST) > 0) {
            $nonce = md5(print_r($_POST, true));
            if (isset($_SESSION['nonce'])) {
                if ($_SESSION['nonce'] == $nonce) {
                    //We cancel the post and just redirect to the page.
                    header(
                        "Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
                        true,
                        303 //  transform post/put into get
                    );
                    exit();
                }
            }
            $_SESSION['nonce'] = $nonce;
        } else {
            unset($_SESSION['nonce']);
        }

        if (!$in_session) {
            session_write_close();
        }
    }

    /**
     * Build the page
     */
    public function buildPage($path)
    {
        if (!$this->load_view_file) {
            return;
        }

        $this->load_path = $path;
        if ($this->header_file !== null)
            require_once $this->base_path . "theme/" . $this->header_file;
        if ($this->menu_file !== null)
            require_once $this->base_path . "theme/" . $this->menu_file;
        require_once $path;
        if ($this->footer_file !== null)
            require_once $this->base_path . "theme/" . $this->footer_file;
    }

    // Checking/loading function based on action name
    public function checkCallFunction($actionname)
    {
        $functionname = str_replace('/', '_', $actionname); // For deep level actions we will use _ instead
        // Calling the controller action function if defined
        $functionname = str_replace('-', '', $functionname); //We strip out any -'s
        $functionname = 'action' . $functionname;
        if (method_exists($this, $functionname)) {
            $this->$functionname();
        }
    }


    /**
     * Get a GET value from the HTTP request
     */
    public function getValue($index, $default_value = null)
    {
        return $_GET[$index] ?? $default_value;
    }

    /**
     * Get a POST value from the HTTP request
     */
    public function postValue($index, $default_value = null)
    {
        return $_POST[$index] ?? $default_value;
    }

    /**
     * Get the site url
     */
    public function getSiteUrl()
    {
        return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    public function loadCSS($css_path)
    {
        $version = date("ymd-Gis", filemtime($this->base_path . 'assets/css/' . $css_path));
        echo $this->base_url . 'assets/css/' . $css_path . '?ver=' . $version;
    }

    public function loadJS($js_path)
    {
        $version = date("ymd-Gis", filemtime($this->base_path  . 'assets/js/' . $js_path));
        echo $this->base_url . 'assets/js/' . $js_path . '?ver=' . $version;
    }

    public function loadWpCSS($ref_name, $css_path, $deps = [])
    {
        $version = date("ymd-Gis", filemtime($this->base_path . 'assets/css/' . $css_path));
        wp_enqueue_style($ref_name, $this->base_url . 'assets/css/' . $css_path, $deps, $version);
    }

    public function loadWpJS($ref_name, $js_path, $deps = [], $phpvars = [])
    {
        $version = date("ymd-Gis", filemtime($this->base_path  . 'assets/js/' . $js_path));
        wp_enqueue_script($ref_name, $this->base_url . 'assets/js/' . $js_path, $deps, $version);

        if (count($phpvars) > 0) {
            wp_localize_script($ref_name, 'passobject', $phpvars);
        }
    }

    /**
     * Check if the request uri contains the needle string
     */
    public function urlContains($needle)
    {
        return strpos($_SERVER['REQUEST_URI'], $needle) !== false;
    }

    /**
     * Get a variable from a model
     */
    public function get(string $value, string $model_name = null)
    {
        $model_name = $model_name ?: $this->default_modelname;

        if (!isset($this->models[$model_name])) {
            return null;
        }

        return $this->models[$model_name]->get($value);
    }

    /**
     * Storing the model reference in the controller
     */
    public function loadModel(string $model_name, $model_path): mixed
    {
        if (strlen($this->default_modelname) == 0) {
            $this->default_modelname = $model_name;
        }
        if (isset($this->models[$model_name])) {
            return $this->models[$model_name];
        } else {
            $current_model = new ($model_path)();
            $this->models[$model_name] = $current_model;
            return $current_model;
        }
    }

    /**
     * Get an error from a model
     */
    public function getError(string $value, string $model_name = null)
    {
        $model_name = $model_name ?: $this->default_modelname;

        if (!isset($this->models[$model_name])) {
            return null;
        }

        return $this->models[$model_name]->getError($value);
    }

    /**
     * Get an options from a model
     * */
    public function getOption(string $value, string $model_name = null)
    {
        $model_name = $model_name ?: $this->default_modelname;
        if (!isset($this->models[$model_name])) {
            return null;
        }

        return $this->models[$model_name]->getOption($value);
    }

    /**
     * Get a data source from a model
     */
    public function getDataSource(string $data_name, string $model_name = null)
    {
        $model_name = $model_name ?: $this->default_modelname;

        if (!isset($this->models[$model_name])) {
            return null;
        }

        return $this->models[$model_name]->getDataSource($data_name);
    }

    /**
     * Get data from a model
     */
    public function getData($data_name, $field, $increment = 0, $model_name = null)
    {
        $model_name = $model_name ?: $this->default_modelname;

        if (!isset($this->models[$model_name])) {
            return null;
        }

        return $this->models[$model_name]->getData(
            $data_name,
            $field,
            $increment
        );
    }

    /**
     * Add a list, will replace prev list
     */
    public function addList(string $list_name, array $array)
    {
        $this->lists[$list_name] = [];

        foreach ($array as $data) {
            $this->lists[$list_name][] = $data;
        }
    }

    /**
     * @param string $response The information returned from the modal
     */
    public function parseUpdateAlerts($response)
    {
        $worked = false;
        if (isset($response['success']) && $response['success'] == true) {
            $this->alerts['successes'] = array_merge($this->alerts['successes'], $response['message']);
            $worked = true;
        }
        if (isset($response['success']) & $response['success'] == false) {
            $this->alerts['errors'] = array_merge($this->alerts['errors'], $response['message']);
        }
        return $worked;
    }

    /**
     * Redirect with a success message
     * @param string $relative_url
     * @param string $success_message
     *            Implies that the action was a success and redirects based on the passed url.
     */
    public function redirectSuccess(string $relative_url, string $success_message = 'Successfully created!')
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        };

        $_SESSION['success_redirect'] = $this->controller;
        $_SESSION['success_message'] = $success_message;

        session_write_close();

        header('Location: ' . $this->base_url . $relative_url);
        exit();
    }

    /**
     * Redirect with a success message
     * @param string $relative_url
     * @param string $success_message
     *            admin url aka admin.php?page=reference_page&id=%s
     *            Implies that the action was a success and redirects based on the passed url.
     */
    public function redirectWpSuccess(string $admin_url, string $success_message = 'Successfully created!')
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        };

        $_SESSION['success_redirect'] = $this->controller;
        $_SESSION['success_message'] = $success_message;

        session_write_close();
        header('Location: ' . admin_url($admin_url));
        exit();
    }

    /**
     * Redirect
     * @param string $relative_url
     *            Implies that the action was a success and redirects based on the passed url.
     */
    function redirect($relative_url)
    {
        header('Location: ' . $this->base_url . $relative_url);
        exit();
    }


    /**
     * Echo out a form field
     * @param string $name name of field
     * @param string $type type of field
     * @param array $vars vars
     * @param mixed $model_name see:
     * false = no model,
     * null = default model,
     * string = model name,
     */
    public function field(
        string $name,
        string $type,
        array $vars = [],
        mixed $model_name = null
    ) {
        if ($model_name === null) {
            $model_name = $this->default_modelname;
        }

        $vars['name'] = $name; //Storing the original name for use elsewhere or as default label

        $combined_name = $name;

        if ($model_name !== false) {
            $vars['value'] = $vars['value'] ?? $this->get($name, $model_name);

            $combined_name = $model_name . '[' . $name . ']';
            //If a name starts with a [ we expect it to be an array style one
            if (substr($name, 0, 1) == '[') {
                $combined_name = $model_name . $name;
            }

            if (count($this->alerts['errors']) > 0) {
                $vars['is_error'] = $this->getError($name, $model_name);
                $vars['postoverride'] = $_POST[$model_name][$name] ?? false;
            }
        } elseif (isset($vars['value'])) {
            $vars['value'] = $vars['value'];
        }

        Form::formInput($type, $combined_name, $vars);
    }

    public function inputSubmit($name, $vars = [], $model_name = null)
    {
        $this->field($name, 'submit', $vars, $model_name);
    }

    /**
     * Echo out a text input
     */
    public function inputText($name, $vars = [], $model_name = null)
    {
        $this->field($name, 'text', $vars, $model_name);
    }
    /**
     * Echo out a Time input
     */
    public function inputTime($name, $vars = [], $model_name = null)
    {
        $this->field($name, 'time', $vars, $model_name);
    }

    /**
     * Echo out an email input
     */
    public function inputEmail($name, $vars = [], $model_name = null)
    {
        $this->field($name, 'email', $vars, $model_name);
    }

    /**
     * Echo out a date input
     */
    public function inputDate($name, $vars = [], $model_name = null)
    {
        $this->field($name, 'date', $vars, $model_name);
    }

    /**
     * Echo out a text area input
     */
    public function inputTextArea($name, $vars = [], $model_name = null)
    {
        $this->field($name, 'area', $vars, $model_name);
    }

    /**
     * Echo out a select input
     */
    public function inputSelect($name, $vars = [], $model_name = null)
    {
        $this->field($name, 'select', $vars, $model_name);
    }

    /**
     * Echo out a multi select input
     */
    public function inputMultiSelect($name, $vars = [], $model_name = null)
    {
        $this->field($name, 'multiselect', $vars, $model_name);
    }

    /**
     * Echo out a hidden input
     */
    public function inputHidden($name, $vars = [], $model_name = null)
    {
        $this->field($name, 'hidden', $vars, $model_name);
    }

    /**
     * Echo out a password input
     */
    public function inputPassword($name, $vars = [], $model_name = null)
    {
        $this->field($name, 'password', $vars, $model_name);
    }

    /**
     * Echo out a checkbox input
     */
    public function inputCheckBox($name, $vars = [], $model_name = null)
    {
        $this->field($name, 'checkbox', $vars, $model_name);
    }

    /**
     * Echo out a search field input
     */
    public function inputSearchField($name, $vars = [])
    {
        $id = $vars['options'][0] ?? 'id';
        $textvars = $vars;
        $textvars['id'] = $id;
        $textvars['label'] = false;
        if (isset($vars['value'])) {
            $textvars['id'] = $vars['value'];
            unset($textvars['value']);
        }
        $this->field($name, 'text', $textvars, false);

        $vars['id'] = $name;
        $vars['label'] = false;
        $this->field($name . 'Select', 'select', $vars, false);
    }

    /**
     * Excel export
     */
    public function excelExport($file_name, $header, $data, $style = [])
    {
        ExcelExport::excelExport($file_name, $header, $data, $style);
    }


    /**
     * Create a table
     *
     * @param array $data
     * @param array $columns
     * @param string $ajaxid
     * @param string $javasearchid
     * @param string $javasearch
     *            Takes passed array data and formats it into a table based on columns data
     *            By default has a ajax id of ajaxsearch.
     *
     */
    public function createTable($data, $columns, $ajaxid = 'ajaxsearch', $javasearchid = false, $javasearch = false)
    {
        if ($javasearchid != false) {
            $headerlist = [];
            foreach ($columns as $key => $value) {
                $headerlist[] = $key;
            }
            $this->parent->addList($javasearchid, $headerlist);
        }

        //Inputs are considered to be in a "model" of whatever $ajaxid is
        $postoverride = $_POST[$ajaxid] ?? [];
        Table::createTable($data, $columns, $ajaxid, $javasearchid, $javasearch, $postoverride);
    }

    /**
     * Show data as a table
     *
     * @param array $data
     * @param array $columns
     * @param string $javasearchid
     *            Takes passed array data and formats it into a table based on columns data
     * @return string
     *
     */
    public function showTableData($data, $columns, $ajaxid = 'ajaxsearch', $javasearchid = false)
    {
        $postoverride = $_POST[$ajaxid] ?? [];
        return Table::showTableData($data, $columns, $javasearchid, $ajaxid, $postoverride);
    }

    public function csvExport($array, $file_name = "export.csv", $delimiter = ",")
    {
        header('Content-Type: application/csv');
        header('Content-Disposition: attachement; filename="' . $file_name . '";');

        $f = fopen('php://output', 'w');

        foreach ($array as $line) {
            fputcsv($f, $line, $delimiter);
        }
        exit();
    }

    /**
     * Add javascript to populate dropdown?
     */
    public function addJavascriptDropdownPopulate($config)
    {
        $this->autoJavascript['dropdownpopulate'] ?? [];
        $this->autoJavascript['dropdownpopulate'][] = $config;
    }

    public function generateJavaScript()
    {

        // If javascript list
        if (count($this->lists) > 0) {
?>
            <script src="<?php echo $this->base_url ?>assets/js/list.min.js"></script>
            <script src="<?php echo $this->base_url ?>assets/js/list.pagination.min.js"></script>
            <script>
                <?php

                foreach ($this->lists as $k => $v) {

                    $list_name = str_replace('-', '_', $k);
                ?>
                    var <?php echo $list_name ?>options = {
                        valueNames: <?php echo json_encode($v); ?>,
                        plugins: [ListPagination()],
                        page: 5
                    };

                    var list_<?php echo $list_name ?> = new List('<?php echo $k ?>', <?php echo $list_name ?>options);
                    <?php if (isset($this->listsort[$k])) { ?>
                        list_<?php echo $list_name ?>.sort('<?php echo $this->listsort[$k]['fieldname'] ?>', {
                            order: "<?php echo $this->listsort[$k]['order'] ?>"
                        });
                    <?php } ?>
                <?php } ?>
            </script>
        <?php
        }

        //Should be turned into a js prototype/file
        if (isset($this->auto_javascript['dropdownpopulate'])) {
        ?>
            <script type="text/javascript" charset="utf-8">
                jQuery(document).ready(function() {
                    <?php

                    foreach ($this->auto_javascript['dropdownpopulate'] as $dropdown) {
                        $firstblank = isset($dropdown['firstblank']) ? $dropdown['firstblank'] : true;
                    ?>
                        jQuery('#<?php echo $dropdown['id'] ?>').on('change', function() {
                            jQuery('#<?php echo $dropdown['select'] ?>').empty()
                            var dropDown = document.getElementById("<?php echo $dropdown['id'] ?>");
                            var query = dropDown.options[dropDown.selectedIndex].value;
                            var additional = "<?php echo isset($dropdown['additional']) ? $dropdown['additional'] : ''; ?>";
                            jQuery.ajax({
                                type: "POST",
                                url: "<?php echo $dropdown['url'] ?>",
                                data: {
                                    'query': query,
                                    'additional': additional
                                },
                                success: function(data) {
                                    // Parse the returned json data

                                    var opts = jQuery.parseJSON(data);
                                    // Use jQuery's each to iterate over the opts value
                                    <?php if ($firstblank == true) { ?>
                                        jQuery('#<?php echo $dropdown['select'] ?>').append('<option value=""></option>');
                                    <?php } ?>
                                    jQuery.each(opts, function(i, d) {
                                        jQuery('#<?php echo $dropdown['select'] ?>').append('<option value="' + i + '">' + d + '</option>');
                                    });
                                }
                            });
                        });
                    <?php } ?>
                });
            </script>
<?php
        }
    }


    public function addAjaxSearch($vars)
    {

        $this->extra_jsfiles['ajaxsearch'] = 'etechspot.ajax.js';

        $search_id = $vars['id'] ?? 'ajaxsearch';
        $search_destination = $vars['destination'] ?? $this->base_url . $this->controller . "/search";
        $search_fields = $vars['search_fields'] ?? [];
        $search_filters = $vars['search_filters'] ?? [];
        $alter_triggers = $vars['alter_triggers'] ?? [];

        $js = "
var $search_id = new etech_ajaxSearch('$search_id');
$search_id.ajaxurl = '$search_destination';";

        $js .= "$search_id.searchfieldnames = " . json_encode($search_fields) . ";";
        $js .= "$search_id.boxarray = " . json_encode($search_filters) . ";";

        foreach ($alter_triggers as $field) {
            $js .= "jQuery('#$field').on('change',function() {" . $search_id . ".alterId(this);});";
        }
        foreach ($search_fields as $field) {
            $js .= "jQuery(\"[name='$field']\").on('change',function() {" . $search_id . ".showUser();});";
            $js .= "jQuery('#$field').on('change',function() {" . $search_id . ".alterId(this);});";
        }
        foreach ($search_filters as $field) {
            $js .= "jQuery('#$field').on('change',function() {" . $search_id . ".updateAdditionalFilters();});";
        }

        $js .= "$search_id.showUser();";

        $this->inpage_js .= $js;

        $this->ajax_search[$search_id] = $vars;
    }

    public function addAjaxExport($id)
    {
        $destination = $this->ajax_search[$id]['destination'] ?? false;
        $returntext = '';
        if ($destination != false) {
            $export_id = $id . '_export';
            $export_items = $id . '_export_items';
            $returntext = "<form method='post' action='$destination' id='$export_id'><button type='submit' class='btn btn-secondary float-right'>Create Excel Report</button><div id='$export_items'></div>";
            $js = <<<EOTx
jQuery('#$export_id').submit(function(e) {
    jQuery('#$export_items').empty(); jQuery('#$export_items').append("<input type='hidden' name='return_csv' value='true' />");
    var keys = [];
    for ( var key in $id.requestarray) {
        if ($id.requestarray.hasOwnProperty(key))
            keys.push(key);
    }
    var passdata = {};
    for ( var i = 0; i < keys.length; i++) {
        if ($id.requestarray[keys[i]] != "") {
            var testid = keys[i].split("box_");
            if (testid.length > 1) {
                passdata[testid[testid.length - 1]] = new Array();
                for ( var x = 0; x < $id.requestarray[keys[i]].length; x++) {
                    passdata[testid[testid.length - 1]]
                            .push($id.requestarray[keys[i]][x]);
                }
            } else {
                var id = keys[i].split("auto_");
                passdata[id[id.length - 1]] = $id.requestarray[keys[i]];
            }
        }
    }
    passdata['sort_by'] = $id.sort_by; passdata['sort_order'] = $id.sort_order;
    for ( var k in passdata) {
        if (typeof passdata[k] === 'object'){
            for (var subk in passdata[k]){
                jQuery('#$export_items').append("<input type='hidden' name='" + k + "[]' value='"+
                        passdata[k][subk] + "' />");
            }
        } else {
        jQuery('#$export_items').append("<input type='hidden' name='" + k + "' value='"+
                passdata[k] + "' />");
        }
    }
});
EOTx;
            $this->inpage_js .= $js;
        }
        print($returntext);
    }
}
