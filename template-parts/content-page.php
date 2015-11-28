<?php
/**
 * The template used for displaying page content
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="entry-content">

		<?php do_action( 'trustedd_entry_content_start' ); ?>

		<?php the_content(); ?>

		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'trustedd' ),
				'after'  => '</div>',
			) );
		?>

		<?php do_action( 'trustedd_entry_content_end' ); ?>

	</div>
</article>
