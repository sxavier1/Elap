<?php
/*
Plugin Name: IE CSS3 Support
Plugin URI: http://cmdino.com/ie-css3-support/
Description: Implement css markup that support CSS3 "border-radius", "box-shadow" and "linear-gradient" in IE versions 6 to 9
Version: 2.0.1
Author: Cristopher M. Diño
Author URI: http://cmdino.com/about-me/
License: GPL v2

Copyright (C) 2012  Cristopher M. Diño

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

*/

define( 'IESUPPORT_PLUGIN_URL'  , plugin_dir_url(  __FILE__  ) );
define( 'IESUPPORT_PLUGIN_PATH' , plugin_dir_path(  __FILE__  ) );
define( 'THEME_PATH'            , get_stylesheet_directory() );
define( 'THEME_CSS_PATH'        , THEME_PATH . '/style.css' );
define( 'CUSTOM_CSS_PATH'       , IESUPPORT_PLUGIN_PATH . '/css/custom-style.css' );
define( 'MARKUP_PATH'           , plugins_url( '/markup/PIE.htc', __FILE__ ) );

include( IESUPPORT_PLUGIN_PATH.'/core.php' );

$support = new support;

// Set option values
function initializer(){

	 add_option( 'current_size' , '0' , '' , 'yes' );
	 add_option( 'added_stylesheet', '0' , '' , '' );

}
register_activation_hook( __FILE__ , 'initializer' );

// Unset option values
function iesupport_remove(){
	
	 delete_option( 'current_size' );
	 delete_option( 'added_stylesheet' );

}
register_deactivation_hook( __FILE__ , 'iesupport_remove' );

// Execute plugin
function ie_css_support_init(){
	
	global $wp_styles , $support;
	
	// Enqueue custom style to theme
	wp_register_style( 'custom-style', plugins_url( '/css/custom-style.css', __FILE__ ), array(), '20121910', false );  
	wp_enqueue_style( 'custom-style' );
	
	// Get styles
	$style_arrs = get_option( 'added_stylesheet' );
			
	if( !is_array( $style_arrs ) ) {
			
			$style_arrs = unserialize( $style_arrs );
				
	}
	
	$prev_size = (int)get_option('current_size');
	$size = $support->get_file_sizes( $style_arrs );
	
	// Check current and previous theme style size
	if( $prev_size != $size ) {

			// Undate current file size
			update_option( 'current_size' , $size );
	
			// Get active theme style content
			$theme_css = $support->get_theme_style_content( THEME_CSS_PATH );
			
			$theme_css .= $support->get_added_styles( $style_arrs );
			
			// Explode theme css
			preg_match_all( '/\s*(.*)\s*\{\s*(.*)\s*\}/sU' , $theme_css , $css_array , PREG_SET_ORDER );
			
			$special_params = $support->get_css_param();
			
			foreach( $css_array as $param ){
				
				$css3_arr = array();
				foreach( $special_params as $special_param ) {
		
					if( trim($param[2])!='' ){
		
							if( substr_count( trim($param[2]) , $special_param ) > 0 ){
								
								$css3_arr[] = $special_param;
												
							}
							
					}
				
				}	
				
				if( count(  $css3_arr ) > 0 ) {
				
					$data_arr = array(
							'class' => $support->clean( $param[1] ),
							'param' => $support->clean( $param[2] ),
							'css3_param' => $css3_arr
					);
								
					$support->add_class( $data_arr );
					
				}
						
							
			}
			
			// Build and write generated css to custom css stylesheet
			$support->build_css();
	
	}
}

add_action('wp_enqueue_scripts','ie_css_support_init');

