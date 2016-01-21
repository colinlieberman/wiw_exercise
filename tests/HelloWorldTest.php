<?php

require 'ServiceTester.php';

Class HelloWorldTest extends ServiceTester
{
    public function testHelloWorld() {
    
        $rsp_headers = array();
        $url = 'http://localhost:8000/hello';

        $content = $this->HTTPRequest( $url, $rsp_headers );

        print "\n\n$content\n\n";
        print_r( $rsp_headers );

        $this->assertEquals( 0, 0 );
    
    }
}
