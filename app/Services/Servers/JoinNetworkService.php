<?php

namespace App\Services\Servers;

use App\Enums\NetworkDriver;
use App\Exceptions\Http\Connection\DaemonConnectionException;
use App\Exceptions\Http\DockerNetworkException;
use App\Models\Network;
use App\Models\Server;
use App\Repositories\Daemon\DaemonNetworkRepository;

class JoinNetworkService
{
    public function __construct(
        private DaemonNetworkRepository $daemonNetworkRepository,
    ) {
    }

    /**
     * @throws DockerNetworkException
     * @throws DaemonConnectionException
     */
    public function handle(int|Server $server, int|Network $network): void
    {
        if (is_int($network)) {
            $network = Network::findOrFail($network);
        }

        if (is_int($server)) {
            $server = Server::findOrFail($server);
        }

        // Ensure the network and server are on the same node if the network driver is a bridge.
        // Otherwise, the container could never join the network
        if ($network->driver == NetworkDriver::Bridge && $server->node_id != $network->node_id) {
            logger()->error('Server cannot join network if not on same node when network uses bridge driver');
        }

        $this->daemonNetworkRepository->setServer($server);
        $this->daemonNetworkRepository->joinNetwork($network);
    }
}
