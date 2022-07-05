<?php
/**
 * =====================================
 * Competition Fields
 * =====================================
 * File Description
 * =====================================
 */

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Hooks\CompetitionsFrontend;

use WpDigitalDriveCompetitions\Helpers\AdminHelper;
use WpDigitalDriveCompetitions\Models\TicketNumber;

class CompetitionProcess extends AdminHelper
{
    protected static $guestEmail = '';

    public function __construct()
    {
        $this->ticketNumbers = new TicketNumber();
    }

    public static function setQuestion()
    {
        $adminHelper = new AdminHelper;
        $productData = get_queried_object();
        $productData = wc_get_product( $productData->ID );
        $showQuestion =  $productData->get_meta('_show_question', true);
        $question =  $productData->get_meta('_question', true);
        $answer_1 =  $productData->get_meta('_answer_1', true);
        $answer_2 =  $productData->get_meta('_answer_2', true);
        $answer_3 =  $productData->get_meta('_answer_3', true);
        if (is_singular('product')) {
            if ($productData->get_type() == 'competition') {
                ?>
                    <div class="competition-price-wrapper">
                        <?php echo $productData->get_price_html(). '<small>per entry</small>'; ?>
                    </div>
                    <?php if ($showQuestion == 'yes'): ?>
                        <div class="question-ans">
                            <h4><?php echo $question; ?></h4>
                            <ul class="competition-answer-list">
                                <li>
                                    <input class="competition_answer" type="radio" name="competition_answer" value="<?php echo $answer_1; ?>" id="<?php echo $answer_1; ?>" required>
                                    <label for="<?php echo $answer_1; ?>"><?php echo $answer_1; ?></label>
                                </li>
                                <li>
                                    <input class="competition_answer" type="radio" name="competition_answer" value="<?php echo $answer_2; ?>" id="<?php echo $answer_2; ?>" required>
                                    <label for="<?php echo $answer_2; ?>"><?php echo $answer_2; ?></label>
                                </li>
                                <li>
                                    <input class="competition_answer" type="radio" name="competition_answer" value="<?php echo $answer_3; ?>" id="<?php echo $answer_3; ?>" required>
                                    <label for="<?php echo $answer_3; ?>"><?php echo $answer_3; ?></label>
                                </li>
                            </ul>
                            <small>for free postal entries, please <a href="#">click here.</a></small>
                        </div>
                    <?php endif; ?>
                <?php
            }
        }
    }

    public static function setRangeQtySlider()
    {
        $adminHelper = new AdminHelper;
        $productData = get_queried_object();
        $productData = wc_get_product( $productData->ID );
        $maximumTicketPerUser =  $productData->get_meta('_maximum_ticket_per_user', true);
        $defaultMaximumTicketPerUser =  get_option('maximum_ticket_default_per_user');
        $defaultBasket =  $productData->get_meta('_default_basket', true);
        $defaultBasketSettings =  get_option('default_basket_quantity');
        if (is_singular('product')) {
            if ($productData->get_type() == 'competition') {
                ?>
                    <div class="range-quantity">
                        <button
                            class="slider-subtract"
                            id="minus-quantity"
                            style="color:#fff;background: linear-gradient(180deg, <?= get_option('top_info_background_color_one'); ?> 0%, <?= get_option('top_info_background_color_two'); ?> 100%);">-</button>
                        <div class="range-wrap">
                            <input
                                type="range"
                                step="1"
                                min="1"
                                max="<?= $maximumTicketPerUser ? $maximumTicketPerUser : $defaultMaximumTicketPerUser; ?>"
                                value="<?= $defaultBasket ? $defaultBasket : $defaultBasketSettings; ?>"
                                name="quantity"
                                class="slider-quantity">
                            <output class="bubble"></output>
                        </div>
                        <button
                            class="slider-addition"
                            id="plus-quantity"
                            style="color:#fff;background: linear-gradient(180deg, <?= get_option('top_info_background_color_one'); ?> 0%, <?= get_option('top_info_background_color_two'); ?> 100%);">+</button>
                    </div>
                <?php
            }
        }
    }

