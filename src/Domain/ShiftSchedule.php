<?php

namespace Equip\Project\Domain;

use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;

class ShiftSchedule extends Shift;
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

}
