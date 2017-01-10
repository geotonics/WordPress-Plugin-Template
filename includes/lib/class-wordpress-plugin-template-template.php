<?php
/**
 * Document for class WordPress_Plugin_Template_Template
 *
 * PHP Version 5.6
 *
 * @category Class
 * @package WordPress_Plugin_Template
 * @author   Peter Pitchford <peter@geotonics.com>
 * @license  GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 * @link     http://geotonics.com/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to create a virtual template
 *
 * @category include
 * @package  WordPress_Plugin_Template
 * @author   Peter Pitchford <peter@geotonics.com>
 * @license  GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 * @version  Release: .1
 * @link     http://geotonics.com
 * @since    Class available since Release .1
 */
class WordPress_Plugin_Template_Template {

	/**
	 * A reference to an instance of this class.
	 */
	private static $instance;

	/**
	 * The array of templates that this plugin tracks.
	 */
	protected $templates;

	/**
	 * Returns an instance of this class. 
	 */
	public static function instance($parent) {

		if ( null == self::$instance ) {
			self::$instance = new WordPress_Plugin_Template_Template($parent);
		} 

		return self::$instance;

	} 

	/**
	 * Initializes the plugin by setting filters and administration functions.
	 */
	private function __construct($parent) {
		$this->parent = $parent;
		$this->templates = array();


		// Add a filter to the attributes metabox to inject template into the cache.
		if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {

			// 4.6 and older
			add_filter(
				'page_attributes_dropdown_pages_args',
				array( $this, 'register_project_templates' )
			);

		} else {

			// Add a filter to the wp 4.7 version attributes metabox
			add_filter(
				'theme_page_templates', array( $this, 'add_new_template' )
			);

		}

		// Add a filter to the save post to inject out template into the page cache
		add_filter(
			'wp_insert_post_data', 
			array( $this, 'register_project_templates' ) 
		);


		// Add a filter to the template include to determine if the page has our 
		// template assigned and return it's path
		add_filter(
			'template_include', 
			array( $this, 'view_project_template') 
		);


		// Add your templates to this array.
		
		$this->templates = array(
			'edit-custom-posts.php'     => 'Edit Custom Posts',
			//'goodtobebad-template.php' => 'It\'s Good to Be Bad',
		);
		
		add_action( 'wp_ajax_update_post_form', array( $this, 'update_post_form' ) );
		add_action( 'wp_ajax_save_post', array( $this, 'save_post' ) );
		add_action( 'wp_ajax_delete_post', array( $this, 'delete_post' ) );
			
	} 

	/**
	 * Adds our template to the page dropdown for v4.7+
	 *
	 */
	public function add_new_template( $posts_templates ) {
		$posts_templates = array_merge( $posts_templates, $this->templates );
		return $posts_templates;
	}

	/**
	 * Adds our template to the pages cache in order to trick WordPress
	 * into thinking the template file exists where it doens't really exist.
	 */
	public function register_project_templates( $atts ) {

		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		// Retrieve the cache list. 
		// If it doesn't exist, or it's empty prepare an array
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		} 

