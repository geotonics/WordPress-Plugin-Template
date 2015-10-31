<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WordPress_Plugin_Template_Admin_API {
    
    /**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
    public $parent = null;
    
    /**
	 * Number of metaboxes.
	 * @var 	int
	 * @access  private
	 * @since 	1.0.0
	 */
    private $num_meta_boxes=0;
    
	/**
	 * Constructor function
	 */
	public function __construct ($parent) {
	    $this->parent=$parent;
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
        
        if(isset($field["post_type"])){
            $field["options"] = $this->get_post_type_options($post, $field["post_type"]);
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
            $all_options = wp_load_alloptions();
			
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
            case 'date_picker':
            case 'datetime_picker':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . esc_attr( $data ) . '" class="'.$field['type'].' " />' . "\n";
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
				$html .= '<p class="description">' . $field['description'] . '</p>';
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
	 * Get posts as options
	 * @param  array  $post Post opject
	 * @param  string $post_type Post type
	 * @return array
	 */
	private function get_post_type_options($post, $post_type){
	
        $args=array(
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'author' =>$post->post_author
        );
        
        $options = array();
        $query = new WP_Query($args);
        
        if ( $query->have_posts() ) {
        
            while ( $query->have_posts() ) {
		        $query->the_post();
		        $options[get_the_ID()]=get_the_title();
	        }
        }
        wp_reset_query();  // Restore global post data stomped by the_post().
        return $options;
	
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

		if ( ! is_array( $fields ) || 0 == count( $fields ) ) {
		    return;
		}

		foreach($fields as $name=>$tabs){
		    if($name!=$args["id"]){
		        continue;
		    }

		    if (isset($tabs["tabs"])) {
		        echo '<div class="metabox_tabs">
		        <ul class="category-tabs">';
		        
		        foreach($tabs["tabs"] as $tab_name=>$tab) {
		            echo '<li><a href="#'. $this->css_encode($tab_name).'">'.$tab_name.'</a></li>';
		        } 
		        echo '</ul> <br class="clear" />';
		        $tabnum=0;
		        
		        foreach($tabs["tabs"] as $tab_name=>$tab) {
		            
		            if($tabnum){
		                $link_class="hidden";
		            } else {
		                $link_class="";
		            }
	                echo '<div id="'.$this->css_encode($tab_name).'" class="'.$link_class.'">';
		            $tabnum++;
		            $this->display_metabox_fields($name,$tab,$post,$args);
		            echo '</div>';
		        }
		        
		        echo "</div>";
		        
		       
		        
		    } else {
		        $this->display_metabox_fields($name,$tabs,$post,$args);
		    }  
		    
		    if (isset($tabs["fields"])) {
		         $this->display_metabox_fields($name,$tabs["fields"],$post,$args);     
		    }
		        
		}

	}
    
    /**
	 * Dispay fields in metabox
	 * @param  array  $fields Field data
	 * @param  object $post  Post object
	 * @return void
	 */
    private function display_metabox_fields($metabox,$fields,$post,$args) {
        echo '<div class="custom-field-panel">' . "\n";
 	    echo '<table class="form-table">' . "\n";
		foreach ( $fields as $field ) {
			$field['metabox'] = array($metabox);
            
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
		
        add_filter($post_type."_custom_fields", array($this->parent->post_types,$post_type."_custom_fields"),10,2);                                                
        $metaboxes = apply_filters( $post_type . '_custom_fields', array(), $post_type );
  
		if ( ! is_array( $metaboxes ) || 0 == count( $metaboxes ) ) {
		    return;
		}

		$fields=array();

        foreach ($metaboxes as $metabox) {

            if (isset($metabox["tabs"])) {
                
                foreach ($metabox["tabs"] as $tab_fields) {
                    $fields=array_merge($fields,$tab_fields);
                }   
                

            } else{
                $fields=array_merge($fields,$metabox);
            }   
            
            if (isset($metabox["fields"])) {
                $fields=array_merge($fields,$metabox["fields"]);
            }
            
        }

		foreach ( $fields as $field ) {
            
			if ( isset( $_REQUEST[ $field['id'] ] ) ) {
				update_post_meta( $post_id, $field['id'], $this->validate_field( $_REQUEST[ $field['id'] ], $field['type'] ) );
			} else {
				update_post_meta( $post_id, $field['id'], '' );
			}
			
		}

	}
	
	/**
	 * Create slug from Name
	 * 
	 * @return string
	 */
	public function css_encode($id){
	    return strtolower(str_replace(" ","_",$id));   
	}

}