@props(['title','route'])

<div class="card-header">
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="text-white fw-semibold mb-0">{{ $title }} list</h5>
        <form action="{{ route('admin.'.$route.'.create') }}" onsubmit="showLoading(event)">
            <button
                type="submit"
                class="btn-inv-secondary"
                id="createBtn">
                <span id="btnText">
                    <i class="bi bi-plus-circle me-1"></i>
                    Create
                </span>
                <span id="btnLoading" style="display:none;">
                    <i class="bi bi-arrow-repeat spin me-1"></i>
                    Loading...
                </span>
            </button>
        </form>
    </div>
</div>

<script>
    function showLoading(e) {
        const btnText = document.getElementById('btnText');
        const btnLoading = document.getElementById('btnLoading');
        btnText.style.display = 'none';
        btnLoading.style.display = 'inline-block';
    }
</script>

<style>
    .spin {
        display: inline-block;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>