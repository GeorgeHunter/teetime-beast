<?php

namespace App;

use App\Exceptions\InsufficientAvailableEntriesException;
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

    public function hasOrderFor($email)
    {
        return $this->orders()->where('email', $email)->count() > 0;
    }

    public function ordersFor($email)
    {
        return $this->orders()->where('email', $email)->get();
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    public function registerEntry($email, $entryQuantity)
    {
        $entries = $this->entries()->available()->take($entryQuantity)->get();

        if ($entries->count() < $entryQuantity) {
            throw new InsufficientAvailableEntriesException;
        }

        $order = $this->orders()->create(['email' => $email]);

        foreach ($entries as $entry) {
            $order->entries()->save($entry);
        }

        return $order;
    }
    
    public function addEntries($quantity)
    {
        foreach (range(1, $quantity) as $i) {
            $this->entries()->create([]);
        }

        return $this;
    }

    public function getEntriesRemainingAttribute()
    {
        return $this->entries()->available()->count();
    }
}
