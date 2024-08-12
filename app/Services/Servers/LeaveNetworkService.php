<?php

namespace App\Services\Servers;

use App\Enums\NetworkDriver;
use App\Models\Network;
use App\Models\Server;
use App\Repositories\Daemon\DaemonNetworkRepository;

class LeaveNetworkService
{
    public function __construct(
        private DaemonNetworkRepository $daemonNetworkRepository,
    ) {
    }

    public function handle(int|Server $server, int|Network $network)
    {
        if (is_int($network)) {
            $network = Network::findOrFail($network);
        }

        if (is_int($server)) {
            $server = Server::findOrFail($server);
        }

        $this->daemonNetworkRepository->setServer($server);
        $this->daemonNetworkRepository->leaveNetwork($network);
    }
}
