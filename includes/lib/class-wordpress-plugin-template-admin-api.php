<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WordPress_Plugin_Template_Admin_API {
    
    private $num_meta_boxes=0;
	/**
	 * Constructor function
	 */
	public function __construct () {
	    add_action( 'add_meta_boxes', array($this,'add_meta_boxes'));
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 10, 1 );
		
	}
	
	

	/**
	 * Generate HTML for displaying fields
	 * @param  array   $field Field data
	 * @param  boolean $echo  Whether to echo the field HTML or return it
	 * @return void
	 */
	public function display_field ( $data = array(), $post = false, $echo = true ) {

		// Get field info
		if ( isset( $data['field'] ) ) {
			$field = $data['field'];
		} else {
			$field = $data;
		}

		// Check for prefix on option name
		$option_name = '';
		if ( isset( $data['prefix'] ) ) {
			$option_name = $data['prefix'];
		}

		// Get saved data
		$data = '';
		if ( $post ) {

			// Get saved field data
			$option_name .= $field['id'];
			
			$option = get_post_meta( $post->ID, $field['id'], true );

			// Get data to display in field
			if ( isset( $option ) ) {
				$data = $option;
			}

		} else {

			// Get saved option
			$option_name .= $field['id'];
			$option = get_option( $option_name );

			// Get data to display in field
			if ( isset( $option ) ) {
				$data = $option;
			}

		}

		// Show default data if no option saved and default is supplied
		if ( !$data && isset( $field['default'] ) ) {
			$data = $field['default'];
		} elseif ( $data === false ) {
			$data = '';
		}

		$html = '';

		switch( $field['type'] ) {

			case 'text':
			case 'url':
			case 'email':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . esc_attr( $data ) . '" />' . "\n";
			break;

			case 'password':
			case 'number':
			case 'hidden':
				$min = '';
				if ( isset( $field['min'] ) ) {
					$min = ' min="' . esc_attr( $field['min'] ) . '"';
				}

				$max = '';
				if ( isset( $field['max'] ) ) {
					$max = ' max="' . esc_attr( $field['max'] ) . '"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . esc_attr( $data ) . '"' . $min . '' . $max . '/>' . "\n";
			break;

			case 'text_secret':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="" />' . "\n";
			break;

			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea><br/>'. "\n";
			break;

			case 'checkbox':
				$checked = '';
				if ( $data && 'on' == $data ) {
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
			break;

			case 'checkbox_multi':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( is_array($data) && in_array( $k, $data ) ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '" class="checkbox_multi"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;

			case 'radio':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( $k == $data ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;

			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( $k == $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
			break;

			case 'select_multi':
				$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple="multiple">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					
					if ( is_array($data) && in_array( $k, $data ) ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
			break;

			case 'image':
				$image_thumb = '';
				if ( $data ) {
					$image_thumb = wp_get_attachment_thumb_url( $data );
				}
				$html .= '<img id="' . $option_name . '_preview" class="image_preview" src="' . $image_thumb . '" /><br/>' . "\n";
				$html .= '<input id="' . $option_name . '_button" type="button" data-uploader_title="' . __( 'Upload an image' , 'wordpress-plugin-template' ) . '" data-uploader_button_text="' . __( 'Use image' , 'wordpress-plugin-template' ) . '" class="image_upload_button button" value="'. __( 'Upload new image' , 'wordpress-plugin-template' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '_delete" type="button" class="image_delete_button button" value="'. __( 'Remove image' , 'wordpress-plugin-template' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '" class="image_data_field" type="hidden" name="' . $option_name . '" value="' . $data . '"/><br/>' . "\n";
			break;

			case 'color':
			geodb($data,'data');
			
			$html.='<div class="color-picker" style="position:relative;">
			    <input type="text" name="'.  esc_attr(__($option_name )).'" class="color" value="'. esc_attr(__( $data )).'" />
			    <div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;" class="colorpicker"></div>
			    </div>';
			   
			break;

		}

		switch( $field['type'] ) {

			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				$html .= '<br/><span class="description">' . $field['description'] . '</span>';
			break;

			default:
				if ( ! $post ) {
					$html .= '<label for="' . esc_attr( $field['id'] ) . '">' . "\n";
				}

				$html .= '<p class="description">' . $field['description'] . '</p>' . "\n";

				if ( ! $post ) {
					$html .= '</label>' . "\n";
				}
			break;
		}

		if ( ! $echo ) {
			return $html;
		}

		echo $html;

	}

	/**
	 * Validate form field
	 * @param  string $data Submitted value
	 * @param  string $type Type of field to validate
	 * @return string       Validated value
	 */
	public function validate_field ( $data = '', $type = 'text' ) {

		switch( $type ) {
			case 'text': $data = esc_attr( $data ); break;
			case 'url': $data = esc_url( $data ); break;
			case 'email': $data = is_email( $data ); break;
		}

		return $data;
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
				$this->display_meta_box_field( $field, $post );
			}

		}
		
echo "</table>";
		echo '</div>' . "\n";

	}

	/**
	 * Dispay field in metabox
	 * @param  array  $field Field data
	 * @param  object $post  Post object
	 * @return void
	 */
	public function display_meta_box_field ( $field = array(), $post ) {

		if ( ! is_array( $field ) || 0 == count( $field ) ) return;

		$field = '<tr class=""><th><label for="' . $field['id'] . '">' . $field['label'] . '</label></th><td>' . $this->display_field( $field, $post, false ) . '</td></tr>' . "\n";
		echo $field;
	}

	/**
	 * Save metabox fields
	 * @param  integer $post_id Post ID
	 * @return void
	 */
	public function save_meta_boxes ( $post_id = 0 ) {
		
		if ( ! $post_id ) return;

		$post_type = get_post_type( $post_id );

		if ( ! wp_verify_nonce( $_REQUEST["wordpress-plugin-template_".$post_type . '_nonce'], "wordpress-plugin-template_".$post_type) ) {
			return;
		}
        add_filter($post_type."_custom_fields", array($this,"custom_fields"),10,2);                                                
        $fields = apply_filters( $post_type . '_custom_fields', array(), $post_type );
    
		if ( ! is_array( $fields ) || 0 == count( $fields ) ) return;

		foreach ( $fields as $field ) {
			if ( isset( $_REQUEST[ $field['id'] ] ) ) {
				update_post_meta( $post_id, $field['id'], $this->validate_field( $_REQUEST[ $field['id'] ], $field['type'] ) );
			} else {
				update_post_meta( $post_id, $field['id'], '' );
			}
		}
	}
	
	function add_meta_boxes(){
           global $post;
           add_filter($post->post_type."_custom_fields", array($this,"custom_fields"),10,2);  
           $this->add_meta_box ('standard',__( 'Standard', 'wordpress-plugin-template' ),array("page","widget"));
           $this->add_meta_box ('extra',__( 'Extra', 'wordpress-plugin-template' ), array("page","widget"));
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
					'placeholder'	=> __( 'Placeholder text', 'wordpress-plugin-template' )
				),
				array(
					'id' 			=> 'password_field',
					'label'			=> __( 'A Password' , 'wordpress-plugin-template' ),
					'description'	=> __( 'This is a standard password field.', 'wordpress-plugin-template' ),
					'type'			=> 'password',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text', 'wordpress-plugin-template' )
				),
				array(
					'id' 			=> 'secret_text_field',
					'label'			=> __( 'Some Secret Text' , 'wordpress-plugin-template' ),
					'description'	=> __( 'This is a secret text field - any data saved here will not be displayed after the page has reloaded, but it will be saved.', 'wordpress-plugin-template' ),
					'type'			=> 'text_secret',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text', 'wordpress-plugin-template' )
				),
				array(
					'id' 			=> 'text_block',
					'label'			=> __( 'A Text Block' , 'wordpress-plugin-template' ),
					'description'	=> __( 'This is a standard text area.', 'wordpress-plugin-template' ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text for this textarea', 'wordpress-plugin-template' )
				),
				array(
					'id' 			=> 'single_checkbox',
					'label'			=> __( 'An Option', 'wordpress-plugin-template' ),
					'description'	=> __( 'A standard checkbox - if you save this option as checked then it will store the option as \'on\', otherwise it will be an empty string.', 'wordpress-plugin-template' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'select_box',
					'label'			=> __( 'A Select Box', 'wordpress-plugin-template' ),
					'description'	=> __( 'A standard select box.', 'wordpress-plugin-template' ),
					'type'			=> 'select',
					'options'		=> array( 'drupal' => 'Drupal', 'joomla' => 'Joomla', 'wordpress' => 'WordPress' ),
					'default'		=> 'wordpress'
				),
				array(
					'id' 			=> 'radio_buttons',
					'label'			=> __( 'Some Options', 'wordpress-plugin-template' ),
					'description'	=> __( 'A standard set of radio buttons.', 'wordpress-plugin-template' ),
					'type'			=> 'radio',
					'options'		=> array( 'superman' => 'Superman', 'batman' => 'Batman', 'ironman' => 'Iron Man' ),
					'default'		=> 'batman'
				),
				array(
					'id' 			=> 'multiple_checkboxes',
					'label'			=> __( 'Some Items', 'wordpress-plugin-template' ),
					'description'	=> __( 'You can select multiple items and they will be stored as an array.', 'wordpress-plugin-template' ),
					'type'			=> 'checkbox_multi',
					'options'		=> array( 'square' => 'Square', 'circle' => 'Circle', 'rectangle' => 'Rectangle', 'triangle' => 'Triangle' ),
					'default'		=> array( 'circle', 'triangle' )
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

}