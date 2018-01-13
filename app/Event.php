<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $guarded = [];
    protected $dates = ['date'];

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }
    
    public function getFormattedDateAttribute()
    {
        return $this->date->format('F j, Y');
    }
    
    public function getFormattedStartTimeAttribute()
    {
        return $this->date->format('g:ia');
    }

    public function getEntryFeeInPoundsAttribute()
    {
        return number_format($this->entry_fee / 100, 2);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function registerEntry($email, $entryQuantity)
    {
        $order = $this->orders()->create([
            'email' => $email,
        ]);

        foreach (range(1, $entryQuantity) as $i) {
            $order->entries()->create([]);
        }

        return $order;
    }
    
    public function addTickets()
    {
        
    }
}
