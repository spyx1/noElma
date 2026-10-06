@extends('layouts.app', ['title' => 'Вход'])

@push('styles')
<style>
    .auth-login { width: min(calc(100% - 32px), 440px); margin: 32px auto; overflow: hidden; border: 1px solid #254b63; border-radius: 14px; background: #18324b; box-shadow: 0 18px 50px rgba(24,50,75,.20); }
    .auth-login__form-side { display: flex; flex-direction: column; min-height: 520px; padding: 42px; }
    .auth-login__brand { display: inline-flex; align-items: center; font-size: 20px; font-weight: 800; letter-spacing: -.055em; line-height: 1; }
    .auth-login__brand-no { color: #f3bd5b; }
    .auth-login__brand-elma { color: #fff; }
    .auth-login__title { margin: 54px 0 7px; color: #fff; font-size: 26px; line-height: 1.15; }
    .auth-login__subtitle { margin: 0 0 28px; color: #c2d7dd; font-size: 14px; line-height: 1.55; }
    .auth-login__form { display: grid; gap: 16px; }
    .auth-login__form label { display: grid; gap: 6px; color: #d9eced; font-size: 13px; font-weight: 700; }
    .auth-login__form input[type="email"], .auth-login__form input[type="password"] { min-height: 42px; margin: 0; padding: 9px 11px; border-color: #c7d8e4 !important; border-radius: 7px; font-size: 14px; font-weight: 400; }
    .auth-login__form input:focus { border-color: #356d98 !important; outline: 2px solid rgba(53,109,152,.14); }
    .auth-login__error { margin: -6px 0 0; color: #b6382e; font-size: 12px; }
    .auth-login__options { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-top: -2px; }
    .auth-login__remember { display: inline-flex !important; grid-template-columns: none !important; align-items: center; gap: 7px !important; color: #c2d7dd !important; font-size: 12px !important; font-weight: 600 !important; }
    .auth-login__remember input { appearance: none; display: grid; width: 15px; min-width: 15px; height: 15px; margin: 0; place-content: center; border: 1px solid #a9bbc7; border-radius: 3px; background: #fff; }
    .auth-login__remember input::after { width: 7px; height: 4px; border: solid #fff; border-width: 0 0 1.5px 1.5px; content: ''; transform: rotate(-45deg) scale(0); }
    .auth-login__remember input:checked { border-color: #0f766e; background: #0f766e; }
    .auth-login__remember input:checked::after { transform: rotate(-45deg) scale(1); }
    .auth-login__link { border: 0; background: transparent; color: #a9dfd5; font: inherit; font-size: 12px; font-weight: 600; cursor: pointer; }
    .auth-login__link:hover { color: #fff; text-decoration: underline; }
    .auth-login__submit { min-height: 42px; margin-top: 4px; border-color: #0f766e !important; border-radius: 7px; background: #0f766e !important; color: #fff; font-size: 14px; font-weight: 700; }
    .auth-login__submit:hover { background: #0b5d57 !important; }
    .auth-login__register { margin-top: auto; padding-top: 30px; color: #a9c1ca; font-size: 12px; text-align: center; }
    .auth-login__register .auth-login__link { margin-left: 3px; }
    .auth-login__aside { display: none; }
    .auth-login__aside-icon { display: grid; place-items: center; width: 52px; height: 52px; margin-bottom: auto; border: 1px solid rgba(255,255,255,.16); border-radius: 12px; background: rgba(255,255,255,.09); color: #b9e3db; }
    .auth-login__aside-icon .ti { font-size: 27px; }
    .auth-login__aside h2 { margin: 0 0 10px; font-size: 25px; line-height: 1.2; }
    .auth-login__aside p { max-width: 330px; margin: 0; color: #c2d7dd; font-size: 14px; line-height: 1.6; }
    .auth-login__aside-note { display: inline-flex; align-items: center; gap: 6px; margin-top: 23px; color: #9fc5c7; font-size: 12px; }
    @media (max-width: 720px) { .auth-login { min-height: 0; margin: 48px auto; } .auth-login__form-side { min-height: 520px; padding: 30px; } .auth-login__title { margin-top: 42px; } }
    @media (max-width: 430px) { .auth-login { margin: 20px auto; border-radius: 10px; } .auth-login__form-side { padding: 24px; } .auth-login__options { align-items: flex-start; flex-direction: column; } }
</style>
@endpush

@section('content')
    <div class="auth-login">
        <section class="auth-login__form-side" aria-labelledby="login-title">
            <div class="auth-login__brand"><span class="auth-login__brand-no">no</span><span class="auth-login__brand-elma">Elma</span></div>
            <h1 class="auth-login__title" id="login-title">С возвращением</h1>
            <p class="auth-login__subtitle">Войдите в рабочее пространство, чтобы продолжить работу с процессами, задачами и данными компании.</p>

            <form class="auth-login__form" method="POST" action="{{ route('login.store') }}">
                @csrf
                <label>Email<input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="name@company.ru"></label>
                <label>Пароль<input type="password" name="password" required autocomplete="current-password" placeholder="Введите пароль"></label>
                @error('email') <p class="auth-login__error">{{ $message }}</p> @enderror
                <div class="auth-login__options"><label class="auth-login__remember"><input type="checkbox" name="remember" value="1">Запомнить меня</label><button class="auth-login__link" type="button">Забыли пароль?</button></div>
                <button class="auth-login__submit" type="submit">Войти</button>
            </form>

            <div class="auth-login__register">Нет учётной записи?<button class="auth-login__link" type="button">Зарегистрироваться</button></div>
        </section>
        <aside class="auth-login__aside"><span class="auth-login__aside-icon"><i class="ti ti-layout-dashboard"></i></span><div><h2>Всё для отдела тестирования</h2><p>Тесты, таблицы, задачи и загрузка команды — в едином рабочем пространстве.</p><span class="auth-login__aside-note"><i class="ti ti-shield-check"></i>Защищённый доступ</span></div></aside>
    </div>
@endsection
