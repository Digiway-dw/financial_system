<?php

namespace App\Livewire\Safes;

use Livewire\Component;
use App\Application\UseCases\CreateSafeToSafeTransfer;
use App\Domain\Interfaces\SafeRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class SafeTransfer extends Component
{
    public $sourceSafeId;
    public $destinationSafeId;
    public $amount;
    public $notes;

    public $safes = [];
    public $sourceSafe = null;
    public $destinationSafe = null;

    protected $rules = [
        'sourceSafeId' => 'required|numeric|exists:safes,id',
        'destinationSafeId' => 'required|numeric|exists:safes,id|different:sourceSafeId',
        'amount' => 'required|numeric|min:1',
        'notes' => 'nullable|string|max:255',
    ];

    public function mount(SafeRepository $safeRepository)
    {
        // Check if user has permission to initiate safe transfers
        Gate::authorize('initiate-safe-transfer');

        $user = Auth::user();

        // Load safes based on user's permissions
        if (Gate::allows('view-all-branches-data')) {
            // Admin and general supervisor can see all safes
            $this->safes = $safeRepository->all();
        } else {
            // Branch manager and agent can only see safes in their branch
            $branchId = $user->branch_id;
            $this->safes = collect($safeRepository->all())->filter(function ($safe) use ($branchId) {
                return $safe->branch_id == $branchId;
            })->toArray();
        }
    }

    public function updatedSourceSafeId()
    {
        if ($this->sourceSafeId) {
            $this->sourceSafe = collect($this->safes)->firstWhere('id', $this->sourceSafeId);
        } else {
            $this->sourceSafe = null;
        }
    }

    public function updatedDestinationSafeId()
    {
        if ($this->destinationSafeId) {
            $this->destinationSafe = collect($this->safes)->firstWhere('id', $this->destinationSafeId);
        } else {
            $this->destinationSafe = null;
        }
    }

    public function transfer(CreateSafeToSafeTransfer $createSafeToSafeTransfer)
    {
        $this->validate();

        try {
            // Create the safe-to-safe transfer
            $transaction = $createSafeToSafeTransfer->execute(
                $this->sourceSafeId,
                $this->destinationSafeId,
                $this->amount,
                Auth::id(),
                $this->notes
            );

            // Determine message based on user role and transaction status
            if ($transaction->status === 'Completed') {
                session()->flash('success', 'Safe-to-safe transfer completed successfully.');
            } else {
                session()->flash('success', 'Safe-to-safe transfer initiated. Waiting for approval.');
            }

            // Reset form
            $this->reset(['sourceSafeId', 'destinationSafeId', 'amount', 'notes']);
            $this->sourceSafe = null;
            $this->destinationSafe = null;

            // Redirect to transaction details
            return redirect()->route('transactions.show', $transaction->id);
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.safes.safe-transfer');
    }
}
