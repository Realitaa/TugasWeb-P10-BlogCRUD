@extends('layouts.app')

@section('title', $post->title . ' — BlogPost')

@section('content')
    <div class="px-4 md:px-0">
        <div class="mb-4">
            <a href="{{ route('posts.index') }}" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:underline">
                &larr; Kembali ke Beranda
            </a>
        </div>

        <article class="w-full">
            <div class="bg-white dark:bg-dark-800 p-4 md:p-6 rounded-xl border border-gray-200/80 dark:border-dark-700 shadow-xs mb-6">
                <!-- Post Header -->
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-2">
                            <x-bladewind.avatar image="https://github.com/Realitaa.png" size="small" show_ring="true" />
                            <div>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-dark-100">Realitaa</span>
                                <time class="text-xs text-gray-500 dark:text-dark-400" datetime="{{ $post->created_at->toIso8601String() }}">
                                    {{ $post->created_at->translatedFormat('d F Y, H:i') }} ({{ $post->created_at->diffForHumans() }})
                                </time>
                            </div>
                        </div>
                        <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white leading-snug">
                            {{ $post->title }}
                        </h1>
                    </div>

                    <!-- Dropdown Options -->
                    <div class="shrink-0">
                        <x-bladewind.dropmenu>
                            <x-bladewind.dropmenu.item icon="pencil-square" onclick="location.href='{{ route('posts.edit', $post) }}'">
                                Edit Post
                            </x-bladewind.dropmenu.item>
                            <x-bladewind.dropmenu.item icon="trash" class="text-red-600! hover:bg-red-50! dark:hover:bg-red-950/30!" onclick="if(confirm('Yakin ingin menghapus postingan ini?')) document.getElementById('delete-form-show-{{ $post->id }}').submit();">
                                Hapus Post
                            </x-bladewind.dropmenu.item>
                        </x-bladewind.dropmenu>
                        <form id="delete-form-show-{{ $post->id }}" action="{{ route('posts.destroy', $post) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>

                <!-- Post Body -->
                <div class="text-gray-800 dark:text-dark-200 text-base leading-relaxed whitespace-pre-line mt-4">
                    {!! nl2br(e($post->body)) !!}
                </div>

                <!-- Media Gallery / Carousel -->
                @if ($post->media->isNotEmpty())
                    <div class="mt-6 overflow-hidden rounded-lg">
                        <x-bladewind.carousel loop="false" arrows="false" indicators="false" class="w-full">
                            @foreach ($post->media as $media)
                                <x-bladewind.carousel.slide>
                                    <img src="{{ $media->url }}" alt="{{ $media->file_name }}" class="w-full h-72 sm:h-96 object-cover rounded-lg" loading="lazy" />
                                </x-bladewind.carousel.slide>
                            @endforeach
                        </x-bladewind.carousel>
                    </div>
                @endif
            </div>
        </article>
    </div>
@endsection
