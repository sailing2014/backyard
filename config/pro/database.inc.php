<?php
return array(
    'mysql_master' => array(
    	array(
			'host'     => '10.0.0.1',
            'port'     => '50161',
			'username' => '50161-all-rw', 
			'password' => 'GuiS3phoWea3ie@W8', 
			'dbname'   => 'helper', 
			'prefix'   => 'fn_', 
			'charset'  => 'utf8',
		),
    ),
    'mysql_slave' => array(
      	array(
			'host'     => '10.0.0.2',
            'port'     => '50161',
			'username' => '50161-all-r',
			'password' => 'ua4Aitahao3Ooj!ie',
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