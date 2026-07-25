<?php

namespace Modules\Room\Services;

use Illuminate\Http\UploadedFile;

class ImageService
{
    /**
     * Kompres dan simpan gambar menggunakan PHP GD.
     * Target: maks $maxKb KB. Simpan ke $dir/$filename.jpg
     *
     * @param  UploadedFile $file
     * @param  string       $dir       Path absolut direktori tujuan
     * @param  string       $filename  Nama file output (tanpa ekstensi)
     * @param  int          $maxKb     Maksimal ukuran dalam KB (default 200)
     * @return string                  Path relatif dari public/ ke file yang disimpan
     */
    public static function compressAndSave(
        UploadedFile $file,
        string $dir,
        string $filename,
        int $maxKb = 200
    ): string {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $mime     = $file->getMimeType();
        $tmpPath  = $file->getRealPath();

        // Load source image dari GD sesuai tipe
        $source = match (true) {
            str_contains($mime, 'jpeg'), str_contains($mime, 'jpg') => imagecreatefromjpeg($tmpPath),
            str_contains($mime, 'png')  => self::pngToTrueColor($tmpPath),
            str_contains($mime, 'webp') => imagecreatefromwebp($tmpPath),
            default                     => imagecreatefromjpeg($tmpPath),
        };

        if (!$source) {
            // Fallback: simpan apa adanya jika GD gagal
            $rawName = $filename . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $rawName);
            return 'assets/rooms/' . $rawName;
        }

        $outFilename = $filename . '.jpg';
        $outPath     = $dir . DIRECTORY_SEPARATOR . $outFilename;

        // Resize jika dimensi terlalu besar (maks 1920px di sisi terpanjang)
        $source = self::resizeIfNeeded($source, 1920);

        // Kompresi iteratif: mulai dari quality 85, turun sampai ukuran ≤ maxKb
        $quality = 85;
        do {
            imagejpeg($source, $outPath, $quality);
            $sizeKb = filesize($outPath) / 1024;
            $quality -= 5;
        } while ($sizeKb > $maxKb && $quality >= 20);

        imagedestroy($source);

        return 'assets/rooms/' . $outFilename;
    }

    /**
     * Convert PNG (mungkin transparan) ke JPEG-compatible truecolor.
     */
    private static function pngToTrueColor(string $path)
    {
        $src = imagecreatefrompng($path);
        if (!$src) return false;

        $w   = imagesx($src);
        $h   = imagesy($src);
        $dst = imagecreatetruecolor($w, $h);

        // Isi background putih (untuk PNG transparan)
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);
        imagecopy($dst, $src, 0, 0, 0, 0, $w, $h);
        imagedestroy($src);

        return $dst;
    }

    /**
     * Resize gambar agar sisi terpanjang tidak melebihi $maxSide px.
     * Jika sudah lebih kecil, kembalikan gambar asli.
     */
    private static function resizeIfNeeded($image, int $maxSide)
    {
        $w = imagesx($image);
        $h = imagesy($image);

        if ($w <= $maxSide && $h <= $maxSide) {
            return $image;
        }

        if ($w >= $h) {
            $newW = $maxSide;
            $newH = (int) round($h * ($maxSide / $w));
        } else {
            $newH = $maxSide;
            $newW = (int) round($w * ($maxSide / $h));
        }

        $resized = imagecreatetruecolor($newW, $newH);
        // Background putih
        $white = imagecolorallocate($resized, 255, 255, 255);
        imagefill($resized, 0, 0, $white);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newW, $newH, $w, $h);
        imagedestroy($image);

        return $resized;
    }
}
