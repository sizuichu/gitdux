<?php 
/**
 * Template name: 热门标签
 * Description:   A tags page
 */

get_header();

?>

<div class="container container-tags">
	<h1><?php the_title(); ?></h1>
	<div class="tagslist">
		<ul>
			<?php 
				$tags_count = 60;
				$tagslist = get_tags('orderby=count&order=DESC&number='.$tags_count);
				foreach($tagslist as $tag) {
					echo '<li><a class="name" href="'.get_tag_link($tag).'">'. $tag->name .'</a><small>&times;'. $tag->count .'</small>'; 

					$posts = get_posts( "tag_id=". $tag->term_id ."&numberposts=1" );
					foreach( $posts as $post ) {
						setup_postdata( $post );
						echo '<p><a class="tit" href="'.get_permalink().'">'.get_the_title().'</a></p>';
					}

					echo '</li>';
				} 
		
			?>
		</ul>
	</div>
</div>

<?php

get_footer();