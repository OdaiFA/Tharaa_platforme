<?php

namespace App\Livewire\Goals;

use App\Models\Goal;
use App\Repositories\GoalRepository;
use App\Services\GoalService;
use Livewire\Component;

class GoalsIndex extends Component
{
    public ?int $contributingGoalId = null;

    public string $amount = '';

    public string $account_id = '';

    public ?string $note = null;

    public ?int $confirmingDeleteId = null;

    public function startContribute(int $goalId): void
    {
        $this->contributingGoalId = $goalId;
        $this->reset(['amount', 'account_id', 'note']);
        $this->resetValidation();
    }

    public function cancelContribute(): void
    {
        $this->contributingGoalId = null;
        $this->reset(['amount', 'account_id', 'note']);
        $this->resetValidation();
    }

    public function contribute(GoalService $goalService): void
    {
        $goal = Goal::forUser(auth()->id())->findOrFail($this->contributingGoalId);
        $this->authorize('contribute', $goal);

        $this->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'account_id' => ['nullable', 'exists:accounts,id'],
            'note' => ['nullable', 'string', 'max:1000'],
        ], [
            'amount.required' => 'مبلغ المساهمة مطلوب',
            'amount.numeric' => 'مبلغ المساهمة يجب أن يكون رقماً',
            'amount.min' => 'مبلغ المساهمة يجب أن يكون أكبر من صفر',
            'account_id.exists' => 'الحساب غير موجود',
        ]);

        try {
            $goalService->contribute(
                $goal,
                (float) $this->amount,
                $this->account_id ?: null,
                $this->note,
                null,
            );
        } catch (\DomainException|\InvalidArgumentException $e) {
            // Currency mismatch is about which account was picked, not the
            // amount — route it to the field the user actually needs to fix.
            $isCurrencyMismatch = $e->getMessage() === 'لا يمكن المساهمة بعملة مختلفة عن عملة الهدف';
            $this->addError($isCurrencyMismatch ? 'account_id' : 'amount', $e->getMessage());

            return;
        }

        $this->contributingGoalId = null;
        $this->reset(['amount', 'account_id', 'note']);
        session()->flash('success', 'تمت إضافة المساهمة بنجاح');
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function delete(int $id, GoalRepository $goals): void
    {
        if ($this->confirmingDeleteId !== $id) {
            return;
        }

        $goal = Goal::forUser(auth()->id())->find($id);

        abort_if(! $goal, 404);
        $this->authorize('delete', $goal);

        $goals->delete($goal);
        $this->confirmingDeleteId = null;
        session()->flash('success', 'تم حذف الهدف مع الاحتفاظ بسجل المساهمات');
    }

    public function render(GoalRepository $goals)
    {
        return view('livewire.goals.goals-index', [
            'goals' => $goals->activeForUser(auth()->id()),
            'accounts' => auth()->user()->accounts()->active()->get(),
        ]);
    }
}
