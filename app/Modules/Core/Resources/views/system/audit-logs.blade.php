@extends('core::layouts.app')

@section('title', 'گزارش عملیات سیستم')

@php($breadcrumbs = [['title' => 'گزارش عملیات سیستم']])

@section('content')
    <div class="card">
        <table class="table-basic">
            <thead>
            <tr>
                <th>زمان</th>
                <th>کاربر</th>
                <th>ماژول</th>
                <th>رویداد</th>
                <th>توضیح</th>
                <th>IP</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td dir="ltr">{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                    <td>{{ $log->user?->fullName() ?? 'سیستم' }}</td>
                    <td>{{ $log->module }}</td>
                    <td>{{ $log->event }}</td>
                    <td>{{ $log->description }}</td>
                    <td dir="ltr">{{ $log->ip_address }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-slate-400">موردی ثبت نشده است.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="mt-4">{{ $logs->links() }}</div>
    </div>
@endsection
