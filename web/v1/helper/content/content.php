<?php
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
require '/home/bbc_helper/src/CURRENT/web/v1/helper/core/Config.php';
$id = $_REQUEST["id"];
$api_list = \Config::get('api_list'); 
// 接收API参数，转换json格式
//=======staging  start==========
$api	= $api_list["image"]["url"];
$imgurl = $api_list["image"]["url"];

$url	= $api . "v1/helper/api.php?c=helper&a=getcontent&id=" . $id;
$rs		= json_decode(file_get_contents($url));

// 接口返回参数判断是否成功
if ($rs->status->code != 200) {
	exit ($rs->status->message);
}

// 接收判断内容是否存在
$data = $rs->data;
if (empty($data)) {
	exit ("no contents");
}


// 匹配内容中的图片地址
$contents = $data->content;
$pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/"; 
preg_match_all($pattern, $contents, $match); 

// 如果内容中有图片，并且是相对地址，给图片加上API接口
if (!empty($match[1])) {
	for ($i=0; $i<count($match[1]); $i++) {
		if (strpos($match[1][$i],"http") != false) {
			$newsrc = $imgurl . $match[1][$i];
			$contents = str_replace($match[1][$i], $newsrc, $contents);
		}
	}
}

// 读取模板
$views_url = "./content.html";
$rs_views = file_get_contents($views_url);

// 替铁模板内容返回到页面
$arr = array("<%=title %>", "<%=keywords %>", "<%=description %>", "<%=source %>", "<%=hits %>", "<%=content %>", "<%=tag %>");
$arr_contents = array($data->title, $data->keywords, $data->description, $data->username, $data->hits + $data->lies, $contents, $data->tag);
echo str_replace($arr, $arr_contents, $rs_views);