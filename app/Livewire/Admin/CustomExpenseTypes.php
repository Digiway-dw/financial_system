<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\CustomExpenseType;
use Illuminate\Support\Facades\Auth;

class CustomExpenseTypes extends Component
{
    public $customTypes = [];
    public $showDeleteModal = false;
    public $typeToDelete = null;

    public function mount()
    {
        $this->loadCustomTypes();
    }

    public function loadCustomTypes()
    {
        $this->customTypes = CustomExpenseType::orderBy('usage_count', 'desc')
            ->get()
            ->toArray();
    }

    public function deleteType($typeId)
    {
        $this->typeToDelete = $typeId;
        $this->showDeleteModal = true;
    }

    public function confirmDelete()
    {
        if ($this->typeToDelete) {
            $type = CustomExpenseType::find($this->typeToDelete);
            if ($type) {
                $type->delete();
                session()->flash('message', 'تم حذف نوع المصروف المخصص بنجاح.');
            }
        }
        
        $this->showDeleteModal = false;
        $this->typeToDelete = null;
        $this->loadCustomTypes();
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->typeToDelete = null;
    }

    public function render()
    {
        return view('livewire.admin.custom-expense-types');
    }
}
