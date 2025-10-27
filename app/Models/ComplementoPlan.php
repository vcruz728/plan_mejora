<?php
// app/Models/ComplementoPlan.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplementoPlan extends Model
{
    protected $table = 'complemento_plan';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
