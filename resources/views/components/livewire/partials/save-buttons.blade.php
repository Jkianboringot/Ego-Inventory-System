<button 
    type="submit"
    class="btn btn-dark text-white position-relative"
    wire:loading.attr="disabled"
    wire:target="save"
>
    {{-- Normal state --}}
    <span wire:loading.remove wire:target="save">
        <i class="bi bi-check-circle me-1"></i>
        Save
    </span>

    {{-- Loading state --}}
    <span wire:loading wire:target="save">
        <i class="bi bi-arrow-repeat spin me-1"></i>
        Saving...
    </span>
</button>

<style>
    .spin {
        display: inline-block;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
