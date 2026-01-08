@props(['search'])
 <input type="text"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Search by product name..."
                    class="form-control mb-3 @if($search) border border-primary @endif">