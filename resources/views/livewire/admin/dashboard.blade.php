<div>

    <!-- Welcome Header Card -->
    <div class="card mb-3 ">
        <div class="card-header rounded-bottom py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <div>
                    <h4 class="mb-1 fw-semibold text-white">
                        Welcome, {{ $user->name }}
                    </h4>
                    <p class=" mb-0 small text-white ">Have a productive day</p>
                </div>

                <div class="text-white  fw-normal d-flex gap-3 align-items-center small">
                    <span>
                        <i class="bi bi-calendar-event me-1"></i>
                        {{ now()->format('l, F j, Y') }}
                    </span>
                    <span class="">•</span>
                    <span>
                        <i class="bi bi-clock me-1"></i>
                        {{ now()->format('h:i A') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky Notes + Notifications -->
    <div class="row g-3 mb-3">
        <div class="col-md-6 col-12">
            <div class="card shadow-sm h-100 ">
                @livewire('admin.sticky-notes.sticky-note-banner')
            </div>
        </div>

        <div class="col-md-6 col-12">
            <div class="card shadow-sm h-100 ">
                @livewire('admin.notifications.alerts-notification')
            </div>
        </div>
    </div>

    @if($op)
    <div class="row g-3">
        <div class="col-12">
            <div class="card ">
                <div class="card-body bg-inv-primary rounded">
                    <div class="row g-3">

                        <div class="col-lg-3 col-md-6 col-12">
                            <a href="{{ route('admin.add-products.index') }}" wire:navigate
                                class="btn btn-inv-primary w-100 py-3 d-flex flex-column align-items-center gap-2">
                                <i class="bi bi-plus-circle fs-3"></i>
                                <span class="fw-semibold">New Arrival</span>
                            </a>
                        </div>

                        <div class="col-lg-3 col-md-6 col-12">
                            <a href="{{ route('admin.sales.index') }}" wire:navigate
                                class="btn btn-inv-success w-100 py-3 d-flex flex-column align-items-center gap-2">
                                <i class="bi bi-cash-stack fs-3"></i>
                                <span class="fw-semibold">Sales</span>
                            </a>
                        </div>

                        <div class="col-lg-3 col-md-6 col-12">
                            <a href="{{ route('admin.orders.index') }}" wire:navigate
                                class="btn btn-inv-warning w-100 py-3 d-flex flex-column align-items-center gap-2">
                                <i class="bi bi-receipt fs-3"></i>
                                <span class="fw-semibold">Orders</span>
                            </a>

                        </div>

                        <div class="col-lg-3 col-md-6 col-12">
                            <a href="{{ route('admin.approvals.approvalcenter') }}" wire:navigate
                                class="btn btn-inv-danger w-100 py-3 d-flex flex-column align-items-center gap-2">
                                <i class="bi bi-shield-check fs-3"></i>
                                <span class="fw-semibold">Approvals</span>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @endif

</div>
</div>