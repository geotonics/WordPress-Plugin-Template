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
	 * Display a summary of the users data, including custom post types
	 * In this case were are displaying the parent custom types by the date of the selected child type,
	 * but this is just an arbitrary example. Each custom post title has a jquery link which opens up a
	 * full page modal with a form to edit the selected post.
	 * @return void
	 */
	public function display_data() {

		echo "<div class='plugin_data'>";
		$args = array(
			'post_type' => array( 'gizmo', 'baby_gizmo' ),
			'author' => $this->author,
			);
		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {

			while ( $query->have_posts() ) {
				$query->the_post();

				$post = get_post( get_the_id() );
				$meta = get_post_meta( get_the_id() );

				if ( $post->post_type === 'gizmo' ) {

					if ( $meta['date_picker_field'][0] ) {
						$date = $meta['date_picker_field'][0];
					} else {
						$date = 'No Date';
					}

					if ( $meta['baby_gizmo_box'][0] ) {
						$baby = $meta['baby_gizmo_box'][0];
					} else {
						$baby = '0';
					}

					$gizmos[ $baby ][ $date ][ $post->ID ] = $post->post_title;
				} else {
					$baby_gizmos[ $post->ID ] = $post->post_title;
				}
			}
		}

		foreach ( $baby_gizmos as $baby_id => $baby_gizmo ) {

			echo "<h3 style='margin:0'>".esc_html( $baby_gizmo ).'</h2>';

			if ( isset( $gizmos[ $baby_id ] ) ) {

	            foreach ( $gizmos[ $baby_id ] as $date => $post_arr ) {
	                echo '<h4>'.esc_html( $date )."</h4>
	                <ul class='admin_list'>";

	                foreach ( $post_arr as $post_id => $post_title ) {
	                    echo '<li class="edit_post_link" id="edit_post_link_'. esc_html( $post_id ).'">'. esc_html( $post_title ).'</li>';
	                }

	                echo '</ul>';

	            }
	        }
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
