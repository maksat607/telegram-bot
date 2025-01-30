<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Maksatsaparbekov\KuleshovAuth\Traits\Chattable;

class Application extends Model
{
    use HasFactory, Chattable;
    protected $guarded = [];

}
