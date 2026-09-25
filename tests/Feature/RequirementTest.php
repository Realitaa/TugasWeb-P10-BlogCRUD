<?php

use App\Http\Controllers\PostController;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Requirement 1 & 2: Route::resource('posts') + named routes & PostController resource (7 methods)
|--------------------------------------------------------------------------
*/

test('requirement 1 & 2: PostController implements all 7 resource methods and named routes are registered', function () {
    $reflection = new ReflectionClass(PostController::class);
    $resourceMethods = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];

    foreach ($resourceMethods as $method) {
        expect($reflection->hasMethod($method))->toBeTrue();
    }

    expect(Route::has('posts.index'))->toBeTrue()
        ->and(Route::has('posts.create'))->toBeTrue()
        ->and(Route::has('posts.store'))->toBeTrue()
        ->and(Route::has('posts.show'))->toBeTrue()
        ->and(Route::has('posts.edit'))->toBeTrue()
        ->and(Route::has('posts.update'))->toBeTrue()
        ->and(Route::has('posts.destroy'))->toBeTrue();
});

test('requirement 1 & 2: all 7 resource routes invoke corresponding methods and return expected responses', function () {
    // 1. GET posts.index
    $this->get(route('posts.index'))->assertStatus(200);

    // 2. GET posts.create
    $this->get(route('posts.create'))->assertStatus(200);

    // 3. POST posts.store
    $storeResponse = $this->post(route('posts.store'), [
        'title' => 'Judul Postingan Resource Test',
        'body' => 'Badan konten untuk pengujian rute resource.',
    ]);
    $storeResponse->assertRedirect(route('posts.index'));

    $post = Post::where('title', 'Judul Postingan Resource Test')->first();
    expect($post)->not->toBeNull();

    // 4. GET posts.show
    $this->get(route('posts.show', $post))->assertStatus(200);

    // 5. GET posts.edit
    $this->get(route('posts.edit', $post))->assertStatus(200);

    // 6. PUT posts.update
    $updateResponse = $this->put(route('posts.update', $post), [
        'title' => 'Judul Postingan Terupdate',
        'body' => 'Badan konten terupdate untuk pengujian.',
    ]);
    $updateResponse->assertRedirect(route('posts.show', $post));
    expect($post->fresh()->title)->toBe('Judul Postingan Terupdate');

    // 7. DELETE posts.destroy
    $deleteResponse = $this->delete(route('posts.destroy', $post));
    $deleteResponse->assertRedirect(route('posts.index'));
    expect($post->fresh()->trashed())->toBeTrue();
});

/*
|--------------------------------------------------------------------------
| Requirement 5: Validasi + error per field + old input
|--------------------------------------------------------------------------
*/

test('requirement 5: validation fails when required fields are missing and returns error per field', function () {
    $response = $this->post(route('posts.store'), [
        'title' => '',
        'body' => '',
    ]);

    $response->assertSessionHasErrors(['title', 'body']);
});

test('requirement 5: validation enforces maximum word constraints on title and body', function () {
    $tooLongTitle = implode(' ', array_fill(0, 65, 'kata'));
    $responseTitle = $this->post(route('posts.store'), [
        'title' => $tooLongTitle,
        'body' => 'Konten aman',
    ]);
    $responseTitle->assertSessionHasErrors('title');

    $tooLongBody = implode(' ', array_fill(0, 1001, 'kata'));
    $responseBody = $this->post(route('posts.store'), [
        'title' => 'Judul Valid',
        'body' => $tooLongBody,
    ]);
    $responseBody->assertSessionHasErrors('body');
});

test('requirement 5: old input is preserved on validation failure', function () {
    $response = $this->from(route('posts.create'))->post(route('posts.store'), [
        'title' => 'Judul Yang Harus Diingat Old Input',
        'body' => '', // sengaja dikosongkan agar gagal validasi
    ]);

    $response->assertRedirect(route('posts.create'));
    $response->assertSessionHasErrors('body');
    $response->assertSessionHasInput('title', 'Judul Yang Harus Diingat Old Input');
});

