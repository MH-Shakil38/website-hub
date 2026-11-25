<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteVisit extends Model
{
    protected $connection = 'central';

    protected $table = 'core_site_visits';

    public $timestamps = false;

    protected $fillable = ['host', 'path', 'visitor_hash', 'tenant_id', 'visited_at'];

    protected $casts = ['visited_at' => 'datetime'];
}
