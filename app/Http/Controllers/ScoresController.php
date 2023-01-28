<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;

class ScoresController extends Controller
{
    public function store(Request $request)
    {
        // $request = '{
        //     "id": "12345",
        //     "client_id": "54321",
        //     "items": [
        //       {
        //         "article":"3005-12",
        //             "name":"Сосиска в тесте",
        //             "price":100,
        //             "quantity":12
        //       },
        //       {
        //         "article":"3005-13",
        //             "name":"Дырка от бублика",
        //             "price":340,
        //             "quantity": 3
        //       },
        //       {
        //         "article":"3005-14",
        //             "name":"Усы Фредди Меркьюри",
        //             "price":900,
        //             "quantity":90
        //       }
        //     ],
        //     "status": "new"
        //   }';

        $data = json_decode($request, true);

        $discountArticle = '3005-13';

        $additionalScore = 0;
        $items = [];

        foreach ($data['items'] as $item) {
            $items[] = [
                'product_id' => $item['article'],
                'quantity' => $item['quantity'],
                'order_id' => $data['id']
            ];
            if ($item['article'] === $discountArticle) {
                $additionalScore += 3 * $item['quantity'];
            }
        }

        //Fill Clients table
        $client = Client::where('id', $data['client_id']);
        if ($client->exists()) {
            if ($additionalScore != 0) {
                $client->increment('scores', $additionalScore);
            }
        } else {
            Client::create(['id' => $data['client_id'], 'scores' => $additionalScore]);
        }

        //Fill Items
        Item::insert($items);

        //Fill Order
        Order::create([
            'id' => $data['id'],
            'client_id' => $data['client_id'],
            'status' => $data['status']
        ]);

        if ($additionalScore != 0) {
            return response()->json([
                'client_id' => $data['client_id'],
                'scores' => $additionalScore,
                'order_id' => $data['id'],
            ]);
        } else {
            return response()->json([
                'client_id' => $data['client_id'],
                'scores' => 0,
                'order_id' => $data['id'],
            ]);
        }
    }
}
