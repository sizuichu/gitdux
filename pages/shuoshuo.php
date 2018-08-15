<?php /*
Template Name: 说说
author: 秋叶
url: http://www.mizuiren.com/141.html
*/

get_header(); ?>
<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <div id="shuoshuo_content">
            <ul class="bsy_timeline">
                <?php query_posts("post_type=shuoshuo&post_status=publish&posts_per_page=-1");if (have_posts()) : while (have_posts()) : the_post(); ?>
                <li> <span class="author_tou"><img src="http://www.99bsy.com/authortou.png" class="avatar" width="48" height="48"></span>
                    <a class="bsy_tmlabel" href="javascript:void(0)">
                        <div></div>
                        <div><?php the_content(); ?></div>
                        <div></div>
                        <div class="shuoshuo_time"><i class="fa fa-user"></i><?php the_author() ?><i class="fa fa-clock-o"></i><?php the_time('Y年n月j日G:i'); ?>
                        </div>
                    </a>
                    <?php endwhile;endif; ?>
                </li>
            </ul>
        </div>
    </main>
    <!-- .site-main -->
</div>
<script type="text/javascript">
    $(function () {
        var oldClass = "";
        var Obj = "";
        $(".bsy_timeline li").hover(function () {
            Obj = $(this).children(".author_tou");
            Obj = Obj.children("img");
            oldClass = Obj.attr("class");
            var newClass = oldClass + " zhuan";
            Obj.attr("class", newClass);
        }, function () {
            Obj.attr("class", oldClass);
        })
    })
</script>
<?php get_sidebar(); ?>
<?php get_footer();?>