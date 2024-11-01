<?php
/*
Plugin Name: wordTube-FlowPlayer
Plugin URI: http://www.das-motorrad-blog.de/meine-wordpress-plugins/
Version: 0.99beta4
Author: Marc Schieferdecker
Author URI: http://www.das-motorrad-blog.de
Description: The wordTube FlowPlayer plugin brings the FlowPlayer (see flowplayer.org) as player for the popular wordTube plugin. <strong>[<a href="options-general.php?page=wordtube-flowplayer.php">configure</a>]</strong>
License: GPL
*/

// Define plugin path
define( 'WTFLOW_PLUGIN_PATH', ABSPATH . 'wp-content/plugins/wordtube-flowplayer' );
// Define web path
define( 'WTFLOW_WEB_PATH', get_option( 'siteurl' ) . '/wp-content/plugins/wordtube-flowplayer' );
// Define option name
define( 'WTFLOW_PLUGIN_OPTION_NAME', 'wtflow_AdminOptions' );

// Load textdomain for translation files
if( function_exists( 'load_plugin_textdomain' ) ) {
	load_plugin_textdomain( 'wtflowPlayer', 'wp-content/plugins/wordtube-flowplayer' );
}

/**
 * Shortcode hook for posts
 */
function wtflow_shortcode( $atts )
{
	$wtflowOptions = get_option( WTFLOW_PLUGIN_OPTION_NAME );

	extract( shortcode_atts( array(
		'id' 		=> 0,
		'width'		=> 0,
		'height'	=> 0
	), $atts ) );

	$id = intval( $id );
	$width = intval( $width );
	$height = intval( $height );

	if( !empty( $id ) )
	{
		global $wpdb;
		$wpdb -> wordtube = $wpdb -> prefix . 'wordtube';
		$dbresult = $wpdb -> get_row( 'SELECT * FROM ' . $wpdb -> wordtube . ' WHERE vid = "' . $id . '";' );
		if( is_object( $dbresult ) )
		{
			/**
			 * Setting video dimensions: First apply wordTube setting, overwrite them with WT flowplayer settings
			 * if something valid is set, then overwrite if shortcode attribs are submitted.
			 */
			// Set wordTube default width and height (width and height is no longer stored in the database by wordTube :()
			$wordtube_options = get_option( 'wordtube_options' );
			$dbresult -> width = $wordtube_options[ 'media_width' ];
			$dbresult -> height = $wordtube_options[ 'media_height' ];
			// Overwrite with own width and height settings if configured
			if( !empty( $wtflowOptions[ 'default_width' ] ) )
				$dbresult -> width = $wtflowOptions[ 'default_width' ];
			if( !empty( $wtflowOptions[ 'default_height' ] ) )
				$dbresult -> height = $wtflowOptions[ 'default_height' ];
			// Overwrite width/height setting if shortcode attribs given
			if( !empty( $width ) )
				$dbresult -> width = $width;
			if( !empty( $height ) )
				$dbresult -> height = $height;
			// If no dimensions given correct it with a default value
			$dbresult -> width = (!$dbresult -> width ? 480 : $dbresult -> width);
			$dbresult -> height = (!$dbresult -> height ? 320 : $dbresult -> height);

			// If RSS feed, only try to output the preview image with a link, not the html and js code!
			if( is_feed() )
			{
				if( !empty( $dbresult -> image ) )
					$return = "<p><a href=\"{$dbresult->file}\" title=\"{$dbresult->name}\"><img src=\"{$dbresult->image}\" alt=\"{$dbresult->name}\"/></a></p>";
				else
					$return = "<p><a href=\"{$dbresult->file}\" title=\"{$dbresult->name}\">{$dbresult->name}</a></p>";
				return $return;
			}

			// FlowPlayer freeware version code
			if( empty( $wtflowOptions[ 'flowplayer_license_key' ] ) )
			{
				// Container
				$return  = "<a id=\"flowplayer$id\" href=\"javascript:void(0);\" style=\"display:block;width:{$dbresult->width}px;height:{$dbresult->height}px\">\n";
				$return .= "</a>\n";
				// JS
				$return .= "<script type=\"text/javascript\">\n";
				$return .= "flowplayer(\"flowplayer$id\", \"" . WTFLOW_WEB_PATH . "/flowplayer/flowplayer-3.1.0.swf\", \n";
				$return .= "	{\n";
				$return .= "		version: [9, 115],\n";
				$return .= "		onFail: function() {\n";
				$return .= "			document.getElementById('flowplayer$id').innerHTML = 'You need the latest Flash version to see MP4 movies. Your version is ' + this.getVersion();\n";
				$return .= "		},\n";
				$return .= "		onFullscreen: function () {\n";
  				$return .= "			this.getClip().update({scaling: 'fit'});\n";
  				$return .= "			this.getPlugin('canvas').css({backgroundImage : ''});\n";
				$return .= "		},\n";
				$return .= "		onFullscreenExit: function () {\n";
  				$return .= "			this.getClip().update({scaling: 'scale'});\n";
				$return .= "		},\n";
				$return .= "		clip: {\n";
				$return .= "			onBeforeBegin: function() {\n";
  				$return .= "				jQuery.ajax({type: 'POST', data: 'id=" . $id . "',url: '" . WTFLOW_WEB_PATH . "/counter.php'})\n";
				$return .= "			},\n";
				$return .= "			url : \"{$dbresult->file}\",\n";
				$return .= "			autoPlay : " . $wtflowOptions[ 'clip_autoplay' ] . ",\n";
				$return .= "			autoBuffering : " . $wtflowOptions[ 'clip_autobuffering' ] . ",\n";
				$return .= "			bufferLength : " . $wtflowOptions[ 'clip_bufferlength' ] . "\n";
				$return .= "		},\n";
				$return .= "		plugins: {\n";
				$return .= "			controls: {\n";

				if( !empty( $wtflowOptions[ 'controls_borderradius' ] ) )
					$return .= "				borderRadius : '" . $wtflowOptions[ 'controls_borderradius' ] . "',\n";
				if( !empty( $wtflowOptions[ 'controls_border' ] ) )
					$return .= "				border : '" . $wtflowOptions[ 'controls_border' ] . "',\n";

				if( !empty( $wtflowOptions[ 'controls_bottom' ] ) )
					$return .= "				bottom : '" . $wtflowOptions[ 'controls_bottom' ] . "',\n";
				if( !empty( $wtflowOptions[ 'controls_top' ] ) )
					$return .= "				top : '" . $wtflowOptions[ 'controls_top' ] . "',\n";
				if( !empty( $wtflowOptions[ 'controls_left' ] ) )
					$return .= "				left : '" . $wtflowOptions[ 'controls_left' ] . "',\n";
				if( !empty( $wtflowOptions[ 'controls_right' ] ) )
					$return .= "				right : '" . $wtflowOptions[ 'controls_right' ] . "',\n";
				if( !empty( $wtflowOptions[ 'controls_height' ] ) )
					$return .= "				height : '" . $wtflowOptions[ 'controls_height' ] . "',\n";
				if( !empty( $wtflowOptions[ 'controls_width' ] ) )
					$return .= "				width : '" . $wtflowOptions[ 'controls_width' ] . "',\n";

				if( !empty( $wtflowOptions[ 'controls_backgroundcolor' ] ) )
					$return .= "				backgroundColor : '" . $wtflowOptions[ 'controls_backgroundcolor' ] . "',\n";
				if( !empty( $wtflowOptions[ 'controls_backgroundimage' ] ) )
					$return .= "				backgroundImage : 'url(" . urlencode( $wtflowOptions[ 'controls_backgroundimage' ] ) . ")',\n";
				$return .= "				backgroundRepeat : '" . $wtflowOptions[ 'controls_backgroundrepeat' ] . "',\n";
				if( !empty( $wtflowOptions[ 'controls_backgroundgradient' ] ) )
					if( strpos( $wtflowOptions[ 'controls_backgroundgradient' ], '[' ) !== false )
						$return .= "				backgroundGradient : " . $wtflowOptions[ 'controls_backgroundgradient' ] . ",\n";
					else
						$return .= "				backgroundGradient : '" . $wtflowOptions[ 'controls_backgroundgradient' ] . "',\n";

				if( !empty( $wtflowOptions[ 'controls_opacity' ] ) )
					$return .= "				opacity : " . $wtflowOptions[ 'controls_opacity' ] . ",\n";
				if( !empty( $wtflowOptions[ 'controls_fontcolor' ] ) )
					$return .= "				fontColor: '" . $wtflowOptions[ 'controls_fontcolor' ] . "',\n";
				if( !empty( $wtflowOptions[ 'controls_timefontcolor' ] ) )
					$return .= "				timeFontColor: '" . $wtflowOptions[ 'controls_timefontcolor' ] . "',\n";
				$return .= "				autoHide : '" . $wtflowOptions[ 'controls_autohide' ] . "',\n";

				if( !empty( $wtflowOptions[ 'controls_play' ] ) )
					$return .= "				play : true,\n";
				else
					$return .= "				play : false,\n";
				if( !empty( $wtflowOptions[ 'controls_volume' ] ) )
					$return .= "				volume : true,\n";
				else
					$return .= "				volume : false,\n";
				if( !empty( $wtflowOptions[ 'controls_mute' ] ) )
					$return .= "				mute : true,\n";
				else
					$return .= "				mute : false,\n";
				if( !empty( $wtflowOptions[ 'controls_time' ] ) )
					$return .= "				time : true,\n";
				else
					$return .= "				time : false,\n";
				if( !empty( $wtflowOptions[ 'controls_stop' ] ) )
					$return .= "				stop : true,\n";
				else
					$return .= "				stop : false,\n";
				if( !empty( $wtflowOptions[ 'controls_playlist' ] ) )
					$return .= "				playlist : true,\n";
				else
					$return .= "				playlist : false,\n";
				if( !empty( $wtflowOptions[ 'controls_fullscreen' ] ) )
					$return .= "				fullscreen : true,\n";
				else
					$return .= "				fullscreen : false,\n";

	   			$return .= "			}\n";
				$return .= "		},\n";
				$return .= "		canvas: {\n";
				if( !empty( $dbresult -> image ) && $wtflowOptions[ 'canvas_backgroundimage' ] == 'true' )
					$return .= "			backgroundImage : 'url(" . urlencode( $dbresult -> image ) . ")',\n";
				else
				if( !empty( $wtflowOptions[ 'canvas_backgroundimage_fixed' ] ) )
					$return .= "			backgroundImage : 'url(" . urlencode( $wtflowOptions[ 'canvas_backgroundimage_fixed' ] ) . ")',\n";
				if( !empty( $wtflowOptions[ 'canvas_backgroundcolor' ] ) )
					$return .= "			backgroundColor : '',\n";
				if( !empty( $wtflowOptions[ 'canvas_backgroundgradient' ] ) )
					$return .= "			backgroundGradient : [0,0],\n";
				if( !empty( $wtflowOptions[ 'canvas_border' ] ) )
					$return .= "			border : '0px solid #000000',\n";
				$return .= "		}\n";
				$return .= "	}\n";
				$return .= ");\n";
				$return .= "</script>\n";
			}
			// FlowPlayer commercial version code
			else
			{
				// Container
				$return  = "<a id=\"flowplayer$id\" href=\"javascript:void(0);\" style=\"display:block;width:{$dbresult->width}px;height:{$dbresult->height}px\">\n";
				$return .= "</a>\n";
				// JS
				$return .= "<script type=\"text/javascript\">\n";
				$return .= "flowplayer(\"flowplayer$id\", \"" . WTFLOW_WEB_PATH . "/flowplayer_commercial/flowplayer.commercial-3.1.0.swf\",\n";
				$return .= "	{\n";
				$return .= "		key: '" . $wtflowOptions[ 'flowplayer_license_key' ] . "',\n";
				$return .= "		version: [9, 115],\n";
				$return .= "		onFail: function() {\n";
				$return .= "			document.getElementById('flowplayer$id').innerHTML = 'You need the latest Flash version to see MP4 movies. Your version is ' + this.getVersion();\n";
				$return .= "		},\n";
				$return .= "		onFullscreen: function () {\n";
  				$return .= "			this.getClip().update({scaling: 'fit'});\n";
  				$return .= "			this.getPlugin('canvas').css({backgroundImage : ''});\n";
				$return .= "		},\n";
				$return .= "		onFullscreenExit: function () {\n";
  				$return .= "			this.getClip().update({scaling: 'scale'});\n";
				$return .= "		},\n";
				$return .= "		clip: {\n";
				$return .= "			onBeforeBegin: function() {\n";
  				$return .= "				jQuery.ajax({type: 'POST', data: 'id=" . $id . "',url: '" . WTFLOW_WEB_PATH . "/counter.php'})\n";
				$return .= "			},\n";
				$return .= "			url : \"{$dbresult->file}\",\n";
				$return .= "			autoPlay : " . $wtflowOptions[ 'clip_autoplay' ] . ",\n";
				$return .= "			autoBuffering : " . $wtflowOptions[ 'clip_autobuffering' ] . ",\n";
				$return .= "			bufferLength : " . $wtflowOptions[ 'clip_bufferlength' ] . "\n";
				$return .= "		},\n";
				if( !empty( $wtflowOptions[ 'logo_src' ] ) )
				{
					$return .= "		logo: {\n";
					$return .= "			url: '" . $wtflowOptions[ 'logo_src' ] . "',\n";
					$return .= "			fullscreenOnly: " . $wtflowOptions[ 'logo_fullscreenonly' ] . ",\n";
					if( !empty( $wtflowOptions[ 'logo_top' ] ) )
						$return .= "			top: " . intval( $wtflowOptions[ 'logo_top' ] ) . ",\n";
					if( !empty( $wtflowOptions[ 'logo_right' ] ) )
						$return .= "			right: " . intval( $wtflowOptions[ 'logo_right' ] ) . ",\n";
					if( !empty( $wtflowOptions[ 'logo_displaytime' ] ) )
						$return .= "			displayTime: " . intval( $wtflowOptions[ 'logo_displaytime' ] ) . ",\n";
					$return .= "		},\n";
				}
				if( !empty( $wtflowOptions[ 'playbutton_src' ] ) )
				{
					$return .= "		play: {\n";
					$return .= "			url: '" . $wtflowOptions[ 'playbutton_src' ] . "',\n";
					if( !empty( $wtflowOptions[ 'playbutton_width' ] ) )
						$return .= "			width: " . intval( $wtflowOptions[ 'playbutton_width' ] ) . ",\n";
					if( !empty( $wtflowOptions[ 'playbutton_height' ] ) )
						$return .= "			height: " . intval( $wtflowOptions[ 'playbutton_height' ] ) . ",\n";
					$return .= "		},\n";
				}
				if( !empty( $wtflowOptions[ 'context_title' ] ) )
				{
					$return .= "		contextMenu: [\n";
					$return .= "			'" . $wtflowOptions[ 'context_title' ] . "'\n";
					if( is_array( $wtflowOptions[ 'context_menu' ] ) && count( $wtflowOptions[ 'context_menu' ] ) )
					{
						foreach( $wtflowOptions[ 'context_menu' ] AS $menu_entry )
						{
							$return .= "			,{'" . $menu_entry[ 'context_link_text' ] . "': function() {\n";
							$return .= "				location.href = '" . $menu_entry[ 'context_link_href' ] . "';\n";
							$return .= "			}}\n";
						}
					}
					$return .= "		],\n";
				}
				$return .= "		plugins: {\n";
				$return .= "			controls: {\n";

				if( !empty( $wtflowOptions[ 'controls_borderradius' ] ) )
					$return .= "				borderRadius : '" . $wtflowOptions[ 'controls_borderradius' ] . "',\n";
				if( !empty( $wtflowOptions[ 'controls_border' ] ) )
					$return .= "				border : '" . $wtflowOptions[ 'controls_border' ] . "',\n";

				if( !empty( $wtflowOptions[ 'controls_bottom' ] ) )
					$return .= "				bottom : '" . $wtflowOptions[ 'controls_bottom' ] . "',\n";
				if( !empty( $wtflowOptions[ 'controls_top' ] ) )
					$return .= "				top : '" . $wtflowOptions[ 'controls_top' ] . "',\n";
				if( !empty( $wtflowOptions[ 'controls_left' ] ) )
					$return .= "				left : '" . $wtflowOptions[ 'controls_left' ] . "',\n";
				if( !empty( $wtflowOptions[ 'controls_right' ] ) )
					$return .= "				right : '" . $wtflowOptions[ 'controls_right' ] . "',\n";
				if( !empty( $wtflowOptions[ 'controls_height' ] ) )
					$return .= "				height : '" . $wtflowOptions[ 'controls_height' ] . "',\n";
				if( !empty( $wtflowOptions[ 'controls_width' ] ) )
					$return .= "				width : '" . $wtflowOptions[ 'controls_width' ] . "',\n";

				if( !empty( $wtflowOptions[ 'controls_backgroundcolor' ] ) )
					$return .= "				backgroundColor : '" . $wtflowOptions[ 'controls_backgroundcolor' ] . "',\n";
				if( !empty( $wtflowOptions[ 'controls_backgroundimage' ] ) )
					$return .= "				backgroundImage : 'url(" . urlencode( $wtflowOptions[ 'controls_backgroundimage' ] ) . ")',\n";
				$return .= "				backgroundRepeat : '" . $wtflowOptions[ 'controls_backgroundrepeat' ] . "',\n";
				if( !empty( $wtflowOptions[ 'controls_backgroundgradient' ] ) )
					if( strpos( $wtflowOptions[ 'controls_backgroundgradient' ], '[' ) !== false )
						$return .= "				backgroundGradient : " . $wtflowOptions[ 'controls_backgroundgradient' ] . ",\n";
					else
						$return .= "				backgroundGradient : '" . $wtflowOptions[ 'controls_backgroundgradient' ] . "',\n";

				if( !empty( $wtflowOptions[ 'controls_opacity' ] ) )
					$return .= "				opacity : " . $wtflowOptions[ 'controls_opacity' ] . ",\n";
				if( !empty( $wtflowOptions[ 'controls_fontcolor' ] ) )
					$return .= "				fontColor: '" . $wtflowOptions[ 'controls_fontcolor' ] . "',\n";
				if( !empty( $wtflowOptions[ 'controls_timefontcolor' ] ) )
					$return .= "				timeFontColor: '" . $wtflowOptions[ 'controls_timefontcolor' ] . "',\n";
				$return .= "				autoHide : '" . $wtflowOptions[ 'controls_autohide' ] . "',\n";

				if( !empty( $wtflowOptions[ 'controls_play' ] ) )
					$return .= "				play : true,\n";
				else
					$return .= "				play : false,\n";
				if( !empty( $wtflowOptions[ 'controls_volume' ] ) )
					$return .= "				volume : true,\n";
				else
					$return .= "				volume : false,\n";
				if( !empty( $wtflowOptions[ 'controls_mute' ] ) )
					$return .= "				mute : true,\n";
				else
					$return .= "				mute : false,\n";
				if( !empty( $wtflowOptions[ 'controls_time' ] ) )
					$return .= "				time : true,\n";
				else
					$return .= "				time : false,\n";
				if( !empty( $wtflowOptions[ 'controls_stop' ] ) )
					$return .= "				stop : true,\n";
				else
					$return .= "				stop : false,\n";
				if( !empty( $wtflowOptions[ 'controls_playlist' ] ) )
					$return .= "				playlist : true,\n";
				else
					$return .= "				playlist : false,\n";
				if( !empty( $wtflowOptions[ 'controls_fullscreen' ] ) )
					$return .= "				fullscreen : true,\n";
				else
					$return .= "				fullscreen : false,\n";

	   			$return .= "			}\n";
				$return .= "		},\n";
				$return .= "		canvas: {\n";
				if( !empty( $dbresult -> image ) && $wtflowOptions[ 'canvas_backgroundimage' ] == 'true' )
					$return .= "			backgroundImage : 'url(" . urlencode( $dbresult -> image ) . ")',\n";
				else
				if( !empty( $wtflowOptions[ 'canvas_backgroundimage_fixed' ] ) )
					$return .= "			backgroundImage : 'url(" . urlencode( $wtflowOptions[ 'canvas_backgroundimage_fixed' ] ) . ")',\n";
				if( !empty( $wtflowOptions[ 'canvas_backgroundcolor' ] ) )
					$return .= "			backgroundColor : '',\n";
				if( !empty( $wtflowOptions[ 'canvas_backgroundgradient' ] ) )
					$return .= "			backgroundGradient : [0,0],\n";
				if( !empty( $wtflowOptions[ 'canvas_border' ] ) )
					$return .= "			border : '0px solid #000000',\n";
				$return .= "		}\n";
				$return .= "	}\n";
				$return .= ");\n";
				$return .= "</script>\n";
			}

			// Correct JS [fix for IE 6, 7]
			$return = preg_replace( "/,[^a-z0-9\}\]]+(\}|\])/", " }", $return );

			return $return;
		}
		else	return 'Video not found!';
	}
}
add_shortcode( 'flowplayer', 'wtflow_shortcode' );
add_shortcode( 'FLOWPLAYER', 'wtflow_shortcode' );
add_shortcode( 'Flowplayer', 'wtflow_shortcode' );
add_shortcode( 'FlowPlayer', 'wtflow_shortcode' );

