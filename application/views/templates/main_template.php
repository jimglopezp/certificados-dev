<?php if( ! defined('BASEPATH') ) exit('No direct script access allowed');
/**
 * @author     CDS
 *
 *
 **/
?><!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title><?php echo ( isset( $title ) ) ? $title.' || '.WEBSITE_NAME.' || Certificados En Línea' : WEBSITE_NAME.' || Certificados En Línea'; ?></title>
<?php
	// Add any keywords
	echo ( isset( $keywords ) ) ? meta('keywords', $keywords) : '';

	// Add a discription
	echo ( isset( $description ) ) ? meta('description', $description) : '';

	// Add a robots exclusion 
	echo ( isset( $no_robots ) ) ? meta('robots', 'noindex,nofollow') : '';
?>
<base href="<?php echo if_secure_base_url(); ?>" />
<?php
	// Favicon
/*	echo link_tag( array( 'href' => 'img/favicon.ico', 'media' => 'all', 'rel' => 'shortcut icon' ) ) . "\n";
	// Always add the main stylesheet
	echo link_tag( array( 'href' => 'css/jquery.dataTables_themeroller.css', 'media' => 'screen', 'rel' => 'stylesheet' ) ) . "\n";
	echo link_tag( array( 'href' => 'css/bootstrap.min.css', 'media' => 'screen', 'rel' => 'stylesheet' ) ) . "\n";
	echo link_tag( array( 'href' => 'css/chosen.min.css', 'media' => 'screen', 'rel' => 'stylesheet' ) ) . "\n";*/

    echo link_tag( array( 'href' => 'css/redmond/jquery-ui-1.10.3.custom.css', 'media' => 'screen', 'rel' => 'stylesheet' ) ) . "\n";
	// Add any additional stylesheets
	if( isset( $style_sheets ) )
	{
		foreach( $style_sheets as $href => $media )
		{
			echo link_tag( array( 'href' => $href, 'media' => $media, 'rel' => 'stylesheet' ) ) . "\n";
		}
	}

	echo link_tag( array( 'href' => 'css/style.css', 'media' => 'screen', 'rel' => 'stylesheet' ) ) . "\n";

	// jQuery  always loaded
	echo script_tag( 'js/jquery-1.9.1.js' ) . "\n";
		echo script_tag( 'js/jquery.ui.datepicker-es.min.js' ) . "\n";
    echo script_tag( 'js/jquery-ui-1.10.2.custom.min.js' ) . "\n";
    echo script_tag( 'js/menu.js' ) . "\n";
    echo script_tag( 'js/jquery.dataTables.min.js' ) . "\n";
    echo script_tag( 'js/jquery.dataTables.defaults.js' ) . "\n";
  //  echo script_tag( 'js/tinymce/tinymce.min.js' ) . "\n";

	// Add any additional javascript
	if( isset( $javascripts ) )
	{
		for( $x=0; $x<=count( $javascripts )-1; $x++ )
		{
			echo script_tag( $javascripts["$x"] ) . "\n";
		}
	}

	// Add anything else to the head
	echo ( isset( $extra_head ) ) ? $extra_head : '';

	// Add Google Analytics code if available in config
	if( ! empty( $tracking_code ) ) echo $tracking_code;
?>
</head>
<body id="<?php echo $this->router->fetch_class() . '-' . $this->router->fetch_method(); ?>" class="<?php echo $this->router->fetch_class(); ?>-controller <?php echo $this->router->fetch_method(); ?>-method">
<div id="alert-bar">&nbsp;</div>
<div class="wrapper">
	<div id="indicator">
		<div>
			<?php
			    $this->load->helper('date');
			     $format = 'DATE_RFC822';
                 $time = time();
			?>
		</div>
	</div>
	<div class="width-limiter">
		<div id="logo">
			<?php echo anchor('', img( array( 'src' => 'img/mesa.jpg', 'alt' => WEBSITE_NAME ) ) )  . "\n"; ?>
		</div>
    <div id="menu" >
    	<ul id="menui">
      	<li>
        	<?php
						echo ( $this->uri->segment(1) ) ? anchor('/', 'Inicio') : anchor('/', 'Inicio', array( 'id' => 'active' ) );
					?>
        </li>
       	<li>
        	<?php
						echo ( $this->uri->segment(2) == 'usuarios' OR $this->uri->segment(2) == 'users' ) ? secure_anchor('index.php/usuarios', 'Administrar usuarios', array( 'id' => 'active' ) ) : secure_anchor('index.php/usuarios', 'Administrar usuarios');
					?>
        </li>
      </ul>
    </div>
		<div id="two-left" class="content">
			<?php echo ( isset( $content ) ) ? $content : ''; ?>
		</div>
	</div>
</div>
<div class="footer">
	<p>Copyright (c) <?php echo date('Y') . ' &bull; SENA. &bull; '; ?></p>
</div>
<?php
	// Insert any HTML before the closing body tag if desired
	if( isset( $final_html ) )
	{
		echo $final_html;
	}

	// Add the cookie checker
	if( isset( $cookie_checker ) )
	{
		echo $cookie_checker;
	}

	// Add any javascript before the closing body tag
	if( isset( $dynamic_extras ) )
	{
		echo '<script>
		';
		echo $dynamic_extras;
		echo '</script>
		';
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
/* End of file main_template.php */
/* Location: /application/views/templates/main_template.php */
