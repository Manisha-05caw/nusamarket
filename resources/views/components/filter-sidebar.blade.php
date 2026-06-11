{{-- components/filter-sidebar.blade.php --}}
<form method="GET" action="{{ request()->url() }}" id="filterForm">
    @foreach(request()->except(['page','min_price','max_price','rating','location']) as $key => $val)
    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
    @endforeach

    {{-- Kategori --}}
    <div class="filter-card p-3 mb-2">
        <div class="filter-title mb-2">Kategori</div>
        @foreach(\App\Models\Product::CATEGORIES as $key => $label)
        <div class="form-check">
            <input class="form-check-input" type="radio" name="category"
                   id="cat_{{ $key }}" value="{{ $key }}"
                   {{ request('category') === $key ? 'checked' : '' }}
                   onchange="document.getElementById('filterForm').submit()">
            <label class="form-check-label small" for="cat_{{ $key }}">{{ $label }}</label>
        </div>
        @endforeach
    </div>

    {{-- Range Harga --}}
    <div class="filter-card p-3 mb-2"
         x-data="{
            min: {{ request('min_price', 0) }},
            max: {{ request('max_price', 5000000) }},
            format(v) { return new Intl.NumberFormat('id-ID').format(v); }
         }">
        <div class="filter-title mb-2">Harga</div>
        <div class="d-flex justify-content-between small text-muted mb-1">
            <span>Rp <span x-text="format(min)">0</span></span>
            <span>Rp <span x-text="format(max)">5.000.000</span></span>
        </div>
        <input type="range" class="form-range" name="min_price"
               min="0" max="5000000" step="10000"
               x-model="min" @change="$el.form.submit()">
        <input type="range" class="form-range" name="max_price"
               min="0" max="5000000" step="10000"
               x-model="max" @change="$el.form.submit()">
    </div>

    {{-- Rating --}}
    <div class="filter-card p-3 mb-2">
        <div class="filter-title mb-2">Rating</div>
        @foreach([5,4,3] as $star)
        <div class="form-check">
            <input class="form-check-input" type="radio" name="rating"
                   id="star_{{ $star }}" value="{{ $star }}"
                   {{ request('rating') == $star ? 'checked' : '' }}
                   onchange="document.getElementById('filterForm').submit()">
            <label class="form-check-label small" for="star_{{ $star }}">
                @for($i=1; $i<=$star; $i++)<i class="bi bi-star-fill" style="color:#f5a623;font-size:.8rem"></i>@endfor
                @for($i=$star+1; $i<=5; $i++)<i class="bi bi-star" style="color:#f5a623;font-size:.8rem"></i>@endfor
                ke atas
            </label>
        </div>
        @endforeach
    </div>

    {{-- Lokasi --}}
    <div class="filter-card p-3 mb-2">
        <div class="filter-title mb-2">Lokasi</div>
        <select class="form-select form-select-sm" name="location"
                onchange="document.getElementById('filterForm').submit()">
            <option value="">Semua Lokasi</option>
            @foreach($provinces ?? [] as $prov)
            <option value="{{ $prov }}" {{ request('location') === $prov ? 'selected' : '' }}>
                {{ $prov }}
            </option>
            @endforeach
        </select>
    </div>

    {{-- Reset --}}
    @if(request()->hasAny(['category','min_price','max_price','rating','location']))
    <a href="{{ request()->url() }}{{ request('q') ? '?q='.request('q') : '' }}"
       class="btn btn-outline-secondary btn-sm w-100">
        <i class="bi bi-x-circle me-1"></i>Reset Filter
    </a>
    @endif
</form>
