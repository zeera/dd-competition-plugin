<div class="row">
    <div class="col-12">
        <div class="card p-0 mw-100">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-12 col-md-4">
                        <?php
                            $product_name  = $this->product_data->name;
                            $product_slug  = $this->product_data->slug;
                        ?>
                        <?php esc_attr_e('Entry Lists ('. $product_name .')', 'WpAdminStyle'); ?>
                    </div>
                    <div class="col-12 col-md-8 text-end">
                        <a
                            href="javascript:;"
                            data-site-url="<?= get_rest_url() ?>"
                            data-product-id="<?= $this->product_data->id; ?>"
                            class="btn btn-dark fw-bold export">
                            Export to csv
                            <svg class="csv-icon d-inline" width="16" height="16" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M224 0V128C224 145.7 238.3 160 256 160H384V448C384 483.3 355.3 512 320 512H64C28.65 512 0 483.3 0 448V64C0 28.65 28.65 0 64 0H224zM80 224C57.91 224 40 241.9 40 264V344C40 366.1 57.91 384 80 384H96C118.1 384 136 366.1 136 344V336C136 327.2 128.8 320 120 320C111.2 320 104 327.2 104 336V344C104 348.4 100.4 352 96 352H80C75.58 352 72 348.4 72 344V264C72 259.6 75.58 256 80 256H96C100.4 256 104 259.6 104 264V272C104 280.8 111.2 288 120 288C128.8 288 136 280.8 136 272V264C136 241.9 118.1 224 96 224H80zM175.4 310.6L200.8 325.1C205.2 327.7 208 332.5 208 337.6C208 345.6 201.6 352 193.6 352H168C159.2 352 152 359.2 152 368C152 376.8 159.2 384 168 384H193.6C219.2 384 240 363.2 240 337.6C240 320.1 231.1 305.6 216.6 297.4L191.2 282.9C186.8 280.3 184 275.5 184 270.4C184 262.4 190.4 256 198.4 256H216C224.8 256 232 248.8 232 240C232 231.2 224.8 224 216 224H198.4C172.8 224 152 244.8 152 270.4C152 287 160.9 302.4 175.4 310.6zM280 240C280 231.2 272.8 224 264 224C255.2 224 248 231.2 248 240V271.6C248 306.3 258.3 340.3 277.6 369.2L282.7 376.9C285.7 381.3 290.6 384 296 384C301.4 384 306.3 381.3 309.3 376.9L314.4 369.2C333.7 340.3 344 306.3 344 271.6V240C344 231.2 336.8 224 328 224C319.2 224 312 231.2 312 240V271.6C312 294.6 306.5 317.2 296 337.5C285.5 317.2 280 294.6 280 271.6V240zM256 0L384 128H256V0z"/></svg>
                            <svg class="loader d-none d-inline" fill="currentColor" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: none; display: block; shape-rendering: auto;" width="30px" height="30px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                                <g transform="rotate(0 50 50)">
                                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="currentColor">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="0.9615384615384615s" begin="-0.8814102564102564s" repeatCount="indefinite"></animate>
                                    </rect>
                                </g>
                                <g transform="rotate(30 50 50)">
                                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="currentColor">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="0.9615384615384615s" begin="-0.8012820512820512s" repeatCount="indefinite"></animate>
                                    </rect>
                                </g>
                                <g transform="rotate(60 50 50)">
                                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="currentColor">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="0.9615384615384615s" begin="-0.7211538461538461s" repeatCount="indefinite"></animate>
                                    </rect>
                                </g>
                                <g transform="rotate(90 50 50)">
                                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="currentColor">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="0.9615384615384615s" begin="-0.641025641025641s" repeatCount="indefinite"></animate>
                                    </rect>
                                </g>
                                <g transform="rotate(120 50 50)">
                                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="currentColor">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="0.9615384615384615s" begin="-0.5608974358974359s" repeatCount="indefinite"></animate>
                                    </rect>
                                </g>
                                <g transform="rotate(150 50 50)">
                                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="currentColor">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="0.9615384615384615s" begin="-0.4807692307692307s" repeatCount="indefinite"></animate>
                                    </rect>
                                </g>
                                <g transform="rotate(180 50 50)">
                                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="currentColor">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="0.9615384615384615s" begin="-0.4006410256410256s" repeatCount="indefinite"></animate>
                                    </rect>
                                </g>
                                <g transform="rotate(210 50 50)">
                                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="currentColor">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="0.9615384615384615s" begin="-0.3205128205128205s" repeatCount="indefinite"></animate>
                                    </rect>
                                </g>
                                <g transform="rotate(240 50 50)">
                                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="currentColor">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="0.9615384615384615s" begin="-0.24038461538461536s" repeatCount="indefinite"></animate>
                                    </rect>
                                </g>
                                <g transform="rotate(270 50 50)">
                                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="currentColor">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="0.9615384615384615s" begin="-0.16025641025641024s" repeatCount="indefinite"></animate>
                                    </rect>
                                </g>
                                <g transform="rotate(300 50 50)">
                                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="currentColor">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="0.9615384615384615s" begin="-0.08012820512820512s" repeatCount="indefinite"></animate>
                                    </rect>
                                </g>
                                <g transform="rotate(330 50 50)">
                                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="currentColor">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="0.9615384615384615s" begin="0s" repeatCount="indefinite"></animate>
                                    </rect>
                                </g>
                                <!-- [ldio] generated by https://loading.io/ -->
                            </svg>
                        </a>
                        <a
                            href="<?= WPDIGITALDRIVE_COMPETITIONS_SITEURL ?>/entry-list?competition_name=<?= $product_slug ?>"
                            target="_blank"
                            class="btn btn-success fw-bold">
                            View Entry List
                            <svg width="16" height="16" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M0 96C0 60.65 28.65 32 64 32H512C547.3 32 576 60.65 576 96V416C576 451.3 547.3 480 512 480H64C28.65 480 0 451.3 0 416V96zM160 256C160 238.3 145.7 224 128 224C110.3 224 96 238.3 96 256C96 273.7 110.3 288 128 288C145.7 288 160 273.7 160 256zM160 160C160 142.3 145.7 128 128 128C110.3 128 96 142.3 96 160C96 177.7 110.3 192 128 192C145.7 192 160 177.7 160 160zM160 352C160 334.3 145.7 320 128 320C110.3 320 96 334.3 96 352C96 369.7 110.3 384 128 384C145.7 384 160 369.7 160 352zM224 136C210.7 136 200 146.7 200 160C200 173.3 210.7 184 224 184H448C461.3 184 472 173.3 472 160C472 146.7 461.3 136 448 136H224zM224 232C210.7 232 200 242.7 200 256C200 269.3 210.7 280 224 280H448C461.3 280 472 269.3 472 256C472 242.7 461.3 232 448 232H224zM224 328C210.7 328 200 338.7 200 352C200 365.3 210.7 376 224 376H448C461.3 376 472 365.3 472 352C472 338.7 461.3 328 448 328H224z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table
                        data-ppp-options="<?php echo get_option('data_per_page_options') ? get_option('data_per_page_options') : ''; ?>"
                        data-ppp="<?php echo get_option('data_per_page') ? get_option('data_per_page') : ''; ?>"
                        id="cashSaleIndexTable"
                        class="table table-striped cashSaleIndexTable"
                        style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th class="row-title text-center"><?php esc_attr_e( 'Ticket No.', 'WpAdminStyle' ); ?></th>
                                <th class="text-center"><?php esc_attr_e( 'Name', 'WpAdminStyle' ); ?></th>
                                <th class="text-center"><?php esc_attr_e( 'Order ID', 'WpAdminStyle' ); ?></th>
                                <th class="text-center"><?php esc_attr_e( 'Order', 'WpAdminStyle' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($this->ticketNumbers): ?>
                                <?php foreach($this->ticketNumbers as $key => $tmpData): ?>
                                    <?php
                                        $full_name = $tmpData['full_name'];
                                    ?>
                                    <tr valign="top">
                                        <td scope="row" class="text-center" width=20%>
                                            <label for="tablecell">
                                                <?php ($tmpData['ticket_number']) == 0 ? '--' : esc_attr_e($tmpData['ticket_number'], 'WpAdminStyle'); ?>
                                            </label>
                                        </td>
                                        <td class="text-center"><?php esc_attr_e( $full_name, 'WpAdminStyle' ); ?></td>
                                        <?php
                                            $prefix = 'ON';
                                            $suffix = 'F';
                                            $new_order_id = $prefix . $tmpData['order_id'] . $suffix;
                                            $orderID =  $tmpData['cash_sale'] == 1 ? $tmpData['order_id'] : $new_order_id ;
                                        ?>
                                        <td class="text-center"><?php esc_attr_e( $orderID, 'WpAdminStyle' ); ?></td>
                                        <td class="text-center" width=10%>
                                            <?php
                                                if( $tmpData['cash_sale'] == 1 ) {
                                                    $url = admin_url('admin.php?page=' . WPDIGITALDRIVE_COMPETITIONS_NAMESPACE . '_cash_sales&id='.$tmpData['id']);
                                                } else {
                                                    $url = admin_url('post.php?post=' .$tmpData['order_id'].'&action=edit');
                                                }
                                            ?>
                                            <a href="<?= $url; ?>"  class="btn btn-primary" title="View Order">
                                                View Order
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th class="row-title text-center"><?php esc_attr_e( 'Ticket No.', 'WpAdminStyle' ); ?></th>
                                <th class="text-center"><?php esc_attr_e( 'Name', 'WpAdminStyle' ); ?></th>
                                <th class="text-center"><?php esc_attr_e( 'Order ID', 'WpAdminStyle' ); ?></th>
                                <th class="text-center"><?php esc_attr_e( 'Order', 'WpAdminStyle' ); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function($) {
        $('body').on('click', '.generateTicketNumber', function(e) {
            e.preventDefault();
            var baseUrl = $(this).data('site-url');
            var clear = '';
            if( $(this).hasClass('clear') ) {
                var clear = true;
            } else {
                var clear = false;
            }
            var apiUrl = `${baseUrl}wp/v2/generate-ticket-number`;
            var productID = $(this).data('product-id');
            $.ajax({
                url: apiUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    product_id: productID,
                    clear: clear,
                },
                beforeSend: function(data) {
                    $('body').find('.ticket-icon').addClass('d-none');
                    $('body').find('.loader').removeClass('d-none');
                },
                success: function(data, xhr) {
                    $('body').find('.ticket-icon').removeClass('d-none');
                    $('body').find('.loader').addClass('d-none');
                    if (xhr == 'success') {
                    }
                    location.reload();
                    return false;
                },
                error: function(data) {
                    // alert('error');
                }
            });
        });
        $('body').on('click', '.export', function() {
            var baseUrl = $(this).data('site-url');
            var apiUrl = `${baseUrl}wp/v2/export`;
            var productID = $(this).data('product-id');
            $.ajax({
                url: apiUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    product_id: productID,
                },
                beforeSend: function(data) {
                    $('.export').find('.csv-icon').addClass('d-none');
                    $('.export').find('.loader').removeClass('d-none');
                },
                success: function(data, xhr) {
                    $('.export').find('.csv-icon').removeClass('d-none');
                    $('.export').find('.loader').addClass('d-none');
                    /*
                    * Make CSV downloadable
                    */
                    var downloadLink = document.createElement("a");
                    var fileData = ['\ufeff'+data];

                    var blobObject = new Blob(fileData,{
                        type: "text/csv;charset=utf-8;"
                    });

                    var url = URL.createObjectURL(blobObject);
                    downloadLink.href = url;
                    var nowDate		= new Date();
                    var nowDay		= ((nowDate.getDate().toString().length) == 1) ? '0'+(nowDate.getDate()) : (nowDate.getDate());
                    var nowMonth	= ((nowDate.getMonth().toString().length) == 1) ? '0'+(nowDate.getMonth()+1) : (nowDate.getMonth()+1);
                    var nowYear		= nowDate.getFullYear();
                    var formatDate	= nowMonth + "-" + nowDay + "-" + nowYear;
                    downloadLink.download = `entry-list-${formatDate}.csv`;

                    /*
                    * Actually download CSV
                    */
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);
                },
                error: function(data) {
                    // alert('error');
                }
            });
        });
    });
</script>
