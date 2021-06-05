<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class usertoaddress extends Model
{
    protected $fillable = [
        'postcode2','postcode','userid','nonmemberid','name','surname','email','address', 'taxno','taxofficial','city','district','phone','nationality','tckn','foreignidentity','passportnumber','type','name2','surname2','city2','dist2','address2','phone2','conname','congsm','conemail','addressname','district2'
    ];

    public function addresstocity()
    {
        return $this->hasMany('App\Model\city','id','city');
    }
    public function invoicetocity()
    {
        return $this->hasMany('App\Model\city','id','city2');
    }   

    public function addresstodistrict()
    {
        return $this->hasMany('App\Model\district','id','district');
    }
    public function invoicetodistrict()
    {
        return $this->hasMany('App\Model\district','id','district2');
    } 
    
    public function addresstoquarter()
    {
        return $this->hasMany('App\Model\quarter','id','quarter');
    }
    public function invoicetodquarter()
    {
        return $this->hasMany('App\Model\quarter','id','quarter2');
    }

    public function address_store()
    {
        return $this->hasMany('App\Model\stores', 'id', 'store_id');
    }
} 
