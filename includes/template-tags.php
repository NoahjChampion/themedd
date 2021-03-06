<?php
/**
 * Template tags
 */

if ( ! function_exists( 'themedd_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function themedd_posted_on( $show_author = true ) {

	$post_author_id   = get_post_field( 'post_author', get_the_ID() );
	$post_author_name = get_the_author_meta( 'display_name', $post_author_id );

	// Get the author name; wrap it in a link.
	$byline = sprintf(
		/* translators: %s: post author */
		__( 'by %s', 'themedd' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID', $post_author_id ) ) ) . '">' . $post_author_name . '</a></span>'
	);

	// Finally, let's write all of this to the page.
	echo '<div class="entry-meta">';
	echo '<span class="posted-on">' . themedd_time_link() . '</span>';
	if ( $show_author ) {
		echo '<span class="byline"> ' . $byline . '</span>';
	}
	echo '</div>';
}
endif;

if ( ! function_exists( 'themedd_time_link' ) ) :
/**
 * Gets a nicely formatted string for the published date.
 */
function themedd_time_link() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		get_the_date( DATE_W3C ),
		get_the_date(),
		get_the_modified_date( DATE_W3C ),
		get_the_modified_date()
	);

	// Wrap the time string in a link, and preface it with 'Posted on'.
	return sprintf(
		/* translators: %s: post date */
		__( '<span class="screen-reader-text">Posted on</span> %s', 'themedd' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);
}
endif;

/**
* Display navigation to next/previous comments when applicable.
*
* @since 1.0.0
*/
if ( ! function_exists( 'themedd_comment_nav' ) ) :
function themedd_comment_nav() {
	// Are there comments to navigate through?
	if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
	?>
	<nav class="navigation comment-navigation" role="navigation">
		<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'themedd' ); ?></h2>
		<div class="nav-links">
			<?php
				if ( $prev_link = get_previous_comments_link( esc_html__( 'Older Comments', 'themedd' ) ) ) {
					printf( '<div class="nav-previous">%s</div>', $prev_link );
				}

				if ( $next_link = get_next_comments_link( esc_html__( 'Newer Comments', 'themedd' ) ) ) {
					printf( '<div class="nav-next">%s</div>', $next_link );
				}
			?>
		</div>
	</nav>
	<?php
	endif;
}
endif;


if ( ! function_exists( 'themedd_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function themedd_entry_footer() {

	/* translators: used between list items, there is a space after the comma */
	$separate_meta = __( ', ', 'themedd' );

	// Get Categories for posts.
	$categories_list = get_the_category_list( $separate_meta );

	// Get Tags for posts.
	$tags_list = get_the_tag_list( '', $separate_meta );

	// We don't want to output .entry-footer if it will be empty, so make sure its not.
	if ( ( ( themedd_categorized_blog() && $categories_list ) || $tags_list ) || get_edit_post_link() ) {

		echo '<footer class="entry-footer">';

			if ( 'post' === get_post_type() ) {
				if ( ( $categories_list && themedd_categorized_blog() ) || $tags_list ) {
					echo '<span class="cat-tags-links">';

						// Make sure there's more than one category before displaying.
						if ( $categories_list && themedd_categorized_blog() ) {
							echo '<span class="cat-links">' . __( 'Categories: ', 'themedd' ) . $categories_list . '</span>';
						}

						if ( $tags_list ) {
							echo '<span class="tags-links">' . __( 'Tags: ', 'themedd' ) . $tags_list . '</span>';
						}

					echo '</span>';
				}
			}

			themedd_edit_link();

		echo '</footer>';
	}
}
endif;

if ( ! function_exists( 'themedd_edit_link' ) ) :
/**
 * Returns an accessibility-friendly link to edit a post or page.
 *
 * This also gives us a little context about what exactly we're editing
 * (post or page?) so that users understand a bit more where they are in terms
 * of the template hierarchy and their content. Helpful when/if the single-page
 * layout with multiple posts/pages shown gets confusing.
 */
function themedd_edit_link() {

	$link = edit_post_link(
		sprintf(
			/* translators: %s: Name of current post */
			__( 'Edit<span class="screen-reader-text"> "%s"</span>', 'themedd' ),
			get_the_title()
		),
		'<span class="edit-link">',
		'</span>'
	);

	return $link;
}
endif;

/**
 * Determine whether blog/site has more than one category.
 *
 * @since 1.0.0
 *
 * @return bool True if there is more than one category, false otherwise.
 */
if ( ! function_exists( 'themedd_categorized_blog' ) ) :
function themedd_categorized_blog() {

	if ( false === ( $all_the_cool_cats = get_transient( 'themedd_categories' ) ) ) {

		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',

			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'themedd_categories', $all_the_cool_cats );

	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so themedd_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so themedd_categorized_blog should return false.
		return false;
	}

}
endif;

