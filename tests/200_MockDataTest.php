<?php
/* FIXME: sort out a proper bootstrap so not requireing once like a chump */
require_once( 'ServiceTestCase.php' );
require( dirname( __FILE__ ) . '/../src/DAL/DAL.php' );
require( dirname( __FILE__ ) . '/../src/DAL/User.php' );
require( dirname( __FILE__ ) . '/../src/DAL/Shift.php' );

Class MockDataTest extends PHPUnit_Framework_TestCase
{
    public function testUserParsing()
    {
        $user = new Equip\Project\DAL\User( 123 );
        $expected_name = 'Anony Mouse #123';
        $this->assertEquals( $expected_name, $user->name );
    }
    
    public function testShiftParsing()
    {
        $shift = new Equip\Project\DAL\Shift( 222 );
//        print "\n"; print_r($shift);
    }
}
