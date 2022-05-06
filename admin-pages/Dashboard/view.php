<?php

/** This is just so intelliphene knows what $this relates to.
 *  @var WpDigitalDriveCompetitions\AdminPages\Dashboard\Controller $this
 * */
if ($this->modelData["coupon"] != NULL) {
    $expired = $this->isCouponExpired($this->modelData["coupon"]);
}
?>
<h1>Price Match Details</h1>
<form method="post">

    <div class="row">
        <div class='col-md-12'>
            <?php $this->inputHidden('id'); ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Price match Status</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-group list-group-flush parentData">
                                        <li class="list-group-item">
                                            <p class="mb-0"><strong>ID:</strong>&nbsp;<?= $this->get("id"); ?></p>
                                        </li>
                                        <li class="list-group-item">
                                            <p class="mb-0"><strong>Name:</strong>&nbsp;<?= $this->get("name"); ?></p>
                                        </li>
                                        <li class="list-group-item">
                                            <p class="mb-0"><strong>Email:</strong>&nbsp;<?= $this->get("email"); ?></p>
                                        </li>
                                        <li class="list-group-item">
                                            <p class="mb-0"><strong>Last Updated:</strong>&nbsp;<?= $this->changeYMD($this->get("last_updated"), 'd/m/Y', true); ?></p>
                                        </li>
                                        <li class="list-group-item">
                                            <p class="mb-0"><strong>Last Updated By:</strong>&nbsp;<?= $this->get("last_updated_by"); ?></p>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <?php
                                    $firsCreation = $this->modelData["date_created"] == $this->modelData["last_updated"] ? true : false;
                                    $statusArgs = [
                                        'label' => 'Status',
                                        'htmlclass' => 'form-select w-100 mw-100',
                                        'options' => $this->getOption('mp_status'),
                                        'disabled' => ($this->modelData["status"] == 'declined' || $this->modelData["status"] == 'completed' || ($this->modelData["status"] == 'pending' && $firsCreation != true && $this->modelData["coupon"] != NULL)) ? true : false
                                    ];
                                    ?>
                                    <?php $this->inputSelect('status', $statusArgs); ?>
                                    <small class="description small text-info <?= ($this->modelData["coupon"] == NULL || $expired == true) ? 'd-none' : ''; ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" height="16px" width="16px" fill="#0dcaf0">
                                            <path d="M256 0C114.6 0 0 114.6 0 256s114.6 256 256 256s256-114.6 256-256S397.4 0 256 0zM256 128c17.67 0 32 14.33 32 32c0 17.67-14.33 32-32 32S224 177.7 224 160C224 142.3 238.3 128 256 128zM296 384h-80C202.8 384 192 373.3 192 360s10.75-24 24-24h16v-64H224c-13.25 0-24-10.75-24-24S210.8 224 224 224h32c13.25 0 24 10.75 24 24v88h16c13.25 0 24 10.75 24 24S309.3 384 296 384z" />
                                        </svg>
                                        if set to decline, generated coupon will expire!
                                    </small>
                                    <?php if (($this->modelData["status"] == 'approved' || $this->modelData["status"] == 'completed') && $this->modelData["coupon"] == NULL) : ?>
                                        <div class="col-12 mt-3">
                                            <?php
                                            $isCoupon = $this->modelData["coupon"] != NULL ? "btn btn-success w-100 mw-10 disabled" : "btn btn-success w-100 mw-10";
                                            $htmlClasses = [
                                                'value' => 'Generate Coupon',
                                                'htmlclass' => $isCoupon,
                                            ];
                                            ?>
                                            <?php $this->inputSubmit('generate_coupon', $htmlClasses); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($this->modelData["coupon"] != NULL) : ?>
                                        <?php
                                        $coupon    = $this->getCoupon($this->modelData["coupon"]);
                                        $couponURl = WPDIGITALDRIVE_COMPETITIONS_SITEURL . "/wp-admin/post.php?post = " . $coupon->id . "&action = edit";
                                        $couponClass = '';
                                        if ($expired) {
                                            $couponClass .= 'text-decoration-line-through text-danger';
                                        }
                                        ?>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-0 <?= $couponClass; ?>"><strong>Coupon Code:</strong>&nbsp;<?= $coupon->code; ?><a target="_blank" class="btn btn-outline-info w-100" href="<?= $couponURl; ?>">View Coupon</a></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-0 <?= $couponClass; ?>"><strong>Coupon Amount:</strong>&nbsp;<?= $coupon->amount; ?>%</p>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($this->modelData["coupon"] != NULL && $expired == false) : ?>
                                        <div class="col-12 <?= ($this->modelData["coupon"] == NULL) ? 'col-md-4' : 'mt-3'; ?>">
                                            <?php
                                            $isSend = $this->modelData["email_sent"] == '1' ? true : false;
                                            $sendEmailArgs = [
                                                'value' => $isSend ? 'Resend Email' : 'Send Email',
                                                'htmlclass' => 'btn btn-info w-100 mw-100',
                                            ];
                                            ?>
                                            <?php $this->inputSubmit('send_email', $sendEmailArgs); ?>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Product Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <ul class="list-group list-group-flush parentData">
                                        <li class="list-group-item">
                                            <p class="mb-0"><strong>Product Name:</strong>&nbsp;<?= $this->product[0]['post_title']; ?></p>
                                        </li>
                                        <li class="list-group-item">
                                            <p class="mb-0"><strong>Product Price:</strong>&nbsp;<?= "\$" . number_format($this->product[0]['price'], 2); ?></p>
                                        </li>
                                        <?php
                                            if(
                                                $this->modelData["variation_id"] != NULL &&
                                                $this->modelData["variation_id"] != '' &&
                                                $this->modelData["variation_id"] != "0"
                                            ):
                                        ?>
                                            <li class="list-group-item">
                                                <p class="mb-0"><strong>Selected Variation:</strong>&nbsp;<?= $this->product['variation']->get_name(); ?></p>
                                            </li>
                                            <li class="list-group-item">
                                                <p class="mb-0"><strong>Variation Price:</strong>&nbsp;<?= "\$" . number_format($this->product['variation']->get_regular_price(), 2); ?></p>
                                            </li>
                                        <?php endif; ?>
                                        <li class="list-group-item">
                                            <p class="mb-0"><a target="_blank" class="btn btn-outline-info w-100" href="<?= the_permalink($this->product[0]['id']); ?>">View Product</a></p>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-12 col-md-6">
                                    <ul class="list-group list-group-flush parentData">
                                        <li class="list-group-item">
                                            <p class="mb-0"><strong>Discount Requested:</strong>&nbsp;<?= $this->modelData["discount"]; ?>%</p>
                                        </li>
                                        <li class="list-group-item">
                                            <p class="mb-0"><strong>Match Price Requested:</strong>&nbsp;<?= "\$" . number_format($this->modelData["competitor_price"], 2); ?></p>
                                        </li>
                                        <li class="list-group-item">
                                            <p class="mb-0"><a target="_blank" class="" href="<?= $this->get("competitor_price_url") ?>"><?= $this->get('competitor_price_url') ?></a></p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Client Message</h4>
                </div>
                <div class="card-body">
                    <?php $this->inputTextArea("message", ['disabled' => true, 'value' => $this->get("message")]); ?>
                </div>
            </div>
            <?php if( $this->isAdmin() ): ?>
                <div class="col-md-12 mt-4">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</form>
