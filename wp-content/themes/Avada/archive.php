<?php get_header(); ?>
	<?php
	if($data['blog_archive_sidebar'] == 'None') {
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

	$container_class = '';
	$post_class = '';
	if($data['blog_archive_layout'] == 'Large Alternate') {
		$post_class = 'large-alternate';
	} elseif($data['blog_archive_layout'] == 'Medium Alternate') {
		$post_class = 'medium-alternate';
	} elseif($data['blog_archive_layout'] == 'Grid') {
		$post_class = 'grid-post';
		$container_class = 'grid-layout';
		if($data['blog_archive_sidebar'] == 'None') {
			if($data['blog_grid_columns'] == "3") {
				$container_class = 'grid-layout grid-full-layout-3';
			} else {
				$container_class = 'grid-layout grid-full-layout-4';
			}
		}
	} elseif($data['blog_archive_layout'] == 'Timeline') {
		$post_class = 'timeline-post';
		$container_class = 'timeline-layout';
		if($data['blog_archive_sidebar'] != 'None') {
			$container_class = 'timeline-layout timeline-sidebar-layout';
		}
	}
	?>
	<div id="content" class="<?php echo $content_class; ?>" style="<?php echo $content_css; ?>">
		<?php if(category_description()): ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="post-content">
				<?php echo category_description(); ?>
			</div>
		</div>
		<?php endif; ?>
		<?php if($data['blog_archive_layout'] == 'Timeline'): ?>
		<div class="timeline-icon"><i class="icon-comments-alt"></i></div>
		<?php endif; ?>
		<div id="posts-container" class="<?php echo $container_class; ?> clearfix">
			<?php
			$post_count = 1;

			$prev_post_timestamp = null;
			$prev_post_month = null;
			$first_timeline_loop = false;

			while(have_posts()): the_post();
				$post_timestamp = strtotime($post->post_date);
				$post_month = date('n', $post_timestamp);
				$post_year = get_the_date('o');
				$current_date = get_the_date('o-n');
			?>
			<?php if($data['blog_archive_layout'] == 'Timeline'): ?>
			<?php if($prev_post_month != $post_month): ?>
				<div class="timeline-date"><h3 class="timeline-title"><?php echo get_the_date($data['timeline_date_format']); ?></h3></div>
			<?php endif; ?>
			<?php endif; ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class($post_class.getClassAlign($post_count).' clearfix'); ?>>
				<?php if($data['blog_archive_layout'] == 'Medium Alternate'): ?>
				<div class="date-and-formats">
					<div class="date-box">
						<span class="date"><?php the_time($data['alternate_date_format_day']); ?></span>
						<span class="month-year"><?php the_time($data['alternate_date_format_month_year']); ?></span>
					</div>
					<div class="format-box">
						<?php
						switch(get_post_format()) {
							case 'gallery':
								$format_class = 'camera-retro';
								break;
							case 'link':
								$format_class = 'link';
								break;
							case 'image':
								$format_class = 'picture';
								break;
							case 'quote':
								$format_class = 'quote-left';
								break;
							case 'video':
								$format_class = 'film';
								break;
							case 'audio':
								$format_class = 'headphones';
								break;
							case 'chat':
								$format_class = 'comments-alt';
								break;
							default:
								$format_class = 'book';
								break;
						}
						?>
						<i class="icon-<?php echo $format_class; ?>"></i>
					</div>
				</div>
				<?php endif; ?>
				<?php
				if($data['featured_images']):
				if($data['legacy_posts_slideshow']) {
					get_template_part('legacy-slideshow');
				} else {
					get_template_part('new-slideshow');
				}
				endif;
				?>
				<div class="post-content-container">
					<?php if($data['blog_archive_layout'] == 'Timeline'): ?>
					<div class="timeline-circle"></div>
					<div class="timeline-arrow"></div>
					<?php endif; ?>
					<?php if($data['blog_archive_layout'] != 'Large Alternate' && $data['blog_archive_layout'] != 'Medium Alternate' && $data['blog_archive_layout'] != 'Grid'  && $data['blog_archive_layout'] != 'Timeline'): ?>
					<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php endif; ?>
					<?php if($data['blog_archive_layout'] == 'Large Alternate'): ?>
					<div class="date-and-formats">
						<div class="date-box">
							<span class="date"><?php the_time($data['alternate_date_format_day']); ?></span>
							<span class="month-year"><?php the_time($data['alternate_date_format_month_year']); ?></span>
						</div>
						<div class="format-box">
							<?php
							switch(get_post_format()) {
								case 'gallery':
									$format_class = 'camera-retro';
									break;
								case 'link':
									$format_class = 'link';
									break;
								case 'image':
									$format_class = 'picture';
									break;
								case 'quote':
									$format_class = 'quote-left';
									break;
								case 'video':
									$format_class = 'film';
									break;
								case 'audio':
									$format_class = 'headphones';
									break;
								case 'chat':
									$format_class = 'comments-alt';
									break;
								default:
									$format_class = 'book';
									break;
							}
							?>
							<i class="icon-<?php echo $format_class; ?>"></i>
						</div>
					</div>
					<?php endif; ?>
					<div class="post-content">
						<?php if($data['blog_archive_layout'] == 'Large Alternate' || $data['blog_archive_layout'] == 'Medium Alternate'  || $data['blog_archive_layout'] == 'Grid' || $data['blog_archive_layout'] == 'Timeline'): ?>
						<h2 class="post-title entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<?php if($data['post_meta']): ?>
						<?php if($data['blog_archive_layout'] == 'Grid' || $data['blog_archive_layout'] == 'Timeline'): ?>
						<p class="single-line-meta vcard"><?php if(!$data['post_meta_author']): ?><?php echo __('By', 'Avada'); ?> <span class="fn"><?php the_author_posts_link(); ?></span><span class="sep">|</span><?php endif; ?><?php if(!$data['post_meta_date']): ?><span class="updated"><?php the_time($data['date_format']); ?></span><span class="sep">|</span><?php endif; ?></p>
						<?php else: ?>
						<p class="single-line-meta vcard"><?php if(!$data['post_meta_author']): ?><?php echo __('By', 'Avada'); ?> <span class="fn"><?php the_author_posts_link(); ?></span><span class="sep">|</span><?php endif; ?><?php if(!$data['post_meta_date']): ?><span class="updated"><?php the_time($data['date_format']); ?></span><span class="sep">|</span><?php endif; ?><?php if(!$data['post_meta_cats']): ?><?php the_category(', '); ?><span class="sep">|</span><?php endif; ?><?php if(!$data['post_meta_comments']): ?><?php comments_popup_link(__('0 Comments', 'Avada'), __('1 Comment', 'Avada'), '% '.__('Comments', 'Avada')); ?><span class="sep">|</span><?php endif; ?></p>
						<?php endif; ?>
						<?php endif; ?>
						<?php endif; ?>
						<div class="content-sep"></div>
						<?php
						if($data['content_length'] == 'Excerpt') {
							$stripped_content = tf_content( $data['excerpt_length_blog'], $data['strip_html_excerpt'] );
							echo $stripped_content;
						} else {
							the_content('');
						}
						?>
					</div>
					<div style="clear:both;"></div>
					<?php if($data['post_meta']): ?>
					<div class="meta-info">
						<?php if($data['blog_archive_layout'] == 'Grid' || $data['blog_archive_layout'] == 'Timeline'): ?>
						<?php if($data['blog_archive_layout'] != 'Large Alternate' && $data['blog_archive_layout'] != 'Medium Alternate'): ?>
						<div class="alignleft">
							<?php if(!$data['post_meta_read']): ?><a href="<?php the_permalink(); ?>" class="read-more"><?php echo __('Read More', 'Avada'); ?></a><?php endif; ?>
						</div>
						<?php endif; ?>
						<div class="alignright">
							<?php if(!$data['post_meta_comments']): ?><?php comments_popup_link('<i class="icon-comments"></i>&nbsp;'.__('0', 'Avada'), '<i class="icon-comments"></i>&nbsp;'.__('1', 'Avada'), '<i class="icon-comments"></i>&nbsp;'.'%'); ?><?php endif; ?>
						</div>
						<?php else: ?>
						<?php if($data['blog_archive_layout'] != 'Large Alternate' && $data['blog_archive_layout'] != 'Medium Alternate'): ?>
						<div class="alignleft vcard">
							<?php if(!$data['post_meta_author']): ?><?php echo __('By', 'Avada'); ?> <span class="fn"><?php the_author_posts_link(); ?></span><span class="sep">|</span><?php endif; ?><?php if(!$data['post_meta_date']): ?><span class="updated"><?php the_time($data['date_format']); ?></span><span class="sep">|</span><?php endif; ?><?php if(!$data['post_meta_cats']): ?><?php the_category(', '); ?><span class="sep">|</span><?php endif; ?><?php if(!$data['post_meta_comments']): ?><?php comments_popup_link(__('0 Comments', 'Avada'), __('1 Comment', 'Avada'), '% '.__('Comments', 'Avada')); ?><span class="sep">|</span><?php endif; ?>
						</div>
						<?php endif; ?>
						<div class="alignright">
							<?php if(!$data['post_meta_read']): ?><a href="<?php the_permalink(); ?>" class="read-more"><?php echo __('Read More', 'Avada'); ?></a><?php endif; ?>
						</div>
						<?php endif; ?>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<?php
			$prev_post_timestamp = $post_timestamp;
			$prev_post_month = $post_month;
			$post_count++;
			endwhile;
			?>
		</div>
		<?php themefusion_pagination($pages = '', $range = 2); ?>
	</div>
	<?php wp_reset_query(); ?>
	<div id="sidebar" style="<?php echo $sidebar_css; ?>">
	<?php
	if ($data['blog_archive_sidebar'] != 'None' && function_exists('dynamic_sidebar')) {
		generated_dynamic_sidebar($data['blog_archive_sidebar']);
	}
	?>
	</div>
<?php get_footer(); ?>