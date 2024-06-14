<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

final class TimesheetBudgetUsed extends TimesheetConstraint
{
    // same messages, so we can re-use the validation translation!
    public string $messageRate = 'The budget is completely used.';
    public string $messageTime = 'The budget is completely used.';
    public string $messagePermission = 'Sorry, the budget is used up.';
}
