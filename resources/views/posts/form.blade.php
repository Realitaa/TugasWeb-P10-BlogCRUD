@php
    $isEdit = isset($post) && $post->exists;
    $actionUrl = $isEdit ? route('posts.update', $post) : route('posts.store');
@endphp

<form action="{{ $actionUrl }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <!-- Judul Post -->
    <div>
        <label for="title" class="block text-sm font-semibold text-gray-800 dark:text-dark-200 mb-1">
            Judul Postingan <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            id="title"
            name="title"
            value="{{ old('title', $post->title ?? '') }}"
            placeholder="Masukkan judul postingan (maksimal 64 kata)"
            required
            class="w-full rounded-lg border {{ $errors->has('title') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 dark:border-dark-600 focus:ring-primary-500 focus:border-primary-500' }} bg-white dark:bg-dark-800 px-4 py-2.5 text-gray-900 dark:text-dark-100 text-sm focus:outline-none focus:ring-2 shadow-xs"
        />
        <div class="flex items-center justify-between mt-1">
            <span class="text-xs text-gray-500 dark:text-dark-400">Maksimal panjang 64 kata.</span>
            @error('title')
                <span class="text-xs text-red-500 font-medium">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <!-- Body Post -->
    <div>
        <label for="body" class="block text-sm font-semibold text-gray-800 dark:text-dark-200 mb-1">
            Konten / Isi Postingan <span class="text-red-500">*</span>
        </label>
        <textarea
            id="body"
            name="body"
            rows="6"
            placeholder="Tuliskan isi postingan Anda di sini (maksimal 1000 kata)..."
            required
            class="w-full rounded-lg border {{ $errors->has('body') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 dark:border-dark-600 focus:ring-primary-500 focus:border-primary-500' }} bg-white dark:bg-dark-800 px-4 py-2.5 text-gray-900 dark:text-dark-100 text-sm focus:outline-none focus:ring-2 shadow-xs resize-y"
        >{{ old('body', $post->body ?? '') }}</textarea>
        <div class="flex items-center justify-between mt-1">
            <span class="text-xs text-gray-500 dark:text-dark-400">Maksimal panjang 1000 kata.</span>
            @error('body')
                <span class="text-xs text-red-500 font-medium">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <!-- Media Upload -->
    <div>
        <label for="media" class="block text-sm font-semibold text-gray-800 dark:text-dark-200 mb-1">
            Lampirkan Media Visual (Gambar)
        </label>
        <input
            type="file"
            id="media"
            name="media[]"
            multiple
            accept="image/*"
            class="w-full text-sm text-gray-500 dark:text-dark-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-dark-700 dark:file:text-primary-400 cursor-pointer border border-gray-300 dark:border-dark-600 rounded-lg p-2 bg-white dark:bg-dark-800"
        />
        <div class="mt-1">
            <span class="text-xs text-gray-500 dark:text-dark-400">
                Format: JPEG, PNG, WEBP, GIF. Maksimal 3 gambar per post, ukuran maksimal 512KB per file.
            </span>
            @error('media')
                <div class="text-xs text-red-500 font-medium mt-1">{{ $message }}</div>
            @enderror
            @foreach ($errors->get('media.*') as $messages)
                @foreach ($messages as $message)
                    <div class="text-xs text-red-500 font-medium mt-0.5">{{ $message }}</div>
                @endforeach
            @endforeach
        </div>
    </div>

    <!-- Existing Media (Edit Mode) -->
    @if ($isEdit && $post->media->isNotEmpty())
        <div>
            <span class="block text-sm font-semibold text-gray-800 dark:text-dark-200 mb-2">
                Media Saat Ini (Centang untuk menghapus)
            </span>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach ($post->media as $media)
                    <div class="relative group rounded-lg overflow-hidden border border-gray-200 dark:border-dark-700 bg-gray-50 dark:bg-dark-900 p-2">
                        <img src="{{ $media->url }}" alt="{{ $media->file_name }}" class="w-full h-28 object-cover rounded-md mb-2" />
                        <div class="text-xs text-gray-500 dark:text-dark-400 truncate mb-1.5" title="{{ $media->file_name }}">
                            {{ $media->file_name }}
                        </div>
                        <label class="flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400 cursor-pointer">
                            <input type="checkbox" name="delete_media[]" value="{{ $media->id }}" class="rounded text-red-600 focus:ring-red-500 size-3.5" />
                            <span>Hapus media</span>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-dark-700">
        <a href="{{ route('posts.index') }}" class="text-sm font-medium text-gray-600 dark:text-dark-400 hover:text-gray-900 dark:hover:text-dark-200 px-4 py-2 rounded-lg transition">
            Batal
        </a>
        <x-bladewind.button can_submit="true" uppercasing="false" icon="check">
            {{ $isEdit ? 'Perbarui Postingan' : 'Terbitkan Postingan' }}
        </x-bladewind.button>
    </div>
</form>
