<?php get_header(); ?>
	<?php
	if($data['blog_full_width']) {
		$content_css = 'width:100%';
		$sidebar_css = 'display:none';
		$content_class= 'full-width';
	} elseif($data['blog_sidebar_position'] == 'Left') {
		$content_css = 'float:right;';
		$sidebar_css = 'float:left;';
	} elseif($data['blog_sidebar_position'] == 'Right') {
		$content_css = 'float:left;';
		$sidebar_css = 'float:right;';
	}
	?>
	<div id="content" class="<?php echo $content_class; ?>" style="<?php echo $content_css; ?>">
		<div class="search-page-search-form">
			<h2><?php echo __('Need a new search?', 'Avada'); ?></h2>
			<p><?php echo __('If you didn\'t find what you were looking for, try a new search!', 'Avada'); ?></p>
			<form id="searchform" class="seach-form" role="search" method="get" action="<?php echo home_url( '/' ); ?>">
				<input type="text" value="" name="s" id="s" placeholder="<?php _e( 'Search ...', 'Avada' ); ?>"/>
				<input type="submit" id="searchsubmit" value="&#xf002;" />
			</form>
		</div>
		<?php
		if($data['search_results_per_page']) {
			$page_num = $paged;
			if ($pagenum='') { $pagenum = 1; }
				global $query_string;
				query_posts($query_string.'&posts_per_page='.$data['search_results_per_page'].'&paged='.$page_num);
		} ?>
		<?php if (have_posts()) : ?>
		<?php while(have_posts()): the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class('post'); ?>>
			<?php
			if('page' != $post->post_type && !$data['search_featured_images']):
			if($data['legacy_posts_slideshow']) {
				get_template_part('legacy-slideshow');
			} else {
				get_template_part('new-slideshow');
			}
			endif;
			?>
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<?php if(!$data['search_excerpt']): ?>
			<div class="post-content">
				<?php
				$stripped_content = tf_content( $data['excerpt_length_blog'], $data['strip_html_excerpt'] );
				echo $stripped_content;
				?>
			</div>
			<?php endif; ?>
			<?php if($data['post_meta']): ?>
			<div class="meta-info">
				<div class="alignleft vcard">
					<?php if(!$data['post_meta_author']): ?><?php echo __('By', 'Avada'); ?> <span class="fn"><?php the_author_posts_link(); ?></span><span class="sep">|</span><?php endif; ?><?php if(!$data['post_meta_date']): ?><span class="updated"><?php the_time($data['date_format']); ?></span><span class="sep">|</span><?php endif; ?><?php if(!$data['post_meta_cats']): ?><?php the_category(', '); ?><span class="sep">|</span><?php endif; ?><?php if(!$data['post_meta_comments']): ?><?php comments_popup_link(__('0 Comments', 'Avada'), __('1 Comment', 'Avada'), '% '.__('Comments', 'Avada')); ?><span class="sep">|</span><?php endif; ?>
				</div>
				<div class="alignright">
					<?php if(!$data['post_meta_read']): ?><a href="<?php the_permalink(); ?>" class="read-more"><?php echo __('Read More', 'Avada'); ?></a><?php endif; ?>
				</div>
			</div>
			<?php endif; ?>
		</div>
		<?php endwhile; ?>
		<?php themefusion_pagination($pages = '', $range = 2); ?>
		<?php else: ?>
		<div class="post-content">
			<div class="title">
				<h2><?php echo __('Couldn\'t find what you\'re looking for!', 'Avada'); ?></h2><div class="title-sep-container"><div class="title-sep"></div></div>
			</div>
			<div class="error_page">
				<div class="one_third">
					<h1 class="oops <?php echo ($sidebar_css != 'display:none') ? 'sidebar-oops' : ''; ?>"><?php echo __('Oops!', 'Avada'); ?></h1>
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
					<h3><?php echo __('Try again!', 'Avada'); ?></a></h3>
					<p><?php echo __('If you want to rephrase your query, here is your chance:', 'Avada'); ?></p>
					<?php get_search_form(); ?>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<div id="sidebar" style="<?php echo $sidebar_css; ?>">
		<?php
		if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Blog Sidebar')):
		endif;
		?>
	</div>
<?php get_footer(); ?>