<?php
/*
 *  根据一定的规则展示图片
 *  @require global $query 需要全局$query对象
 */

/*======================================
获取当前登录用户信息
======================================*/
$c_user_id = get_current_user_id();
//当前用户保存的所有图片
$c_saved_record = $wpdb->get_col("
    SELECT pic_id as p FROM pic_save
    WHERE user_id = {$c_user_id}
  ");
//当前用户喜欢的所有图片
$c_liked_record = $wpdb->get_col("
    SELECT pic_id as p FROM pic_like
    WHERE user_id = {$c_user_id}
  ");


while ($query && $query->have_posts()) {
    $query->the_post();

    $id = get_the_ID();
    //author_id决定了当前图片的储存位置
    $author_id = get_the_author_meta('ID');
    $thumb = preg_replace('/(\..{3,4})$/', '_200$1', get_the_content());
    $width = get_post_meta($id, 'width', true);
    $height = get_post_meta($id, 'height', true);
    $like_count = get_post_meta($id, 'like_count', true);
    $save_count = get_post_meta($id, 'save_count', true);

    //判断当前用户是否保存或喜欢了当前被浏览用户保存的图片
    //以确定是否给操作选项增加 active 的 class
    if (in_array($id, $c_saved_record)) {
        $save_text = "编辑";
        $save_class = " active";
    }
    else{
        $save_text = "保存";
        $save_class = "";
    }
    if (in_array($id, $c_liked_record)) {
        $like_class = " active";
    }
    else{
      $like_class = "";
    }
?>

    <div class="polaroid tile saved" id="image_<?php echo $id; ?>"
        data-likes="<?php echo $like_count; ?>"
        data-saves="<?php echo $save_count; ?>"
        data-w="<?php echo $width; ?>"
        data-h="<?php echo $height; ?>">
      <div class="options">
        <div class="save<?php echo $save_class;?>" data-id="<?php echo $id; ?>"
             title="<?php echo $save_text; ?>这个图像">
          <em></em><span><?php echo($save_text); ?></span>
        </div>
        <div class="like<?php echo $like_class;?>" data-id="<?php echo $id; ?>"
             title="喜欢这个图像">
          <em></em><span>喜欢</span>
        </div>
      </div>
      <a href="<?php the_permalink(); ?>" class="imageLink">
        <img src="<?php echo URL."/uploads/images/$author_id/".$thumb;?>"
              alt="<?php the_title(); ?>" width="200"
              height="<?php echo $height * 200 / $width; ?>"></a>
      <div class="stats">
        <p>
          <a href="<?php the_permalink(); ?>">
            <em class="s"></em><span class="saves"><?php echo $save_count; ?></span>
            <em class="l"></em> <span class="likes"><?php echo $like_count; ?></span>
          </a>
        </p>
      </div>
    </div>

<?php  } ?>