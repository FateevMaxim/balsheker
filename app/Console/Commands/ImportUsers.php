<?php

namespace App\Console\Commands;

use App\Imports\UsersImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ImportUsers extends Command
{
    protected $signature = 'import:users';
    protected $description = 'Импорт пользователей из CSV';

    public function handle(): void
    {
        $path = storage_path('app/users.csv');

        // Считываем строки с заголовками без импорта
        $rows = Excel::toCollection(new class implements ToCollection, WithHeadingRow {
            public Collection $rows;

            public function collection(Collection $collection)
            {
                $this->rows = $collection;
            }
        }, $path)->first();

        $this->output->progressStart(count($rows));

        foreach ($rows as $row) {
            if (User::where('login', $row['login'])->exists()) {
                $this->output->progressAdvance();
                continue;
            }

            User::create([
                'name'      => $row['name'],
                'surname'   => $row['surname'],
                'login'     => $row['login'],
                'city'      => $row['city'],
                'is_active' => true,
                'password'  => $row['password'],
            ]);

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->info('Импорт завершен.');
    }
}
