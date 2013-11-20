<?php
/*
 *  常用ajax脚本代码
 */

require_once('settings.php');


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