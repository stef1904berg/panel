<?php

namespace App\Services\Network;

use App\Exceptions\Model\DataValidationException;
use App\Models\Network;
use App\Models\Node;
use Illuminate\Support\Facades\Http;

class NetworkDeletionService
{
    /**
     * Create a new network on the selected node.
     *
     * @throws DataValidationException
     */
    public function handle(int|Network $network): void
    {
        $node = Node::findOrFail($network->node_id);

        if (is_int($network)) {
            $network = Network::findOrFail($network);
        }

        Http::daemon($node)
            ->connectTimeout(5)
            ->timeout(30)
            ->delete('/api/networks', [
                'network_id' => $network->network_id,
            ]);
    }
}
