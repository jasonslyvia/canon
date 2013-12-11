<?php get_header(); ?>

<div id="luka">
  <div class="hamburger"></div>
  <p><a href="/"><?php echo get_the_title().' - '.get_bloginfo(); ?></a></p>
</div>

<div id="page">
  <div class="headerSpacer"></div>
  <div id="maincontent" class="center">
    <?php the_content(); ?>
  </div>
</div>

<?php get_footer();?>