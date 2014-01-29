<?php global $data; ?>
<?php if($data['slidingbar_open_on_load']): ?>
<div id="slidingbar-area" class="open_onload">
<?php else: ?>
<div id="slidingbar-area">
<?php endif; ?>
	<div id="slidingbar">
		<div class="avada-row">
			<section class="columns columns-<?php echo $data['slidingbar_widgets_columns']; ?>">
				<article class="col">
				<?php
				if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('SlidingBar Widget  1')):
				endif;
				?>
				</article>
				<article class="col">
				<?php
				if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('SlidingBar Widget  2')):
				endif;
				?>
				</article>
				<article class="col">
				<?php
				if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('SlidingBar Widget  3')):
				endif;
				?>
				</article>

				<article class="col last">
				<?php
				if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('SlidingBar Widget  4')):
				endif;
				?>
				</article>
			</section>
		</div>
	</div>
	<a class="sb_toggle" href="#"></a>
</div>