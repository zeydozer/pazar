<?php

namespace App\Http\Parasut;

class Category extends Base
{
    public function listCategories($data = [])
    {
        return $this->client->request(
            'item_categories',
            $data,
            'GET'
        );
    }

    public function listTags($data = [])
    {
        return $this->client->request(
            'tags',
            $data,
            'GET'
        );
    }
}