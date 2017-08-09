<?php
return array(
    'mysql_master' => array(
    	array(
			'host'     => '10.168.53.187',
            'port'     => '50161',
			'username' => '50161-all-rw', 
			'password' => 'M#@%$#&^#@', 
			'dbname'   => 'helper', 
			'prefix'   => 'fn_', 
			'charset'  => 'utf8',
		),
    ),
    'mysql_slave' => array(
      	array(
			'host'     => '10.171.239.143',
            'port'     => '50161',
			'username' => '50161-all-r',
			'password' => 'S##@!$%&^$#@',
			'dbname'   => 'helper',
			'prefix'   => 'fn_',
			'charset'  => 'utf8',
		),
    ),
    'session'   => array(
        'table' => 'sessions',
        'expire' => 3600,
        'prefix' => 'fn_',
    ),
);