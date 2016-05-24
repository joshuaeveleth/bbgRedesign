<?php
/**
 * Template part for displaying a multicolumn child pages in About pages
 * 3 columns without byline or date
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbginnovate
 */


global $includePageDescription;
global $gridClass;
global $headline;

$includeDescription = TRUE;
if ( isset ( $includePageDescription ) && $includePageDescription == FALSE ) {
	$includeDescription = FALSE;
}

if ( ! isset ($gridClass) ) {
	$gridClass = "bbg-grid--1-2-2";
}
$classNames = "bbg__about__excerpt " . "bbg__about__child--" . strtolower(get_the_title()) . " " . $gridClass;

$postPermalink = esc_url( get_permalink() );
if ( isset( $_GET['category_id'] ) ) {
	$postPermalink = add_query_arg('category_id', $_GET['category_id'], $postPermalink);
}

?>


<article id="post-<?php the_ID(); ?>" <?php post_class($classNames); ?>>
	<header class="entry-header bbg__about__excerpt-header">
	<?php
		$link = sprintf( '<a href="%s" rel="bookmark">', $postPermalink );
		$linkImage = sprintf( '<a href="%s" rel="bookmark" tabindex="-1">', $postPermalink );
		$linkLabel = '<h6 class="bbg-label">' . $link;
		// $linkH3 = '<h3 class="entry-title bbg__about__excerpt-title">' . $link;
	?>
		<!-- Child page title -->
		<?php the_title( sprintf( $linkLabel, $postPermalink ), '</a></h6>' ); ?>

		<!-- Child page thumbnail -->
		<div class="single-post-thumbnail clear bbg__excerpt-header__thumbnail--medium">
			<?php
				echo $linkImage;

				/* Set a default thumbnail image in case one isn't set */
				$thumbnail = '<img src="' . get_template_directory_uri() . '/img/portfolio-project-default.png" alt="This is a default image." />';

				if ( has_post_thumbnail() ) {
					$thumbnail = the_post_thumbnail('medium-thumb');
				}
				echo $thumbnail;
			?>
			</a>
		</div>

		<!-- Child page headline text -->
		<?php if ($headline) { ?>
			<h3>
				<?php
					echo $link;
					echo $headline;
				?>
			</a>
			</h3>
		<?php } ?>

	</header><!-- .entry-header -->

	<!-- Child page excerpt -->
	<?php if ($includeDescription) { ?>
		<div class="entry-content bbg__about__excerpt-content">
			<?php the_excerpt(); ?>

			<?php
				wp_link_pages( array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'bbginnovate' ),
					'after'  => '</div>',
				) );
			?>
		</div><!-- .bbg__about__excerpt-title -->
	<?php } ?>

</article><!-- .bbg__about__excerpt -->