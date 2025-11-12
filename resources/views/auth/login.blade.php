@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Login</div>

                    <div class="card-body">
                        <div id="error-message" class="alert alert-danger d-none"></div>
                        <form id="loginForm">
                            <div class="row mb-3">
                                <label for="phone" class="col-md-4 col-form-label text-md-end">Phone</label>
                                <div class="col-md-6">
                                    <input id="phone" type="phone" class="form-control" name="phone" required autofocus>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password" class="col-md-4 col-form-label text-md-end">Password</label>
                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" name="password" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                        <label class="form-check-label" for="remember">Remember Me</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">Login</button>
                                </div>
                            </div>
                        </form>
                        <a href="https://tg.kuleshov.studio/telegram/login?redirect=https://ваш-сайт.ru/auth/telegram-callback"
                           style="display: inline-block; background-color: #0088cc; color: white; padding: 10px 20px;
          border-radius: 5px; text-decoration: none; font-family: Arial, sans-serif;">
                            <img src="https://telegram.org/img/t_logo.svg" alt="Telegram Logo"
                                 style="width: 24px; vertical-align: middle; margin-right: 10px;">
                            <span style="vertical-align: middle;">Войти через Telegram</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
    console.log('Submitting form...')
            const errorDiv = document.getElementById('error-message');
            errorDiv.classList.add('d-none');

            try {
                console.log('Submitting form...')
                const response = await axios.post('/api/login', {
                    phone: document.getElementById('phone').value,
                    password: document.getElementById('password').value,
                });
                console.log('Response:', response.data);

                if (response.data.token) {
                    const token = response.data.token;
                    // Store token in localStorage and cookies
                    localStorage.setItem('token', token);
                    document.cookie = `token=${token}; path=/`;
                    // Set default Axios Authorization header
                    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                    // Redirect to the home page
                    window.location.href = '/';
                }
            } catch (error) {
                console.error('Login error:', error);
                errorDiv.textContent = error.response?.data?.error || 'Login failed';
                errorDiv.classList.remove('d-none');
            }
        });

        axios.interceptors.request.use(config => {
            const token = localStorage.getItem('token');
            if (token) {
                config.headers.Authorization = `Bearer ${token}`;
            }
            return config;
        });

        axios.interceptors.response.use(
            response => response,
            error => {
                if (error.response?.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                }
                return Promise.reject(error);
            }
        );
    </script>
{{--    <script>--}}
{{--        document.getElementById('loginForm').addEventListener('submit', async function(e) {--}}
{{--            e.preventDefault();--}}

{{--            const errorDiv = document.getElementById('error-message');--}}
{{--            errorDiv.classList.add('d-none');--}}

{{--            try {--}}
{{--                const response = await axios.post('/login', {--}}
{{--                    phone: document.getElementById('phone').value,--}}
{{--                    password: document.getElementById('password').value,--}}
{{--                });--}}
{{--                console.log(response);--}}

{{--                if (response.data.token) {--}}
{{--                    localStorage.setItem('token', response.data.token);--}}

{{--                    if (response.data.token) {--}}
{{--                        const token = response.data.token;--}}

{{--                        // Store token in localStorage and cookies--}}
{{--                        localStorage.setItem('token', token);--}}
{{--                        document.cookie = `token=${token}; path=/`;--}}

{{--                        // Set default Axios Authorization header--}}
{{--                        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;--}}

{{--                        // Redirect to the home page--}}
{{--                        window.location.href = '/';--}}
{{--                    }--}}
{{--                }--}}
{{--            } catch (error) {--}}
{{--                errorDiv.textContent = error.response?.data?.error || 'Login failed';--}}
{{--                errorDiv.classList.remove('d-none');--}}
{{--            }--}}
{{--        });--}}

{{--        axios.interceptors.request.use(config => {--}}
{{--            const token = localStorage.getItem('token');--}}
{{--            if (token) {--}}
{{--                config.headers.Authorization = `Bearer ${token}`;--}}
{{--            }--}}
{{--            return config;--}}
{{--        });--}}

{{--        axios.interceptors.response.use(--}}
{{--            response => response,--}}
{{--            error => {--}}
{{--                if (error.response?.status === 401) {--}}
{{--                    localStorage.removeItem('token');--}}
{{--                    window.location.href = '/login';--}}
{{--                }--}}
{{--                return Promise.reject(error);--}}
{{--            }--}}
{{--        );--}}
{{--    </script>--}}
@endsection
