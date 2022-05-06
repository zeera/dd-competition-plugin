<div class="row">
    <div class="col-12">
        <div class="card p-0 mw-100">
            <div class="card-header">
                <?php esc_attr_e('Shortcodes', 'WpAdminStyle'); ?>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th class="row-title"><?php esc_attr_e( 'Shortcode', 'WpAdminStyle' ); ?></th>
                            <th><?php esc_attr_e( 'Description', 'WpAdminStyle' ); ?></th>
                        </tr>
                    </thead>
                    <?php if($this->shortcodes): ?>
                        <?php foreach($this->shortcodes as $key => $tmpData): ?>
                            <tr valign="top">
                                <td scope="row">
                                    <label for="tablecell">
                                        <?php esc_attr_e($tmpData['code'], 'WpAdminStyle'); ?>
                                    </label>
                                </td>
                                <td><?php esc_attr_e( $tmpData['document'], 'WpAdminStyle' ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>
