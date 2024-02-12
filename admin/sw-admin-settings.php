<?php
/**
 * File name    :   sw-admin-settings.php
 * @author      :   Callistus
 * Description  :   settings page for admin submenu
 */

 function sw_options_dash_page() {
    echo '<div class="wrap">';
    
    echo '<h2>Smart Woo Settings and Documentations</h2>';

    echo '<div class="sw-container">';

    // Left column (Topics)
    echo '<div class="sw-left-column">';
    echo '<h3>Quick Guides</h3>';
    echo '<ul>';
    echo '<li><a href="#topic1">Topic 1</a></li>';
    echo '<li><a href="#topic2">Topic 2</a></li>';
    echo '<li><a href="#topic3">Topic 3</a></li>';
    // Add more topics as needed
    echo '</ul>';
    echo '</div>';

    // Right column (Instructions)
    echo '<div class="sw-right-column">';
    echo '<h3>Instructions</h3>';

    // Instruction for Topic 1
    echo '<div id="topic1" class="instruction">';
    echo '<p>Instructions for Topic 1 go here.</p>';
    echo '</div>';

    // Instruction for Topic 2
    echo '<div id="topic2" class="instruction">';
    echo '<p>Instructions for Topic 2 go here.</p>';
    echo '</div>';

    // Instruction for Topic 3
    echo '<div id="topic3" class="instruction">';
    echo '<p>Instructions for Topic 3 go here.</p>';
    echo '</div>';

    // Add more instructions as needed

    echo '</div>';

    echo '</div>';

    echo '</div>'; 

}



