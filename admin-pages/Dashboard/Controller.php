<?php
/**
 * Controller for the dashboard
 */

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\AdminPages\Dashboard;
use WpDigitalDriveCompetitions\Helpers\AdminHelper;
use WpDigitalDriveCompetitions\Hooks\PriceMatchProcess;

/**
 * Controller for the dashboard
 */
class Controller extends AdminHelper
{
    /** Controller */
    protected string $controller = 'dashboard';
    protected $product = [];
    protected $modelData = [];

    /**
     * Index
     */
    public function actionIndex()
    {

        /** @var \WpDigitalDriveCompetitions\Models\PriceMatch $model */
        $model = $this->loadModel('PriceMatch', '\WpDigitalDriveCompetitions\Models\PriceMatch');
        $model->search();

        $this->buildPage(dirname(__FILE__) . '/index.php');
    }

    /**
     * View
     */
    public function actionView($id = null)
    {
        $id = $id ?: $this->getValue('id');

        /** @var \WpDigitalDriveCompetitions\Models\PriceMatch $model */
        $model = $this->loadModel('PriceMatch', '\WpDigitalDriveCompetitions\Models\PriceMatch');
        $priceMatchProcess = new PriceMatchProcess;
        $mergeErr = [];
        if ($id != null) {
            $this->modelData = $model->loadId($id);
            $postValues = $this->postValue($this->default_modelname);
            $this->product = $model->getProductData($this->modelData['product_id']);
            if(
                $this->modelData["variation_id"] != null &&
                $this->modelData["variation_id"] != '' &&
                $this->modelData["variation_id"] != "0"
            ) {
                $this->product['variation'] = new \WC_Product_Variation($this->modelData['variation_id']);
            }
            if( !$this->isAdmin() ) {
                $result = false;
                $mergeErr = [
                    'access_denied' => 'Coupon is already set!'
                ];
            }
            if($postValues != null) {
                if( $postValues['generate_coupon'] && $this->modelData['status'] == 'approved') {
                    if( $this->modelData['coupon'] == NULL ) {
                        $result = $priceMatchProcess->generateCoupon($postValues);
                    } else {
                        $result = false;
                        $mergeErr = [
                            'generate_coupon' => 'Coupon is already set!'
                        ];
                    }
                }

                if( $postValues['send_email'] && $this->modelData['status'] == 'approved') {
                    if( $this->modelData['coupon'] != NULL ) {
                        $match_price = (float) $this->modelData['competitor_price'];
                        $product_price = (float) $this->product[0]['price'];
                        $emailArgs = [
                            'code' => $this->modelData['coupon'],
                            'email' => $this->modelData['email'],
                            'subject' => 'Discount Code',
                            'amount' => $this->modelData['discount'],
                            'status' => 'completed',
                            'additional' => '<h3><b>For Product ' . $this->product[0]['post_title'] . '</b><br />Requested Price Match of $' . number_format($match_price, 2) . ' from $' . number_format($product_price, 2) .'</h3>'
                        ];
                        $sendEmail = $priceMatchProcess->setEmail($emailArgs);
                        if( $sendEmail ) {
                            $postValues['status'] = 'completed';
                            $postValues['email_sent'] = 1;
                            $result = $model->update($id, $postValues);
                        }

                        if( get_option('priceMatchEmailNotification') ) {
                            $emailArgs = [
                                'code' => $this->modelData['coupon'],
                                'user_email' => $this->modelData['email'],
                                'user_full_name' => $this->modelData['name'],
                                'email' => get_option('priceMatchEmailNotification'),
                                'subject' => 'New Price Match Request',
                                'amount' => $this->modelData['discount'],
                                'status' => 'email-admin-notif',
                                'additional' => $emailArgs['additional']
                            ];
                            $adminEmailNotif = $priceMatchProcess->setEmail($emailArgs);
                        }
                    }
                }

                if (
                        $postValues['status'] == 'declined'  &&
                        $this->modelData['status'] == 'approved' &&
                        $this->modelData['coupon'] != NULL
                    ) {
                    $coupon = $priceMatchProcess->terminateCoupon($postValues);
                    if ($coupon) {
                        $result = $model->update($id, $postValues);
                    }
                }

                if(
                    !isset($postValues['generate_coupon']) &&
                    $this->modelData['status'] == 'declined' ||
                    $this->modelData['status'] == 'completed'
                ) {
                    $result = false;
                    $mergeErr = [
                        'status_change' => 'Request is already been declined/completed. Action not permitted!'
                    ];
                }

                if( !isset($postValues['generate_coupon']) && ($this->modelData['status'] == 'pending' || $this->modelData['status'] == 'approved') ) {
                    $result = $model->update($id, $postValues);
                }

                if ($result == true) {
                    $this->alerts['successes']['Updated'] = 'The record was successfully updated!';
                } else {
                    $this->alerts['errors'] = array_merge($this->alerts['errors'], $model->errors, $mergeErr);
                }
            }
            $this->modelData = $model->loadId($id);
        }

        $this->buildPage(dirname(__FILE__) . '/view.php');
    }

    /**
     * get coupon
     */
    public function getCoupon( $coupon )
    {
        if ( class_exists('WC_Coupon') ) {
            $couponData = new \WC_Coupon($coupon);
            return $couponData;
        }
    }

    public function isCouponExpired( $couponCode )
    {
        $coupon = $this->getCoupon($couponCode);
        if( $coupon ) {
            $expiryDate  = $coupon->get_date_expires();
            $expiryDate  = $expiryDate->date('Y-m-d');
            $dateNow     = date('Y-m-d');
            if( $expiryDate < $dateNow ) {
                return true;
            } else {
                return false;
            }
        }
    }
}
