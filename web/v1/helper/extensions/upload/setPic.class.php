<?php
/**
 *@info 图片尺寸控制，图片缩略图生成，水印生成
 *@example 见本页底部
 */
define("PIC_WATER_TEXT", "淘金易"); //水印水字
define("PIC_WATER_LOGO", CMS_ROOT."app/upload/logo.png"); //水印LOGO
define("PIC_WATER_FONT", CMS_ROOT."app/upload/STLITI.TTF"); //水印字体文件


class setPic {
    private $err; //错误提示
    private $picSrc; //源图片地址
    private $image; //源图片对象
    private $width; //宽
    private $height; //高
    private $sizeObj; //图片大小限制对象(img:资源,w:宽,h：高)
    private $smallObj; //缩略图对象数组(img:资源,w:宽,h：高)
    private $waterObj; //水印图对象(w:宽,h：高)
    private $x; //水印x坐标
    private $y; //水印y坐标
    private $xx; //水平偏移
    private $yy; //纵向偏移
    private $fun; //图像生成函数
    //以下为可配置参数/////////////////
    public $font;
    public $position;
    public $margin;
    public $fontsize;
    public $fontcolor;
    
    public function __construct($picSrc) {
        $this->picSrc = $picSrc;
        if (file_exists($this->picSrc)) {
            //@ini_set("memory_limit","100M");//处理一些超大图片需要开启
            $info = getimagesize($this->picSrc);
            $this->width = $info [0];
            $this->height = $info [1];
            $this->err = "";
            $this->sizeObj = null;
            $this->smallObj = Array ();
            $this->waterObj = null;
            $this->font = PIC_WATER_FONT; //水印字体文件，若为文字水印必须配置此参数
            $this->position = "br"; //水印位置
            $this->margin = 5; //水印边距
            $this->fontsize = 24; //水印字体大小
            $this->fontcolor = "#cccccc"; //水印字体色
            switch ($info [2]) {
                case 1 :
                    $this->image = imagecreatefromgif($this->picSrc);
                    $this->fun = "imagegif";
                    break;
                case 2 :
                    $this->image = imagecreatefromjpeg($this->picSrc);
                    $this->fun = "imagejpeg";
                    break;
                case 3 :
                    $this->image = imagecreatefrompng($this->picSrc);
                    $this->fun = "imagepng";
                    break;
                default :
                    $this->err = "不支持“" . $info ["mime"] . "”格式的图片\r\n";
                    return;
            }
        } else {
            $this->err = "源图片“" . $picSrc . "”不存在\r\n";
        }
    }
    
    //执行
    public function ok() {
        if ($this->err != "") {
            echo "[ERROR] " . $this->err;
            if (isset($this->image))
                imagedestroy($this->image);
            return false;
        }
        $fun = $this->fun; //执行函数
        foreach ( $this->smallObj as $small ) { //缩略图
            $path = dirname($small ["src"]);
            if (! file_exists($path))
                mkdir($path);
            copy($this->picSrc, $small ["src"]); //copy一份原图
            $fun($small ["img"], $small ["src"]);
            imagedestroy($small ["img"]);
        }
        if ($this->sizeObj || $this->waterObj)
            $fun($this->image, $this->picSrc); //限制大小或生成水印
        imagedestroy($this->image);
        return true;
    }
    
    //限制图片大小
    public function size($w, $h) {
        if ($this->err != "")
            return;
        if ($w !== 0 && $h !== 0 && ($w < $this->width || $h < $this->height)) {
            $this->sizeObj = $this->formatSmallImage($w, $h);
            $this->image = $this->sizeObj ["img"]; //将源图片资源更属性改为限制后的资源属性
            $this->width = $this->sizeObj ["w"];
            $this->height = $this->sizeObj ["h"];
        }
    }
    
    //缩略图
    public function small($w, $h, $src) {
        if ($this->err != "")
            return;
        if ($w !== 0 && $h !== 0 && ($w < $this->width || $h < $this->height)) {
            $s = $this->formatSmallImage($w, $h);
            $s ["src"] = $src;
            $this->smallObj [] = $s;
        }
    }
    
    //水印
    public function water($resource) {
        if ($this->err != "")
            return;
        if (file_exists($resource)) { //若资源是文件，则视为加图片水印
            $this->waterImage($resource);
        } else { //文字水印
            $this->waterText($resource);
        }
    }
    
    /*内部函数区域***********************************************************************/
    
    //生成缩略图对象
    private function formatSmallImage($w, $h) {
        $p = Array ();
        if ($w !== 0 && $h !== 0) {
            $size = $this->getSmallSize($this->width, $this->height, $w, $h); //获取缩略图等比尺寸
            if (function_exists("imagecopyresampled")) { //imagecopyresampled速度相对慢，但效果精细
                $p ["img"] = imagecreatetruecolor($size ["w"], $size ["h"]); //新建一个图像
                imagecopyresampled($p ["img"], $this->image, 0, 0, 0, 0, $size ["w"], $size ["h"], $this->width, $this->height); //生成一个原图的缩略对象
            } else { //所有GD版本中有效，速度相对快，但比较粗糙
                $p ["img"] = imagecreate($size ["w"], $size ["h"]);
                imagecopyresized($p ["img"], $this->image, 0, 0, 0, 0, $size ["w"], $size ["h"], $this->width, $this->height);
            }
            $p ["w"] = $size ["w"];
            $p ["h"] = $size ["h"];
        }
        return $p;
    }
    
