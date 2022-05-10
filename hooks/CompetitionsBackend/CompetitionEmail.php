<?php

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Hooks\CompetitionsBackend;

use WpDigitalDriveCompetitions\Helpers\AdminHelper;

/**
 * Controller Core
 */

class CompetitionEmail extends AdminHelper
{
    public static function wpMailContentFilter()
    {
        return "text/html";
    }

    public static function emailDetails()
    {
        $info = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <title></title>
                <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            </head>
            <body style="margin: 0; padding: 0px 0px 150px 0px; background-color:#e7e7e7;">
            <table
                border="0"
                cellpadding="0"
                cellspacing="0"
                style="background-color:#e7e7e7; width:775px; max-width:90%; margin:0 auto;" >
                <thead>
                    <tr>
                        <th style="padding: 20px 0px 20px 0px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 60px 30px 60px 30px; background-color:#FFFFFF; text-align:left; font-size:14px; color:#656565;">
                            [message]
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center; font-size:10px; padding:30px 0px 0px 0px; color:#656565;">
                            &copy; ' . date("Y") . ' ' . get_bloginfo() . '. All Rights Reserved
                        </td>
                    </tr>
                </tbody>
            </table>
            </body>
        </html>';

        return $info;
    }

    public function setEmail($args = [])
    {
        if (isset($args['status'])) {
            $message = '';
            if ( $args['status'] == 'correct' ) {
                $message = '<h2>Congratulations your answer is correct!</h2>';
                $message .= '
                    <table>
                        <tbody>
                            <tr>
                                <td width="25%"><b>Competition Name:</b></td>
                                <td width="75%">' . $args['competition_name'] . '</td>
                            </tr>
                            <tr>
                                <td width="25%"><b>Question:</b></td>
                                <td width="75%">' . $args['question'] . '</td>
                            </tr>
                            <tr>
                                <td width="25%"><b>Your Answer:</b></td>
                                <td width="75%">' . $args['answer'] . '</td>
                            </tr>
                            <tr>
                                <td width="25%"><b>Correct Answer:</b></td>
                                <td width="75%">' . $args['correct_answer'] . '</td>
                            </tr>
                            <tr>
                                <td width="25%"><b>Ticket Numbers:</b></td>
                                <td width="75%">' . implode(', ', $args['ticket_number']) . '</td>
                            </tr>
                        </tbody>
                    </table>
                ';
            } elseif ( $args['status'] == 'in-correct' ) {
            }
            $message = str_replace("[message]", $message, $this->emailDetails());
            $sendEmail = self::sendEmail($args['email'], $args['subject'], $message);
            return $sendEmail;
        }
    }

    public static function sendEmail($email = '', $subject = '', $message = '')
    {
        add_filter('wp_mail_content_type', [self::class, 'wpMailContentFilter']);

        wp_mail($email, $subject, $message);

        // Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
        remove_filter('wp_mail_content_type', [self::class, 'wpMailContentFilter']);

        return true;
    }
}
