<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\UpdateImageRequest;
use App\Models\Image;
use App\Models\Tag;
use App\Services\ImageService;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function __construct(private readonly ImageService $imageService) {}

    public function index(Request $request)
    {
        // Ne jamais charger la colonne blob dans les listes.
        $images = Image::select(Image::COLUMNS_WITHOUT_BLOB)
            ->with(['tags', 'location'])
            ->search($request->string('q')->toString())
            ->when($request->integer('tag'), function ($query, $tagId) {
                $query->whereHas('tags', fn ($q) => $q->where('bp_tags.id', $tagId));
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.images.index', [
            'images' => $images,
            'tags' => Tag::orderBy('nom')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.images.create', [
            'image' => new Image,
            'tags' => Tag::orderBy('nom')->get(),
        ]);
    }

    public function store(StoreImageRequest $request)
    {
        $image = $this->imageService->create(
            $request->safe()->except('photo'),
            $request->file('photo'),
        );

        return redirect()
            ->route('admin.oeuvres.show', $image)
            ->with('success', 'Œuvre créée avec succès.');
    }

    public function show(Image $image)
    {
        $image->load(['tags', 'location', 'reservations']);

        return view('admin.images.show', compact('image'));
    }

    public function edit(Image $image)
    {
        $image->load(['tags', 'location']);

        return view('admin.images.edit', [
            'image' => $image,
            'tags' => Tag::orderBy('nom')->get(),
        ]);
    }

    public function update(UpdateImageRequest $request, Image $image)
    {
        $this->imageService->update(
            $image,
            $request->safe()->except('photo'),
            $request->file('photo'),
        );

        return redirect()
            ->route('admin.oeuvres.show', $image)
            ->with('success', 'Œuvre mise à jour avec succès.');
    }

    public function destroy(Image $image)
    {
        $this->imageService->delete($image);

        return redirect()
            ->route('admin.oeuvres.index')
            ->with('success', 'Œuvre supprimée avec succès.');
    }

    /**
     * Sert la photo stockée en base (colonne LONGBLOB partagée avec WordPress).
     */
    public function photo(Image $image)
    {
        $content = $image->getRawOriginal('image');

        abort_if($content === null || $content === '', 404);

        $mime = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $content) ?: 'application/octet-stream';

        return response($content)
            ->header('Content-Type', $mime)
            ->header('Cache-Control', 'private, max-age=3600');
    }
}
