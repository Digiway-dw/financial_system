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
    public $network = 'Vodafone';
    public $status = 'active';
    public $branchId = '';

    private LineRepository $lineRepository;
    private UpdateLine $updateLineUseCase;
    private BranchRepository $branchRepository;

    public Collection $branches;

    protected function rules(): array
    {
        return [
            'mobileNumber' => [
                'required',
                'string',
                'max:20',
                Rule::unique('lines', 'mobile_number')->ignore($this->lineId),
            ],
            'currentBalance' => 'required|numeric|min:0',
            'dailyLimit' => 'required|numeric|min:0',
            'monthlyLimit' => 'required|numeric|min:0',
            'network' => 'required|in:vodafone,orange,etisalat,we,fawry',
            'status' => 'required|string|in:active,inactive',
            'branchId' => 'required|exists:branches,id',
        ];
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
            $this->currentBalance = $this->line->current_balance;
            $this->dailyLimit = $this->line->daily_limit;
            $this->monthlyLimit = $this->line->monthly_limit;
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
            $this->updateLineUseCase->execute(
                $this->lineId,
                [
                    'mobile_number' => $this->mobileNumber,
                    'current_balance' => (float) $this->currentBalance,
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
