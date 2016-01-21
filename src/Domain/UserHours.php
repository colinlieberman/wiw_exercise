<?php

namespace Equip\Project\Domain;

use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;

class UserHours extends User
{
    /**
     * @param PayloadInterface $payload
     */
    public function __construct( PayloadInterface $payload )
    {
        /* fixme: this really annoys me: that payload is private rather than
         * protected, it's kind of a chore to sublcass Domain objects
         */
        parent::__construct( $payload );
        $this->payload = $payload;
    }
    
    public function __invoke(array $input)
    {
        $return_obj = $this->_validateUserRequest( $input );

        if( $return_obj ) 
        {
            return $return_obj;
        }
        
        $id = $input[ 'id' ];

        $user = new \Equip\Project\DAL\User( $id );
       
        $date = null;
        /* TODO: get (validated) option query param 
         * for date argument
         */

        return $this->payload
            ->withStatus( PayloadInterface::OK )
            ->withOutput( $user->hoursWorked( $date ) );
    }
}
