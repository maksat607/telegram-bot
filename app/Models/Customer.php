<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Customer extends Model
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    public function getTelegramIdAttribute($value)
    {
        if ($this->active != 1) {
            return null;
        }
        return ($value); // Convert the name to title case
    }
}
