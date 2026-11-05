<?php

namespace App\Imports;

use App\Models\TrackList;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithColumnLimit;

class TracksImport implements ToModel, SkipsOnError, WithColumnLimit
{

    use Importable;
    private $date;

    public function __construct(string $date)
    {
        $this->date = $date;
    }

    /**
     * Читаем только первые колонки: в файлах от поставщиков стили колонок
     * бывают прописаны до XFD, и без лимита PhpSpreadsheet перебирает
     * ~16k пустых ячеек на строку и падает по памяти.
     */
    public function endColumn(): string
    {
        return 'C';
    }
    /**
     * @param \Throwable $e
     */
    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if (isset($row[1])){
            return new TrackList([
                'track_code' => $row[1],
                'to_china' => $this->date,
                'status' => 'Получено в Китае',
                'reg_china' => 1,
                'created_at' => date(now()),
            ]);
        }
    }
}
