@php
    $statuses = ['active' => 'Active', 'draft' => 'Draft', 'archived' => 'Archived'];
    $schools = ['wolf' => 'School of the Wolf', 'griffin' => 'School of the Griffin', 'bear' => 'School of the Bear', 'cat' => 'School of the Cat', 'manticore' => 'School of the Manticore', 'viper' => 'School of the Viper', 'generic' => 'Generic'];
    $rarities = ['common' => 'Common', 'uncommon' => 'Uncommon', 'rare' => 'Rare', 'legendary' => 'Legendary'];
@endphp

<div class="form-grid">
    <div class="form-col">
        <section class="form-sec">
            <div class="form-sec__hdr">Product Information</div>
            <div class="form-sec__body">
                <div class="form-field">
                    <label for="name">Name</label>
                    <input id="name" name="name" value="{{ old('name', $product->name) }}" required />
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-field--row">
                    <div class="form-field">
                        <label for="slug">Slug</label>
                        <input id="slug" name="slug" value="{{ old('slug', $product->slug) }}" placeholder="Generated from name" />
                        @error('slug') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-field">
                        <label for="sku">SKU</label>
                        <input id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required />
                        @error('sku') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="form-field">
                    <label for="short_description">Short Description</label>
                    <input id="short_description" name="short_description" value="{{ old('short_description', $product->short_description) }}" />
                    @error('short_description') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-field">
                    <label for="full_description">Description</label>
                    <textarea id="full_description" name="full_description" rows="7">{{ old('full_description', $product->full_description) }}</textarea>
                    @error('full_description') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section class="form-sec">
            <div class="form-sec__hdr">Images</div>
            <div class="form-sec__body">
                @isset($product->id)
                    @if ($product->images->isNotEmpty())
                        <div class="img-grid admin-img-grid">
                            @foreach ($product->images as $image)
                                <div class="img-existing">
                                    <img src="{{ $image->path }}" alt="{{ $image->alt_text ?? $product->name }}" />
                                    <label class="img-existing__primary">
                                        <input type="radio" name="primary_image_id" value="{{ $image->id }}" @checked(old('primary_image_id', $product->primaryImage?->id) == $image->id) />
                                        Primary
                                    </label>
                                    <button type="submit" form="delete-image-{{ $image->id }}" class="img-existing__rm" data-confirm="Remove this image?"><i class="bi bi-x"></i></button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endisset

                <label class="img-slot admin-upload-slot">
                    <i class="bi bi-cloud-arrow-up"></i>
                    Upload images
                    <input type="file" name="images[]" accept="image/*" multiple />
                </label>
                @error('images') <p class="form-error">{{ $message }}</p> @enderror
                @error('images.*') <p class="form-error">{{ $message }}</p> @enderror
            </div>
        </section>
    </div>

    <aside class="form-col">
        <section class="form-sec">
            <div class="form-sec__hdr">Publishing</div>
            <div class="form-sec__body">
                <div class="form-field">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="select-arrow">
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" @selected(old('status', $product->status) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <label class="form-check-wwe">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured)) />
                    Featured product
                </label>
                <div class="form-field">
                    <label for="published_at">Published At</label>
                    <input id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', optional($product->published_at)->format('Y-m-d\TH:i')) }}" />
                    @error('published_at') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section class="form-sec">
            <div class="form-sec__hdr">Catalogue</div>
            <div class="form-sec__body">
                <div class="form-field">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" class="select-arrow" required>
                        <option value="">Select category...</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id) === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-field">
                    <label for="school">School / Brand</label>
                    <select id="school" name="school" class="select-arrow">
                        @foreach ($schools as $key => $label)
                            <option value="{{ $key }}" @selected(old('school', $product->school) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('school') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-field">
                    <label for="rarity">Rarity</label>
                    <select id="rarity" name="rarity" class="select-arrow">
                        @foreach ($rarities as $key => $label)
                            <option value="{{ $key }}" @selected(old('rarity', $product->rarity) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('rarity') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section class="form-sec">
            <div class="form-sec__hdr">Price & Stock</div>
            <div class="form-sec__body">
                <div class="form-field--row">
                    <div class="form-field">
                        <label for="price">Price</label>
                        <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $product->price) }}" required />
                        @error('price') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-field">
                        <label for="compare_price">Compare</label>
                        <input id="compare_price" name="compare_price" type="number" min="0" step="0.01" value="{{ old('compare_price', $product->compare_price) }}" />
                        @error('compare_price') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="form-field--row">
                    <div class="form-field">
                        <label for="stock">Stock</label>
                        <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', $product->stock) }}" required />
                        @error('stock') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-field">
                        <label for="low_stock_threshold">Low Stock</label>
                        <input id="low_stock_threshold" name="low_stock_threshold" type="number" min="0" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" required />
                        @error('low_stock_threshold') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="form-field">
                    <label for="weight">Weight</label>
                    <input id="weight" name="weight" type="number" min="0" step="0.01" value="{{ old('weight', $product->weight) }}" />
                    @error('weight') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <div class="form-actions-sticky">
            <button type="submit" class="btn-base btn-gold btn-full"><i class="bi bi-check-lg"></i> {{ $submitLabel }}</button>
            <a href="{{ route('admin.products.index') }}" class="btn-base btn-outline-gold btn-full">Cancel</a>
        </div>
    </aside>
</div>
