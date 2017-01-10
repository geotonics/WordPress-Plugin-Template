<?php
/**
 * Document for class WordPress_Plugin_Template_User
 *
 * PHP Version 5.6
 *
 * @category Class
 * @package  WordPress_Plugin_Template
 * @author   Peter Pitchford <peter@geotonics.com>
 * @license  GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 * @link     http://geotonics.com/#geolib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to access user data created by this plugin
 *
 * @category library
 * @package  WordPress_Plugin_Template
 * @author   Peter Pitchford <peter@geotonics.com>
 * @license  GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 * @version  Release: .1
 * @link     http://geotonics.com
 * @since    Class available since Release .1
 */
class WordPress_Plugin_Template_User
{
	/**
	 * WordPress author id
	 *
	 * @var    int
	 * @access private
	 * @since  1.0.0
	 */

	private $author = null;


	/**
	 * Constructor for WordPress_Plugin_Template_User Class
	 *
	 * @param integer $author   WordPress author id.
	 * @param object  $instance Instance of WordPress_Plugin_Template.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct( $author, $instance ) {

		$this->author = $author;
		$this->instance = $instance;

	}

	/**
	 * Display User gizmos. 
	 * @return void
	 */
	public function display_data() {
		
		echo "<div class='plugin_data'>";
		$args = array(
			'post_type' => array( 'gizmo' )
			
		);
			
		$query = new WP_Query( $args );
		//$brands=$this->instance->brands("No brand selected");
		//geodb($brands,'allthebrands');

		if ( $query->have_posts() ) {
			echo '<table class="gizmo">';
			
			while ( $query->have_posts() ) {
				$query->the_post();

				$post = get_post( get_the_id() );
				$meta = get_post_meta( get_the_id() );
								
				if ( $post->post_type === 'gizmo' ) {
					
					if( isset($meta["date_picker_field"][0]) && $meta["date_picker_field"][0]) {
						$date=$meta["date_picker_field"][0];
					} else {
						$date="";
					}
					
					echo '<tr>
							<td class="entitle">'.$post->post_title.'</td>
							<td class="enapproved">'.$date.'</td>
							<td class="enedit"><a class="edit_post_link" id="edit_post_link_'. esc_html( $post->ID ).'">Edit</a></td>
							
							
					</tr>';
				}
			}
			
			echo '</table>';
		}

		
		printf( // WPCS: XSS OK.
			'<div id="edit_post_box" class="popupBox lgPopupBox"></div>
			<div id="edit_post_modal" class="modalBox popupCloser"></div>
			<div id="edit_post_loader" class="loader" style="background-image:url('.$this->instance->assets_url.'images/ajax-loader.gif);">
			</div>'
		);

		/* Restore original Post Data */
		wp_reset_postdata();
		echo  '</div>';
	}
	
}
?>
