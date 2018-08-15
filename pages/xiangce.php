
<?php /*
Template Name: 相册页面
author: 似最初
url: http://xiaos.life
*/

get_header(); ?>

<?php while (have_posts() ) : the_post(); ?>
<article>
 
<h2><a href="<?php the_permalink() ?>"  title="<?php the_title(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
 
<div class="pic">
<?php if ( has_post_thumbnail()) : ?>
<?php the_post_thumbnail(); ?>
<?php endif ?>
</div>
 
</article>
<?php endwhile; ?>
<?php get_sidebar(); ?>
<?php get_footer();?>