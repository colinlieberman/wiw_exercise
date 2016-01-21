<?php

namespace Equip\Project\Domain;

use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;

class Shift extends AuthenticatedDomain
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
     * @inheritDoc
     */
    public function __invoke(array $input)
    {

        /* TODO: this */

        return $this->payload
            ->withStatus( PayloadInterface::OK )
            ->withOutput( array('todo'); );
    }
}
