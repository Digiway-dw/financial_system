<?php

namespace App\Livewire\Branches;

use App\Application\UseCases\CreateBranch;
use App\Models\Domain\Entities\Branch;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Gate;

class Create extends Component
{
    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('nullable|string|max:1000')]
    public $description = '';

    #[Validate('required|string|max:255')]
    public $location = '';

    #[Validate('required|string|max:255|unique:branches,branch_code|regex:/^[A-Z]{2}[0-9]{3}$/')]
    public $branch_code = '';

    #[Validate('required|numeric|min:0')]
    public $safe_initial_balance;

    #[Validate('nullable|string|max:1000')]
    public $safe_description = '';

    private CreateBranch $createBranchUseCase;

    public function boot(CreateBranch $createBranchUseCase)
    {
        $this->createBranchUseCase = $createBranchUseCase;
    }

    public function mount()
    {
        Gate::authorize('create-branches');
        $this->generateBranchCode();
    }

    public function generateBranchCode()
    {
        do {
            // Generate 2 random uppercase letters + 3 random digits
            $letters = chr(rand(65, 90)) . chr(rand(65, 90)); // A-Z
            $numbers = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT); // 000-999
            $code = $letters . $numbers;
        } while (Branch::where('branch_code', $code)->exists());

        $this->branch_code = $code;
    }

    public function regenerateBranchCode()
    {
        $this->generateBranchCode();
    }

    public function createBranch()
    {
        $this->validate();

        try {
            $this->createBranchUseCase->execute([
                'name' => $this->name,
                'description' => $this->description,
                'location' => $this->location,
                'branch_code' => $this->branch_code,
                // Safe fields
                'safe_initial_balance' => $this->safe_initial_balance,
                'safe_description' => $this->safe_description,
            ]);

            session()->flash('message', 'Branch created successfully.');
            $this->redirect(route('branches.index'), navigate: true); // Redirect after successful creation
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create branch: ' . $e->getMessage());
        }
    }

    protected function messages()
    {
        return [
            'branch_code.regex' => 'Branch code must be exactly 2 uppercase letters followed by 3 digits (e.g., AB123).',
            'branch_code.unique' => 'This branch code is already taken. Please use a different code.',
            'branch_code.required' => 'Branch code is required.',
        ];
    }

    public function render()
    {
        return view('livewire.branches.create');
    }
}
