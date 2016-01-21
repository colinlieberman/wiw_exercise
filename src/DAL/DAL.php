<?php

namespace Equip\Project\DAL;

abstract Class DAL 
{
    protected $_obj_json;
    /* things like database connection management go here */
    
    /**
     * @param string type
     * @param string id
     *
     * this kind of function is pretty much only useful 
     * with these file-system mock objects, since a 
     * proper db connection would almost always
     * want to do something a little more clever
     * than this
     */
    protected function fetchObject( $type, $id )
    {
        /* assuming case is correct */
        $base_dir    = dirname( __FILE__ ) . '/mock_objects';
        $object_dir  = "${base_dir}/${type}";
        $object_path = "${object_dir}/${id}.json";

        if( !is_dir( $object_dir ) ) 
        {
            throw new \Exception( "no directory at ${object_dir}" );
        }

        if( !is_file( $object_path ) ) 
        {
            throw new \Exception( "no object file at ${object_path}" );
        }

        $this->_obj_json = file_get_contents( $object_path );
        $object = json_decode( $this->_obj_json );

        if( !$object )
        {
            throw new \Exception( "Error parsing object at ${object_path}" );
        }
        
        return $object;
    }

    public function toArray() 
    {
        return json_decode( $this->_obj_json, true );
    }
}
