@props(['var', 'method', 'title' => 'Items'])

<div x-data="{ open: false }" class="position-relative d-inline-block">
    <!-- Compact Trigger -->
    <button
        type="button"
        class="btn btn-link p-0 text-decoration-none"
        @click="open = !open"
        @click.outside="open = false">
        <i class="bi bi-collection text-primary me-1"></i>
        <span class="text-primary fw-semibold">{{ $var->products->count() }}</span>
        <small class="text-muted ms-1">{{ $var->products->count() === 1 ? 'item' : 'items' }}</small>
    </button>

    <!-- Popover -->
    <div
        x-show="open"
        x-transition
        class="position-absolute bg-white border rounded shadow-lg"
        style="display: none; min-width: 350px; max-width: 500px; z-index: 1050; left: 0; top: calc(100% + 5px);"
        @click.outside="open = false">
        
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom bg-primary bg-opacity-10">
            <strong class="text-dark small">
                <i class="bi bi-list-check me-1"></i>{{ $title }}
            </strong>
            <button 
                type="button" 
                class="btn-close btn-sm" 
                @click="open = false"
                style="font-size: 0.7rem;"></button>
        </div>

        <!-- Items List -->
        <div class="p-2" style="max-height: 300px; overflow-y: auto;">
            @php
                $items = $var->products->pluck($method)->filter();
            @endphp

            @forelse ($items as $index => $item)
                <div class="p-2 mb-1 rounded hover-bg-light border-bottom" style="transition: background-color 0.2s;">
                    <div class="d-flex align-items-start">
                        <span class="badge bg-light text-dark me-2" style="font-size: 0.7rem;">{{ $index + 1 }}</span>
                        <small class="text-dark flex-grow-1">{{ Str::limit($item, 80) }}</small>
                        
                        @if(strlen($item) > 80)
                            <div x-data="{ tooltip: false }" class="position-relative">
                                <button 
                                    type="button" 
                                    class="btn btn-link btn-sm p-0 text-primary"
                                    @mouseenter="tooltip = true"
                                    @mouseleave="tooltip = false"
                                    @click.prevent="alert(@js($item))">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                
                                <!-- Tooltip -->
                                <div 
                                    x-show="tooltip"
                                    x-transition
                                    class="position-absolute bg-dark text-white rounded px-2 py-1"
                                    style="display: none; right: 100%; top: -5px; white-space: nowrap; font-size: 0.7rem; z-index: 1060;">
                                    Click to view full text
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-3">
                    <i class="bi bi-inbox"></i>
                    <p class="mb-0 small mt-1">No items</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        @if($items->count() > 0)
        <div class="px-3 py-2 border-top bg-light">
            <small class="text-muted">
                <i class="bi bi-check-circle text-success me-1"></i>
                {{ $items->count() }} total
            </small>
        </div>
        @endif
    </div>
</div>

<style>
    .hover-bg-light:hover {
        background-color: #f8f9fa;
    }
</style>