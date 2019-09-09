<?php

/**
 * Core plugin class
 *
 * @since      1.0.0
 * @package    ETD_Dynamic_Shortcodes
 * @subpackage ETD_Dynamic_Shortcodes/includes
 * @author     Erik Teichmann <erik@eriktdesign.com>
 * 
 */
class ETD_Dynamic_Shortcodes {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The meta box for the shortcode
	 * @since    1.0.0
	 * @access   protected
	 * @var      ETD_Shortcode_Metabox    $metabox    Object with metabox
	 */
	protected $metabox;

	protected $defined_shortcodes;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'ETD_DYNAMIC_SHORTCODES_VERSION' ) ) {
			$this->version = ETD_DYNAMIC_SHORTCODES_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'etd_dynamic_shortcodes';
		$this->defined_shortcodes = array();

		include plugin_dir_path( __FILE__ ) . 'class-etd-shortcode-metabox.php';
		$this->metabox = new ETD_Shortcode_Metabox();

		$this->define_hooks();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_hooks() {

		// define post type
		add_action( 'init', array( $this, 'define_post_type' ), 0 );

		// add shortcodes
		add_action( 'init', array( $this, 'define_shortcodes' ), 10 );

	}

	/**
	 * Register the custom post type for our shortcodes
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function define_post_type() {

		$labels = array(
			'name'                  => _x( 'Shortcodes', 'Post Type General Name', 'etd' ),
			'singular_name'         => _x( 'Shortcode', 'Post Type Singular Name', 'etd' ),
			'menu_name'             => __( 'Shortcodes', 'etd' ),
			'name_admin_bar'        => __( 'Shortcode', 'etd' ),
			'archives'              => __( 'Shortcode Archives', 'etd' ),
			'attributes'            => __( 'Shortcode Attributes', 'etd' ),
			'parent_item_colon'     => __( 'Parent Shortcode:', 'etd' ),
			'all_items'             => __( 'All Shortcodes', 'etd' ),
			'add_new_item'          => __( 'Add New Shortcode', 'etd' ),
			'add_new'               => __( 'Add New', 'etd' ),
			'new_item'              => __( 'New Shortcode', 'etd' ),
			'edit_item'             => __( 'Edit Shortcode', 'etd' ),
			'update_item'           => __( 'Update Shortcode', 'etd' ),
			'view_item'             => __( 'View Shortcode', 'etd' ),
			'view_items'            => __( 'View Shortcodes', 'etd' ),
			'search_items'          => __( 'Search Shortcode', 'etd' ),
			'not_found'             => __( 'Not found', 'etd' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'etd' ),
			'featured_image'        => __( 'Featured Image', 'etd' ),
			'set_featured_image'    => __( 'Set featured image', 'etd' ),
			'remove_featured_image' => __( 'Remove featured image', 'etd' ),
			'use_featured_image'    => __( 'Use as featured image', 'etd' ),
			'insert_into_item'      => __( 'Insert into Shortcode', 'etd' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Shortcode', 'etd' ),
			'items_list'            => __( 'Shortcode list', 'etd' ),
			'items_list_navigation' => __( 'Shortcode list navigation', 'etd' ),
			'filter_items_list'     => __( 'Filter Shortcode list', 'etd' ),
		);
		$args = array(
			'label'                 => __( 'Shortcode', 'etd' ),
			'description'           => __( 'Dynamic Shortcodes to replace with user-defined text.', 'etd' ),
			'labels'                => $labels,
			'supports'              => array( 'title' ),
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 75,
			'menu_icon'             => 'dashicons-code-standards',
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			'rewrite'               => false,
			'capability_type'       => 'page',
		);
		register_post_type( 'dynamic_shortcode', $args );

	}

	/**
	 * Retrieve the user's shortcodes and store in an array
	 * Uses a transient to cache the shortcodes.
	 * @return [type] [description]
	 */
	public function get_shortcodes() {
		
		// Check if transient exists
		if ( false === ( $this->defined_shortcodes = get_transient( 'etd_defined_shortcodes' ) ) ) {
			
			// Query our custom post type for all published posts
			$query = new WP_Query( array(
				'post_type'      => 'dynamic_shortcode',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			) );
			
			// Loop
			if ( $query->have_posts() ) {
				while( $query->have_posts() ) {
					$query->the_post();
					
					// Retrieve the shortcode tag
					$tag = get_post_meta( get_the_ID(), 'shortcode_tag', true );
					// Retrieve the shortcode value
					$value = get_post_meta( get_the_ID(), 'shortcode_value', true );
					
					// If tag is set, add to the shortcodes array
					if ( $tag != '' ) $this->defined_shortcodes[$tag] = $value;
				}
			}
			
			// Save results
			set_transient( 'etd_defined_shortcodes', 24 * HOUR_IN_SECONDS );
		}

		// Return array of $tag => $value pairs
		return $this->defined_shortcodes;
	}

	/**
	 * Define a shortcode for each post
	 * @return null 
	 */
	public function define_shortcodes() {
		
		// Retrieve shortcodes
		$shortcodes = $this->get_shortcodes();

		// If shortcodes exist
		if ( is_array( $shortcodes ) ) {
			foreach ( $shortcodes as $tag => $value ) {
				// Add a shortcode for each valid tag
				add_shortcode( $tag, array( $this, 'shortcode_handler' ) );
			}			
		}
	}

	/**
	 * Output handler for shortcodes
	 * @param  array   $atts    Shortcode attributes
	 * @param  string  $content Content surrounded by shortcode tag (not used)
	 * @param  string  $tag     The tag called. Used to pull the correct output
	 * @return string           Filtered output
	 */
	public function shortcode_handler( $atts, $content, $tag ) {
		// Retrieve the correct output value
		$output = $this->defined_shortcodes[$tag];

		// Handle shortcode attributes
		if ( is_array( $atts ) ) {

			// Do case conversion
			if ( array_key_exists( 'case', $atts ) ) {
				switch ( $atts['case'] ) {
					case 'upper':
						$output = strtoupper( $output );
						break;

					case 'lower':
						$output = strtolower( $output );
						break;
					
					case 'first':
						$output = ucfirst( $output );
						break;

					case 'words':
						$output = ucwords( $output );
						break;
				}
			}
		}

		// Filter output
		$output = apply_filters( "dynamic_shortcode_$tag", $output );

		// Send the output
		return $output;
	}

}