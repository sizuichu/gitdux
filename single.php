<?php get_header(); ?>
<?php if( _hui('breadcrumbs_single_s') ){ ?>
	<div class="breadcrumbs">
		<div class="container"><?php echo hui_breadcrumbs() ?></div>
	</div>
<?php } ?>
<section class="container">
	<div class="content-wrap">
	<div class="content">
		<?php while (have_posts()) : the_post(); ?>
		<header class="article-header">
			<h1 class="article-title"><a href="<?php the_permalink() ?>"><?php the_title(); ?><?php echo get_the_subtitle() ?></a></h1>
			<div class="article-meta">
				<span class="item"><?php echo tb_xzh_is_original() ? get_the_time('Y-m-d H:i:s') : get_the_time('Y-m-d'); ?></span>
				<?php _moloader('mo_get_post_from', false); ?>
				<?php if( mo_get_post_from() ){ ?><span class="item"><?php echo mo_get_post_from(); ?></span><?php } ?>
				<span class="item"><?php echo '分类：';the_category(' / '); ?></span>
				<?php if( _hui('post_plugin_view') ){ ?><span class="item post-views"><?php echo _get_post_views() ?></span><?php } ?>
				<span class="item"><?php echo _get_post_comments() ?></span>
				<span class="item"><?php edit_post_link('[编辑]'); ?></span>
			</div>
		</header>
		<?php tb_xzh_render_body() ?>
		<article class="article-content">
			<?php _the_ads($name='ads_post_01', $class='asb-post asb-post-01') ?>
			<?php the_content(); ?>
		</article>
		<?php wp_link_pages('link_before=<span>&link_after=</span>&before=<div class="article-paging">&after=</div>&next_or_number=number'); ?>
		<?php if (_hui('ads_post_footer_s')) {
			echo '<div class="asb-post-footer"><b>AD：</b><strong>【' . _hui('ads_post_footer_pretitle') . '】</strong><a'.(_hui('ads_post_footer_link_blank')?' target="_blank"':'').' href="' . _hui('ads_post_footer_link') . '">' . _hui('ads_post_footer_title') . '</a></div>';
		} ?>
		<?php tb_xzh_render_tail() ?>
		<?php 
		$link = get_post_meta(get_the_ID(), 'link', true);
		if( _hui('post_like_s') || _hui('post_rewards_s') || (_hui('post_link_single_s')&&$link) ){ ?>
            <div class="post-actions">
            	<?php if( _hui('post_like_s') ){ ?><?php echo hui_get_post_like($class='post-like action action-like'); ?><?php } ?>
            	<?php if( _hui('post_rewards_s') ){ ?><a href="javascript:;" class="action action-rewards" data-event="rewards"><i class="fa fa-jpy"></i> <?php echo _hui('post_rewards_text', '打赏') ?></a><?php } ?>
            	<?php if( _hui('post_link_single_s') && $link ){ 
            		echo '<a class="action action-link" href="'. $link .'"'. (_hui('post_link_blank_s')?' target="_blank"':'') . (_hui('post_link_nofollow_s')?' rel="external nofollow"':'') .'><i class="fa fa-external-link"></i> '._hui('post_link_h1') .'</a>';
            	} ?>
            </div>
        <?php } ?>
		<?php if( _hui('post_copyright_s') ){
			echo '<div class="post-copyright">' . _hui('post_copyright') . '<a href="' . get_bloginfo('url') . '">' . get_bloginfo('name') . '</a> &raquo; <a href="' . get_permalink() . '">' . get_the_title() . '</a></div>';
		} ?>
		<?php endwhile; ?>

		<?php if( !wp_is_mobile() || (!_hui('m_post_share_s') && wp_is_mobile()) ){ ?>
			<div class="action-share"><?php _moloader('mo_share'); ?></div>
		<?php } ?>

		<div class="article-tags"><?php the_tags('标签：','',''); ?></div>
		
		<?php if( _hui('post_authordesc_s') ){ ?>
		<div class="article-author">
			<?php echo _get_the_avatar(get_the_author_meta('ID'), get_the_author_meta('email')); ?>
			<h4><i class="fa fa-user" aria-hidden="true"></i><a title="查看更多文章" href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php echo get_the_author_meta('nickname'); ?></a></h4>
			<?php echo get_the_author_meta('description'); ?>
		</div>
		<?php } ?>

		<?php if( _hui('post_prevnext_s') ){ ?>
            <nav class="article-nav">
                <span class="article-nav-prev"><?php previous_post_link('上一篇<br>%link'); ?></span>
                <span class="article-nav-next"><?php next_post_link('下一篇<br>%link'); ?></span>
            </nav>
        <?php } ?>

		<?php _the_ads($name='ads_post_02', $class='asb-post asb-post-02') ?>
		<?php 
			if( _hui('post_related_s') ){
				_moloader('mo_posts_related', false); 
				mo_posts_related(_hui('related_title'), _hui('post_related_n'));
			}
		?>
		<?php _the_ads($name='ads_post_03', $class='asb-post asb-post-03') ?>
		<?php comments_template('', true); ?>
	</div>
	</div>
	<?php 
		if( has_post_format( 'aside' )){

		}else{
			get_sidebar();
		} 
	?>
</section>

<?php get_footer(); 