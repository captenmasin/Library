<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;

it('resets an empty table sequence so its next id is one', function () {
    $connection = Mockery::mock(Connection::class);
    $connection->shouldReceive('getDriverName')->once()->andReturn('pgsql');

    $query = Mockery::mock(Builder::class);
    $query->shouldReceive('max')->once()->with('id')->andReturnNull();

    DB::shouldReceive('connection')->once()->andReturn($connection);
    DB::shouldReceive('select')->once()->andReturn([
        (object) [
            'sequence_name' => 'book_category_id_seq',
            'table_name' => 'book_category',
            'column_name' => 'id',
        ],
    ]);
    DB::shouldReceive('table')->once()->with('book_category')->andReturn($query);
    DB::shouldReceive('statement')
        ->once()
        ->with('SELECT setval(?, ?, ?)', ['book_category_id_seq', 1, false])
        ->andReturnTrue();

    $this->artisan('db:sync-sequences')
        ->expectsOutput('Synced book_category.id → 0')
        ->expectsOutput('Done.')
        ->assertSuccessful();
});
