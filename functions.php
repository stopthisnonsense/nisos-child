<?php
function my_theme_enqueue_styles()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
	wp_enqueue_style( 'slick-css', get_stylesheet_directory_uri() . '/slick.css', array(), CHILD_THEME_VERSION );
    wp_enqueue_script( 'slick-js', get_stylesheet_directory_uri() . '/slick.js', array( 'jquery' ), '1.5.3', true );
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');


function mycustomscript_enqueue()
{
    wp_enqueue_script('nisos_main', get_stylesheet_directory_uri() . '/main.js', array('jquery'));
}
add_action('wp_enqueue_scripts', 'mycustomscript_enqueue');

function library_redirect()
{
    global $wp_query;
    $postid = $wp_query->post->ID;
    if (get_field('redirect_link', $postid)) {
        echo '<div id="library-redirect" data-url="'.get_field('redirect_link', $postid).'" />';
    }
}
add_action('wp_footer', 'library_redirect');




add_shortcode( 'vimeo_video_player', 'vimeo_video_player_shortcode' );
function vimeo_video_player_shortcode( $atts )
{
	$videoCode = '';

	global $post;

	$params = array(
        'where'   => 't.ID = ' . $post->ID,
        'limit'   => 1
    );

	$podcast = pods('podcast', $params);
	if($atts['type'] == 'video')
	{
		$podcast = pods('video', $params);
	}

	$videoLink = "";
    if ($podcast->total() > 0)
	{
        while ($podcast->fetch())
		{
			$videoLink = $podcast->display('video_url');
		}
	}

	$videoScript = "https://www.nisos.com/videojs/";


	if(strpos($videoLink, 'youtube') !== false)
	{
		$videoCode = '<iframe width="560" height="315" src="'.$videoLink.'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
    }
	else
	{
		$videoCode .= '<link href="'.$videoScript.'node_modules/video.js/dist/video-js.css" rel="stylesheet">';
		$videoCode .= '<div class="videoContainer"><video id="videoPlayer" class="video-js" data-setup=\'{ "techOrder": ["vimeo"], "sources": [{ "type": "video/vimeo", "src": "'.$videoLink.'"}], "vimeo": { "color": "#fbc51b"} }\'></video></div>';
		$videoCode .= '<script src="'.$videoScript.'node_modules/video.js/dist/video.js"></script>';
		$videoCode .= '<script src="'.$videoScript.'dist/videojs-vimeo.js"></script>';

	}

    return $videoCode;
}


add_shortcode( 'podcast_list', 'podcast_list_shortcode' );
function podcast_list_shortcode( $atts )
{
	$podcasts = "";

	$params = array(
        'where'   => 'podcast_category.slug = "'.$atts['category'].'"',
        'orderby'   => 'episode_date.meta_value DESC',
        'limit'   => 10
    );

    $podcastList = pods('podcast', $params);

    if ($podcastList->total() > 0) {
		$podcasts .= '<div class="podcasts-archive-container">';
		$podcasts .= '<div class="podcasts-container">';
        while ($podcastList->fetch())
		{

			$podcasts .= '<div class="podcast-item">';
			$podcasts .= '	<div class="podcast-episode"><a href="'.$podcastList->display('permalink').'">Episode ' . $podcastList->display('episode') . '<img class="play-icon" src="' . get_stylesheet_directory_uri() . '/images/play.png" /></a></div>';

			$createTitle = wp_strip_all_tags($podcastList->display('post_title'));
			if(strlen($createTitle) > 90)
			{
				$createTitle = substr($createTitle, 0, 90) . '...';
			}

			$podcasts .= '	<div class="podcast-title"><a href="'.$podcastList->display('permalink').'">' . $createTitle . '</a></div>';

			$createExcerpt = wp_strip_all_tags($podcastList->display('post_content'));
			$createExcerpt = substr($createExcerpt, 0, 115);
			$podcasts .= '	<div class="podcast-excerpt">' . $createExcerpt . '...</div>';

			$podcasts .= '	<div class="podcast-date">' . $podcastList->display('episode_date') . '</div>';
			$podcasts .= '	<div class="podcast-bar"> | </div>';
			$podcasts .= '	<div class="podcast-length">' . ($podcastList->display('length') != '' ? $podcastList->display('length') : '1') . ' min</div>';

			$podcasts .= '	<div class="podcast-cc">' . ($podcastList->display('show_cc') == 'Yes' ? '<img src="' . get_stylesheet_directory_uri() . '/images/subtitle.png" title="Closed Captioned" alt="Closed Captioned" />' : '') . '</div>';
			$podcasts .= '	<div class="podcast-transcript">' . ($podcastList->display('show_transcription') == 'Yes' ? '<img src="' . get_stylesheet_directory_uri() . '/images/transcription.png" title="Transcription" alt="Transcription" />' : '') . '</div>';

			$podcasts .= '</div>';
        }
		$podcasts .= '</div>';
		$podcasts .= $podcastList->pagination(array('type' => 'simple' , 'prev_text' => 'Previous', 'next_text' => 'Next'));
		$podcasts .= '</div>';

    }

    return $podcasts;
}

/*================================================

#Load custom Blog  Module

================================================*/

function divi_custom_blog_module() {

    get_template_part( '/includes/Blog' );

    $myblog = new custom_ET_Builder_Module_Blog();

    remove_shortcode( 'et_pb_blog' );

    add_shortcode( 'et_pb_blog', array( $myblog, '_render' ) );

}

add_action( 'et_builder_ready', 'divi_custom_blog_module' );


/*================================================

# Adding ACF Site Options Page

================================================*/

if( function_exists('acf_add_options_page') ) {

	acf_add_options_page(array(
		'page_title' 	=> 'Site Options',
		'menu_title'	=> 'Site Options',
		'menu_slug' 	=> 'site-options-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));

}

/*================================================

# Creating Shortcodes for Inserting Podcast Service Links

================================================*/

function podcast_service_icon_function( $atts = array() ) {

	$links = get_field('podcast_links', 'option');

	extract(shortcode_atts(array(
      'service' => ''
    ), $atts));

	$serviceIcon = $links[$service];
    return '<a href="' . $serviceIcon['link'] . '"><img src="' . $serviceIcon['icon']['url'] . '"
	alt="' . $serviceIcon['icon']['alt'] . '" width="35" height="35" class="alignnone size-full" /></a>';

} add_shortcode('podcasticon', 'podcast_service_icon_function');
