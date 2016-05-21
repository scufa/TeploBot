<?php
if(!defined('ABSPATH')) die; // Die if accessed directly

class GWPTB_Filters {
	
	private static $instance = NULL;
	
	private function __construct() {
				
		//add input filter
		add_filter('gwptb_input_latin', array('GWPTB_Filters', 'sanitize_email'));
		add_filter('gwptb_input_text', array('GWPTB_Filters', 'sanitize_string'));
		
		//search term
		add_filter('gwptb_search_term', array('GWPTB_Filters','sanitize_search_term'));
		
		//output filter (for tlgrm)
		add_filter('gwptb_output_html', array('GWPTB_Filters','print_html'));
	}
	
	
	/** instance */
    public static function get_instance(){
        
        if (NULL === self :: $instance)
			self :: $instance = new self;
					
		return self :: $instance;
    }     
	
	
	/** == Input == **/
	public static function sanitize_email($input){
		//Remove all characters except letters, digits and !#$%&'*+-=?^_`{|}~@.[]. 
		
		return filter_var($input, FILTER_SANITIZE_EMAIL);
	}
	
	
	public static function sanitize_string($input){
		//Strip tags, strip special characters
		
		return filter_var($input, FILTER_SANITIZE_STRING);
	}
	
	public static function sanitize_text($input){
		//HTML-escape '"<>& and characters with ASCII value less than 32
		
		$input = strip_tags($input); //no html at all
		return filter_var($input, FILTER_SANITIZE_SPECIAL_CHARS);
	}
	
	
	public static function sanitize_url($input){
		//Remove all characters except letters, digits and $-_.+!*'(),{}|\\^~[]`<>#%";/?:@&=. 
		
		return filter_var($input, FILTER_SANITIZE_URL);
	}
	
	public static function sanitize_search_term($input){
		
		$input = preg_replace("/&#?[a-z0-9]{2,8};/i","",$input);
		$input = preg_replace('/[^a-zA-ZА-Яа-я0-9-ёЁ\s]/u','',$input);
				
		return $input;
	}
	
	/** == Output == **/
	public static function print_html($output){
		
		$allowed_html = array(
			'a' => array(
				'href' => array()				
			),
			'b' => array(),
			'i' => array(),
			'em' => array(),
			'strong' => array(),
			'code' => array(),
			'pre' => array()
		);
		
		$output = wp_kses($output, $allowed_html);
		
		return $output;
	}
	
	
	
	/** == Special filters == **/
	public static function sanitize_message_entity($ent){
		
		if(isset($ent->type))
			$ent->type = self::sanitize_email($ent->type);
		
		if(isset($ent->offset))
			$ent->offset = (int)$ent->offset;
			
		if(isset($ent->length))
			$ent->length = (int)$ent->length;
		
		if(isset($ent->url))
			$ent->type = self::sanitize_url($ent->type);
			
		return $ent;
	}
	
} //class

GWPTB_Filters::get_instance();