    public static function setTopData()
    {
        $adminHelper = new AdminHelper;
        $productData = get_queried_object();
        $productData = wc_get_product( $productData->ID );
        if (is_singular('product')) {
            if ($productData->get_type() == 'competition') {
                ?>
                    <section class="top-competition-data">
                        <div class="container">
                            <div class="row align-items-center justify-content-center">
                                <div class="col text-center">
                                    <div class="heading">
                                        <div class="right-border"></div>
                                        <h1 class="text-uppercase fw-bold" style="color:<?= get_option('top_info_heding_color') ?>"><?= $productData->name; ?></h1>
                                        <div class="left-border"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row align-items-center justify-content-center">
                                <div class="col-12 col-md-4">
                                    <div
                                        class="top-data fw-medium py-3 px-2 rounded d-flex flex-row align-items-center justify-content-center text-uppercase"
                                        style="color:<?= get_option('top_info_text_color'); ?>; background: linear-gradient(180deg, <?= get_option('top_info_background_color_one'); ?> 0%, <?= get_option('top_info_background_color_two'); ?> 100%);">
                                        <?php
                                            $date = $productData->get_meta('_draw_date_and_time', true);
                        $date = date("jS F Y \@ h:i A", strtotime($date)); ?>
                                        <svg class="me-3" width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.4546 8.88683V9.9323H9.40918V11.5005H10.4546V16.205H12.5455V11.5005H13.936L14.1137 9.9323H12.5455V9.01751C12.5455 8.59412 12.5874 8.36933 13.2407 8.36933H14.1137V6.7959H12.7128C11.0401 6.79594 10.4546 7.58001 10.4546 8.88683Z" fill="#1F1F1F"/>
                                        <path d="M11.5 0C5.14872 0 0 5.14872 0 11.5C0 17.8513 5.14872 23 11.5 23C17.8513 23 23 17.8513 23 11.5C23 5.14872 17.8513 0 11.5 0ZM11.5 21.9545C5.7261 21.9545 1.04547 17.2739 1.04547 11.5C1.04547 5.7261 5.7261 1.04547 11.5 1.04547C17.2739 1.04547 21.9545 5.7261 21.9545 11.5C21.9545 17.2739 17.2739 21.9545 11.5 21.9545Z" fill="#1F1F1F"/>
                                        </svg>
                                        Live Draw <?= $date; ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div
                                        class="top-data fw-medium py-3 px-2 rounded d-flex flex-row align-items-center justify-content-center text-uppercase"
                                        style="color:<?= get_option('top_info_text_color'); ?>; background: linear-gradient(180deg, <?= get_option('top_info_background_color_one'); ?> 0%, <?= get_option('top_info_background_color_two'); ?> 100%);">
                                        <?php
                                            $tickets = $productData->get_meta('_maximum_ticket', true); ?>
                                        <svg class="me-3" width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.27906 10.376C6.40504 10.376 6.52841 10.3248 6.61739 10.2358C6.70636 10.1466 6.75758 10.0233 6.75758 9.89747C6.75758 9.77148 6.70636 9.64811 6.61739 9.55914C6.52841 9.47016 6.40504 9.41895 6.27906 9.41895C6.1527 9.41895 6.02989 9.47016 5.94073 9.55914C5.85175 9.64811 5.80054 9.77148 5.80054 9.89747C5.80054 10.0233 5.85175 10.1466 5.94073 10.2358C6.02989 10.3248 6.15326 10.376 6.27906 10.376Z" fill="black"/>
                                            <path d="M13.1339 9.74224C13.0445 9.65327 12.9215 9.60205 12.7952 9.60205C12.6694 9.60205 12.5458 9.65327 12.4568 9.74224C12.3679 9.83122 12.3167 9.95477 12.3167 10.0806C12.3167 10.2064 12.3679 10.3299 12.4568 10.4189C12.5458 10.5079 12.6694 10.5591 12.7952 10.5591C12.9215 10.5591 13.0445 10.5079 13.1339 10.4189C13.2228 10.3299 13.2737 10.2069 13.2737 10.0806C13.2737 9.95477 13.2228 9.83122 13.1339 9.74224Z" fill="black"/>
                                            <path d="M14.4703 8.88429C14.5963 8.88429 14.7197 8.83307 14.8087 8.7441C14.8977 8.65512 14.9485 8.53157 14.9485 8.40577C14.9485 8.27997 14.8977 8.15641 14.8087 8.06744C14.7197 7.97846 14.5963 7.92725 14.4703 7.92725C14.3442 7.92725 14.2206 7.97846 14.1316 8.06744C14.0427 8.15641 13.9915 8.27997 13.9915 8.40577C13.9915 8.53157 14.0427 8.65512 14.1316 8.7441C14.2206 8.83307 14.3442 8.88429 14.4703 8.88429Z" fill="black"/>
                                            <path d="M23.0647 11.7714H21.7295L24.0799 9.42106C24.3509 9.14983 24.5002 8.78926 24.5002 8.40588C24.5002 8.02251 24.3509 7.66193 24.0799 7.3909L21.948 5.25905C21.7613 5.07232 21.4583 5.07232 21.2714 5.25905C21.0003 5.53027 20.6397 5.67963 20.2564 5.67963C19.8728 5.67963 19.5124 5.53027 19.2412 5.25905C18.97 4.98801 18.8208 4.62744 18.8208 4.24406C18.8208 3.8605 18.9702 3.50011 19.2412 3.22889C19.3309 3.13917 19.3814 3.01748 19.3814 2.89056C19.3814 2.76364 19.3309 2.64196 19.2412 2.55223L17.1095 0.420575C16.8383 0.149351 16.4779 0 16.0944 0C15.711 0 15.3504 0.149351 15.0794 0.420575L7.17594 8.32382C6.98901 8.51056 6.98901 8.81356 7.17594 9.00048C7.36286 9.1874 7.66586 9.1874 7.85278 9.00048L15.756 1.09723C15.8465 1.00676 15.9665 0.957041 16.0944 0.957041C16.2222 0.957041 16.3424 1.00676 16.4327 1.09723L18.2599 2.92421C18.0021 3.31226 17.8638 3.7676 17.8638 4.24406C17.8638 4.88315 18.1126 5.48392 18.5645 5.9359C19.0165 6.38769 19.6173 6.63667 20.2564 6.63667C20.7328 6.63667 21.188 6.49834 21.5762 6.24058L23.4032 8.06755C23.5897 8.2541 23.5897 8.55766 23.4032 8.74421L20.376 11.7714H18.2782L19.8504 10.1992C20.0371 10.0123 20.0371 9.70929 19.8504 9.52256L14.9779 4.65006C14.791 4.46314 14.488 4.46314 14.301 4.65006L7.17968 11.7714H1.43556C0.643946 11.7714 0 12.4154 0 13.207V16.2217C0 16.4858 0.214213 16.7002 0.47852 16.7002C1.27014 16.7002 1.91408 17.3441 1.91408 18.1357C1.91408 18.9272 1.27014 19.5713 0.47852 19.5713C0.214213 19.5713 0 19.7855 0 20.0498V23.0645C0 23.8559 0.643946 24.5001 1.43556 24.5001H23.0647C23.8563 24.5001 24.5002 23.8559 24.5002 23.0645V20.0498C24.5002 19.7855 24.286 19.5713 24.0217 19.5713C23.2301 19.5713 22.5862 18.9272 22.5862 18.1357C22.5862 17.3441 23.2301 16.7002 24.0217 16.7002C24.286 16.7002 24.5002 16.4858 24.5002 16.2217V13.207C24.5002 12.4154 23.8563 11.7714 23.0647 11.7714ZM14.6394 5.66505L18.8352 9.86088L16.9247 11.7714H15.0298L16.4327 10.3684C16.6196 10.1816 16.6196 9.87864 16.4327 9.69172C16.246 9.5048 15.943 9.5048 15.756 9.69172L13.6763 11.7712H8.53318L14.6394 5.66505ZM23.5432 15.7912C22.4525 16.0134 21.6291 16.9802 21.6291 18.1357C21.6291 19.2911 22.4525 20.2579 23.5432 20.4803V23.0645C23.5432 23.3282 23.3286 23.543 23.0647 23.543H1.43556C1.17163 23.543 0.957041 23.3282 0.957041 23.0645V20.4803C2.04773 20.2579 2.87112 19.2911 2.87112 18.1357C2.87112 16.9802 2.04773 16.0136 0.957041 15.7912V13.207C0.957041 12.943 1.17163 12.7285 1.43556 12.7285H23.0647C23.3286 12.7285 23.5432 12.943 23.5432 13.207V15.7912Z" fill="black"/>
                                            <path d="M10.2199 17.3256C10.3089 17.2365 10.3601 17.1131 10.3601 16.9873C10.3601 16.8613 10.3089 16.738 10.2199 16.649C10.131 16.56 10.0074 16.5088 9.8816 16.5088C9.7558 16.5088 9.63224 16.56 9.54327 16.649C9.45429 16.738 9.40308 16.8613 9.40308 16.9873C9.40308 17.1131 9.45429 17.2365 9.54327 17.3256C9.63224 17.4146 9.7558 17.4658 9.8816 17.4658C10.0074 17.4658 10.131 17.4146 10.2199 17.3256Z" fill="black"/>
                                            <path d="M14.9572 17.3256C15.0462 17.2365 15.0974 17.1131 15.0974 16.9873C15.0974 16.8613 15.0462 16.738 14.9572 16.649C14.8683 16.56 14.7447 16.5088 14.6189 16.5088C14.4931 16.5088 14.3695 16.56 14.2806 16.649C14.1916 16.738 14.1404 16.8613 14.1404 16.9873C14.1404 17.1131 14.1916 17.2365 14.2806 17.3256C14.3695 17.4146 14.4931 17.4658 14.6189 17.4658C14.7447 17.4658 14.8683 17.4146 14.9572 17.3256Z" fill="black"/>
                                            <path d="M12.5886 17.3256C12.6776 17.2365 12.7288 17.1131 12.7288 16.9873C12.7288 16.8613 12.6776 16.738 12.5886 16.649C12.4996 16.56 12.376 16.5088 12.2502 16.5088C12.1245 16.5088 12.0009 16.56 11.9119 16.649C11.8229 16.738 11.7717 16.8613 11.7717 16.9873C11.7717 17.1131 11.8229 17.2365 11.9119 17.3256C12.0009 17.4146 12.1245 17.4658 12.2502 17.4658C12.376 17.4658 12.4996 17.4146 12.5886 17.3256Z" fill="black"/>
                                            <path d="M7.5127 17.4658C7.6385 17.4658 7.76205 17.4146 7.85103 17.3256C7.94 17.2365 7.99122 17.1131 7.99122 16.9873C7.99122 16.8613 7.94 16.738 7.85103 16.649C7.76205 16.56 7.6385 16.5088 7.5127 16.5088C7.3869 16.5088 7.26335 16.56 7.17437 16.649C7.0854 16.738 7.03418 16.8613 7.03418 16.9873C7.03418 17.1131 7.0854 17.2365 7.17437 17.3256C7.26335 17.4146 7.3869 17.4658 7.5127 17.4658Z" fill="black"/>
                                            <path d="M17.3259 17.3256C17.4149 17.2365 17.4661 17.1131 17.4661 16.9873C17.4661 16.8613 17.4149 16.738 17.3259 16.649C17.2369 16.56 17.1134 16.5088 16.9876 16.5088C16.8618 16.5088 16.7382 16.56 16.6492 16.649C16.5602 16.738 16.509 16.8613 16.509 16.9873C16.509 17.1131 16.5602 17.2365 16.6492 17.3256C16.7382 17.4146 16.8618 17.4658 16.9876 17.4658C17.1134 17.4658 17.2369 17.4146 17.3259 17.3256Z" fill="black"/>
                                            <path d="M7.5127 19.7627H16.9874C17.2517 19.7627 17.4659 19.5483 17.4659 19.2842C17.4659 19.0199 17.2517 18.8057 16.9874 18.8057H7.5127C7.24839 18.8057 7.03418 19.0199 7.03418 19.2842C7.03418 19.5483 7.24839 19.7627 7.5127 19.7627Z" fill="black"/>
                                            <path d="M9.25074 21.1025C9.12494 21.1025 9.00138 21.1538 8.91241 21.2427C8.82343 21.3317 8.77222 21.4551 8.77222 21.5811C8.77222 21.7069 8.82343 21.8302 8.91241 21.9194C9.00138 22.0084 9.12494 22.0596 9.25074 22.0596C9.3771 22.0596 9.50009 22.0084 9.58907 21.9194C9.6786 21.8302 9.72926 21.7069 9.72926 21.5811C9.72926 21.4551 9.6786 21.3317 9.58907 21.2427C9.50009 21.1538 9.3771 21.1025 9.25074 21.1025Z" fill="black"/>
                                            <path d="M19.5236 14.2119H4.97657C4.71226 14.2119 4.49805 14.4261 4.49805 14.6904V21.5811C4.49805 21.8452 4.71226 22.0596 4.97657 22.0596H6.9699C7.23421 22.0596 7.44842 21.8452 7.44842 21.5811C7.44842 21.3168 7.23421 21.1026 6.9699 21.1026H5.45509V15.169H19.0451V21.1026H11.2766C11.0123 21.1026 10.7981 21.3168 10.7981 21.5811C10.7981 21.8452 11.0123 22.0596 11.2766 22.0596H19.5236C19.7879 22.0596 20.0021 21.8452 20.0021 21.5811V14.6904C20.0021 14.4261 19.7879 14.2119 19.5236 14.2119Z" fill="black"/>
                                        </svg>
                                        <?= $tickets; ?> Tickets available
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div
                                        class="top-data fw-medium py-3 px-2 rounded d-flex flex-row align-items-center justify-content-center text-uppercase"
                                        style="color:<?= get_option('top_info_text_color'); ?>; background: linear-gradient(180deg, <?= get_option('top_info_background_color_one'); ?> 0%, <?= get_option('top_info_background_color_two'); ?> 100%);">
                                        <?php
                                            $ticketsPerUser = $productData->get_meta('_maximum_ticket_per_user', true); ?>
                                        <svg class="me-3" width="23" height="26" viewBox="0 0 23 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M21.8456 23.4965H21.6056C21.6571 23.2797 21.6228 23.4385 21.633 19.6025C21.633 19.3945 21.4644 19.226 21.2565 19.226C21.0485 19.226 20.88 19.3946 20.88 19.6025V23.2707C20.88 23.3931 20.7765 23.4966 20.6541 23.4966H10.0618C9.93943 23.4966 9.83593 23.3931 9.83593 23.2707C9.83593 22.666 9.83593 17.2761 9.83593 16.4153C9.83593 16.2928 9.93938 16.1893 10.0618 16.1893H20.6541C20.7765 16.1893 20.88 16.2928 20.88 16.4153V17.8455C20.88 18.0535 21.0485 18.222 21.2565 18.222C21.4645 18.222 21.633 18.0535 21.633 17.8455V16.4153C21.633 15.8755 21.1938 15.4364 20.6541 15.4364C20.1144 15.4364 12.6705 15.4364 12.6705 15.4364L11.8308 15.1508C11.7674 15.129 11.7248 15.0694 11.7248 15.0022V14.0795C13.0914 13.2673 13.9696 11.8694 14.1298 10.3433C15.282 10.3967 16.0718 9.29653 15.5938 8.34248V5.27312C15.5938 3.05345 14.3866 2.2813 14.0178 2.0972C11.8908 -0.441997 7.50659 -0.811407 4.81276 1.74043C4.56431 1.97372 4.73125 2.39142 5.07059 2.39142C5.3479 2.39142 5.30559 2.19728 6.16266 1.64005C6.22616 1.59884 6.34923 1.52622 6.34416 1.52923C8.56413 0.196691 11.8389 0.604899 13.4898 2.64057C13.532 2.69262 13.5835 2.73101 13.6463 2.7545C13.6951 2.77408 14.8407 3.25622 14.8407 5.27317V7.64944C14.6318 7.561 14.4029 7.51568 14.1587 7.52251V6.291C14.1587 5.97861 13.9234 5.72043 13.6113 5.69031C12.9786 5.62923 12.6241 5.21695 12.438 4.88192C12.2052 4.46262 11.6493 4.36214 11.2849 4.67358C9.60611 6.10871 7.67323 6.18159 6.34712 5.99026C5.34223 5.84515 4.41339 6.53102 4.25187 7.52296C4.00814 7.51242 3.76722 7.55553 3.54582 7.64934C3.56941 6.47686 3.3703 5.20767 4.2991 3.58814C4.40254 3.40775 4.34016 3.17772 4.15987 3.07422C3.97943 2.97073 3.7494 3.03317 3.64596 3.21346C3.07949 4.20123 2.8056 5.34233 2.79827 6.15338C2.78888 6.3962 2.79456 6.30732 2.7929 8.34233C2.31417 9.29768 3.1071 10.3964 4.25684 10.3431C4.42182 11.9138 5.35267 13.361 6.8024 14.1604V15.0021C6.8024 15.0692 6.75974 15.1288 6.69655 15.1505C5.2042 15.6723 6.96287 15.0378 3.05023 16.4253C2.70868 16.5266 2.78442 16.5002 2.6496 16.5552C1.8104 16.9077 1.29177 17.5817 1.12413 18.4106C-0.0503032 24.3096 -0.005683 23.9751 0.00229747 24.2958C0.0389373 25.2257 0.633808 25.698 1.5499 25.698C1.56481 25.698 9.27048 25.698 9.26355 25.698C10.0274 25.698 9.73359 25.698 10.4974 25.698C10.7054 25.698 10.8738 25.5294 10.8738 25.3215C10.8738 25.1135 10.7053 24.945 10.4974 24.945H9.2636C8.73734 24.9449 8.56308 24.2495 8.87055 24.2495C9.71588 24.2495 21.3649 24.2495 21.8458 24.2495C22.1538 24.2495 21.9775 24.945 21.4527 24.945H12.2543C12.0463 24.945 11.8778 25.1136 11.8778 25.3215C11.8778 25.5295 12.0463 25.698 12.2543 25.698H21.4525C22.1679 25.698 22.7498 25.116 22.7498 24.4006C22.7499 23.9021 22.3443 23.4965 21.8456 23.4965ZM14.1587 8.27573C14.5928 8.25279 14.9816 8.54526 14.9816 8.93324C14.9816 9.32504 14.5888 9.61354 14.1587 9.5908V8.27573ZM4.22778 9.59075C3.79367 9.61379 3.40494 9.32122 3.40494 8.93324C3.40494 8.54144 3.79774 8.25294 4.22778 8.27568V9.59075ZM4.98677 10.0229C4.97724 9.84868 4.98191 9.91167 4.98075 7.97122C4.99204 7.90798 4.89974 7.3905 5.35864 6.99303C5.60057 6.78348 5.92169 6.68973 6.23951 6.73555C7.71931 6.94932 9.88096 6.86419 11.7741 5.24596L11.7797 5.24742C12.1433 5.90232 12.7171 6.31575 13.4057 6.42296C13.4057 9.8261 13.4498 10.1579 13.2806 10.8315C13.1765 11.2479 12.9969 11.6682 12.7784 12.0209C10.5689 15.513 5.19266 14.1402 4.98677 10.0229ZM3.44454 24.9451H1.54975C0.905244 24.9451 0.789703 24.6401 0.761094 24.3743C0.73128 24.0973 0.792715 23.973 1.05195 22.628H3.44454V24.9451ZM8.8704 23.4966C8.25861 23.4966 7.77863 24.0891 8.03958 24.8306C8.05037 24.8613 8.06864 24.908 8.0859 24.9452H4.19756C4.19756 22.3399 4.19756 24.0471 4.19756 21.3966C4.19756 21.1886 4.02902 21.0201 3.82107 21.0201C3.61308 21.0201 3.44459 21.1886 3.44459 21.3966V21.8751H1.20188C1.91209 18.3105 1.87133 18.4597 1.94878 18.2542C2.10377 17.8473 2.33465 17.6091 2.66597 17.3905C2.96054 17.2206 2.99392 17.23 3.27047 17.1456C3.2904 17.1399 3.15739 17.1861 5.44066 16.3773L6.80451 15.9096C6.81365 16.563 7.23225 17.1388 7.85302 17.3467C8.24602 17.4783 8.65924 17.5508 9.08301 17.5639C9.1069 23.2928 9.04873 23.2411 9.10931 23.4966H8.8704ZM9.08296 16.4151C9.08296 16.4246 9.08296 16.816 9.08296 16.811C8.74085 16.7983 8.40818 16.7385 8.09208 16.6325C7.7718 16.5253 7.55663 16.226 7.55663 15.8878C7.55573 14.9742 7.55809 15.5321 7.55523 14.4958C9.14595 15.049 10.3979 14.6101 10.483 14.6036L10.4821 14.6006L10.4872 14.5992L10.4881 14.6022C10.6192 14.5596 10.6633 14.5627 10.9718 14.444C10.9718 14.6937 10.9718 15.1809 10.9718 15.4361H10.0619C9.52214 15.4362 9.08296 15.8754 9.08296 16.4151Z" fill="black"/>
                                            <rect x="7.66675" y="15.1567" width="15.3333" height="10.5416" fill="url(#paint0_linear_2_1819)"/>
                                            <path d="M12.2411 16.3426C12.3137 16.3426 12.3847 16.3132 12.436 16.2619C12.4872 16.2106 12.5167 16.1395 12.5167 16.0671C12.5167 15.9945 12.4872 15.9235 12.436 15.8722C12.3847 15.821 12.3137 15.7915 12.2411 15.7915C12.1684 15.7915 12.0977 15.821 12.0463 15.8722C11.9951 15.9235 11.9656 15.9945 11.9656 16.0671C11.9656 16.1395 11.9951 16.2106 12.0463 16.2619C12.0977 16.3132 12.1687 16.3426 12.2411 16.3426Z" fill="black"/>
                                            <path d="M16.1884 15.9767C16.1369 15.9255 16.0661 15.896 15.9933 15.896C15.9209 15.896 15.8497 15.9255 15.7985 15.9767C15.7473 16.028 15.7178 16.0991 15.7178 16.1716C15.7178 16.244 15.7473 16.3152 15.7985 16.3664C15.8497 16.4176 15.9209 16.4471 15.9933 16.4471C16.0661 16.4471 16.1369 16.4176 16.1884 16.3664C16.2396 16.3152 16.2689 16.2443 16.2689 16.1716C16.2689 16.0991 16.2396 16.028 16.1884 15.9767Z" fill="black"/>
                                            <path d="M16.9582 15.4823C17.0307 15.4823 17.1018 15.4528 17.153 15.4016C17.2042 15.3503 17.2335 15.2792 17.2335 15.2067C17.2335 15.1343 17.2042 15.0631 17.153 15.0119C17.1018 14.9606 17.0307 14.9312 16.9582 14.9312C16.8855 14.9312 16.8143 14.9606 16.7631 15.0119C16.7119 15.0631 16.6824 15.1343 16.6824 15.2067C16.6824 15.2792 16.7119 15.3503 16.7631 15.4016C16.8143 15.4528 16.8855 15.4823 16.9582 15.4823Z" fill="black"/>
                                            <path d="M21.9076 17.1452H21.1387L22.4922 15.7917C22.6483 15.6355 22.7343 15.4278 22.7343 15.207C22.7343 14.9862 22.6483 14.7786 22.4922 14.6225L21.2645 13.3948C21.157 13.2873 20.9825 13.2873 20.8748 13.3948C20.7188 13.551 20.5111 13.637 20.2903 13.637C20.0694 13.637 19.8619 13.551 19.7057 13.3948C19.5495 13.2387 19.4636 13.0311 19.4636 12.8103C19.4636 12.5894 19.5496 12.3819 19.7057 12.2257C19.7574 12.174 19.7864 12.1039 19.7864 12.0308C19.7864 11.9577 19.7574 11.8877 19.7057 11.836L18.4781 10.6084C18.3219 10.4522 18.1144 10.3662 17.8935 10.3662C17.6727 10.3662 17.4651 10.4522 17.309 10.6084L12.7575 15.1598C12.6499 15.2673 12.6499 15.4418 12.7575 15.5494C12.8652 15.6571 13.0396 15.6571 13.1473 15.5494L17.6987 10.9981C17.7508 10.946 17.8199 10.9174 17.8935 10.9174C17.9671 10.9174 18.0363 10.946 18.0883 10.9981L19.1406 12.0502C18.9921 12.2737 18.9125 12.5359 18.9125 12.8103C18.9125 13.1783 19.0557 13.5243 19.316 13.7846C19.5763 14.0448 19.9223 14.1882 20.2903 14.1882C20.5647 14.1882 20.8268 14.1085 21.0504 13.9601L22.1025 15.0122C22.21 15.1196 22.21 15.2944 22.1025 15.4019L20.3592 17.1452H19.1511L20.0565 16.2398C20.1641 16.1321 20.1641 15.9576 20.0565 15.8501L17.2505 13.0441C17.1429 12.9365 16.9684 12.9365 16.8607 13.0441L12.7597 17.1452H9.45172C8.99584 17.1452 8.625 17.516 8.625 17.9719V19.708C8.625 19.8601 8.74836 19.9836 8.90057 19.9836C9.35645 19.9836 9.72729 20.3544 9.72729 20.8103C9.72729 21.2661 9.35645 21.637 8.90057 21.637C8.74836 21.637 8.625 21.7604 8.625 21.9126V23.6487C8.625 24.1045 8.99584 24.4754 9.45172 24.4754H21.9076C22.3635 24.4754 22.7343 24.1045 22.7343 23.6487V21.9126C22.7343 21.7604 22.6109 21.637 22.4587 21.637C22.0029 21.637 21.632 21.2661 21.632 20.8103C21.632 20.3544 22.0029 19.9836 22.4587 19.9836C22.6109 19.9836 22.7343 19.8601 22.7343 19.708V17.9719C22.7343 17.516 22.3635 17.1452 21.9076 17.1452ZM17.0556 13.6286L19.4719 16.0449L18.3717 17.1452H17.2804L18.0883 16.3372C18.196 16.2297 18.196 16.0552 18.0883 15.9475C17.9808 15.8399 17.8063 15.8399 17.6987 15.9475L16.501 17.1451H13.5391L17.0556 13.6286ZM22.1832 19.4601C21.5551 19.5881 21.0809 20.1448 21.0809 20.8103C21.0809 21.4757 21.5551 22.0324 22.1832 22.1605V23.6487C22.1832 23.8006 22.0596 23.9243 21.9076 23.9243H9.45172C9.29972 23.9243 9.17614 23.8006 9.17614 23.6487V22.1605C9.80426 22.0324 10.2784 21.4757 10.2784 20.8103C10.2784 20.1448 9.80426 19.5882 9.17614 19.4601V17.9719C9.17614 17.8199 9.29972 17.6963 9.45172 17.6963H21.9076C22.0596 17.6963 22.1832 17.8199 22.1832 17.9719V19.4601Z" fill="black"/>
                                            <path d="M14.5102 20.343C14.5614 20.2916 14.5909 20.2206 14.5909 20.1481C14.5909 20.0756 14.5614 20.0045 14.5102 19.9533C14.459 19.9021 14.3878 19.8726 14.3154 19.8726C14.2429 19.8726 14.1718 19.9021 14.1205 19.9533C14.0693 20.0045 14.0398 20.0756 14.0398 20.1481C14.0398 20.2206 14.0693 20.2916 14.1205 20.343C14.1718 20.3942 14.2429 20.4237 14.3154 20.4237C14.3878 20.4237 14.459 20.3942 14.5102 20.343Z" fill="black"/>
                                            <path d="M17.2387 20.343C17.29 20.2916 17.3195 20.2206 17.3195 20.1481C17.3195 20.0756 17.29 20.0045 17.2387 19.9533C17.1875 19.9021 17.1163 19.8726 17.0439 19.8726C16.9714 19.8726 16.9003 19.9021 16.849 19.9533C16.7978 20.0045 16.7683 20.0756 16.7683 20.1481C16.7683 20.2206 16.7978 20.2916 16.849 20.343C16.9003 20.3942 16.9714 20.4237 17.0439 20.4237C17.1163 20.4237 17.1875 20.3942 17.2387 20.343Z" fill="black"/>
                                            <path d="M15.8745 20.343C15.9257 20.2916 15.9552 20.2206 15.9552 20.1481C15.9552 20.0756 15.9257 20.0045 15.8745 19.9533C15.8232 19.9021 15.7521 19.8726 15.6796 19.8726C15.6072 19.8726 15.536 19.9021 15.4848 19.9533C15.4335 20.0045 15.4041 20.0756 15.4041 20.1481C15.4041 20.2206 15.4335 20.2916 15.4848 20.343C15.536 20.3942 15.6072 20.4237 15.6796 20.4237C15.7521 20.4237 15.8232 20.3942 15.8745 20.343Z" fill="black"/>
                                            <path d="M12.9514 20.4237C13.0238 20.4237 13.095 20.3942 13.1462 20.343C13.1974 20.2916 13.2269 20.2206 13.2269 20.1481C13.2269 20.0756 13.1974 20.0045 13.1462 19.9533C13.095 19.9021 13.0238 19.8726 12.9514 19.8726C12.8789 19.8726 12.8078 19.9021 12.7565 19.9533C12.7053 20.0045 12.6758 20.0756 12.6758 20.1481C12.6758 20.2206 12.7053 20.2916 12.7565 20.343C12.8078 20.3942 12.8789 20.4237 12.9514 20.4237Z" fill="black"/>
                                            <path d="M18.6025 20.343C18.6537 20.2916 18.6832 20.2206 18.6832 20.1481C18.6832 20.0756 18.6537 20.0045 18.6025 19.9533C18.5513 19.9021 18.4801 19.8726 18.4077 19.8726C18.3352 19.8726 18.2641 19.9021 18.2128 19.9533C18.1616 20.0045 18.1321 20.0756 18.1321 20.1481C18.1321 20.2206 18.1616 20.2916 18.2128 20.343C18.2641 20.3942 18.3352 20.4237 18.4077 20.4237C18.4801 20.4237 18.5513 20.3942 18.6025 20.343Z" fill="black"/>
                                            <path d="M12.9514 21.7469H18.4077C18.5599 21.7469 18.6833 21.6235 18.6833 21.4714C18.6833 21.3192 18.5599 21.1958 18.4077 21.1958H12.9514C12.7991 21.1958 12.6758 21.3192 12.6758 21.4714C12.6758 21.6235 12.7991 21.7469 12.9514 21.7469Z" fill="black"/>
                                            <path d="M13.9523 22.52C13.8799 22.52 13.8087 22.5495 13.7575 22.6008C13.7063 22.652 13.6768 22.723 13.6768 22.7956C13.6768 22.868 13.7063 22.9391 13.7575 22.9904C13.8087 23.0417 13.8799 23.0712 13.9523 23.0712C14.0251 23.0712 14.0959 23.0417 14.1472 22.9904C14.1987 22.9391 14.2279 22.868 14.2279 22.7956C14.2279 22.723 14.1987 22.652 14.1472 22.6008C14.0959 22.5495 14.0251 22.52 13.9523 22.52Z" fill="black"/>
                                            <path d="M19.8683 18.5508H11.4909C11.3387 18.5508 11.2153 18.6741 11.2153 18.8264V22.7946C11.2153 22.9467 11.3387 23.0702 11.4909 23.0702H12.6388C12.791 23.0702 12.9144 22.9467 12.9144 22.7946C12.9144 22.6424 12.791 22.519 12.6388 22.519H11.7665V19.1019H19.5927V22.519H15.119C14.9668 22.519 14.8434 22.6424 14.8434 22.7946C14.8434 22.9467 14.9668 23.0702 15.119 23.0702H19.8683C20.0205 23.0702 20.1439 22.9467 20.1439 22.7946V18.8264C20.1439 18.6741 20.0205 18.5508 19.8683 18.5508Z" fill="black"/>
                                            <defs>
                                            <linearGradient id="paint0_linear_2_1819" x1="15.3334" y1="15.1567" x2="15.3334" y2="25.6984" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#BDAD6E"/>
                                            <stop offset="1" stop-color="#B1A367"/>
                                            </linearGradient>
                                            </defs>
                                        </svg>
                                        Maximum <?= $ticketsPerUser; ?> Tickets per Person
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                <?php
            }
        }
    }

