@extends('layouts.app')

@section('content')

    <section class="mainApp">
        @include('left')
        @include('right')
{{--        @include('right')--}}


    </section>

    <!-- ---------------------- -->
    <!-- MENU AND OVERLAY STUFF -->
    <!-- ---------------------- -->

    <!-- MENU -->
    @include('menu')

    <!-- CONVERSATION OPTIONS MENU -->
    @include('options')



    <!-- PROFILE OPTIONS OVERLAY -->
   @include('profile')

    <!-- DARK FRAME OVERLAY -->
    <section class="overlay"></section>

    <!-- -------------------------------- -->
    <!-- SPECIFIC FOR CONNECTION WARNINGS -->
    <!-- -------------------------------- -->
    <div class="alerts">
        Trying to reconnect...
    </div>
{{--    <script src="https://cpwebassets.codepen.io/assets/common/stopExecutionOnTimeout-2c7831bb44f98c1391d6a4ffda0e1fd302503391ca806e7fcc7b9b87197aec26.js"></script>--}}

{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>--}}


@endsection