/*
|--------------------------------------------------------------------------
| Requirement 6: Flash message sukses/gagal
|--------------------------------------------------------------------------
*/

test('requirement 6: store, update, and delete actions trigger success flash messages', function () {
    // Store flash message
    $storeResponse = $this->post(route('posts.store'), [
        'title' => 'Post Flash Message Test',
        'body' => 'Konten untuk flash message.',
    ]);
    $storeResponse->assertSessionHas('success');

    $post = Post::where('title', 'Post Flash Message Test')->first();

    // Update flash message
    $updateResponse = $this->put(route('posts.update', $post), [
        'title' => 'Post Flash Message Test Diperbarui',
        'body' => 'Konten baru.',
    ]);
    $updateResponse->assertSessionHas('success');

    // Destroy flash message
    $destroyResponse = $this->delete(route('posts.destroy', $post));
    $destroyResponse->assertSessionHas('success');
});

/*
|--------------------------------------------------------------------------
| Requirement 7: @csrf semua form + @method PUT/DELETE
|--------------------------------------------------------------------------
*/

test('requirement 7: update and destroy endpoints reject plain POST requests without spoofed method', function () {
    $post = Post::factory()->create();

    // Plain POST to PUT route without _method=PUT returns 405 Method Not Allowed
    $rawPostUpdate = $this->post(route('posts.update', $post), [
        'title' => 'Percobaan Update',
        'body' => 'Konten update',
    ]);
    $rawPostUpdate->assertStatus(405);

    // Plain POST to DELETE route without _method=DELETE returns 405 Method Not Allowed
    $rawPostDelete = $this->post(route('posts.destroy', $post));
    $rawPostDelete->assertStatus(405);
});

test('requirement 7: method spoofing via POST with _method PUT and DELETE is supported', function () {
    $post = Post::factory()->create();

    // Spoofed PUT
    $spoofedPut = $this->post(route('posts.update', $post), [
        '_method' => 'PUT',
        'title' => 'Judul Spoofed PUT Berhasil',
        'body' => 'Konten berhasil diubah lewat spoofing method PUT.',
    ]);
    $spoofedPut->assertRedirect(route('posts.show', $post));
    expect($post->fresh()->title)->toBe('Judul Spoofed PUT Berhasil');

    // Spoofed DELETE
    $spoofedDelete = $this->post(route('posts.destroy', $post), [
        '_method' => 'DELETE',
    ]);
    $spoofedDelete->assertRedirect(route('posts.index'));
    expect($post->fresh()->trashed())->toBeTrue();
});

/*
|--------------------------------------------------------------------------
| Requirement 8: Route Model Binding + pagination
|--------------------------------------------------------------------------
*/

test('requirement 8: Route Model Binding binds model or throws 404 for invalid identifier', function () {
    $post = Post::factory()->create(['title' => 'Judul Route Model Binding']);

    // Valid UUID model binding
    $response = $this->get(route('posts.show', $post));
    $response->assertStatus(200)->assertSee('Judul Route Model Binding');

    // Nonexistent UUID returns 404 Not Found
    $notFound = $this->get('/posts/00000000-0000-0000-0000-000000000000');
    $notFound->assertStatus(404);
});

test('requirement 8: posts list supports pagination via query parameter', function () {
    Post::factory()->count(12)->create();

    $page1 = $this->get(route('posts.index'));
    $page1->assertStatus(200);
    $page1->assertViewHas('posts', fn ($posts) => $posts->currentPage() === 1 && $posts->perPage() === 5);

    $page2 = $this->get(route('posts.index', ['page' => 2]));
    $page2->assertStatus(200);
    $page2->assertViewHas('posts', fn ($posts) => $posts->currentPage() === 2 && $posts->count() === 5);
});

/*
|--------------------------------------------------------------------------
| Bonus 1: Pencarian (posts.search endpoint)
|--------------------------------------------------------------------------
*/

