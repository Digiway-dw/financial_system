<?php

namespace App\Livewire\AuditLog;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
    use WithPagination;

    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $search = '';
    public $logName = '';
    public $eventType = '';
    public $causerType = '';
    public $subjectType = '';

    protected $queryString = ['search', 'logName', 'eventType', 'causerType', 'subjectType', 'sortField', 'sortDirection'];

    public function mount()
    {
        Gate::authorize('view-audit-log');
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function resetFilters()
    {
        $this->reset(['search', 'logName', 'eventType', 'causerType', 'subjectType']);
    }

    public function render()
    {
        $query = Activity::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('description', 'like', '%' . $this->search . '%')
                  ->orWhere('log_name', 'like', '%' . $this->search . '%')
                  ->orWhere('event', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->logName) {
            $query->where('log_name', $this->logName);
        }

        if ($this->eventType) {
            $query->where('event', $this->eventType);
        }

        if ($this->causerType) {
            $query->where('causer_type', $this->causerType);
        }

        if ($this->subjectType) {
            $query->where('subject_type', $this->subjectType);
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        $activities = $query->paginate(10);

        $logNames = Activity::select('log_name')->distinct()->pluck('log_name');
        $eventTypes = Activity::select('event')->distinct()->pluck('event');
        $causerTypes = Activity::select('causer_type')->distinct()->pluck('causer_type');
        $subjectTypes = Activity::select('subject_type')->distinct()->pluck('subject_type');

        return view('livewire.audit-log.index', [
            'activities' => $activities,
            'logNames' => $logNames,
            'eventTypes' => $eventTypes,
            'causerTypes' => $causerTypes,
            'subjectTypes' => $subjectTypes,
        ]);
    }
} 