<?php
/**
 * Plugin Name: Google Custom Search actions
 * Plugin URI: https://github.com/fiveeightsix/fes-google-cs-actions
 * Description: Integrate a Google custom search engine into a WordPress site using action hooks.
 * Author: Karl Inglis
 * Author URI: http://web.karlinglis.net
 * Version: 1.0.0
 */

// If this file is called directly, die.
if ( ! defined( 'WPINC' ) ) {
  die( 'Nope.' );
}

/**
 * URL for Google custom search script.
 *
 * @return string Google CSE URL for the site.
 */
function fes_gcsa_script_url() {
  $options = get_option( 'fes_gcsa_options' );
  return 'https://cse.google.com/cse.js?cx=' . $options['engine_id'];
}

/**
 * Enqueue Google CSE script.
 */
function fes_gcsa_scripts() {
  wp_enqueue_script( 'fes-google-search', fes_gcsa_script_url(), array(), null );
}
add_action( 'wp_enqueue_scripts', 'fes_gcsa_scripts' );

/**
 * Google search element with results page URL.
 */
function fes_gcsa_search_box() {
  $options = get_option( 'fes_gcsa_options' );
  echo '<gcse:searchbox-only resultsUrl="' . site_url( '/' . $options['site_url'] ) . '"></gcse:searchbox-only>';
}
add_action( 'fes_gcsa_search_box', 'fes_gcsa_search_box' );

/**
 * Google search results element.
 */
function fes_gcsa_search_results() {
  echo "<gcse:search></gcse:search>";
}
add_action( 'fes_gcsa_search_results', 'fes_gcsa_search_results' );

/**
 * Validate submitted input from the options page.
 *
 * @param array $input Input from form fields.
 * @return array Sanitized input for the database.
 */
function fes_gcsa_options_validate( $input ) {
  $new_input = array(
    'engine_id'    => sanitize_text_field( $input['engine_id'] ),
    'results_page' => sanitize_title( $input['results_page'] )
  );
  return $new_input;
}

/**
 * HTML template for the options page.
 */
function fes_gcsa_settings_page_html() {
?>
  <div class="wrap">
    <h1><?php echo  __( 'Google Custom Search actions settings', 'fes-gcsa' ); ?></h1>
    <form action="options.php" method="post">
      <?php
      settings_fields( 'fes_gcsa_option_group' );
      do_settings_sections( 'google-csa-settings' );
      submit_button();
      ?>
    </form>
  </div>
<?php
}

/**
 * HTML template for main option section.
 */
function fes_gcsa_main_section_html() {
  // Do nothing for now.
}

/**
 * HTML template for the search engine ID form field.
 *
 * @global array $options Plugin options.
 */
function fes_gcsa_id_html() {
  $options = get_option( 'fes_gcsa_options' );
  $value = esc_attr( $options['engine_id'] );
?>
  <input id="fes_gcsa_id" name="fes_gcsa_options[engine_id]" size="30" type="text" value="<?php echo $value; ?>" />
  <p class="description">
    <?php _e( 'This is obtained from the custom search control panel.', 'fes-gcsa' ) ?>
  </p>
<?php
}

/**
 * HTML template for the results page slug form field.
 *
 * @global array $options Plugin options.
 */
function fes_gcsa_results_page_html() {
  $options = get_option( 'fes_gcsa_options' );
  $value = esc_attr( $options['results_page'] );
?>
  <p>
  <code><?php echo site_url( '/' ); ?></code>
  <input id="fes_gcsa_results_page" name="fes_gcsa_options[results_page]" size="20" type="text" value="<?php echo $value; ?>" />
  </p>
  <p class="description">
    <?php _e( 'This page must contain the ', 'fes-gcsa' ); ?>
    <code>&lt;gcse:search&gt;&lt;/gcse:search&gt;</code>
    <?php _e( ' element.', 'fes-gcsa' ); ?>
  </p>
<?php
}

/**
 * Register settings and set up fields and sections for the plugin options page. 
 */
function fes_gcsa_admin_init() {
  register_setting(
    'fes_gcsa_option_group',               /* Option group */
    'fes_gcsa_options',                    /* Option name */
    'fes_gcsa_options_validate'            /* Callback to sanitize option value */
  );
  add_settings_section(
    'fes_gcsa_main',                       /* Tag attribute id strings */
    null,                                  /* Section title */ 
    'fes_gcsa_main_section_html',          /* Callback for display HTML */
    'google-csa-settings'                  /* Page slug on which to display */
  );
  add_settings_field(
    'fes_gcsa_id',                         /* Tag attribute id strings */
    __( 'Search engine ID', 'fes-gcsa' ),  /* Field title */
    'fes_gcsa_id_html',                    /* Callback for display HTML */
    'google-csa-settings',                 /* Page slug on which to display */
    'fes_gcsa_main'                        /* Section this option belongs to */
  );
  add_settings_field(
    'fes_gcsa_results_page',               /* Tag attribute id strings */
    __( 'Results page slug', 'fes-gcsa' ), /* Field title */
    'fes_gcsa_results_page_html',          /* Callback for display HTML */
    'google-csa-settings',                 /* Page slug on which to display */
    'fes_gcsa_main'                        /* Section this option belongs to */
  );
}
add_action( 'admin_init', 'fes_gcsa_admin_init' );

/**
 * Add the plugin's option page as an item under the 'Settings' menu.
 */
function fes_gcsa_add_pages() {
  // Add a new page under Settings
  add_options_page(
    __( 'Google CS actions', 'fes-gcsa' ), /* Page title */
    __( 'Google CS actions', 'fes-gcsa' ), /* Menu title */
    'manage_options',                      /* User capability needed to access this page */
    'google-csa-settings',                 /* Menu slug */
    'fes_gcsa_settings_page_html'          /* Callback to display page */
  );
}
add_action( 'admin_menu', 'fes_gcsa_add_pages' );

?>
