<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchTermHistory extends Model
{
    protected $guarded = [];
    protected $table = 'search_term_history';

    public function search_term()
    {
        return $this->hasOne('App\Search', 'id', 'search_id');
    }
}
