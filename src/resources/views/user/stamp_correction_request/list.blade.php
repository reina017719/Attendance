@php
    $layout = Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.user';
@endphp
@extends($layout)

@section('css')
<link rel="stylesheet" href="{{ asset('css/request_list.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="content__inner">
        <h2>申請一覧</h2>
        <div class="items">
            <div class="items__inner">
                <label class="item-label active" data-tab="pending">承認待ち</label>
                <label class="item-label" data-tab="approved">承認済み</label>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日時</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody class="tab-content active" id="tab-pending">
                @foreach ($requests->where('status', 'pending') as $request)
                <tr>
                    <td><strong>承認待ち</strong></td>
                    <td>{{ $request->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($request->attendance->work_date)->format('Y/m/d') }}</td>
                    <td>{{ $request->reason }}</td>
                    <td>{{ \Carbon\Carbon::parse($request->created_at)->format('Y/m/d') }}</td>
                    <td>
                        @if (Auth::user()->role === 'admin')
                            <a href="{{ route('stamp_correction_request.approve', ['attendance_correction_request' => $request->id]) }}">詳細</a>
                        @else
                            <a href="{{ route('attendance.show', ['id' => $request->attendance->id]) }}">詳細</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tbody class="tab-content" id="tab-approved">
            @foreach ($requests->where('status', 'approved') as $request)
                <tr>
                    <td><strong>承認済み</strong></td>
                    <td>{{ $request->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($request->attendance->work_date)->format('Y/m/d') }}</td>
                    <td>{{ $request->reason }}</td>
                    <td>{{ \Carbon\Carbon::parse($request->created_at)->format('Y/m/d') }}</td>
                    <td>
                        @if (Auth::user()->role === 'admin')
                            <a href="{{ route('stamp_correction_request.approve', ['attendance_correction_request' => $request->id]) }}">詳細</a>
                        @else
                            <a href="{{ route('attendance.show', ['id' => $request->attendance->id]) }}">詳細</a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    document.querySelectorAll('.item-label').forEach(label => {
        label.addEventListener('click', function() {
            document.querySelectorAll('.item-label').forEach(l => l.classList.remove('active'));
            this.classList.add('active');

            const tab = this.getAttribute('data-tab');
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });

            const target = document.getElementById(`tab-${tab}`);
            if (target) {
                target.classList.add('active');
            }
        });
    });
</script>
@endsection