function sw_render_service_options_page() {
    sw_handle_options_submission();
    $site_name              = get_bloginfo( 'name' );
    $business_name          = get_option( 'sw_business_name', $site_name );
    $admin_phone_numbers    = get_option( 'sw_admin_phone_numbers', '' );
    $service_page           = get_option( 'sw_service_page', 0 );
    $upgrade_product_cat    = get_option( 'sw_upgrade_product_cat', '0' );
    $downgrade_product_cat  = get_option( 'sw_downgrade_product_cat', '0' );
    $product_categories     = get_terms( 'product_cat', array( 'hide_empty' => false ) ); // Get all product categories
    $pages                  = get_pages();
    $sw_prorate             = get_option( 'sw_prorate', 'Select option' );
    $migration_option       = get_option( 'sw_allow_migration', 'Disable' );
    $service_id_prefix      = get_option( 'sw_service_id_prefix', 'SID' );
    echo '<h1>Business Info</h1>';

    
    ?>
        <div class="wrap">
        <form method="post" class="inv-settings-form">

        <input type="submit" class="sw-blue-button" name="sw_save_options" value="Save Settings">
        
        <!-- Business Name -->
        <div class="sw-form-row">
        <label for="sw_business_name" class="sw-form-label">Business Name</label>
        <span class="sw-field-description" title="Enter your business name">?</span>
        <input type="text" name="sw_business_name" id="sw_business_name" value="<?php echo esc_attr( $business_name ); ?>" placeholder="Enter business name" class="sw-form-input">
        </div>

        <!--Business Phone -->
        <div class="sw-form-row">
        <label for="sw_admin_phone_numbers" class="sw-form-label">Phone Numbers</label>
        <span class="sw-field-description" title="Enter admin phone numbers separated by commas (e.g., +123456789, +987654321).">?</span>
        <input type="text" name="sw_admin_phone_numbers" id="sw_admin_phone_numbers" value="<?php echo esc_attr( $admin_phone_numbers ); ?>" placeholder="Enter business phone numbers" class="sw-form-input">
        </div>

        <!--Service Page -->
        <div class="sw-form-row">
        <label for="sw_service_page" class="sw-form-label">Service Page</label>
        <span class="sw-field-description" title="This page should have this shortcode [sw_service_page] ">?</span>
        <select name="sw_service_page" id="sw_service_page" class="sw-form-input">
        <option value="0">Select a service page</option>
        <?php
        foreach ($pages as $page) {
            $selected = ($service_page == $page->ID) ? 'selected' : '';
            echo '<option value="' . $page->ID . '" ' . $selected . '>' . $page->post_title . '</option>';
        }
        ?>
        </select>
        </div>

           <!-- Form field for service_id_prefix -->
        <div class="sw-form-row">
        <label for="sw_service_id_prefix" class="sw-form-label">Service ID Prefix</label>
        <span class="sw-field-description" title="Enter a text to prifix your service IDs">?</span>
        <input class="sw-form-input" type="text" name="sw_service_id_prefix" id="sw_service_id_prefix" value="<?php echo esc_attr( $service_id_prefix ); ?>" placeholder="eg, SMWSI">
        </div>
 

        <!-- Form field for service migration -->
        <div class="sw-form-row">
        <label for="sw_allow_migration" class="sw-form-label">Allow Service Migration</label>
        <span class="sw-field-description" title="Choose to allow users switch from their current service to another">?</span>
        <select name="sw_allow_migration" id="sw_allow_migration" class="sw-form-input">
        <option value="Enable" <?php  selected( 'Enable', $migration_option ); ?>>Yes</option>
        <option value="Disable" <?php  selected( 'Disable', $migration_option ); ?>>No</option>
        </select>
        </div>

        <!-- Service Upgrade Categories -->
        <div class="sw-form-row">
        <label for="sw_upgrade_product_cat" class="sw-form-label">Product Category for Upgrade</label>
        <span class="sw-field-description" title="Select the category of products to mark as products for service upgrades.">?</span>
        <select name="sw_upgrade_product_cat" class="sw-form-input" id="sw_upgrade_product_cat">
        <option value="0" <?php selected( '0', $upgrade_product_cat ); ?>>None</option>
        <?php
        foreach ($product_categories as $category) {
            $selected = ( $category->term_id == $upgrade_product_cat ) ? 'selected' : '';
            echo '<option value="' . $category->term_id . '" ' . $selected . '>' . $category->name . '</option>';
        }
        ?>
        </select>
        </div>

        <!-- Service Downdgrade Categories -->
        <div class="sw-form-row">
        <label for="sw_downgrade_product_cat" class="sw-form-label">Product Category for Downgrade</label>
        <span class="sw-field-description" title="Select the category of products to mark as products for service downgrades.">?</span>
        <select name="sw_downgrade_product_cat" class="sw-form-input" id="sw_downgrade_product_cat">
        <option value="0" <?php selected('0', $downgrade_product_cat); ?>>None</option>
        <?php
        foreach ( $product_categories as $category ) {
            $selected = ( $category->term_id == $downgrade_product_cat ) ? 'selected' : '';
            echo '<option value="' . $category->term_id . '" ' . $selected . '>' . $category->name . '</option>';
        }
        ?>
        </select>
        </div>

        <input type="submit" class="sw-blue-button" name="sw_save_options" value="Save Settings">

        </form>
        </div>

    <?php
}

