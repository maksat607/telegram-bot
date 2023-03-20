<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">


{{--    <link rel="apple-touch-icon" type="image/png" href="https://cpwebassets.codepen.io/assets/favicon/apple-touch-icon-5ae1a0698dcc2402e9712f7d01ed509a57814f994c660df9f7a952f3060705ee.png">--}}
    <link href="data:image/x-icon;base64,AAABAAEAEBAAAAEAIABoBAAAFgAAACgAAAAQAAAAIAAAAAEAIAAAAAAAAAQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABAWFf9PT0/qAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAE9PT4jr6Of/3tzb/09PT4gAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAARFxb/6Obl/+De3f8RFxb/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABPT0+I7O7w/+jm5f/g3t3/r6yr/09PT4gAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADhAQ//Du7f/o5uX/4N7d/9nX1v8KDg3/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAEBYV//X29f/w7u3/6Obl/+De3f/Z19b/0M7N/w8VFP8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAICAgpfj6/P/19fX/8O7t/+jm5f/g3t3/2dfW/9DOzf/KxsX/29vbDgAAAAAAAAAAAAAAAAAAAAAAAAAAT09PjMTGxv/+/v7/9fX1/+rr6f8VGhn/CA0M/9nX1v/Qzs3/ysbF/woNDf9PT0+MAAAAAAAAAAAAAAAAAAAAAE9PT8b19/3//v7+//X19f8RFxb/rKan/6Sen/8SFxb/0M7N/8rGxf+XlZT/T09PxgAAAAAAAAAAAAAAAAAAAABPT0/GKR/h/yQa3v8lGdT/Hh8f/6ymp/+knp//DxgU/yYWo/8lFJr/MCJ5/09PT8YAAAAAAAAAAAAAAAAAAAAAT09PjCwh4/8kGt7/JhjU/zEkrv+4srP/SUhI/yMUrv8mFqP/JRSb/xISLv9PT0+MAAAAAAAAAAAAAAAAAAAAAAAAAAAQGBj/JRrY/yYZ1P8kGMr/JBe//yUWt/8lFq3/Jhaj/yUUmv8GBwe/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABEUGf8lGc3/JBjK/yQXv/8lFrf/JRat/yYWo/8QFhf/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAChUT/yIYy/8kF7//JRa3/ygZrP8OFhT/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABPT0+MT09Pxk9PT8ZPT0+MAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA//8AAP5/AAD8PwAA/D8AAPgfAAD4HwAA8A8AAOAPAADAAwAAwAMAAMADAADAAwAA4AcAAPAPAAD4HwAA/D8AAA==" rel="icon" type="image/x-icon" />
    <meta name="apple-mobile-web-app-title" content="CodePen">

    <link rel="shortcut icon" type="image/x-icon" href="https://cpwebassets.codepen.io/assets/favicon/favicon-aec34940fbc1a6e787974dcd360f2c6b63348d4b1f4e06c77743096d55480f33.ico">

    <link rel="mask-icon" type="image/x-icon" href="https://cpwebassets.codepen.io/assets/favicon/logo-pin-b4b4269c16397ad2f0f7a01bcdf513a1994f4c94b8af2f191c09eb0d601762b1.svg" color="#111">



    <title>TelegramBot</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<audio id="sound" class="d-none">
    <source src="{{ asset('sound.mp3') }}" type="audio/mp3">
</audio>
<script type="text/javascript">
    const APP_URL = {!! json_encode(url('/')) !!};

</script>
<body>
    <div id="app">
        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>





<script>
    function delay(time) {
        return new Promise(resolve => setTimeout(resolve, time));
    }

    delay(500).then(() => {
            $('.chats div .chatButton:first').trigger('click');
            const audio = $('#sound')[0]; // Get the audio element
        audio.play();
    });

</script>
</html>
