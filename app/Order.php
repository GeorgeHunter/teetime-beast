<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];
    
    public function entries()
    {
        return $this->hasMany(Entry::class);
    }
    
    public function cancel()
    {
        $this->entries()->each(function($entry) {
            $entry->release();
        });
        $this->delete();
    }

    public function getEntryCountAttribute()
    {
        return $this->entries()->count();
    }
}
