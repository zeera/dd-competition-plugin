<div class="row">
    <div class="col-12">
        <div class="card p-0 mw-100">
            <div class="card-header bg-light">
                <h4 class="mb-0 fs-5 fw-bold text-dark"><?php esc_attr_e('Shortcodes', 'WpAdminStyle'); ?></h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table
                        data-ppp-options="<?php echo get_option('data_per_page_options') ? get_option('data_per_page_options') : ''; ?>"
                        data-ppp="<?php echo get_option('data_per_page') ? get_option('data_per_page') : ''; ?>"
                        id="cashSaleIndexTable"
                        class="table table-striped cashSaleIndexTable" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th>Shortcode</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($this->shortcodes): ?>
                                <?php foreach($this->shortcodes as $key => $tmpData): ?>
                                    <tr>
                                        <td><?php esc_attr_e($tmpData['code'], 'WpAdminStyle'); ?></td>
                                        <td><?php esc_attr_e( $tmpData['document'], 'WpAdminStyle' ); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th>Shortcode</th>
                                <th>Description</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
