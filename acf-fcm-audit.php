<?php
/**
 * Plugin Name: ACF Flexible Content Modules Audit
 * Plugin URI: https://www.roidna.com
 * Description: Analyze usage of ACF Flexible Content Modules
 * Version: 1.0
 * Author: Chris Bellew
 */

if (!defined('ABSPATH')) die();

if( ! class_exists('ACF_FCM_Audit') ) :

  class ACF_FCM_Audit {
    function __construct() {
      $this->initialize();
      add_action('admin_enqueue_scripts', array($this, 'init_scripts'));
      add_action('wp_ajax_acf_fcm_module_audit_run_audit', array($this, 'run_audit'));

  	}

    function init_scripts() {
      wp_enqueue_script( 'acf-fcm-audit-admin', plugins_url('js/acf-fcm-audit-admin.js', __FILE__), array('jquery'));
      wp_localize_script( 'acf-fcm-audit-admin',
          'acfFcmModuleAudit',
          [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ), //url for php file that process ajax request to WP
            'nonce' => wp_create_nonce( "acf-fcm-module-nonce." ),// this is a unique token to prevent form hijacking
          ]
      );
      wp_enqueue_style('acf-fcm-audit-admin', plugins_url('css/acf-fcm-audit.css', __FILE__), false);
    }

    function initialize() {
      $version = $this->version;
  		$basename = plugin_basename( __FILE__ );
  		$path = plugin_dir_path( __FILE__ );
  		$url = plugin_dir_url( __FILE__ );
  		$slug = dirname($basename);

      add_action('admin_init',	array($this, 'init'), 5);
    }

    function init() {
      add_action('acf/include_admin_tools', array($this, 'include_tools'));
    }

    function include_tools() {
      include_once($path . 'acf-fcm-audit-admin.php');
    }

    function run_audit() {
      $groups = acf_get_field_groups();


      $field_groups = [];

      foreach($groups as $group) {
        $fields = acf_get_fields($group);

        foreach($fields as $field) {
          if($field['type'] == 'flexible_content') {
            if(!isset($field_groups[$group['key']])) {
              $field_groups[$group['key']] = [
                'title' => $group['title'],
                'fields' => []
              ];
            }
            $field_groups[$group['key']]['fields'][$field['key']] = [
              'name' => $field['name'],
              'label' => $field['label'],
              'modules' => []
            ];
            foreach($field['layouts'] as $layout_id => $layout) {
              $field_groups[$group['key']]['fields'][$field['key']]['modules'][$layout['name']] = [
                'label' => $layout['label'],
                'count' => 0
              ];
            }
          }
        }
      }

      $posts = get_posts([
          'posts_per_page' => -1,
          'post_type' => 'any'
      ]);

      foreach ($posts as $post) {
        foreach($field_groups as $group_key => $group) {
          foreach($group['fields'] as $field_key => $field) {

            $modules_arr = get_field($field['name'], $post->ID);
            if(!is_array($modules_arr))
                continue;
            foreach($modules_arr as $module) {
              if(isset($field_groups[$group_key]['fields'][$field_key]['modules'][$module['acf_fc_layout']])) {
                $field_groups[$group_key]['fields'][$field_key]['modules'][$module['acf_fc_layout']]['count']++;
                $field_groups[$group_key]['fields'][$field_key]['modules'][$module['acf_fc_layout']]['urls'][] = get_permalink($post);
              }
            }
          }
        }
      }


      usort($field_groups, function($a, $b) {
        if($a['title'] == $b['title'])
          return 0;
        return ($a['title'] < $b['title']) ? -1 : 1;
      });
      // ksort[$uses];
      // print_r($field_groups);
      ?>
      <h2>Field Groups</h2>
      <ul class="groups">
      <?php
      foreach($field_groups as $group) { ?>
          <li>
            <h3><i></i><?php echo $group['title']; ?></h3>
            <ul class="fields">
            <?php
            foreach($group['fields'] as $field_id => $field) { ?>
              <li>
                <h4><i <?php echo sizeof($field['modules'] == 1) ? ' class="minus"' : ''; ?>"></i><?php echo $field['label']; ?></h4>
                <ul class="modules<?php echo sizeof($field['modules'] == 1) ? ' show' : ''; ?>">
              <?php foreach($field['modules'] as $module_key => $module) { ?>
                  <li>
                    <h5<?php echo $module['count'] == 0 ? ' class="empty">' : '><i></i>'; ?><?php printf("%s (%d)", $module['label'], $module['count']); ?></h5>
                    <ul class="urls">
                      <?php foreach($module['urls'] as $url) { ?>
                        <li><?php printf('<a href="%s" target="_blank">%s</a>', $url, $url); ?></li>
                      <?php } ?>
                    </ul>
                  </li>
              <?php } ?>
                </ul>
              </li>
            <?php } ?>
            </ul>
         </li>
      <?php } ?>
      </ul>
      <?php






      die();
    }
  }

  global $acf_fcm_audit;
  $acf_fcm_audit = new ACF_FCM_Audit();

endif;
