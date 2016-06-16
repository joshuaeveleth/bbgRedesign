<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
   template name: 2-column
 */

function formatBytes($bytes, $precision = 2) { 
	$units = array('B', 'KB', 'MB', 'GB', 'TB'); 

	$bytes = max($bytes, 0); 
	$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
	$pow = min($pow, count($units) - 1); 

	// Uncomment one of the following alternatives
	$bytes /= pow(1024, $pow);
	// $bytes /= (1 << (10 * $pow)); 

	return round($bytes, $precision) . ' ' . $units[$pow]; 
} 


$bannerPosition = get_field( 'adjust_the_banner_image', '', true);
$bannerPositionCSS = get_field( 'adjust_the_banner_image_css', '', true);
$bannerAdjustStr="";
if ($bannerPositionCSS) {
	$bannerAdjustStr = $bannerPositionCSS;
} else if ($bannerPosition) {
	$bannerAdjustStr = $bannerPosition;
}

$videoUrl = get_field( 'featured_video_url', '', true );
$secondaryColumnContent = get_field( 'secondary_column_content', '', true );
$headline = get_field( 'headline', '', true );
$headlineStr = "";
//$secondaryColumnContent = get_post_meta( get_the_ID(), 'secondary_column_content', true );



$sidebarInclude = get_field( 'sidebar_downloads_include', '', true);
$sidebarDownloads = "";
if( $sidebarInclude ){
	$downloadsTitle = get_field( 'sidebar_downloads_title' );
	$optionDefault = get_field ( 'sidebar_downloads_default' );
	$rows = get_field('sidebar_downloads');
	if ( $rows ) {
		$s = '<form style="">';
		$s .= '<label for="options" style="display: inline-block; font-size: 2rem; font-weight: bold; margin-top: 0;">' . $downloadsTitle . '</label>';
		$s .= '<select name="file_download_list" id="file_download_list" style="display: inline-block;">';
		$s .= '<option>' . $optionDefault . '</option>';

		foreach( $rows as $row ) {
			$fileID = $row['sidebar_download_file']['ID']; 
			$filesize = formatBytes(filesize( get_attached_file( $fileID ) ));
			$s .= '<option value="' . $row['sidebar_download_file'] .'">' . $row["sidebar_download_title"] . " ($filesize) " . '</option>';
		}

		$s .= '</select>';
		$s .= '</form>';

		$s .= '<button class="usa-button" id="downloadFile" style="width: 100%;">Download</button>';
		$sidebarDownloads = $s;
	}
}




