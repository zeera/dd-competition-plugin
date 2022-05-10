<form method="POST" action="options.php">
    <?php settings_errors(); ?>
    <?php settings_fields('digital_drive_competition_settings'); ?>
    <h2 class="nav-tab-wrapper">
        <a href="#general_settings" class="nav-tab" data-target="#general_settings">General Settings</a>
        <a href="#" class="nav-tab" data-target="#frontend_styles">FrontEnd Styles</a>
    </h2>
    <div id="general_settings" class="tab-content general-settings-wrapper">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-border p-0 mw-100">
                    <div class="card-header">
                        <h4 class="card-title">General Settings</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="buttonText">Maximum Ticket Default Value</label>
                            <input class="form-control" id="buttonText" type="number" name="maximum_ticket_default_value" value="<?php echo get_option('maximum_ticket_default_value') ? get_option('maximum_ticket_default_value') : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="buttonText">Default Basket Quantity</label>
                            <input class="form-control" id="buttonText" type="number" name="default_basket_quantity" value="<?php echo get_option('default_basket_quantity') ? get_option('default_basket_quantity') : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="buttonText">Maximum Ticket Default Per User</label>
                            <input class="form-control" id="buttonText" type="number" name="maximum_ticket_default_per_user" data-default-color="#81d742" value="<?php echo get_option('maximum_ticket_default_per_user') ? get_option('maximum_ticket_default_per_user') : ''; ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="frontend_styles" class="tab-content frontend-styles-wrapper">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-border p-0 mw-100">
                    <div class="card-header">
                        <h4 class="card-title">FrontEnd Styles</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="textColor">Text Color</label>
                            <input class="colorField form-control" id="textColor" type="text" name="text_color" data-default-color="#81d742" value="<?php echo get_option('text_color') ? get_option('text_color') : '#81d742'; ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?php submit_button(); ?>
        </div>
    </div>
</form>
<style>
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
</style>
<script>
    jQuery(function($) {
        $(window).on('load', function() {
            $('.nav-tab').first().click();
        });

        $('body').on('click', '.nav-tab', function() {
            $('.tab-content').hide();
            $('.nav-tab').removeClass('nav-tab-active');
            var target = $(this).data('target');
            if( $(this).hasClass('nav-tab-active') ) {
                $(this).removeClass('nav-tab-active');
                $(`${target}`).hide();
            } else {
                $(this).addClass('nav-tab-active');
                $(`${target}`).show();
            }
        });
    });
</script>
