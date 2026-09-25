<?php

use App\Models\Media;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('post resource routes have registered route names', function () {
    expect(Route::has('posts.index'))->toBeTrue()
        ->and(Route::has('posts.create'))->toBeTrue()
        ->and(Route::has('posts.store'))->toBeTrue()
        ->and(Route::has('posts.show'))->toBeTrue()
        ->and(Route::has('posts.edit'))->toBeTrue()
        ->and(Route::has('posts.update'))->toBeTrue()
        ->and(Route::has('posts.destroy'))->toBeTrue();
});

test('can store a post with media uploads', function () {
    Storage::fake('public');

    $response = $this->post(route('posts.store'), [
        'title' => 'Judul Postingan Baru',
        'body' => 'Ini adalah konten isi dari postingan baru.',
        'media' => [
            UploadedFile::fake()->image('gambar1.jpg', 600, 400)->size(300),
            UploadedFile::fake()->image('gambar2.png', 600, 400)->size(400),
        ],
    ]);

    $response->assertRedirect(route('posts.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('posts', [
        'title' => 'Judul Postingan Baru',
        'body' => 'Ini adalah konten isi dari postingan baru.',
    ]);

    $post = Post::first();
    expect($post->media)->toHaveCount(2);

    Storage::disk('public')->assertExists($post->media->first()->file_path);
});

test('store fails when title exceeds 64 words', function () {
    $longTitle = implode(' ', array_fill(0, 65, 'kata'));

    $response = $this->post(route('posts.store'), [
        'title' => $longTitle,
        'body' => 'Konten aman',
    ]);

    $response->assertSessionHasErrors('title');
});

test('store fails when media exceeds 3 files or 512KB', function () {
    Storage::fake('public');

    // More than 3 files
    $response = $this->post(route('posts.store'), [
        'title' => 'Test Post',
        'body' => 'Konten aman',
        'media' => [
            UploadedFile::fake()->image('1.jpg')->size(100),
            UploadedFile::fake()->image('2.jpg')->size(100),
            UploadedFile::fake()->image('3.jpg')->size(100),
            UploadedFile::fake()->image('4.jpg')->size(100),
        ],
    ]);
    $response->assertSessionHasErrors('media');

    // File > 512KB
    $responseBig = $this->post(route('posts.store'), [
        'title' => 'Test Post 2',
        'body' => 'Konten aman',
        'media' => [
            UploadedFile::fake()->image('big.jpg')->size(600),
        ],
    ]);
    $responseBig->assertSessionHasErrors('media.0');
});

test('can update a post', function () {
    $post = Post::factory()->create([
        'title' => 'Judul Lama',
        'body' => 'Isi Lama',
    ]);

    $response = $this->put(route('posts.update', $post), [
        'title' => 'Judul Baru Diubah',
        'body' => 'Isi Baru Diubah',
    ]);

    $response->assertRedirect(route('posts.show', $post));
    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'title' => 'Judul Baru Diubah',
    ]);
});

test('can soft delete a post and keep media in database', function () {
    $post = Post::factory()->create();
    $media = Media::factory()->create(['post_id' => $post->id]);

    $response = $this->delete(route('posts.destroy', $post));

    $response->assertRedirect(route('posts.index'));
    $response->assertSessionHas('success');

    $this->assertSoftDeleted('posts', ['id' => $post->id]);
    $this->assertDatabaseHas('media', ['id' => $media->id]);
});

test('uploading files with identical names stores them with unique uuid filenames and preserves original name', function () {
    Storage::fake('public');

    $response = $this->post(route('posts.store'), [
        'title' => 'Post Media Duplikat Nama',
        'body' => 'Konten postingan untuk pengujian duplikasi nama file media.',
        'media' => [
            UploadedFile::fake()->image('foto.jpg', 600, 400)->size(200),
            UploadedFile::fake()->image('foto.jpg', 600, 400)->size(200),
        ],
    ]);

    $response->assertRedirect(route('posts.index'));

    $post = Post::where('title', 'Post Media Duplikat Nama')->first();
    expect($post->media)->toHaveCount(2);

    $media1 = $post->media[0];
    $media2 = $post->media[1];

    // Kedua file tetap menyimpan nama asli yang sama di database
    expect($media1->file_name)->toBe('foto.jpg')
        ->and($media2->file_name)->toBe('foto.jpg');

    // Namun nama file fisik di storage menggunakan UUID yang berbeda sehingga tidak menimpa
    expect($media1->file_path)->not->toBe($media2->file_path);
    Storage::disk('public')->assertExists($media1->file_path);
    Storage::disk('public')->assertExists($media2->file_path);
});

test('index renders successfully with posts and pagination', function () {
    Post::factory()->count(7)->create();

    $response = $this->get(route('posts.index'));

    $response->assertStatus(200);
    $response->assertViewIs('posts.index');
    $response->assertViewHas('posts');
    $response->assertSee('BlogPost');
    $response->assertSee('Apa yang Anda pikirkan hari ini?');
});

test('index renders empty state when no posts exist', function () {
    $response = $this->get(route('posts.index'));

    $response->assertStatus(200);
    $response->assertSee('Belum Ada Postingan');
});

test('create page renders successfully', function () {
    $response = $this->get(route('posts.create'));

    $response->assertStatus(200);
    $response->assertViewIs('posts.create');
    $response->assertSee('Buat Postingan Baru');
});

test('show page renders post with media', function () {
    $post = Post::factory()->create();
    $media = Media::factory()->create(['post_id' => $post->id]);

    $response = $this->get(route('posts.show', $post));

    $response->assertStatus(200);
    $response->assertViewIs('posts.show');
    $response->assertSee($post->title);
    $response->assertSee($media->file_name);
});

test('edit page renders post form', function () {
    $post = Post::factory()->create();

    $response = $this->get(route('posts.edit', $post));

    $response->assertStatus(200);
    $response->assertViewIs('posts.edit');
    $response->assertSee('Edit Postingan');
    $response->assertSee($post->title);
});

test('soft deleted post is displayed on index with deleted message instead of content', function () {
    $post = Post::factory()->create([
        'title' => 'Judul Postingan Terhapus',
        'body' => 'Badan konten postingan rahasia yang terhapus.',
    ]);
    $media = Media::factory()->create(['post_id' => $post->id, 'file_name' => 'gambar_terhapus.jpg']);

    $post->delete();

    $response = $this->get(route('posts.index'));

    $response->assertStatus(200);
    $response->assertSee('Postingan ini telah dihapus.');
    $response->assertDontSee('Judul Postingan Terhapus');
    $response->assertDontSee('Badan konten postingan rahasia yang terhapus.');
    $response->assertDontSee('gambar_terhapus.jpg');
    $response->assertDontSee('Edit Post');
});

test('soft deleted post on show page displays deleted message instead of content', function () {
    $post = Post::factory()->create([
        'title' => 'Judul Postingan Terhapus Show',
        'body' => 'Badan konten show yang terhapus.',
    ]);
    $post->delete();

    $response = $this->get(route('posts.show', $post));

    $response->assertStatus(200);
    $response->assertSee('Postingan ini telah dihapus.');
    $response->assertDontSee('Judul Postingan Terhapus Show');
    $response->assertDontSee('Badan konten show yang terhapus.');
    $response->assertDontSee('Edit Post');
});
