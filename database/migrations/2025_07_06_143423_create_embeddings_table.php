<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmbeddingsTable extends Migration
{
    public function up()
    {
        Schema::create('embeddings', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->json('embedding'); // Store vector as JSON
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('embeddings');
    }
}
