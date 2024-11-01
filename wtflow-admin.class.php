<?php

if( !defined( 'WTFLOW_PLUGIN_OPTION_NAME' ) )
	define( 'WTFLOW_PLUGIN_OPTION_NAME', 'wtflow_AdminOptions' );

// The main plugin class
class wtflow_admin
{
	var $wtflow_AdminOptionsName;
	var $wtflow_AdminOptions;

	// Construct
	function __construct()
	{
		$this -> wtflow_AdminOptionsName	= WTFLOW_PLUGIN_OPTION_NAME;
		$this -> wtflow_AdminOptions		= $this -> wtflow_GetAdminOptions();
	}
	// PHP4 compatibe construct (please update to PHP 5 soon! ;) )
	function wtflow_admin() { $this -> __construct(); }

	// Get options for this plugin
	function wtflow_GetAdminOptions()
	{
		// Set default options
		$wtflowOptions = array(	'flowplayer_license_key' => '',
					'playbutton_src' => '',
					'playbutton_width' => 83,
					'playbutton_height' => 83,
					'logo_src' => '',
					'logo_displaytime' => 10,
					'logo_top' => 2,
					'logo_right' => 2,
					'logo_fullscreenonly' => 'false',
					'context_title' => '',
					'context_menu' => array(),
					'clip_autoplay' => 'false',
					'clip_autobuffering' => 'false',
					'clip_bufferlength' => '6',
					'controls_borderradius' => '20',
					'controls_border' => '0px solid #000000',
					'controls_bottom' => '2',
					'controls_top' => '',
					'controls_left' => '2',
					'controls_right' => '2',
					'controls_height' => '20',
					'controls_width' => '99%',
					'controls_backgroundcolor' => 'transparent',
					'controls_backgroundimage' => '',
					'controls_backgroundrepeat' => 'no-repeat',
					'controls_backgroundgradient' => 'medium',
					'controls_opacity' => '0.9',
					'controls_fontcolor' => '#ffffff',
					'controls_timefontcolor' => '#333333',
					'controls_autohide' => 'always',
					'controls_play' => 'true',
					'controls_volume' => 'true',
					'controls_mute' => 'true',
					'controls_time' => 'true',
					'controls_stop' => 'true',
					'controls_playlist' => 'false',
					'controls_fullscreen' => 'true',
					'canvas_backgroundimage' => 'true',
					'canvas_backgroundimage_fixed' => '',
					'canvas_backgroundcolor' => '',
					'canvas_backgroundgradient' => '[0,0]',
					'canvas_border' => '0px solid #000000',
					'default_width' => '0',
					'default_height' => '0'
				);
		// Load existing options
		$_wtflowOptions = get_option( $this -> wtflow_AdminOptionsName );
		// Overwrite defaults
		$update = false;
		if( is_array( $_wtflowOptions ) && count( $_wtflowOptions ) )
		{
			foreach( $_wtflowOptions AS $oKey => $oVal )
				$wtflowOptions[ $oKey ] = $oVal;
		}
		// Set default options to wp db if no existing options or new options are found
		if( !count( $_wtflowOptions ) || count( $_wtflowOptions ) != count( $wtflowOptions ) || $update )
			update_option( $this -> wtflow_AdminOptionsName, $wtflowOptions );
		// Return options
		return $wtflowOptions;
	}

