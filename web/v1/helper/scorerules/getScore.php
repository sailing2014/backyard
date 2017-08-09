<?php

// post 调用接口
function post ($url, $data) {
    $ch = curl_init () ;
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
    curl_setopt($ch, CURLOPT_URL, $url) ;
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data))
    );
    return curl_exec($ch) ;
}

function setForm () {
    $nowtime = time();
    $form['api_key']    = '32C73BA3F00E407A';
    $form['time']	    = $nowtime;
    $form['view_table']	= "point_rule";
    $status['status']   = 1;
    $form['condition']  = $status;
    $form['api_token']  = sha1 ("2683a517847ee76607a10677e092f6be".$nowtime);
    return $form;
}

function getlist(){

    $host = $_SERVER['HTTP_HOST'];
    if (strpos($host, "dev.") !== false) {
        $domain = 'https://dev.sdcp.qiwocloud1.com';
    } elseif (strpos($host, "test.") !== false) {
        $domain = 'https://test.sdcp.qiwocloud1.com';
    } else {
        $domain = 'https://sdcp.qiwocloud1.com';
    }
    
    $domain    = 'https://dev.sdcp.qiwocloud1.com';
    $url       = $domain . "/v1/user/list";

    $form      = setForm(data);
    $post_data = json_encode($form); 

    $rs        = post($url, $post_data);
    $rs        = json_decode($rs, 1);
    return $rs;
}

$rs = getlist();
print_r(json_encode($rs));exit;