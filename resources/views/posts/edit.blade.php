@extends('layouts.app')

@section('title', 'BlogPost — Edit Postingan')

@section('content')
    <div class="px-4 md:px-0">
        <div class="mb-4 flex items-center justify-between">
            <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">
                Edit Postingan
            </h1>
            <a href="{{ route('posts.index') }}" class="inline-flex items-center text-sm font-medium text-primary-600 dark:text-primary-400 hover:underline">
                <x-bladewind.icon name="arrow-left" class="size-4 mr-1 stroke-2 inline-block" /> Kembali
            </a>
        </div>

        <x-bladewind.card class="rounded-xl border border-gray-200/80 dark:border-dark-700 shadow-xs p-4 md:p-6">
            @include('posts.form', ['post' => $post])
        </x-bladewind.card>
    </div>
@endsection
