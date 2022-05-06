<?php
declare(strict_types=1);
namespace WpDigitalDriveCompetitions\core;

/**
 * String and misc Validation
 */
class Validation
{
    /**
     * Validate Dmy date ?
     */
    public static function validateDmyDate($string)
    {
        $string = str_replace('/', '-', $string);
        $date_explode = explode('-', $string);
        if (isset($date_explode[2])) {
            $datevalid = checkdate(
                (int) $date_explode[1],
                (int) $date_explode[0],
                (int) $date_explode[2]
            );
            return $datevalid;
        } else {
            return false;
        }
    }

    /**
     * Validate DATE ?
     */
    public static function validateDATE($string)
    {
        $string = str_replace('/', '-', $string);
        $date_explode = explode('-', $string);
        if (isset($date_explode[2])) {
            $datevalid = checkdate(
                (int) $date_explode[1],
                (int) $date_explode[2],
                (int) $date_explode[0]
            );
            return $datevalid;
        } else {
            return false;
        }
    }

    /**
     * Validate an email address
     *
     * Basic check of dns and syntax for the email address, more advanced validation is done via ajax but potentially
     * Incorrect thus only basic check here
     */
    public static function validateEmail($mail)
    {
        // Match Email Address Pattern
        if (
            preg_match(
                '/^([\w\.\%\+\-]+)@([a-z0-9\.\-]+\.[a-z]{2,20})$/i',
                trim($mail),
                $m
            )
        ) {
            // More strict is to only check MX, we do both in example
            if (
                checkdnsrr($m[2], 'MX') == true ||
                checkdnsrr($m[2], 'A') == true
            ) {
                // host found!
                return true;
            }
        }
        return false;
    }
    public static function validatePhone($phonenumber)
    {
        $phonenumber = str_replace(' ', '', $phonenumber);
        $phonenumber = str_replace('(', '', $phonenumber);
        $phonenumber = str_replace(')', '', $phonenumber);
        $phonenumber = str_replace('-', '', $phonenumber);
        if (
            preg_match(
                '/((^[+]?[0-9][0-9]{7}$)|(^[+]?61[1-9][0-9]{8}$)|(1800|1300)[0-9]{4,6}|(^[+]?(04|05)[0-9][0-9]{7}$)|(^[+]?(03|02|07|08)[0-9][0-9]{7}$)|(^[+](?=[^61])[1-9]{2}[0-9]{6}$)|(^1300[0-9]{6}$)|(^[+](999|998|997|996|995|994|993|992|991|990|979|978|977|976|975|974|973|972|971|970|969|968|967|966|965|964|963|962|961|960|899|898|897|896|895|894|893|892|891|890|889|888|887|886|885|884|883|882|881|880|879|878|877| 876|875|874|873|872|871|870|859|858|857|856|855|854|853|852|851|850|839|838|837|836|835| 834|833|832|831|830|809|808|807|806|805|804| 803|802|801|800|699|698|697|696|695|694|693|692|691|690|689|688|687|686|685|684|683|682|681|680|679|678|677|676|675|674|673|672|671|670|599|598|597|596|595|594|593|592|591|590|509|508|507|506|505|504|503|502|501|500|429|428|427|426|425|424|423|422|421|420|389|388|387|386|385|384|383|382|381|380|379|378|377|376|375|374|373|372|371|370|359|358|357|356|355|354|353|352|351|350|299|298|297|296|295|294|293|292|291|290|289|288|287|286|285|284|283|282|281|280|269|268|267|266|265|264|263|262|261|260|259|258|257|256|255|254|253|252|251|250|249|248|247|246|245|244|243|242|241|240|239|238|237|236|235|234|233|232|231|230| 229|228|227|226|225|224|223|222|221|220|219|218|217|216|215|214|213|212|211|210|98|95|94|93|92|91|90|86|84|82|81|66|65|64|63|62|61|60|58|57|56|55|54|53|52|51|49|48|47|46|45|44|43|41|40|39|36|34|33|32|31|30|27|20|7|1)[0-9]{0,14}$))/',
                $phonenumber
            )
        ) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * This is a method to be overwritten from the helper class
     * It adds additional validators or modifies existing
     */
    public static function validateExtra($method, $item)
    {
        $returnarray = [];
        switch ($method) {
            case 'PFTEST':
                if (is_numeric($item) == false) {
                    $returnarray['error'] = 'Not A Number.';
                } else {
                    if (preg_match('/[abcdef]/', $item)) {
                        $returnarray['error'] = 'Not A Number.';
                    } else {
                        $returnarray['value'] = [$item, 'INT'];
                    }
                }
                break;
        }

        return $returnarray;
    }

    /**
     * Luhn algorithm number checker - (c) 2005-2008 shaman - www.planzero.org *
     * This code has been released into the public domain, however please *
     * give credit to the original author where possible.
     */
    protected static function luhnCheck($number)
    {
        // Strip any non-digits (useful for credit card numbers with spaces and hyphens)
        $number = preg_replace('/\D/', '', $number);

        // Set the string length and parity
        $number_length = strlen($number);
        $parity = $number_length % 2;

        // Loop through each digit and do the maths
        $total = 0;
        for ($i = 0; $i < $number_length; $i++) {
            $digit = $number[$i];
            // Multiply alternate digits by two
            if ($i % 2 == $parity) {
                $digit *= 2;
                // If the sum is two digits, add them together (in effect)
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            // Total up the digits
            $total += $digit;
        }

        // If the total mod 10 equals 0, the number is valid
        return $total % 10 == 0 ? true : false;
    }

    // Taken from http://stackoverflow.com/questions/174730/what-is-the-best-way-to-validate-a-credit-card-in-php/603361#603361
    public static function validateCCard($cc)
    {
        $cc = preg_replace('/\D/', '', $cc);
        $cards = [
            'visa' => '(4\d{12}(?:\d{3})?)',
            'amex' => '(3[47]\d{13})', // (3[47]\d{13})
            'jcb' => '(35[2-8][89]\d\d\d{10})',
            'maestro' => '((?:5020|5038|6304|6579|6761)\d{12}(?:\d\d)?)',
            'solo' => '((?:6334|6767)\d{12}(?:\d\d)?\d?)',
            'mastercard' => '(5[1-5]\d{14})',
            'dinerscard' => '(([30|36|38]{2})([0-9]{12}))',
            'switch' =>
            '(?:(?:(?:4903|4905|4911|4936|6333|6759)\d{12})|(?:(?:564182|633110)\d{10})(\d\d)?\d?)',
        ];
        $names = [
            'Visa',
            'American Express',
            'JCB',
            'Maestro',
            'Solo',
            'Mastercard',
            'Diners',
            'Switch',
        ];
        $matches = [];
        $pattern = '#^(?:' . implode('|', $cards) . ')$#';
        $result = preg_match($pattern, str_replace(' ', '', $cc), $matches);
        return $result > 0 ? self::luhnCheck($cc) : false;
    }

    public static function validateRegex($pattern, $text)
    {
        $result = preg_match($pattern, $text);
        return $result == 1 ? true : false;
    }

    /**
     * Validate an item
     */
    public static function validateItem($validationitem, $item)
    {
        $validationmethod = $validationitem[1];
        $validationnonull = isset($validationitem[2])
            ? $validationitem[2]
            : false;
        $validationmaxsize = isset($validationitem[3]) ? $validationitem[3] : 0;
        $validationextra  = isset($validationitem[4]) ? $validationitem[4] : '';

        $returnarray = [];

        if ($item !== null && $validationmaxsize > 0) {
            if (strlen($item) > $validationmaxsize) {
                $returnarray['error'] = 'Too much text for field, please fix.';
            }
        }

        if ($item === null) {
            if ($validationnonull == true) {
                $returnarray['error'] = 'field is required, please complete.';
            } else {
                $returnarray['value'] = [null, 'NULL'];
            }
        } else {
            if (!isset($returnarray['error'])) {
                switch ($validationmethod) {
                    case 'NULL':
                        $returnarray['value'] = [null, 'NULL'];
                        break;
                    case 'STRING':
                        $returnarray['value'] = [$item, 'STRING'];
                        break;
                    case 'DATETIME':
                        $date = date_create_from_format('d/m/Y H:i:s', $item);
                        if ($date == false) {
                            $returnarray['error'] = 'Invalid Date Time.';
                        } else {
                            $returnarray['value'] = [
                                $date->format('Y-m-d H:i:s'),
                                'DATETIME',
                            ];
                        }
                        break;
                    case 'DATE':
                        if (self::validateDmyDate($item) == false) {
                            $returnarray['error'] = 'Invalid Date.';
                        } else {
                            $returnarray['value'] = [
                                Conversion::changeDMY($item),
                                'DATE',
                            ];
                        }
                        break;
                    case 'CDATE': // current date
                        if (self::validateDmyDate($item) == false) {
                            $returnarray['error'] = 'Invalid Date.';
                        } else {
                            $newdate = Conversion::changeDMY($item);
                        }
                        $current = new \DateTime('now');
                        $testdate = new \DateTime($newdate);

                        if ($testdate < $current) {
                            $returnarray['value'] = [$newdate, 'DATE'];
                        } else {
                            $returnarray['error'] = 'Cant be in the future';
                        }

                        break;
                    case 'YMDDATE': // Basically dont reformat the date
                        if (self::validateDATE($item) == false) {
                            $returnarray['error'] = 'Invalid Date.';
                        } else {
                            $returnarray['value'] = [$item, 'DATE'];
                        }
                        break;
                    case 'MYDATE': // Card specific expiry date
                        $item = '01/' . $item;
                        if (self::validateDmyDate($item) == false) {
                            $returnarray['error'] = 'Invalid Expiry Date.';
                        } else {
                            $returnarray['value'] = [
                                Conversion::changeDMY($item),
                                'MYDATE',
                            ];
                        }
                        break;
                    case 'INT':
                        if (is_numeric($item) == false) {
                            $returnarray['error'] = 'Not A Number.';
                        } else {
                            if (preg_match('/[^0-9\.]/', strval($item))) {
                                $returnarray['error'] = 'Not A Number.';
                            } else {
                                $returnarray['value'] = [$item, 'INT'];
                            }
                        }
                        break;
                    case 'REGEX':
                        if (self::validateRegex($validationextra, (string) $item)) {
                            $returnarray['error'] = 'Invalid Data.';
                        } else {
                            $returnarray['value'] = [$item, 'REGEX'];
                        }
                        break;
                    case 'TIME24':
                        $explode = explode(':', $item);
                        if (
                            is_numeric($explode[0]) and is_numeric($explode[1])
                        ) {
                            $returnarray['value'] = [$item, 'TIME24'];
                        } else {
                            $returnarray['error'] =
                                'Not In the Correct HH:MM Format.';
                        }
                        break;
                    case 'EMAIL':
                        if (self::validateEmail($item) == false) {
                            $returnarray['error'] =
                                'Invalid Email Syntax or Domain.';
                        } else {
                            $returnarray['value'] = [$item, 'EMAIL'];
                        }
                        break;
                    case 'PHONE':
                        if (self::validatePhone($item) == false) {
                            $returnarray['error'] =
                                'Invalid Phone number, for international please start with +';
                        } else {
                            $returnarray['value'] = [$item, 'PHONE'];
                        }
                        break;
                    case 'ARRAY':
                        if (is_array($item) == false) {
                            $returnarray['error'] = 'Invalid Input';
                        } else {
                            $returnarray['value'] = [$item, 'ARRAY'];
                        }
                        break;
                    default:
                        echo  "unknown validation method $validationmethod";
                        exit;
                        break;
                }

                $test = self::validateExtra($validationmethod, $item);
                if (count($test) > 0) {
                    $returnarray = $test;
                }
            }
        }

        return $returnarray;
    }

    /**
     * Validate post array
     */
    public static function validatePostArray($passedarray, $postdata)
    {
        $results = [];
        $results['errors'] = [];
        $results['values'] = [];

        foreach ($passedarray as $item_name => $validation) {
            if (isset($postdata[$item_name])) {
                $temp = self::validateItem($validation, $postdata[$item_name]);
                if (isset($temp['error'])) {
                    $results['errors'][$item_name] = $temp['error'];
                } else {
                    $results['values'][$item_name] = $temp['value'][0];
                }
            }
        }

        return $results;
    }

    /**
     *  Validate data
     *
     * @param array $validationarray
     * @param array $data
     * @return array $errorarray
     *         This is a simple error verification based on the criteria, it will return an error array.
     *         It uses the same methods that the validateData public static function does but does no other processing
     */
    public static function validateDataOnly($validationarray, $data)
    {
        $errorarray = [];

        // Check that we have all required fields
        /**
         * value[0] = database column name
         * value[1] = type of validation to use
         * value[2] = is a required field
         * value[3] = max field length
         * value[4] = extra validation information (some validaiton types such as REGEX require additional info otherwise pass everything)
         */
        foreach ($validationarray as $key => $value) {
            // Check thast the validation array has a required field
            if (!isset($value[2])) {
                continue;
            }

            $isRequired = $value[2];

            // Not required
            if (!$isRequired) {
                continue;
            }

            // We have field
            if (isset($data[$key]) && $data[$key] !== null && !empty($data[$key])) {
                continue;
            }

            // Error, missing required field
            $errorarray[$key] = "Missing required field";
        }

        foreach ($data as $k => $v) {
            if (isset($validationarray[$k])) {
                // print("!");
                $item = null;
                if (isset($v)) {
                    if (strlen($v) > 0) {
                        $item = $v;
                        $hasdata = true;
                    }
                }
                $testitem = self::validateItem($validationarray[$k], $item);
                // print_r($testitem);
                if (isset($testitem['error'])) {
                    $errorarray[$k] = $testitem['error'];
                }
            }
        }

        return $errorarray;
    }
}
