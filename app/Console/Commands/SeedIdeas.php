<?php

namespace App\Console\Commands;

use App\Models\FeatureIdea;
use App\Services\Ideas\IdeaBank;
use Illuminate\Console\Command;

class SeedIdeas extends Command
{
    protected $signature = 'ideas:seed {--feature= : kunci fitur spesifik, default semua}';

    protected $description = 'Isi bank ide: 1.000 ide per fitur (idempotent via upsert).';

    public function handle(): int
    {
        $only = (string) ($this->option('feature') ?? '');

        $keys = $only !== ''
            ? [$only]
            : IdeaBank::keys();

        foreach ($keys as $key) {
            if (! array_key_exists($key, IdeaBank::FEATURES)) {
                $this->error("Fitur tidak dikenal: {$key}");

                return self::FAILURE;
            }

            $rows = IdeaBank::generate($key);

            foreach (array_chunk($rows, 250) as $chunk) {
                FeatureIdea::query()->upsert(
                    $chunk,
                    ['feature_key', 'slot'],
                    [
                        'title', 'description', 'audience', 'need', 'mechanism',
                        'impact', 'effort', 'submodule', 'priority', 'business_value',
                        'complexity', 'dependencies', 'security_notes', 'db_impact',
                        'api_impact', 'ui_impact', 'testing_notes',
                    ]
                );
            }

            $this->info("ideas:seed {$key} — ".count($rows).' ide (total: '.FeatureIdea::query()->where('feature_key', $key)->count().')');
        }

        return self::SUCCESS;
    }
}
