<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmbeddingsTable extends Migration
{
    public function up()
    {
        Schema::create('embeddings', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->json('embedding'); // Store vector as JSON
            $table->timestamps();
        });

        // Load the SQLite extension
        DB::statement("SELECT load_extension('sqlite-vss')");

        // Create a vector index for fast search
        DB::statement('CREATE VIRTUAL TABLE vss_index USING vss(embedding(1536))'); // Example: OpenAI embedding size 1536
    }

    public function down()
    {
        Schema::dropIfExists('embeddings');
    }
}
