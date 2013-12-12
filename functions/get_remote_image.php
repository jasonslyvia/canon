<?php
/*
 *  抓取远程图片地址
 *
 *  @method post
 *
 *  @param {string} url 远程图片地址或网址
 *  @return {json}
 */
header('Content-Type: application/json');

if (verify_ajax(array("url"), "post", true, "upload_pic")) {
    # code...
}


?>