	// Adminpage
	function wtflow_AdminPage()
	{
		$open = intval( $_REQUEST[ 'open' ] );

		// Set config
		if( $_POST[ 'wtflow_admin_action' ] == 'set' )
		{
			$wtflowOptionsNew = array();
			foreach( array_keys( $this -> wtflow_AdminOptions ) AS $oKey )
			{
				if( $oKey != 'context_menu' )
				{
					$wtflowOptionsNew[ $oKey ] = (!eregi( "[^0-9,.]", $_POST[ $oKey ] ) ? str_replace( ',', '.', $_POST[ $oKey ] ) : $_POST[ $oKey ]);
				}
				else
				{
					$wtflowOptionsNew[ $oKey ] = array();
					if( is_array( $_POST[ 'context_link_text' ] ) && is_array( $_POST[ 'context_link_href' ] ) )
					{
						foreach( $_POST[ 'context_link_text' ] AS $tkey => $context_link_text )
						{
							$context_link_href = $_POST[ 'context_link_href' ][ $tkey ];
							if( !empty( $context_link_text ) && !empty( $context_link_href ) )
								$wtflowOptionsNew[ $oKey ][] = array( 'context_link_text' => $context_link_text, 'context_link_href' => $context_link_href );
						}
					}
					if( !empty( $_POST[ 'add_context_link_text' ] ) && !empty( $_POST[ 'add_context_link_href' ]) )
					{
						$wtflowOptionsNew[ $oKey ][] = array( 'context_link_text' => $_POST[ 'add_context_link_text' ], 'context_link_href' => $_POST[ 'add_context_link_href' ] );
					}
				}
			}
			update_option( $this -> wtflow_AdminOptionsName, $wtflowOptionsNew );
			$this -> wtflow_AdminOptions = $wtflowOptionsNew;
			print "<div id=\"message\" class=\"updated fade\"><p><strong>" . __( "Options updated.", "wtflowPlayer" ) . "</strong></p></div>";
		}

		// Container
		print "<div class=\"wrap\">\n";
		print "<h2>" . __( "wordTube FlowPlayer Plugin", "wtflowPlayer" ) . "</h2>\n";
		print "<br class=\"clear\"/>";
		print "<form name=\"wtflowAdminPage\" method=\"post\" action=\"options-general.php?page=wordtube-flowplayer.php\" enctype=\"multipart/form-data\">\n";

		/**
		 * Comercial version settings
		 */
		// Output setup form
		print "<div id=\"poststuff\">\n";
		print "<div class=\"postbox\">\n";
		print "<input type=\"hidden\" name=\"wtflow_admin_action\" value=\"set\"/>\n";
		print "<h3>" . __( "wordTube FlowPlayer options (commercial version, license key required)", "wtflowPlayer" ) . "</h3>\n";
		print "<div class=\"inside\">\n";

		// Options for the commercial player
		print "<h4>" . __( "Options for the licensed commercial version of FlowPlayer", "wtflowPlayer" ) . "</h4>\n";
		print "<table class=\"form-table\">\n";

		// Key
		print "<tr valign=\"top\"><td colspan=\"2\"><h4 style=\"border-bottom:1px solid #999\">" . __( "License key", "wtflowPlayer" ) . "</h4></td></tr>";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Enter your license key to use the commercial version of flowplayer. If you don't have one and want to purchase one, click here: ", "wtflowPlayer" ) . "<a href=\"http://www.flowplayer.org/download/\">http://www.flowplayer.org/download/</a>";
		print "</td><td><input type=\"text\" name=\"flowplayer_license_key\" size=\"30\" maxlength=\"400\" value=\"" . $this -> wtflow_AdminOptions[ 'flowplayer_license_key' ] . "\"/></td></tr>\n";

		// Playbutton settings
		print "<tr valign=\"top\"><td colspan=\"2\"><h4 style=\"border-bottom:1px solid #999\">" . __( "Play button", "wtflowPlayer" ) . "</h4></td></tr>";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Play button image source (leave blank to use standard button)", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"playbutton_src\" size=\"60\" maxlength=\"400\" value=\"" . $this -> wtflow_AdminOptions[ 'playbutton_src' ] . "\"/></td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Width of the play button image in pixel", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"playbutton_width\" size=\"3\" maxlength=\"4\" value=\"" . $this -> wtflow_AdminOptions[ 'playbutton_width' ] . "\"/></td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Height of the play button image in pixel", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"playbutton_height\" size=\"3\" maxlength=\"4\" value=\"" . $this -> wtflow_AdminOptions[ 'playbutton_height' ] . "\"/></td></tr>\n";

		// Logo settings
		print "<tr valign=\"top\"><td colspan=\"2\"><h4 style=\"border-bottom:1px solid #999\">" . __( "Logo", "wtflowPlayer" ) . "</h4></td></tr>";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Logo image source (leave blank to use no logo image)", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"logo_src\" size=\"60\" maxlength=\"400\" value=\"" . $this -> wtflow_AdminOptions[ 'logo_src' ] . "\"/></td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Margin in pixel to top border of player window", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"logo_top\" size=\"3\" maxlength=\"4\" value=\"" . $this -> wtflow_AdminOptions[ 'logo_top' ] . "\"/></td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Margin in pixel to right border of player window", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"logo_right\" size=\"3\" maxlength=\"4\" value=\"" . $this -> wtflow_AdminOptions[ 'logo_right' ] . "\"/></td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Display logo only in fullscreen mode?", "wtflowPlayer" );
		print "</td><td>";
		print "<select name=\"logo_fullscreenonly\">\n";
			print "<option value=\"false\" " . ($this -> wtflow_AdminOptions[ 'logo_fullscreenonly' ] == 'false' ? "selected=\"selected\"" : "") . ">" . __( "No, display always", "wtflowPlayer" ) . "</option>\n";
			print "<option value=\"true\" " . ($this -> wtflow_AdminOptions[ 'logo_fullscreenonly' ] == 'true' ? "selected=\"selected\"" : "") . ">" . __( "Yes, only in fullscreen mode", "wtflowPlayer" ) . "</option>\n";
		print "</select>\n";
		print "</td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Display time in seconds before the logo will be hidden", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"logo_displaytime\" size=\"3\" maxlength=\"4\" value=\"" . $this -> wtflow_AdminOptions[ 'logo_displaytime' ] . "\"/></td></tr>\n";

