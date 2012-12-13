<?php
/*
Plugin Name: sponsor
Plugin URI: http://www.google.be
Description: This plugin adds space to put sponsor logos
Version: 1.0
Author: Kurt Geebelen
Author URI: http://www.google.be
License: GPLv2 or later
*/

/*  Copyright 2013  Kurt Geebelen (email : kurtgeebelen@gmail.com)

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

//options initialization
if(strlen(get_option('sponsor_logo_width'))==0){update_option('sponsor_logo_width',"130");}
if(strlen(get_option('sponsor_logo_vspace'))==0){update_option('sponsor_logo_vspace',"10");}
if(strlen(get_option('sponsor_logo_content_width'))==0){update_option('sponsor_logo_content_width',"960");}
if(strlen(get_option('sponsor_logo_positionfromtop'))==0){update_option('sponsor_logo_positionfromtop',"40");}
if(strlen(get_option('sponsor_logo_positionfromcontent'))==0){update_option('sponsor_logo_positionfromcontent',"10");}
if(strlen(get_option('sponsor_numberofsponsors'))==0){update_option('sponsor_numberofsponsors',"1");}
$sponsor_images = array(
		'1' => '',
	);
$sponsor_urls = array(
		'1' => '',
	);
update_option('sponsor_images',$sponsor_images);
update_option('sponsor_urls',$sponsor_urls);

//hooks
//add_action( 'wp_head', 'sponsor_add_css' );
//add_action( 'admin_head', 'sponsor_add_css' );
add_action( 'admin_menu', 'sponsor_menu' );
add_action( 'wp_footer','display_sponsors' );
//add_shortcode( 'soccer', 'display_sponsors');

//functions
//function sponsor_add_css()
//{
//echo '<link rel="stylesheet" type="text/css" media="all" href="'.WP_PLUGIN_URL.'/soccer-field/css/style.css" />';
//}

function sponsor_menu() {
	add_options_page( 'Sponsors', 'Sponsors', 'manage_options', 'sponsor_options', 'sponsor_options' );
}

function sponsor_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}   
    // Save options if user has posted some information
    if(isset($_POST[sponsorwidth]) or isset($_POST[sponsorvspace])){        
        $sponsorwidth=$_POST[sponsorwidth];
	$sponsorvspace=$_POST[sponsorvspace];
	$sponsorcontentwidth=$_POST[sponsorcontentwidth];
	$sponsorpositionfromtop=$_POST[sponsorpositionfromtop];
	$sponsorpositionfromcontent=$_POST[sponsorpositionfromcontent];
	$sponsor_numberofsponsors=$_POST[sponsor_numberofsponsors];
	$sponsor_numberofsponsors_old=(int)get_option('sponsor_numberofsponsors');

        //check if is an hexadecimal RGB color
        $error=0;
	if(!is_numeric($sponsorwidth)){$error=1;}
	if(!is_numeric($sponsorvspace)){$error=1;}
	if(!is_numeric($sponsorcontentwidth)){$error=1;}
	if(!is_numeric($sponsorpositionfromtop)){$error=1;}
	if(!is_numeric($sponsorpositionfromcontent)){$error=1;}
	if(!is_numeric($sponsor_numberofsponsors)){$error=1;}
        if($error==0){
			// Save into database
			update_option('sponsor_logo_width',$sponsorwidth);
			update_option('sponsor_logo_vspace',$sponsorvspace);
			update_option('sponsor_logo_content_width',$sponsorcontentwidth);
			update_option('sponsor_logo_positionfromtop',$sponsorpositionfromtop);
			update_option('sponsor_logo_positionfromcontent',$sponsorpositionfromcontent);
			update_option('sponsor_numberofsponsors',$sponsor_numberofsponsors);

			echo '<p class="sf-saved">Your options have been saved</p>';
		}else{
			//show error message
			echo '<p class="sf-saved">Only positive integers are allowed</p>';
		}
	$sponsor_images = get_option('sponsor_images');
	$sponsor_urls = get_option('sponsor_urls');
	for($i=1;$i<=min($sponsor_numberofsponsors_old,$sponsor_numberofsponsors);$i++){
		$sponsor_images[(string)$i]=$_POST['sponsorlogoimageurl_'.(string)$i];
		$sponsor_urls[(string)$i]=$_POST['sponsorlogositeurl_'.(string)$i];
	}
	update_option('sponsor_images',$sponsor_images);
	update_option('sponsor_urls',$sponsor_urls);
	}
	echo '<h2>Sponsor Options</h2>';
	echo '<form method="post" action="">';
	echo '<input autocomplete="off" size="6" type="text" name="sponsorwidth" value="'.get_option('sponsor_logo_width').'"><span>Sponsor logo width (in pixels)</span><br />';
	echo '<input autocomplete="off" size="6" type="text" name="sponsorvspace" value="'.get_option('sponsor_logo_vspace').'"><span>Vertical space between the logos (in pixels)</span><br />';
	echo '<input autocomplete="off" size="6" type="text" name="sponsorcontentwidth" value="'.get_option('sponsor_logo_content_width').'"><span>The widht of the content of your site (in pixels)</span><br />';
	echo '<input autocomplete="off" size="6" type="text" name="sponsorpositionfromtop" value="'.get_option('sponsor_logo_positionfromtop').'"><span>The position from the top of the page for the first logo (in pixels)</span><br />';
	echo '<input autocomplete="off" size="6" type="text" name="sponsorpositionfromcontent" value="'.get_option('sponsor_logo_positionfromcontent').'"><span>The space between the content and the logos (in pixels)</span><br /> <br />	';
	echo '<input autocomplete="off" size="6" type="text" name="sponsor_numberofsponsors" value="'.get_option('sponsor_numberofsponsors').'"><span>The number of sponsors you want</span><br /> <br />';
	echo 'Fill in below the image location of the sponsor logos that you would like to show, and the web site to which it should link (leave empty if none). <br /> <br />';	
	$sponsor_images = get_option('sponsor_images');
	$sponsor_urls = get_option('sponsor_urls');
	for($i=1;$i<=(int)get_option('sponsor_numberofsponsors');$i++){
		
		echo '<span>Logo '.$i.'</span>&nbsp;&nbsp; <input autocomplete="off" size="20" type="text" name="sponsorlogoimageurl_'.$i.'" value="'.$sponsor_images[(string)$i].'"><span>The url of the image to be shown</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input autocomplete="off" size="20" type="text" name="sponsorlogositeurl_'.$i.'" value="'.$sponsor_urls[(string)$i].'"><span>The link of the sponsor&#146s web site</span> <br />';
	}

	echo '<input type="submit" value="Save">';
	echo '</form>';	
}
 function display_sponsors(  ) {
	$positionfromtop=(int) get_option("sponsor_logo_positionfromtop");
	$logowidth=(int) get_option("sponsor_logo_width");
	$contentwidth=(int) get_option("sponsor_logo_content_width");
	$positionfromcontent=(int) get_option("sponsor_logo_positionfromcontent");
	$logovspace=(int) get_option("sponsor_logo_vspace");
	$sponsor_images = get_option('sponsor_images');
	$sponsor_urls = get_option('sponsor_urls');
	
	$margin_left=-$contentwidth/2-$logowidth-$positionfromcontent;
	echo '<div style="position:fixed; top:'.$positionfromtop.'px; left:50%; margin-left:'.$margin_left.'px;width:'.$logowidth.'px">';

	for($i=1;$i<=(int)get_option('sponsor_numberofsponsors');$i++){
			if( strcmp("",str_replace(" ","",$sponsor_urls[(string)$i])) ){
				echo '<a style="position:relative;margin-top:'.$logovspace.'px;display:block;" href="'.$sponsor_urls[(string)$i].'" target="_blank"><img src="'.$sponsor_imagess[(string)$i].'" width="'.$logowidth.'px"></a>';
			}else{
				echo '<a style="position:relative;display:block;"><img src="'.$sponsor_imagess[(string)$i].'" width="'.$logowidth.'px"></a>';
			}
		}
	echo '</div>';	

}

?>
