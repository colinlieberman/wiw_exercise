<?php

namespace Equip\Project\DAL;

class Shift extends DAL
{
    public $object_dir = 'shifts';
    
    public $id;
    public $manager_id;
    public $employee_id;
    public $break;
    public $start_time;
    public $end_time;
    public $created_at;
    public $updated_at;

    public function __construct( $id )
    {
        $data_obj = $this->fetchObject( $this->object_dir, $id );
    
        /* TODO: type checking values */

        $this->id         = $data_obj->id;
        $this->manager_id = $data_obj->manager_id;
        $this->break      = $data_obj->break;
        $this->start_time = new \DateTime( $data_obj->start_time );
        $this->end_time   = new \DateTime( $data_obj->end_time );
        $this->created_at = new \DateTime( $data_obj->created_at );
        $this->updated_at = new \DateTime( $data_obj->updated_at );
    }

}
