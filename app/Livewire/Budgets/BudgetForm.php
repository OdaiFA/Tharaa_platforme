<?php

namespace App\Livewire\Budgets;

use App\Models\Budget;
use App\Models\Category;
use App\Repositories\BudgetRepository;
use Livewire\Component;

class BudgetForm extends Component
{
    public ?int $budgetId = null;

    public string $name = '';

    public string $total_amount = '';

    public string $start_date;

    public string $end_date;

    public string $alert_percentage = '80';

    public string $currency = 'SAR';

    public bool $is_active = true;

    /** @var array<int, array{category_id: string, limit_amount: string, alert_percentage: string}> */
    public array $categories = [];

    public function mount(?int $budgetId = null): void
    {
        $this->start_date = now()->startOfMonth()->format('Y-m-d');
        $this->end_date = now()->endOfMonth()->format('Y-m-d');

        if ($budgetId) {
            $budget = Budget::findOrFail($budgetId);
            $this->authorize('update', $budget);

            $this->budgetId = $budget->id;
            $this->name = $budget->name;
            $this->total_amount = (string) $budget->total_amount;
            $this->start_date = $budget->start_date->format('Y-m-d');
            $this->end_date = $budget->end_date->format('Y-m-d');
            $this->alert_percentage = (string) $budget->alert_percentage;
            $this->is_active = $budget->is_active;
        } else {
            $this->name = 'ميزانية ' . now()->translatedFormat('F Y');
            $this->currency = auth()->user()->currency;
        }
    }

    public function addCategoryRow(): void
    {
        $this->categories[] = ['category_id' => '', 'limit_amount' => '', 'alert_percentage' => '80'];
    }

    public function removeCategoryRow(int $index): void
    {
        unset($this->categories[$index]);
        $this->categories = array_values($this->categories);
    }

    protected function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'total_amount' => ['required', 'numeric', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'alert_percentage' => ['required', 'integer', 'min:1', 'max:100'],
        ];

        if (! $this->budgetId) {
            $rules['currency'] = ['required', 'string', 'size:3'];
            $rules['categories'] = ['nullable', 'array'];
            $rules['categories.*.category_id'] = ['required', 'exists:categories,id'];
            $rules['categories.*.limit_amount'] = ['required', 'numeric', 'min:1'];
            $rules['categories.*.alert_percentage'] = ['nullable', 'integer', 'min:1', 'max:100'];
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'اسم الميزانية مطلوب',
            'total_amount.required' => 'إجمالي الميزانية مطلوب',
            'total_amount.min' => 'إجمالي الميزانية يجب أن يكون أكبر من صفر',
            'start_date.required' => 'تاريخ البداية مطلوب',
            'end_date.required' => 'تاريخ النهاية مطلوب',
            'end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',
            'alert_percentage.required' => 'نسبة التنبيه مطلوبة',
            'alert_percentage.between' => 'نسبة التنبيه يجب أن تكون بين 1 و 100',
            'currency.size' => 'رمز العملة يجب أن يكون 3 أحرف',
            'categories.*.category_id.required' => 'التصنيف مطلوب',
            'categories.*.limit_amount.required' => 'حد التصنيف مطلوب',
            'categories.*.limit_amount.min' => 'حد التصنيف يجب أن يكون أكبر من صفر',
        ];
    }

    public function save(BudgetRepository $budgets)
    {
        $validated = $this->validate();

        if (! $this->budgetId) {
            $categoryIds = array_filter(array_column($validated['categories'] ?? [], 'category_id'));
            if (count($categoryIds) !== count(array_unique($categoryIds))) {
                $this->addError('categories', 'لا يمكن تكرار نفس التصنيف أكثر من مرة');

                return;
            }
        }

        if ($this->budgetId) {
            $budget = Budget::findOrFail($this->budgetId);
            $this->authorize('update', $budget);
            $budgets->update($budget, array_merge($validated, ['is_active' => $this->is_active]));
            session()->flash('success', 'تم تحديث الميزانية بنجاح');
        } else {
            $categories = $validated['categories'] ?? [];
            unset($validated['categories']);

            $budget = $budgets->create(array_merge($validated, ['user_id' => auth()->id()]));

            foreach ($categories as $category) {
                $budget->budgetCategories()->create([
                    'category_id' => $category['category_id'],
                    'limit_amount' => $category['limit_amount'],
                    'alert_percentage' => $category['alert_percentage'] !== '' ? $category['alert_percentage'] : 80,
                ]);
            }

            session()->flash('success', 'تم إنشاء الميزانية بنجاح');
        }

        return redirect()->route('budgets.index');
    }

    public function render()
    {
        return view('livewire.budgets.budget-form', [
            'expenseCategories' => Category::query()->expense()->orderBy('name')->get(),
        ]);
    }
}
