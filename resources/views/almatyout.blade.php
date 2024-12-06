@if(isset($config->address)) @section( 'chinaaddress', $config->address ) @endif
@if(isset($config->title_text)) @section( 'title_text', $config->title_text ) @endif
@if(isset($config->address_two)) @section( 'address_two', $config->address_two ) @endif

<x-app-layout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                @if(session()->has('message'))
                    <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                        <span class="font-medium">{{ session()->get('message') }}
                    </div>
                @endif
                    <div id="toast-error" class="flex absolute z-10 items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" style="width: 95%;" role="alert">
                        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <div>
                            <span class="font-medium">Такой трек у клиентов не найден!</span>
                        </div>
                    </div>
                    <div id="toast-success" class="flex absolute z-10 items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" style="width: 95%;" role="alert">
                        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <div>
                            <span class="font-medium">Товар выдан!</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 max-w-3xl mx-auto md:grid-cols-2 h-22 pl-6 pr-6 pb-4">

                        <div class="min_height round_border p-4">
                            <div>
                                <h3 class="mt-0 p-4 text-2xl font-medium leading-tight text-primary">Пункт выдачи в {{ $cityin }}</h3>
                            </div>
                            <form method="POST" action="{{ route('getinfo-product') }}" id="getInfoForm">
                                <div class="w-full">
                                    <x-input-label for="phone" :value="__('Трек код')" />
                                    @csrf
                                    <x-text-input id="track_code" class="block mt-1 w-full border-2 border-sky-400" type="text" name="track_code" autofocus />
                                </div>
                            </form>
                            <div class="p-4 bottom-0">
                                <h3 class="mt-0 text-2xl font-medium leading-tight text-primary">Выдано сегодня: <span id="count">{{ $count }}</span></h3>
                            </div>

                        </div>

                        <div class="grid md:mt-0 mt-4 p-4 min_height round_border relative">
                            <div class="grid mt-5">
                                <div>
                                    <div class="text-right">
                                        <span class="mt-0 text-base text-gray-500">Данные клиента</span>
                                        <p><b><span id="login" class="text text-3xl"></span></b></p>
                                        <p class="text-2xl"><b><span id="surname"></span>&nbsp;<span id="name"></span></b></p>
                                        <p class="text-2xl"><b><span id="city"></span></b></p>
                                        <h5 id="block" class="mt-0 text text-red-400 font-medium leading-tight text-primary" style="display: none;">Клиент находится в чёрном списке</h5>

                                    </div>

                                    <h5 id="unknown" class="mt-0 text text-red-400 font-medium leading-tight text-primary" style="display: none;">Неопознанный трек код</h5>
                                    <p class="mt-0 text-base text-gray-500">Трек код</p>
                                    <b><span id="trackcode" class="text text-3xl"></span></b>
                                    <p class="mt-0 text-base text-gray-500">Дата регистрации клиентом</p>
                                    <p><b><span id="client_added" class="text text-xl"></span></b></p>
                                    <p class="mt-0 text-base text-gray-500">Получено на складе в Китае</p>
                                    <p><b><span id="to_china" class="text text-xl"></span></b></p>
                                    <p class="mt-0 text-base text-gray-500">Получено на складе в Алматы</p>
                                    <p><b><span id="to_almaty" class="text text-xl"></span></b></p>

                                    <div id="filial_one">
                                        <p class="mt-0 text-base text-gray-500">Отправлено в <span id="city_name"></span></p>
                                        <p><b><span id="to_othercity" class="text text-xl"></span></b></p>
                                    </div>
                                    <div id="filial_two">
                                        <p class="mt-0 text-base text-gray-500">Получено в <span id="city_name_two"></span></p>
                                        <p><b><span id="to_city" class="text text-xl"></span></b></p>
                                    </div>
                                    <p class="mt-0 text-base text-gray-500">Дата выдачи клиенту</p>
                                    <p><b><span id="to_client" class="text text-xl"></span></b></p>
                                    <p><b><span id="to_client_city" class="text text-xl"></span></b></p>
                                    <p class="mt-0 text-base text-gray-500">Дата получения клиентом</p>
                                    <p><b><span id="client_accept" class="text text-xl"></span></b></p>
                                </div>
                            </div>

                            <div class="absolute w-full bottom-0 p-4">
                                <form method="POST" action="{{ route('othercity-product') }}" id="almatyOut">
                                        <div class="w-full">
                                            @csrf

                                            <x-primary-button class="mx-auto w-full">
                                                {{ __('Выдать клиенту') }}
                                            </x-primary-button>
                                            <x-secondary-button class="mx-auto mt-4 w-full" id="clear">
                                                {{ __('Отправить дальше') }}
                                            </x-secondary-button>
                                        </div>
                                </form>
                            </div>

                        </div>
                    <script>
                        document.getElementById('toast-success').style.display = 'none';
                        document.getElementById('toast-error').style.display = 'none';

                        /* прикрепить событие submit к форме */
                        $("#getInfoForm").submit(function(event) {
                            /* отключение стандартной отправки формы */
                            event.preventDefault();

                            /* собираем данные с элементов страницы: */
                            var $form = $( this ),
                                track_code = $("#track_code").val();

                            url = $form.attr( 'action' );
                            $("#trackcode").text(track_code);
                            /* отправляем данные методом POST */
                            $.post( url, { track_code: track_code } )
                                .done(function( data ) {
                                    $("#to_china").text(null);
                                    $("#to_almaty").text(null);
                                    $("#to_city").text(null);
                                    $("#to_client_city").text(null);
                                    $("#city_name").text(null);
                                    $("#city_name_two").text(null);
                                    $("#to_othercity").text(null);
                                    $("#client_accept").text(null);
                                    $("#to_client").text(null);
                                    if (data[2] == null){
                                        $("#unknown").css("display","block");
                                        document.getElementById('toast-error').style.display = 'flex'; // показываем сообщение
                                        $("#client_added").text(null);
                                        $("#surname").text(null);
                                        $("#name").text(null);
                                        $("#login").text(null);
                                        $("#city").text(null);
                                        setTimeout(function() {
                                            document.getElementById('toast-error').style.display = 'none'; // скрываем сообщение через 5 секунд
                                        }, 10000); // 5000 миллисекунд = 5 секунд
                                    }else{
                                        $("#surname").text(data[1].surname);
                                        $("#name").text(data[1].name);
                                        $("#login").text(data[1].login);
                                        $("#city").text(data[1].city);
                                        $("#client_added").text(data[2].created_at);
                                    }
                                    $("#to_china").text(data[0].to_china);
                                    $("#trackcode").text(track_code);
                                    $("#to_almaty").text(data[0].to_almaty);
                                    $("#to_city").text(data[0].to_city);
                                    $("#to_client_city").text(data[0].to_client_city);
                                    $("#city_name").text(data[0].city);
                                    $("#city_name_two").text(data[0].city);

                                    var city_name = data[0].city;

                                    if(city_name){
                                        $("#to_othercity").text(data[0].to_client);
                                        //$("#to_client").text(data[0].to_client);
                                    }else{

                                        $("#filial_one").css("display", "none");
                                        $("#filial_two").css("display", "none");
                                        $("#to_client").text(data[0].to_client);
                                    }

                                    $("#client_accept").text(data[0].client_accept);

                                    if (data[1].block === 'нет'){
                                        $("#unknown").css("display","block");
                                    }else if(data[1].block != null && data[1].block != 0){
                                        $("#block").css("display","block");
                                    }else{
                                        $("#block").css("display","none");
                                        $("#unknown").css("display","none");
                                    }


                                });

                            $("#track_code").val('');
                            document.getElementById('track_code').focus();
                        });

                        /* прикрепить событие submit к форме */
                        $("#almatyOut").submit(function(event) {
                            /* отключение стандартной отправки формы */
                            event.preventDefault();

                            /* собираем данные с элементов страницы: */
                            var $form = $( this ),
                                track_codes = $("#trackcode").text();
                            to_city = $("#city_name").text();
                            url = $form.attr( 'action' );

                            /* отправляем данные методом POST */
                            $.post( url, { track_codes: track_codes, to_city: to_city } )
                                .done(function( data ) {
                                    var count = parseInt($("#count").text());
                                    $("#count").text(count+1);
                                    $("#to_client").text(data["date"]);
                                    document.getElementById('toast-success').style.display = 'flex'; // показываем сообщение
                                    setTimeout(function() {
                                        document.getElementById('toast-success').style.display = 'none'; // скрываем сообщение через 5 секунд
                                    }, 5000); // 5000 миллисекунд = 5 секунд
                                    document.getElementById('track_code').focus();
                                });

                        });

                        /* прикрепить событие submit к форме */
                        $("#clear").click(function(event) {
                            /* отключение стандартной отправки формы */
                            event.preventDefault();
                            var $form = $( this ),
                                track_codes = $("#trackcode").text();
                            track_codes = $("#trackcode").text();
                            url = 'othercity-product';

                            /* отправляем данные методом POST */
                            $.post( url, { track_codes: track_codes, send: true } )
                                .done(function( data ) {
                                    var count = parseInt($("#count").text());
                                    $("#count").text(count+1);
                                    $("#to_client").text(data["date"]);
                                    document.getElementById('toast-success').style.display = 'flex'; // показываем сообщение
                                    setTimeout(function() {
                                        document.getElementById('toast-success').style.display = 'none'; // скрываем сообщение через 5 секунд
                                    }, 5000); // 5000 миллисекунд = 5 секунд
                                    document.getElementById('track_code').focus();
                                });

                        });

                    </script>
                </div>

                    @include('components.scanner-settings')


        </div>
</x-app-layout>
