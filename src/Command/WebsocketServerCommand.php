<?php

// src/Command/WebsocketServerCommand.php
namespace App\Command;

use App\Websocket\SiteRealTimeHandler;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WebsocketServerCommand extends Command
{
    protected static $defaultName = 'app:websocket-server';

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(new SiteRealTimeHandler())
            ),
            8090 // Port number
        );

        $server->run();
    }
}
