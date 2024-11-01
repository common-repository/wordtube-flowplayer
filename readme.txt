=== wordTube FlowPlayer ===
Contributors: Marc Schieferdecker, raufaser
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=m_schieferdecker%40hotmail%2ecom&item_name=wordTube%20FlowPlayer%20wp%20plugin&no_shipping=0&no_note=1&tax=0&currency_code=EUR&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: wordtube,flowplayer,flv,video,mp4
Requires at least: 2.6
Tested up to: 2.8.3
Stable tag: 0.99beta4

The wordTube FlowPlayer plugin brings you the FlowPlayer (see flowplayer.org) as player for the popular wordTube plugin by Alex Rabe.

== Description ==

You don't like the JW FLV Media Player or want to play MP4 movies? Okay, here is an alternate player: the FlowPlayer. And the very best: This Plugin integrates with the popular wordTube plugin. You can still manage your videos using wordTube and then use the FlowPlayer with an alternate shortcode to play a video (playlists coming soon).

There is an administration page to style the FlowPlayer to your needs (free version and commercial version supported!).

**Example shortcode:** [flowplayer id=1 width=480 height=320] or simply [flowplayer id=1]

You can also use the flowplayer in your theme, a post or your sidebar with a PHP tag (install plugin exec-php!):
`
<?php
/**
 * Usage: flowplayer( $id, $width = 0, $height = 0, $return = false )
 * Params:
 * id = wordTube video id (required)
 * width = width of video (0 = use defaults, integer = use own width)
 * height = height of video (0 = use defaults, integer = use own height)
 * return = output html code or return it (true = return html, false = echo html)
 */

// Example 1: Use default with and height setting, just display a video
flowplayer( 43 );

// Example 2: Use own width and height
flowplayer( 43, 320, 200 );

// Example 3: Get html code into variable to do something with the code...
$playerhtml = flowplayer( 43, 0, 0, true );

?>
`

**Important:** wordTube is required to use this plugin. Get it here: http://wordpress.org/extend/plugins/wordtube/

== Installation ==

Just install the plugin and activate it. Then use the new shortcode [flowplayer id=1] instead of the [MEDIA=1] shortcode of wordTube and your wordTube video will be played with FlowPlayer.

You can also use the shortcode [flowplayer id=1 width=480 height=320] if you do not want to use the dimensions you set in the database of wordTube and display the FlowPlayer with alternative dimensions.

== Frequently Asked Questions ==

= Playlists? =

Not supported yet, coming soon. We're still beta.

= Is the statistic counter increased? =

Yes, if you play a video with the flowplayer, the play counter of the video is increased in wordTube database table.

= Can I use the plugin without wordTube? =

No, wordTube by Alex Rabe is **required**!

= Can I configure and style the FlowPlayer with this plugin? =

Yes, have a look at the administration page.

= Is the FlowPlayer included when I download this plugin? =

Yes, the latest FlowPlayer is included. Be sure to check the LICENSE and the README located in /wp-content/plugins/wordtube-flowplayer/flowplayer(_commercial) - further informations you get here: http://flowplayer.org/

== Screenshots ==

1. Here you see an example (http://mopeten.tv - a german motorcycle video magazin)
2. This is a part of the administration page
