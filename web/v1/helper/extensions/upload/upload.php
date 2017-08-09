<?php require("ALIOSS/SDK.php");
set_time_limit(0);
$oss_sdk_service = new \ALIOSS();
$tmp = explode('.', $_FILES["Filedata"]['name']);
$tmp_type = $tmp[count($tmp) - 1];
$file_name = time() . '_' . rand(10000, 99999) . '.' . $tmp_type;
$api_list   =   \Config::get('api_list');
$bucket     =   $api_list['alioss_bucket'];
$content = '';
$length = 0;
$fp = fopen($_FILES["Filedata"]["tmp_name"], 'r');
if ($fp) {
    $f = fstat($fp);
    $length = $f['size'];
    while (!feof($fp)) {
        $content .= fgets($fp, 8192);
    }
}
$upload_file_options = array('content' => $content, 'length' => $length);

$upload_file_by_content = $oss_sdk_service->upload_file_by_content($bucket, $file_name, $upload_file_options);
if($upload_file_by_content->status == 200){
    die('{ok}{"file":"' . $bucket .'/'. $file_name . '","small":"' . "" . 's' . "" . '","msg":"上传成功！"}');
}else{
    die('{ok}{"file":"' . $bucket .'/'. $file_name . '","small":"","msg":"上传失败！"}');
}
