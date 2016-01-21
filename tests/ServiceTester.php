<?php

Class ServiceTester extends PHPUnit_Framework_TestCase
{
    const APP_PATH = '/Users/clieberman/dev/wiw_exercise';

    protected $_ch = null;

    public function __construct() {
        parent::__construct();

        /* hackety hack: check to see if there's a server running, and if not, start it */
        if( ! $this->_getServerPid() ) {
            $app_path = self::APP_PATH; 
            $sys_cmd  = "${app_path}/bin/start-server > ${app_path}/logs/access.log &> ${app_path}/logs/error.log &";
            exec( $sys_cmd, $output );
            
            /* sleep .05 seconds to give server time to start */
            usleep( 50000 );
       } 

    }

    protected function _getServerPid() {
        return exec( 'lsof -n -i:8000 | awk \'/php/{print $2}\'' );
    }

    protected function _killServer() {
        exec( 'kill ' . $this->_getServerPid() );
    }

    public function __destruct() {
        /* hackety hack kill the webserver; this is only tested on osx; lsof syntax
         * likely not the same on linux
         */
        $this->_killServer();
    }

    protected function _setMethod( $request_method ) {
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

    public function HTTPRequest( $url, &$response_headers=array(), $method='GET', $curl_opts=array() ) {
        $this->_ch = curl_init( $url );

        $this->_setMethod( $method );

        foreach( $curl_opts as $opt => $val ) {
            curl_setopt( $this->_ch, constant( $opt ), $val );
        }
   
        /* all that jazz for making sure we get all the response headers 
         * http://stackoverflow.com/questions/9183178/php-curl-retrieving-response-headers-and-body-in-a-single-request
         */
        curl_setopt( $this->_ch, CURLOPT_RETURNTRANSFER, 1 );
        //curl_setopt( $this->_ch, CURLOPT_VERBOSE, 1 );
        curl_setopt( $this->_ch, CURLOPT_HEADER, 1 );

        $response = curl_exec( $this->_ch );
        $header_size = curl_getinfo( $this->_ch, CURLINFO_HEADER_SIZE );
        curl_close( $this->_ch );
        
        $header = substr( $response, 0, $header_size );
        $body = substr( $response, $header_size );

        $response_headers = $this->http_parse_headers( $header );

        return $body;
    }
}
