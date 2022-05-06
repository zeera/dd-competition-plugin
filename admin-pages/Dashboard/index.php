<?php

/** This is just so intelliphene knows what $this relates to.
 *  @var WpDigitalDriveCompetitions\AdminPages\Dashboard\Controller $this
 * */

/**
 * This is a default ajax search function that relies upon the .js file
 * It is quite flexible, but please dig into the function for more info.
 * The css for the table does rely upon bootstrap classes, so if you are not using bootstrap you will need to set the bootstrap
 * css classes in question to get it looking right
 *
 * This will also add the appropriate inline js and the normal required js include file for this.
 */
$this->addAjaxSearch([
    'id' => 'ajaxsearch',
    'destination' => admin_url('admin-ajax.php') . '?action=' . WPDIGITALDRIVE_COMPETITIONS_AJAX_PREFIX . 'adminAjax',
    'search_fields' => ['namebox' => 'namebox'],

]);

$box1options = $this->getOption('search_options');
?>


<div class="row">
	<div class="col-md-4">
		<div class="card card-border">
			<div class="card-header">
				<h4 class="card-title">Search</h4>
			</div>
			<div class="card-body">
				<?php $this->inputSearchField('namebox', ['options' => $box1options,'value' => 'email']); ?>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12 memberdisplaybox">
		<div class="card card-border">
			<div class="card-header">
				<h4 class="card-title">Results</h4>
			</div>
			<div class="card-body" style="overflow: auto">
				<div class="row">
					<div class="col-md-12">
						<div id="filterlist">&nbsp;</div>
					</div>
				</div>
				<?php $this->createTable([], $this->getOption('columns')); ?>
				<?php $this->addAjaxExport('ajaxsearch'); ?>
				<br />
			</div>
		</div>
	</div>
</div>

<script>
    jQuery(function($) {
        $('body').on('click', '.showDetails', function() {
            var data = $(this).data('price-match');
            var couponUrl = $(this).data('coupon-url');
            var dataHtml = `
            <div class="card" data-user-data=${data}>
                <ul class="list-group list-group-flush parentData" data-row-id=${data.id}>
                    <li class="list-group-item"><p><strong>Product:</strong> ${data.name}</p></li>
                    <li class="list-group-item"><p><strong>Product Price:</strong> ${data.price}</p></li>
                    <li class="list-group-item"><p><strong>Product Url:</strong> ${data.product_url}</p></li>
                    <li class="list-group-item"><p><strong>Competitor Price:</strong> ${data.cPrice}</p></li>
                    <li class="list-group-item"><p><strong>Competitor Product URL: </strong> ${data.competitor_price_url}</p></li>
                    <li class="list-group-item"><p><strong>Message: </strong> ${data.message}</p></li>
                </ul>
            </div>
            <div class="card ${ data.coupon == null ? 'd-none' : ''}">
                <div class="card-header">
                    Coupon Details
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><p><strong>Coupon:</strong> <a class="coupon-link" href=${couponUrl} target="_blank">${data.coupon}</a></p></li>
                    <li class="list-group-item"><p><strong>Discount in Percentage:</strong> ${data.discount}%</p></li>
                </ul>
            </div>
        `;

            $("#priceMatchingModalAdmin").find('.modal-body-content').html(dataHtml);
            if (data.status == 'approved' || data.status == 'completed') {
                $("#priceMatchingModalAdmin").find('.modal-footer').removeClass('d-none');
                $("#priceMatchingModalAdmin").find('.status-buttons').addClass('d-none');
                $("#priceMatchingModalAdmin").find('.coupon-buttons').removeClass('d-none');
            } else if (data.status == 'declined') {
                $("#priceMatchingModalAdmin").find('.modal-footer').addClass('d-none');
            } else {
                $("#priceMatchingModalAdmin").find('.modal-footer').removeClass('d-none');
                $("#priceMatchingModalAdmin").find('.status-buttons').removeClass('d-none');
                $("#priceMatchingModalAdmin").find('.coupon-buttons').addClass('d-none');
            }

            $(".generate-coupon").removeClass('mp-disabled');
            $(".send-to-email").removeClass('mp-disabled');

            if (data.coupon != null) {
                $(".generate-coupon").addClass('mp-disabled');
            }
            if (data.email_sent == '1') {
                $(".send-to-email").addClass('mp-disabled');
            }
            $("#priceMatchingModalAdmin").modal('show');
        });
        $('body').on('click', '.updatePriceMatch', function() {
            var value = $(this).data('value');
            var id = $(this).closest('.modal').find('.parentData').data('row-id');
            var baseUrl = $('#priceMatchingModalAdmin').data('site-url');
            var apiUrl = `${baseUrl}wp/v2/update-price-match`;
            var generateCoupon = ($(this).hasClass('generate-coupon')) ? true : false;
            var discount = $(this).data('discout-percentage');
            var dataArr = [];
            if ($(this).hasClass('generate-coupon')) {
                var arr = {
                    'id': id,
                    'generateCoupon': generateCoupon,
                    'discount': discount,
                };
                dataArr.push(arr);
            } else if ($(this).hasClass('send-to-email')) {
                var arr = {
                    'sendToEmail': true,
                    'status': 'completed',
                    'id': id,
                };
                dataArr.push(arr);
            } else {
                var arr = {
                    'updatesStatus': true,
                    'status': value,
                    'id': id,
                };
                dataArr.push(arr);
            }
            $.ajax({
                url: apiUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    form_data: dataArr,
                },
                beforeSend: function(data) {
                    $('.mploader').removeClass('d-none');
                    $('.updatePriceMatch').prop('disabled', true).css('font-size', '0');
                },
                success: function(data, xhr) {
                    $('.mploader').addClass('d-none');
                    $('.updatePriceMatch').prop('disabled', false).css('font-size', 'initial');
                    var errors = data.error !== undefined ? data.error : '';
                    if (xhr == 'success') {
                        if (errors) {
                            $('body').find('#matchPriceAlert')
                                .text(errors.message)
                                .addClass('alert-danger')
                                .removeClass('d-none');
                        }
                        if (data && !errors) {
                            $('body').find('#matchPriceAlert')
                                .text(`Success`)
                                .addClass('alert-success')
                                .removeClass('d-none alert-danger');
                            setTimeout(() => {
                                $('#priceMatchingModalAdmin').modal('toggle');
                            }, 2500);
                            location.reload();
                        }
                    }
                },
                error: function(data) {
                    // alert('error');
                }
            });
        })
    });
</script>
