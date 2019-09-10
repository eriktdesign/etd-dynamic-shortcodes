<?php
class ETD_Shortcode_Metabox {

	/**
	 * Add actions for metabox initialization
	 */
	public function __construct() {

		if ( is_admin() ) {
			add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
			add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
		}

	}

	/**
	 * Add metaboxes and save action
	 * @return [type] [description]
	 */
	public function init_metabox() {

		add_action( 'add_meta_boxes', array( $this, 'add_metabox'  )        );
		add_action( 'save_post',      array( $this, 'save_metabox' ), 10, 2 );

	}

	/**
	 * Add metabox to Dynamic Shortcode CPT
	 */
	public function add_metabox() {

		add_meta_box(
			'shortcode_meta',
			__( 'Shortcode Details', 'etd' ),
			array( $this, 'render_metabox' ),
			'dynamic_shortcode',
			'advanced',
			'default'
		);

	}

	/**
	 * Output fields for shortcode metabox
	 * @param  WP_Post $post post object being edited
	 * @return null
	 */
	public function render_metabox( $post ) {

		// Add nonce for security and authentication.
		wp_nonce_field( 'shortcode_nonce_action', 'shortcode_nonce' );

		// Retrieve an existing value from the database.
		$shortcode_tag = get_post_meta( $post->ID, 'shortcode_tag', true );
		$shortcode_value = get_post_meta( $post->ID, 'shortcode_value', true );

		// Set default values.
		if( empty( $shortcode_tag ) ) $shortcode_tag = '';
		if( empty( $shortcode_value ) ) $shortcode_value = '';

		// Form fields.
		echo '<table class="form-table">';

		// Shortcode Tag
		echo '	<tr>';
		echo '		<th><label for="shortcode_tag" class="shortcode_tag_label">' . __( 'Shortcode:', 'etd' ) . '</label></th>';
		echo '		<td>';
		echo '			<input type="text" id="shortcode_tag" name="shortcode_tag" class="shortcode_tag_field" placeholder="' . esc_attr__( 'tag', 'etd' ) . '" value="' . esc_attr__( $shortcode_tag ) . '">';
		echo '		</td>';
		echo '	</tr>';

		// Shortcode Value
		echo '	<tr>';
		echo '		<th><label for="shortcode_value" class="shortcode_value_label">' . __( 'Replace with:', 'etd' ) . '</label></th>';
		echo '		<td>';
		echo '			<input type="text" id="shortcode_value" name="shortcode_value" class="shortcode_value_field" placeholder="' . esc_attr__( 'value', 'etd' ) . '" value="' . esc_attr__( $shortcode_value ) . '">';
		echo '		</td>';
		echo '	</tr>';

		echo '</table>';

		// Instructions
		printf( '<p>Your tag <code>[%s]</code> will be replaced with <code>%s</code> wherever it appears in your site content.',
			$shortcode_tag != '' ? $shortcode_tag : 'tag', 
			$shortcode_value != '' ? $shortcode_value : 'value' );

	}

	/**
	 * Save metabox values to post meta
	 * @param  int      $post_id Post ID of edited post
	 * @param  WP_Post  $post    Post object being edited
	 * @return null
	 */
	public function save_metabox( $post_id, $post ) {

		// Add nonce for security and authentication.
		// $nonce_name   = $_POST['shortcode_nonce'];
		$nonce_action = 'shortcode_nonce_action';

		// Check if a nonce is set.
		if ( ! isset( $_POST['shortcode_nonce'] ) )
			return;

		// Check if a nonce is valid.
		if ( ! wp_verify_nonce( $_POST['shortcode_nonce'], $nonce_action ) )
			return;

		// Check if the user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return;

		// Check if it's not an autosave.
		if ( wp_is_post_autosave( $post_id ) )
			return;

		// Check if it's not a revision.
		if ( wp_is_post_revision( $post_id ) )
			return;

		// Sanitize user input.
		$shortcode_new_tag = isset( $_POST['shortcode_tag'] ) ? $this->sanitize_tag( $_POST['shortcode_tag'] ) : '';
		$shortcode_new_value = isset( $_POST['shortcode_value'] ) ? sanitize_text_field( $_POST['shortcode_value'] ) : '';

		// Update the meta field in the database.
		update_post_meta( $post_id, 'shortcode_tag', $shortcode_new_tag );
		update_post_meta( $post_id, 'shortcode_value', $shortcode_new_value );

	}

	/**
	 * Helper function to sanitize the tag name
	 * Tag name must be valid as a PHP variable name
	 * @param  string $input Tag name from user
	 * @return string        Sanitized tag name
	 */
	public function sanitize_tag( $input ) {
		if ( ! @preg_match( '/\pL/u', 'a' ) ) {
			$pattern = '/[^a-zA-Z0-9]/';
		} else {
			$pattern = '/[^\p{L}\p{N}]/u';
		}
		return preg_replace($pattern, '', (string) $input);
	}

}