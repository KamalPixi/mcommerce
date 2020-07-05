<?php

namespace App\AppModels;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    public function attribute() {
      return $this->belongsTo('App\AppModels\ProductAttribute');
    }
}
