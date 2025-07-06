<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Embedding extends Model
{
    protected $connection = 'libsql'; // Use the libsql connection

    protected $table = 'embeddings'; // Specify the table name

    protected $fillable = ['key', 'embedding']; // Fillable attributes
}
