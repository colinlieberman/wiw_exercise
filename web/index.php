<?php

// Include Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

use Equip\Project\Domain;

function debug_out( $var ) {
    print "\n" . print_r( $var, 1 ) . "\n";
}

Equip\Application::build()
->setConfiguration([
    Equip\Configuration\AurynConfiguration::class,
    Equip\Configuration\DiactorosConfiguration::class,
    Equip\Configuration\PayloadConfiguration::class,
    Equip\Configuration\RelayConfiguration::class,
    Equip\Configuration\WhoopsConfiguration::class,
])
->setMiddleware([
    Relay\Middleware\ResponseSender::class,
    Equip\Handler\ExceptionHandler::class,
    Equip\Handler\DispatchHandler::class,
    Equip\Handler\JsonContentHandler::class,
    Equip\Handler\FormContentHandler::class,
    Equip\Handler\ActionHandler::class,
])
->setRouting(function (Equip\Directory $directory) {
    return $directory
    ->get('/hello[/{name}]', Domain\Hello::class)
    ->post('/hello[/{name}]', Domain\Hello::class)
    
    ->get('/user/{id}', Domain\User::class)
    ->get('/user/{id}/hours', Domain\UserHours::class)
    ->get('/user/{id}/shifts', Domain\UserShifts::class)
  
    /* there's not a specific use case for this (yet) but one
     * could well imagine it
     */
    ->get('/shift/{id}', Domain\Shift::class)
    ->put('/shift/{id}', Domain\Shift::class)
    ->post('/shift', Domain\Shift::class)
    
    ->get('/shifts', Domain\ShiftShedule::class)
    ; // End of routing
})
->run();
