@extends('layouts.app')

@section('title', 'BlogPost — Buat Postingan Baru')

@section('content')
    <div class="px-4 md:px-0">
        <div class="mb-4 flex items-center justify-between">
            <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">
                Buat Postingan Baru
            </h1>
            <a href="{{ route('posts.index') }}" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:underline">
                &larr; Kembali
            </a>
        </div>

        <x-bladewind.card class="rounded-xl border border-gray-200/80 dark:border-dark-700 shadow-xs p-4 md:p-6">
            @include('posts.form')
        </x-bladewind.card>
    </div>
@endsection
