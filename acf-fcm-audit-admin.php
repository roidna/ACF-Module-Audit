<?php



if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('ACF_FCM_Audit_Admin') ) :

acf_include('includes/admin/tools/class-acf-admin-tool.php');

class ACF_FCM_Audit_Admin extends ACF_Admin_Tool {
  function __construct() {
    parent::__construct();
    //wp_enqueue_style( ‘style’, plugins_url(‘css/demo_style.css’,__FILE__));


  }

  function initialize() {

    // vars
    $this->name = 'ACF_FCM_Audit_Admin';
    $this->title = __("Flexible Content Modules Audit", 'acf_fcm_module_audit');
    $this->icon = 'dashicons-upload';

  }

  function html() {

		?>
		<p><?php _e('Click the Run Audit button to analyze usage of Flexible Content Modules.', 'acf'); ?></p>
		<p class="acf-submit">
			<input type="button" id="run-audit-button" class="button button-primary" value="<?php _e('Run Audit', 'acf-fcm-module-audit'); ?>" />
		</p>
    <div class="acf-fcm-module-audit-results"></div>
		<?php

	}

}

// initialize
acf_register_admin_tool( 'ACF_FCM_Audit_Admin' );

endif; // class_exists check

?>
