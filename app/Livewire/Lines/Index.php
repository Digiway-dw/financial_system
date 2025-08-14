<?php

namespace App\Livewire\Lines;

use App\Application\UseCases\ListLines;
use App\Application\UseCases\DeleteLine;
use App\Application\UseCases\ToggleLineStatus;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public $sortField = 'mobile_number';
    public $sortDirection = 'asc';
    public array $lines = [];
    public $number = ''; // Filter by line number
    public $serialNumber = ''; // Filter by serial number

    private ListLines $listLinesUseCase;
    private DeleteLine $deleteLineUseCase;
    private ToggleLineStatus $toggleLineStatusUseCase;

    public function boot(ListLines $listLinesUseCase, DeleteLine $deleteLineUseCase, ToggleLineStatus $toggleLineStatusUseCase)
    {
        $this->listLinesUseCase = $listLinesUseCase;
        $this->deleteLineUseCase = $deleteLineUseCase;
        $this->toggleLineStatusUseCase = $toggleLineStatusUseCase;
    }

    public function mount()
    {
        Gate::authorize('view-lines');
        $this->loadLines();
    }

    public function loadLines()
    {
        $user = Auth::user();

        $lines = $this->listLinesUseCase->execute([
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ]);

        // Filter lines by branch for agents (non-admin, non-supervisor users)
        if ($user && $user->branch_id) {
            // Use Gate to check if user has admin permissions
            // If they don't have manage-sim-lines permission and are not an auditor, they're likely an agent
            if (!Gate::allows('manage-sim-lines') && !$user->hasRole(\App\Constants\Roles::AUDITOR)) {
                $userBranchId = $user->branch_id;
                $lines = array_filter($lines, function ($line) use ($userBranchId) {
                    return isset($line['branch_id']) && $line['branch_id'] == $userBranchId;
                });
            }
        }

        // Filter by line number if provided
        if (!empty($this->number)) {
            $search = ltrim($this->number, '+');
            $lines = array_filter($lines, function ($line) use ($search) {
                $lineNumber = isset($line['mobile_number']) ? ltrim($line['mobile_number'], '+') : '';
                return stripos($lineNumber, $search) !== false;
            });
        }
        // Filter by serial number if provided
        if (!empty($this->serialNumber)) {
            $searchSerial = $this->serialNumber;
            $lines = array_filter($lines, function ($line) use ($searchSerial) {
                $serial = isset($line['serial_number']) ? $line['serial_number'] : '';
                return stripos($serial, $searchSerial) !== false;
            });
        }

        // Add color classes for each line based on remaining amounts
        foreach ($lines as &$line) {
            $line['daily_remaining_class'] = '';
            if (
                isset($line['daily_remaining'], $line['status']) &&
                $line['daily_remaining'] <= 0 &&
                $line['status'] === 'active'
            ) {
                $line['daily_remaining_class'] = 'bg-red-100 text-red-700 font-bold';
            }
            $line['monthly_remaining_row_class'] = '';
            if (
                isset($line['monthly_remaining'], $line['status']) &&
                $line['monthly_remaining'] <= 0 &&
                $line['status'] === 'active'
            ) {
                $line['monthly_remaining_row_class'] = 'bg-red-50';
            }
        }
        $this->lines = $lines;
    }

    public function filter()
    {
        $this->loadLines();
    }

    public function resetFilter()
    {
    $this->number = '';
    $this->serialNumber = '';
    $this->loadLines();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->loadLines();
    }

    public function deleteLine(string $lineId)
    {
        Gate::authorize('manage-sim-lines');
        try {
            $this->deleteLineUseCase->execute($lineId);
            session()->flash('message', 'Line deleted successfully.');
            $this->loadLines();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete line: ' . $e->getMessage());
        }
    }

    public function toggleStatus(string $lineId)
    {
        Gate::authorize('manage-sim-lines');
        try {
            $this->toggleLineStatusUseCase->execute($lineId);
            session()->flash('message', 'Line status updated successfully.');
            $this->loadLines();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update line status: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.lines.index', [
            'lines' => $this->lines,
        ]);
    }
}
