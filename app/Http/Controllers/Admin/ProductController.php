<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\PackagingType;
use App\Models\Producer;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductImage;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'producer']);

        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->string('q').'%');
        }

        $products = $query->latest()->paginate(20)->withQueryString();
        $products->getCollection()->transform(fn (Product $product) => [
            'id' => $product->id,
            'name' => $product->name,
            'category' => $product->category ? ['id' => $product->category->id, 'name' => $product->category->name] : null,
            'price' => (float) $product->price,
            'stock_quantity' => $product->stock_quantity,
            'stock_alert_threshold' => $product->stock_alert_threshold,
            'is_active' => $product->is_active,
        ]);

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'filters' => ['q' => $request->input('q', '')],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Products/Create', [
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'producers' => Producer::orderBy('name')->get(['id', 'name']),
            'units' => Unit::where('is_active', true)->orderBy('name')->get(['id', 'name', 'abbreviation']),
            'packagingTypes' => PackagingType::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'attributes' => ProductAttribute::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['reference'] = $this->nextReference();

        $product = Product::create($data);

        $this->storeImages($request, $product);
        $this->storeAttributeValues($request, $product);

        return redirect()->route('admin.produits.index')->with('success', 'Produit créé.');
    }

    public function edit(Product $product)
    {
        $product->load('images', 'attributeValues');

        return Inertia::render('Admin/Products/Edit', [
            'product' => [
                ...$product->only([
                    'id', 'category_id', 'producer_id', 'name', 'reference', 'short_description', 'description',
                    'origin', 'unit', 'packaging_type_id', 'stock_quantity', 'stock_alert_threshold',
                    'is_featured', 'is_new', 'is_active',
                ]),
                'price' => (float) $product->price,
                'professional_price' => $product->professional_price !== null ? (float) $product->professional_price : null,
                'wholesale_price' => $product->wholesale_price !== null ? (float) $product->wholesale_price : null,
                'promo_price' => $product->promo_price !== null ? (float) $product->promo_price : null,
                'weight' => $product->weight !== null ? (float) $product->weight : null,
                'promo_starts_at' => $product->promo_starts_at?->format('Y-m-d\TH:i'),
                'promo_ends_at' => $product->promo_ends_at?->format('Y-m-d\TH:i'),
                'expiry_date' => $product->expiry_date?->format('Y-m-d'),
                'images' => $product->images->map(fn (ProductImage $image) => [
                    'id' => $image->id,
                    'url' => asset('fichiers/'.$image->path),
                ]),
            ],
            'attributeValues' => $product->attributeValues->pluck('value', 'product_attribute_id'),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'producers' => Producer::orderBy('name')->get(['id', 'name']),
            'units' => Unit::where('is_active', true)->orderBy('name')->get(['id', 'name', 'abbreviation']),
            'packagingTypes' => PackagingType::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'attributes' => ProductAttribute::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request);

        if ($data['name'] !== $product->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $product->id);
        }

        $product->update($data);

        $this->storeImages($request, $product);
        $this->storeAttributeValues($request, $product);

        return redirect()->route('admin.produits.index')->with('success', 'Produit mis à jour.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $product->delete();

        return back()->with('success', 'Produit supprimé.');
    }

    public function deleteImage(ProductImage $image): RedirectResponse
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', 'Image supprimée.');
    }

    protected function storeImages(Request $request, Product $product): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $position = $product->images()->max('position') + 1;

        foreach ($request->file('images') as $file) {
            ProductImage::create([
                'product_id' => $product->id,
                'path' => $file->store('products', 'public'),
                'position' => $position++,
            ]);
        }
    }

    protected function storeAttributeValues(Request $request, Product $product): void
    {
        $values = $request->input('attributes', []);

        foreach ($values as $attributeId => $value) {
            $value = trim((string) $value);

            if ($value === '') {
                ProductAttributeValue::where('product_id', $product->id)->where('product_attribute_id', $attributeId)->delete();

                continue;
            }

            ProductAttributeValue::updateOrCreate(
                ['product_id' => $product->id, 'product_attribute_id' => $attributeId],
                ['value' => $value]
            );
        }
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'producer_id' => ['nullable', 'exists:producers,id'],
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'origin' => ['nullable', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'packaging_type_id' => ['nullable', 'exists:packaging_types,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'professional_price' => ['nullable', 'numeric', 'min:0'],
            'wholesale_price' => ['nullable', 'numeric', 'min:0'],
            'promo_price' => ['nullable', 'numeric', 'min:0'],
            'promo_starts_at' => ['nullable', 'date'],
            'promo_ends_at' => ['nullable', 'date'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'stock_alert_threshold' => ['required', 'integer', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'is_new' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:16384'],
            'attributes' => ['nullable', 'array'],
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_new'] = $request->boolean('is_new');
        $data['is_active'] = $request->boolean('is_active');
        unset($data['images'], $data['attributes']);

        return $data;
    }

    protected function nextReference(): string
    {
        $max = Product::where('reference', 'like', 'CA-%')
            ->get(['reference'])
            ->map(fn (Product $p) => (int) substr($p->reference, 3))
            ->max() ?? 0;

        return 'CA-'.str_pad((string) ($max + 1), 4, '0', STR_PAD_LEFT);
    }

    protected function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Product::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
