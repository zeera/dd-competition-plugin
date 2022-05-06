<?php
declare(strict_types=1);
namespace WpDigitalDriveCompetitions\Core;
/**
 * Conversion Core
 */
class Conversion
{

    /**
     * Sort an array by its fields alphabetically
     */
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
     * Echo out the difference in time, returns the time
     */
    public static function speedTimer($prevtime = 0)
    {
        $time_start = microtime(true);

        if ($prevtime == 0) {
            echo 'Timer: Start <br />';
        } else {
            $timedif = $time_start - $prevtime;
            echo "Timer: $timedif <br />";
        }

        return $time_start;
    }

    /**
     * ..?
     */
    public static function customJoin($query, $values)
    {
        $result = vsprintf($query, $values);
        return $result;
    }

    /**
     * A safe divide by zero public static function.
     */
    public static function division($a, $b)
    {
        $c = @($a / $b);
        if ($b == 0) {
            $c = 0;
        }
        return $c;
    }

    /**
     * Add commas to a number (1000000.00 -> 1,000,000.00)
     */
    public static function commaNumbers($number)
    {
        return number_format($number, 2, '.', ',');
    }

    //added strval
    public static function changeYMD($date,$dateformat = 'd/m/Y', $addtime = false){
        $date = strval($date);
        if (strlen($date) < 4){
            return '';
        }

		if ($addtime != false){
			$dateformat .= ' H:i';
		}
        return date($dateformat,strtotime($date));
    }


    /**
     * Turn a csv file into an associative array
     * can pass an array to be the header file, in the case of the incomming file not having one.
     */
    public static function csvToArray($filename = '',$delimiter = ',',$header = [])
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

        $header = count($header) > 0 ? $header : null;
        $data = [];
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header) {
                    foreach ($row as $r) {
                        $header[] = str_replace(
                            '?',
                            '',
                            utf8_decode(strtolower($r))
                        ); //Removing invalid characters and forcing lowercase
                    }
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }
        return $data;
    }

    /**
     * Changes dmy into ymd
     */
	public static function changeDMY($date,$seperators = false,$dateformat = 'd-m-Y'){
        if (strlen($date) < 4){
            return '';
        }
		if ($seperators == false){
			$dateformat = str_replace("-","",$dateformat);
		}
        return date($dateformat,strtotime($date));
    }


    /**
     * Converty a number's base from one to another, eg, base 16 (hex) to 10 (dec)
     *
     * From PHPCoder at niconet2k dot com
     * Example: convBase('355927353784509896715106760','0123456789','Christopher');
     * Convert '355927353784509896715106760' from decimal (base10) to undecimal (base11) using "Christopher" as the numbers.
     */
    public static function convBase($numberInput, $fromBaseInput, $toBaseInput)
    {
        if ($fromBaseInput == $toBaseInput) {
            return $numberInput;
        }
        $fromBase = str_split($fromBaseInput, 1);
        $toBase = str_split($toBaseInput, 1);
        $number = str_split($numberInput, 1);
        $fromLen = strlen($fromBaseInput);
        $toLen = strlen($toBaseInput);
        $numberLen = strlen($numberInput);
        $retval = '';
        if ($toBaseInput == '0123456789') {
            $retval = 0;
            for ($i = 1; $i <= $numberLen; $i++) {
                $retval = bcadd(
                    (string) $retval,
                    bcmul(
                        array_search($number[$i - 1], $fromBase),
                        bcpow((string) $fromLen, (string) ($numberLen - $i))
                    )
                );
            }
            return $retval;
        }
        if ($fromBaseInput != '0123456789') {
            $base10 = self::convBase(
                $numberInput,
                $fromBaseInput,
                '0123456789'
            );
        } else {
            $base10 = $numberInput;
        }
        if ($base10 < strlen($toBaseInput)) {
            return $toBase[$base10];
        }
        while ($base10 != '0') {
            $retval = $toBase[bcmod($base10, (string) $toLen)] . $retval;
            $base10 = bcdiv($base10, (string) $toLen, 0);
        }
        return $retval;
    }

    /**
     * figures out searching with dates involved
     */
    public static function searchDate($date)
    {
        $date = str_replace('/', '-', $date);

        $date_explode = explode('-', $date);

        if (count($date_explode) == 1) {
            if (strlen($date_explode[0]) == 2) {
                $date_explode[0] = '20' . $date_explode[0];
            }

            $date_explode[2] = $date_explode[0];
            $date_explode[0] = '';
            $date_explode[1] = '';
        }

        if (count($date_explode) == 2) {
            if (strlen($date_explode[1]) == 2) {
                $date_explode[1] = '20' . $date_explode[1];
            }
            $date_explode[2] = $date_explode[1];
            $date_explode[1] = $date_explode[0];
            $date_explode[0] = '';
        }

        if (count($date_explode) == 3) {
            if (strlen($date_explode[2]) == 2) {
                $date_explode[2] = '20' . $date_explode[2];
            }
            $date_explode[2] = $date_explode[2];
            $date_explode[1] = $date_explode[1];
            $date_explode[0] = $date_explode[0];
        }

        return $date_explode;
    }

    /**
     * This function takes a given string, and culls it as needed to fit within the given length
     *
     * @param string $string
     * @param int $length
     * @return string
     */
    public function shrinkString(string $string, int $length = 50): string
	{
		return mb_substr($string, 0, $length, 'UTF-8');
	}
}
