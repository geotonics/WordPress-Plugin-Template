<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WordPress_Plugin_Template_Init {

	/**
	 * The single instance of WordPress_Plugin_Template Init.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Minimal Required PHP Version
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
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */  
	public function __construct (  ) {
	    $this->plugin_name=str_replace("_"," ",__CLASS__);
	} // End __construct ()
    
    /**
     * Check the PHP version and give a useful error message if the user's version is less than the required version
     * @return boolean true if version check passed. If false, triggers an error which WP will handle, by displaying
     * an error message on the Admin page
     */
    public function noticePhpVersionWrong() {
        echo '<div class="updated fade">' .
          __('Error: plugin "'.$this->plugin_name.'" requires a newer version of PHP to be running.',  'example').
                '<br/>' . __('Minimal version of PHP required: ', 'example') . '<strong>' . $this->minimalRequiredPhpVersion . '</strong>' .
                '<br/>' . __('Your server\'s PHP version: ', 'example') . '<strong>' . phpversion() . '</strong>' .
             '</div>';
    }
    
    protected function phpVersionCheck() {

        if (version_compare(phpversion(), $this->minimalRequiredPhpVersion) < 0) {
            add_action('admin_notices', array($this,'noticePhpVersionWrong'));
            return false;
        }

        return true;
    }
	
}