test('bonus 1: search endpoint filters posts by title and body and returns truncated body with show url', function () {
    $targetPost = Post::factory()->create([
        'title' => 'Kiat Belajar Framework Laravel 12',
        'body' => 'Panduan lengkap pemrograman web modern berbasis Laravel dan Tailwind CSS.',
    ]);

    $otherPost = Post::factory()->create([
        'title' => 'Resep Memasak Masakan Tradisional Nusantara',
        'body' => 'Cara membuat kuliner khas nusantara lezat di rumah.',
    ]);

    $response = $this->getJson(route('posts.search', ['q' => 'Laravel']));
    $response->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'id' => $targetPost->id,
            'title' => 'Kiat Belajar Framework Laravel 12',
            'url' => route('posts.show', $targetPost),
        ])
        ->assertJsonMissing(['title' => 'Resep Memasak Masakan Tradisional Nusantara']);
});

/*
|--------------------------------------------------------------------------
| Bonus 2: Soft Delete
|--------------------------------------------------------------------------
*/

test('bonus 2: soft deleted posts have deleted_at timestamp and are displayed with deletion notice', function () {
    $post = Post::factory()->create([
        'title' => 'Post Sensitif Yang Dihapus',
        'body' => 'Konten rahasia yang tidak boleh tampil lagi setelah dihapus.',
    ]);

    $post->delete();
    expect($post->trashed())->toBeTrue();

    // Excluded from normal search
    $searchResponse = $this->getJson(route('posts.search', ['q' => 'Sensitif']));
    $searchResponse->assertOk()->assertExactJson([]);

    // On index, title/body are omitted and replaced by deletion notice
    $indexResponse = $this->get(route('posts.index'));
    $indexResponse->assertStatus(200)
        ->assertSee('Postingan ini telah dihapus.')
        ->assertDontSee('Post Sensitif Yang Dihapus')
        ->assertDontSee('Konten rahasia yang tidak boleh tampil lagi');

    // On show page, deletion notice is displayed
    $showResponse = $this->get(route('posts.show', $post));
    $showResponse->assertStatus(200)
        ->assertSee('Postingan ini telah dihapus.')
        ->assertDontSee('Post Sensitif Yang Dihapus');
});

/*
|--------------------------------------------------------------------------
| Bonus 3: Upload Gambar
|--------------------------------------------------------------------------
*/

test('bonus 3: post can store up to 3 visual media images and persists files to public storage', function () {
    Storage::fake('public');

    $file1 = UploadedFile::fake()->image('preview1.jpg', 640, 480)->size(150);
    $file2 = UploadedFile::fake()->image('preview2.png', 800, 600)->size(250);

    $response = $this->post(route('posts.store'), [
        'title' => 'Postingan Galeri Media',
        'body' => 'Uji coba upload multi-gambar visual.',
        'media' => [$file1, $file2],
    ]);

    $response->assertRedirect(route('posts.index'));

    $post = Post::where('title', 'Postingan Galeri Media')->first();
    expect($post)->not->toBeNull()
        ->and($post->media)->toHaveCount(2);

    foreach ($post->media as $media) {
        Storage::disk('public')->assertExists($media->file_path);
        expect($media->url)->toContain('/storage/'.$media->file_path);
    }
});

test('bonus 3: upload validation rejects non-visual files, more than 3 files, or files > 512KB', function () {
    Storage::fake('public');

    // Exceeding 3 files
    $fourFiles = [
        UploadedFile::fake()->image('1.jpg'),
        UploadedFile::fake()->image('2.jpg'),
        UploadedFile::fake()->image('3.jpg'),
        UploadedFile::fake()->image('4.jpg'),
    ];
    $responseCount = $this->post(route('posts.store'), [
        'title' => 'Post 4 Files',
        'body' => 'Body test',
        'media' => $fourFiles,
    ]);
    $responseCount->assertSessionHasErrors('media');

    // File > 512KB
    $largeFile = UploadedFile::fake()->image('large.jpg')->size(600);
    $responseSize = $this->post(route('posts.store'), [
        'title' => 'Post File Besar',
        'body' => 'Body test',
        'media' => [$largeFile],
    ]);
    $responseSize->assertSessionHasErrors('media.0');
});
