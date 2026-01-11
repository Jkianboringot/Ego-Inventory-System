<div wire:init="loadData" class="p-3">

    <!-- Header with Search -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">Activity History</h5>
        <div style="width: 300px;">
            <input
                type="text"
                wire:model.live.debounce.500ms="search"
                class="form-control"
                placeholder="Search activity...">
        </div>
    </div>

    @if($readyToLoad)

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <tr>
                    <th style="width: 180px;">When</th>
                    <th style="width: 150px;">User</th>
                    <th style="width: 180px;">Action Made</th>
                    <th style="width: 120px;">Type</th>
                    <th style="max-width: 300px;">Details</th>
                    <th style="width: 160px;">IP Address</th>
                </tr>
            </thead>

            <tbody>
                @forelse($logs as $log)
                <tr style="border-bottom: 1px solid #e9ecef;">

                    <!-- Date & Time -->
                    <td>
                        <div style="line-height: 1.3;">
                            <div style="font-weight: 600; color: #212529; font-size: 0.95rem;">
                                {{ $log->created_at->format('M d, Y') }}
                            </div>
                            <div style="color: #6c757d; font-size: 0.85rem;">
                                {{ $log->created_at->format('g:i A') }}
                            </div>
                        </div>
                    </td>

                    <!-- User -->
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.75rem;">
                                {{ strtoupper(substr($log->user?->name ?? 'S', 0, 1)) }}
                            </div>
                            <span style="font-weight: 500;">{{ $log->user?->name ?? 'System' }}</span>
                        </div>
                    </td>

                    <!-- Action with Badge -->
                    <td>
                        @php
                        $actionColors = [
                            'created' => 'success',
                            'updated' => 'primary',
                            'deleted' => 'danger',
                            'viewed' => 'info',
                            'restored' => 'warning',
                        ];
                        $actionText = ucfirst(str_replace('_', ' ', $log->action));
                        $badgeColor = $actionColors[strtolower($log->action)] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $badgeColor }}" style="font-size: 0.85rem; padding: 6px 12px; font-weight: 500;">
                            {{ $actionText }}
                        </span>
                    </td>

                    <!-- Model Type -->
                    <td>
                        <span style="color: #495057; font-size: 0.9rem;">
                            {{ ucfirst(class_basename($log->model)) }}
                        </span>
                    </td>

                    <!-- Changes / Details -->
                    <td style="max-width: 300px; word-break: break-word;">
                        @php
                        $changes = $log->enriched_changes;
                        @endphp

                        @if($changes && is_array($changes) && count($changes) > 0)
                            <div style="max-height: 150px; overflow-y: auto;">
                                @foreach($changes as $key => $value)
                                    @if($key === 'products' && is_array($value))
                                        @foreach($value as $product)
                                            <div style="margin-bottom: 6px; padding: 8px; background-color: #f1f3f5; border-radius: 6px; border-left: 4px solid #0d6efd;">
                                                <div style="font-weight: 700; color: #212529; font-size: 1rem;">
                                                    {{ $product['product_name'] ?? 'N/A' }}
                                                </div>
                                                <div style="color: #212529; font-size: 0.95rem;">
                                                    <strong>Qty: {{ $product['quantity'] ?? '0' }}</strong>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div style="margin-bottom: 6px; padding: 8px; background-color: #f1f3f5; border-radius: 6px; border-left: 4px solid #0d6efd;">
                                            <div style="font-weight: 700; color: #212529; font-size: 1rem;">
                                                {{ ucfirst(str_replace('_', ' ', $key)) }}
                                            </div>
                                            <div style="color: #212529; font-size: 0.95rem;">
                                                @if(is_array($value))
                                                    <code style="font-size: 0.85rem;">{{ json_encode($value) }}</code>
                                                @else
                                                    {{ Str::limit($value, 100) }}
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <span style="color: #495057; font-weight: 600; font-size: 1rem;">No details</span>
                        @endif
                    </td>

                    <!-- IP Address Only -->
                    <td>
                        <span style="color: #495057; font-size: 0.85rem;">{{ $log->ip_address ?? 'Unknown' }}</span>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div style="color: #6c757d;">
                            <svg width="48" height="48" style="opacity: 0.3; margin-bottom: 12px;" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                            </svg>
                            <div style="font-size: 1.1rem; font-weight: 500;">No activity found</div>
                            <div style="font-size: 0.9rem; margin-top: 4px;">Try adjusting your search</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $logs->links() }}
    </div>

    @else
    <x-livewire.partials.loading-modal-form />
    @endif

</div>
