<?php
/*
 * Template Name: Feed Reader Template
 * Description: WPeMatico RSS Feed Reader default template.
 */
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

  if (file_exists(get_stylesheet_directory() . '/header.php')) {
    get_header();
  } else {
  ?>
  <!DOCTYPE html>
  <html <?php language_attributes(); ?>>
    <head>
      <meta charset="<?php bloginfo('charset'); ?>">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>
  <?php
  }
?>

<div id="primary" class="content-area">
  <main id="main" class="site-main">
    <?php
    while (have_posts()) :
      the_post();
      the_content();
    endwhile;
    ?>
  </main>
</div>

<?php
  if (file_exists(get_stylesheet_directory() . '/footer.php')) {
    get_footer();
  } else {
    ?>
    <footer>
      <p>Powered by <a href="https://etruel.com/" target="_blank">Etruel.com</a></p>
    </footer>
    <?php
  }
?>
