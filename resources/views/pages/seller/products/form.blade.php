{{-- pages/seller/products/form.blade.php --}}
@extends('layouts.app')
@section('title', $product ? 'Edit Produk' : 'Tambah Produk')

@section('content')
<div class="container">
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('seller.products.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-bold mb-0">{{ $product ? 'Edit Produk' : 'Tambah Produk Baru' }}</h4>
    </div>

    <form action="{{ $product ? route('seller.products.update', $product->id) : route('seller.products.store') }}"
          method="POST" enctype="multipart/form-data"
          x-data="{
              variants: {{ $product ? $product->variants->toJson() : '[{size:null,color:null,price:0,stock:0}]' }},
              addVariant() { this.variants.push({size:null,color:null,price:0,stock:0}); },
              removeVariant(i) { if(this.variants.length > 1) this.variants.splice(i,1); },
              basePrice: {{ $product?->base_price ?? 0 }},
              previewImages: [],
              handleImages(e) {
                  this.previewImages = [];
                  Array.from(e.target.files).forEach(f => {
                      const r = new FileReader();
                      r.onload = ev => this.previewImages.push(ev.target.result);
                      r.readAsDataURL(f);
                  });
              }
          }">
        @csrf
        @if($product) @method('PUT') @endif

        <div class="row g-3">

            {{-- KIRI --}}
            <div class="col-lg-8">

                {{-- Info dasar --}}
                <div class="card border mb-3">
                    <div class="card-header bg-white fw-semibold py-2">Informasi Produk</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $product?->name) }}"
                                   placeholder="Contoh: Kemeja Batik Pria Motif Parang" maxlength="255">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select name="category" class="form-select @error('category') is-invalid @enderror">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach(\App\Models\Product::CATEGORIES as $key => $label)
                                <option value="{{ $key }}" {{ old('category', $product?->category) === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                            @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Deskripsi Produk <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                      rows="5" placeholder="Deskripsikan produk kamu secara lengkap...">{{ old('description', $product?->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Harga Dasar (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="base_price" class="form-control @error('base_price') is-invalid @enderror"
                                       x-model="basePrice" value="{{ old('base_price', $product?->base_price) }}"
                                       min="100" placeholder="50000">
                                @error('base_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Diskon (%)</label>
                                <input type="number" name="discount_percent" class="form-control"
                                       value="{{ old('discount_percent', $product?->discount_percent ?? 0) }}"
                                       min="0" max="90" placeholder="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Berat (gram) <span class="text-danger">*</span></label>
                                <input type="number" name="weight_gram" class="form-control @error('weight_gram') is-invalid @enderror"
                                       value="{{ old('weight_gram', $product?->weight_gram ?? 200) }}"
                                       min="1" placeholder="200">
                                @error('weight_gram')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Foto --}}
                <div class="card border mb-3">
                    <div class="card-header bg-white fw-semibold py-2">Foto Produk</div>
                    <div class="card-body">
                        {{-- Existing photos --}}
                        @if($product && $product->images->isNotEmpty())
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @foreach($product->images as $img)
                            <div class="position-relative">
                                <img src="{{ $img->url }}" class="rounded-2 border" width="80" height="80" style="object-fit:cover">
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- Upload area --}}
                        <label class="d-block border-2 border-dashed rounded-3 p-4 text-center cursor-pointer"
                               style="border-style:dashed!important;cursor:pointer"
                               :class="previewImages.length > 0 ? 'border-success' : 'border-secondary'">
                            <input type="file" name="images[]" multiple accept="image/*"
                                   class="d-none" @change="handleImages">
                            <template x-if="previewImages.length === 0">
                                <div>
                                    <i class="bi bi-cloud-upload fs-2 text-muted"></i>
                                    <p class="small text-muted mb-0">Klik untuk upload foto (maks. 5 foto, 3MB/foto)</p>
                                </div>
                            </template>
                            <div x-show="previewImages.length > 0" class="d-flex flex-wrap gap-2 justify-content-center">
                                <template x-for="(src, i) in previewImages" :key="i">
                                    <img :src="src" class="rounded-2" width="72" height="72" style="object-fit:cover">
                                </template>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Variasi --}}
                <div class="card border mb-3">
                    <div class="card-header bg-white fw-semibold py-2 d-flex justify-content-between align-items-center">
                        <span>Variasi Produk (Ukuran & Warna)</span>
                        <button type="button" class="btn btn-outline-primary btn-sm" @click="addVariant">
                            <i class="bi bi-plus me-1"></i>Tambah Variasi
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 mb-2 text-muted small fw-semibold d-none d-md-flex">
                            <div class="col-md-3">Ukuran</div>
                            <div class="col-md-3">Warna</div>
                            <div class="col-md-3">Harga (Rp)</div>
                            <div class="col-md-2">Stok</div>
                            <div class="col-md-1"></div>
                        </div>
                        <template x-for="(v, i) in variants" :key="i">
                            <div class="row g-2 mb-2 align-items-center">
                                <div class="col-6 col-md-3">
                                    <input type="text" :name="`variants[${i}][size]`" class="form-control form-control-sm"
                                           placeholder="S / M / L / XL" x-model="v.size">
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="text" :name="`variants[${i}][color]`" class="form-control form-control-sm"
                                           placeholder="Hitam / Merah..." x-model="v.color">
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="number" :name="`variants[${i}][price]`" class="form-control form-control-sm"
                                           placeholder="Harga" x-model="v.price" :value="v.price || basePrice" min="100">
                                </div>
                                <div class="col-4 col-md-2">
                                    <input type="number" :name="`variants[${i}][stock]`" class="form-control form-control-sm"
                                           placeholder="Stok" x-model="v.stock" min="0">
                                </div>
                                <div class="col-2 col-md-1">
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100"
                                            @click="removeVariant(i)" :disabled="variants.length === 1">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <div class="small text-muted mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Jika produk tidak memiliki variasi, isi satu baris dengan stok dan harga.
                        </div>
                    </div>
                </div>

            </div>

            {{-- KANAN --}}
            <div class="col-lg-4">
                <div class="card border sticky-top" style="top:80px">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Pratinjau Harga</h6>
                        <div class="mb-2">
                            <div class="small text-muted">Harga Jual</div>
                            <div class="price fs-5 fw-bold"
                                 x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(basePrice)">
                                Rp 0
                            </div>
                        </div>
                        <hr>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary-nusa fw-semibold">
                                <i class="bi bi-check-lg me-1"></i>
                                {{ $product ? 'Simpan Perubahan' : 'Tambahkan Produk' }}
                            </button>
                            <a href="{{ route('seller.products.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection
