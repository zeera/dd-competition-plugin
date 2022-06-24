<form method="POST" action="options.php">
    <?php settings_errors(); ?>
    <?php settings_fields('digital_drive_competition_settings'); ?>
    <h2 class="nav-tab-wrapper">
        <a href="#general_settings" class="nav-tab nav-tab-active" data-target="#general_settings">General Settings</a>
        <a href="#" class="nav-tab" data-target="#frontend_styles">Front End Styles</a>
    </h2>
    <div id="general_settings" class="tab-content general-settings-wrapper" style="display: block;">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-border p-0 mw-100 h-100">
                    <div class="card-header bg-dark">
                        <h4 class="card-title mb-0 fs-5 fw-bold text-white">General Settings</h4>
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
            <div class="col-md-6">
                <div class="card card-border p-0 mw-100 h-100">
                    <div class="card-header bg-dark">
                        <h4 class="card-title mb-0 fs-5 fw-bold text-white">Table Settings</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col">
                                <div class="form-group">
                                    <label for="buttonText">Data per page</label>
                                    <input
                                        class="form-control"
                                        id="buttonText"
                                        type="text"
                                        name="data_per_page"
                                        value="<?php echo get_option('data_per_page') ? get_option('data_per_page') : ''; ?>">
                                </div>
                            </div>
                            <div class="form-group col">
                                <div class="form-group">
                                    <label for="buttonText">Data per page options</label>
                                    <input
                                        class="form-control"
                                        id="buttonText"
                                        type="text"
                                        name="data_per_page_options"
                                        value="<?php echo get_option('data_per_page_options') ? get_option('data_per_page_options') : ''; ?>">
                                    <span class="small description">numbers separated by comma (eg: 5,10,15,20)</span class="small description">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="frontend_styles" class="tab-content frontend-styles-wrapper">
        <div class="row">
            <div class="col">
                <div class="card card-border p-0 mw-100">
                    <div class="card-header bg-dark">
                        <h4 class="card-title mb-0 fs-5 fw-bold text-white">Question and Answer Custom Styles</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col">
                                <label for="textColor">Text Color: </label>
                                <input class="colorField form-control" id="textColor" type="text" name="text_color" data-default-color="#81d742" value="<?php echo get_option('text_color') ? get_option('text_color') : '#81d742'; ?>">
                            </div>
                            <div class="form-group col">
                                <label for="textColor">Answer Background Color: </label>
                                <input class="colorField form-control" id="textColor" type="text" name="answerBgColor" data-default-color="#81d742" value="<?php echo get_option('answerBgColor') ? get_option('answerBgColor') : '#81d742'; ?>">
                            </div>
                            <div class="form-group col">
                                <label for="textColor">Selected Answer Background Color: </label>
                                <input class="colorField form-control" id="textColor" type="text" name="selectedAnswerBgColor" data-default-color="#81d742" value="<?php echo get_option('selectedAnswerBgColor') ? get_option('selectedAnswerBgColor') : '#81d742'; ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row align-items-center justify-content-end">
        <div class="col-12 text-right">
            <div class="card card-border p-0 mw-100 h-100 mt-5">
                <div class="card-body">
                    <input type="submit" name="submit" id="submit" class="btn btn-dark ms-auto me-0 d-block" value="Save Settings">
                </div>
            </div>
        </div>
    </div>
</form>
<style>
    .tab-content {
        display: none;
    }
</style>
<script>
    jQuery(function($) {
        $(window).on('load', function() {
            $('.nav-tab:first-child').trigger('click');
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
