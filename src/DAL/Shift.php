<?php

namespace Equip\Project\DAL;

class Shift extends DAL
{
    protected $_object_dir = 'shifts';
    
    public $id;
    public $manager_id;
    public $employee_id;
    public $break;
    public $start_time;
    public $end_time;
    public $created_at;
    public $updated_at;

    public $workers = array();
    public $manager = array();

    public function __construct( $id, $include_workers = false )
    {
        $data_obj = $this->fetchObject( $this->_object_dir, $id );
    
        /* TODO: type checking values */

        $this->id         = $data_obj->id;
        $this->manager_id = $data_obj->manager_id;
        $this->break      = $data_obj->break;
        $this->start_time = new \DateTime( $data_obj->start_time );
        $this->end_time   = new \DateTime( $data_obj->end_time );
        $this->created_at = new \DateTime( $data_obj->created_at );
        $this->updated_at = new \DateTime( $data_obj->updated_at );
   
        $this->manager = new User( $this->manager_id );

        if( $include_workers ) 
        {
            /* in real life this would be another db query for the users
             * in the shift; instead, just going to hack something up
             */
            $uids = array( 123, 234, 345 );
            foreach( $uids as $uid )
            {
                $this->workers[ $uid ] = new User( $uid );
            }
        }
    }

}
