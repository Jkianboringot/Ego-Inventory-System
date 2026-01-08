<div class="h-100 d-flex flex-column">

    {{-- HEADER --}}
    <div class="border-bottom px-3 py-2">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="fw-bold fs-5 d-flex align-items-center">
                <i class="bi bi-stickies-fill text-primary me-2"></i> Notes
            </div>

            <div class="d-flex align-items-center gap-2">
                @if ($editing)
                    <button wire:click="save" class="btn btn-success btn-sm">
                        <i class="bi bi-check2"></i> Save
                    </button>
                    <button wire:click="$set('editing', false)" class="btn btn-secondary btn-sm">
                        <i class="bi bi-x"></i> Cancel
                    </button>
                @else
                    <button wire:click="addNote" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-plus-circle"></i> Add
                    </button>
                    <button wire:click="deleteAllNote" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash"></i> Clear All
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- BODY (fills remaining card space) --}}
    <div class="flex-grow-1 p-3 overflow-auto">

        @if ($editing)
            <textarea
                wire:model.defer="content"
                class="form-control bg-white border-secondary text-dark"
                rows="3"
                placeholder="Write your note here..."></textarea>

            @error('content')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        @else

            @if ($notes->isEmpty())
                <div class="text-muted">No sticky notes yet.</div>
            @else
                <div class="d-flex flex-wrap gap-5">
                    @foreach ($notes->groupBy('section') as $section => $group)
                        <div class="flex-grow-1">
                            <ul class="ps-3 mb-0 text-dark small">
                                @foreach ($group as $note)
                                    <li class="mb-1">
                                        {!! nl2br(e($note->content)) !!}
                                        <div class="d-inline-flex gap-1 ms-2">
                                            <button
                                                wire:click.stop="edit({{ $note->id }})"
                                                class="btn btn-outline-primary btn-sm py-0 px-1">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button
                                                wire:click.stop="deleteNote({{ $note->id }})"
                                                class="btn btn-outline-danger btn-sm py-0 px-1">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </li>
                                    <hr>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @endif

        @endif

    </div>
</div>
