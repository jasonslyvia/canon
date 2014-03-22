<?php
/*
 *  生成保存和喜欢的相关信息
 *
 *  @param {int} image_id 图片id（即post_id）
 *  @param {int} user_id 用户id（即post_id）
 *  @return {array_a}
 */
function get_pic_save_like($image_id, $user_id){

    require_once('common.php');
    define('WP_USE_THEMES', false);
    require_once(CANON_ABSPATH.'wp-load.php');

    $like_arr = get_pic_op($image_id, $user_id, "like");
    $save_arr = get_pic_op($image_id, $user_id, "save");

    return array("like" => $like_arr,
                 "save" => $save_arr);

}


/*
 *  获得保存或喜欢的详细数据
 *
 *  @param {int} image_id 图像id
 *  @param {int} user_id 用户id
 *  @param {string} type 指明类型是喜欢还是保存 可选值：like save
 *  @return {array_a}
 */
function get_pic_op($image_id, $user_id, $type){

    if ($type !== "save" && $type !== "like") {
        return null;
    }
    $type_cn = $type == "save" ? "保存" : "喜欢";
    $table = $type == "save" ? "pic_save" : "pic_like";

    global $wpdb;
    //当前用户是否喜欢或保存该图片
    $current_user_op = $wpdb->get_var(
            $wpdb->prepare("
                    SELECT count(*) FROM {$table}
                    WHERE pic_id = %d AND user_id = %d
                ", $image_id, $user_id)
    );
    $current_user_op = $current_user_op == 0 ? '' : ' active';

    //获取操作的数据
    $op_row = $wpdb->get_results(
        $wpdb->prepare("
            SELECT SQL_CALC_FOUND_ROWS user_id, time FROM {$table}
            WHERE pic_id = %d
            ORDER BY time DESC
            LIMIT 0,10
        ", $image_id)
    );


    //用于显示简略版信息
    $op_sample_html = "<p class='{$type}s'>";
    //用于显示完整版信息
    //<div class="group clearfix">
    $op_list_html = '';

    //若存在用户数据则进行渲染
    if ($op_row) {
        //总操作数
        $op_count = $wpdb->get_var('SELECT FOUND_ROWS()');

        //保存操作该图片的所有用户信息
        $i = 0;
        foreach ($op_row as $row) {
            //操作该图片的用户id
            $uid = $row->user_id;
            //获取用户姓名
            $name = get_userdata($uid)->display_name;
            //获取用户头像
            $avatar = AVATAR . get_user_meta($uid, 'avatar_small', true);

            if ($i < 2) {
                $op_sample_html .=
                "<a href='/profile/{$uid}'>".
                    "{$name}".
                 "</a> ";
            }

            $op_list_html .=
            "<a href='/profile/{$uid}' data-time='{$row->time}'>".
                "<img src='{$avatar}' width='30' height='30' />".
            "</a>".
            "<p><a href='/profile/{$uid}'>{$name}</a> {$type_cn}了这张图片</a></p>";

            $i++;
        }

        if ($op_count > 2) {
            $real_count = $op_count - 2;
            $op_sample_html .= " 和<b>另外{$real_count}个人</b>也{$type_cn}了这张图片</p>";
        }

    }
    //若无操作记录
    else{
        $op_count = 0;
        $op_sample_html .= "做第一个{$type_cn}的人吧！</p>";
    }

    //将查询结果封装进数组
    $arr["count"] = $op_count;
    $arr["class_name"] = $current_user_op;
    $arr["sample_html"] = $op_sample_html;
    $arr["list_html"] = $op_list_html;

    return $arr;
}

 ?>