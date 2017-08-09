<?php
/**
 * file_upload class file
 */

if (!defined('IN_FINECMS')) exit();

class file_upload extends Fn_base {

    /**
     * 文件大小
     *
     * @var integer
     */
    protected $limit_size;

    /**
     * 文件名字
     *
     * @var string
     */
    protected $file_name;

    /**
     * 文件类型
     *
     * @var string
     */
    protected $limit_type;

    /**
     * 图片模式
     *
     * @var array
     */
    protected $image;


    /**
     * 构造函数
     *
     * @access public
     * @return boolean
     */
    public function  __construct() {
        $this->limit_size = 2097152;	//默认文件大小 2M
        return true;
    }

    /**
     * 取得文件扩展
     *
     * @return 扩展名
     */
    public function fileext() {
        return strtolower(trim(substr(strrchr($this->file_name['name'], '.'), 1, 10)));
    }

    /**
     * 取得文件名
     * 带扩展名和不带扩展名
     * @return
     */
    public function filename($ext = false) {
        return $ext ? $this->file_name['name'] : str_replace('.' . $this->fileext(), '', $this->file_name['name']);
    }


    /**
     * 设置文件字段,$_FIELDS['file']
     * @param  $file
     */
    public function set($file) {
        $this->file_name = $file;
        return $this;
    }

    /**
     * 设置图片模式
     * @param int $w
     * @param int $h
     * @param int $t
     * @return file_upload
     */
    public function set_image($w, $h, $t) {
        $this->image = array($w, $h, $t);
        return $this;
    }

    /**
     * 设置上传文件的大小限制.
     *
     * @param integer $size
     * @return unkonow
     */
    public function set_limit_size($size) {
        if ($size) $this->limit_size = $size;
        return $this;
    }

    /**
     * 设置上传文件允许的格式
     *
     * @param string $type
     * @return unkonow
     */
    public function set_limit_type($type) {
        if (!$type || !is_array($type)) return false;
        $this->limit_type = $type;
        return $this;
    }

    /**
     * 验证上传文件的格式
     *
     * @return boolean
     */
    protected function parse_mimetype() {
        if (!in_array($this->fileext(), $this->limit_type)) {
            return lang('lib-0', array('1' => $this->fileext(), '2' => implode(',', $this->limit_type)));
        }
        return false;
    }

    /**
     * 上传文件
     *
     * @param string $path		          上传后的目标目录/结尾
     * @param string $file_name		上传后的目标文件名
     * @return boolean              上传成功返回0，返回错误信息
     */
    public function upload($path, $file_name) {
//echo $path .'<br/>'.$file_name;
        $result = $this->parse_init();
        if ($result) return $result;
        //验证路径
//		if (!is_dir($path)) $this->mkdirs($path);
//		if (!@move_uploaded_file($this->file_name['tmp_name'], $path . $file_name)) {
        //logdebug('调用file_upload->aliupload');
        $file_name = $this->ali_upload($this->file_name['tmp_name'],$file_name);
        if (!$file_name) {
            return lang('lib-1', array('1' => $this->file_name['name']));
        }
        //图片则生成缩略图
        if(in_array($this->fileext(),array('jpg','jpeg','gif','png'))){
            $file_name = $this->image($path, $file_name);
        }
        return $file_name;
    }

