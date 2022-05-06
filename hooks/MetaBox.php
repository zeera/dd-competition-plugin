<?php
/**
 * =====================================
 * Competition Fields
 * =====================================
 * File Description
 * =====================================
 */

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Hooks;

use WpDigitalDriveCompetitions\Helpers\AdminHelper;

// Sample Data
// $metaArgs = array(
//     array(
//         'id' => 'dd_competition_field_email',
//         'title' => 'Email',
//         'post_type' => 'product',
//         'priority' => 'high',
//         'args' => array(
//             'desc' => 'Enter Email',
//             'field' => 'textfield',
//         )
//     ),
//     array(
//         'id' => 'dd_competition_field_checkbox',
//         'title' => 'Sample Checkbox',
//         'post_type' => 'product',
//         'context' => 'side',
//         'priority' => 'high',
//         'args' => array(
//             'desc' => 'checkbox description',
//             'field' => 'checkbox',
//             'readonly' => true,
//         )
//     ),
// );

class MetaBox extends AdminHelper
{
    private $boxes;

    public function __construct( $args )
    {
        $this->boxes = $args;
        add_action( 'plugins_loaded', [ $this, 'initAddMetaBox' ] );
    }

    public function initAddMetaBox()
    {
        add_action( 'add_meta_boxes',[$this, 'setMetaBox'] );
        add_action( 'save_post', [$this, 'saveMetaBox'] );
    }

    public function setMetaBox()
    {
        $group = true;
        if( !$group ) {
            foreach( $this->boxes as $box ) {
                add_meta_box(
                    $box['id'],
                    $box['title'],
                    array( $this, 'metaBoxCallback' ),
                    $box['post_type'],
                    isset( $box['context'] ) ? $box['context'] : 'normal',
                    isset( $box['priority'] ) ? $box['priority'] : 'default',
                    $box['args']
                );
            }
        } else {
            add_meta_box(
                'competition_fields',
                'Competitions',
                array( $this, 'metaBoxCallbackGroup' ),
                'product',
            );
        }
    }

    public function metaBoxCallback( $post, $box )
    {
        wp_nonce_field('saveMetaBox','competiotion_meta_box_nonce');
        switch( $box['args']['field'] )
        {
            case 'textfield':
                $this->textfield( $box, $post->ID );
            break;
            case 'checkbox':
                $this->checkbox( $box, $post->ID );
            break;
        }
    }

    public function metaBoxCallbackGroup($post)
    {
        wp_nonce_field('saveMetaBox','competiotion_meta_box_nonce');
        foreach( $this->boxes as $box ) {
            switch( $box['args']['field'] )
            {
                case 'textfield':
                    $this->textfield( $box, $post->ID );
                break;
                case 'checkbox':
                    $this->checkbox( $box, $post->ID );
                break;
            }
        }
    }

    private function textfield( $box, $post_id )
    {
        $post_meta = get_post_meta( $post_id, $box['id'], true );
        ?>
            <fieldset>
                <label for=<?php echo $box['id']; ?>">
                    <?php _e( $box['title'], '_namespace' ); ?>
                </label>
                <input
                    id="<?php echo $box['id']; ?>"
                    name="<?php echo $box['id']; ?>"
                    type="text"
                    value="<?php echo $post_meta ? $post_meta : ''; ?>"
                    id="<?php echo $box['id']; ?>"
                    class="large-text"
                    <?php echo isset($box['args']['readonly']) ? 'disabled' : ''; ?>/>
                <small><?php echo isset( $box['args']['desc'] ) ? $box['args']['desc'] : ''; ?></small>
            </fieldset> <br>
        <?php
    }

    private function checkbox( $box, $post_id )
    {
        $post_meta = get_post_meta( $post_id, $box['id'], true );
        ?>
            <fieldset>
                <label for=<?php echo $box['id']; ?>">
                    <?php _e( $box['title'], '_namespace' ); ?>
                </label>
                <input
                    name="<?php echo $box['id']; ?>"
                    type="checkbox"
                    id="<?php echo $box['id']; ?>"
                    value="1"
                    <?php echo ( $post_meta == 1 || $post_meta == '1') ? 'checked' : ''; ?>
                    <?php echo isset($box['args']['readonly']) ? 'readonly' : ''; ?>/>
                <small><?php echo isset( $box['args']['desc'] ) ? $box['args']['desc'] : ''; ?></small>
            </fieldset>
        <?php
    }

    public function saveMetaBox($post_id)
    {
        // if( !current_user_can("edit_post", $post_id) ) {
        //     return $post_id;
        // }

        // if( defined("DOING_AUTOSAVE") && DOING_AUTOSAVE ) {
        //     return $post_id;
        // }
        foreach ($this->boxes as $box) {
            if( isset( $_POST[$box['id']] ) ) {
                $fieldID = $box['args']['field'] != 'checkbox' ? sanitize_text_field( $_POST[$box['id']] ) : $_POST[$box['id']];
                update_post_meta($post_id, $box['id'], $fieldID);
            } else {
                if(
                    $box['id'] != 'match_price_lifestyle_product_url_meta_box' &&
                    $box['id'] != 'match_price_request_more_discount_meta_box' )
                {
                    delete_post_meta( $post_id, $box['id'] );
                }
            }
        }
    }
}
