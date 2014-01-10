<?php
/*
 *  添加一张图片到数据库
 *
 *  @param {string} filename 图片文件名
 *  @param {string} nonce AJAX凭证
 *  @param {int} userId 用户id
 *  @param {int} width 图片宽
 *  @param {int} height 图片高
 *  @param {string} optional referrer 图片图片来源
 *  @param {string} optional title 图片标题
 *  @param {string} optional category 图片所属主题
 *  @return {json}
 */
header('Content-Type: application/json');
require('common.php');
define(DEFAULT_WIDTH, 200);


if (verify_ajax(array("filename"), "post", true, "upload_pic")) {

    $filename = $_POST['filename'];
    $userId = get_current_user_id();
    $title = $_POST['title'] ? $_POST['title'] : '无标题';
    $category = $_POST['category'];

    //若是远程图片，首先将其保存到本地
    if (preg_match('/^https?:\/\//i', $filename)) {
        $filename = save_remote_image($filename);
    }

    //根据用户所处的用户组，判断内容是否需要审核
    $author = wp_get_current_user();

    //若用户不是『订阅者』（Wordpress默认用户角色），则无需审核
    if (!in_array("subscriber", (array)$author->roles)) {
        $post_status = "publish";
        $result_message = "照片发布成功！";
    }
    else{
        $post_status = "draft";
        $result_message = "照片发布成功，请等待管理员审核！";
    }

    //添加新文章
    $post_name = basename($filename);
    $post_id = wp_insert_post(array("post_author" => $userId,
                                    "post_name" => substr(md5(time().$post_name),
                                                          5,
                                                          26),
                                    "post_content" => $filename,
                                    "post_title" => $title,
                                    "post_status" => $post_status,
                                    "post_category" => array($category)));
    if (!$post_id) {
        send_result(true, "无法创建新内容");
    }
    else{
        //增加文章信息
        add_post_meta($post_id, 'like_count', 0);
        add_post_meta($post_id, 'save_count', 1);
        add_post_meta($post_id, 'width', $_POST['width']);
        add_post_meta($post_id, 'height', $_POST['height']);
        add_post_meta($post_id, 'referrer', $_POST['referrer']);
        add_post_meta($post_id, 'post_view', 0);

        global $wpdb;
        $save_record = $wpdb->insert('pic_save',
                                     array("pic_id" => $post_id,
                                           "user_id" => $userId));

        send_result(false, $result_message, array("postId" => $post_id));
    }
}


/*
 *  将远程图片保存到本地，保存规则为 uploads/images/用户数字id/图片名
 *  图片名为图片文件名（不含后缀）md5后加后缀
 *
 *  @param {string} $url 远程图片地址
 *  @return {string} 返回新保存文件的文件名
 */
function save_remote_image($url){
    $user = get_current_user_id();
    $target_folder = '../uploads/images/'.$user;
    //检查目标文件夹是否存在，若不存在则尝试创建
    if (!file_exists($target_folder)) {
        if(!@mkdir($target_folder) || !chmod($target_folder, 0755)){
            send_result(true, "无法创建文件夹");
        }
    }

    $path = preg_replace('/^https?:\//i', '', $url);
    $p_info = pathinfo($path);
    $filename = $p_info["filename"];
    $extension = $p_info["extension"];

    $image = file_get_contents($url);
    if (strlen($image) > 5242880) {
        send_result(true, "远程图片大小超过限制！");
    }

    $new_filename = md5($filename) . '.' . $extension;
    $target_file = $target_folder . '/' . $new_filename;
    if (file_put_contents($target_file, $image)) {
        $thumbname = $target_folder. '/' .md5($filename).'_200.';
        create_thumb($target_file, $thumbname, 200);
        return $new_filename;
    }
    else{
        send_result(true, "远程图片保存失败！");
    }
}


/*
 *  创建指定尺寸的缩略图
 *
 *  @param {string} $filename 图像文件名
 *  @param {string} $thumbname 缩略图文件名
 *  @param {int} optional $target_width 目标尺寸，不设置则为 DEFAULT_WIDTH
 */
function create_thumb($filename, $thumbname, $target_width){
    //获取图片长宽信息
    list($width, $height) = getimagesize($filename);
    $target_width = $target_width ? $target_width : DEFAULT_WIDTH;

    $extension = pathinfo($filename);
    $extension = $extension['extension'];

    if ($width > $target_width) {
        //创建宽度为 target_width 的缩略图
        $newwidth = $target_width;
        $newheight = $height * $newwidth / $width;

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $src = imagecreatefromjpeg($filename);
                break;
            case 'png':
                $src = imagecreatefrompng($filename);
                break;
            case 'gif':
                $src = imagecreatefromgif($filename);
                break;
            default:
                $src = null;
                break;
        }

        if ($src) {
            //创建空白画布
            $dst = imagecreatetruecolor($newwidth, $newheight);
            //获得新尺寸的图像
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            //将新图像储存
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $new_pic = imagejpeg($dst, $thumbname . $extension);
                    break;
                case 'png':
                    $new_pic = imagepng($dst, $thumbname . $extension);
                    break;
                case 'gif':
                    $new_pic = imagegif($dst, $thumbname . $extension);
                    break;
                default:
                    break;
            }
            //释放内存
            imagedestroy($dst);
        }
    }
}

 ?>