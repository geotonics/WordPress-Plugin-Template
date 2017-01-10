<?php
/**
 * Template part for displaying page content in archive-gizmo.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress_Plugin_Template
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php echo '<h2 class="gizmo_title"><a href="'.get_permalink().'">'.get_the_title().'</h2></a>'; ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
		<?php
		geodb($post,'thebigpost');
		$meta = get_post_meta( get_the_id() );
		geodb($meta,'thebigmeta');
		
		echo '<div class="one_gizmo">';
			
			if ( isset($meta['date_picker_field'][0]) ) {
				$date = $meta['date_picker_field'][0];
			} else {
				$date = 'No Date';
			}
			echo '<div class="gizmo_date">Date:'.$date.'</div>';

			if ( isset($meta['radio_buttons'][0])) {
				$radio_buttons = $meta['radio_buttons'][0];
			} else {
				$radio_buttons= '';
			}
			echo '<div class="gizmo_radio_buttons">Radio Buttons:'.$radio_buttons.'</div>';

			
			echo '</div>';
		
			?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'wordpress-plugin-template' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php edit_post_link( esc_html__( 'Edit', 'wordpress-plugin-template' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->

