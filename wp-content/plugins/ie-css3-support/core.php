<?php

class Support {
	
	private $class_arr = array();
	public $message = array();
	
	/*
	 * Return active theme style content
	*/
	public function get_theme_style_content( $path='' ){

		$file 		 =   $path;
		$thenesize   =   filesize( $file );
		$handle      =   fopen( $file , 'r' );
		$css        =   fread($handle,filesize($file));
		
		fclose( $handle );
		return $css;
		
	}
	
	/*
	 * Return css3 paramenters
	*/
	public function get_css_param(){
		
		$arr = array( 'border-radius' , 'box-shadow' , 'linear-gradient' );
		return $arr;
	
	}
	
	/*
	 * Add class to class list
	 * @class - array
	*/
	public function add_class( $class=NULL ){
		
		$this->class_arr[] = $class;
	
	}
	
	/*
	 * Return class list
	*/
	public function get_class_arr(){
		
		$unique = array_map('unserialize', array_unique(array_map('serialize', $this->class_arr)));
		return $unique;
	
	}
	
	/*
	 * Return added stylesheet's size
	 * @files - array
	*/
	public function get_file_sizes( $files = array() ){
	
		$size = (int)filesize(THEME_CSS_PATH);
		
		if( count( $files ) > 0 && is_array( $files ) ) {
				
			foreach( $files as $file ) {
			
				$size = $size + (int)filesize( $file );
				
			}
		
		}
		
		return $size;
		
	}
	
	/*
	 * Return added stylesheet contents
	 * @files - array
	*/
	public function get_added_styles( $files = array() ){
	
		$str = '';
		
		if( count( $files ) > 0 && is_array( $files ) ) {
				
				foreach( $files as $file ){
					
					if( file_exists( $file ) ) { 
					
						$str .= $this->get_theme_style_content( $file );
						
					}
					
				}
				
		}
				
		return $str;
		
	}
	
	/*
	 * Strip extra characters
	 * @str - string
	*/
	public function clean( $str='' ){
		
		$str = trim( preg_replace('/\s\s+/', ' ', $str) );
		$par   = array ('/{/', '/}/'); 
		$str = preg_replace( $par , '' , $str);
		return $str;
		
	}
	
	/*
	 * Apply PIE markup
	*/
	public function build_css(){
		
		$css            =  '';
		$css_arrs       =  $this->get_class_arr();
		$css3_param_arr =  $this->get_class_arr();
		
		foreach( $css_arrs as $css_arr ){
			
			$css .= $css_arr['class'].' {';
			
			foreach( $css_arr['css3_param'] as $css3_par ){
				$css .= $this->build_param( $css3_par , $css_arr['param'] );
			}
			
			$css .= 'behavior: url('.MARKUP_PATH.');';
			$css .= ' }';
			
		}
		
		$this->write( CUSTOM_CSS_PATH , $css );
		
	}
	
	/*
	 * Write something to file
	 * @path - string
	 * @css - string
	*/
	public function write( $path='' , $css='' ){
		
		$holder = fopen( $path , 'w+' );
		fwrite( $holder , $css );
		fclose( $holder );
		
	}
	
	/*
	 * Construct PIE css paramenters
	 * @css3_param - string
	 * @param_string - string
	*/
	public function build_param( $css3_param = '' , $param_string ){
		
		$par = '';
		
		preg_match_all( '/\s*(.*)\s*\:\s*(.*)\s*\;/sU' , $param_string , $param_arrs , PREG_SET_ORDER );
		
		foreach( $param_arrs as $param_arr ) {

			if( trim( $css3_param ) == 'linear-gradient' ) {
				if( substr_count( trim( $param_arr[0] ) , '-webkit-linear-gradient' ) > 0 || substr_count( trim( $param_arr[0] ) , '-moz-linear-gradient' ) > 0 || substr_count( trim( $param_arr[0] ) , '-ms-linear-gradient' ) > 0 || substr_count( trim( $param_arr[0] ) , '-o-linear-gradient' ) > 0 ){
					$lg_preg = array( '/-webkit-linear-gradient/','/-moz-linear-gradient/','/-ms-linear-gradient/','/-o-linear-gradient/' );
					$par = '-pie-background:' . preg_replace( $lg_preg , 'linear-gradient' , $param_arr[2] ) . ';';
					break;
				}
			} else {
				if( substr_count( trim( $param_arr[0] ) , $css3_param ) > 0 ){
					$par = $css3_param.':'.$param_arr[2].';';
					break;
				}
			}
			
		}
		
		// REmove Condition for version 2.0.1 if( substr_count( trim( $param_arr[0] ) , 'box-shadow' ) > 0 ) {
			
		if( substr_count( trim( $param_string ) , 'z-index' ) == 0 ){
				$par .= 'z-index:2;';
		}
		if( substr_count( trim( $param_string ) , 'position:' ) == 0 ){
				$par .= 'position:relative;';
		}
		
		return $par;
			
	}
	
	/*
	 * Add stylesheed
	 * @path - string
	*/
	function add_path( $path='' ){
		
		$type = substr( strrchr( $path , '.' ) , 1 );
	
		if( file_exists( $path ) ) {
			
			if( $type=='css' ) {
			
				$paths = array();
				
				$paths_ser = get_option( 'added_stylesheet' );
				
				if( !is_array( $paths_ser ) ) {
					$paths = unserialize( $paths_ser );
				}
				
				$paths[] = $path;
				
				$paths = array_unique( $paths );
				
				update_option( 'added_stylesheet' ,serialize( $paths ) );
				
				$this->message( array('type'=>'updated','message'=>'Stylesheet Added.') );
			
			} else {
			
				$this->message( array('type'=>'error','message'=>'Error adding Stylesheet. Invalid file format.') );
			
			}
			
		} else {
		
			$this->message( array('type'=>'error','message'=>'Error adding Stylesheet. File does not exist.') );
		
		}
		
	}
	
	/*
	 * Remove stylesheed
	 * @index - int
	*/
	public function remove_path( $index , $redirect=true ){
	
		$paths_arr = get_option( 'added_stylesheet' );
		
		if( !is_array( $paths_arr ) ) {
			$paths_arr = unserialize( $paths_arr );
		}
		
		unset( $paths_arr[ $index ] );
		
		$paths_ser = serialize($paths_arr);
		update_option( 'added_stylesheet' , $paths_ser );
		
		if( $redirect ) {
			
			wp_redirect( remove_query_arg( 'remove' ) );
			
		}
		
	}
	
	/*
	 * Add flash message
	 * @message - array
	*/
	public function message( $message ){
		
		$this->message = $message;
		
	}
	
	/*
	 * Display flash message
	*/
	public function get_message(){
		
		$message_arr = $this->message;
		if( isset( $message_arr['type'] ) && isset( $message_arr['message'] ) ) {
			echo '<div id="message" class="' . $message_arr['type'] . ' below-h2"> <p>' . $message_arr['message'] . ' </p></div>';
		}
		
	}
	
	
}

?>