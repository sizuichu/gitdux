<?php /*
Template Name: 说说页面
author: 秋叶
url: http://www.mizuiren.com/141.html
*/
get_header(); ?>



 <ul class="archives-monthlisting">
 <?php query_posts("post_type=shuoshuo&post_status=publish&posts_per_page=-1");if (have_posts()) : while (have_posts()) : the_post(); ?>
 <li><span class="tt"><?php the_time('Y年n月j日G:i'); ?></span>
 <div class="shuoshuo-content"><?php the_content(); ?><br/>
 <div class="shuoshuo-meta ><span >—<?php the_author() ?>
 <img src="http://127.0.0.1/wordpress/wp-content/themes/gitdux/img/logo.png"style="widtt:3px;height:30px">
 </span></div></div>
 <?php endwhile;endif; ?></li>
 </ul>
</div>

<?php get_footer();?>