<?php
return array(
    "content"     =>array("url"=> "http://x.x.com/v1/helper/content/content.php?id="),
    "image"       =>array("url"=> "http://x.x.com/"),
    "alioss_bucket" => 'qiwo-dev',

    'category' => array(
        'daily' => 96,
        'topic' => 97,
        'special' => 98,
        'supply' => 99,
        'vaccine' =>18,     //疫苗
        "check" =>19,        //检疫
        "intl_ad"=>242,   //助手内部广告位
        "outer_ad"=>243  //助手外部广告位
    ),

    /**
     * api
     */
    'api'   => array(
        'api_key'       => 'x',
        'api_secret'    => 'x'
    ), 

     /**
     * 客户
     */
    'user'    => array(
        'get_all'          =>  'https://x.x1.com/v1/user/list',
        'get_one_by_uids'          =>  'https://x.x1.com/v1/user/get_by_uids',
        'get_one_by_name'         =>  'https://x.x1.com/v1/user/get_by_name',
    ),
    /**
     * 宝宝
     */
    'baby'    => array(
        'get_all'         =>  'https://x.x1.com/entity/v1/intl/Get_entity_info',
        'get_one'         =>  'https://x.x1.com/entity/v1/intl/Get_entity_info',
        'get_device'      =>  'https://x.x1.com/device/v1/intl/device/get_device_by_entitys',
        'get_devices'     =>  'https://x.x1.com/device/v1/intl/device/entity_device_list',
        'get_friends'     =>  'https://x.x1.com/entity/v1/intl/get_entity_share_list',
    ),
    /**
     * 日记
     */
    'diary'     =>  array(
        'get_all'   => 'http://x.x.com/v1/api/diary/admindiary/get_all',
        'get_one'   => 'http://x.x.com/v1/api/diary/admindiary/get_one',
        'set_one'   => 'http://x.x.com/v1/api/diary/admindiary/set_one',
        'del_one'   => 'http://x.x.com/v1/api/diary/admindiary/del_one',
        'set_hide'  => 'http://x.x.com/v1/api/diary/admindiary/set_hide',
        'set_show'  => 'http://x.x.com/v1/api/diary/admindiary/set_show',
        'get_user_by_uids'          => 'https://x.x1.com/v1/user/get_by_uids',
        'get_user_by_name'          => 'https://x.x1.com/v1/user/get_by_name',
        'get_entity_info'           => 'https://x.x1.com/entity/v1/intl/get_entity_info',
        'get_relation_by_baby_id'   => 'https://x.x1.com/entity/v1/intl/get_relation',
    ),

    /**
     * 看看
     */
    'blog'=>array(),

    /**
     * 看看标签
     */
    'blog_tags' => array(
        'get_all' => 'http://x.x.com/v1/api/diary/tag_system/get_all',
        'get_one' => 'http://x.x.com/v1/api/diary/tag_system/get_one',
        'set_one' => 'http://x.x.com/v1/api/diary/tag_system/set_one',
        'del_any' => 'http://x.x.com/v1/api/diary/tag_system/del_any',
        'set_any' => 'http://x.x.com/v1/api/diary/tag_system/set_any',
        'get_diary_by_tags' => 'http://x.x.com/v1/api/diary/tag/get_diary_by_tags',
	'get_all_by_diary' => 'http://bbc.qiwocloud1.com/v1/api/diary/tag/get_all_by_diary',
        'get_tags_by_diary' => 'http://bbc.qiwocloud1.com/v1/api/diary/tag/get_tags_by_diary',
    ),

    /**
     * 家庭
     */
    'family'        => array(
        ''          =>  '',
    ),
    /**
     *设备内容
     */
    'device_info'   => array(
        'get_all'   =>  'https://x.x1.com/device/v1/intl/device/device_list',
        'get_one'   =>  '',
//        'get_device_status'=>'https://x.x1.com/device/v1/intl/gateway/device_get_status',
        'get_device_binding'=>'https://x.x1.com/device/v1/intl/device/device_by_uid'
    ),
    /**
     * 设备配置
     */
    'device_rule'   => array(
        'get_sensors_by_device_type'=> 'https://x.x1.com/config/v1/device/intl/get_sensors_by_device_type',
        'get_all'   =>  'https://x.x1.com/config/v1/device/intl/get_all',
        'set_one'   =>  'https://x.x1.com/config/v1/device/intl/set_one',
        'get_one'   =>  'https://x.x1.com/config/v1/device/intl/get_one'
    ),
    /**
     * 分析
     */
    'analysis'    => array(
        'get_all'   =>  'https://x.x1.com/config/v1/analysis/intl/get_all',
        'add_one'   =>  'https://x.x1.com/config/v1/analysis/intl/add_one',
        'set_one'   =>  'https://x.x1.com/config/v1/analysis/intl/set_one',
        'get_one'   =>  'https://x.x1.com/config/v1/analysis/intl/get_one',
        'del_one'   =>  'https://x.x1.com/config/v1/analysis/intl/del_one',
    ),
    
    /**
     * 警报内容
     */
    'alarm_info'    => array(
        'get_all'          =>  'https://x.x1.com/config/v1/alarm/intl/get_all',
        'get_one'          =>  'https://x.x1.com/config/v1/alarm/intl/get_one',
        'set_one'          =>  'https://x.x1.com/config/v1/alarm/intl/set_one',
        'add_one'          =>  'https://x.x1.com/config/v1/alarm/intl/add_one',
        'set_any'          =>  'https://x.x1.com/config/v1/alarm/intl/set_any',
        'del_any'          =>  'https://x.x1.com/config/v1/alarm/intl/del_any',
    ),
    /**
     * 消息分类
     */
    'alarm_type'   => array(
        'get_all'          =>  'https://x.x1.com/config/v1/alarmtype/intl/get_all',
        'get_one'          =>  'https://x.x1.com/config/v1/alarmtype/intl/get_one',
        'set_one'          =>  'https://x.x1.com/config/v1/alarmtype/intl/set_one',
        'set_any'          =>  'https://x.x1.com/config/v1/alarmtype/intl/set_any',
        'del_any'          =>  'https://x.x1.com/config/v1/alarmtype/intl/del_any',
    ),
    /**
     *警报策略
     */
    'alarm_rule'    => array(
        'get_all'   => 'https://x.x1.com/config/v1/alarm/intl/get_config',
        'get_one'   => 'https://x.x1.com/config/v1/alarm/intl/get_config_by_id',
        'set_one'   => 'https://x.x1.com/config/v1/alarm/intl/update_config',
        'add_one'   => '',
        'del_one'   => '',
    ),
    /**
     * 反馈管理
     */
    'feedback'  =>array(
        'get_all'   =>  'https://x.x1.com/v1/feedbackapi/get_feedback_list',
        'get_one'   =>  'https://x.x1.com/v1/feedbackapi/get_feedback',
        'del_one'   =>  'https://x.x1.com/v1/feedbackapi/delete_feedback'
    ),

    /**
     * 版本管理
     */
    'version' => array(
        'get_all'   =>  'https://x.x1.com/v1/versionapi/get_version_list',
        'get_one'   =>  'https://x.x1.com/v1/versionapi/get_version',
        'set_one'   =>  'https://x.x1.com/v1/versionapi/set_version',
        'add_one'   =>  'https://x.x1.com/v1/versionapi/add_version',
        'del_one'   =>  'https://x.x1.com/v1/versionapi/delete_version'
    ),


    /**
     * 手机绑定
     */
    'phone_bind'=>array(
        'get_all'=>'https://x.x1.com/config/v1/device/intl/get_mobile_binding_list',
        'get_one'=>'https://x.x1.com/config/v1/device/intl/get_mobile_binding_by_id',
        'set_one'=>'https://x.x1.com/config/v1/device/intl/update_mobile_binding',
        'add_one'=>'https://x.x1.com/config/v1/device/intl/add_mobile_binding',
        'del_one'=>'https://x.x1.com/config/v1/device/intl/delete_mobile_binding',
        'add_batch'=>'https://x.x1.com/config/v1/device/intl/batch_add_mobile_binding',
    ),

    /**
     * 试用接口
     */
    'tryout'=>'https://x.x1.com/bbc_tryout/v1/index/index',

    /**
     * app首页
     */
    'app_home'=>array(
        'get_entity'     => 'https://x.x1.com/entity/v1/entity/get_profile',
        'get_two_event'   => 'https://x.x1.com/entity/v1/event/getTwoEvent',
        'home_carousel'=>'http://x.x.com/v1/helper/api.php?c=helper&a=getlist&catid=87&pic=true',//有设备
        'home_advert'=>'http://x.x.com/v1/helper/api.php?c=helper&a=getlist&catid=98&pic=true',//无设备
        'get_all_real_time_data' => 'https://x.x1.com/storage/v1/device/get_all_real_time_data',
        'get_device_list_by_entity' => 'https://x.x1.com/device/v1/intl/device/entity_device_list',
        'get_hot_of_last_week'     => 'http://x.x.com/v1/api/diary/get_hot_of_last_week',
        'url_bbc' => 'https://bbc.qiwocloud1.com',
    ),
    /**
     * 数据页
     */
    'baby_data' => array(
        'single_data' => 'https://x.x1.com/storage/v1/app/get_baby_index',
        'chat_data' => 'https://x.x1.com/storage/v1/app/get_baby_chart',
        'upload_data' => 'https://x.x1.com/storage/v1/device/app_upload_data',
        'set_is_upload_height' => 'https://x.x1.com/storage/v1/device/is_receive_data/set',
        'get_is_upload_height' => 'https://x.x1.com/storage/v1/device/is_receive_data/get',
        'set_underarm' => 'https://x.x1.com/storage/v1/app/underarm/set',
        'get_underarm' => 'https://x.x1.com/storage/v1/app/underarm/get',
        'get_recommend' => 'http://x.x.com/v1/helper/?s=api&c=tags&a=getcontentbytags',
        'get_entity' => 'https://x.x1.com/entity/v1/get_profile',
        'get_entity_share' => 'https://x.x1.com/entity/v1/entity_share',
        'get_suggest' => 'http://x.x.com/v1/helper/api.php',
        'get_all_data' =>'https://x.x1.com/storage/v1/device/get_all_real_time_data',
        'notify_get_single_data' =>'https://x.x1.com/storage/v1/device/notify_get_single'
    ),
    'score' => array(
        'add' => 'https://x.x1.com/v1/user/point/add',
        'rule_add' => 'https://x.x1.com/v1/user/point/rule/set',
        'rule_get' => 'https://x.x1.com/v1/user/point/rule/get',
        'remove'=>'https://x.x1.com/v1/user/point/remove',
        'update'=>'https://x.x1.com/user/point/update',
        'get'=>'https://x.x1.com/v1/user/point/get',
        'list'=>'https://x.x1.com/v1/user/list',
        'get_history'=>'https://x.x1.com/v1/user/point/get_history'
    )

);