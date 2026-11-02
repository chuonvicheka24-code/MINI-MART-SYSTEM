<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Lets the admin replace the homepage hero banner and the category photos.
 * Files are saved straight into public/uploads/... (no storage:link needed).
 */
class SiteImageController extends Controller
{
    public const DEFAULT_HERO = 'Photo/150a7d6ac15444d505c789b0c017f9f1.jpg';

    private const RULES = ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']; // 2 MB

    public function hero(Request $request): JsonResponse
    {
        $request->validate(['image' => self::RULES]);

        $settings = Setting::current();
        $old = $settings->hero_image;

        $settings->update(['hero_image' => $this->store($request->file('image'), 'uploads/site', 'hero')]);
        $this->deleteOld($old, 'uploads/site/');

        return response()->json(['url' => Product::resolveImageUrl($settings->hero_image)]);
    }

    public function resetHero(): JsonResponse
    {
        $settings = Setting::current();
        $old = $settings->hero_image;

        $settings->update(['hero_image' => null]);
        $this->deleteOld($old, 'uploads/site/');

        return response()->json(['url' => Product::resolveImageUrl(self::DEFAULT_HERO)]);
    }

    public function category(Request $request, Category $category): JsonResponse
    {
        $request->validate(['image' => self::RULES]);

        $old = $category->image;
        $category->update(['image' => $this->store($request->file('image'), 'uploads/categories', 'cat'.$category->id)]);
        $this->deleteOld($old, 'uploads/categories/');

        return response()->json(['url' => $category->fresh()->image_url]);
    }

    private function store(UploadedFile $file, string $dir, string $prefix): string
    {
        $name = $prefix.'-'.now()->timestamp.'-'.Str::random(6).'.'.$file->getClientOriginalExtension();
        $file->move(public_path($dir), $name);

        return $dir.'/'.$name;
    }

    /** Only ever delete files this feature created — never the original Photo/ files. */
    private function deleteOld(?string $path, string $mustStartWith): void
    {
        if ($path && str_starts_with($path, $mustStartWith) && is_file(public_path($path))) {
            @unlink(public_path($path));
        }
    }
}
