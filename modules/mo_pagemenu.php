<?php  
	/*$pagemenus = _hui('page_menu');
	$menus = '';
	if( $pagemenus ){
		$pageURL = curPageURL();
		foreach ($pagemenus as $key => $value) {
			if( $value ) {
				$purl = get_permalink($key);
				$menus .= '<li '.($purl==$pageURL?'class="active"':'').'><a href="'.$purl.'">'.get_post($key)->post_title.'</a></li>';
			}
		}
	}*/

?>
<div class="pageside">
	<div class="pagemenus">
		<ul class="pagemenu">
			<?php _the_menu('pagenav') ?>
		</ul>
	</div>
</div>