<?php

Class ServiceTester extends PHPUnit_Framework_TestCase
{
    const APP_PATH = '/Users/clieberman/dev/wiw_exercise';

    public function __construct() {
        parent::__construct();

        /* hackety hack start the webserver */
        $app_path = self::APP_PATH; 
        $sys_cmd  = "${app_path}/bin/start-server > ${app_path}/logs/access.log &> ${app_path}/logs/error.log &";
        exec( $sys_cmd, $output );

    }

    public function __destruct() {
        /* hackety hack kill the webserver; this is only tested on osx; lsof syntax
         * likely not the same on linux
         */
        exec( 'kill "$(lsof -n -i:8000 | awk \'/php/{print $2}\')"' );
    }
}
