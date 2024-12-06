@if(isset($config->address)) @section( 'chinaaddress', $config->address ) @endif
@if(isset($config->title_text)) @section( 'title_text', $config->title_text ) @endif
@if(isset($config->address_two)) @section( 'address_two', $config->address_two ) @endif
<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="w-full h-22 pl-4 pr-4 pb-4">
                <div class="grid h-full grid-cols-3 mx-auto circleBaseTwo circle2">
                    <button type="button" class="inline-flex flex-col items-center justify-center px-5">
                    </button>
                    <div class="mx-auto">
                        <button data-modal-target="popup-modal" data-modal-toggle="popup-modal" type="button" class="inline-flex flex-col items-center justify-center px-5">
                            <div class="circleBase circle1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-11 h-11 block mx-auto mt-2 text-white">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </div>
                        </button>
                    </div>
                    @if(Auth::user()->type === 'admin')<a href="{{ route('result') }}" class="inline-flex flex-col items-center justify-center px-5">
                        <button type="button">
                            <div class="items-center">
                                <span class="text-sm text-white leading-8">Итоги</span>
                            </div>
                        </button>
                    </a>@endif
                </div>
            </div>
            <div id="popup-modal" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] md:h-full">
                <div class="relative w-full h-full max-w-md md:h-auto">
                    <div class="relative bg-white rounded-lg shadow">
                        <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-hide="popup-modal">
                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            <span class="sr-only">Закрыть</span>
                        </button>
                        <div class="p-6 text-center">
                            <h4 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Добавление сообщения</h4>
                            <form method="POST" action="{{ route('message-add') }}">
                                <textarea id="message" name="message" rows="5" required="required" class="block mb-2 mx-auto w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 " placeholder="Сообщение..."></textarea>
                                <button type="submit" class="items-center px-4 py-3 bg-fuchsia-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700  focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Отправить сообщение') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @if(Route::currentRouteName() != 'dashboard')
                <div class="flex items-center justify-start mt-2 ml-6">
                    <a href="{{ route('result') }}">
                        <x-classic-button class="mx-auto mb-4 w-full">
                            {{ __('Назад') }}
                        </x-classic-button>
                    </a>
                </div>
            @endif
            @if(session()->has('message'))
                <div id="alert-3" class="flex p-4 mb-4 mr-6 ml-6 text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                    <div class="ml-3 text-sm font-medium">
                        {{ session()->get('message') }}
                    </div>
                    <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-3" aria-label="Close">
                        <span class="sr-only">Закрыть</span>
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </button>
                </div>
            @endif
            @if(session()->has('error'))
                <div id="alert-2" class="flex mr-6 ml-6 p-4 mb-4 text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <div class="ml-3 text-sm font-medium">
                        {{ session()->get('error') }}
                    </div>
                    <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex h-8 w-8 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-2" aria-label="Close">
                        <span class="sr-only">Закрыть</span>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </button>
                </div>
            @endif
            @if(Route::currentRouteName() === 'dashboard')
                @foreach($messages as $message)
                    <div id="alert-2" class="flex justify-between mr-6 ml-6 p-4 mb-4 text-red-800 rounded-lg bg-red-50" role="alert">
                        <div class="flex flex-row ml-3 text-sm font-medium">
                            <svg aria-hidden="true" class="flex-shrink-0 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            <span class="sr-only">Info</span>
                            &nbsp;{{ $message->message }}
                        </div>
                        @if(\Illuminate\Support\Facades\Auth::user()->type === 'admin' || \Illuminate\Support\Facades\Auth::user()->type === 'moderator')
                            <form method="POST" action="{{ route('message-delete', ['id' => $message->id]) }}">
                                <button type="submit" class="ml-auto -mx-1.5 -my-1.5 bg-blue-50 text-blue-500 rounded-lg focus:ring-2 focus:ring-blue-400 p-1.5 hover:bg-blue-200 inline-flex h-8 w-8 dark:bg-gray-800 dark:text-blue-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-1" aria-label="Close">
                                    <span class="sr-only">Закрыть</span>
                                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            @endif

            <div class="griditems-center justify-start mt-2 mb-4 ml-6">
                <div class="w-full grid-cols-12 text-center">
                    <h2 class="text-3xl font-extrabold">Фильтр клиентов</h2>
                </div>
                <form method="POST" action="{{ route('users-filter') }}" id="userFilterForm">
                    @csrf
                <div class="grid grid-cols-2 md:grid-cols-6">
                        <div class="mr-2">
                            <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Выберите статус</label>
                            <select name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                @if(isset($statusFiler))
                                    @foreach($statusFiler as $status)
                                        <option value="{{ $status["key"] }}" @if($status["selected"] === true) selected @endif >{{ $status["value"] }}</option>
                                    @endforeach
                                @else
                                    <option value="Все" selected>Все</option>
                                    <option value="1">Активные</option>
                                    <option value="2">Заблокированные</option>
                                    <option value="0">Не активированные</option>
                                @endif

                            </select>
                        </div>
                        <div class="mr-2">
                            <label for="city" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Выберите город</label>
                            <select name="city" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                @if(isset($user_city))
                                    <option value="Все города">Все города</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->title }}" @if($city->title === $user_city) selected @endif>{{ $city->title }}</option>
                                    @endforeach
                                @else
                                    <option value="Все города" selected>Все города</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->title }}">{{ $city->title }}</option>
                                    @endforeach
                                @endif

                            </select>
                        </div>

                    <div style="align-content: end;" class="mr-2 mt-2 md:mt-0">
                        <button type="submit" class="w-full focus:outline-none text-white bg-[#31c48d] hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2">Показать</button>
                    </div>
                        <div style="align-content: end;" class="mr-2 mt-2 md:mt-0">
                            <a href="" id="a"><button type="button" id="excel" class="w-full text-white  bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Экспорт в Excel</button></a>
                        </div>

                </div>
            </form>
            </div>

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

                <table id="userTable" class="w-full text-sm text-left text-gray-500 p-4">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Имя
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Телефон
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Дата последнего входа
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Статус
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Город
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Количество заказов
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                @foreach($userTracksCount as $user)

                            <tr class="bg-white border-b hover:bg-gray-50">

                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{$user->name}}
                                </th>
                                <td class="px-6 py-4">
                                    <a href="https://api.whatsapp.com/send?phone={{Str::remove('+', $user->login)}}">

                                    {{ $user->login }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">

                                    {{ $user->login_date }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->is_active === 1)
                                        <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded">Активный</span>
                                    @elseif($user->is_active === 3)
                                        <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded">Заблокирован</span>
                                    @else
                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded">Не активирован</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    {{$user->city}}
                                </td>
                                <td class="px-6 py-4">
                                    {{$user->client_track_lists_count}}
                                </td>
                                <!-- Main modal -->
                            </tr>
                @endforeach
                    </tbody>
                </table>
                <div class="mt-2 mb-2">
                    {{ $userTracksCount->links() }}
                </div>
            </div>
            <script type="text/javascript">
                $(document).ready(function(){
                    $("#a").attr("href", 'users-export')
                });
            </script>
        </div>
    </div>


</x-app-layout>