// Option display
function iesupport_options(){
	
	global $support;
	
	$base_path_text = THEME_PATH.'/';
	$base_path = '';
	
	// Get path selection
	if( isset( $_POST['iecsssupport_select'] ) ) {
		
			$base_path = $_POST['base-path-select'];
			
			if( $base_path=='ROOT' ){
				
				$base_path_text = ABSPATH;
				
			} else if( $base_path=='PLUGIN PATH' ){
				
				$base_path_text = plugin_dir_path(  dirname(__FILE__)  );
				
			}
			
	}
	
	?>
	<div class="iecsssupport-option-wrapper">
    
    	<h2><?php echo _e('IE CSS3 Support Options');?></h2>
        
        <?php  echo $support->get_message(); ?>
        
        <?php 
		$style_arrs  = get_option( 'added_stylesheet' );
		 
		if( !is_array( $style_arrs ) ){
			
			$style_arrs = unserialize( $style_arrs );
			
		}
		?>
        
        <p><i><?php _e( " This plugin automatically scan your theme's main stylesheet and look for CSS3 properties to apply the markup. In case you have <br />CSS3 properties in other stylesheet, you can add them. " ); ?> </i></p>
		
        <ul class="path-list">
        
        <?php if( count( $style_arrs ) > 0 && is_array( $style_arrs ) ) { ?>
        
			<?php foreach( $style_arrs as $key => $style_arr ){?>
            
            	<?php if( file_exists( $style_arr ) ) {?>
                
                <li>
                
                    <span class="added-path"><?php echo $style_arr;?></span>
                    
                    <div class="actions">
                    
                        <a href="<?php echo add_query_arg( 'remove', $key );?>">Remove</a>
                        
                    </div>
                    
                    <div class="clr"></div>
                    
                </li>
                
                <?php } else { 
				
					$support->remove_path( $key , false );
				
				}?>
            
            <?php } ?>
            
        <?php } ?>
        
        </ul>
        
        <div class="clr"></div>
        
        <p><b><?php _e( " Specify stylesheet here. " ); ?> </b></p>
        
        <div class="clr"></div>

    	<form id="add-form" method="post">

        	<label>PATH: </label>
            <select name="base-path-select">
            	<option <?php echo ( ( $base_path=='THEME PATH' )? 'selected="selected"' : '' ); ?> >THEME PATH</option>
                <option <?php echo ( ( $base_path=='ROOT' )? 'selected="selected"' : '' ); ?> >ROOT</option>
                <option <?php echo ( ( $base_path=='PLUGIN PATH' )? 'selected="selected"' : '' ); ?> >PLUGIN PATH</option>
            </select>
            <input type="submit" name="iecsssupport_select" value="Select">
            <input readonly="readonly" name="base-path" class="base-path" value="<?php echo $base_path_text; ?>">
            <input type="text" name="path" id="path" value="" />
            <input type="submit" name="iecsssupport_add" value="Add">
        
        </form>
        
        <div class="clr"></div>
        
        <div id="iecsssupport-affiliates">
        <a href="https://managewp.com/?utm_source=A&utm_medium=Banner&utm_content=mwp_banner_5_125x125&utm_campaign=A&utm_mrl=636"><img src="https://managewp.com/banners/affiliate/mwp_banner_5_125x125.jpg" /></a>
        <a href="http://www.tipsandtricks-hq.com/wordpress-emember-easy-to-use-wordpress-membership-plugin-1706?ap_id=cmdino88" target="_blank"><img src="https://s3.amazonaws.com/product_banners/eMember-banner-125.gif" alt="WordPress Membership Plugin" border="0" /></a>
        <a href='http://www.magicaffiliateplugin.com?affid=438' target='_blank'><img src='http://www.magicaffiliateplugin.com/img/mga-125x125.gif' alt='Magic Affiliate Plugin' height='125' width='125' border='0'></a>
        </div>
        
    </div>
	
	<?php
	
}

// Execute admin init hook
function iecsssupport_admin_init() {
		
		global $support;
		
		if( isset( $_POST['iecsssupport_add'] ) ) {
			
			// Add stylesheet
			$support->add_path( $_POST['base-path'] . $_POST['path'] );
			
		}
		
		if( isset( $_GET['remove'] ) ) {
		
			// Remove added stylesheet
			$support->remove_path( $_GET['remove'] );
		
		}
		
		// Add option styles
		wp_enqueue_style( 'iecsssupport-style' , IESUPPORT_PLUGIN_URL . 'css/style.css' , false , '2.0.0' , 'all' );
		
}
	
add_action( 'admin_init' , 'iecsssupport_admin_init' );

// Add settings menu
function baw_create_menu() {

	add_options_page( 'IE CSS3 Support', 'IE CSS3 Support', 'manage_options', 'ie-csss3-support', 'iesupport_options' );

}

add_action( 'admin_menu', 'baw_create_menu' );

?>