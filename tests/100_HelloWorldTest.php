<?php
/* FIXME: sort out a proper bootstrap so not requireing once like a chump */
require_once( 'ServiceTestCase.php' );

Class HelloWorldTest extends ServiceTestCase
{
    public function testHelloWorld() 
    {
    
        $rsp_headers = array();
        $url = 'http://localhost:8000/hello';

        $content = $this->HTTPRequest( $url, $rsp_headers );

        $this->assertHTTPStatus( $rsp_headers[0], 200 );
        $this->assertEquals( $content, '{"hello":"world"}' );
    
    }

    public function testBadEntrypoint()  
    {
    
        $rsp_headers = array();
        $url = 'http://localhost:8000/olleh';

        $content = $this->HTTPRequest( $url, $rsp_headers );

        $this->assertHTTPStatus( $rsp_headers[0], 404 );
    }
}
