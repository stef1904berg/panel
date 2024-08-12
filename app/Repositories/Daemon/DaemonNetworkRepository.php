<?php

namespace App\Repositories\Daemon;

use App\Enums\ServerState;
use App\Models\Network;
use App\Models\Server;
use GuzzleHttp\Exception\TransferException;
use App\Exceptions\Http\Connection\DaemonConnectionException;
use Illuminate\Http\Client\ConnectionException;
use Webmozart\Assert\Assert;

class DaemonNetworkRepository extends DaemonRepository
{
    /**
     * Gets a list of networks the server has joined
     * @return array
     * @throws DaemonConnectionException
     * @throws ConnectionException
     */
    public function getNetworks(): array
    {
        try {
            $response = $this->getHttpClient()
                ->connectTimeout(5)
                ->get("/api/servers/{$this->server->uuid}/networks");
        } catch (TransferException $exception) {
            throw new DaemonConnectionException($exception);
        }

        return $response->json();
    }

    /**
     * Makes a server join an existing network
     * @param Network $network
     * @param Server $server
     * @return void
     */
    public function joinNetwork(Network $network): void
    {
        Assert::isInstanceOf($this->server, Server::class);

        // A server can only join a network if it is done installing, joining a network before that may de-sync
        // The server_network table with the actual data from the daemon, leading in unwanted behaviour and falsy
        // reporting a server has joined the network
        Assert::true($this->server->status == ServerState::Normal);

        try {
            $this->getHttpClient()
                ->connectTimeout(5)
                ->post("/api/servers/{$this->server->uuid}/networks/join", $network->getNetwork());
        } catch (TransferException $exception) {
            throw new DaemonConnectionException($exception);
        }
    }

    /**
     * Makes a server leave a network
     * @param Network $network
     * @param Server $server
     * @return void
     * @throws DaemonConnectionException
     * @throws ConnectionException
     */
    public function leaveNetwork(Network $network): void
    {
        Assert::isInstanceOf($this->server, Server::class);

        try {
            $this->getHttpClient()
                ->connectTimeout(5)
                ->delete("/api/servers/{$this->server->uuid}/networks/leave", $network->getNetwork());
        } catch (TransferException $exception) {
            throw new DaemonConnectionException($exception);
        }
    }
}
