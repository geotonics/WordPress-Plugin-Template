<?php
/**
 * Document for class WordPress_Plugin_Template_Init
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
 * Class to check php and plugin version, and do updates if neccesary
 *
 * @category include
 * @package  WordPress_Plugin_Template
 * @author   Peter Pitchford <peter@geotonics.com>
 * @license  GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 * @version  Release: .1
 * @link     http://geotonics.com
 * @since    Class available since Release .1
 */
class WordPress_Plugin_Template_Init
{

	/**
	 * The single instance of WordPress_Plugin_Template Init.
	 * @var     object
	 * @access  private
	 * @since     1.0.0
	 */
	private static $_instance = null;

	/**
	 * Minimal Required PHP Version
	 * @var string
	 * @access  private
	 * @since   1.0.0
	 */
	private $minimalRequiredPhpVersion  = 5;

	/**
	 * Plugin Name.
	 * @var     string
	 * @access  private
	 * @since   1.0.0
	 */
	public $plugin_name;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * New versions installed by this upgrade
	 * @var     array
	 * @access  private
	 * @since   1.0.0
	 */
	private $new_versions;


	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct() {

		$this->plugin_name = str_replace( '_', ' ', __CLASS__ );
	} // End __construct ().

	/**
	 * Provide a useful error message when the user's PHP version is less than the required version
	 */
	public function notice_php_version_wrong() {
		$allowed = array(
		    'div' => array(
		    	'class' => array(),
		    	'id' => array(),
		    ),
		    'p' => array(),
		    'br' => array(),
		    'strong' => array(),
		);

		$html = '<div class="updated fade">'.
		  __( 'Error: plugin "'.$this->plugin_name.'" requires a newer version of PHP to be running.',  'example' ).
				'<br/>' . __( 'Minimal version of PHP required: ', 'example' ) . '<strong>' . $this->minimalRequiredPhpVersion . '</strong>
				<br/>' . __( 'Your server\'s PHP version: ', 'example' ) . '<strong>' . phpversion() . '</strong>
				</div>';
		echo  wp_kses( $html, $allowed );
	}

	/**
	 * PHP version check
	 */
	protected function php_version_check() {

		if ( version_compare( phpversion(), $this->minimalRequiredPhpVersion ) < 0 ) {
			add_action( 'admin_notices', array( $this, 'notice_php_version_wrong' ) );
			return false;
		}

		return true;
	}

	/**
	 * Check plugin version, Do any neccesary actions if new version has been installed.
	 */
	public function upgrade() {

		$upgradeOk = true;
		$savedVersion = $this->option->get_version_saved();
		$newVersion = $this->_version;
		$new_versions = array();

		if ( $this->is_version_less_than( $savedVersion, $newVersion ) ) {

			if ( $this->is_version_less_than( $savedVersion, '1.8' ) ) {

				if ( $this->is_version_less_than( $savedVersion, '1.5' ) ) {

					if ( $this->is_version_less_than( $savedVersion, '1' ) ) {

						$new_versions[] = '1';
						// Perform version 1 upgrade action.
					}

					$new_versions[] = '1.5';
					// Perform version 1.5 upgrade action.
				}

				$new_versions[] = '1.8';
				// Perform version 1.8 upgrade action.
			}

			$new_versions[] = '2.0';
			// Perform version 2.0 upgrade action.
		}

		// Post-upgrade, set the current version in the options.
		// Saving newVersion.
		if ( $upgradeOk && $savedVersion !== $newVersion ) {
			$this->new_versions = $new_versions;
			add_action( 'admin_notices', array( $this, 'notice_new_version' ) );
			$this->log_version_number();
		}

	}

	/**
	 * Compares version numbers and determines if the result is less than zero.
	 * @param  string $version1 A version string such as '1', '1.1', '1.1.1', '2.0', etc.
	 * @param  string $version2 A version string such as '1', '1.1', '1.1.1', '2.0', etc.
	 * @return bool true if version_compare of $versions1 and $version2 shows $version1 as earlier
	 */
	public function is_version_less_than( $version1, $version2 ) {
		return (version_compare( $version1, $version2 ) < 0);
	}
	
	/**
	 * Provide a useful error message if the Plugin has been updated.
	 */
	public function notice_new_version() {

		foreach ( $this->new_versions as $new_version ) {
			echo '<div class="updated fade">' .
		  		esc_html( __( 'Plugin "'.$this->plugin_name.'" has been updated to version ', 'wordpress_plugin_template' ).$new_version ).
			 	'</div>';
		}

	}

	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function log_version_number() {
		$this->option->update_option( '_version', $this->_version );
	} // End _log_version_number ()
}
