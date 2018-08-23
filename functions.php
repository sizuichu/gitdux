<?php
// Require theme functions
require get_stylesheet_directory() . '/functions-theme.php';

// Customize your functions



//说说
 add_action('init', 'my_custom_init'); function my_custom_init() { $labels = array( 'name' => '说说', 'singular_name' => 'singularname', 'add_new' => '发表说说', 'add_new_item' => '发表说说', 'edit_item' => '编辑说说', 'new_item' => '新说说', 'view_item' => '查看说说', 'search_items' => '搜索说说', 'not_found' => '暂无说说', 'not_found_in_trash' => '没有已遗弃的说说', 'parent_item_colon' => '', 'menu_name' => '说说' ); $args = array( 'labels' => $labels, 'public' => true, 'publicly_queryable' => true, 'show_ui' => true, 'show_in_menu' => true, 'query_var' => true, 'rewrite' => true, 'capability_type' => 'post', 'has_archive' => true, 'hierarchical' => false, 'menu_position' => null, 'supports' => array('title','editor','author') ); register_post_type('shuoshuo',$args); }

function textarea($atts, $content = null) 
{ return '<script src="http://xiaos.life/wp-content/uploads/2018/08/2018081511133998.js" type="text/javascript" charset="utf-8"></script> <form> 
<div align="center"> 
<textarea id="code" style="width:555px;height:200px; border:1px solid #ff0000;" cols="80" rows="15">'.$content.'</textarea> 
<br />
 <input type="button"onclick=runCode(code) value="运行代码" style="border:1px solid #B1B4CD;background:#556b2f;color:#FFF; padding-top:5px;"> 
 <input type="button"onclick=copycode(code) style="border:1px solid #B1B4CD;background:#556b2f;color:#FFF; padding-top:5px;"value="复制代码" onclick="copycode(runcode3)">
 <input type="button"onclick=saveCode(code) style="border:1px solid #B1B4CD;background:#556b2f;color:#FFF; padding-top:5px;"value="另存代码" onclick="saveCode(runcode3)"> 
 提示：可以先修改部分代码再运行</div> </form><br>';} add_shortcode("code", "textarea");


 //自定义登录页面的LOGO图片
function my_custom_login_logo() {
    echo '<style type="text/css">
        .login h1 a {
            background-image:url("http://s.xiaos.life/wp-content/themes/gitdux/img/logo.png") !important;
        height: 60px; //修改为图片的高度
        width: 250px; //修改为图标的宽度
        -webkit-background-size: 250px; //修改为图标的宽度
        background-size: 250px; //修改为图标的宽度
        }
    </style>';
}
add_action('login_head', 'my_custom_login_logo');

//自定义登录页面LOGO提示为任意文本
function custom_loginlogo_desc($url) {
    return '欢迎登录小生活'; //修改文本信息
}
add_filter( 'login_headertitle', 'custom_loginlogo_desc' );


//判断百度收录
function v7v3_bdsl($url){
    $url='http://www.baidu.com/s?wd='.$url;
    $curl=curl_init();
    curl_setopt($curl,CURLOPT_URL,$url);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
    $rs=curl_exec($curl);
    curl_close($curl);
    if(!strpos($rs,'没有找到与')){
        return 1;
    }else{
        return 0;
    }  
}

//展开收缩功能
function xcollapse($atts, $content = null){
    extract(shortcode_atts(array("title"=>""),$atts));
    return '<div style="margin: 0.5em 0;">
        <div class="xControl">
            <span class="xTitle">'.$title.'</span> 
            <a href="javascript:void(0)" class="collapseButton xButton">展开/收缩</a>
            <div style="clear: both;"></div>
        </div>
        <div class="xContent" style="display: none;">'.$content.'</div>
    </div>';
}
add_shortcode('collapse', 'xcollapse');

// 添加HTML按钮
function appthemes_add_quicktags() {
?> 
<script type="text/javascript"> 
QTags.addButton( '文字收缩', '文字收缩', '\n[collapse title=标题]', '[/collapse]\n' );
</script>
<?php
}
add_action('admin_print_footer_scripts', 'appthemes_add_quicktags' );


?>