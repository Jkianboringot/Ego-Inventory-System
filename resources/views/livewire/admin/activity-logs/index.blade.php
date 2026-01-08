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
                    <th>Details</th>
                    <th style="width: 200px;">Location</th>
                </tr>
            </thead>

            <tbody>
                @forelse($logs as $log)
                <tr style="border-bottom: 1px solid #e9ecef;">
                    <!-- Date & Time - Separated for clarity -->
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

                  

                    <!-- Changes -->
                    <td>
                        @php
                        $changes = json_decode($log->changes, true);
                        $isUpdate = strtolower($log->action) === 'updated';
                        @endphp

                        @if(is_array($changes) && count($changes) > 0 && $isUpdate)
                            <div x-data="{ open: false }" class="position-relative d-inline-block">
                                <!-- Compact Trigger -->
                                <button
                                    type="button"
                                    class="btn btn-link p-0 text-decoration-none"
                                    @click="open = !open">
                                    <i class="bi bi-file-text text-primary me-1"></i>
                                    <span class="text-primary fw-semibold">{{ count($changes) }}</span>
                                    <small class="text-muted ms-1">{{ count($changes) === 1 ? 'change' : 'changes' }}</small>
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
                                            <i class="bi bi-list-check me-1"></i>Change Details
                                        </strong>
                                        <button 
                                            type="button" 
                                            class="btn-close btn-sm" 
                                            @click="open = false"
                                            style="font-size: 0.7rem;"></button>
                                    </div>

                                    <!-- Changes List -->
                                    <div class="p-2" style="max-height: 300px; overflow-y: auto;">
                                        @foreach($changes as $key => $value)
                                            <div class="p-2 mb-2 rounded border-start border-3 border-primary" style="background-color: #f8f9fa;">
                                                <div style="font-weight: 600; color: #495057; font-size: 0.85rem; margin-bottom: 4px;">
                                                    {{ ucfirst(str_replace('_', ' ', $key)) }}
                                                </div>
                                                <div style="color: #212529; font-size: 0.85rem; word-break: break-word;">
                                                    @if(is_array($value))
                                                        <pre style="margin: 0; font-size: 0.75rem; white-space: pre-wrap; background-color: #ffffff; padding: 8px; border-radius: 4px;">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                    @else
                                                        {{ Str::limit($value, 100) }}
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Footer -->
                                    <div class="px-3 py-2 border-top bg-light">
                                        <small class="text-muted">
                                            <i class="bi bi-check-circle text-success me-1"></i>
                                            {{ count($changes) }} total
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @elseif(is_array($changes) && count($changes) > 0)
                            <!-- Non-update actions: show inline -->
                            <div style="max-width: 250px;">
                                @foreach($changes as $key => $value)
                                <div style="margin-bottom: 6px; padding: 6px; background-color: #f8f9fa; border-radius: 4px; border-left: 3px solid #0d6efd;">
                                    <div style="font-weight: 600; color: #495057; font-size: 0.8rem; margin-bottom: 2px;">
                                        {{ ucfirst(str_replace('_', ' ', $key)) }}
                                    </div>
                                    <div style="color: #212529; font-size: 0.85rem; word-break: break-word;">
                                        @if(is_array($value))
                                            <code style="font-size: 0.75rem;">{{ json_encode($value) }}</code>
                                        @else
                                            {{ Str::limit($value, 50) }}
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <span style="color: #6c757d; font-style: italic; font-size: 0.9rem;">No details</span>
                        @endif
                    </td>

                    <!-- Location with Icon -->
                    <td>
                        <div style="line-height: 1.4;">
                            <div style="color: #495057; font-size: 0.85rem; margin-bottom: 2px;">
                                 {{ $log->city ?? 'Unknown' }}
                            </div>
                            <div style="color: #6c757d; font-size: 0.8rem;">
                                {{ $log->region ?? 'Unknown' }}, {{ $log->country ?? 'Unknown' }}
                            </div>
                            <div style="color: #adb5bd; font-size: 0.75rem; margin-top: 2px;">
                                IP: {{ $log->ip_address }}
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
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