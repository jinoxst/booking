<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    /*
    * properties for mail
    */
    public $book_date_str;
    public $userscnt;
    public $zones_type_name;

    protected $guarded = [];
}