function sw_render_invoice_options_page() {

    sw_handle_options_submission();
    $invoice_prefix  = get_option('sw_invoice_id_prefix', 'CINV');
    $invoice_page   = get_option('sw_invoice_page', 0);
    $pages                  = get_pages();
    $invoice_logo_url       = get_option('sw_invoice_logo_url', '');
    $invoice_watermark_url  = get_option('sw_invoice_watermark_url', '');
    echo '<h1> Invoice</h1>';
    ?>
        <div class="wrap">
        <form method="post" class="inv-settings-form">

        <input type="submit" class="sw-blue-button" name="sw_save_options" value="Save Settings">

        <!--Service Page -->
        <div class="sw-form-row">
        <label for="sw_invoice_page" class="sw-form-label">Invoice Page</label>
        <span class="sw-field-description" title="This page should have this shortcode [sw_service_page]">?</span>
        <select name="sw_invoice_page" id="sw_invoice_page" class="sw-form-input">
        <option value="0">Select an invoice page</option>
        <?php
        foreach ($pages as $page) {
            $selected = ($invoice_page == $page->ID) ? 'selected' : '';
            echo '<option value="' . $page->ID . '" ' . $selected . '>' . $page->post_title . '</option>';
        }
        ?>
        </select>
        </div>

        <!-- Invoice ID Prefix -->
        <div class="sw-form-row">
        <label for="sw_invoice_id_prefix" class="sw-form-label">Invoice ID Prefix</label>
        <span class="sw-field-description" title="Enter a text to prifix your invoice IDs">?</span>
        <input class="sw-form-input" type="text" name="sw_invoice_id_prefix" id="sw_invoice_id_prefix" value="<?php echo esc_attr( $invoice_prefix ); ?>" placeholder="eg, INV">
        </div>

        <!-- Invoice Logo URL -->
        <div class="sw-form-row">
        <label for="sw_invoice_logo_url" class="sw-form-label">Logo URL</label>
        <span class="sw-field-description" title="Paste the link to your logo url">?</span>
        <input type="text" name="sw_invoice_logo_url" id="sw_invoice_logo_url" value="<?php echo esc_attr( $invoice_logo_url ); ?>" placeholder="eg https://pic.com/logo.png" class="sw-form-input">
        </div>        
        
        
        <!-- Invoice Watermark URL -->
        <div class="sw-form-row">
        <label for="sw_invoice_watermark_url" class="sw-form-label">Watermark URL </label>
        <span class="sw-field-description" title="Enter your business name">?</span>
        <input type="text" name="sw_invoice_watermark_url" id="sw_invoice_watermark_url" value="<?php echo esc_attr( $invoice_watermark_url ); ?>" placeholder="eg https://pic.com/img.png" class="sw-form-input">
        </div>

        </form>
        </div>
    <?php
}

function sw_render_email_options_page() {
    sw_handle_email_options();
    $billing_email = get_option('sw_billing_email', '');
    $sender_name = get_option('sw_sender_name', '');

    // Define an array of checkbox names
    $checkboxes = array(
        'sw_cancellation_mail_to_user',
        'sw_service_opt_out_mail',
        'sw_payment_reminder_to_client',
        'sw_service_expiration_mail',
        'sw_new_invoice_mail',
        'sw_send_renewal_mail',
        'sw_reactivation_mail',
        'sw_invoice_paid_mail',
        'sw_service_cancellation_mail_to_admin',
        'sw_service_expiration_mail_to_admin',
    );

    ?>
    <div class="wrap">
        <h1>Emails</h1>
        <form method="post" class="inv-settings-form">

            <!-- Sender Name -->
            <div class="sw-form-row">
                <label for="sw_sender_name" class="sw-form-label">Sender Name</label>
                <input type="text" name="sw_sender_name" id="sw_sender_name" value="<?php echo esc_attr($sender_name); ?>" placeholder="eg, Billing Team" class="sw-form-input">
            </div>

            <!-- Billing Email -->
            <div class="sw-form-row">
                <label for="sw_billing_email" class="sw-form-label">Billing Email</label>
                <input type="email" name="sw_billing_email" id="sw_billing_email" value="<?php echo esc_attr($billing_email); ?>" placeholder="eg, billing@domain.com" class="sw-form-input">
            </div>

            <!-- Checkboxes -->
            <?php foreach ($checkboxes as $checkbox_name) : ?>
                <div class="sw-form-row">
                    <label for="<?php echo esc_attr($checkbox_name); ?>" class="sw-form-checkbox">
                        <?php echo esc_html(ucwords(str_replace (array('_', 'sw'), ' ', $checkbox_name))); ?>
                    </label>
                    <input type="checkbox" id="<?php echo esc_attr($checkbox_name); ?>" name="<?php echo esc_attr($checkbox_name); ?>" class="sw-form-input" <?php checked(get_option($checkbox_name, 0), 1); ?>>
                </div>
                <hr>
            <?php endforeach; ?>

            <input type="submit" class="sw-blue-button" name="sw_save_email_options" value="Save Changes">
        </form>
    </div>
    <?php
}
