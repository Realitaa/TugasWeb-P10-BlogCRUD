@extends('layouts.app')

@section('title', 'BlogPost — Beranda')

@section('content')
    <!-- Top Create Post Banner -->
    <div class="px-4 md:px-0 mb-4 md:mb-6">
        <div
            class="bg-white dark:bg-dark-800 rounded-xl border border-gray-200/80 dark:border-dark-700 shadow-xs p-4 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <x-bladewind.avatar image="https://github.com/Realitaa.png" size="small" show_ring="true" />
                <a href="{{ route('posts.create') }}"
                    class="text-sm md:text-base text-gray-500 dark:text-dark-400 hover:text-gray-700 dark:hover:text-dark-200 transition truncate cursor-pointer select-none">
                    Apa yang Anda pikirkan hari ini?
                </a>
            </div>
            <a href="{{ route('posts.create') }}">
                <x-bladewind.button size="small" icon="plus" uppercasing="false" class="shrink-0 font-medium">
                    Posting
                </x-bladewind.button>
            </a>
        </div>
    </div>

    <!-- Posts Feed -->
    @forelse ($posts as $post)
        <article class="w-full">
            <div
                class="bg-white dark:bg-dark-800 p-4 md:p-6 md:rounded-xl md:border md:border-gray-200/80 md:dark:border-dark-700 md:shadow-xs md:mb-6">
                <!-- Post Header -->
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1.5">
                            <x-bladewind.avatar image="https://github.com/Realitaa.png" size="tiny" />
                            <span class="text-xs font-semibold text-gray-900 dark:text-dark-100">Realitaa</span>
                            <span class="text-gray-400 dark:text-dark-500 text-xs">•</span>
                            <time class="text-xs text-gray-500 dark:text-dark-400"
                                datetime="{{ $post->created_at->toIso8601String() }}">
                                {{ $post->created_at->diffForHumans() }}
                            </time>
                        </div>
                        @unless ($post->trashed())
                            <h2 class="text-base md:text-lg font-bold text-gray-900 dark:text-white leading-snug">
                                <a href="{{ route('posts.show', $post) }}"
                                    class="hover:text-primary-600 dark:hover:text-primary-400 transition">
                                    {{ $post->title }}
                                </a>
                            </h2>
                        @endunless
                    </div>

                    <!-- Dropdown Options (Edit / Delete) only if not trashed -->
                    @unless ($post->trashed())
                        <div class="shrink-0 -mr-1">
                            <x-bladewind.dropmenu>
                                <x-bladewind.dropmenu.item icon="pencil-square"
                                    onclick="location.href='{{ route('posts.edit', $post) }}'">
                                    Edit Post
                                </x-bladewind.dropmenu.item>
                                <x-bladewind.dropmenu.item icon="trash"
                                    class="text-red-600! hover:bg-red-50! dark:hover:bg-red-950/30!"
                                    onclick="showModal('delete-post-{{ $post->id }}')">
                                    Hapus Post
                                </x-bladewind.dropmenu.item>
                            </x-bladewind.dropmenu>
                            <form id="delete-form-{{ $post->id }}" action="{{ route('posts.destroy', $post) }}" method="POST"
                                class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    @endunless
                </div>

                @if ($post->trashed())
                    <!-- Soft deleted message in italic with faded color replacing title, body, and images -->
                    <div class="italic text-gray-400 dark:text-dark-500 text-sm mt-3">
                        Postingan ini telah dihapus.
                    </div>
                @else
                    <!-- Post Body -->
                    <div class="text-gray-700 dark:text-dark-200 text-sm md:text-base leading-relaxed whitespace-pre-line mt-3">
                        {!! nl2br(e($post->body)) !!}
                    </div>

                    <!-- Media Carousel with arrows and indicators -->
                    @if ($post->media->isNotEmpty())
                        <div class="mt-4 overflow-hidden rounded-lg" data-viewer>
                            <x-bladewind.carousel loop="false" arrows="true" indicators="true" class="w-full">
                                @foreach ($post->media as $media)
                                    <x-bladewind.carousel.slide>
                                        <img src="{{ $media->url }}" alt="{{ $media->file_name }}"
                                            class="w-full h-64 sm:h-72 md:h-80 object-cover rounded-lg cursor-pointer select-none"
                                            draggable="false" loading="lazy" />
                                    </x-bladewind.carousel.slide>
                                @endforeach
                            </x-bladewind.carousel>
                        </div>
                    @endif
                @endif
            </div>

            @unless ($post->trashed())
                <!-- Delete Confirmation Modal -->
                <x-bladewind.modal
                    name="delete-post-{{ $post->id }}"
                    type="warning"
                    title="Hapus Postingan"
                    ok_button_label="Hapus"
                    cancel_button_label="Batal"
                    ok_button_action="document.getElementById('delete-form-{{ $post->id }}').submit()">
                    Apakah Anda yakin ingin menghapus postingan ini?
                </x-bladewind.modal>
            @endunless

            <!-- Horizontal line separator on screens smaller than md -->
            <hr class="border-gray-200 dark:border-dark-700 md:hidden my-0" />
        </article>
    @empty
        <!-- Empty State -->
        <div class="px-4 md:px-0 py-8">
            <x-bladewind.card class="rounded-xl border border-gray-200/80 dark:border-dark-700 shadow-xs">
                <x-bladewind.empty-state heading="Belum Ada Postingan"
                    message="Belum ada postingan yang diterbitkan. Jadilah yang pertama membagikan cerita!"
                    button_label="+ Buat Postingan Baru" onclick="location.href='{{ route('posts.create') }}'" />
            </x-bladewind.card>
        </div>
    @endforelse

    <!-- Pagination -->
    @if ($posts->hasPages())
        <div class="px-4 md:px-0 py-6">
            {{ $posts->links() }}
        </div>
    @endif
@endsection