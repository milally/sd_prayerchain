<?php
/**
 * Default Page Template
 */
// Header
//get_template_part( 'header', 'page' ); // this makes $content_title available
?>
		
<?php while ( have_posts() ) : the_post(); ?>

<div id="content">

	<div id="content-inner">

	<h1>Prayerchain</h1>
		
	</div>

</div>

<?php endwhile; ?>

<?php
get_footer();
?>
