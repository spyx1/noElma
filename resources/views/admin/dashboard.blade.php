@extends('layouts.app', ['title' => 'Рабочий кабинет'])

@section('content')
    @php
        $role = request('role', 'tester');
        $roles = ['chief' => 'Начальник ОТ', 'tester' => 'Тестировщик', 'manager' => 'Руководитель'];
        $shiftDays = [
            ['label' => '01 окт', 'date' => '1 октября', 'working' => true, 'tests' => 5, 'planned' => 8, 'test_time' => '4 ч 20 м', 'work_time' => '1 ч 40 м', 'remaining' => '5 ч 00 м', 'completion' => 55, 'pending' => 2],
            ['label' => '02 окт', 'date' => '2 октября', 'working' => true, 'tests' => 8, 'planned' => 9, 'test_time' => '6 ч 10 м', 'work_time' => '2 ч 00 м', 'remaining' => '2 ч 50 м', 'completion' => 74, 'pending' => 1],
            ['label' => '03 окт', 'date' => '3 октября', 'working' => false, 'tests' => 0, 'planned' => 0, 'test_time' => '—', 'work_time' => '—', 'remaining' => 'Выходной', 'completion' => 0, 'pending' => 1],
            ['label' => '04 окт', 'date' => '4 октября', 'working' => false, 'tests' => 0, 'planned' => 0, 'test_time' => '—', 'work_time' => '—', 'remaining' => 'Выходной', 'completion' => 0, 'pending' => 1],
            ['label' => '05 окт', 'date' => '5 октября', 'working' => true, 'tests' => 6, 'planned' => 8, 'test_time' => '4 ч 50 м', 'work_time' => '2 ч 40 м', 'remaining' => '3 ч 30 м', 'completion' => 68, 'pending' => 2],
            ['label' => '06 окт', 'date' => '6 октября', 'working' => true, 'tests' => 7, 'planned' => 9, 'test_time' => '5 ч 20 м', 'work_time' => '2 ч 10 м', 'remaining' => '3 ч 30 м', 'completion' => 68, 'pending' => 1, 'selected' => true],
            ['label' => '07 окт', 'date' => '7 октября', 'working' => false, 'tests' => 0, 'planned' => 0, 'test_time' => '—', 'work_time' => '—', 'remaining' => 'Выходной', 'completion' => 0, 'pending' => 1],
            ['label' => '08 окт', 'date' => '8 октября', 'working' => false, 'tests' => 0, 'planned' => 0, 'test_time' => '—', 'work_time' => '—', 'remaining' => 'Выходной', 'completion' => 0, 'pending' => 1],
            ['label' => '09 окт', 'date' => '9 октября', 'working' => true, 'tests' => 4, 'planned' => 7, 'test_time' => '3 ч 30 м', 'work_time' => '1 ч 50 м', 'remaining' => '5 ч 40 м', 'completion' => 48, 'pending' => 3],
            ['label' => '10 окт', 'date' => '10 октября', 'working' => true, 'tests' => 9, 'planned' => 9, 'test_time' => '6 ч 30 м', 'work_time' => '2 ч 30 м', 'remaining' => '2 ч 00 м', 'completion' => 82, 'pending' => 0],
        ];
        $activeShift = collect($shiftDays)->firstWhere('label', request('shift', '06 окт')) ?? $shiftDays[5];
        $adminUpdates = [
            ['icon' => 'layout-dashboard', 'title' => 'Дашборд тестировщика', 'text' => 'Добавлены ежедневные показатели, график активности, текущие задачи и тесты в работе.', 'date' => '06.10.2026'],
            ['icon' => 'table', 'title' => 'Рабочие таблицы', 'text' => 'Обновлён редактор полей: компактная таблица, единые чекбоксы и управление окном.', 'date' => '05.10.2026'],
            ['icon' => 'users', 'title' => 'Выбор пользователей', 'text' => 'В формах доступен поиск по сотрудникам для быстрого выбора автора или редактора.', 'date' => '04.10.2026'],
        ];
        $team = [
            ['name' => 'Лапетов С. А.', 'initials' => 'ЛС', 'state' => 'В смене', 'tests' => '7 / 9', 'works' => '3', 'time' => '7 ч 30 м'],
            ['name' => 'Ушакова В. А.', 'initials' => 'УВ', 'state' => 'В смене', 'tests' => '6 / 8', 'works' => '2', 'time' => '6 ч 50 м'],
            ['name' => 'Маресин И. А.', 'initials' => 'МИ', 'state' => 'В смене', 'tests' => '5 / 7', 'works' => '1', 'time' => '5 ч 40 м'],
            ['name' => 'Свиридов Е. В.', 'initials' => 'СЕ', 'state' => 'Выходной', 'tests' => '—', 'works' => '—', 'time' => '—'],
        ];
    @endphp

    <div class="workspace-dashboard">
        <div class="dashboard-topline">
            <div><p class="dashboard-eyebrow">Рабочий кабинет</p><h1 class="dashboard-title">Здравствуйте, {{ auth()->user()->displayName() }}</h1><p class="dashboard-subtitle">Демонстрационный вид показателей и текущей загрузки.</p></div>
            <nav class="dashboard-role-switch" aria-label="Выбор роли">@foreach ($roles as $key => $label)<a @class(['active' => $role === $key]) href="{{ route('workspace.dashboard', ['role' => $key, 'shift' => $activeShift['label']]) }}">{{ $label }}</a>@endforeach</nav>
        </div>

        @if ($role === 'chief')
            <section class="dashboard-chief-metrics" aria-label="Сводка по отделу">
                <article class="dashboard-metric"><span class="dashboard-metric-icon"><i class="ti ti-users-group"></i></span><div><div class="dashboard-metric-label">Сотрудников в смене</div><div class="dashboard-metric-value">3 / 4</div><div class="dashboard-metric-note">один сотрудник выходной</div></div></article>
                <article class="dashboard-metric"><span class="dashboard-metric-icon"><i class="ti ti-clipboard-check"></i></span><div><div class="dashboard-metric-label">Тестов выполнено</div><div class="dashboard-metric-value">18</div><div class="dashboard-metric-note">из 24 запланированных</div></div></article>
                <article class="dashboard-metric"><span class="dashboard-metric-icon"><i class="ti ti-briefcase"></i></span><div><div class="dashboard-metric-label">Доп. работ на проверке</div><div class="dashboard-metric-value">5</div><div class="dashboard-metric-note">требуют вашего решения</div></div></article>
                <article class="dashboard-metric"><span class="dashboard-metric-icon"><i class="ti ti-alert-triangle"></i></span><div><div class="dashboard-metric-label">Требуют внимания</div><div class="dashboard-metric-value">2</div><div class="dashboard-metric-note">задачи с истекшим сроком</div></div></article>
            </section>

            <section class="dashboard-grid">
                <article class="dashboard-panel"><div class="dashboard-panel-head"><div><h2>Сотрудники сегодня</h2><p>Нагрузка и прогресс тестировщиков за смену</p></div><a href="{{ route('workspace.testing') }}">Открыть тестирование</a></div>
                    <div class="reference-table-wrap"><table class="dashboard-staff-table"><thead><tr><th>Сотрудник</th><th>Статус</th><th>Тесты</th><th>Доп. работы</th><th>Время</th></tr></thead><tbody>@foreach ($team as $member)<tr><td><span class="dashboard-staff-person"><span class="dashboard-staff-avatar">{{ $member['initials'] }}</span>{{ $member['name'] }}</span></td><td><span @class(['dashboard-staff-state', 'rest' => $member['state'] === 'Выходной'])>{{ $member['state'] }}</span></td><td>{{ $member['tests'] }}</td><td>{{ $member['works'] }}</td><td>{{ $member['time'] }}</td></tr>@endforeach</tbody></table></div>
                </article>
                <article class="dashboard-shift-summary"><div><h2>Моя смена</h2><p>Продолжительность смены — 11 часов</p><div class="dashboard-pending-work"><strong>5 доп. работ</strong> ожидают вашей проверки.</div></div><div class="dashboard-shift-times"><div class="dashboard-shift-time"><span>Проверка работ</span><strong>3 ч 40 м</strong></div><div class="dashboard-shift-time"><span>Организация</span><strong>1 ч 50 м</strong></div><div class="dashboard-shift-time"><span>Осталось</span><strong>5 ч 30 м</strong></div></div><div class="dashboard-shift-progress"><div class="dashboard-shift-progress-copy"><span>Выполнено смены</span><strong>50%</strong></div><span class="dashboard-shift-progress-bar"><span style="width:50%"></span></span></div></article>
            </section>

            <section class="dashboard-grid">
                <article class="dashboard-panel"><div class="dashboard-panel-head"><div><h2>Мои текущие задачи</h2><p>Задачи начальника отдела тестирования</p></div><a href="{{ route('workspace.my-tasks') }}">Все задачи</a></div><div class="dashboard-task-list"><div class="dashboard-task"><i class="dashboard-priority overdue"></i><div><a href="{{ route('workspace.my-tasks') }}">Проверить и согласовать результаты ИПС60-700Т</a><small>Проверка дополнительной работы · приоритет 18</small></div><span class="dashboard-due">сегодня, 16:00</span></div><div class="dashboard-task"><i class="dashboard-priority"></i><div><a href="{{ route('workspace.my-tasks') }}">Распределить заявки на тестирование</a><small>Организационная задача · приоритет 5</small></div><span class="dashboard-due">сегодня, 17:30</span></div><div class="dashboard-task"><i class="dashboard-priority"></i><div><a href="{{ route('workspace.my-tasks') }}">Проверить загрузку сотрудников на завтра</a><small>Планирование смены</small></div><span class="dashboard-due">завтра, 09:00</span></div></div></article>
                <article class="dashboard-panel"><div class="dashboard-panel-head"><div><h2>Дополнительные работы</h2><p>Ожидают решения начальника</p></div><a href="{{ route('workspace.additional-work') }}">Открыть</a></div><div class="dashboard-task-list"><div class="dashboard-task"><i class="dashboard-priority"></i><div><a href="{{ route('workspace.additional-work') }}">Оформление результатов ИПС35-350Т</a><small>Лапетов С. А. · отправлено в 14:30</small></div><span class="dashboard-status">На проверке</span></div><div class="dashboard-task"><i class="dashboard-priority"></i><div><a href="{{ route('workspace.additional-work') }}">Сверка параметров образца</a><small>Ушакова В. А. · отправлено в 13:45</small></div><span class="dashboard-status">На проверке</span></div><div class="dashboard-task"><i class="dashboard-priority"></i><div><a href="{{ route('workspace.additional-work') }}">Подготовка протокола ИПС210-1050ТУ</a><small>Маресин И. А. · отправлено в 12:20</small></div><span class="dashboard-status">На проверке</span></div></div></article>
            </section>
            <section class="dashboard-updates">
                <div class="dashboard-panel-head"><div><h2>Новые возможности</h2><p>Что нового в системе</p></div><span class="dashboard-status">Демо</span></div>
                <div class="dashboard-update-list">@foreach ($adminUpdates as $update)<article class="dashboard-update"><time class="dashboard-update-date">{{ $update['date'] }}</time><span class="dashboard-update-icon"><i class="ti ti-{{ $update['icon'] }}"></i></span><div><div class="dashboard-update-title">{{ $update['title'] }}</div><div class="dashboard-update-text">{{ $update['text'] }}</div></div></article>@endforeach</div>
            </section>
        @elseif ($role !== 'tester')
            <section class="dashboard-empty"><div><i class="ti ti-layout-dashboard"></i><strong>Дашборд «{{ $roles[$role] ?? 'Роль' }}» готовится</strong><br><span>Сейчас доступен демонстрационный дашборд тестировщика.</span></div></section>
        @else
            <section class="dashboard-metrics" aria-label="Показатели за день">
                <article class="dashboard-shift-summary"><div><h2>Смена · {{ $activeShift['date'] }}</h2><p>{{ $activeShift['working'] ? 'Продолжительность смены — 11 часов' : 'Сегодня по графику выходной' }}</p><div class="dashboard-pending-work"><strong>{{ $activeShift['pending'] }} {{ $activeShift['pending'] === 1 ? 'доп. работа' : 'доп. работы' }}</strong> ожидает проверки.</div></div><div class="dashboard-shift-times"><div class="dashboard-shift-time"><span>Тесты</span><strong>{{ $activeShift['test_time'] }}</strong></div><div class="dashboard-shift-time"><span>Доп. работы</span><strong>{{ $activeShift['work_time'] }}</strong></div><div class="dashboard-shift-time"><span>Осталось</span><strong>{{ $activeShift['remaining'] }}</strong></div></div><div class="dashboard-shift-progress"><div class="dashboard-shift-progress-copy"><span>Выполнено смены</span><strong>{{ $activeShift['completion'] }}%</strong></div><span class="dashboard-shift-progress-bar"><span style="width:{{ $activeShift['completion'] }}%"></span></span></div></article>
            </section>

            <section class="dashboard-shift-panel">
                <div class="dashboard-panel-head"><div><h2>График смен</h2><p>График 2/2 · рабочая смена 11 часов</p></div><span class="dashboard-status is-progress">Сегодня — смена</span></div>
                <div class="dashboard-shift-calendar" aria-label="Календарь смен">
                    @foreach ($shiftDays as $day)
                        <a @class(['dashboard-shift-day', 'is-working' => $day['working'], 'is-selected' => $day['label'] === $activeShift['label']]) href="{{ route('workspace.dashboard', ['role' => 'tester', 'shift' => $day['label']]) }}" aria-label="Выбрать {{ $day['date'] }}"><time>{{ $day['label'] }}</time><strong>{{ $day['working'] ? '11 ч' : 'Выходной' }}</strong></a>
                    @endforeach
                </div>
            </section>

            <section class="dashboard-chart-grid">
                <article class="dashboard-panel">
                    <div class="dashboard-panel-head"><div><h2>Результат по дням</h2><p>Выполнение плана за период</p></div><span class="dashboard-legend"><span><i></i>Результат</span></span></div>
                    <svg class="dashboard-chart" viewBox="0 0 660 206" role="img" aria-label="График результата по дням">
                        <line class="grid-line" x1="48" y1="20" x2="640" y2="20"/><line class="grid-line" x1="48" y1="65" x2="640" y2="65"/><line class="grid-line" x1="48" y1="110" x2="640" y2="110"/><line class="grid-line" x1="48" y1="155" x2="640" y2="155"/><text x="7" y="24">120%</text><text x="15" y="69">80%</text><text x="15" y="114">40%</text><text x="22" y="159">0%</text>
                        <path class="secondary-line" d="M48 88 L640 88"/><path class="line" d="M48 115 L132 100 L216 108 L300 82 L384 96 L468 57 L552 99 L636 76"/>
                        <circle class="point" cx="48" cy="115" r="4"/><circle class="point" cx="132" cy="100" r="4"/><circle class="point" cx="216" cy="108" r="4"/><circle class="point" cx="300" cy="82" r="4"/><circle class="point" cx="384" cy="96" r="4"/><circle class="point" cx="468" cy="57" r="4"/><circle class="point" cx="552" cy="99" r="4"/><circle class="point" cx="636" cy="76" r="4"/>
                        <text x="35" y="192">30.09</text><text x="119" y="192">01.10</text><text x="203" y="192">02.10</text><text x="287" y="192">03.10</text><text x="371" y="192">04.10</text><text x="455" y="192">05.10</text><text x="539" y="192">06.10</text><text x="623" y="192">07.10</text>
                    </svg>
                </article>
                <article class="dashboard-panel">
                    <div class="dashboard-panel-head"><div><h2>Норма / факт по дням</h2><p>Учёт времени за смены</p></div><span class="dashboard-legend"><span><i></i>Норма</span><span><i class="secondary"></i>Факт</span></span></div>
                    <svg class="dashboard-chart" viewBox="0 0 660 206" role="img" aria-label="График нормы и факта по дням">
                        <line class="grid-line" x1="45" y1="20" x2="640" y2="20"/><line class="grid-line" x1="45" y1="65" x2="640" y2="65"/><line class="grid-line" x1="45" y1="110" x2="640" y2="110"/><line class="grid-line" x1="45" y1="155" x2="640" y2="155"/><text x="8" y="24">11 ч</text><text x="14" y="69">8 ч</text><text x="14" y="114">5 ч</text><text x="14" y="159">2 ч</text>
                        <rect class="bar-norm" x="70" y="70" width="18" height="85" rx="4"/><rect class="bar-fact" x="92" y="82" width="18" height="73" rx="4"/><rect class="bar-norm" x="145" y="70" width="18" height="85" rx="4"/><rect class="bar-fact" x="167" y="64" width="18" height="91" rx="4"/><rect class="bar-norm" x="220" y="70" width="18" height="85" rx="4"/><rect class="bar-fact" x="242" y="95" width="18" height="60" rx="4"/><rect class="bar-norm" x="295" y="70" width="18" height="85" rx="4"/><rect class="bar-fact" x="317" y="78" width="18" height="77" rx="4"/><rect class="bar-norm" x="370" y="70" width="18" height="85" rx="4"/><rect class="bar-fact" x="392" y="73" width="18" height="82" rx="4"/><rect class="bar-norm" x="445" y="70" width="18" height="85" rx="4"/><rect class="bar-fact" x="467" y="87" width="18" height="68" rx="4"/><rect class="bar-norm" x="520" y="70" width="18" height="85" rx="4"/><rect class="bar-fact" x="542" y="60" width="18" height="95" rx="4"/>
                        <text x="73" y="192">01.10</text><text x="148" y="192">02.10</text><text x="223" y="192">03.10</text><text x="298" y="192">04.10</text><text x="373" y="192">05.10</text><text x="448" y="192">06.10</text><text x="523" y="192">07.10</text>
                    </svg>
                </article>
            </section>

            <section class="dashboard-grid dashboard-grid--single">
                <article class="dashboard-panel">
                    <div class="dashboard-panel-head"><div><h2>Тесты в работе</h2><p>Выполняются сейчас</p></div><a href="{{ route('workspace.testing') }}">Все тесты</a></div>
                    <div class="dashboard-test-list">
                        <div class="dashboard-test"><span class="dashboard-status is-progress">В работе</span><div><a href="{{ route('workspace.testing') }}">№1277 Пробник 24-0862/5436 ИПС35-350Т</a><small>Пробник ИПС · начат в 10:24</small></div><div class="dashboard-progress"><span>12 / 16</span><span class="dashboard-progress-bar"><span style="width:75%"></span></span></div></div>
                        <div class="dashboard-test"><span class="dashboard-status is-progress">В работе</span><div><a href="{{ route('workspace.testing') }}">№1289 Пробник 24-0894/5442 ИПС60-700Т</a><small>Пробник ИПС · начат в 11:10</small></div><div class="dashboard-progress"><span>8 / 12</span><span class="dashboard-progress-bar"><span style="width:67%"></span></span></div></div>
                        <div class="dashboard-test"><span class="dashboard-status">Ожидает</span><div><a href="{{ route('workspace.testing') }}">№1295 Пробник 24-0911/4326 ИПС40-390Т</a><small>Назначен на сегодня</small></div><div class="dashboard-progress"><span>0 / 10</span><span class="dashboard-progress-bar"><span style="width:0%"></span></span></div></div>
                    </div>
                </article>
            </section>

            <section class="dashboard-grid">
                <article class="dashboard-panel"><div class="dashboard-panel-head"><div><h2>Мои текущие задачи</h2><p>Назначены на вас и требуют внимания</p></div><a href="{{ route('workspace.my-tasks') }}">Все задачи</a></div><div class="dashboard-task-list">
                    <div class="dashboard-task"><i class="dashboard-priority"></i><div><a href="{{ route('workspace.my-tasks') }}">Взять №2741 ИПС60-700Т IP67 3325-5444 в работу</a><small>Взятие протокола в работу · приоритет 3</small></div><span class="dashboard-due">сегодня, 15:12</span></div>
                    <div class="dashboard-task"><i class="dashboard-priority"></i><div><a href="{{ route('workspace.my-tasks') }}">Взять №2746 ИПС210-1050ТУ IP67 5602-7381 в работу</a><small>Взятие протокола в работу · приоритет 8</small></div><span class="dashboard-due">сегодня, 15:05</span></div>
                    <div class="dashboard-task"><i class="dashboard-priority overdue"></i><div><a href="{{ route('workspace.my-tasks') }}">Проверить результаты испытаний ИПС24-700ТД</a><small>Дополнительная работа · приоритет 22</small></div><span class="dashboard-due">вчера, 16:10</span></div>
                </div></article>
                <article class="dashboard-panel"><div class="dashboard-panel-head"><div><h2>Дополнительные работы</h2><p>Статус за сегодня</p></div><div class="dashboard-panel-actions"><a href="{{ route('workspace.additional-work') }}">Открыть</a><a class="button" href="{{ route('workspace.additional-work') }}"><i class="ti ti-plus"></i> Добавить</a></div></div><div class="dashboard-task-list">
                    <div class="dashboard-task"><i class="dashboard-priority"></i><div><a href="{{ route('workspace.additional-work') }}">Подготовка протокола ИПС60-700Т</a><small>Записано в 09:40</small></div><span class="dashboard-status is-progress">Проверено</span></div>
                    <div class="dashboard-task"><i class="dashboard-priority"></i><div><a href="{{ route('workspace.additional-work') }}">Сверка параметров образца</a><small>Записано в 12:15</small></div><span class="dashboard-status is-progress">Проверено</span></div>
                    <div class="dashboard-task"><i class="dashboard-priority"></i><div><a href="{{ route('workspace.additional-work') }}">Оформление результатов</a><small>Записано в 14:30</small></div><span class="dashboard-status">На проверке</span></div>
                </div></article>
            </section>

            <section class="dashboard-updates">
                <div class="dashboard-panel-head"><div><h2>Новые возможности</h2><p>Что нового в системе</p></div><span class="dashboard-status">Демо</span></div>
                <div class="dashboard-update-list">
                    @foreach ($adminUpdates as $update)
                        <article class="dashboard-update"><time class="dashboard-update-date">{{ $update['date'] }}</time><span class="dashboard-update-icon"><i class="ti ti-{{ $update['icon'] }}"></i></span><div><div class="dashboard-update-title">{{ $update['title'] }}</div><div class="dashboard-update-text">{{ $update['text'] }}</div></div></article>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
