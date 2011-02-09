<?php
/*****************************************************************************************
* Theme Options Functions
* 
*  - Define Default Theme Options
*  - Register/Initialize Theme Options
*  - Define Admin Settings Page
*  - Register Contextual Help
*******************************************************************************************/

global $oenology_options;
global $oenology_admin_options_hook;

/*****************************************************************************************
* Define the default options
*******************************************************************************************/

function oenology_get_default_options() {

    $options = array(
        'header_nav_menu_position' => 'top',
        'display_footer_credit' => false,
	'varietal' => 'cuvee',
        'theme_version' => '1.1'
    );
    return $options;
}

function oenology_get_valid_varietals() {

    $varietals = array(
        'cuvee' => array(
	      'slug' => 'cuvee',
	      'name' => 'Cuvee',
	      'description' => '"Cuvee" is a term often used by wineries to describe a particularly high-quality batch of wine. Cuvee is the base style for Oenology.'
	      ),
        'syrah' => array(
	      'slug' => 'syrah',
	      'name' => 'Syrah',
	      'description' => 'Syrah is a red grape that produces a full-bodied, almost inky-black wine with a spicy, earthy flavor and aroma.'
	      ),
        'seyval-blanc' => array(
	      'slug' => 'seyval-blanc',
	      'name' => 'Seyval Blanc',
	      'description' => 'Seyval Blanc is a white grape, typically grown in cooler climates, that produces a wine with flavors of citrus and mineral.'
	      )
    );
    return $varietals;
}


/*****************************************************************************************
* Setup initial Theme options
*******************************************************************************************/

function oenology_options_init() {

	// set options equal to defaults
	global $oenology_options;
	$oenology_options = get_option( 'theme_oenology_options' );
	
	if ( false === $oenology_options ) {
		$oenology_options = oenology_get_default_options();
	}
	update_option( 'theme_oenology_options', $oenology_options );
}
// Initialize Theme options
add_action('after_setup_theme', 'oenology_options_init', 9 );


/*****************************************************************************************
* Enqueue Varietal Stylesheet
*******************************************************************************************/

function oenology_enqueue_varietal_style() {

	// define varietal stylesheet
	global $oenology_options;
	$oenology_options = get_option( 'theme_oenology_options' );
	$varietal_handle = 'oenology_' . $oenology_options['varietal'] . '_stylesheet';
	$varietal_stylesheet = get_template_directory_uri() . '/varietals/' . $oenology_options['varietal'] . '.css';
	
	wp_enqueue_style( $varietal_handle, $varietal_stylesheet );
}
// Enqueue Varietal Stylesheet at wp_head()
add_action('wp_print_styles', 'oenology_enqueue_varietal_style', 11 );


/*****************************************************************************************
* Setup the Theme Admin Settings Page
*******************************************************************************************/

// Add "Theme Options" link to the "Appearance" menu
function oenology_menu() {
	global $oenology_admin_options_hook;
	$oenology_admin_options_hook = add_theme_page('Theme Options', 'Oenology Options', 'edit_theme_options', 'oenology', 'oenology_admin_options_page');
}
// Load the Admin Options page
add_action('admin_menu', 'oenology_menu');


// Define Settings Page Tabs
// http://www.onedesigns.com/tutorials/separate-multiple-theme-options-pages-using-tabs
function oenology_admin_options_page_tabs( $current = 'general' ) {

    if ( isset ( $_GET['tab'] ) ) :
        $current = $_GET['tab'];
    else:
        $current = 'general';
    endif;
    
    $tabs = array( 
        'general' => 'General',
        'varietals' => 'Varietals'
    );
    
    $links = array();
    
    foreach( $tabs as $tab => $name ) :
        if ( $tab == $current ) :
            $links[] = "<a class='nav-tab nav-tab-active' href='?page=oenology&tab=$tab'>$name</a>";
        else :
            $links[] = "<a class='nav-tab' href='?page=oenology&tab=$tab'>$name</a>";
        endif;
    endforeach;
    
    echo '<div id="icon-themes" class="icon32"><br /></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach ( $links as $link )
        echo $link;
    echo '</h2>';
    
}


// Admin settings page markup 
function oenology_admin_options_page() { ?>

	<div class="wrap">
		<?php if ( isset( $_GET['settings-updated'] ) ) {
    			echo "<div class='updated'><p>Theme settings updated successfully.</p></div>";
		} ?>
		<?php oenology_admin_options_page_tabs(); ?>
		<form action="options.php" method="post">
			<?php 
			settings_fields('theme_oenology_options');
			do_settings_sections('oenology');
			?>
			<?php $tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'general' ); ?>
			<input name="theme_oenology_options[submit-<?php echo $tab; ?>]" type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings'); ?>" />
			<input name="theme_oenology_options[reset-<?php echo $tab; ?>]" type="submit" class="button-secondary" value="<?php esc_attr_e('Reset Defaults'); ?>" />
		</form>
	</div>
<?php }

// Admin settings page Form Fields markup
// 
// Codex Reference: http://codex.wordpress.org/Settings_API
// Reference: http://ottopress.com/2009/wordpress-settings-api-tutorial/
// Reference: http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/
function oenology_admin_init(){
	require( get_template_directory() . '/functions/options-init.php' );
}
// Settings API options initilization and validation
add_action('admin_init', 'oenology_admin_init');


/*****************************************************************************************
* Setup the Theme Admin Settings Page Contextual help
*******************************************************************************************/

// Admin settings page contextual help markup
// Separate file for ease of management
function oenology_contextual_help( $contextual_help, $screen_id, $screen ) {		
	global $oenology_admin_options_hook;
	require( get_template_directory() . '/functions/options-help.php' );
	if ( $screen_id == $oenology_admin_options_hook ) {
		$contextual_help = $text;
	}
	return $contextual_help;
}
// Add contextual help to Admin Options page
add_action('contextual_help', 'oenology_contextual_help', 10, 3);
?>