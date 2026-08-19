<?php

namespace App\Livewire\Admin\AgeGroups;

use App\Models\AgeGroup;
use Livewire\Attributes\Validate;
use Livewire\Component;

class AgeGroupsIndex extends Component
{
    #[Validate('required|string|max:50')]
    public string $name = '';

    #[Validate('required|integer|min:0')]
    public ?int $min_age = null;

    #[Validate('required|integer|gte:min_age')]
    public ?int $max_age = null;

    public ?int $editingId = null;

    protected function messages(): array
    {
        return [
            'name.required' => 'اسم الفئة العمرية مطلوب',
            'min_age.required' => 'الحد الأدنى مطلوب',
            'max_age.required' => 'الحد الأقصى مطلوب',
            'max_age.gte' => 'الحد الأقصى يجب أن يكون أكبر من أو يساوي الحد الأدنى',
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->editingId) {
            AgeGroup::findOrFail($this->editingId)->update($validated);
            session()->flash('success', 'تم تحديث الفئة العمرية بنجاح');
        } else {
            AgeGroup::create($validated);
            session()->flash('success', 'تم إنشاء الفئة العمرية بنجاح');
        }

        $this->reset(['name', 'min_age', 'max_age', 'editingId']);
    }

    public function edit(int $id): void
    {
        $ageGroup = AgeGroup::findOrFail($id);

        $this->editingId = $ageGroup->id;
        $this->name = $ageGroup->name;
        $this->min_age = $ageGroup->min_age;
        $this->max_age = $ageGroup->max_age;
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->reset(['name', 'min_age', 'max_age', 'editingId']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.age-groups.age-groups-index', [
            'ageGroups' => AgeGroup::withCount('users')->orderBy('min_age')->get(),
        ]);
    }
}
