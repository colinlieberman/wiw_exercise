<?php

namespace Equip\Project\Domain;

use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;

abstract class AuthenticatedDomain implements DomainInterface
{
    protected $_rx_headers;
    protected $_auth_status;

    protected $_user_id;
    protected $_user_role;

    /* FIXME: probably a much better place for these constants */
    const ROLE_USER    = 1;
    const ROLE_MANAGER = 2;

    protected function _validateAuth( $payload ) 
    {
       /* TODO: actually validate token, etc */
       return true;
    }

    /**
     * @param PayloadInterface $payload
     */
    public function __construct( $payload ) 
    {
        $this->_rx_headers = getallheaders();
        $this->_auth_status = PayloadInterface::OK;
       
        /* validate authentication for this resource */
        if(    !isset( $this->_rx_headers[ 'X-UserID' ] ) 
            || !isset( $this->_rx_headers[ 'X-AuthToken' ] )
            || !isset( $this->_rx_headers[ 'X-UserRole' ] )
            || !$this->_validateAuth( $payload ) )
        {
           $this->_auth_status = PayloadInterface::AUTHERR;
        }
        else
        {
           $this->_user_id   = $this->_rx_headers[ 'X-UserID' ];
           $this->_user_role = $this->_rx_headers[ 'X-UserRole' ];
       }
    }
 
    protected function _returnAuthError( $payload )
    {
        /* FIXME: correctly handle 401 in the framework */
        return $payload
            ->withStatus( $this->_auth_status )
            ->withoutput([ 'err' => 'auth' ]);
    }
 
    protected function _returnBadRequest( $payload )
    {
        /* FIXME: figure out how this stuff is really supposed to work */
        return $payload
            ->withStatus( PayloadInterface::ERROR )
            ->withoutput([ 'err' => 'bad request']);
    }
}
