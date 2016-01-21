<?php
/* FIXME: sort out a proper bootstrap so not requireing once like a chump */
require_once( 'ServiceTestCase.php' );

print "\nNB these tests are testing my hack around not getting the app to handle 401 properly\n";

Class UserTest extends ServiceTestCase
{
    protected $base_url         = 'http://localhost:8000/user'; 
    protected $auth_err_content = '{"err":"auth"}';

    public function testNoAuth()
    {
        /* test auth with no auth headers */
    
        $rsp_headers = array();
        $url = $this->base_url . '/123' ; 
            
        $content = $this->HTTPRequest( $url, $rsp_headers );
         
        /* $this->assertHTTPStatus( $rsp_headers[0], 401 ); */
        $this->assertEquals( $this->auth_err_content, $content, "Expected auth error got $content" );
    }

    public function testIncompleteAuth() {
        /* test auth with parts of auth but not complete set */
    
        $rsp_headers = array();
        $url = $this->base_url . '/123'; 

        $opt_configs = array (
            array(
                'X-UserID: 1234'
            )
           ,array(
                'X-UserRole: 2'
            )
           ,array(
                'X-AuthToken: 1a23c3b405e'
            )
           ,array(
                'X-UserID: 1234'
               ,'X-UserRole: 2'
            )
           ,array(
                'X-UserID: 1234'
               ,'X-AuthToken: 1a23c3b405e'
            )
           ,array(
                'X-UserRole: 2'
               ,'X-AuthToken: 1a23c3b405e'
            )
        );

        foreach( $opt_configs as $opt_config )
        {
            $opts = array(
                'CURLOPT_HTTPHEADER' => $opt_config
            );

            $content = $this->HTTPRequest( $url, $rsp_headers, $opts );

            /* $this->assertHTTPStatus( $rsp_headers[0], 401 ); */
            $this->assertEquals( $this->auth_err_content, $content, "Expected auth error got $content" );
        }
    }


    public function testAuthComplete()
    {
        /* test auth with parts of auth but not complete set */
    
        $rsp_headers = array();
        $url = $this->base_url . '/123' ; 
        
        $opts = array(
            'CURLOPT_HTTPHEADER' => array(
                'X-UserID: 1234'
               ,'X-AuthToken: 0a1b2c3d'
               ,'X-UserRole: 2'
            )
        );

        $content = $this->HTTPRequest( $url, $rsp_headers, $opts );
        
        $this->assertHTTPStatus( $rsp_headers[0], 200 );
    }

    public function testUserOnlySeesSelf() 
    {
    
        $rsp_headers = array();
        $url = $this->base_url . '/1234'; 
        
        $opts = array(
            'CURLOPT_HTTPHEADER' => array(
                'X-UserID: 1234'
               ,'X-AuthToken: 0a1b2c3d'
               ,'X-UserRole: 1'
            )
        );

        /* test user can get own info */
        $content = $this->HTTPRequest( $url, $rsp_headers, $opts );
        $this->assertHTTPStatus( $rsp_headers[0], 200 );

        /* test user can't get other's info */
        
        /* this test actually passes, but skip anyway because it passes on 
         * a fallback and not because it's fixed at the user/role authetication
         * level
         */
        $this->markTestSkipped( "TODO: access control not implemented yet" );
        
        
        $url = $this->base_url . '/234324';
        $content = $this->HTTPRequest( $url, $rsp_headers, $opts );
        /* $this->assertHTTPStatus( $rsp_headers[0], 401 ); */
        $this->assertEquals( $this->auth_err_content, $content, "Expected auth error got $content" );
    }

    public function testErrorOnMissingId() 
    {
        /* test auth with parts of auth but not complete set */
    
        $rsp_headers = array();
        $url = $this->base_url; 
        
        $opts = array(
            'CURLOPT_HTTPHEADER' => array(
                'X-UserID: 1234'
               ,'X-AuthToken: 0a1b2c3d'
               ,'X-UserRole: 2'
            )
        );

        $content = $this->HTTPRequest( $url, $rsp_headers, $opts );

        $this->assertHTTPStatus( $rsp_headers[0], 404 );
    }
    
    public function testUserData() 
    {
        print( "\nImplements use case As a manager, I want to contact an employee, by seeing employee details." );
    
        $rsp_headers = array();
        $url = $this->base_url . '/123'; 
        
        $opts = array(
            'CURLOPT_HTTPHEADER' => array(
                'X-UserID: 1234'
               ,'X-AuthToken: 0a1b2c3d'
               ,'X-UserRole: 2'
            )
        );

        $content = $this->HTTPRequest( $url, $rsp_headers, $opts );
        $obj = json_decode( $content );
        $this->assertEquals( 'Anony Mouse #123', $obj->name );
    }

    public function testTryDataForOtherUser()
    {
        $rsp_headers = array();
        $url = $this->base_url . '/234'; 
        
        $opts = array(
            'CURLOPT_HTTPHEADER' => array(
                'X-UserID: 1234'
               ,'X-AuthToken: 0a1b2c3d'
               ,'X-UserRole: 1'
            )
        );

        $content = $this->HTTPRequest( $url, $rsp_headers, $opts );
        /* $this->assertHTTPStatus( $rsp_headers[0], 401 ); */
        $this->assertEquals( $this->auth_err_content, $content, "Expected auth error got $content" );
    }

    public function testUserHours()
    {
        print( "\nImplements use case As an employee, I want to know how much I worked, by being able to get a summary of hours worked for each week." );
    
        $rsp_headers = array();
        $url = $this->base_url . '/123/hours'; 
        
        $opts = array(
            'CURLOPT_HTTPHEADER' => array(
                'X-UserID: 123'
               ,'X-AuthToken: 0a1b2c3d'
               ,'X-UserRole: 1'
            )
        );

        $content = $this->HTTPRequest( $url, $rsp_headers, $opts );
        $obj = json_decode( $content, 1 );
        $this->assertEquals( $obj[0]['date'], '2016-01-04' );
        $this->assertEquals( $obj[0]['hours'], '38' );
    }

}