		// New cache, therefore remove the old one
		wp_cache_delete( $cache_key , 'themes');

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );

		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $atts;

	} 

	/**
	 * Checks if the template is assigned to the page
	 */
	public function view_project_template( $template ) {
		
		// Get global post
		global $post;

		// Return template if post is empty
		if ( ! $post ) {
			return $template;
		}

		// Return default template if we don't have a custom one defined
		if ( ! isset( $this->templates[get_post_meta( 
			$post->ID, '_wp_page_template', true 
		)] ) ) {
			return $template;
		} 

		$file = plugin_dir_path( $this->parent->file ). 'includes/templates/'.get_post_meta(
			$post->ID, '_wp_page_template', true
		);

		// Just to be safe, we check if the file exist first
		if ( file_exists( $file ) ) {
			return $file;
		} else {
			echo $file." does not exist";
		}

		// Return template
		return $template;

	}
	
	/**
	 * Form for saving data for plugin template.
	 */
	public function update_post_form() {
		
		$current_user = wp_get_current_user();
		
		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );
		$orig_post = get_post( $post_id );
		
		echo '<h3>Edit '.esc_html( $orig_post->post_title ).'</h3>';
		$meta = get_post_meta( $post_id );
		
		$post_type = $orig_post->post_type;
		$fields = $this->parent->admin->get_fields( $post_type );
		
		
		echo '<div>
			<a id="redx" class="popupCloser pointer geoJsLink">
				<img width="19" height="19" alt="Close" src="'.esc_url( $this->parent->assets_url ).'/images/redx.png">
			</a>
		</div>
		<div class="popup_content">';

		echo '<form id="edit_post_form" method="post">
         		<div>
         			<h4>Edit this</h4>
         			<div>
         				<input type="text" name="post-post_title" id="post_title" value="'.esc_html( $orig_post->post_title ).'">
					</div>
         			<div>
         				<textarea name="post-post_content" id="post_content" rows="4" cols="35">'.
			        		esc_html( $orig_post->post_content ).
			        	'</textarea>
			        </div>
         			
					<div>'.
		 				$this->parent->admin->display_field( array( 'prefix' => 'meta-', 'field' => $fields['radio_buttons'] ), $orig_post ).
		 			'</div>
		 			<div>'.
        				$this->parent->admin->display_field( array( 'prefix' => 'meta-', 'field' => $fields['date_picker_field'] ), $orig_post ).
        			'</div>
        		</div>';
    		
			echo '<div>
				<input type="hidden" value="save_post" name="action">
	        	<input type="hidden" value="'.esc_html( $post_id ).'" name="post_id" id="post_id">
	        	<input type="submit" value="Save" class="save_button">';
	    		wp_nonce_field( 'gizmos_save_post'.esc_html( $post_id ), 'gizmos_save_post'.esc_html( $post_id ).'_nonce' );
			echo '</div>
		</form>';

        echo '<form id="delete_post_form" method="post">
			<div>
				<input type="hidden" value="delete_post" name="action">
	        	<input type="hidden" value="'.esc_html( $post_id ).'" name="delete_post_id" id="delete_post_id">
	        	<input type="submit" value="Delete" name="delete_gizmo">';
	    		wp_nonce_field( 'gizmos_delete_post'.esc_html( $post_id ), 'gizmos_delete_post'.esc_html( $post_id ).'_nonce' );
			echo '</div>
		</form><div id="feedback"></div>';
	        
		echo geoDebug::vars();
		die();
	}

	/**
	 * Save data for plugin template.
	 */
	public function save_post() {
		
		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );
		geodb($post_id,'post_id in save_post');

		if ( isset( $post_id ) ) {
			$nonce_id = 'gizmos_save_post'.$post_id;
			$nonce = filter_input( INPUT_POST, $nonce_id.'_nonce', FILTER_SANITIZE_STRING );
			if ( $nonce ) {

				if ( ! wp_verify_nonce( $nonce, $nonce_id ) ) {
					return;
				}
			} else {
				return;
			}

			$orig_post = get_post( $post_id );
			$post_type = $orig_post->post_type;
			
			// reset any checkboxes. 
			$fields = $this->parent->admin->get_fields( $post_type );
			foreach ($fields as $field) {

				if ($field["type"]=="checkbox") {
					$result = $this->parent->admin->update_post_metas( $post_id , array($field["id"]=>null) );
				}
				
			}
			
			$update_post = $update_meta = array();

			foreach ( filter_input_array( INPUT_POST )  as $name => $value ) {
				$namearr = explode( '-',$name );

				switch ( $namearr[0] ) {
					case 'meta':
						// It must be a meta value, update it.
						$update_meta[ $namearr[1] ] = $value;
						break;
					case 'post':
						$update_post[ $namearr[1] ] = $value;
						continue;
						break;
				}
				
			}

			if ( $update_meta ) {
				$update_post['ID'] = $post_id;
				$result = $this->parent->admin->update_post_metas( $post_id , $update_meta );
			}

			if ( $update_post ) {
				$update_post['ID'] = $post_id;
				// Update the post into the database.
				$result['post_result'] = wp_update_post( $update_post );
			}
			
		}
		$User=new WordPress_Plugin_Template_User( true, $this->parent);
      	$User->display_data();
		//echo geoDebug::vars();
		//echo wp_json_encode( $result );
		die();
	}
	
	
	/**
	 * delete data for plugin template.
	 */
	public function delete_post() {
		
		$post_id = filter_input( INPUT_POST, 'delete_post_id', FILTER_SANITIZE_NUMBER_INT );
		if ( isset( $post_id ) ) {
			$nonce_id = 'gizmos_delete_post'.$post_id;
			$nonce = filter_input( INPUT_POST, $nonce_id.'_nonce', FILTER_SANITIZE_STRING );
			
			if ( $nonce ) {

				if ( ! wp_verify_nonce( $nonce, $nonce_id ) ) {
					return;
				}
			} else {
				return;
			}
			
			$fields = $this->parent->admin->get_fields( "gizmo");
			
			foreach($fields as $fieldid => $field){
				$result=delete_post_meta($post_id,$fieldid);
			}
			
			wp_delete_post( $post_id);
			
		}

		$User=new WordPress_Plugin_Template_User( true, $this->parent);
      	$User->display_data();
		echo geoDebug::vars();
		die();
	}

} 

