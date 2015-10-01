<?php

if ( ! defined( 'ABSPATH' ) ) exit;


class WordPress_Plugin_Template_Initialize{

	/**
	 * Constructor function
	 */
	public function __construct () {

        WordPress_Plugin_Template()->register_post_type( 'widget', __( 'Widgets', '' ), __( 'Widget', 'wordpress-plugin-template' ) );
               
       // add_action( 'add_meta_boxes', array($this,'add_meta_boxes'));
        
        
        
        
    }
    
    
}


