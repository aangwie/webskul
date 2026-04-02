<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarouselImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarouselController extends Controller
{
    /**
     * Display a listing of carousel images.
     */
    public function index()
    {
        $images = CarouselImage::ordered()->get();
        return view('admin.carousel.index', compact('images'));
    }

    /**
     * Store a newly created carousel image.
     * Image is validated (max 500 KB), then compressed to WebP.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:512', // 512 KB ≈ 500 KB
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $webpPath = $this->convertToWebp($request->file('image'));

        CarouselImage::create([
            'title'     => $request->title,
            'image_path'=> $webpPath,
            'order'     => $request->order ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.carousel.index')
            ->with('success', 'Gambar carousel berhasil ditambahkan!');
    }

    /**
     * Update the specified carousel image.
     */
    public function update(Request $request, CarouselImage $carousel)
    {
        $request->validate([
            'title'    => 'nullable|string|max:255',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:512',
            'order'    => 'nullable|integer|min:0',
            'is_active'=> 'boolean',
        ]);

        $data = [
            'title'     => $request->title,
            'order'     => $request->order ?? $carousel->order,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('image')) {
            // Delete old file
            if ($carousel->image_path) {
                Storage::disk('public')->delete($carousel->image_path);
            }
            $data['image_path'] = $this->convertToWebp($request->file('image'));
        }

        $carousel->update($data);

        return redirect()->route('admin.carousel.index')
            ->with('success', 'Gambar carousel berhasil diperbarui!');
    }

    /**
     * Remove the specified carousel image.
     */
    public function destroy(CarouselImage $carousel)
    {
        if ($carousel->image_path) {
            Storage::disk('public')->delete($carousel->image_path);
        }
        $carousel->delete();

        return redirect()->route('admin.carousel.index')
            ->with('success', 'Gambar carousel berhasil dihapus!');
    }

    /**
     * Convert an uploaded image file to WebP using GD (lossless quality 100).
     * Returns the stored path relative to the public disk.
     */
    private function convertToWebp(\Illuminate\Http\UploadedFile $file): string
    {
        $mime = $file->getMimeType();

        // Load source image
        $source = match (true) {
            str_contains($mime, 'jpeg') => imagecreatefromjpeg($file->getRealPath()),
            str_contains($mime, 'png')  => imagecreatefrompng($file->getRealPath()),
            str_contains($mime, 'gif')  => imagecreatefromgif($file->getRealPath()),
            str_contains($mime, 'webp') => imagecreatefromwebp($file->getRealPath()),
            default                     => imagecreatefromjpeg($file->getRealPath()),
        };

        // Fix image orientation based on EXIF data (critical for mobile uploads)
        if (function_exists('exif_read_data') && str_contains($mime, 'jpeg')) {
            $exif = @exif_read_data($file->getRealPath());
            if (!empty($exif['Orientation'])) {
                $source = match ($exif['Orientation']) {
                    3 => imagerotate($source, 180, 0),
                    6 => imagerotate($source, -90, 0),
                    8 => imagerotate($source, 90, 0),
                    default => $source,
                };
            }
        }

        // Create new image 800x600 (width x height landscape)
        $targetWidth = 800;
        $targetHeight = 600;
        $resized = imagecreatetruecolor($targetWidth, $targetHeight);

        // Preserve transparency for PNG/GIF
        if (str_contains($mime, 'png') || str_contains($mime, 'gif')) {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
            imagefilledrectangle($resized, 0, 0, $targetWidth, $targetHeight, $transparent);
        }

        $srcWidth = imagesx($source);
        $srcHeight = imagesy($source);

        // Center crop to aspect ratio
        $srcRatio = $srcWidth / $srcHeight;
        $targetRatio = $targetWidth / $targetHeight;

        $cropX = 0;
        $cropY = 0;

        if ($srcRatio > $targetRatio) {
            $cropWidth = $srcHeight * $targetRatio;
            $cropHeight = $srcHeight;
            $cropX = ($srcWidth - $cropWidth) / 2;
        } else {
            $cropWidth = $srcWidth;
            $cropHeight = $srcWidth / $targetRatio;
            $cropY = ($srcHeight - $cropHeight) / 2;
        }

        imagecopyresampled($resized, $source, 0, 0, $cropX, $cropY, $targetWidth, $targetHeight, $cropWidth, $cropHeight);

        $directory = 'carousel';
        $filename  = uniqid('carousel_', true) . '.webp';
        $storagePath = storage_path('app/public/' . $directory);

        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0775, true);
        }

        $fullPath = $storagePath . '/' . $filename;

        // Quality 100 = best quality
        imagewebp($resized, $fullPath, 100);
        imagedestroy($source);
        imagedestroy($resized);

        return $directory . '/' . $filename;
    }
}
