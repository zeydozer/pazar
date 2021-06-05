<?php

namespace App\Http\Parasut;

class Account extends Base
{
    public function create($data)
    {
        return $this->client->request(
            'contacts',
            $data,
            'POST'
        );
    }

    public function show($id , $data = [])
    {
        return $this->client->request(
            'contacts/' . $id,
            $data,
            'GET'
        );
    }

    public function update($id , $data = [])
    {
        return $this->client->request(
            'contacts/' . $id,
            $data,
            'PUT'
        );
    }

    public function delete($id , $data = [])
    {
        return $this->client->request(
            'contacts/' . $id,
            $data,
            'DELETE'
        );
    }
}