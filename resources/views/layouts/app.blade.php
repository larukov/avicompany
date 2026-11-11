<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="@vasset('css/app.css')">
    @yield('css')
</head>

<body class="h-100">
    @include('layouts.header')

    @guest
        @include('layouts.modal')
    @endguest

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">

        <div id="successToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true"
            data-bs-delay="3000">
            <div class="toast-header bg-success text-white">
                <strong class="me-auto">✅ Успешно</strong>
                <small>только что</small>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage">
                Операция выполнена успешно.
            </div>
        </div>

        <div id="errorToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true"
            data-bs-delay="5000">
            <div class="toast-header bg-danger text-white">
                <strong class="me-auto">❌ Ошибка</strong>
                <small>только что</small>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastErrorMessage">
                Произошла ошибка.
            </div>
        </div>

    </div>

    <main class="flex-grow-1">
        @yield('content')
    </main>
    @include('layouts.footer')

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="@vasset('js/search.js')"></script>
    <script src="@vasset('js/sidebar.js')"></script>
    @yield('script')
    @guest
        <script src="@vasset('js/modal.js')"></script>
    @endguest
    @stack('modals')
    <script src="@vasset('js/jquery-3.3.3.js')"></script>
    <script src="@vasset('js/jquery.maskedinput.min.js')"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if (session('toast_success'))
                showToast('success', "{{ session('toast_success') }}");
            @endif

                @if ($errors->hasBag('login'))
                    const loginModal = new bootstrap.Modal(document.getElementById('AuthorizationModal'));
                    loginModal.show();
                @elseif ($errors->any())
                const regModal = new bootstrap.Modal(document.getElementById('RegModal'));
                regModal.show();
            @endif
        });

        function openModal(modalId) {
            const modalEl = document.getElementById(modalId);
            if (modalEl && typeof bootstrap !== 'undefined') {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        }

        function showToast(type, message) {
            if (typeof bootstrap === 'undefined') return;

            if (type === 'success') {
                document.getElementById('toastMessage').innerHTML = message;
                const toast = new bootstrap.Toast(document.getElementById('successToast'));
                toast.show();
            }
        }
    </script>
    <script>
        $(function() {
            $("#phone").mask("+7 (999) 999-99-99");

            $('[data-mask]').each(function() {
                var mask = $(this).attr('data-mask');
                if (mask) {
                    $(this).mask(mask);
                }
            });
        });
    </script>
</body>

</html>