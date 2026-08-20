<?php

namespace App\Livewire\Goals;

use App\Models\Goal;
use App\Repositories\GoalRepository;
use Illuminate\Validation\Rule;
use Livewire\Component;

class GoalForm extends Component
{
    public ?int $goalId = null;

    public string $name = '';

    public string $target_amount = '';

    public string $current_amount = '0';

    public string $currency_code = 'SAR';

    public ?string $deadline = null;

    public string $priority = 'medium';

    public ?string $icon = null;

    public function mount(?int $goalId = null): void
    {
        if ($goalId) {
            $goal = Goal::findOrFail($goalId);
            $this->authorize('update', $goal);

            $this->goalId = $goal->id;
            $this->name = $goal->name;
            $this->target_amount = (string) $goal->target_amount;
            $this->currency_code = $goal->currency_code;
            $this->deadline = optional($goal->deadline)->format('Y-m-d');
            $this->priority = $goal->priority;
            $this->icon = $goal->icon;
        } else {
            $this->currency_code = auth()->user()->currency;
        }
    }

    protected function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'min:0.01'],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'icon' => ['nullable', 'string', 'max:255'],
        ];

        // Currency is fixed at creation and not editable afterward — past
        // contributions were already validated against it, so changing it
        // later would retroactively invalidate that guarantee. Same pattern
        // as AccountForm's create-only initial_balance.
        if (! $this->goalId) {
            $rules['currency_code'] = ['required', 'string', 'size:3'];
        }

        $rules['deadline'] = $this->goalId
            ? ['required', 'date']
            : ['required', 'date', 'after_or_equal:today'];

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'اسم الهدف مطلوب',
            'target_amount.required' => 'المبلغ المستهدف مطلوب',
            'target_amount.min' => 'المبلغ المستهدف يجب أن يكون أكبر من صفر',
            'currency_code.required' => 'عملة الهدف مطلوبة',
            'currency_code.size' => 'رمز العملة يجب أن يكون 3 أحرف',
            'deadline.required' => 'تاريخ الانتهاء مطلوب',
            'deadline.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون اليوم أو في المستقبل',
            'priority.required' => 'الأولوية مطلوبة',
            'priority.in' => 'الأولوية غير صالحة',
        ];
    }

    public function save(GoalRepository $goals)
    {
        $validated = $this->validate();

        if ($this->goalId) {
            $goal = Goal::findOrFail($this->goalId);
            $this->authorize('update', $goal);
            $goals->update($goal, $validated);
            session()->flash('success', 'تم تحديث الهدف بنجاح');
        } else {
            $goals->create(array_merge($validated, [
                'user_id' => auth()->id(),
            ]));
            session()->flash('success', 'تم إنشاء الهدف بنجاح');
        }

        return redirect()->route('goals.index');
    }

    public function render()
    {
        return view('livewire.goals.goal-form');
    }
}
