@extends('layouts.user')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="content__inner">
        <div class="status" id="status">勤務外</div>
        <div class="date" id="date"></div>
        <div class="time" id="time"></div>
        <div class="buttons" id="buttons">
            <button class="button" id="clockInBtn">出勤</button>
        </div>
    </div>
</div>

<script>
    const hasAttendanceToday = JSON.parse(@json($hasAttendanceToday));
</script>
<script>
    function updateClock() {
        const now = new Date();
        const days = ['日', '月', '火', '水', '木', '金', '土'];
        const year = now.getFullYear();
        const month = now.getMonth() + 1;
        const date = now.getDate();
        const day = days[now.getDay()];
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        document.getElementById('date').textContent = `${year}年${month}月${date}日(${day})`;
        document.getElementById('time').textContent = `${hours}:${minutes}`;
        }

    document.addEventListener('DOMContentLoaded', function () {
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        if (tokenMeta) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = tokenMeta.getAttribute('content');
        } else {
        console.warn('CSRFトークンが見つかりませんでした');
        }

        updateClock();
        setInterval(updateClock, 1000);

        const clockInBtn = document.getElementById('clockInBtn');
        const status = document.getElementById('status');
        const buttons = document.getElementById('buttons');

        if (hasAttendanceToday) {
            status.textContent = '勤務外';
            buttons.innerHTML = `<p style="font-size: 18px; color: #333;"><strong>本日の勤怠は登録済みです。</strong></p>`;
            return;
        }

        clockInBtn.addEventListener('click', async function () {
            try {
                await axios.post('/attendance/clock-in');
                status.textContent = '勤務中';

            buttons.innerHTML = `
                <button class="button" id="clockOutBtn">退勤</button>
                <button class="button white-button" id="breakBtn">休憩入</button>
            `;
            setBreakMode();
            setClockOut();
            } catch (error) {
                alert('出勤処理に失敗しました。');
            }
        });

        function setBreakMode() {
            const breakBtn = document.getElementById('breakBtn');
            const status = document.getElementById('status');
            const buttons = document.querySelector('.buttons');

            breakBtn?.addEventListener('click', async function () {
                try {
                    await axios.post('/attendance/break-start');
                    status.textContent = '休憩中';

                    buttons.innerHTML = `
                        <button class="button white-button" id="returnBtn">休憩戻</button>
                    `;

                    const returnBtn = document.getElementById('returnBtn');
                    returnBtn.addEventListener('click', async function () {
                        try {
                            await axios.post('/attendance/break-end');
                            status.textContent = '勤務中';

                            buttons.innerHTML = `
                                <button class="button" id="clockOutBtn">退勤</button>
                                <button class="button white-button" id="breakBtn">休憩入</button>
                            `;
                            setBreakMode();
                            setClockOut();
                        } catch (error) {
                            alert('休憩戻りに失敗しました。');
                        }
                    });
                } catch (error) {
                    alert('休憩入りに失敗しました。');
                }
            });
        }

        function setClockOut() {
            const clockOutBtn = document.getElementById('clockOutBtn');
            const status = document.getElementById('status');
            const buttons = document.querySelector('.buttons');

            clockOutBtn?.addEventListener('click', async function () {
                try {
                    await axios.post('/attendance/clock-out');
                    status.textContent = '退勤済';

                    buttons.innerHTML = `<p style="font-size: 18px; color: #333;"><strong>お疲れ様でした。</strong></p>`;
                } catch (error) {
                    alert('退勤に失敗しました。');
                }
            });
        }
    });
</script>
@endsection