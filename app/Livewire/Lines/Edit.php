<?php

namespace App\Livewire\Lines;

use App\Application\UseCases\UpdateLine;
use App\Domain\Interfaces\LineRepository;
use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Domain\Interfaces\BranchRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;

class Edit extends Component
{
    public $line;
    public $lineId;

    public $mobileNumber = '';
    public $currentBalance = 0.00;
    public $dailyLimit = 0.00;
    public $monthlyLimit = 0.00;
    public $network = 'vodafone';
    public $status = 'active';
    public $branchId = '';

    private LineRepository $lineRepository;
    private UpdateLine $updateLineUseCase;
    private BranchRepository $branchRepository;

    public Collection $branches;

    protected function rules(): array
    {
        $rules = [
            'mobileNumber' => [
                'required',
                'digits:11',
                Rule::unique('lines', 'mobile_number')->ignore($this->lineId),
            ],
            'dailyLimit' => 'required|numeric|min:0',
            'monthlyLimit' => 'required|numeric|min:0',
            'network' => 'required|in:vodafone,orange,etisalat,we,fawry',
            'status' => 'required|string|in:active,inactive',
            'branchId' => 'required|exists:branches,id',
        ];

        // Only admins can edit balance
        if ($this->canEditBalance()) {
            $rules['currentBalance'] = 'required|numeric|min:0';
        }

        return $rules;
    }

    public function canEditBalance(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole('admin');
    }

    public function boot(LineRepository $lineRepository, UpdateLine $updateLineUseCase, BranchRepository $branchRepository)
    {
        $this->lineRepository = $lineRepository;
        $this->updateLineUseCase = $updateLineUseCase;
        $this->branchRepository = $branchRepository;
    }

    public function mount($lineId)
    {
        $this->authorize('manage-sim-lines');

        $this->lineId = $lineId;
        $this->line = $this->lineRepository->findById($lineId);

        if ($this->line) {
            $this->mobileNumber = $this->line->mobile_number;
            $this->currentBalance = (int) $this->line->current_balance;
            $this->dailyLimit = (int) $this->line->daily_limit;
            $this->monthlyLimit = (int) $this->line->monthly_limit;
            $this->network = $this->line->network;
            $this->status = $this->line->status;
            $this->branchId = $this->line->branch_id;
            $this->branches = $this->branchRepository->all();
        } else {
            abort(404);
        }
    }

    public function updateLine()
    {
        $this->validate();

        try {
            // Preserve original balance for non-admin users
            $originalBalance = (float) $this->line->current_balance;
            $balanceToUpdate = $this->canEditBalance() ? (float) $this->currentBalance : $originalBalance;

            $this->updateLineUseCase->execute(
                $this->lineId,
                [
                    'mobile_number' => $this->mobileNumber,
                    'current_balance' => $balanceToUpdate,
                    'daily_limit' => (float) $this->dailyLimit,
                    'monthly_limit' => (float) $this->monthlyLimit,
                    'network' => $this->network,
                    'status' => $this->status,
                    'branch_id' => $this->branchId,
                ]
            );

            session()->flash('message', 'Line updated successfully.');
            return redirect()->route('lines.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update line: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.lines.edit');
    }
}
