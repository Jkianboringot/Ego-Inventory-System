@props(['msg','var','search'])
@if ($search && $var->isEmpty())
<div class="text-center py-5">
    <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
    <h5 class="text-inv-primary">No {{$msg}} found for "{{ $search }}"</h5>
    <p class="text-muted">Try adjusting your search terms</p>
</div>

@elseif ($var->isEmpty())
<div class="text-center py-5">
    <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
    <h5 class="text-inv-primary">No {{ $msg }} Record</h5>
    <p class="text-muted">Start by creating a {{$msg}}</p>
</div>
@else
{{ $slot }}
@endif