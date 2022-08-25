<?php if( ! defined('BASEPATH') ) exit('No direct script access allowed');
/**
 * @author      ICDS
 * 
 *
 **/
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title><?php echo ( isset( $title ) ) ? $title.' || '.WEBSITE_NAME.' || Certificados En Línea' : WEBSITE_NAME.' || Certificados En Línea'; ?></title>
<?php
	// Add any keywords
	echo ( isset( $keywords ) )     ? meta('keywords', $keywords) : '';
	// Add a discription
	echo ( isset( $description ) )  ? meta('description', $description) : '';
	// Add a robots exclusion
	echo ( isset( $no_robots ) )    ? meta('robots', 'noindex,nofollow') : '';
	
	// Favicon
	echo link_tag( array( 'href' => 'img/favicon.ico', 'media' => 'all', 'rel' => 'shortcut icon' ) ) . "\n";
	
	// Always add the main stylesheet
	echo link_tag( array( 'href' => 'css/bootstrap.css', 'media' => 'screen', 'rel' => 'stylesheet' ) ) . "\n";
	echo link_tag( array( 'href' => 'css/font-awesome.css', 'media' => 'screen', 'rel' => 'stylesheet' ) ) . "\n";
	echo link_tag( array( 'href' => 'css/gris/jquery-ui-1.10.3.custom.css', 'media' => 'screen', 'rel' => 'stylesheet' ) ) . "\n";
	
	// Add any additional stylesheets
	if( isset( $style_sheets ) ) {
			foreach( $style_sheets as $href => $media ) {
					echo link_tag( array( 'href' => $href, 'media' => $media, 'rel' => 'stylesheet' ) ) . "\n";
			}
	}
	
	echo link_tag( array( 'href' => 'css/style.css', 'media' => 'screen', 'rel' => 'stylesheet' ) ) . "\n";
	
	// jQuery  always loaded
	echo script_tag( 'js/jquery-1.9.1.js' ) . "\n";
		echo script_tag( 'js/jquery.ui.datepicker-es.min.js' ) . "\n";
	echo script_tag( 'js/jquery-ui-1.10.2.custom.min.js' ) . "\n";
	echo script_tag( 'js/bootstrap.min.js' ) . "\n";
	
	// Add any additional javascript
	if( isset( $javascripts ) ) {
			for( $x=0; $x<=count( $javascripts )-1; $x++ ) {
					echo script_tag( $javascripts["$x"] ) . "\n";
			}
	}
	
	// Add anything else to the head
	echo ( isset( $extra_head ) ) ? $extra_head : '';
?>
</head>

<body>
<?php if ($this->ion_auth->logged_in()) { ?>
<!--
    |
    | ::::::: Header logo and title
    |
-->
<div class="masthead">
  <div class="header"> <?php echo anchor('', img( array( 'src' => 'img/logo.png', 'alt' => WEBSITE_NAME ) ) ) . "\n"; ?>
    <div class="logo-separator"></div>
    <div class="large">Certificados En Línea</div>
  </div>
</div>
<?php } ?>
<!--
        |
        | ::::::: Content
        |
    -->
<div class="container">
  <div id="contents">
    <?php  if ($this->ion_auth->logged_in()){  ?>
    <p> <?php echo $contents ?></p>
    <?php } ?>
  </div>
</div>
<!-- /container --> 
<!--
        |
        | ::::::: Foooter
        |
    --> 
<div class="container">
  <div class="footer">
    <p>Copyright (c) <?php echo date('Y') . ' &bull; SENA. &bull; '; ?></p>
  </div>
</div>
<?php
	// Insert any HTML before the closing body tag if desired
	if( isset( $final_html ) ) {
			echo $final_html;
	}
	// Add the cookie checker
	if( isset( $cookie_checker ) ) {
			echo $cookie_checker;
	}
	
	// Add any javascript before the closing body tag
	if( isset( $dynamic_extras ) ) {
			echo '<script>';
			echo $dynamic_extras;
			echo '</script>';
	}
?>
<script language="javascript" type="text/javascript">
$(document).ready(function() {
	window.location.hash="no";
	window.location.hash="Again-No" //chrome
	window.onhashchange=function(){
		window.location.hash="no";
	}
});
</script>
</body>
</html>
<?php
/* End of file main.php */
/* Location: /application/views/templates/main.php */


