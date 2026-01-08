@props(['route','id','var'=>null])






<a href="{{ route('admin.'.$route.'.edit', $id) }}"
    class="btn btn-dark" wire:navigate>
    <i class="bi bi-pencil-square"></i>
</a>
@if ($var && !in_array($var->status,['approved','edit_pending']) )

<button wire:confirm="Are you sure you want to delete this record?" class="btn btn-danger"
    wire:click='delete({{ $id }})'>
    <i class="bi bi-trash"></i>
</button>
@endif