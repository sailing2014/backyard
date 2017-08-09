<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/16
 * Time: 13:37
 */
include_once MOB_PATH . '/MobiController.php';

class BabydataController extends MobiController
{
    private $url_entity;
    private $url_baby_data;
    private $url_chat_data;
    private $url_suggest_data;
    private $url_recommend_data;
    private $url_upload_data;
    private $url_set_is_upload_height;
    private $url_get_is_upload_height;
    private $url_notify_get_single_data;
    private $url_set_underarm;
    private $url_get_underarm;
    private $url_all_data;
    private $uid;
    private $token;
    private $entity_id;
    private $data_type;
    private $send_command;
    private $common_param;

    public function __construct()
    {
        parent::__construct(false);
        $this->url_entity = $this->item('baby_data', 'get_entity');
        $this->url_baby_data = $this->item('baby_data', 'single_data');
        $this->url_all_data = $this->item('baby_data', 'get_all_data');
        $this->url_chat_data = $this->item('baby_data', 'chat_data');
        $this->url_suggest_data = $this->item('baby_data', 'get_suggest');
        $this->url_recommend_data = $this->item('baby_data', 'get_recommend');
        $this->url_upload_data = $this->item('baby_data', 'upload_data');
        $this->url_set_is_upload_height = $this->item('baby_data', 'set_is_upload_height');
        $this->url_get_is_upload_height = $this->item('baby_data', 'get_is_upload_height');
        $this->url_set_underarm = $this->item('baby_data', 'set_underarm');
        $this->url_get_underarm = $this->item('baby_data', 'get_underarm');
        $this->url_notify_get_single_data = $this->item('baby_data', 'notify_get_single_data');

        $this->send_command = 0;
        $this->uid = self::post("uid");
        $this->token = self::post('token');
        $this->entity_id = intval(self::post('entity_id'));
        $this->data_type = intval(self::post("data_type"));
        $this->common_param = array(
            'uid' => isset($this->uid) ? intval($this->uid) : 0,
            'token' => isset($this->token) ? $this->token : "",
        );
        header('Content-type: application/json');
    }

    public function GetEntityAction()
    {
        $param = array(
            'entity_id' => $this->entity_id
        );
        $entity_data = $this->httpclient->post($this->url_entity, $param + $this->common_param, false, array("Content-Type:application/json"));
        if ($entity_data && isset($entity_data["status"]["code"]) && $entity_data["status"]["code"] == 16003) {
            $return_result = json_encode($entity_data["entity"]);
        } else {
            $return_result = "{}";
        }
        echo $return_result;
        exit;
    }

    public function NotifyDataAction()
    {
        $param = array(
            'data_type' => $this->data_type,
            'gid' => intval(self::post('gid')),
            'entity_id' => $this->entity_id
        );
        $notify_result = $this->httpclient->post($this->url_notify_get_single_data, $param + $this->common_param, false, array("Content-Type:application/json"));
        if ($notify_result && isset($notify_result["status"]["code"]) && $notify_result["status"]["code"] == 200) {
            $return_result = 1;
        } else {
            $return_result = 0;
        }
        echo $return_result;
        exit;
    }

    public function GetAllDataAction()
    {
        $param = array(
            'data_type' => $this->data_type,
            'send_command' => $this->send_command,
            'entity_id' => $this->entity_id
        );
        $single_data = $this->httpclient->post($this->url_all_data, $param + $this->common_param, false, array("Content-Type:application/json"));
        if ($single_data && isset($single_data["status"]["code"]) && $single_data["status"]["code"] == 200) {
            $return_result = json_encode($single_data["data"]);
        } else {
            $return_result = "{}";
        }
        echo $return_result;
        exit;
    }

    public function GetSingleAction()
    {
        $param = array(
            'data_type' => $this->data_type,
            'send_command' => $this->send_command,
            'entity_id' => $this->entity_id
        );
        $single_data = $this->httpclient->post($this->url_baby_data, $param + $this->common_param, false, array("Content-Type:application/json"));
        if ($single_data && isset($single_data["status"]["code"]) && $single_data["status"]["code"] == 200) {
            $return_result = json_encode($single_data["data"]);
        } else {
            $return_result = "{}";
        }
        echo $return_result;
        exit;
    }

