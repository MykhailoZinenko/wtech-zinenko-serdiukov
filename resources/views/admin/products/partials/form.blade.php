@php
    $isEdit = isset($product->id);
    $schools = [
        'none' => '— None —',
        'wolf' => 'Wolf School', 'griffin' => 'Griffin School', 'cat' => 'Cat School',
        'bear' => 'Bear School', 'viper' => 'Viper School', 'manticore' => 'Manticore School',
        'ofieri' => 'Ofieri', 'toussaint' => 'Toussaint',
    ];
    $rarities = [
        'common' => 'Common', 'new' => 'New', 'limited' => 'Limited',
        'rare' => 'Rare', 'legendary' => 'Legendary',
    ];
    $rarityBadge = [
        'common' => 'badge--grey', 'new' => 'badge--grey', 'limited' => 'badge--blue',
        'rare' => 'badge--orange', 'legendary' => 'badge--gold',
    ];
@endphp

<div class="form-grid">
    <div class="form-col">
        <section class="form-sec">
            <div class="form-sec__hdr">Product Info</div>
            <div class="form-sec__body">
                <div class="form-field">
                    <label for="name">Name <span class="lbl-req">*</span></label>
                    <input id="name" name="name" value="{{ old('name', $product->name) }}" placeholder="e.g. Aerondight" required />
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-field">
                    <label for="short_description">Short Description <span class="lbl-opt">(shown on cards)</span></label>
                    <input id="short_description" name="short_description" value="{{ old('short_description', $product->short_description) }}" placeholder="One sentence about this item…" />
                    @error('short_description') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-field">
                    <label for="full_description">Full Description <span class="lbl-req">*</span></label>
                    <textarea id="full_description" name="full_description" rows="7" placeholder="Detailed lore, materials, specifications…" required>{{ old('full_description', $product->full_description) }}</textarea>
                    @error('full_description') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section class="form-sec">
            <div class="form-sec__hdr">
                Product Images
                @if ($isEdit && $product->images->count())
                    <span class="form-hint">{{ $product->images->count() }} of 5 slots used</span>
                @endif
            </div>
            <div class="form-sec__body">
                <p class="form-hint">Upload images. First image is the main photo on product cards.</p>

                @php $slotsUsed = $isEdit ? $product->images->count() : 0; @endphp
                <div class="img-grid">
                    @if ($isEdit)
                        @foreach ($product->images as $image)
                            <div class="img-existing">
                                <img src="{{ $image->path }}" alt="{{ $image->alt_text ?? $product->name }}" />
                                <label class="img-existing__primary">
                                    <input type="radio" name="primary_image_id" value="{{ $image->id }}" @checked(old('primary_image_id', $product->primaryImage?->id) == $image->id) />
                                    Primary
                                </label>
                                <button type="button" class="img-existing__rm" onclick="sessionStorage.setItem('scrollY',String(window.scrollY));sessionStorage.setItem('scrollUrl',location.pathname+location.search);document.getElementById('delete-image-{{ $image->id }}').submit()"><i class="bi bi-x"></i></button>
                            </div>
                        @endforeach
                    @endif
                    @for ($i = $slotsUsed; $i < 5; $i++)
                        <label class="img-slot">
                            <i class="bi {{ $i === 0 && $slotsUsed === 0 ? 'bi-camera-fill' : 'bi-plus-lg' }}"></i>
                            {{ $i === 0 && $slotsUsed === 0 ? 'Main Photo' : 'Add Photo' }}
                            <input type="file" name="images[]" accept="image/*" />
                        </label>
                    @endfor
                </div>
                @error('images') <p class="form-error">{{ $message }}</p> @enderror
                @error('images.*') <p class="form-error">{{ $message }}</p> @enderror
            </div>
        </section>
    </div>

    <aside class="form-col">
        <section class="form-sec">
            <div class="form-sec__hdr">Pricing &amp; Inventory</div>
            <div class="form-sec__body">
                <div class="form-field">
                    <label for="price">Price (Crowns) <span class="lbl-req">*</span></label>
                    <input id="price" name="price" type="number" min="0" step="1" value="{{ old('price', $product->price) }}" placeholder="e.g. 4500" required />
                    @error('price') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-field">
                    <label for="compare_price">Compare Price <span class="lbl-opt">(crossed-out)</span></label>
                    <input id="compare_price" name="compare_price" type="number" min="0" step="1" value="{{ old('compare_price', $product->compare_price) }}" />
                    @error('compare_price') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-field">
                    <label for="stock">Stock Quantity <span class="lbl-req">*</span></label>
                    <input id="stock" name="stock" type="number" min="0" step="1" value="{{ old('stock', $product->stock) }}" placeholder="e.g. 12" required />
                    @error('stock') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <label class="form-check-wwe">
                    <input type="checkbox" name="is_limited_edition" value="1" @checked(old('is_limited_edition', $product->is_limited_edition)) />
                    Mark as Limited Edition
                </label>
                <label class="form-check-wwe">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured)) />
                    Featured product
                </label>
            </div>
        </section>

        <section class="form-sec">
            <div class="form-sec__hdr">Category &amp; Attributes</div>
            <div class="form-sec__body">
                <div class="form-field">
                    <label for="category_id">Category <span class="lbl-req">*</span></label>
                    <select id="category_id" name="category_id" class="select-arrow" required>
                        <option value="" disabled @selected(!old('category_id', $product->category_id))>Select category…</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id) === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-field">
                    <label for="rarity">Rarity <span class="lbl-req">*</span></label>
                    <select id="rarity" name="rarity" class="select-arrow" required>
                        <option value="" disabled @selected(!old('rarity', $product->rarity))>Select rarity…</option>
                        @foreach ($rarities as $key => $label)
                            <option value="{{ $key }}" @selected(old('rarity', $product->rarity) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('rarity') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-field">
                    <label for="school">School / Brand <span class="lbl-opt">(optional)</span></label>
                    <select id="school" name="school" class="select-arrow">
                        @foreach ($schools as $key => $label)
                            <option value="{{ $key }}" @selected(old('school', $product->school) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('school') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section class="form-sec">
            <div class="form-sec__hdr">Visibility</div>
            <div class="form-sec__body">
                <div class="form-field">
                    <label for="status">Listing Status</label>
                    <select id="status" name="status" class="select-arrow">
                        <option value="active" @selected(old('status', $product->status) === 'active')>Active — visible in shop</option>
                        <option value="draft" @selected(old('status', $product->status) === 'draft')>Draft — hidden from shop</option>
                        <option value="archived" @selected(old('status', $product->status) === 'archived')>Archived</option>
                    </select>
                    @error('status') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <div class="form-actions-sticky">
            <button type="submit" class="btn-base btn-gold btn-full"><i class="bi bi-check-lg"></i> {{ $submitLabel }}</button>
            <a href="{{ route('admin.products.index') }}" class="btn-base btn-outline-gold btn-full"><i class="bi bi-x-lg"></i> {{ $isEdit ? 'Cancel' : 'Discard' }}</a>
        </div>
    </aside>
</div>
