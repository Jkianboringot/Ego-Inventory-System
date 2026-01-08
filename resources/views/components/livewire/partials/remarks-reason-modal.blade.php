@props(['var'])
@if ($var)
 <div x-data="{ open: false }" class="position-relative">
     <button
         type="button"
         class="btn btn-link btn-sm p-0 text-start text-decoration-none"
         @click="open = !open"
         @click.outside="open = false">
         <i class="bi bi-info-circle me-1"></i>
         <small class="text-primary">View</small>
     </button>

     <div
         x-show="open"
         x-transition
         class="position-absolute start-0 bg-white border rounded shadow-lg p-3 mt-1"
         style="display: none; min-width: 350px; max-width: 500px; z-index: 1050;"
         @click.outside="open = false">
         <div class="d-flex justify-content-between align-items-start mb-2">
             <strong class="text-dark">Content</strong>
             <button
                 type="button"
                 class="btn-close btn-sm"
                 @click="open = false"
                 aria-label="Close"></button>
         </div>
         <div class="text-muted small" style="max-height: 200px; overflow-y: auto;">
             {{ $var }}
         </div>
     </div>
 </div>
 @else
 <small class="text-muted">No Content</small>
 @endif