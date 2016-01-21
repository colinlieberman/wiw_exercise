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

}
