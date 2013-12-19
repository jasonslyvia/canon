<?php
/*
 *  初始化一些必要的数据，如主题（对应wordpress中的category）及一些数据表等
 */

require('../functions/settings.php');
require_once(ABSPATH . '/wp-load.php');

if (!is_user_logged_in()) {
    wp_redirect('/');
    exit();
}

//修复使用wp_insert_category提示undefined的问题
if (!function_exists('wp_insert_category')) {
    function wp_insert_category($args){
        $cat_name = $args["cat_name"];
        $arg = array("name" => $args["cat_name"],
                     "description" => $args["category_description"]);

        wp_insert_term($cat_name, "category", $arg);
    }
}

if ($_POST['action'] == "init_category") {
    init_category();
}
else if ($_POST['action'] == "init_table") {
    init_table();
}
else if ($_POST['action'] == "reset_table") {
    $password = "canon_reset_2013";
    $in_password = $_POST["password"];
    if ($password != $in_password) {
        echo "你想干什么？！";
    }
    else{
        reset_table();
    }
}


//插入默认的主题类别
function init_category(){
    if (count(get_all_category_ids()) > 1) {
        return true;
    }
    $cat = array("cat_name" => "动物",
                 "category_nicename" => "animals",
                 "category_description" => "世间所有动人生灵",
                 "taxonomy" => "category");
    wp_insert_category($cat);

    $cat = array("cat_name" => "建筑",
                 "category_nicename" => "arichitecture",
                 "category_description" => "将美学与力学发展到极致的人类智慧成果",
                 "taxonomy" => "category");
    wp_insert_category($cat);

    $cat = array("cat_name" => "风景",
                 "category_nicename" => "scene",
                 "category_description" => "有一颗善于发现的心，走到哪里都是风景",
                 "taxonomy" => "category");
    wp_insert_category($cat);

    $cat = array("cat_name" => "人物",
                 "category_nicename" => "figure",
                 "category_description" => "造物主的神奇在人的身上发挥的淋漓尽致",
                 "taxonomy" => "category");
    wp_insert_category($cat);

    $cat = array("cat_name" => "植物",
                 "category_nicename" => "plants",
                 "category_description" => "静中有动，动中有静",
                 "taxonomy" => "category");
    wp_insert_category($cat);

    $cat = array("cat_name" => "品牌",
                 "category_nicename" => "branding",
                 "category_description" => "将自己介绍给世界",
                 "taxonomy" => "category");
    wp_insert_category($cat);

    $cat = array("cat_name" => "时尚",
                 "category_nicename" => "fashion",
                 "category_description" => "形于外，实于内",
                 "taxonomy" => "category");
    wp_insert_category($cat);

    $cat = array("cat_name" => "食物",
                 "category_nicename" => "food",
                 "category_description" => "让美味在瞬间定格",
                 "taxonomy" => "category");
    wp_insert_category($cat);

    $cat = array("cat_name" => "抽象",
                 "category_nicename" => "abstract",
                 "category_description" => "看似没有章法的，往往成就了章法本身",
                 "taxonomy" => "category");
    wp_insert_category($cat);

    $cat = array("cat_name" => "信息图",
                 "category_nicename" => "infographics",
                 "category_description" => "一张图让你看懂",
                 "taxonomy" => "category");
    wp_insert_category($cat);

    $cat = array("cat_name" => "设计",
                 "category_nicename" => "design",
                 "category_description" => "设计才是一切产品的灵魂，设计师说",
                 "taxonomy" => "category");
    wp_insert_category($cat);

    $cat = array("cat_name" => "静物",
                 "category_nicename" => "still-life",
                 "category_description" => "它在那里一动不动",
                 "taxonomy" => "category");
    wp_insert_category($cat);

    echo "目录初始化成功";
}


//插入自定义数据表pic_save, pic_like, user_relation
function init_table(){
    global $wpdb;

    $init_pic_like = $wpdb->get_results("
        delimiter $$

        CREATE TABLE `pic_like` IF NOT EXISTS (
          `like_id` int(11) NOT NULL AUTO_INCREMENT,
          `pic_id` int(11) NOT NULL,
          `user_id` int(11) NOT NULL,
          `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`like_id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1$$
    ");

    echo "喜欢数据表初始化成功";

    $init_pic_save = $wpdb->get_results("
        delimiter $$

        CREATE TABLE `pic_save` IF NOT EXISTS (
          `save_id` int(11) NOT NULL AUTO_INCREMENT,
          `pic_id` int(11) NOT NULL,
          `user_id` int(11) NOT NULL,
          `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`save_id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1$$
    ");

    echo "保存数据表初始化成功";

    $init_user_relation = $wpdb->get_results("
        delimiter $$

        CREATE TABLE `user_relation` IF NOT EXISTS (
          `relation_id` int(11) NOT NULL AUTO_INCREMENT,
          `follower_id` int(11) NOT NULL,
          `followee_id` int(11) NOT NULL,
          `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`relation_id`),
          UNIQUE KEY `relation_id_UNIQUE` (`relation_id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1$$
    ");

    echo "用户关注表初始化成功";

    $init_reset_password = $wpdb->get_results("
        delimiter $$

        CREATE TABLE `reset_password` IF NOT EXISTS (
          `reset_id` int(11) NOT NULL AUTO_INCREMENT,
          `email` varchar(128) NOT NULL,
          `token` varchar(512) NOT NULL,
          `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `reseted_flag` tinyint(1) NOT NULL DEFAULT 0,
          PRIMARY KEY (`reset_id`),
          UNIQUE KEY `reset_id_UNIQUE` (`reset_id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1$$
    ");
    echo "密码重置表初始化成功";
}

//重置所有数据
function reset_table(){
    global $wpdb;
    $reset_result = $wpdb->get_results("
        TRUNCATE pic_save;
        TRUNCATE pic_like;
        TRUNCATE user_relation;
        TRUNCATE wp_posts;
        TRUNCATE wp_postmeta;
    ");
}

if (count($_POST) === 0 ) {
?>
<!doctype html>
<html>
<head>
    <meta charset="utf8" />
</head>
<body>
    <h1>初始化canon项目</h1>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <select name="action">
            <option value="init_category">初始化图片主题（category）</option>
            <option value="init_table">初始化数据表</option>
            <option value="reset_table">重置所有数据</option>
        </select>
        <label for="password">若选择重置所有数据，请输入密码</label>
        <input name="password" id="password" type="pasword" />
        <div style="background-color:#eee;border:1px #333 dashed;padding:5px 15px;">
            <input type="submit" value="确认" />
        </div>
    </form>
</body>
</html>
<?php }



?>