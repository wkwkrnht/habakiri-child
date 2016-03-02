<?php
function habakiri_child_theme_setup(){
		class Habakiri extends Habakiri_Base_Functions{
		// wp_enqueue_scripts は親テーマで定義済なので__construct()でフックさせる必要はありません
		public function wp_enqueue_scripts(){
			// Habakiri の wp_enqueue_scripts をまず実行する
			parent::wp_enqueue_scripts();
			// Habakiri が標準でロードする子テーマの style.cssを解除しstyle.min.css を読み込む
			wp_deregister_style( get_stylesheet() );
			wp_enqueue_style(
				get_stylesheet(),
				get_stylesheet_directory_uri() . '/style.min.css',
				array( get_template() )
			);
		}
	}
}
add_action('after_setup_theme','habakiri_child_theme_setup');

//add mimetype
add_filter('upload_mimes','set_mime_types');
function set_mime_types($mimes) {$mimes['svg'] = 'image/svg+xml';return $mimes;}

// jqueryを同梱の物からcdnに
if (!is_admin()){wp_deregister_script('jquery');wp_enqueue_script('jquery','http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js',array(),'1.7.1');}

//キーワードをハイライト
function wps_highlight_results($text){if(is_search()){$sr = get_query_var('s');$keys = explode(" ",$sr);$text = preg_replace('/('.implode('|', $keys) .')/iu','<span class="search-highlight">'.$sr.'</span>',$text);}return $text;}
add_filter('the_title','wps_highlight_results');
add_filter('the_content','wps_highlight_results');

// タグクラウドパラメータ変更
function my_tag_cloud_filter($args){$myargs = array('smallest' => 12, 'largest' => 35, 'number' => 0,  );return $myargs;}
add_filter('widget_tag_cloud_args','my_tag_cloud_filter');

//Android Chrome不具合対策
function replace_nbsp_to_ensp($the_content){if(is_singular()){$the_content = str_replace('&nbsp;','&ensp;',$the_content);}return $the_content;}
add_filter('the_content','replace_nbsp_to_ensp');

//タブレットをモバイルとしないモバイル判定関数
if(!function_exists('is_mobile')):
//スマホ表示分岐
function is_mobile(){
  if(is_page_cache_enable()){return false;}
  if(is_tablet_mobile()){return wp_is_mobile();}
  $useragents = array('iPhone','iPod','Windows.*Phone',
    'Android.*Mobile',
		'blackberry9500','blackberry9530','blackberry9520','blackberry9550','blackberry9800',
    'dream', // Pre 1.5 Android
    'CUPCAKE', // 1.5+ Android
    'webOS', // Palm Pre Experimental
    'incognito', // Other iPhone browser
    'webmate' // Other iPhone browser
  );
  $pattern = '/'.implode('|',$useragents).'/i';
  return preg_match($pattern,$_SERVER['HTTP_USER_AGENT']);
}

// remove jetpack frontend css
add_filter('jetpack_implode_frontend_css','__return_false');

// toc+.js読み込み振り分け
function onze_toc(){wp_deregister_script('toc-front');if(is_single()){if(class_exists('doc')){wp_register_script('toc-front',plugins_url('front.min.js',__FILE__ ),array('jquery'),false,true);}}}
add_action('wp_enqueue_scripts','onze_toc');
