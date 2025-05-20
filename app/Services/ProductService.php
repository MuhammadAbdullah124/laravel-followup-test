<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ProductService
{
    protected $jsonPath = 'products.json';
    protected $xmlPath = 'products.xml';

    public function loadData(): array
    {
        if (!Storage::exists($this->jsonPath)) return [];
        return json_decode(Storage::get($this->jsonPath), true) ?: [];
    }


    protected function saveData(array $data): void
    {
        Storage::put($this->jsonPath, json_encode($data, JSON_PRETTY_PRINT));
        $this->saveXml($data);
    }

    protected function saveXml(array $data): void
    {
        $xml = new \SimpleXMLElement('<products/>');
        foreach ($data as $p) {
            $item = $xml->addChild('product');
            $item->addChild('id', $p['id']);
            $item->addChild('name', htmlspecialchars($p['name']));
            $item->addChild('quantity', $p['quantity']);
            $item->addChild('price', $p['price']);
            $item->addChild('submitted_at', $p['submitted_at']);
        }
        Storage::put($this->xmlPath, $xml->asXML());
    }

    public function saveOrUpdate(array $input): array
    {
        $products = $this->loadData();

        if (!empty($input['id'])) {
            foreach ($products as &$p) {
                if ($p['id'] === $input['id']) {
                    $p['name'] = $input['name'];
                    $p['quantity'] = (int)$input['quantity'];
                    $p['price'] = (float)$input['price'];
                    break;
                }
            }
            unset($p);
        } else {
            $products[] = [
                'id' => uniqid(),
                'name' => $input['name'],
                'quantity' => (int)$input['quantity'],
                'price' => (float)$input['price'],
                'submitted_at' => date('Y-m-d H:i:s'),
            ];
        }

        usort($products, fn($a, $b) => strtotime($a['submitted_at']) <=> strtotime($b['submitted_at']));

        $this->saveData($products);

        return $products;
    }
}