get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main bbg__2-column" role="main">

			<div class="usa-grid-full">

				<?php while ( have_posts() ) : the_post();
					//$videoUrl = get_post_meta( get_the_ID(), 'featured_video_url', true );
				?>
					<article id="post-<?php the_ID(); ?>" <?php post_class("bbg__article"); ?>>


						<div class="usa-grid">
							<header class="page-header">

								<?php if($post->post_parent) {
									//borrowed from: https://wordpress.org/support/topic/link-to-parent-page
									$parent = $wpdb->get_row("SELECT post_title FROM $wpdb->posts WHERE ID = $post->post_parent");
									$parent_link = get_permalink($post->post_parent);
									?>
									<h5 class="bbg-label--mobile large"><a href="<?php echo $parent_link; ?>"><?php echo $parent->post_title; ?></a></h5>
								<?php } else{ ?>
									<h5 class="bbg-label--mobile large"><?php the_title(); ?></h5>
								<?php } ?>


							</header><!-- .page-header -->
						</div>


						<?php
							$hideFeaturedImage = FALSE;
							if ($videoUrl != "") {
								echo featured_video($videoUrl);
								$hideFeaturedImage = TRUE;
							} elseif ( has_post_thumbnail() && ( $hideFeaturedImage != 1 ) ) {
								echo '<div class="usa-grid-full">';
								$featuredImageClass = "";
								$featuredImageCutline = "";
								$thumbnail_image = get_posts(array('p' => get_post_thumbnail_id($id), 'post_type' => 'attachment'));
								if ($thumbnail_image && isset($thumbnail_image[0])) {
									$featuredImageCutline = $thumbnail_image[0]->post_excerpt;
								}

								$src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), array( 700,450 ), false, '' );

								echo '<div class="single-post-thumbnail clear bbg__article-header__thumbnail--large bbg__article-header__banner" style="background-image: url('.$src[0].'); background-position: '.$bannerAdjustStr.'">';
								echo '</div>';
								echo '</div> <!-- usa-grid-full -->';
							}
						?><!-- .bbg__article-header__thumbnail -->





						<div class="usa-grid">

							<header class="entry-header">
								<!-- .bbg-label -->
								<?php if($post->post_parent) {
									//borrowed from: https://wordpress.org/support/topic/link-to-parent-page
									$parent = $wpdb->get_row("SELECT post_title FROM $wpdb->posts WHERE ID = $post->post_parent");
									$parent_link = get_permalink($post->post_parent);
									?>
									<!--<h5 class="entry-category bbg-label"><a href="<?php echo $parent_link; ?>"><?php echo $parent->post_title; ?></a></h5>-->
									<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

								<?php } else{ ?>
									<!--<h5 class="entry-category bbg-label"><?php the_title(); ?></h5>-->
									<?php $headlineStr = "<h1 class='bbg__entry__secondary-title'>" . $headline . "</h1>"; ?>
								<?php } ?>

							</header><!-- .entry-header -->







							<div class="entry-content bbg__article-content large <?php echo $featuredImageClass; ?>">
								<div class="bbg__profile__content">
								<?php echo $headlineStr; ?>

								<?php the_content(); ?>
								</div>



								<?php
									//Add blog posts below the main content
									$relatedCategory=get_field('related_category_posts', $id);

									if ( $relatedCategory != "" ) {
										$qParams2=array(
											'post_type' => array('post'),
											'posts_per_page' => 2,
											'cat' => $relatedCategory->term_id,
											'orderby' => 'date',
											'order' => 'DESC'
										);
										$categoryUrl = get_category_link($relatedCategory->term_id);
										$custom_query = new WP_Query( $qParams2 );
										if ($custom_query -> have_posts()) {
											echo '<h6 class="bbg-label"><a href="'.$categoryUrl.'">'.$relatedCategory->name.'</a></h6>';
											echo '<div class="usa-grid-full">';
											while ( $custom_query -> have_posts() )  {
												$custom_query->the_post();
												get_template_part( 'template-parts/content-portfolio', get_post_format() );
											}
											echo '</div>';
										}
										wp_reset_postdata();
									}
								?>



							</div><!-- .entry-content -->

							<div class="bbg__article-sidebar large">

								<?php
									if ( $secondaryColumnContent != "" ) {
										echo $secondaryColumnContent;
									}

									echo $sidebarDownloads;
								?>

							</div><!-- .bbg__article-sidebar -->

						</div>



						<div class="usa-grid">
							<footer class="entry-footer bbg-post-footer 1234">
								<?php
									edit_post_link(
										sprintf(
											/* translators: %s: Name of current post */
											esc_html__( 'Edit %s', 'bbginnovate' ),
											the_title( '<span class="screen-reader-text">"', '"</span>', false )
										),
										'<span class="edit-link">',
										'</span>'
									);
								?>
							</footer><!-- .entry-footer -->
						</div><!-- .usa-grid -->


					</article><!-- #post-## -->

					<div class="bbg-post-footer">
					<?php
						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;
					?>
					</div>

				<?php endwhile; // End of the loop. ?>
			</div><!-- .usa-grid-full -->
		</main><!-- #main -->
	</div><!-- #primary -->

<?php /*get_sidebar();*/ ?>
<?php get_footer(); ?>