		// Context menu
		print "<tr valign=\"top\"><td colspan=\"2\"><h4 style=\"border-bottom:1px solid #999\">" . __( "Context menu", "wtflowPlayer" ) . "</h4></td></tr>";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Title of your context menu (leave blank if you dont want to use this feature)", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"context_title\" size=\"30\" maxlength=\"64\" value=\"" . $this -> wtflow_AdminOptions[ 'context_title' ] . "\"/></td></tr>\n";
		print "<tr valign=\"top\"><td colspan=\"2\"><b>" . __( "Current player context menu entrys", "wtflowPlayer" ) . "</b></td></tr>";
		if( is_array( $this -> wtflow_AdminOptions[ 'context_menu' ] ) && count( $this -> wtflow_AdminOptions[ 'context_menu' ] ) )
		{
			print "<tr valign=\"top\"><td colspan=\"2\">" . __( "To delete an entry, just blank the link text or url.", "wtflowPlayer" ) . "</td></tr>";
			foreach( $this -> wtflow_AdminOptions[ 'context_menu' ] AS $key => $menu_entry )
			{
				print "<tr valign=\"top\"><td width=\"50%\" style=\"background-color:#FAFAFA\">";
				print __( "Link text", "wtflowPlayer" ) . " #" . ($key+1);
				print "</td><td style=\"background-color:#FAFAFA\"><input type=\"text\" name=\"context_link_text[]\" size=\"40\" maxlength=\"400\" value=\"" . $menu_entry[ 'context_link_text' ] . "\"/></td></tr>\n";
				print "<tr valign=\"top\"><td width=\"50%\" style=\"background-color:#FAFAFA;border-bottom:2px solid #FFF\">";
				print __( "Link target (URL)", "wtflowPlayer" ) . " #" . ($key+1);
				print "</td><td style=\"background-color:#FAFAFA;border-bottom:2px solid #FFF\"><input type=\"text\" name=\"context_link_href[]\" size=\"40\" maxlength=\"400\" value=\"" . $menu_entry[ 'context_link_href' ] . "\"/></td></tr>\n";
			}
		}
		else 	print "<tr valign=\"top\"><td colspan=\"2\">No own context menu entrys yet</td></tr>";
		print "<tr valign=\"top\"><td colspan=\"2\"><b>" . __( "Add a new context menu entry", "wtflowPlayer" ) . "</b></td></tr>";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Link text", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"add_context_link_text\" size=\"40\" maxlength=\"400\" value=\"\"/></td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Link target (URL)", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"add_context_link_href\" size=\"40\" maxlength=\"400\" value=\"\"/></td></tr>\n";

		// Submit options
		print "<tr valign=\"top\"><td colspan=\"2\"><h4 style=\"border-bottom:1px solid #999\">" . __( "Save this configuration", "wtflowPlayer" ) . "</h4></td></tr>";
		print "<tr valign=\"top\"><td colspan=\"2\"><div class=\"submit\"><input type=\"submit\" name=\"update_AdminOptions\" value=\"" . __( "Update options", "wtflowPlayer" ) . "\"/></div></td></tr>";
		print "</table>\n";
		print "</div>\n";
		print "</div>\n";
		print "</div>\n";


