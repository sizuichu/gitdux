<?php 
/**
 * 百度熊掌号 粉丝关注+添加JSON_LD数据+实时推送
 * 代码由 themebetter.com 提供
 */

// 熊掌号 是否启用
function tb_xzh_on(){
	if( _hui('xzh_on') && _hui('xzh_appid') && wp_is_mobile() ){
		return true;
	}
	return false;
}

function tb_xzh_is_original(){
	global $post;
	$isoriginal = get_post_meta($post->ID, 'is_original', true);
	if( _hui('xzh_on') && $isoriginal ){
		return true;
	}
	return false;
}

// 熊掌号 粉丝关注 声明
function tb_xzh_head_var(){
	echo (tb_xzh_on()&&(_hui('xzh_render_head')||_hui('xzh_render_body')||_hui('xzh_render_tail'))) ? '<script src="//msite.baidu.com/sdk/c.js?appid='. _hui('xzh_appid') .'"></script>' : '';
}

// 熊掌号 粉丝关注 吸顶bar
function tb_xzh_render_head(){
	echo (tb_xzh_on()&&_hui('xzh_render_head')) ? "<div class='xzh-render-head'><script>cambrian.render('head')</script></div>" : '';
}

// 熊掌号 粉丝关注 文章段落间bar
function tb_xzh_render_body(){
	echo (tb_xzh_on()&&_hui('xzh_render_body')) ? "<div class='xzh-render-body'><script>cambrian.render('body')</script></div>" : '';
}

// 熊掌号 粉丝关注 底部bar
function tb_xzh_render_tail(){
	echo (tb_xzh_on()&&_hui('xzh_render_tail')) ? "<div class='xzh-render-tail'><script>cambrian.render('tail')</script></div>" : '';
}


// 熊掌号 添加JSON_LD数据
add_action('wp_head', 'tb_xzh_jsonld', 20, 1);
function tb_xzh_jsonld() {
	if ( _hui('xzh_on') && _hui('xzh_appid') && ((is_single()&&_hui('xzh_jsonld_single')) || (is_page()&&_hui('xzh_jsonld_page'))) ){
		echo '<script type="application/ld+json">
    {
        "@context": "https://ziyuan.baidu.com/contexts/cambrian.jsonld",
        "@id": "'.get_the_permalink().'",
        "appid": "'._hui('xzh_appid').'",
        "title": "'.get_the_title().strip_tags(get_the_subtitle()).'",
        "images": ['.tb_xzh_post_imgs().'],
        "description": "'.tb_xzh_post_excerpt().'",
        "pubDate": "'.get_the_time('Y-m-d\TH:i:s').'"
    }
</script>'."\n";
	}
}

// 熊掌号 获取文章摘要
function tb_xzh_post_excerpt($len=120){
	global $post;
	$post_content = '';
	if ($post->post_excerpt) {
		$post_content  = $post->post_excerpt;
	} else {
		if(preg_match('/<p>(.*)<\/p>/iU',trim(strip_tags($post->post_content,"<p>")),$result)){
			$post_content = $result['1'];
		} else {
			$post_content_r = explode("\n",trim(strip_tags($post->post_content)));
			$post_content = $post_content_r['0'];
		}
	}
	$excerpt = preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,0}'.'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s','$1',$post_content);
	return str_replace(array("\r\n", "\r", "\n"), "", $excerpt);
}

// 熊掌号 获取文章图片
function tb_xzh_post_imgs(){
	if( _hui('xzh_jsonld_img') ) return '';
	global $post;
	$src = '';
	$content = $post->post_content;  
	preg_match_all('/<img .*?src=[\"|\'](.+?)[\"|\'].*?>/', $content, $strResult, PREG_PATTERN_ORDER);  
	$n = count($strResult[1]);  
	if($n >= 3){
		$src = $strResult[1][0].'","'.$strResult[1][1].'","'.$strResult[1][2];
		$src = '"'.$src.'"';
	}elseif($n >= 1){
		$src = $strResult[1][0];
		$src = '"'.$src.'"';
	}
	return $src;
}

// 熊掌号 新文章发布时实时推送
add_action('publish_post', 'tb_xzh_post_to_baidu');
function tb_xzh_post_to_baidu() {
	if( _hui('xzh_on') && _hui('xzh_appid') && _hui('xzh_post_on') && _hui('xzh_post_token') ){
	    global $post;
	    $plink = get_permalink($post->ID);
	    if( 'publish' !== $post->post_status && $plink ){
	    	$isoriginal = get_post_meta($post->ID, 'is_original', true);
		    $urls = array();
		    $urls[] = $plink;
			$api = 'http://data.zz.baidu.com/urls?appid='. _hui('xzh_appid') .'&token='. _hui('xzh_post_token') .'&type=realtime';
			if( $isoriginal ){
		    	$api .= ',original';
		    }
			$ch = curl_init();
			$options =  array(
			    CURLOPT_URL => $api,
			    CURLOPT_POST => true,
			    CURLOPT_RETURNTRANSFER => true,
			    CURLOPT_POSTFIELDS => implode("\n", $urls),
			    CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
			);
			curl_setopt_array($ch, $options);
			$result = curl_exec($ch);
			$result = json_decode($result);
			$result_text = '成功';
			if( $result->error ){
				$result_text = '失败 '.$result->message;
			}
			update_post_meta($post->ID, 'xzh_tui_back', $result_text);
		}
	}
}

