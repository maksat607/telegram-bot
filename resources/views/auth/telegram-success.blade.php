@extends('layouts.app')

@section('content')
    <div class="container text-center mt-5">
        <h3>Login successful!</h3>
        <p>Redirecting to home...</p>
    </div>

    <script>
        (function() {
            const token = @json($token);

            // ✅ same logic as your normal login
            localStorage.setItem('token', token);
            document.cookie = `token=${token}; path=/`;

            if (window.axios) {
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            }

            window.location.href = '/';
        })();
    </script>
@endsection
