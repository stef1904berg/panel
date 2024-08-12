<?php

namespace App\Services\Network;

use App\Exceptions\Http\Connection\DaemonConnectionException;
use App\Exceptions\Model\DataValidationException;
use App\Models\Network;
use App\Models\Node;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Support\Facades\Http;

class NetworkCreationService
{
    /**
     * Create a new network on the selected node.
     *
     * @throws DataValidationException
     */
    public function handle(array $data): Network
    {
        $node = Node::query()->find($data['node_id']);

        try {
            $response = Http::daemon($node)
                ->connectTimeout(5)
                ->timeout(30)
                ->post('/api/networks',
                    [
                        'name' => $data['name'],
                        'driver' => $data['driver'],
                    ]);

        } catch (TransferException $exception) {
            throw new DaemonConnectionException($exception);
        }

        $data['network_id'] = $response->json('network_id');

        return Network::create($data);
    }
}
