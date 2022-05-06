<?php
declare(strict_types=1);
namespace WpDigitalDriveCompetitions\Core;
/**
 * Form Core
 */
class Form
{
    /**
     * Change a YMD date format
     * @param string $date Expects a ymd date format
     * @param string $dateformat a valid php date dateformat
     */
    private static function formChangeDateFormat($date, $dateformat = 'd/m/Y')
    {
        if (strlen($date) < 4) {
            return '';
        }
        return date($dateformat, strtotime($date));
    }


    /**
     * Output a form input
     *
     * @param string $inputtype
     *            text,select,hidden,compare,password
     * @param string $name
     *            name of the form element
     * @param array $vars Array of options
     */
    public static function formInput($inputtype = 'text', $name = 'default', $vars = [])
    {
        //Label options for field
        $label = $vars['label'] ?? $name;
        //If the label text should be a hyperlink as well?
        $labelhref = isset($vars['labelhref']) ? $vars['labelhref'] : false;

        //Id created from name or if directly defined
        $id = isset($vars['id']) ? $vars['id'] : $name;

        //Setting the classes defined to the field, or adding additional classes
        $class = isset($vars['htmlclass']) ? $vars['htmlclass'] : 'form-control input-sm w-100 mw-100';
        $addclass = isset($vars['addclass']) ? $vars['addclass'] : '';
        if (strlen($addclass) > 0) {
            $class = $class . ' ' . $addclass;
        }

        //Adding inline styles to this field
        $style = isset($vars['style']) ? $vars['style'] : '';

        //Adding inline javascript to fire
        $javascript = isset($vars['java']) ? $vars['java'] : null;

        //Setting the input field value
        $value = isset($vars['value']) ? $vars['value'] : null;
        $selected = isset($vars['selected']) ? $vars['selected'] : null; // An obsolete/deprecated field to pass data should be value
        if ($selected != null && $value == null) {
            $value = $selected;
        }

        if ($value !== null && ($vars['is_date'] ?? false || $inputtype == 'date')) {
            $date_format = $vars['is_date_format'] ?? 'd/m/Y';
            $value = self::formChangeDateFormat($value, $date_format);
        }

        if ($value !== null) {
            $value = htmlentities($value); //Making it safe for " ' etc
        }

        //This is the default value if there is no $value
        $default = isset($vars['default']) ? $vars['default'] : false;

        if ($default != false && strlen($value) == 0) {
            $value = $default;
        }

        //Post override is when a submission has an error or something of this note, in which case we want the field to not lose the added data.
        //So we readd it here, this is normally automatically done by the system
        $postoverride = $vars['postoverride'] ?? false;
        if ($postoverride !== false) {
            $value = $postoverride;
        }

        //Orig_value is used to create comparisons between the two sets of data, will flag in organge if different as well as show the previous data
        $orig_value = isset($vars['orig_value']) ? $vars['orig_value'] : null;


        //The options for a specific select dropdown box
        $selectdata = $vars['options'] ?? [];

        //A colour array for a coloured drop down box aka option X is red
        //This is a array keyed by the $value and will populate a
        // background-color: {$colorarray[$value]}
        $colorarray = isset($vars['colorarray']) ? $vars['colorarray'] : [];


        //Some specific changes for select and submit input elements
        if ($inputtype === 'select') {
            $class = str_replace('form-control', 'form-select', $class);
        } elseif ($inputtype === 'submit') {
            $class = $vars['htmlclass'] ?? 'btn btn-success';
            $value = $value ?? $label;
            $label = false;
        }

        //Check values are used for checkboxs, this is the value that the ticked box submits
        //If this matches "value" then the tickbox is ticked when created
        $checkvalue = isset($vars['checkvalue']) ? $vars['checkvalue'] : '1';


        //This is the text area rows aka size of the text area field
        $rows = isset($vars['rows']) ? $vars['rows'] : 3; // textarea default rows

        //Form id is to assign a specific field to belong to a form that it might not be inside of, so you can have input fields outside of a form element that still link to it
        $formid = isset($vars['formid']) ? $vars['formid'] : false;

        //Flagging the field as disabled
        $disabled = isset($vars['disabled']) ? $vars['disabled'] : '';


        //This is the placeholder value of this field
        $placeholder = isset($vars['placeholder']) ? $vars['placeholder'] : '';

        //Filters are a bit special, they are part of creating chunks of options
        $filters = isset($vars['filters']) ? $vars['filters'] : null; // These are used to seperate chhunks of option results

        //Flags autocomplet or not
        $autocomplete_off = isset($vars['autocomplete_off']) ? $vars['autocomplete_off'] : false;
        if ($autocomplete_off == true) {
            // We add the autocomplete tag to the end of the javascript string data as it is directly entered onto the tail of the input
            $javascript = $javascript . ' autocomplete="off" ';
        }


        //Regex pattern
        //This should be a valid regex pattern
        $pattern = $vars['field_pattern'] ?? false;
        //Data is needed
        $required = $vars['required'] ?? false;
        //The html message that will popup if either condition is not met
        $invalid_message = $vars['field_error_message'] ?? false;

        $validitystring = '';
        if ($pattern){
            $validitystring .= ' pattern="' . $pattern . '"';
        }
        if ($required){
            $validitystring .= ' required';
        }
        if ($invalid_message){
            $invalid_message = \htmlentities($invalid_message, ENT_QUOTES, 'UTF-8');
            $validitystring .= ' oninvalid="this.setCustomValidity(' . "'$invalid_message'" . ')';
            $validitystring .= ' oninput="this.setCustomValidity(' . "''" . ')';
        }

        //Flagging it as error
        $is_error = $vars['is_error'] ?? false;

        if ($is_error === true && !in_array($inputtype, ['hidden'])) {
            $class = $class . ' submiterror';
        }

        //Creating the input.
        if ($disabled == true) {
            $disabled = 'disabled';
        }

        $checked = ''; // If a checkbox is checked
        if ($value == $checkvalue) {
            $checked = 'checked';
        }

        if ($orig_value != null && $value != $orig_value) {
            $class = $class . ' submitwarn';
        }
        if ($orig_value == null && $value != null && $inputtype == 'compare') {
            $class = $class . ' submitwarn';
        }

        if ($value == null && $orig_value != null && $inputtype == 'compare') {
            $class = $class . ' submitwarn';
        }

        $formidtext = '';
        if ($formid !== false) {
            $formidtext = "form='$formid'";
        }

        if ($placeholder !== '') {
            $placeholder = \htmlentities($placeholder, ENT_QUOTES, 'UTF-8');
            $placeholder = 'placeholder="' . $placeholder . '"';
        }

        $selectedcolorstring = '';
        if ($value != null && isset($colorarray[$value])) {
            $selectedcolorstring = "style='background-color: {$colorarray[$value]}'";
        }


        // If there is an option in value that is not in the select data we automatically add it just incase
        if (
            $value !== null &&
            !is_array($value) &&
            strlen(trim($value)) > 1 &&
            !isset($selectdata[$value])
        ) {
            $selectdata[$value] = $value;
        }

        // This has been moved to be prior to the label
        if ($inputtype == 'checkbox') {
            echo <<<EOTx
        <div class="custom-control custom-checkbox">
		<input type="checkbox" name="$name" id="$id" class="$class" value="$checkvalue" $checked $formidtext $javascript $validitystring $disabled >
EOTx;
        } elseif ($label != false && $inputtype != 'hidden') {
            echo <<<EOTx
            <div class="form-group">
EOTx;
        }

        if ($label != false && $inputtype != 'hidden') {
            if ($labelhref != false) {
                echo '<label for="' . $id . '"><a href="' . $labelhref . '">' . $label . '</a></label>';
            } else {
                if ($orig_value != null or $inputtype == 'compare') {
                    echo "<label for='$id'>$label<br/><span style='color:green'>$orig_value &nbsp;</span></label>";
                } else {
                    echo '<label for="' . $id . '">' . $label . '</label>';
                }
            }
        }


        if ($inputtype == 'text') {
            echo <<<EOTx
		<input type="text" name="$name" id="$id" class="$class" value="$value" $placeholder $formidtext $javascript $validitystring $disabled>
EOTx;
        } elseif ($inputtype == 'date') {
            echo <<<EOTx
        <input type="text" name="$name" id="$id" class="$class" value="$value" data-provide="datepicker" data-date-autoclose="true" placeholder="dd/mm/yyyy" class="form-control" $placeholder $formidtext $javascript $validitystring $disabled
EOTx;
            //     echo <<<EOTx
            // <input type="date" name="$name" id="$id" class="$class" value="$value" max="9999-12-31" placeholder="yyyy-mm-dd" required pattern="\d{4}-\d{2}-\d{2}" $placeholder $javascript $disabled>
            // EOTx;
        } elseif ($inputtype == 'email') {
            echo <<<EOTx
<input type="email" autocomplete="email" name="$name" id="$id" class="$class" value="$value" $placeholder $formidtext $javascript $validitystring $disabled>
EOTx;
        } elseif ($inputtype == 'time') {
            echo <<<EOTx
<input type="time" name="$name" id="$id" class="$class" value="$value" $javascript $validitystring $disabled>
EOTx;
        } elseif ($inputtype == 'hidden') {
            echo <<<EOTx
		<input type="hidden" name="$name" id="$id" class="$class" value="$value" $formidtext $validitystring $javascript >
EOTx;
        } elseif ($inputtype == 'newpassword') {
            echo <<<EOTx
		<input type="password" autocomplete="new-password" name="$name" id="$id" class="$class" value="$value" $formidtext $javascript $validitystring $disabled>
EOTx;
        } elseif ($inputtype == 'password') {
            echo <<<EOTx
		<input type="password" autocomplete="current-password" name="$name" id="$id" class="$class" value="$value" $formidtext $javascript $validitystring $disabled>
EOTx;
        } elseif ($inputtype == 'compare') {
            echo <<<EOTx
		<input type="text" name="$name" id="$id" class="$class" value="$value" $formidtext $javascript $validitystring $disabled>
EOTx;
        } elseif ($inputtype == 'money') {
            $value = number_format((float) $value, 2, '.', '');
            echo <<<EOTx
		<input type="text" name="$name" id="$id" class="$class" value="$value" $formidtext $javascript $validitystring $disabled>
EOTx;
        } elseif ($inputtype == 'area') {
            echo <<<EOTx
		<textarea name="$name" id="$id" rows="$rows" class="$class" $formidtext $javascript $validitystring $disabled style="$style">$value</textarea>
EOTx;
        } elseif ($inputtype == 'select') {
            if ($filters != null && is_array($filters)) {
                echo "<select	class='$class' name='$name' id='$id' $selectedcolorstring $formidtext $javascript $validitystring $disabled>
						";
                foreach ($selectdata as $k => $v) {
                    $colorstring = isset($colorarray[$k])
                        ? "style='background-color: {$colorarray[$k]}'"
                        : '';
                    $filter = $filters[$k];
                    if (!is_array($filter)) {
                        $filter = [$filter];
                    }

                    foreach ($filter as $fil) {
                        if ($k == $value) {
                            echo <<<EOTx
<option value="$k" selected=true class="filter_$fil" $colorstring>$v</option>
EOTx;
                        } else {
                            echo <<<EOTx
<option value='$k' class="filter_$fil" $colorstring>$v</option>
EOTx;
                        }
                    }
                }
                echo "</select>
					";
            } else {
                echo "<select	class='$class' name='$name' id='$id' $selectedcolorstring $formidtext $javascript $validitystring $disabled>
						";
                foreach ($selectdata as $k => $v) {
                    $colorstring = isset($colorarray[$k])
                        ? "style='background-color: {$colorarray[$k]}'"
                        : '';

                    if ($k == $value) {
                        echo <<<EOTx
<option value="$k" selected=true $colorstring>$v</option>
EOTx;
                    } else {
                        echo <<<EOTx
<option value='$k' $colorstring>$v</option>
EOTx;
                    }
                }
                echo "</select>
					";
            }
        } elseif ($inputtype == 'multiselect') {
            echo <<<EOTx
<select multiple='multiple' class='$class' name='{$name}[]' id='$id' $formidtext $javascript $validitystring $disabled>
EOTx;
            foreach ($selectdata as $k => $v) {
                if ($value != null && in_array($k, $value)) {
                    echo <<<EOTx
			<option value="$k" selected>$v</option>
EOTx;
                } else {
                    echo <<<EOTx
			<option value="$k">$v</option>
EOTx;
                }
            }
            echo "</select>
				";
        } elseif ($inputtype == 'submit') {
            echo <<<EOT
			<input type="submit" class="{$class}" name="{$name}" value="{$value}" />
EOT;
        }

        if (
            $inputtype == 'checkbox' ||
            ($label != false && $inputtype != 'hidden')
        ) {
            echo '</div>';
        }
    }

}