    public static function setCounter()
    {
        $adminHelper = new AdminHelper;
        $productData = get_queried_object();
        $productData = wc_get_product( $productData->ID );
        $drawDate =  $productData->get_meta('_draw_date_and_time', true) ?? '';
        $endDate =  $productData->get_meta('_end_date_and_time', true) ?? '';
        $totalSold =  $productData->get_meta('total_sales', true) ?? '';
        $stockQty =  $productData->get_meta('_maximum_ticket', true) ?? '--';
        $salesPercentage = 0;
        if( $stockQty != "--" ) {
            $salesPercentage = ((int)$totalSold/(int)$stockQty) * 100;
        }
        if (is_singular('product')) {
            if ($productData->get_type() == 'competition') {
                ?>
                    <div class="countdown-shortcode-wrap">
                        <div class="countdown-wrap">
                            <div class="countdown cdjs" data-enddate="<?= $drawDate; ?>">
                                <div class="count-item">
                                    <div class="count-value e-m-days">00</div>
                                    <label>Days</label>
                                </div>
                                <div class="count-item">
                                    <div class="count-value e-m-hours">00</div>
                                    <label>Hr</label>
                                </div>
                                <div class="count-item">
                                    <div class="count-value e-m-minutes">00</div>
                                    <label>Min</label>
                                </div>
                                <div class="count-item">
                                    <div class="count-value e-m-seconds">00</div>
                                    <label>Sec</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="competition-ticket-progress-wrap">
                        <div class="ticket-infos">
                            <div class="info-item">
                                <label>Tickets Sold: <?= $totalSold; ?>/<?= $stockQty; ?></label>
                            </div>
                        </div>
                        <div class="progress-wrapper">
                            <div class="progress-bar" style="width:<?= $salesPercentage; ?>%;">
                                <?php if( $salesPercentage >= 8): ?>
                                    <div class="percentage-marker">
                                        <span><?= number_format($salesPercentage, 2); ?>%</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if( $salesPercentage < 8 ): ?>
                                <p class="placeholder-text"><?= number_format($salesPercentage, 2); ?>%</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="facebook-rating d-flex align-items-center justify-content-center text-center pt-3">
                        <img src="<?= WPDIGITALDRIVE_COMPETITIONS_URL ?>assets/images/facebook-rating.png" alt="">
                    </div>
                <?php
            }
        }
    }