		/**
		 * Free and comercial version settings
		 */
		// Set general options
		print "<div id=\"poststuff\">\n";
		print "<div class=\"postbox\">\n";
		print "<h3>" . __( "wordTube FlowPlayer options (non-commercial and commercial version, no key required)", "wtflowPlayer" ) . "</h3>\n";
		print "<div class=\"inside\" style=\"line-height:160%;font-size:1em;\">\n";

		// Options for both player versions
		print "<h4>" . __( "Options used by both - the free and the commercial version of FlowPlayer", "wtflowPlayer" ) . "</h4>\n";
		print "<table class=\"form-table\">\n";

		// Default width and height
		print "<tr valign=\"top\"><td colspan=\"2\"><h4 style=\"border-bottom:1px solid #999\">" . __( "Default width and height of player (overwrites wordTube settings)" ) . "</h4></td></tr>";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Default video width in pixel (Enter 0 to use wordTube settings, otherwise wordTube settings will be overwritten. Shortcode attributes always overwrites all settings!)", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"default_width\" size=\"5\" maxlength=\"5\" value=\"" . $this -> wtflow_AdminOptions[ 'default_width' ] . "\"/> " . __( "default:", "wtflowPlayer" ) . " 0</td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Default video height in pixel (Enter 0 to use wordTube settings, otherwise wordTube settings will be overwritten. Shortcode attributes always overwrites all settings!)", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"default_height\" size=\"5\" maxlength=\"5\" value=\"" . $this -> wtflow_AdminOptions[ 'default_height' ] . "\"/> " . __( "default:", "wtflowPlayer" ) . " 0</td></tr>\n";

