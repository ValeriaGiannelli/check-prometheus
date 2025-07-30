<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitorLog extends Model
{
    protected $table = 'monitor_logs'; // Specify the new table name
    protected $fillable = ['client_id', 'description', 'received_at'];
    protected $dates = ['received_at'];
}