/**
 * Display an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index views, or a div
 * element when on single views.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'themedd_post_thumbnail' ) ) :
function themedd_post_thumbnail() {

	if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
		return;
	}

	/**
	 * Allow developers to remove the post thumbnail
	 */
	if ( ! apply_filters( 'themedd_post_thumbnail', true ) ) {
		return;
	}

	if ( is_singular() ) : ?>

	<div class="post-thumbnail">
		<?php the_post_thumbnail(); ?>
	</div>

	<?php else : ?>

	<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
		<?php the_post_thumbnail( 'post-thumbnail', array( 'alt' => get_the_title() ) ); ?>
	</a>

	<?php endif; // End is_singular()
}
endif;


/**
 * Display the post header
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'themedd_page_header' ) ) :

	function themedd_page_header( $args = array() ) {

		/**
		 * Allow header to be removed via filter
		 */
		if ( ! apply_filters( 'themedd_page_header', true ) ) {
			return;
		}

		do_action( 'themedd_page_header_before' );

		if ( is_404() ) {
			$title = esc_html__( 'Oops! That page can&rsquo;t be found.', 'themedd' );
		} else {
			$title = ! empty( $args['title'] ) ? $args['title'] : get_the_title();
		}

		// Process any classes passed in.
		if ( ! empty( $args['classes'] ) ) {
			if ( is_array( $args['classes'] ) ) {
				// array of classes
				$classes = $args['classes'];
			} else {
				// must be string, explode it into an array
				$classes = explode( ' ', $args['classes'] );
			}
		} else {
			$classes = array();
		}

        $defaults = apply_filters( 'themedd_header_defaults',
            array(
                'subtitle' => ! empty( $args['subtitle'] ) ? $args['subtitle'] : '',
                'title'    => ! empty( $args['title'] ) ? $args['title'] : get_the_title(),
            )
        );

        $args = wp_parse_args( $args, $defaults );
		?>

		<header class="page-header<?php echo themedd_page_header_classes( $classes ); ?>">
			<?php do_action( 'themedd_page_header_start' ); ?>
			<div class="wrapper">
				<?php do_action( 'themedd_page_header_wrapper_start' ); ?>
				<h1 class="<?php echo get_post_type(); ?>-title">
					<?php if ( $args['subtitle'] ) : ?>
						<span class="entry-title-primary"><?php echo $args['title']; ?></span>
						<span class="subtitle"><?php echo $args['subtitle']; ?></span>
					<?php elseif ( $args['title'] ) : ?>
						<?php echo $args['title']; ?>
					<?php endif; ?>
				</h1>
				<?php do_action( 'themedd_page_header_wrapper_end' ); ?>
			</div>
			<?php do_action( 'themedd_page_header_end' ); ?>
		</header>

	<?php

	}

endif;

if ( ! function_exists( 'themedd_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 *
 * @since 1.0.0
 */
function themedd_paging_nav() {

	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}

	$defaults = apply_filters( 'themedd_paging_nav',
		array(
			'next_posts_link'     => __( 'Older posts', 'themedd' ),
			'previous_posts_link' => __( 'Newer posts', 'themedd' ),
		)
	);
	?>
	<nav class="navigation paging-navigation" role="navigation">

		<h1 class="screen-reader-text"><?php _e( 'Posts navigation', 'themedd' ); ?></h1>

		<div class="nav-links">
			<?php if ( get_next_posts_link() ) : ?>
			<div class="nav-previous"><?php next_posts_link( $defaults['next_posts_link'] ); ?></div>
			<?php endif; ?>

			<?php if ( get_previous_posts_link() ) : ?>
			<div class="nav-next"><?php previous_posts_link( $defaults['previous_posts_link'] ); ?></div>
			<?php endif; ?>

		</div>
	</nav>
	<?php
}
endif;

if ( ! function_exists( 'themedd_the_custom_logo' ) ) :
/**
 * Displays the optional custom logo.
 *
 * Does nothing if the custom logo is not available.
 *
 * @since 1.0.0
 */
function themedd_the_custom_logo() {
	if ( function_exists( 'the_custom_logo' ) ) {
		the_custom_logo();
	}
}
endif;
