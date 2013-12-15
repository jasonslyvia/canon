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

require_once("common.php");

if (verify_ajax(array("url"), "post", true, "upload_pic")) {
    $url = $_POST["url"];
    if (!preg_match('/^(https?:\/\/)?(.+?\.)?.+?\..{2,4}(\/.*)?$/i', $url)) {
        send_result(true, "请输入有效的图片地址或网站！");
    }

    $html = @file_get_contents($url);
    if (!$html) {
        send_result(true, "无法获取该网页的内容！");
    }

    preg_match_all('/<img.*?src=[\'\"](.*?)[\'\"]/i', $html, $images);
    send_result(false,
                "图片获取成功",
                array("images" => array_values(array_filter(array_unique($images[1]),
                                                "is_valid_image"))));
}


function is_valid_image($image){
    return preg_match('/\.(jpg|jpeg|bmp|png|gif)$/i', $image);
}

?>