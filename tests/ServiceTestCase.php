<?php

abstract Class ServiceTestCase extends PHPUnit_Framework_TestCase
{
    protected $_ch       = null;
    protected $_app_path = '';

    public function __construct() {
        parent::__construct();
        
        /* assume __FILE__ is app_root/tests/ServiceTestCase.php */
        $this->_app_path = dirname( __FILE__ ) . '/..';

        /* hackety hack: check to see if there's a server running, and if not, start it
         * FIXME: replace with phpunit --bootstrap file and appropriate cleanup/teardown
         * to stop server 
         */
        if( ! $this->_getServerPid() ) 
        {
            $start_bin  = $this->_app_path . '/bin/start-server';
            $log_path   = $this->_app_path . '/logs/access.log';
            $error_path = $this->_app_path . '/logs/error.log';
            
            $sys_cmd  = "${start_bin} >> ${log_path} 2>> ${error_path} &";
            exec( $sys_cmd, $output );

            /* sleep .05 seconds to give server time to start */
            usleep( 50000 );
       }

    }

    protected function _getServerPid() 
    {
        /* FIXME: have server write to pid file; this isn't portable */
        $pid = exec( 'lsof -n -i:8000 | awk \'/php/{print $2}\'' );
        /*print "\npid $pid\n";*/
        return $pid;
    }

    protected function _killServer() 
    {
        $pid = $this->_getServerPid();
        if( $pid ) {
            exec( 'kill ' . $this->_getServerPid() );
        }
    }

    public function __destruct() 
    {
        $this->_killServer();
    }

    protected function _setMethod( $request_method ) 
    {
        $methods = array( 'GET', 'POST', 'PUT', 'DELETE' );

        if( !in_array( $request_method, $methods ) ) {
            throw new Exception( "method $request_method not supported" );
        }

        foreach( $methods as $method ) {
            if( $method == 'DELETE' ) {
                /* delete is handled differently from others */
                if( $method == $request_method ) {
                    curl_setopt( $this->_ch, CURLOPT_CUSTOMREQUEST, $request_method );
               }
            }
            else if( $method == 'GET' ) {
                curl_setopt( $this->_ch, CURLOPT_HTTPGET, $request_method == 'GET' );
            }
            else {
                /* otherwise, set true or false as needed */
                curl_setopt( $this->_ch, constant( 'CURLOPT_' . $method ), $method == $request_method );
            }
        }
    }

    /* so tests can run anywhere without pecl headaches, here's the
     * manual version from http://php.net/http_parse_headers comments
     */
    public function http_parse_headers($raw_headers)
    {
        $headers = array();
        $key = ''; // [+]

        foreach(explode("\n", $raw_headers) as $i => $h)
        {
            $h = explode(':', $h, 2);

            if (isset($h[1]))
            {
                if (!isset($headers[$h[0]]))
                    $headers[$h[0]] = trim($h[1]);
                elseif (is_array($headers[$h[0]]))
                {
                    // $tmp = array_merge($headers[$h[0]], array(trim($h[1]))); // [-]
                    // $headers[$h[0]] = $tmp; // [-]
                    $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1]))); // [+]
                }
                else
                {
                    // $tmp = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [-]
                    // $headers[$h[0]] = $tmp; // [-]
                    $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [+]
                }

                $key = $h[0]; // [+]
            }
            else // [+]
            { // [+]
                if (substr($h[0], 0, 1) == "\t") // [+]
                    $headers[$key] .= "\r\n\t".trim($h[0]); // [+]
                elseif (!$key) // [+]
                    $headers[0] = trim($h[0]);trim($h[0]); // [+]
            } // [+]
        }

        return $headers;
    }

    public function HTTPRequest( $url, &$response_headers=array(), $curl_opts=array(), $method='GET' ) 
    {
        $this->_ch = curl_init( $url );

        $this->_setMethod( $method );

        foreach( $curl_opts as $opt => $val ) 
        {
            curl_setopt( $this->_ch, constant( $opt ), $val );
        }

        /* all that jazz for making sure we get all the response headers
         * http://stackoverflow.com/questions/9183178/php-curl-retrieving-response-headers-and-body-in-a-single-request
         */
        curl_setopt( $this->_ch, CURLOPT_RETURNTRANSFER, 1 );
        /* curl_setopt( $this->_ch, CURLOPT_VERBOSE, 1 ); */
        curl_setopt( $this->_ch, CURLOPT_HEADER, 1 );
        curl_setopt( $this->_ch, CURLINFO_HEADER_OUT, true );

        $response = curl_exec( $this->_ch );
        $header_size = curl_getinfo( $this->_ch, CURLINFO_HEADER_SIZE );
        
        //print "\ncurl info: " . print_r( curl_getinfo( $this->_ch, CURLINFO_HEADER_OUT ), 1 ) . "\n";
        
        curl_close( $this->_ch );

        $header = substr( $response, 0, $header_size );
        $body = substr( $response, $header_size );

        $response_headers = $this->http_parse_headers( $header );

        return $body;
    }

    public function assertHTTPStatus( $http_header, $status, $msg='')  
    {
        $msg = $msg ? $msg : "Got status ${http_header}, expected ${status}";
        $this->assertTrue( strpos( $http_header, "HTTP/1.1 ${status} " ) === 0, $msg );
    }

}
