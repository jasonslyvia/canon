<?php
/*
 *	处理上传文件并生成缩略图
 *	文件储存逻辑：
 *		所有用户上传的图片储存在 ./uploads/images/用户数字id/ 文件夹下
 *		用户头像储存在 ./uploads/avatar/ 下，以用户数字id命名
 *	** 若图像存在则统一替换 **
 *
 *	@param {mixed} $_FILES
 *	@return {json}	错误编码及错误信息，若成功返回图片文件名及长宽信息
 *
 */

define('FILESIZE', 5242880);
define('DEFAULT_WIDTH', 200);

header('Content-Type: application/json');


require_once('../functions/common.php');
require_once(CANON_ABSPATH.'wp-load.php');


$type = $_POST['type'] == "avatar" ? "avatar" : "image";
//firefox下flash无法将正确的cookie传到服务器，因此无法获取用户登录状态
//出此下策，亟待解决！！ 对高级浏览器尝试HTML5 API
$user = $_POST['userId'];
if (!$user) {
	send_result(true, "无法获取用户");
}

//设置目标文件夹
if ($type == "avatar") {
	$targetFolder = './avatar/';
}
else{
	$targetFolder = './images/'.$user;
}

//检查目标文件夹是否存在，若不存在则尝试创建
if (!file_exists($targetFolder)) {
	if(!@mkdir($targetFolder) || !chmod($targetFolder, 0755)){
		send_result(true, "无法创建文件夹");
	}
}

if (!empty($_FILES)) {
	//允许的文件后缀
	$fileTypes = array('jpg','jpeg','gif','png', 'bmp');
	//获取文件路径信息
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	//文件上传后储存在服务器上的临时文件名
	$tempFile = $_FILES['Filedata']['tmp_name'];

	if (!is_uploaded_file($tempFile)) {
		send_result(true, "非上传文件！".$_FILES['error']);
	}
	else if(filesize($tempFile) > FILESIZE){
		send_result(true, "超过文件大小限制！");
	}

	//图片宽度最小限制为 200
	list($temp_width) = getimagesize($tempFile);
	if ($temp_width < DEFAULT_WIDTH) {
		send_result(true, "图片宽度最小限制为 200 像素");
	}

	//生成要储存的文件名
	$extension = strtolower($fileParts['extension']);
	if ($type == "avatar") {
		$filename = $user . '.' . $extension;
	}
	else{
		$filename = md5($fileParts['basename']) .'.'. $extension;
	}
	$targetFile = $targetFolder .'/'. $filename;

	//若符合后缀名要求
	if (in_array($extension, $fileTypes)) {
		//尝试将临时文件夹中的文件移动到目标文件夹
		if(!move_uploaded_file($tempFile, $targetFile)){
			send_result(true, "无法移动文件！");
		}
		//若成功移动，则开始生成缩略图
		else{
			if ($type == "avatar") {
				$small_avatar = 'avatar/'.$user.'_small.';
				$large_avatar = 'avatar/'.$user.'_large.';
				create_thumb($targetFile, $small_avatar, 45);
				create_thumb($targetFile, $large_avatar, 200);

				$small_avatar = substr($small_avatar, 7).$extension;
				$large_avatar = substr($large_avatar, 7).$extension;

				update_user_meta($user, 'avatar', $large_avatar);
				update_user_meta($user, 'avatar_small', $small_avatar);

				send_result(false,
							"上传成功",
							array("small" => $small_avatar,
								  "large" => $large_avatar
				));
			}
			else{
				$thumbname = 'images/'. $user . '/' . md5($fileParts['basename']).'_'.DEFAULT_WIDTH.'.';
				list($width, $height) = create_thumb($targetFile,
												$thumbname,
												DEFAULT_WIDTH);

			    //判断图片颜色
			    require_once('../functions/get_color.php');
			    $image = new ColorsOfImage($targetFile, 15, 1);
			    $colors = $image->getProminentColors();

				send_result(false, "上传成功", array("filename" => $filename,
									   "width" => $width,
									   "height" => $height,
									   "color" => $colors[0]));
			}
		}
	}
	else {
		send_result(true, "文件类型不符合要求！");
	}
}
else{
	send_result(true, "无法获取文件信息！");
}


/*
 *  创建指定尺寸的缩略图
 *
 *  @param {string} $filename 图像文件名
 *	@param {string} $thumbname 缩略图文件名
 *	@param {int} optional $target_width	目标尺寸，不设置则为 DEFAULT_WIDTH
 *  @return {array} ($width, $height)
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
	return array($width, $height);
}
?>