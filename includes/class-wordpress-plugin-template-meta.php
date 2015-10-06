<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WordPress_Plugin_Template_Meta
{
/**
	 * The single instance of WordPress_Plugin_Template_Settings.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $parent = null; 
    
    private $num_meta_boxes=0;
	/**
	 * Constructor function
	 */
	public function __construct ($parent) {
	    $this->parent = $parent;
	    $parent->register_post_type( 'gizmo', __( 'Gizmos', '' ), __( 'Gizmos', 'wordpress-plugin-template' ) );
	    add_action( 'add_meta_boxes', array($this,'add_meta_boxes'));
		
	}

	/**
	 * Add meta box to the dashboard
	 * @param string $id            Unique ID for metabox
	 * @param string $title         Display title of metabox
	 * @param array  $post_type    Post type to which this metabox applies
	 * @param string $context       Context in which to display this metabox ('advanced' or 'side')
	 * @param string $priority      Priority of this metabox ('default', 'low' or 'high')
	 * @param array  $callback_args Any axtra arguments that will be passed to the display function for this metabox
	 * @return void
	 */
	public function add_meta_box ( $id = '', $title = '', $post_types = array(), $context = 'advanced', $priority = 'default', $callback_args = null ) {
        global $post;

		// Get post type(s)
		if ( ! is_array( $post_types ) ) {
			$post_types = array( $post_types );
		}

		// Generate each metabox
		foreach ( $post_types as $post_type ) {
		    
		    if($post->post_type==$post_type){
    		    geodb("adding ".$post_type." metaboxes");
    		    add_meta_box( $id, $title, array( $this, 'meta_box_content' ), $post_type, $context, $priority, $callback_args );
		    }
		    
		}
		
	}

	function add_meta_boxes(){
           global $post;
           
           add_filter($post->post_type."_custom_fields", array($this,"custom_fields"),10,2);  
           $this->add_meta_box ('standard',__( 'Standard', 'wordpress-plugin-template' ),array("page","gizmo"));
           $this->add_meta_box ('extra',__( 'Extra', 'wordpress-plugin-template' ), array("page","gizmo"));
    }
    
    
    
    function custom_fields($fields,$postType){
        
            $fields=array();
            $settings['standard'] = array(
				array(
					'id' 			=> 'text_field',
					'label'			=> __( 'Some Text' , 'wordpress-plugin-template' ),
					'description'	=> __( 'This is a standard text field.', 'wordpress-plugin-template' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text', 'wordpress-plugin-template' ),
                    'tab'           => 0
				),
				array(
					'id' 			=> 'password_field',
					'label'			=> __( 'A Password' , 'wordpress-plugin-template' ),
					'description'	=> __( 'This is a standard password field.', 'wordpress-plugin-template' ),
					'type'			=> 'password',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text', 'wordpress-plugin-template' ),
                    'tab'           => 0
				),
				array(
					'id' 			=> 'secret_text_field',
					'label'			=> __( 'Some Secret Text' , 'wordpress-plugin-template' ),
					'description'	=> __( 'This is a secret text field - any data saved here will not be displayed after the page has reloaded, but it will be saved.', 'wordpress-plugin-template' ),
					'type'			=> 'text_secret',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text', 'wordpress-plugin-template' ),
                    'tab'           => 0
				),
				array(
					'id' 			=> 'text_block',
					'label'			=> __( 'A Text Block' , 'wordpress-plugin-template' ),
					'description'	=> __( 'This is a standard text area.', 'wordpress-plugin-template' ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text for this textarea', 'wordpress-plugin-template' ),
                    'tab'           => 0
				),
				array(
					'id' 			=> 'single_checkbox',
					'label'			=> __( 'An Option', 'wordpress-plugin-template' ),
					'description'	=> __( 'A standard checkbox - if you save this option as checked then it will store the option as \'on\', otherwise it will be an empty string.', 'wordpress-plugin-template' ),
					'type'			=> 'checkbox',
					'default'		=> '',
                    'tab'           => 1
				),
				array(
					'id' 			=> 'select_box',
					'label'			=> __( 'A Select Box', 'wordpress-plugin-template' ),
					'description'	=> __( 'A standard select box.', 'wordpress-plugin-template' ),
					'type'			=> 'select',
					'options'		=> array( 'drupal' => 'Drupal', 'joomla' => 'Joomla', 'wordpress' => 'WordPress' ),
					'default'		=> 'wordpress',
                    'tab'           => 1
				),
				array(
					'id' 			=> 'radio_buttons',
					'label'			=> __( 'Some Options', 'wordpress-plugin-template' ),
					'description'	=> __( 'A standard set of radio buttons.', 'wordpress-plugin-template' ),
					'type'			=> 'radio',
					'options'		=> array( 'superman' => 'Superman', 'batman' => 'Batman', 'ironman' => 'Iron Man' ),
					'default'		=> 'batman',
                    'tab'           => 1
				),
				array(
					'id' 			=> 'multiple_checkboxes',
					'label'			=> __( 'Some Items', 'wordpress-plugin-template' ),
					'description'	=> __( 'You can select multiple items and they will be stored as an array.', 'wordpress-plugin-template' ),
					'type'			=> 'checkbox_multi',
					'options'		=> array( 'square' => 'Square', 'circle' => 'Circle', 'rectangle' => 'Rectangle', 'triangle' => 'Triangle' ),
					'default'		=> array( 'circle', 'triangle' ),
                    'tab'           => 1
				)
		    );

		$settings['extra'] = array(
				array(
					'id' 			=> 'number_field',
					'label'			=> __( 'A Number' , 'wordpress-plugin-template' ),
					'description'	=> __( 'This is a standard number field - if this field contains anything other than numbers then the form will not be submitted.', 'wordpress-plugin-template' ),
					'type'			=> 'number',
					'default'		=> '',
					'placeholder'	=> __( '42', 'wordpress-plugin-template' )
				),
				array(
					'id' 			=> 'colour_picker',
					'label'			=> __( 'Pick a colour', 'wordpress-plugin-template' ),
					'description'	=> __( 'This uses WordPress\' built-in colour picker - the option is stored as the colour\'s hex code.', 'wordpress-plugin-template' ),
					'type'			=> 'color',
					'default'		=> '#21759B'
				),
				array(
					'id' 			=> 'an_image',
					'label'			=> __( 'An Image' , 'wordpress-plugin-template' ),
					'description'	=> __( 'This will upload an image to your media library and store the attachment ID in the option field. Once you have uploaded an imge the thumbnail will display above these buttons.', 'wordpress-plugin-template' ),
					'type'			=> 'image',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'multi_select_box',
					'label'			=> __( 'A Multi-Select Box', 'wordpress-plugin-template' ),
					'description'	=> __( 'A standard multi-select box - the saved data is stored as an array.', 'wordpress-plugin-template' ),
					'type'			=> 'select_multi',
					'options'		=> array( 'linux' => 'Linux', 'mac' => 'Mac', 'windows' => 'Windows' ),
					'default'		=> array( 'linux' )
				)
		);
        
        foreach($settings as $metabox=>$metabox_fields){
            
            foreach($metabox_fields as $field){
                $field["metabox"]=$metabox;
                $fields[]=$field;
            }                
              
        }     
        return $fields;
        }
        
  /**
	 * Display metabox content
	 * @param  object $post Post object
	 * @param  array  $args Arguments unique to this metabox
	 * @return void
	 */
	public function meta_box_content ( $post, $args ) {
	    
	    if (!$this->num_meta_boxes) {
            wp_nonce_field( "wordpress-plugin-template_".$post->post_type, "wordpress-plugin-template_".$post->post_type.'_nonce' );
		}
		
		$this->num_meta_boxes++;
		$fields = apply_filters( $post->post_type . '_custom_fields', array(), $post->post_type );

		if ( ! is_array( $fields ) || 0 == count( $fields ) ) return;
        
		echo '<div class="custom-field-panel">' . "\n";
 	echo '<table class="form-table">' . "\n";
		foreach ( $fields as $field ) {

			if ( ! isset( $field['metabox'] ) ) continue;

			if ( ! is_array( $field['metabox'] ) ) {
				$field['metabox'] = array( $field['metabox'] );
			}
            
			if ( in_array( $args['id'], $field['metabox'] ) ) {
				$this->parent->admin->display_meta_box_field( $field, $post );
			}

		}
		
echo "</table>";
		echo '</div>' . "\n";

	}      
        
        /**
	 * Main WordPress_Plugin_Template_Settings Instance
	 *
	 * Ensures only one instance of WordPress_Plugin_Template_Settings is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WordPress_Plugin_Template()
	 * @return Main WordPress_Plugin_Template_Settings instance
	 */
	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __wakeup()

}