    // add by yang.f
    public function ali_upload($file,$file_name) {
        require_once APP_ROOT . './extensions/upload/ALIOSS/SDK.php';
        $ret =false;
        $oss_sdk_service = new \ALIOSS();
        $api_list   = \Config::get('api_list');
        $bucket     = $api_list['alioss_bucket'];
        //logdebug(sprintf('bucket: %s',$bucket));
        //logdebug('根据文件大小选择上传方式');
        if($this->file_name['size']>2097152){
            //logdebug('开始传输大文件');
            set_time_limit(600);
            $upload_file = $oss_sdk_service->upload_file_by_file($bucket,$file_name, $file);
           //logdebug('结束传输大文件');
        }else{
            $content = '';
            $length = 0;
            $fp = fopen($file, 'r');
            //logdebug('开始获取小文件内容');
            if ($fp) {
                $f = fstat($fp);
                $length = $f['size'];
                while (!feof($fp)) {
                    $content .= fgets($fp, 8192);
                }
            }
            //logdebug('结束小文件内容');
            $upload_file_options = array('content' => $content, 'length' => $length);
            //logdebug('开始传输小文件');
            $upload_file = $oss_sdk_service->upload_file_by_content($bucket,$file_name , $upload_file_options);
            //logdebug('结束传输小文件');
        }
        //logdebug(sprintf('sdk上传返回信息：%s',var_export($upload_file,true)));
        if($upload_file->status == 200){
            //logdebug(sprintf('正常传输，返回文件名称:%s',$file_name));
            $ret = $file_name;
        }
        return $ret;
    }


    //========= ali_upload end 

    /**
     * 验证文件
     */
    protected function parse_init() {
        if (is_null($this->file_name['size']) || $this->file_name['size'] > $this->limit_size) {
            return lang('lib-2', array('1' => $this->file_name['name']));
        }
        if ($this->limit_type) return $this->parse_mimetype();
        return false;
    }

    /**
     * 递归创建目录
     * @param  $dir
     */
    protected function mkdirs($dir){
        if(!is_dir($dir)){
            $this->mkdirs(dirname($dir));
            mkdir($dir);
        }
    }

    /**
     * 处理图片，生成缩略图和剪切图
     * @param  $path
     * @param  $file_name
     * @return boolean
     */
    protected function image($path, $file_name) {
        if (empty($this->image)) return false;
        //图片处理
        $width   = (int)$this->image['0'];
        $height  = (int)$this->image['1'];
        $type    = $this->image['2'];
        if (empty($width) || empty($height)) return $file_name;
        $srcfile = $path . $file_name;
        $tofile  = $file_name . '.thumb.' . $width . 'x' . $height . '.' . $this->fileext();
        list($src_w, $src_h, $src_t) = getimagesize($srcfile);  // 获取原图尺寸
        $dst_scale = $width/$height; //目标图像长宽比
        $src_scale = $src_h/$src_w; // 原图长宽比
        if( $src_scale >= $dst_scale) {  // 过高
            $w = intval($src_w);
            $h = intval($dst_scale*$w);
            $x = 0;
            $y = ($src_h - $h)/3;
        } else { // 过宽
            $h = intval($src_h);
            $w = intval($h/$dst_scale);
            $x = ($src_w - $w)/2;
            $y = 0;
        }
        switch ($src_t) {
            case 1: //图片类型，1是gif图 
                $source = @imagecreatefromgif($srcfile);
                break;
            case 2: //图片类型，2是jpg图 
                $source = @imagecreatefromjpeg($srcfile);
                break;
            case 3: //图片类型，3是png图 
                $source = @imagecreatefrompng($srcfile);
                break;
        }
        if ($type) {
            // 剪裁
            $target  = imagecreatetruecolor($width, $height);
            $ret = imagecopy($target, $source, 0, 0 , 0, 0, $width, $height);
        } else {
            // 缩放
            $scale   = $width/$w;
            $target  = imagecreatetruecolor($width, $height);
            $final_w = intval($w*$scale);
            $final_h = intval($h*$scale);
            $ret = imagecopyresampled($target, $source, 0, 0, 0, 0, $final_w, $final_h, $w, $h);
        }
        $tmp_file = '/home/bbc_helper/tmp/'.uniqid('image').'.'. $this->fileext();
        imagejpeg($target,$tmp_file, 100);
        $ret = $this->ali_upload($tmp_file, $tofile);
        imagedestroy($target);
        return $tofile;
    }
}