		// Clip settings
		print "<tr valign=\"top\"><td colspan=\"2\"><h4 style=\"border-bottom:1px solid #999\">" . __( "Clip settings", "wtflowPlayer" ) . "</h4></td></tr>";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Auto play after loading the player?", "wtflowPlayer" );
		print "</td><td>";
		print "<select name=\"clip_autoplay\">\n";
			print "<option value=\"false\" " . ($this -> wtflow_AdminOptions[ 'clip_autoplay' ] == 'false' ? "selected=\"selected\"" : "") . ">" . __( "No", "wtflowPlayer" ) . "</option>\n";
			print "<option value=\"true\" " . ($this -> wtflow_AdminOptions[ 'clip_autoplay' ] == 'true' ? "selected=\"selected\"" : "") . ">" . __( "Yes", "wtflowPlayer" ) . "</option>\n";
		print "</select>\n";
		print "</td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Buffer length of a clip in seconds", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"clip_bufferlength\" size=\"3\" maxlength=\"4\" value=\"" . $this -> wtflow_AdminOptions[ 'clip_bufferlength' ] . "\"/></td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Activate automatic buffering?", "wtflowPlayer" );
		print "</td><td>";
		print "<select name=\"clip_autobuffering\">\n";
			print "<option value=\"false\" " . ($this -> wtflow_AdminOptions[ 'clip_autobuffering' ] == 'false' ? "selected=\"selected\"" : "") . ">" . __( "No", "wtflowPlayer" ) . "</option>\n";
			print "<option value=\"true\" " . ($this -> wtflow_AdminOptions[ 'clip_autobuffering' ] == 'true' ? "selected=\"selected\"" : "") . ">" . __( "Yes", "wtflowPlayer" ) . "</option>\n";
		print "</select>\n";
		print "</td></tr>\n";

		// Controlbar setup
		print "<tr valign=\"top\"><td colspan=\"2\"><h4 style=\"border-bottom:1px solid #999\">" . __( "Control bar settings", "wtflowPlayer" ) . "</h4></td></tr>";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Border of control bar", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"controls_border\" size=\"15\" maxlength=\"400\" value=\"" . $this -> wtflow_AdminOptions[ 'controls_border' ] . "\"/> " . __( "e.g.:", "wtflowPlayer" ) . " 2px solid #FF0000</td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Border radius (rounded edges)", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"controls_borderradius\" size=\"3\" maxlength=\"4\" value=\"" . $this -> wtflow_AdminOptions[ 'controls_borderradius' ] . "\"/> " . __( "default:", "wtflowPlayer" ) . " 20</td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Margin from bottom in pixel (leave empty if top margin is set)", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"controls_bottom\" size=\"3\" maxlength=\"4\" value=\"" . $this -> wtflow_AdminOptions[ 'controls_bottom' ] . "\"/> " . __( "default:", "wtflowPlayer" ) . " 2</td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Margin from top in pixel (leave empty if bottom margin is set)", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"controls_top\" size=\"3\" maxlength=\"4\" value=\"" . $this -> wtflow_AdminOptions[ 'controls_top' ] . "\"/> " . __( "default:", "wtflowPlayer" ) . " </td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Margin from left in pixel", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"controls_left\" size=\"3\" maxlength=\"4\" value=\"" . $this -> wtflow_AdminOptions[ 'controls_left' ] . "\"/> " . __( "default:", "wtflowPlayer" ) . " 2</td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Margin from right in pixel", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"controls_right\" size=\"3\" maxlength=\"4\" value=\"" . $this -> wtflow_AdminOptions[ 'controls_right' ] . "\"/> " . __( "default:", "wtflowPlayer" ) . " 2</td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Height of the control bar in pixel or percent", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"controls_height\" size=\"3\" maxlength=\"4\" value=\"" . $this -> wtflow_AdminOptions[ 'controls_height' ] . "\"/> " . __( "default:", "wtflowPlayer" ) . " 20</td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Height of the control bar in pixel or percent", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"controls_width\" size=\"3\" maxlength=\"4\" value=\"" . $this -> wtflow_AdminOptions[ 'controls_width' ] . "\"/> " . __( "default:", "wtflowPlayer" ) . " 99%</td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Background color", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"controls_backgroundcolor\" size=\"15\" maxlength=\"400\" value=\"" . $this -> wtflow_AdminOptions[ 'controls_backgroundcolor' ] . "\"/> " . __( "e.g.:", "wtflowPlayer" ) . " transparent, black, #FF0000</td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Background image", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"controls_backgroundimage\" size=\"25\" maxlength=\"400\" value=\"" . $this -> wtflow_AdminOptions[ 'controls_backgroundimage' ] . "\"/><br/>" . __( "e.g.:", "wtflowPlayer" ) . " http://www.xyz.de/images/bg_control.png</td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Background repeat?", "wtflowPlayer" );
		print "</td><td>";
		print "<select name=\"controls_backgroundrepeat\">\n";
			print "<option value=\"no-repeat\" " . ($this -> wtflow_AdminOptions[ 'controls_backgroundrepeat' ] == 'no-repeat' ? "selected=\"selected\"" : "") . ">" . __( "No", "wtflowPlayer" ) . "</option>\n";
			print "<option value=\"repeat\" " . ($this -> wtflow_AdminOptions[ 'controls_backgroundrepeat' ] == 'repeat' ? "selected=\"selected\"" : "") . ">" . __( "Yes", "wtflowPlayer" ) . "</option>\n";
		print "</select>\n";
		print "</td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Background gradient", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"controls_backgroundgradient\" size=\"15\" maxlength=\"400\" value=\"" . $this -> wtflow_AdminOptions[ 'controls_backgroundgradient' ] . "\"/> " . __( "e.g.:", "wtflowPlayer" ) . " none, low, medium, high, [1,0], [0,0.7]</td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Overall opacity (1 = solid, 0 = transparent)", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"controls_opacity\" size=\"5\" maxlength=\"8\" value=\"" . $this -> wtflow_AdminOptions[ 'controls_opacity' ] . "\"/> " . __( "default:", "wtflowPlayer" ) . " 0.9</td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Font color", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"controls_fontcolor\" size=\"15\" maxlength=\"400\" value=\"" . $this -> wtflow_AdminOptions[ 'controls_fontcolor' ] . "\"/> " . __( "e.g.:", "wtflowPlayer" ) . " green, black, #FF0000</td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Timeline font color", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"controls_timefontcolor\" size=\"15\" maxlength=\"400\" value=\"" . $this -> wtflow_AdminOptions[ 'controls_timefontcolor' ] . "\"/> " . __( "e.g.:", "wtflowPlayer" ) . " green, black, #FF0000</td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Autohide control bar?", "wtflowPlayer" );
		print "</td><td>";
		print "<select name=\"controls_autohide\">\n";
			print "<option value=\"never\" " . ($this -> wtflow_AdminOptions[ 'controls_autohide' ] == 'never' ? "selected=\"selected\"" : "") . ">" . __( "No", "wtflowPlayer" ) . "</option>\n";
			print "<option value=\"always\" " . ($this -> wtflow_AdminOptions[ 'controls_autohide' ] == 'always' ? "selected=\"selected\"" : "") . ">" . __( "Yes", "wtflowPlayer" ) . "</option>\n";
		print "</select>\n";
		print "</td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Define buttons to show...", "wtflowPlayer" );
		print "</td><td>";
		print "<input type=\"checkbox\" name=\"controls_stop\" value=\"true\" " . ($this -> wtflow_AdminOptions[ 'controls_stop' ] == 'true' ? "checked=\"checked\"" : "") . "/> " . __( "Stop", "wtflowPlayer" ) . "<br/>";
		print "<input type=\"checkbox\" name=\"controls_play\" value=\"true\" " . ($this -> wtflow_AdminOptions[ 'controls_play' ] == 'true' ? "checked=\"checked\"" : "") . "/> " . __( "Play", "wtflowPlayer" ) . "<br/>";
		print "<input type=\"checkbox\" name=\"controls_time\" value=\"true\" " . ($this -> wtflow_AdminOptions[ 'controls_time' ] == 'true' ? "checked=\"checked\"" : "") . "/> " . __( "Time", "wtflowPlayer" ) . "<br/>";
		print "<input type=\"checkbox\" name=\"controls_volume\" value=\"true\" " . ($this -> wtflow_AdminOptions[ 'controls_volume' ] == 'true' ? "checked=\"checked\"" : "") . "/> " . __( "Volume", "wtflowPlayer" ) . "<br/>";
		print "<input type=\"checkbox\" name=\"controls_mute\" value=\"true\" " . ($this -> wtflow_AdminOptions[ 'controls_mute' ] == 'true' ? "checked=\"checked\"" : "") . "/> " . __( "Mute", "wtflowPlayer" ) . "<br/>";
		print "<input type=\"checkbox\" name=\"controls_fullscreen\" value=\"true\" " . ($this -> wtflow_AdminOptions[ 'controls_fullscreen' ] == 'true' ? "checked=\"checked\"" : "") . "/> " . __( "Fullscreen", "wtflowPlayer" ) . "<br/>";
		print "<input type=\"checkbox\" name=\"controls_playlist\" value=\"true\" " . ($this -> wtflow_AdminOptions[ 'controls_playlist' ] == 'true' ? "checked=\"checked\"" : "") . "/> " . __( "Playlist", "wtflowPlayer" ) . "<br/>";
		print "</td></tr>\n";

		// Canvas settings
		print "<tr valign=\"top\"><td colspan=\"2\"><h4 style=\"border-bottom:1px solid #999\">" . __( "Canvas settings", "wtflowPlayer" ) . "</h4></td></tr>";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Show preview image if set in wordTube?", "wtflowPlayer" );
		print "</td><td>";
		print "<select name=\"canvas_backgroundimage\">\n";
			print "<option value=\"false\" " . ($this -> wtflow_AdminOptions[ 'canvas_backgroundimage' ] == 'false' ? "selected=\"selected\"" : "") . ">" . __( "No", "wtflowPlayer" ) . "</option>\n";
			print "<option value=\"true\" " . ($this -> wtflow_AdminOptions[ 'canvas_backgroundimage' ] == 'true' ? "selected=\"selected\"" : "") . ">" . __( "Yes", "wtflowPlayer" ) . "</option>\n";
		print "</select>\n";
		print "</td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Or: Use a fixed background image if none set in wordTube", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"canvas_backgroundimage_fixed\" size=\"25\" maxlength=\"400\" value=\"" . $this -> wtflow_AdminOptions[ 'canvas_backgroundimage_fixed' ] . "\"/><br/>" . __( "e.g.:", "wtflowPlayer" ) . " http://www.xyz.de/images/fix_preview_clip.png</td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Background color", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"canvas_backgroundcolor\" size=\"15\" maxlength=\"400\" value=\"" . $this -> wtflow_AdminOptions[ 'canvas_backgroundcolor' ] . "\"/> " . __( "e.g.:", "wtflowPlayer" ) . " transparent, black, #FF0000</td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Background gradient", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"canvas_backgroundgradient\" size=\"15\" maxlength=\"400\" value=\"" . $this -> wtflow_AdminOptions[ 'canvas_backgroundgradient' ] . "\"/> " . __( "e.g.:", "wtflowPlayer" ) . " none, low, medium, high, [1,0], [0,0.7]</td></tr>\n";
		print "<tr valign=\"top\"><td width=\"50%\">";
		print __( "Border of player", "wtflowPlayer" );
		print "</td><td><input type=\"text\" name=\"canvas_border\" size=\"15\" maxlength=\"400\" value=\"" . $this -> wtflow_AdminOptions[ 'canvas_border' ] . "\"/> " . __( "e.g.:", "wtflowPlayer" ) . " 2px solid #FF0000</td></tr>\n";

		print "<tr valign=\"top\"><td colspan=\"2\"><h4 style=\"border-bottom:1px solid #999\">" . __( "Save this configuration", "wtflowPlayer" ) . "</h4></td></tr>";
		print "<tr valign=\"top\"><td colspan=\"2\"><div class=\"submit\"><input type=\"submit\" name=\"update_AdminOptions2\" value=\"" . __( "Update options", "wtflowPlayer" ) . "\"/></div></td></tr>";
		print "</table>";
		print "</div>";
		print "</div>\n";
		print "</div>\n";
		print "</form>\n";

		// Donate link and support informations
		print "<div id=\"poststuff\">\n";
		print "<div class=\"postbox\">\n";
		print "<h3>" . __( "Donate &amp; support", "wtflowPlayer" ) . "</h3>\n";
		print "<div class=\"inside\" style=\"line-height:160%;font-size:1em;\">\n";
		print __( "Please", "wtflowPlayer" ) . " <a href=\"https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&amp;business=m_schieferdecker%40hotmail%2ecom&amp;item_name=wtflowPlayer%20wp%20plugin&amp;no_shipping=0&amp;no_note=1&amp;tax=0&amp;currency_code=EUR&amp;bn=PP%2dDonationsBF&amp;charset=UTF%2d8\">" . __( "DONATE", "wtflowPlayer" ) . "</a> " . __( "if you like this plugin.", "wtflowPlayer" ) . "<br/>";
		print "<br/>" . __( "If you need support, want to report bugs or suggestions, drop me an ", "wtflowPlayer" ) . " <a href=\"mailto:m_schieferdecker@hotmail.com\">" . __( "email", "wtflowPlayer" ) . "</a> " . __( "or visit the", "wtflowPlayer" ) . " <a href=\"http://www.das-motorrad-blog.de/meine-wordpress-plugins\">" . __( "plugin homepage", "wtflowPlayer" ) . "</a>.<br/>";
		print "<br/>" . __( "Greetings to Alex Rabe and many thanks for developing the wordTube plugin. :-)", "wtflowPlayer" ) . "<br/>";
		print "<br/>" . __( "Translations: ", "wtflowPlayer" ) . " Marc Schieferdecker (Deutsch, upcoming on final release!)<br/>";
		print "<br/>" . __( "And this persons I thank for a donation:", "wtflowPlayer" ) . " none yet! ;)<br/>";
		print "<br/>" . __( "Final statements: Code is poetry. Motorcycles are cooler than cars.", "wtflowPlayer" );
		print "</div>";
		print "</div>\n";
		print "</div>\n";

		// Close container
		print "</div>\n";

		// Nice display
		if( version_compare( substr($wp_version, 0, 3), '2.6', '<' ) )
		{
?>
		<script type="text/javascript">
		//<!--
			var wtflow_openPanel = <?php print $open; ?>;
			var wtflow_PanelCounter = 1;
			jQuery('.postbox h3').prepend('<a class="togbox">+</a> ');
			jQuery('.postbox h3').click( function() { jQuery(jQuery(this).parent().get(0)).toggleClass('closed'); } );
			jQuery('.postbox h3').each(function() {
				if( (wtflow_PanelCounter++) != wtflow_openPanel )
					jQuery(jQuery(this).parent().get(0)).toggleClass('closed');
			});
		//-->
		</script>
		<style type="text/css">
			h4 {
				margin-bottom:0em;
			}
		</style>
<?php
		}
	}
}

?>