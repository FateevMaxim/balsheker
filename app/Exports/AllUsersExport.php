<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AllUsersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{

    use Importable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = User::query()
            ->select('name', 'surname', 'login', 'password', 'city', 'created_at', 'is_active', 'login_date')
            ->where('type', null)
            ->get();

        return $data;
    }

    /**
     * @param $data
     * @return array
     */
    public function map($data): array
    {
        return [
            $data->name,
            $data->surname,
            $data->login,
            $data->password,
            $data->city,
            $data->created_at,
            $data->is_active == 1 ? 'Активен' : ($data->is_active == 0 ? 'Не активирован' : 'Заблокирован'),
            $data->login_date
        ];
    }
    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER,
        ];
    }
    public function headings(): array
    {
        return [
            'Имя',
            'Фамилия',
            'Номер телефона',
            'Пароль',
            'Город',
            'Дата регистрации',
            'Статус',
            'Дата входа',
        ];
    }
}
