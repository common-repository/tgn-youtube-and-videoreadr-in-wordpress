<?php
/*
Plugin Name: TGN YouTube and VideoReadr shortcodes
Plugin URI: http://www.thewpwiki.com/extensions/tgn-youtube-and-videoreadr-shortcodes
Description: Embed YouTube videos using [youtube=id] or [youtube=id width height] and VideoReadr videos using [videoreadr=id] or [videoreadr=id width height]

Author: George Vanous
Version: 1.0.2
Author URI: http://www.thewpwiki.com/
*/

function tgn_youtube_video   ($the_content) { return tgn_video_helper($the_content, 'youtube',  'tgn_youTubeElement',  604, 364); }
function tgn_videoreadr_video($the_content) { return tgn_video_helper($the_content, 'videoreadr', 'tgn_videoReadrElement', 604, 560); }

function tgn_video_helper($the_content, $tag, $renderHtml, $defaultWidth, $defaultHeight)
{
  $startTag = '[' . $tag . '='; // [$name=id] or [$name=id width height]

  $start = strpos($the_content, $startTag);

  if ($start !== false)
  {
    $end = strpos($the_content, ']', $start);
    $spose = $start + strlen($startTag);
    $slen = $end - $spose;
    $args = substr($the_content, $spose, $slen);

    $the_args = explode(' ', $args);

    if (1 == sizeof($the_args))
    {
      $file = $args;
      $width = $defaultWidth;
      $height = $defaultHeight;
    }
    else if (3 == sizeof($the_args))
    {
      list($file, $width, $height) = $the_args;
    }

    return tgn_video_helper( substr($the_content, 0, $start) . $renderHtml($file, $width, $height) . substr($the_content, $end + 1), $tag, $renderHtml, $defaultWidth, $defaultHeight );
  }
  else
  {
    return $the_content;
  }
}

function tgn_youTubeElement($youtubeId, $width, $height)
{
  $youtubeId = parseYouTubeId($youtubeId);

  return <<<EOS
<object width="$width" height="$height">
	<param name="movie" value="http://www.youtube.com/v/$youtubeId?fs=1&amp;hl=en_US"></param>
	<param name="allowFullScreen" value="true"></param>
	<param name="allowscriptaccess" value="always"></param>
	<embed src="http://www.youtube.com/v/$youtubeId?fs=1&amp;hl=en_US" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="$width" height="$height"></embed>
</object>
EOS;
}

function parseYouTubeId($url)
{
	preg_match('@(?:\?v=|/v/)([-\w]+)|([-\w]+)$@', $url, $matches);
	return $matches[ count($matches) - 1 ];

//	$values = parse_url( $url, PHP_URL_QUERY );
//	parse_str($values);

//	if (isSet($v))
//	{
//		return $v;
//	}

//	return 'not found';
}

//function parseYouTubeId_test()
//{
// jzNmX69h_HA
// http://www.youtube.com/watch?v=jzNmX69h_HA
// http://www.youtube.com/watch?v=jzNmX69h_HA&feature=player_embedded
// http://www.youtube.com/v/jzNmX69h_HA?fs=1&amp;hl=en_US
// http://www.youtube.com/tgnDragonAge2#p/u/0/jzNmX69h_HA
// http://www.youtube.com/tgnWorldOfWarcraft#p/c/FD3ACB6A1128406A/3/ZXKobFJWuyc
//
//	$s = 'jzNmX69h_HA';
//	echo parseYouTubeId($s);
//	echo '<br><br>';
//
//	$s = 'http://www.youtube.com/watch?v=jzNmX69h_HA';
//	echo parseYouTubeId($s);
//	echo '<br><br>';
//
//	$s = 'http://www.youtube.com/watch?v=jzNmX69h_HA&feature=player_embedded';
//	echo parseYouTubeId($s);
//	echo '<br><br>';
//
//	$s = 'http://www.youtube.com/v/jzNmX69h_HA?fs=1&amp;hl=en_US';
//	echo parseYouTubeId($s);
//	echo '<br><br>';
//
//	$s = 'http://www.youtube.com/user/tgnDragonAge2#p/u/0/jzNmX69h_HA';
//	echo parseYouTubeId($s);
//	echo '<br><br>';
//
//	$s = 'http://www.youtube.com/tgnWorldOfWarcraft#p/c/FD3ACB6A1128406A/3/ZXKobFJWuyc';
//	echo parseYouTubeId($s);
//	echo '<br><br>';
//}

function tgn_videoReadrElement($id, $width, $height)
{
  return tgn_videoReadrElement_javascript($id, $width, $height);
}

function tgn_videoReadrElement_javascript($id, $width, $height)
{
  return <<<EOS

<script src="http://videoreadr.com/read/$id.js?embed=true&width=$width&height=$height&background=%23000&layout=transcript2" type="text/javascript"></script>

EOS;
}

function tgn_videoReadrElement_iframe($id, $width, $height)
{
  return <<<EOS

<iframe src="http://videoreadr.com/read/$id?embed=true&width=$width&height=$height&background=%23000&layout=transcript2" style="width:{$width}px; height:{$height}px" scrolling="no" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0"></iframe>

EOS;
}

add_filter('the_content', 'tgn_youtube_video');
add_filter('the_excerpt', 'tgn_youtube_video');

add_filter('the_content', 'tgn_videoreadr_video');
add_filter('the_excerpt', 'tgn_videoreadr_video');

add_action('wp_head', 'tgn_videoreadr_head');

function tgn_videoreadr_head()
{
  // temporary patch to size videos to their correct height

	echo <<<EOS
<style type="text/css">
.layout_transcript2 .l_left { height:371px !important }
.layout_transcript2 .l_transcript { top:371px !important }
</style>
EOS;
}

?>
