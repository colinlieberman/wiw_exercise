<?php

namespace Equip\Project\DAL;

class User extends DAL
{
    public $object_dir = 'users';
    
    public $id;
    public $name;
    public $role;
    public $email;
    public $phone;
    public $created_at;
    public $updated_at;

    public function __construct( $id )
    {
        $data_obj = $this->fetchObject( $this->object_dir, $id );
        
        /* TODO: type checking values */
    	
        $this->id         = $data_obj->id;
        $this->name       = $data_obj->name;
        $this->role       = $data_obj->role;
        $this->email      = $data_obj->email;
        $this->phone      = $data_obj->phone;
        $this->created_at = new \DateTime( $data_obj->created_at );
        $this->updated_at = new \DateTime( $data_obj->updated_at );
    }

    public function hoursWorked( DateTime $date=null )
    {
        /* TODO: if date is set, get hours for date + 7 days;
         * otherwise get hours for current week
         *
         * I'm not going to build a system to read all those
         * mock objects to query for user id; in the real 
         * world that would almost certainly be a database query
         */

        return array(
            [ 
                'date'   => '2016-01-04'
                ,'hours' => 38
            ]
           ,[ 
                'date'   => '2016-01-01'
                ,'hours' => 42
            ]
           ,[ 
                'date'   => '2016-01-18'
                ,'hours' => 12
            ]
        );
    }



}
