<?php
declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Shortcode;

class Example
{
public static function big($text1 = "",$text2 = "")
{
    //You can add styles here, which if its a normal wordpress site will end up loading correctly
    //wp_enqueue_style('parrotfish.booking', MOTIV8BOOKING_URL . "/css/parrotfish.booking.css",array(),MOTIV8BOOKING_CSS_VERSION);

    //We warn if the required attributes in the shortcode are not entered.
    if (empty($text1) and empty($text2)) {
        return '<div style="color:red;">' . (__("Error! You must specify a text1 or text2 in the shortcode.", "WSPSC")) . '</div>';
    }

    //A return string, many different way to do this,
    $replacestring = '<h2>Our super special text %text1% is almost as good as the alternative, %text2%</h2>';

    $replacestring = str_replace(['%text1%','%text2%'],[$text1,$text2],$replacestring);

    return $replacestring;
}
}
?>
