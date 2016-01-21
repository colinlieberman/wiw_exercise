<?php

namespace Equip\Project\Domain;

use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;

class User extends AuthenticatedDomain
{
    /**
     * @var PayloadInterface
     */
    private $payload;
    
    /**
     * @param PayloadInterface $payload
     */
    public function __construct( PayloadInterface $payload )
    {
        parent::__construct( $payload );
        $this->payload = $payload;
    }

    /**
     * @_validateUserRequest
     *
     * @param array input from __invoke method
     * 
     * @returns payload return object or false if there's nothing to return immediately (request is so far so good)
     */
    protected function _validateUserRequest( array $input )
    {
        /* FIXME: I'm sure there's a better way to drop the request on the floor 
         * in the constructor; need to learn how to do that
         */
        
        if( $this->_auth_status != PayloadInterface::OK ) 
        {
            return $this->_returnAuthError( $this->payload );
        }

        if( !isset( $input[ 'id' ] ) ) 
        {
            /* FIXME: really frustrating I can't easily figure out how to
             * get the framework to return error conditions
             *
             * the docs at http://equipframework.readthedocs.org/
             * turn up 0 results for error, 0 results for 400, 
             * and even 0 results for header
             */
            return $this->_returnBadRequest( $this->payload );
        }

        /* if the request is on a user role, ensure user's only trying
         * for their own data
         */
        if( $this->_rx_headers[ 'X-UserRole' ] == self::ROLE_USER 
                && $input[ 'id' ] != $this->_rx_headers[ 'X-UserID' ] )
        {
            return $this->_returnAuthError( $this->payload );
        }

        return false;
    }
    /**
     * @inheritDoc
     */
    public function __invoke(array $input)
    {
        $return_obj = $this->_validateUserRequest( $input );

        if( $return_obj ) 
        {
            return $return_obj;
        }

        if( $this->_user_role != self::ROLE_MANAGER ) 
        {
            return $this->_returnAuthError( $this->payload );
        }

        $id = $input[ 'id' ];

        $user = new \Equip\Project\DAL\User( $id );

        return $this->payload
            ->withStatus( PayloadInterface::OK )
            ->withOutput( $user->toArray() );
    }
}
