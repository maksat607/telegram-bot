<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">


{{--    <link rel="apple-touch-icon" type="image/png" href="https://cpwebassets.codepen.io/assets/favicon/apple-touch-icon-5ae1a0698dcc2402e9712f7d01ed509a57814f994c660df9f7a952f3060705ee.png">--}}
{{--    <link href="data:image/x-icon;base64,AAABAAEAEBAAAAEAIABoBAAAFgAAACgAAAAQAAAAIAAAAAEAIAAAAAAAAAQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABAWFf9PT0/qAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAE9PT4jr6Of/3tzb/09PT4gAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAARFxb/6Obl/+De3f8RFxb/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABPT0+I7O7w/+jm5f/g3t3/r6yr/09PT4gAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADhAQ//Du7f/o5uX/4N7d/9nX1v8KDg3/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAEBYV//X29f/w7u3/6Obl/+De3f/Z19b/0M7N/w8VFP8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAICAgpfj6/P/19fX/8O7t/+jm5f/g3t3/2dfW/9DOzf/KxsX/29vbDgAAAAAAAAAAAAAAAAAAAAAAAAAAT09PjMTGxv/+/v7/9fX1/+rr6f8VGhn/CA0M/9nX1v/Qzs3/ysbF/woNDf9PT0+MAAAAAAAAAAAAAAAAAAAAAE9PT8b19/3//v7+//X19f8RFxb/rKan/6Sen/8SFxb/0M7N/8rGxf+XlZT/T09PxgAAAAAAAAAAAAAAAAAAAABPT0/GKR/h/yQa3v8lGdT/Hh8f/6ymp/+knp//DxgU/yYWo/8lFJr/MCJ5/09PT8YAAAAAAAAAAAAAAAAAAAAAT09PjCwh4/8kGt7/JhjU/zEkrv+4srP/SUhI/yMUrv8mFqP/JRSb/xISLv9PT0+MAAAAAAAAAAAAAAAAAAAAAAAAAAAQGBj/JRrY/yYZ1P8kGMr/JBe//yUWt/8lFq3/Jhaj/yUUmv8GBwe/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABEUGf8lGc3/JBjK/yQXv/8lFrf/JRat/yYWo/8QFhf/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAChUT/yIYy/8kF7//JRa3/ygZrP8OFhT/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABPT0+MT09Pxk9PT8ZPT0+MAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA//8AAP5/AAD8PwAA/D8AAPgfAAD4HwAA8A8AAOAPAADAAwAAwAMAAMADAADAAwAA4AcAAPAPAAD4HwAA/D8AAA==" rel="icon" type="image/x-icon" />--}}
{{--    <link href="data:image/x-icon;base64,AAABAAEAEBAAAAEAIABoBAAAFgAAACgAAAAQAAAAIAAAAAEAIAAAAAAAAAQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAsrKy/8vLy//EaRr/xGkb/8aHUP/Ly8v/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAzc3N/8luIP/JbiD/ym4g/8puIP/JbiD/yW4g/82skP/l5eUtAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA0tLS/89zJf/PdCb/z3Ml/89zJf/PcyX/z3Qm/89zJf/PcyX/0tXY/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAANR3KP/UeSv/1Hgq/9R4Kv+7kGr/1Hgq/9zc3P/UeSv/1Hgq/9R5K//Ly8v/AAAAAAAAAAAAAAAAAAAAAO7u7g3afjD/2n4w/9p+MP/afjD/pXNH/+Pj4//j4+P/xHUx/9p+MP/afjD/3Nzc/wAAAAAAAAAAAAAAAAAAAADn5+d/34M1/9yBNP/r6+v/6+vr/9W6pP/r6+v/6urq/+vs6//fgzX/34M1/+Li4/8AAAAAAAAAAAAAAAAAAAAA8vLyAuSIOv/kiDr/5Ig6/+GGOf/y8vL/8fHx//Hy8v/y8vL/5Ig6/+SIOv/o6Oj/AAAAAAAAAAAAAAAAAAAAAAAAAADqkUf/6o0//+mNP//pjT//6o0//+qNP//5+vv/+fn5/+mNP//qjT//29vb/wAAAAAAAAAAAAAAAAAAAAAAAAAA8fHx/++SRP/vkkT/75JE/++SRP/vkkT/75NF/++SRP/vkkT/9PX2/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAD39/f/9JdJ//SXSf/0mEr/9JhK//SXSf/0l0n/+fr7/+7u7iMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAObm5v/8/P3/+ZpL//mbTP/87eD//f39/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA//8AAP//AAD4HwAA8A8AAOAHAADgAwAA4AMAAOADAADgAwAA4AMAAOAHAADwDwAA+B8AAP//AAD//wAA//8AAA==" rel="icon" type="image/x-icon" />--}}
    <link href="data:image/x-icon;base64,AAABAAEAEBAAAAEAIABoBAAAFgAAACgAAAAQAAAAIAAAAAEAIAAAAAAAAAQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABAAAAAgAAAAMAAAADQAAAA0AAAAMAAAACAAAAAQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAGAAAAHBwRCkZEKBZ0WTQci2I5HpRiOR6UWTQci0QoFnQcEQpGAAAAHAAAAAYAAAAAAAAAAAAAAAAAAAAGJxcNTJ5dMdHIczr+yHI5/8hyOf/Icjn/yHI5/8hyOf/Icjn/yHM6/p5dMdEmFw1MAAAABgAAAAAAAAAAGhAJNL1vOvHJdDv/yHM7/8dzOv/IdDv/yXQ7/8l0O//JdDv/yXQ7/8l0O//JdDv/vW868RoQCTQAAAAAAAAABHpJKaLJdT3/yXU9/8h5Rf+8glv/s2s6/8JxO//Dcjv/x3Q9/8l1Pf/JdT3/yXU9/8l1Pf96SSmhAAAABAAAAAi1bTzjync//8l3P//EdD//8tbF/+/Uwv/JnYH/x5t9/7mBXP+zajn/xnU+/8l3P//Kdz//tW084wAAAAgLBwQUy3pE/sp4Qv/EdUD/wpFw//np3v//8ej///Ho///x6P//8ej/7dfJ/7uBW//HdkH/ynhC/8t6RP4MBwQUGxELJst7Rf/KekT/x45o//318P//9/P///fz///38///9/P///fz///38//57+j/woJY/8t6RP/Le0X/HBILJiUYDyzMfEf/y3tG/+fOvf///fz///38///9/P///fz///38///9/P///fz///38/+LFsf/Le0b/zHxH/ycZECwhFQ4ozH5J/8x9Sf/v283////////////////////////////////////////////v3M//y31J/8x+Sf8kFw8oGA8KGM2ATf/Nf0v/47me////////////////////////////////////////////58St/8x/S//NgE3/HBIMGAwIBQTFfU3wzYBN/86EUv/y39P/////////////////////////////////+O3m/9GKW//NgE3/xX1N8BIMCAQAAAAApmtEtc6CUP/OglD/z4VU/+S8ov/47uf///7+///////79vL/6su2/9GMXf/OglD/zoJQ/6dsRbUAAAAAAAAAAKNtSEfOhFP+zoRS/86EUv/OhFL/zoRS/9GLXP/SjmD/zoRT/86EUv/OhFL/zoRS/86EU/6rckxHAAAAAAAAAAAAAAAAzopcc8+GVvrPhVT/z4VU/8+FVP/PhVT/z4VU/8+FVP/PhVT/z4VU/8+GVvrOilxzAAAAAAAAAAAAAAAAAAAAAAAAAACrdE8g0IxeddCKXKfQilvC0YlazdGJWs3QilvC0Ipcp9CMXnWvd1EgAAAAAAAAAAAAAAAA//8AAPw/AADgBwAAwAMAAIABAACAAQAAgAEAAIABAACAAQAAgAEAAIABAACAAQAAgAEAAMADAADgBwAA+B8AAA==" rel="icon" type="image/x-icon" />
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
