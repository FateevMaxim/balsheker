<?php

namespace App\Console\Commands;

use App\Services\Cargo786ApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncCitiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cities:sync {--lang=ru : Language for API request}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync cities from Cargo786 API to database';

    private Cargo786ApiService $cargo786Service;

    public function __construct(Cargo786ApiService $cargo786Service)
    {
        parent::__construct();
        $this->cargo786Service = $cargo786Service;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $lang = $this->option('lang') ?? 'ru';

        $this->info('Starting cities synchronization...');

        $result = $this->cargo786Service->getAddressList($lang);

        if (!$result['success']) {
            $this->error('Failed to fetch cities from API: ' . $result['msg']);
            return;
        }

        $targetCities = $result['data']['target_local_arr'] ?? [];

        if (empty($targetCities)) {
            $this->warn('No cities found in target_local_arr');
            return;
        }

        $synced = 0;
        $updated = 0;
        $deleted = 0;

        // Get all current city IDs from API response
        $apiCityIds = array_column($targetCities, 'id');

        // Process insertions and updates
        foreach ($targetCities as $city) {
            $cityId = $city['id'];
            $cityName = $city['name'];

            $affected = DB::table('cities')->updateOrInsert(
                ['city_id' => $cityId],
                [
                    'name' => $cityName,
                    'updated_at' => now()
                ]
            );

            if ($affected) {

                $existing = DB::table('cities')->where('city_id', $cityId)->first();
                if ($existing && $existing->created_at !== $existing->updated_at) {
                    $updated++;
                } else {
                    $synced++;
                }
            }
        }

        // Delete cities that are no longer in the API response
        $deletedCount = DB::table('cities')
            ->whereNotIn('city_id', $apiCityIds)
            ->delete();

        if ($deletedCount > 0) {
            $deleted = $deletedCount;
        }

        $this->info("Cities synchronization completed!");
        $this->info("New cities added: {$synced}");
        $this->info("Cities updated: {$updated}");
        $this->info("Cities deleted: {$deleted}");
        $this->info("Total cities processed: " . count($targetCities));
    }
}