/**
 * Wrapper function to make the shortcode callable via php
 */
function flowplayer( $id, $width = 0, $height = 0, $return = false )
{
	$args = array( 'id' => $id, 'width' => $width, 'height' => $height );
	$player = wtflow_shortcode( $args );
	if( $return == false )
		echo $player;
	else
		return $player;
}

/**
 * Page hooks (css/js)
 */
function add_flowplay_js_to_page()
{
	$wtflowOptions = get_option( WTFLOW_PLUGIN_OPTION_NAME );

	if( empty( $wtflowOptions[ 'flowplayer_license_key' ] ) )
		print "\t" . '<script type="text/javascript" src="' . WTFLOW_WEB_PATH . '/flowplayer/flowplayer-3.1.0.min.js"></script>' . "\n";
	else
		print "\t" . '<script type="text/javascript" src="' . WTFLOW_WEB_PATH . '/flowplayer_commercial/flowplayer-3.1.0.min.js"></script>' . "\n";
}
add_action( 'wp_head', 'add_flowplay_js_to_page' );


/**
 * Admin hooks if on admin page
 */
if( strpos( $_SERVER[ 'REQUEST_URI' ], 'wp-admin' ) !== false )
{
	// Include Admin Class
	require( WTFLOW_PLUGIN_PATH . '/wtflow-admin.class.php' );
	$wtflow_admin = new wtflow_admin();
	// Admin wrapper
	function wrapper_wtflow_adminpage()
	{
		global $wtflow_admin;
		add_options_page( 'wordTube FlowPlayer', 'wordTube FlowPlayer', 9, basename(__FILE__), array( &$wtflow_admin, 'wtflow_AdminPage' ) );
	}
	add_action( 'admin_menu', 'wrapper_wtflow_adminpage' );
}

?>