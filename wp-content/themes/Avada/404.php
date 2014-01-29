<?php get_header(); ?>
	<div id="content" class="full-width">
		<div id="post-404page" class="post">
			<div class="post-content">
				<div class="title">
					<h2><?php echo __('Oops, This Page Could Not Be Found!', 'Avada'); ?></h2><div class="title-sep-container"><div class="title-sep"></div></div>
				</div>
				<div class="error_page">
					<div class="one_third">
						<div class="error-image"></div>
					</div>
					<div class="one_third useful_links">
						<h3><?php echo __('Here are some useful links:', 'Avada'); ?></h3>

						<?php $iconcolor = strtolower($data['checklist_icons_color']); ?>

						<style type='text/css'>
							.post-content #checklist-1 li:before{color:<?php echo $iconcolor; ?> !important;}
							.rtl .post-content #checklist-1 li:after{color:<?php echo $iconcolor; ?> !important;}
						</style>
						<?php wp_nav_menu(array('theme_location' => '404_pages', 'depth' => 1, 'container' => false, 'menu_id' => 'checklist-1', 'menu_class' => 'list-icon circle-yes list-icon-arrow')); ?>
					</div>
					<div class="one_third last">
						<h3><?php echo __('Search Our Website', 'Avada'); ?></a></h3>
						<p><?php echo __('Can\'t find what you need? Take a moment and do a search below!', 'Avada'); ?></p>
						<div class="search-page-search-form">
							<form class="seach-form" id="searchform" role="search" method="get" action="<?php echo home_url( '/' ); ?>">
								<input type="text" value="" name="s" id="s" placeholder="<?php _e( 'Search ...', 'Avada' ); ?>"/>
								<input type="submit" id="searchsubmit" value="&#xf002;" />
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php get_footer(); ?>