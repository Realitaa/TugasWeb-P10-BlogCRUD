<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('media');

        // Nomor acak unik dari 1-1000
        $uniquePicsumIds = range(1, 1000);
        shuffle($uniquePicsumIds);

        // Buat minimal 20 post
        $posts = Post::factory()->count(20)->create();

        $this->command?->info('Membuat 20 post dan mengunduh gambar acak unik dari picsum.photos...');

        foreach ($posts as $index => $post) {
            $mediaCount = rand(0, 3);

            for ($i = 0; $i < $mediaCount; $i++) {
                if (empty($uniquePicsumIds)) {
                    break;
                }

                $randomNumber = array_pop($uniquePicsumIds);
                $width = 800;
                $url = "https://picsum.photos/id/{$randomNumber}/{$width}.jpg";

                $fileName = "picsum_{$randomNumber}_".Str::random(6).'.jpg';
                $filePath = "media/{$fileName}";

                try {
                    $response = Http::timeout(8)->retry(2, 200)->get($url);

                    if ($response->successful() && ! empty($response->body())) {
                        $imageContent = $response->body();
                    } else {
                        $imageContent = $this->generateFallbackImage($randomNumber, $width);
                    }
                } catch (\Throwable) {
                    $imageContent = $this->generateFallbackImage($randomNumber, $width);
                }

                Storage::disk('public')->put($filePath, $imageContent);

                Media::create([
                    'post_id' => $post->id,
                    'file_path' => $filePath,
                    'file_name' => $fileName,
                    'mime_type' => 'image/jpeg',
                    'file_size' => strlen($imageContent),
                ]);
            }

            $this->command?->getOutput()->write('.');
        }

        $this->command?->newLine();
        $this->command?->info('Seeding posts dan media selesai! Total post: '.Post::count().', Total media: '.Media::count());
    }

    /**
     * Generate fallback JPEG image if network request fails.
     */
    private function generateFallbackImage(int $id, int $width): string
    {
        $height = (int) ($width * 0.75);
        $image = imagecreatetruecolor($width, $height);

        $bgColor = imagecolorallocate(
            $image,
            ($id * 37) % 200 + 40,
            ($id * 67) % 200 + 40,
            ($id * 97) % 200 + 40
        );
        imagefill($image, 0, 0, $bgColor);

        $textColor = imagecolorallocate($image, 255, 255, 255);
        imagestring($image, 5, 20, 20, "Picsum ID: {$id}", $textColor);

        ob_start();
        imagejpeg($image, null, 80);
        $content = (string) ob_get_clean();
        imagedestroy($image);

        return $content;
    }
}
