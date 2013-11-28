<?php
/*
 *  常用ajax脚本代码
 */

require_once('settings.php');

/*
 *  验证ajax请求是否合理
 *
 *  @param {array_n} keys 必须存在的参数名
 *  @param {string} optional type HTTP请求方法，不填写则使用$_REQUESTS
 *  @param {bool} optional check_nonce 对于涉及数据更改的操作验证nonce
 *  @param {string} optional nonce_name 生成nonce生成的phase
 *  @param {bool} optional display 是否直接返回结果
 *  @return {bool}
 */
function verify_ajax($keys, $type = '_REQUESTS',
                     $check_nonce = false, $nonce_name = '',
                     $display = true)
{
    try {
        //获得请求内容的数组
        if (stripos($type, 'post') !== false) {
            $request = $_POST;
        }
        else if (stripos($type, 'get') !== false) {
            $request = $_GET;
        }
        else{
            $request = $_REQUESTS;
        }

        //检查必填参数个数是否足够
        if (count($request) < count($keys)) {
            throw new Exception("请求参数异常", 1);
        }


        //使用 wp_verify_nonce 函数验证 nonce 有效性
        if ($check_nonce && $nonce_name != '') {
            define('WP_USE_THEMES', false);
            require_once(ABSPATH.'wp-load.php');

            //当前登录用户id
            $uid = get_current_user_id();

            //验证用户id设置是否正确及用户是否存在
            if (isset($request['userId']) || isset($request['user_id'])) {
                $user_id = $request['userId'] ? $request['userId'] : $request['user_id'];
                if ($user_id != $uid) {
                    throw new Exception("参数userId：{$user_id}与当前用户id：{$uid}不符", 1);
                }
                else if (get_user_by('id', $user_id) == false) {
                    send_result(true, "用户id{$user_id}不存在");
                }
            }

            //若设置了imageId，同时检查该图片id是否存在
            if (isset($request['imageId'])) {
                $image_id = $request['imageId'];
                if (get_post($image_id) == null) {
                    send_result(true, "图片id{$image_id}不存在");
                }
            }
            /*
                nonce的格式为 操作名称_用户id
                             如：upload_pic_128
            */
            $nonce = $request['nonce'];
            $nonce_name = preg_replace('/(_\d{1,})?/', '', $nonce_name);
            $nonce_name .= "_{$uid}";
            if (!wp_verify_nonce($nonce, $nonce_name)) {
                throw new Exception("权限验证失败", 1);
            }
        }

        //验证是否拥有所有的必须参数
        foreach ($keys as $key) {
            if (!array_key_exists($key, $request)) {
                throw new Exception("缺少必要参数{$key}", 1);
            }
        }

        return true;

    } catch (Exception $e) {
        if ($display) {
            send_result(true, $e->getMessage());
        }
        else{
            return false;
        }
    }
}

/*
 *  返回ajax结果
 *
 *  @param {bool} error 是否发生错误
 *  @param {string} message 返回的提示信息
 *  @param {array} extra 额外的信息，关联数组
 *  @param {bool} only_extra 是否仅显示额外的信息
 *  @return {返回类型}
 */
function send_result($error, $message, $extra = array(), $only_extra = false){

    if ($only_extra) {
        echo json_encode($extra);
    }
    else{
        echo json_encode(array_merge(
                            array("error" => $error,
                                 "message" => $message),
                            $extra));
    }
    exit();
}


/*
 *  获取缩略图地址
 *
 *  @param {string} pic 原始图像文件名
 *  @param {int} optional uid 用户id
 *  @param {bool} optional prefix 是否加上完整路径
 *  @return {string}
 */
function get_thumb($pic, $uid = null, $prefix = null){
    if ($prefix) {
        return IMAGE_PATH. $uid.'/'.preg_replace('/(\..{3,4})$/', '_200$1', $pic);
    }
    else{
        return preg_replace('/(\..{3,4})$/', '_200$1', $pic);
    }
}

?>