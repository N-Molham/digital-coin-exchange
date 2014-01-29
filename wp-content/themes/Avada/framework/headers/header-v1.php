<?php global $data; ?>
<div class="header-v1">
	<header id="header">
		<div class="avada-row" style="margin-top:<?php echo $data['margin_header_top']; ?>;margin-bottom:<?php echo $data['margin_header_bottom']; ?>;">
			<div class="logo" style="margin-right:<?php echo $data['margin_logo_right']; ?>;margin-top:<?php echo $data['margin_logo_top']; ?>;margin-left:<?php echo $data['margin_logo_left']; ?>;margin-bottom:<?php echo $data['margin_logo_bottom']; ?>;">
				<a href="<?php bloginfo('url'); ?>">
					<img src="<?php echo $data['logo']; ?>" alt="<?php bloginfo('name'); ?>" class="normal_logo" />
					<?php if($data['logo_retina'] && $data['retina_logo_width'] && $data['retina_logo_height']): ?>
					<?php
					$pixels ="";
					if(is_numeric($data['retina_logo_width']) && is_numeric($data['retina_logo_height'])):
					$pixels ="px";
					endif; ?>
					<img src="<?php echo $data["logo_retina"]; ?>" alt="<?php bloginfo('name'); ?>" style="width:<?php echo $data["retina_logo_width"].$pixels; ?>;max-height:<?php echo $data["retina_logo_height"].$pixels; ?>; height: auto !important" class="retina_logo" />
					<?php endif; ?>
				</a>
			</div>
			<?php if($data['ubermenu']): ?>
			<nav id="nav-uber">
			<?php else: ?>
			<nav id="nav" class="nav-holder">
			<?php endif; ?>
				<?php get_template_part('framework/headers/header-main-menu'); ?>
			</nav>
			<?php if(tf_checkIfMenuIsSetByLocation('main_navigation')): ?>
			<div class="mobile-nav-holder main-menu"></div>
			<?php endif; ?>
		</div>
	</header>
</div>