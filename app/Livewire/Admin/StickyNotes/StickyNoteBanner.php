<?php

namespace App\Livewire\Admin\StickyNotes;

use App\Models\StickyNote;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class StickyNoteBanner extends Component
{
    public $content;
    public $editing = false;
    public $editNoteId = null;
    public $notes = [];

    protected $cacheKey = 'sticky_notes';

    public function mount()
    {
        $this->loadNotes();
        $latest = $this->notes->first();
        $this->content = $latest?->content ?? '';
    }

    public function addNote()
    {
        $this->resetForm();
        $this->editing = true;
    }

    public function edit($id)
    {
        $note = $this->notes->firstWhere('id', $id)
            ?? StickyNote::findOrFail($id);

        $this->editNoteId = $note->id;
        $this->content = $note->content;
        $this->editing = true;
    }

    public function save()
    {
        $this->validate([
            'content' => 'required|string|min:5',
        ], [
            'content.required' => 'Note cannot be empty.',
            'content.min' => 'Note must be at least 5 characters.',
        ]);

        if ($this->editing && $this->editNoteId) {
            StickyNote::whereKey($this->editNoteId)
                ->update(['content' => $this->content]);
        } else {
            StickyNote::create(['content' => $this->content]);
        }

        $this->clearCache();
        $this->resetForm();
        $this->loadNotes();
    }

    public function deleteNote($id)
    {
        StickyNote::whereKey($id)->delete();
        $this->clearCache();
        $this->loadNotes();
    }

    public function deleteAllNote()
    {
        StickyNote::truncate();
        $this->clearCache();
        $this->resetForm();
        $this->loadNotes();
    }

    private function resetForm()
    {
        $this->content = '';
        $this->editNoteId = null;
        $this->editing = false;
    }

    private function loadNotes()
    {
        $this->notes = Cache::remember($this->cacheKey, 60, function () {
            return StickyNote::latest()->get();
        });
    }

    private function clearCache()
    {
        Cache::forget($this->cacheKey);
    }

    public function render()
    {
        return view('components.sticky-note-banner', [
            'notes' => $this->notes,
            'editing' => $this->editing,
        ]);
    }
}
