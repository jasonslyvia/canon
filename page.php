<?php get_header(); ?>

<div id="luka">
  <div class="hamburger"></div>
  <p><a href="/"><?php echo get_the_title().' - '.get_bloginfo(); ?></a></p>
</div>

<div id="page">
  <div class="headerSpacer"></div>

<?php $pages = get_pages(array("sort_column" => "menu_order"));
    if (count($pages)) {
        echo '<ul class="page-menu clearfix">';
        foreach ($pages as $page) {
?>
<li>
    <a href="/<?php echo $page->post_name; ?>"<?php if (is_page($page->ID)) echo ' class="active"'; ?>>
        <?php echo $page->post_title; ?>
    </a>
</li>
<?php
        }
        echo '</ul>';
    }
?>
  </ul>

  <div id="maincontent" class="center page-content" style="width:700px;">
      <?php
        while ( have_posts() ) : the_post();
          the_content();
        endwhile;
      ?>

  </div>
</div>

<?php get_footer();?>