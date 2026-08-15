@extends('core::layouts.app')

@section('title', 'تاریخچه ورود')

@php($breadcrumbs = [['title' => 'تاریخچه ورود']])

@section('content')
    <div class="card">
        <table class="table-basic">
            <thead>
            <tr>
                <th>زمان ورود</th>
                <th>زمان خروج</th>
                <th>نشانی IP</th>
                <th>سیستم/مرورگر</th>
                <th>وضعیت</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($histories as $history)
                <tr>
                    <td dir="ltr">{{ $history->logged_in_at?->format('Y-m-d H:i') ?? '—' }}</td>
                    <td dir="ltr">{{ $history->logged_out_at?->format('Y-m-d H:i') ?? '—' }}</td>
                    <td dir="ltr">{{ $history->ip_address }}</td>
                    <td>{{ $history->platform }} / {{ $history->browser }}</td>
                    <td>
                        @if ($history->successful)
                            <span class="text-emerald-600">موفق</span>
                        @else
                            <span class="text-rose-600">ناموفق ({{ $history->failure_reason }})</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-6 text-center text-slate-400">موردی ثبت نشده است.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="mt-4">{{ $histories->links() }}</div>
    </div>
@endsection