    public static function cartScripts()
    {
        $version = date("ymd-Gis", filemtime(WPDIGITALDRIVE_COMPETITIONS_PATH . 'assets/css/add-to-cart.css'));
        wp_register_style('cart-styles', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/css/add-to-cart.css?v=' . $version);

        $version = date("ymd-Gis", filemtime(WPDIGITALDRIVE_COMPETITIONS_PATH . 'assets/js/add-to-cart.js'));
        wp_register_script('cart-scripts', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/js/add-to-cart.js?v=' . $version, array('jquery'), '', true);

        if( is_singular('product') ) {
            wp_enqueue_style("cart-styles");
            wp_enqueue_script("cart-scripts");
        }
    }

    public static function validateAnswer( $passed, $product_id, $quantity )
    {
        $adminHelper  = new AdminHelper;
        $answer       = $_POST['competition_answer'];
        $email        = $_POST['competition_email'];
        $current_user = $adminHelper->isLoggedIn();
        $isQuestion =  get_post_meta($product_id, '_show_question', true);

        if ( $isQuestion == 'yes' && !$answer ) {
            wc_add_notice( __( ' Please select an answer!', 'woocommerce' ), 'error' );
            $passed = false;
            return $passed;
        }

        // if ( !$current_user && !$email ) {
        //     wc_add_notice( __( ' Please enter Email!', 'woocommerce' ), 'error' );
        //     $passed = false;
        //     return $passed;
        // }

        // if ( self::$guestEmail != '' ) {
        //     $passed = true;
        //     if( $email != self::$guestEmail  ) {
        //         wc_add_notice( __( ' Your Using different email and you have items on your cart. Please clear your cart first when using different email!', 'woocommerce' ), 'error' );
        //         $passed = false;
        //     }
        //     return $passed;
        // }

        if( $current_user ) {
            $cartQty = self::getCartItems($product_id);
            $passed = self::validateItems($quantity, $product_id, '', $cartQty);
        }

        return $passed;

    }

    public static function onCartUpdate( $cart_updated ) {
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        $cart_updated = true;
        $adminHelper = new AdminHelper();
        $current_user = $adminHelper->isLoggedIn();
        if( $current_user && $items ) {
            foreach ($items as $key => $item) {
                // $adminHelper->dd($item, true, true);
                $cartQty = $item['quantity'];
                $product_id = $item['product_id'];
                $cartItemKey = $item['key'];
                $cart_updated = self::validateItems($cartQty, $product_id, $cartItemKey);
                return $cart_updated;
            }
        }
    }

    public static function validateItems($qty, $productID, $cartItemKey = '', $cartQty = 0, $email = '')
    {
        $ticketNumbers = new TicketNumber;
        $adminHelper = new AdminHelper;

        $productData = wc_get_product( $productID );
        $maxQtyUser = get_post_meta($productID, '_maximum_ticket_per_user', true);
        $current_user = \wp_get_current_user();
        $user = ( $adminHelper->isLoggedIn() ) ? $current_user->ID : $email;
        $totalBought = $ticketNumbers->getTotalBoughtPerUser($productID, $user, $adminHelper->isLoggedIn());
        $totalBought = (int) $totalBought + (int) $cartQty;
        $remainingCredits = (int) $maxQtyUser - (int) $totalBought;
        $cart = WC()->cart;
        $cartUrl = "<a href='".WPDIGITALDRIVE_COMPETITIONS_SITEURL."/cart' class='btn btn-success'>Go back to cart?</a>";
        $cartHtml = $adminHelper->isLoggedIn() == false ? $cartUrl : '';
        if( $productData && $productData->get_type() == 'competition' ) {
            if ($totalBought < $maxQtyUser) {
                if( $remainingCredits == $maxQtyUser ) {
                    if( $qty > $maxQtyUser ) {
                        if( $cartItemKey ) {
                            $cart->cart_contents[ $cartItemKey ]['quantity'] = $maxQtyUser;
                        }
                        $message = "You can only bought at least {$maxQtyUser} ticket for this product: <strong> {$productData->name} </strong>. {$cartHtml}";
                        wc_add_notice(__($message, 'woocommerce'), 'error');
                        return false;
                    }
                    return true;
                } else if ( $qty > $remainingCredits ) {
                    if( $cartItemKey ) {
                        $cart->cart_contents[ $cartItemKey ]['quantity'] = $remainingCredits;
                    }
                    $message = "You have {$remainingCredits} remaining tickets for this product: <strong>{$productData->name}</strong>. {$cartHtml}";
                    wc_add_notice(__($message, 'woocommerce'), 'error');
                    return false;
                } else {
                    return true;
                }
            } else {
                $message = "You have reached the maximum ticket for this product: <strong>{$productData->name}</strong>. {$cartHtml}";
                wc_add_notice( __($message, 'woocommerce' ), 'error' );
                return false;
            }
        } else {
            return true;
        }
    }

    public static function getCartItems($productID = null)
    {
        $qty = 0;
        foreach (WC()->cart->get_cart() as $cart_item) {
            $product_in_cart = $cart_item['product_id'];
            if ($product_in_cart === $productID) {
                $qty = (int) $qty + (int) $cart_item['quantity'];
            }
        }

        return $qty;
    }

    public static function addCartItemData ( $cartItemData, $productId, $variationId ) {
        $answer = $_POST['competition_answer'];
        //$email = $_POST['competition_email'];
        $cartItemData['_my_competition_answer'] = $answer;
        //$cartItemData['_competition_guest_email'] = $email;
        return $cartItemData;
    }

    public static function getCartItemFromSession( $cartItemData, $cartItemSessionData, $cartItemKey ) {
        if ( isset( $cartItemSessionData['_my_competition_answer'] ) ) {
            $cartItemData['_my_competition_answer'] = $cartItemSessionData['_my_competition_answer'];
        }

        // if ( isset( $cartItemSessionData['_competition_guest_email'] ) ) {
        //     $cartItemData['_competition_guest_email'] = $cartItemSessionData['_competition_guest_email'];
        // }

        // self::$guestEmail = $cartItemSessionData['_competition_guest_email'];

        return $cartItemData;
    }

    public static function getItemData( $data, $cartItem ) {
        if ( isset( $cartItem['_my_competition_answer'] ) ) {
            $data[] = array(
                'name' => '<strong>Answer</strong>',
                'value' => $cartItem['_my_competition_answer']
            );
        }

        return $data;
    }

    public static function addOrderItemMeta( $itemId, $values, $key ) {
        if ( isset( $values['_my_competition_answer'] ) ) {
            wc_add_order_item_meta( $itemId, '_my_competition_answer', $values['_my_competition_answer'] );
        }
    }

    public static function filterWcOrderItemDisplayMetaKey( $display_key, $meta, $item ) {
        // Change displayed label for specific order item meta key
        if( is_admin() && $item->get_type() === 'line_item' && $meta->key === '_my_competition_answer' ) {
            $display_key = __("Answer", "woocommerce" );
        }
        return $display_key;
    }

    public static function setBillingFieldsReadOnly($fields)
    {
        if( self::$guestEmail ) {
            $fields['billing']['billing_email']['custom_attributes'] = array('readonly'=>'readonly');
            $fields['billing']['billing_email']['default'] = self::$guestEmail;
        }

        return $fields;
    }

    public static function guestValidation($fields, $errors)
    {
        $adminHelper = new AdminHelper();
        $cartItems = WC()->cart->get_cart();
        if( $fields['billing_email'] != '') {
            foreach ( $cartItems as $key => $cartItem) {
                $cartQty = $cartItem['quantity'];
                $product_id = $cartItem['product_id'];
                $cartItemKey = $cartItem['key'];
                $status = self::validateItems($cartQty, $product_id, '', '', $fields['billing_email'] );
                return $status;
            }
        } else {
            $errors->add( 'validation', 'Email is required!' );
        }
    }
}
