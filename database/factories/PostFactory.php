<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = fake('id_ID');

        $titles = [
            'Panduan Lengkap Belajar Pemrograman Web Modern untuk Pemula',
            'Tips Efektif Meningkatkan Produktivitas Kerja dari Rumah di Era Digital',
            'Perkembangan Teknologi Kecerdasan Buatan dan Dampaknya pada Industri Kreatif',
            'Mengenal Fitur Unggulan Framework Laravel dan Arsitektur Modern',
            'Strategi Mengatur Keuangan Pribadi dan Investasi untuk Generasi Muda',
            'Eksplorasi Keindahan Destinasi Wisata Tersembunyi di Kepulauan Nusantara',
            'Resep Kuliner Tradisional Nusantara yang Mudah Dibuat di Dapur Sendiri',
            'Langkah Nyata Menjaga Kesehatan Mental di Tengah Tekanan Beban Kerja',
            'Cara Membangun Portofolio Software Engineer yang Menarik Perhatian Recruiter',
            'Kiat Membangun Kebiasaan Membaca Buku Secara Konsisten Setiap Hari',
            'Tren Desain Antarmuka UI dan Pengalaman Pengguna UX Terpopuler Tahun Ini',
            'Prinsip Menulis Kode Bersih dan Mudah Dirawat Menggunakan Clean Code',
            'Manfaat Berolahraga Secara Teratur Terhadap Kebugaran Fisik dan Fokus Pikiran',
            'Peluang dan Tantangan Bisnis Ramah Lingkungan Berkelanjutan di Masa Depan',
            'Optimasi Kinerja Basis Data untuk Aplikasi Skala Menengah dan Besar',
            'Pentingnya Kesadaran Keamanan Siber dalam Menjaga Privasi Akun Digital',
            'Menumbuhkan Semangat Kolaborasi dan Kepemimpinan Efektif dalam Sebuah Tim',
            'Inovasi Energi Terbarukan Sebagai Solusi Menghadapi Perubahan Iklim',
            'Kiat Memilih Perangkat Kerja yang Tepat untuk Menunjang Produktivitas Harian',
            'Menikmati Harmoni Tradisi Budaya Lokal dalam Arus Modernisasi Global',
            'Mengenal Konsep Arsitektur Microservices dan Penerapannya di Dunia Nyata',
            'Peran Penting Literasi Finansial Sejak Usia Dini bagi Mahasiswa',
            'Menghadapi Tantangan Burnout: Cara Cerdas Melepaskan Stres dan Menyegarkan Pikiran',
            'Membangun Komunikasi Efektif Antara Anggota Tim dalam Proyek Kolaboratif',
            'Masa Depan Komputasi Awan dan Transformasi Sistem Informasi Perusahaan',
        ];

        $paragraphs = [
            'Di era perkembangan informasi yang bergerak sangat dinamis, kemampuan untuk terus beradaptasi dan mempelajari hal-hal baru merupakan aset yang sangat berharga. Tanpa kemauan untuk terus mengasah diri, kita akan mudah tertinggal oleh cepatnya perubahan teknologi dan kebutuhan industri.',
            'Salah satu kunci utama keberhasilan adalah konsistensi dalam tindakan kecil setiap hari. Menentukan skala prioritas yang tepat, mengatur waktu dengan bijak, serta memiliki komitmen yang teguh akan membantu kita menyelesaikan berbagai tanggung jawab tanpa merasa terbebani secara berlebihan.',
            'Tidak kalah penting adalah kemampuan untuk membangun komunikasi yang sehat dan terbuka dengan rekan kerja maupun lingkungan sekitar. Sinergi yang baik mampu menghasilkan ide-ide inovatif yang sering kali tidak terpikirkan jika kita hanya bekerja secara individual.',
            'Kesehatan fisik dan kesejahteraan mental harus selalu menjadi fondasi utama. Menyediakan waktu untuk beristirahat yang cukup, berolahraga, serta menikmati hobi di luar rutinitas harian dapat mengembalikan energi dan menjaga kreativitas tetap prima.',
            'Semoga tulisan ini dapat memberikan inspirasi serta motivasi positif bagi para pembaca. Teruslah berproses, jangan takut mencoba hal baru, dan jadikan setiap rintangan sebagai batu loncatan menuju pencapaian yang lebih tinggi.',
        ];

        $title = $faker->randomElement($titles);
        $bodyParagraphs = $faker->randomElements($paragraphs, $faker->numberBetween(2, 4));

        return [
            'title' => $title,
            'body' => implode("\n\n", $bodyParagraphs),
        ];
    }
}