    public function GetChartAction()
    {
        $param = array(
            'data_type' => $this->data_type,
            'send_command' => $this->send_command,
            'entity_id' => $this->entity_id
        );
        $chart_data = $this->httpclient->post($this->url_chat_data, $param + $this->common_param, false, array("Content-Type:application/json"));
        if ($chart_data && isset($chart_data["status"]["code"]) && $chart_data["status"]["code"] == 200) {
            $chart_temp_data = array();
            if (count($chart_data["data_list"]["rows"]) > 0) {
                foreach ($chart_data["data_list"]["rows"] as $value) {
                    if (isset($value["value"]["min"])) {
                        $chart_temp_data[] = array($value["value"]["time"], array("min" => $value["value"]["min"], "max" => $value["value"]["max"]));
                    } else {
                        $chart_temp_data[] = array($value["value"]["time"], $value["value"]["value"]);
                    }
                }
            }
            $return_result = json_encode($chart_temp_data);
        } else {
            $return_result = "{}";
        }
        echo $return_result;
        exit;
    }

    public function GetSuggestAction()
    {
        $cat_id = intval(self::post('catid'));
        $suggest_data = $this->httpclient->get($this->url_suggest_data, array("c" => "helper", "a" => "getlist", "catid" => $cat_id, "page" => 1, "pagesize" => 4));
        $return_result = $suggest_data && isset($suggest_data["status"]["code"]) && $suggest_data["status"]["code"] == 200 ? json_encode($suggest_data["data"]) : "{}";
        echo $return_result;
        exit;
    }

    /**
     *
     */
    public function GetRecommendAction()
    {
        $tags = self::post('tags');
        $recommend_data = $this->httpclient->post($this->url_recommend_data, array("tags" => $tags), false, array("Content-Type:application/json"));
        $return_result = $recommend_data && isset($recommend_data["status"]["code"]) && $recommend_data["status"]["code"] == 200 ? json_encode($recommend_data["data"]) : "{}";
        echo $return_result;
        exit;
    }

    public function SelectHeightAction()
    {
        // "value":[{"did":0,"entry_id":1,"entity_id":45,"type":5,"value":65}]
        $height_value = intval(self::post('value'));
        $param = array("value" => array(array("did" => 1, "entry_id" => 1, "entity_id" => $this->entity_id, "time" => time(), "type" => 5, "value" => $height_value)));
        $select_height_result = $this->httpclient->post($this->url_upload_data, $param + $this->common_param, false, array("Content-Type:application/json"));
        $return_result = $select_height_result && isset($select_height_result["status"]["code"]) && $select_height_result["status"]["code"] == 200 ? 1 : 0;
        echo $return_result;
        exit;
    }

    public function SetIsManualHeightAction()
    {
        $is_manual = intval(self::post('is_auto'));
        $param = array("entity_id" => $this->entity_id, "data_type" => 5, "is_receive" => $is_manual);
        $set_is_upload_result = $this->httpclient->post($this->url_set_is_upload_height, $param + $this->common_param, false, array("Content-Type:application/json"));
        $return_result = $set_is_upload_result && isset($set_is_upload_result["status"]["code"]) && $set_is_upload_result["status"]["code"] == 200 ? 1 : 0;
        echo $return_result;
        exit;
    }

    public function GetIsManualHeightAction()
    {
        $param = array("entity_id" => $this->entity_id, "data_type" => 5);
        $get_is_upload_result = $this->httpclient->post($this->url_get_is_upload_height, $param + $this->common_param, false, array("Content-Type:application/json"));
        $return_result = $get_is_upload_result && isset($get_is_upload_result["status"]["code"]) && $get_is_upload_result["status"]["code"] == 200 ? json_encode($get_is_upload_result["data"]) : "{}";
        echo $return_result;
        exit;
    }

    public function SetIsUnderArmAction()
    {
        $is_underarm = intval(self::post('is_underarm'));
        $alarm_time = intval(self::post('alarm_time'));
        $param = array("entity_id" => $this->entity_id, "doc_val" => array("is_underarm" => $is_underarm, "alarm_time" => $alarm_time));
        $set_is_underarm_result = $this->httpclient->post($this->url_set_underarm, $param + $this->common_param, false, array("Content-Type:application/json"));
        $return_result = $set_is_underarm_result && isset($set_is_underarm_result["status"]["code"]) && $set_is_underarm_result["status"]["code"] == 200 ? 1 : 0;
        echo $return_result;
        exit;
    }

    public function GetIsUnderArmAction()
    {
        $param = array("entity_id" => $this->entity_id);
        $get_is_underarm_result = $this->httpclient->post($this->url_get_underarm, $param + $this->common_param, false, array("Content-Type:application/json"));
        $return_result = $get_is_underarm_result && isset($get_is_underarm_result["status"]["code"]) && $get_is_underarm_result["status"]["code"] == 200 ? json_encode($get_is_underarm_result["data"]) : "{}";
        echo $return_result;
        exit;
    }
}
