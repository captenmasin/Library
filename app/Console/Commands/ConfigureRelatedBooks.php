<?php

namespace App\Console\Commands;

use MeiliSearch\Client;
use Illuminate\Console\Command;

use function Laravel\Prompts\info;
use function Laravel\Prompts\note;

class ConfigureRelatedBooks extends Command
{
    protected $signature = 'books:related:configure';

    public function handle()
    {
        $index = app()->environment().'_books';
        note('Configuring books index: '.$index);

        note('Flush books');
        $this->call('scout:flush', [
            'model' => 'App\Models\Book',
        ]);

        note('Importing books');
        $this->call('scout:import', [
            'model' => 'App\Models\Book',
        ]);

        $client = new Client(
            config('scout.meilisearch.host'),
            config('scout.meilisearch.key')
        );

        note('Indexing...');
        $index = $client->index($index);

        note('Setting searchable attributes...');
        $index->updateSearchableAttributes([
            'tags',
            'authors',
            'description',
            'title',
        ]);

        note('Setting synonyms...');
        $index->updateSynonyms([
            // Genre equivalents
            'sci-fi' => ['science fiction', 'sci fi', 'scifi'],
            'ya' => ['young adult'],
            'thriller' => ['suspense', 'psychological thriller'],
            'rom-com' => ['romantic comedy'],
            'non-fiction' => ['nonfiction', 'fact', 'true story'],
            'historical fiction' => ['historical novel'],
            'memoir' => ['autobiography', 'life story'],
            'self-help' => ['self improvement', 'personal development'],
            'crime' => ['detective', 'mystery', 'whodunit'],
            'fantasy' => ['epic fantasy', 'urban fantasy', 'magic'],

            // Popular variations
            'dystopia' => ['dystopian', 'post-apocalyptic'],
            'graphic novel' => ['comic book', 'manga'],
            'classic' => ['literature', 'canonical'],

            // Audience targeting
            'children' => ['kids', 'childrens', 'young readers'],
            'teen' => ['ya', 'young adult', 'teenager'],
            'adult' => ['mature', 'grown-up'],

            // Miscellaneous
            'humor' => ['funny', 'comedy', 'humour'],
            'horror' => ['scary', 'terror', 'ghost'],
            'poetry' => ['poems', 'verse'],
        ]);

        info('Done!');
    }
}
