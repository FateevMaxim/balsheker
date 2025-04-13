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
        $qrUrdzhar = QrCodes::query()->select()->where('id', 3)->first();
        $qrPriozersk = QrCodes::query()->select()->where('id', 4)->first();
        $qrShimkent = QrCodes::query()->select()->where('id', 5)->first();
        $qrAstana = QrCodes::query()->select()->where('id', 6)->first();
        $qrKizilorda = QrCodes::query()->select()->where('id', 7)->first();
        $qrTemirtau = QrCodes::query()->select()->where('id', 8)->first();
        $qrIssik = QrCodes::query()->select()->where('id', 9)->first();
        $qrAtyrau = QrCodes::query()->select()->where('id', 10)->first();
        $qrAktau = QrCodes::query()->select()->where('id', 11)->first();
        $qrAktau = QrCodes::query()->select()->where('id', 11)->first();
        $qrAlmaly = QrCodes::query()->select()->where('id', 12)->first();
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
            $config = Configuration::query()->select('address', 'title_text', 'address_two')->first();
            return view('newstock')->with(compact('count', 'config', 'qr'));
        }elseif (Auth::user()->type === 'almatyin') {
            $count = TrackList::query()->whereDate('to_almaty', Carbon::today())->where('status', 'Получено на складе в Алматы')->count();
            return view('almaty', ['count' => $count, 'config' => $config, 'cityin' => 'Алматы', 'qr' => $qr]);
        }elseif (Auth::user()->type === 'urdzharin') {
            $count = TrackList::query()->whereDate('to_city', Carbon::today())->where('status', 'Получено на складе в Урджаре')->count();
            return view('almaty', ['count' => $count, 'config' => $config, 'cityin' => 'Урджар', 'qr' => $qrUrdzhar]);
        }elseif (Auth::user()->type === 'priozerskin') {
            $count = TrackList::query()->whereDate('to_city', Carbon::today())->where('status', 'Получено на складе в Приозёрске')->count();
            return view('almaty', ['count' => $count, 'config' => $config, 'cityin' => 'Приозёрск', 'qr' => $qrPriozersk]);
        }elseif (Auth::user()->type === 'shimkentin') {
            $count = TrackList::query()->whereDate('to_city', Carbon::today())->where('status', 'Получено на складе в Шымкенте')->count();
            return view('almaty', ['count' => $count, 'config' => $config, 'cityin' => 'Шымкент', 'qr' => $qrShimkent]);
        }elseif (Auth::user()->type === 'astanain') {
            $count = TrackList::query()->whereDate('to_city', Carbon::today())->where('status', 'Получено на складе в Астане')->count();
            return view('almaty', ['count' => $count, 'config' => $config, 'cityin' => 'Астана', 'qr' => $qrAstana]);
        }elseif (Auth::user()->type === 'kizilordain') {
            $count = TrackList::query()->whereDate('to_city', Carbon::today())->where('status', 'Получено на складе в Қызылорда')->count();
            return view('almaty', ['count' => $count, 'config' => $config, 'cityin' => 'Қызылорда', 'qr' => $qrKizilorda]);
        }elseif (Auth::user()->type === 'temirtauin') {
            $count = TrackList::query()->whereDate('to_city', Carbon::today())->where('status', 'Получено на складе в Теміртау')->count();
            return view('almaty', ['count' => $count, 'config' => $config, 'cityin' => 'Теміртау', 'qr' => $qrTemirtau]);
        }elseif (Auth::user()->type === 'issikin') {
            $count = TrackList::query()->whereDate('to_city', Carbon::today())->where('status', 'Получено на складе в Иссыке')->count();
            return view('almaty', ['count' => $count, 'config' => $config, 'cityin' => 'Иссык', 'qr' => $qrIssik]);
        }elseif (Auth::user()->type === 'atyrauin') {
            $count = TrackList::query()->whereDate('to_city', Carbon::today())->where('status', 'Получено на складе в Атырау')->count();
            return view('almaty', ['count' => $count, 'config' => $config, 'cityin' => 'Атырау', 'qr' => $qrAtyrau]);
        }elseif (Auth::user()->type === 'aktauin') {
            $count = TrackList::query()->whereDate('to_city', Carbon::today())->where('status', 'Получено на складе в Актау')->count();
            return view('almaty', ['count' => $count, 'config' => $config, 'cityin' => 'Актау', 'qr' => $qrAktau]);
        }elseif (Auth::user()->type === 'almalyin') {
            $count = TrackList::query()->whereDate('to_city', Carbon::today())->where('status', 'Получено на складе в Алмалы')->count();
            return view('almaty', ['count' => $count, 'config' => $config, 'cityin' => 'Алмалы', 'qr' => $qrAlmaly]);
        }elseif (Auth::user()->type === 'almatyout') {
            $count = TrackList::query()->whereDate('to_client', Carbon::today())->count();
            return view('almatyout', ['count' => $count, 'config' => $config, 'cityin' => 'Алматы', 'qr' => $qr]);
        }elseif (Auth::user()->type === 'urdzharout') {
            $count = TrackList::query()->whereDate('to_client_city', Carbon::today())->count();
            return view('almatyout', ['count' => $count, 'config' => $config, 'cityin' => 'Урджар', 'qr' => $qrUrdzhar]);
        }elseif (Auth::user()->type === 'priozerskout') {
            $count = TrackList::query()->whereDate('to_client_city', Carbon::today())->count();
            return view('almatyout', ['count' => $count, 'config' => $config, 'cityin' => 'Приозёрск', 'qr' => $qrPriozersk]);
        }elseif (Auth::user()->type === 'shimkentout') {
            $count = TrackList::query()->whereDate('to_client_city', Carbon::today())->count();
            return view('almatyout', ['count' => $count, 'config' => $config, 'cityin' => 'Шымкент', 'qr' => $qrShimkent]);
        }elseif (Auth::user()->type === 'astanaout') {
            $count = TrackList::query()->whereDate('to_client_city', Carbon::today())->count();
            return view('almatyout', ['count' => $count, 'config' => $config, 'cityin' => 'Астана', 'qr' => $qrAstana]);
        }elseif (Auth::user()->type === 'kizilordaout') {
            $count = TrackList::query()->whereDate('to_client_city', Carbon::today())->count();
            return view('almatyout', ['count' => $count, 'config' => $config, 'cityin' => 'Қызылорда', 'qr' => $qrKizilorda]);
        }elseif (Auth::user()->type === 'temirtauout') {
            $count = TrackList::query()->whereDate('to_client_city', Carbon::today())->count();
            return view('almatyout', ['count' => $count, 'config' => $config, 'cityin' => 'Теміртау', 'qr' => $qrTemirtau]);
        }elseif (Auth::user()->type === 'issikout') {
            $count = TrackList::query()->whereDate('to_client_city', Carbon::today())->count();
            return view('almatyout', ['count' => $count, 'config' => $config, 'cityin' => 'Иссык', 'qr' => $qrIssik]);
        }elseif (Auth::user()->type === 'atyrauout') {
            $count = TrackList::query()->whereDate('to_client_city', Carbon::today())->count();
            return view('almatyout', ['count' => $count, 'config' => $config, 'cityin' => 'Атырау', 'qr' => $qrAtyrau]);
        }elseif (Auth::user()->type === 'aktauout') {
            $count = TrackList::query()->whereDate('to_client_city', Carbon::today())->count();
            return view('almatyout', ['count' => $count, 'config' => $config, 'cityin' => 'Актау', 'qr' => $qrAktau]);
        }elseif (Auth::user()->type === 'almalyout') {
            $count = TrackList::query()->whereDate('to_client_city', Carbon::today())->count();
            return view('almatyout', ['count' => $count, 'config' => $config, 'cityin' => 'Алмалы', 'qr' => $qrAlmaly]);
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
            ->select( 'client_track_lists.track_code', 'client_track_lists.detail', 'client_track_lists.created_at','client_track_lists.id',
                'track_lists.to_china','track_lists.to_almaty','track_lists.to_client','track_lists.to_city','track_lists.city','track_lists.to_client_city','track_lists.client_accept','track_lists.status')
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
