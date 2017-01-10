<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Wordpress Plugin Template
 */

get_header(); geodb('archive gizmo');?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php
		if ( have_posts() ) : ?>

			<header class="page-header">
				<?php
					the_archive_title( '<h1 class="page-title">', '</h1>' );
					the_archive_description( '<div class="taxonomy-description">', '</div>' );
				?>
			</header><!-- .page-header -->

			<?php
			/* Start the Loop */
			while ( have_posts() ) : the_post();

				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				 $post_format=get_post_format();
				 if(!$post_format){
				 	$post_format="gizmo-format";// default post format
				}
				// Check for overiding template in theme
				$theme_files = array('template-parts/content-'.$post_format.'.php');
	    		$exists_in_theme = locate_template($theme_files);

				if ($exists_in_theme) {
	    			get_template_part( 'template-parts/content', $post_format );
	    		} else {
					$template=dirname(__FILE__)."/template-parts/content-".$post_format.".php";
					$file_exists=file_exists($template);
				
					if ($file_exists) {
						include($template);
					} 				 
				}

			endwhile;

			the_posts_navigation();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
