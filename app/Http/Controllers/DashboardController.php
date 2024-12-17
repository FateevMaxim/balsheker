<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShowUsersRequest;
use App\Models\City;
use App\Models\ClientTrackList;
use App\Models\Configuration;
use App\Models\Message;
use App\Models\QrCodes;
use App\Models\TrackList;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index ()
    {
        $qr = QrCodes::query()->select()->where('id', 1)->first();
        $qrChina = QrCodes::query()->select()->where('id', 2)->first();
        $config = Configuration::query()->select('address', 'title_text', 'address_two', 'whats_app')->first();
        $cities = City::query()->select('title')->get();
        if (Auth::user()->is_active === 1 && Auth::user()->type === null){
            $tracks = ClientTrackList::query()
                ->leftJoin('track_lists', 'client_track_lists.track_code', '=', 'track_lists.track_code')
                ->select('client_track_lists.track_code', 'client_track_lists.detail', 'client_track_lists.created_at', 'client_track_lists.id',
                    'track_lists.to_china', 'track_lists.to_almaty', 'track_lists.to_client', 'track_lists.to_city',
                    'track_lists.city', 'track_lists.to_client_city', 'track_lists.client_accept', 'track_lists.status')
                ->where('client_track_lists.user_id', Auth::user()->id)
                ->where('client_track_lists.status', null)
                ->orderByDesc('client_track_lists.id')
                ->get();
            $count = count($tracks);

            $messages = Message::all();

            return view('dashboard')->with(compact('tracks', 'count', 'messages', 'config'));
        }elseif (Auth::user()->is_active === 1 && Auth::user()->type === 'stock'){
            $count = TrackList::query()->whereDate('to_china', Carbon::today())->count();
            return view('stock', ['count' => $count, 'config' => $config, 'qr' => $qrChina]);
        }elseif (Auth::user()->type === 'newstock') {
            $count = TrackList::query()->whereDate('to_china', Carbon::today())->count();
            logger()->info('Кол-во новых посылок: ' . $count);
            $config = Configuration::query()->select('address', 'title_text', 'address_two')->first();
            return view('newstock')->with(compact('count', 'config', 'qr'));
        }elseif (Auth::user()->type === 'almatyin') {
            $count = TrackList::query()->whereDate('to_almaty', Carbon::today())->where('status', 'Получено на складе в Алматы')->count();
            return view('almaty', ['count' => $count, 'config' => $config, 'cityin' => 'Алматы', 'qr' => $qr]);
        }elseif (Auth::user()->type === 'almatyout') {
            $count = TrackList::query()->whereDate('to_client', Carbon::today())->count();
            return view('almatyout', ['count' => $count, 'config' => $config, 'cityin' => 'Алматы', 'qr' => $qr]);
        }elseif (Auth::user()->is_active === 1 && Auth::user()->type === 'othercity'){
            $count = TrackList::query()->whereDate('to_client', Carbon::today())->count();
            return view('othercity')->with(compact('count', 'config', 'cities', 'qr'));
        }elseif ((Auth::user()->is_active === 1 && Auth::user()->type === 'admin') || (Auth::user()->is_active === 1 && Auth::user()->type === 'moderator')){
            $messages = Message::all();
            $config = Configuration::query()->select('address')->first();
            $search_phrase = '';
            $users = User::query()->select('id', 'name', 'surname', 'type', 'login', 'city', 'is_active', 'block', 'password', 'created_at')->where('type', null)->where('is_active', false)->get();
            return view('admin')->with(compact('users', 'messages', 'search_phrase', 'config'));
        }
        return view('register-me')->with(compact( 'config'));
    }

    public function archive ()
    {
            $tracks = ClientTrackList::query()
                ->leftJoin('track_lists', 'client_track_lists.track_code', '=', 'track_lists.track_code')
                ->select( 'client_track_lists.track_code', 'client_track_lists.detail', 'client_track_lists.created_at',
                    'track_lists.to_china','track_lists.to_almaty','track_lists.to_client','track_lists.client_accept','track_lists.status')
                ->where('client_track_lists.user_id', Auth::user()->id)
                ->where('client_track_lists.status', '=', 'archive')
                ->get();
        $config = Configuration::query()->select('address', 'title_text', 'address_two')->first();
            $count = count($tracks);
            return view('dashboard')->with(compact('tracks', 'count', 'config'));
    }

    public function users ()
    {
        $config = Configuration::query()->select('address', 'title_text', 'address_two', 'whats_app')->first();

        $userTracksCount = User::select('users.*')
            ->leftJoin('client_track_lists', 'users.id', '=', 'client_track_lists.user_id')
            ->leftJoin('track_lists', 'client_track_lists.track_code', '=', 'track_lists.track_code')
            ->selectRaw('COUNT(client_track_lists.id) as client_track_lists_count')
            ->groupBy('users.id')
            ->orderByDesc('client_track_lists_count')
            ->paginate(30);

        $cities = City::all();

        return view('users')->with(compact('userTracksCount', 'config', 'cities'));
        /*foreach ($userTracksCount as $user) {
            echo "Пользователь " . $user->id . " - " . $user->client_track_lists_count . "<br>";
        }*/
    }
    public function usersFilter (ShowUsersRequest $request)
    {
        $config = Configuration::query()->select('address', 'title_text', 'address_two', 'whats_app')->first();

        $userTracksCount = User::select('users.*')
            ->when($request->userStatus() !== "Все",
                fn($query) => $query->where('users.is_active', $request->userStatus()))
            ->when($request->userCity() !== "Все города",
                fn($query) => $query->where('users.city', $request->userCity()))
            ->leftJoin('client_track_lists', 'users.id', '=', 'client_track_lists.user_id')
            ->leftJoin('track_lists', 'client_track_lists.track_code', '=', 'track_lists.track_code')
            ->selectRaw('COUNT(client_track_lists.id) as client_track_lists_count')
            ->groupBy('users.id')
            ->orderByDesc('client_track_lists_count')
            ->paginate(30);

        $cities = City::all();

        $statusFiler = [
            ['key' => 'Все', 'value' => 'Все', 'selected' => false],
            ['key' => '1', 'value' => 'Активные', 'selected' => false],
            ['key' => '0', 'value' => 'Неактивные', 'selected' => false],
            ['key' => '2', 'value' => 'Заблокированные', 'selected' => false],
        ];

        foreach ($statusFiler as &$item) {
            if ($item['key'] === $request->userStatus()) {
                $item['selected'] = true;
            }
        }
        $user_city = $request->userCity();

        return view('users')->with(compact('userTracksCount', 'config', 'cities' , 'statusFiler', 'user_city'));

    }



}
