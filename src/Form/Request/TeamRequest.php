<?php

namespace App\Form\Request;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * A request object for Team
 *
 * @package App\Form\Request
 */
class TeamRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100, minMessage: 'Name has to have at least 2 characters!',
        maxMessage: 'Name has to have at most 100 characters!')]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100, minMessage: 'City has to have at least 2 characters!',
        maxMessage: 'City has to have at most 100 characters!')]
    public string $city;

    #[Assert\PositiveOrZero(message: 'Founded year must be positive or zero!')]
    public int $founded;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100, minMessage: 'Stadium has to have at least 2 characters!',
        maxMessage: 'Stadium has to have at most 100 characters!')]
    public string $stadium;

    /**
     * Checks if founded year is not larger than current year
     *
     * @param ExecutionContextInterface $context
     * @return void
     */
    #[Assert\Callback]
    private function validateFounded(ExecutionContextInterface $context): void
    {
        $currentYear = (int) date('Y');
        if ($this->founded > $currentYear) {
            $context->buildViolation("Founded year cannot be in the future!")
                ->atPath('founded')
                ->addViolation();
        }
    }
}