    //获取缩略图等比尺寸
    private function getSmallSize($oldw, $oldh, $w, $h) {
        $w_h = Array ();
        if ($oldw < $w && $oldh < $h) { //原尺寸小于缩略尺寸，直接返回原尺寸
            $w_h ["w"] = $oldw;
            $w_h ["h"] = $oldh;
            return $w_h;
        }
        $wBh = $oldw / $oldh; //原尺寸宽高比
        if ($wBh > $w / $h) { //若原宽高比大于缩略宽高比，则以缩略宽为基准
            $w_h ["w"] = $w;
            $w_h ["h"] = $w / $wBh;
        } else { //若原宽高比小于缩略宽高比，则以缩略高为基准
            $w_h ["h"] = $h;
            $w_h ["w"] = $h * $wBh;
        }
        return $w_h;
    }
    
    //图片文印
    private function waterImage($resource) {
        $info = getimagesize($resource);
        $this->waterObj ["w"] = $info [0];
        $this->waterObj ["h"] = $info [1];
        if ($this->waterObj ["w"] < $this->width && $this->waterObj ["h"] < $this->height) {
            switch ($info [2]) {
                case 1 :
                    $waterImg = imagecreatefromgif($resource);
                    break;
                case 2 :
                    $waterImg = imagecreatefromjpeg($resource);
                    break;
                case 3 :
                    $waterImg = imagecreatefrompng($resource);
                    break;
                default :
                    $this->err = "不支持" . $info ["mime"] . "格式的图片\r\n";
                    return;
            }
            $this->pos(); //设置水印坐标
            imagealphablending($this->image, true); //设置混色模式
            imagecopy($this->image, $waterImg, $this->x + $this->xx, $this->y + $this->yy, 0, 0, $this->waterObj ["w"], $this->waterObj ["h"]);
        }
    }
    
    //文字水印
    private function waterText($resource) {
        if (! file_exists($this->font)) {
            $this->err = "字体文件“" . $this->font . "”不存在\r\n";
            return;
        }
        $img = imagettfbbox($this->fontsize, 0, $this->font, $resource); //将文字写到图中,需Freetype库
        $this->waterObj ["w"] = $img [2] - $img [6];
        $this->waterObj ["h"] = $img [3] - $img [7];
        if ($this->waterObj ["w"] < $this->width && $this->waterObj ["h"] < $this->height) {
            $color = $this->fontcolor;
            if (strlen($color) == 7) {
                $R = hexdec(substr($color, 1, 2));
                $G = hexdec(substr($color, 3, 2));
                $B = hexdec(substr($color, 5));
                imagealphablending($this->image, true); //设置混色模式
                $this->pos(); //设置水印坐标
                $color = imagecolorallocate($this->image, $R, $G, $B);
                imagettftext($this->image, $this->fontsize, 0, $this->x + $this->xx, $this->y + $this->yy + $this->waterObj ["h"], $color, $this->font, $resource);
                unset($img);
            } else {
                $this->err = "错误的色码“" . $color . "”，请使用井号加六位十六进制数表示，如：#FFFFFF\r\n";
                return;
            }
        }
    }
    
    //获取水印位置坐标，标识说明：先是上t-top、中c-center、下b-bottom，再是左l-left、中c-center、右r-right
    private function pos() {
        $w = $this->width - $this->waterObj ["w"];
        $h = $this->height - $this->waterObj ["h"];
        switch ($this->position) {
            case "tl" : //上左
                $this->x = 0;
                $this->y = 0;
                $this->xx = $this->margin;
                $this->yy = $this->margin;
                break;
            case "tc" : //上中
                $this->x = $w / 2;
                $this->y = 0;
                $this->xx = 0;
                $this->yy = $this->margin;
                break;
            case "tr" : //上右
                $this->x = $w;
                $this->y = 0;
                $this->xx = - $this->margin;
                $this->yy = $this->margin;
                break;
            case "cl" : //中左
                $this->x = 0;
                $this->y = $h / 2;
                $this->xx = $this->margin;
                $this->yy = 0;
                break;
            case "cc" : //中中
                $this->x = $w / 2;
                $this->y = $h / 2;
                $this->xx = 0;
                $this->yy = 0;
                break;
            case "cr" : //中右
                $this->x = $w;
                $this->y = $h / 2;
                $this->xx = - $this->margin;
                $this->yy = 0;
                break;
            case "bl" : //下左
                $this->x = 0;
                $this->y = $h;
                $this->xx = $this->margin;
                $this->yy = - $this->margin;
                break;
            case "bc" : //下中
                $this->x = $w / 2;
                $this->y = $h;
                $this->xx = 0;
                $this->yy = - $this->margin;
                break;
            case "br" : //下右
                $this->x = $w;
                $this->y = $h;
                $this->xx = - $this->margin;
                $this->yy = - $this->margin;
                break;
            default : //随机
                $this->xx = 0;
                $this->yy = 0;
                $this->x = rand(0, $w);
                $this->y = rand(0, $h);
        }
    }
}
/*
$pic = new setPic("test.jpg");//新建一个实例，源图片为test.jpg
$pic->size(500,400);//控制源图片尺寸为500x400，超过此尺寸会自动等比缩放，小于此尺寸不作处理
$pic->small(200,100,"s1.jpg");//生成一个200x100的缩略图s1.jpg
$pic->small(80,50,"s2.jpg");//再生成一个80x50的缩略图s2.jpg

$pic->position = "br";	//水印位置，先是上t-top、中c-center、下b-bottom，再是左l-left、中c-center、右r-right；br表示下右，即右下角；设置为空即随机出现
$pic->margin = 10;		//水印边距。
$pic->fontsize = 24;	//水印字体大小
$pic->fontcolor = "#cccccc";//水印字体色

$pic->water("华股财经");//打文字水印
$pic->water(PIC_WATER_LOGO);//若logo.gif存在的话，则打成图片水印，否则打成文字文印“logo.gif”
$pic->ok();//执行操作
*/
?>