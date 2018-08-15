<?php
// Require theme functions
require get_stylesheet_directory() . '/functions-theme.php';

// Customize your functions



//新建说说功能 
add_action('init', 'my_custom_init');
function my_custom_init()
{ $labels = array( 'name' => '说说',
'singular_name' => '说说',
'add_new' => '发表说说',
'add_new_item' => '发表说说',
'edit_item' => '编辑说说',
'new_item' => '新说说',
'view_item' => '查看说说',
'search_items' => '搜索说说',
'not_found' => '暂无说说',
'not_found_in_trash' => '没有已遗弃的说说',
'parent_item_colon' => '', 'menu_name' => '说说' );
$args = array( 'labels' => $labels,
'public' => true,
'publicly_queryable' => true,
'show_ui' => true,
'show_in_menu' => true,
'exclude_from_search' =>true,
'query_var' => true,
'rewrite' => true, 'capability_type' => 'post',
'has_archive' => false, 'hierarchical' => false,
'menu_position' => null,
'taxonomies'=> array('category','post_tag'),
'supports' => array('editor','author','title', 'custom-fields','comments') );
register_post_type('shuoshuo',$args);
}


function textarea($atts, $content = null) 
{ return '<script src="http://xiaos.life/wp-content/uploads/2018/08/2018081511133998.js" type="text/javascript" charset="utf-8"></script> <form> 
<div align="center"> 
<textarea id="code" style="width:555px;height:200px; border:1px solid #ff0000;" cols="80" rows="15">'.$content.'</textarea> 
<br />
 <input type="button"onclick=runCode(code) value="运行代码" style="border:1px solid #B1B4CD;background:#556b2f;color:#FFF; padding-top:5px;"> 
 <input type="button"onclick=copycode(code) style="border:1px solid #B1B4CD;background:#556b2f;color:#FFF; padding-top:5px;"value="复制代码" onclick="copycode(runcode3)">
 <input type="button"onclick=saveCode(code) style="border:1px solid #B1B4CD;background:#556b2f;color:#FFF; padding-top:5px;"value="另存代码" onclick="saveCode(runcode3)"> 
 提示：可以先修改部分代码再运行</div> </form><br>';} add_shortcode("code", "textarea");

 //生成文章二维码
 
 function zm_content_insert( $return = 0 ) {// 插入的内容
	$str.= "<hr>";
	$str.= "<div class='same'>";


	$str.= "<p>声明：<a href='http://xiaos.life/shengming.html' rel='external nofollow' target='_blank'>若无特殊声明，本文皆为原创，转载请注明文章信息！</a></p>";
	$str.= "</div>";
	if ($return) { return $str; } else { echo $str; }
}
function zm_content_filter($content) {
	if(!is_feed() && !is_home() && is_singular() && is_main_query()) {
		$content .= zm_content_insert(1);// 0在正文上面
		//$content .= zm_content_insert(1);//1在正文下面
	}
	return $content;
}
add_filter('the_content','zm_content_filter');
 
 //自定义登录页面风格
function uazoh_custom_login_page() {
echo'<style type="text/css">#login form {-webkit-box-shadow:0 2px 5px 0 rgba(146,146,146,.1);-moz-box-shadow:0 2px 5px 0 rgba(146,146,146,.1);box-shadow:0 8px 25px 0 rgba(146,146,146,0.21);}#login form .forgetmenot{float:none}
#login form p.submit{padding: 20px 0 0;}#login form p.submit .button-primary{float:none;background-color: #494949;font-weight: bold;color: #fff;width: 100%;height: 40px;border-width: 0;border-color:none}#login form input{box-shadow:none!important;outline:none!important}</style>';
}
add_action('login_head', 'uazoh_custom_login_page');

require get_template_directory() . '/avatar/zm-first-letter-avatar.php';
//添加文章格式

?>

