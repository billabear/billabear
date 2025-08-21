<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Validator\Constraints;

use BillaBear\Entity\CountryTaxRule;
use BillaBear\Repository\CountryTaxRuleRepositoryInterface;
use BillaBear\Validator\Constraints\CountryTaxRuleExists;
use BillaBear\Validator\Constraints\CountryTaxRuleExistsValidator;
use Parthenon\Common\Exception\NoEntityFoundException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class CountryTaxRuleExistsValidatorTest extends TestCase
{
    private CountryTaxRuleRepositoryInterface $countryTaxRuleRepository;
    private ExecutionContextInterface $context;
    private CountryTaxRuleExistsValidator $validator;
    private CountryTaxRuleExists $constraint;

    protected function setUp(): void
    {
        $this->countryTaxRuleRepository = $this->createMock(CountryTaxRuleRepositoryInterface::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->constraint = new CountryTaxRuleExists();

        $this->validator = new CountryTaxRuleExistsValidator($this->countryTaxRuleRepository);
        $this->validator->initialize($this->context);
    }

    public function testValidateWithEmptyValueDoesNothing()
    {
        $this->countryTaxRuleRepository->expects($this->never())->method('findById');
        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate('', $this->constraint);
    }

    public function testValidateWithNullValueDoesNothing()
    {
        $this->countryTaxRuleRepository->expects($this->never())->method('findById');
        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate(null, $this->constraint);
    }

    public function testValidateWithZeroValueDoesNothing()
    {
        $this->countryTaxRuleRepository->expects($this->never())->method('findById');
        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate(0, $this->constraint);
    }

    public function testValidateWithFalseValueDoesNothing()
    {
        $this->countryTaxRuleRepository->expects($this->never())->method('findById');
        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate(false, $this->constraint);
    }

    public function testValidateWithValidCountryTaxRuleIdPassesValidation()
    {
        $countryTaxRule = $this->createMock(CountryTaxRule::class);
        $this->countryTaxRuleRepository->expects($this->once())
            ->method('findById')
            ->with('valid-rule-id')
            ->willReturn($countryTaxRule);

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate('valid-rule-id', $this->constraint);
    }

    public function testValidateWithInvalidCountryTaxRuleIdAddsViolation()
    {
        $this->countryTaxRuleRepository->expects($this->once())
            ->method('findById')
            ->with('invalid-rule-id')
            ->willThrowException(new NoEntityFoundException());

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->expects($this->once())
            ->method('setParameter')
            ->with('{{ string }}', 'invalid-rule-id')
            ->willReturnSelf();
        $violationBuilder->expects($this->once())
            ->method('addViolation');

        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($this->constraint->message)
            ->willReturn($violationBuilder);

        $this->validator->validate('invalid-rule-id', $this->constraint);
    }

    public function testValidateWithCustomConstraintMessage()
    {
        $this->countryTaxRuleRepository->expects($this->once())
            ->method('findById')
            ->with('invalid-rule-id')
            ->willThrowException(new NoEntityFoundException());

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->expects($this->once())
            ->method('setParameter')
            ->with('{{ string }}', 'invalid-rule-id')
            ->willReturnSelf();
        $violationBuilder->expects($this->once())
            ->method('addViolation');

        $customMessage = 'Custom country tax rule not found message';
        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($customMessage)
            ->willReturn($violationBuilder);

        $this->constraint->message = $customMessage;
        $this->validator->validate('invalid-rule-id', $this->constraint);
    }

    public function testValidateWithNumericIdPassesValidation()
    {
        $countryTaxRule = $this->createMock(CountryTaxRule::class);
        $this->countryTaxRuleRepository->expects($this->once())
            ->method('findById')
            ->with(123)
            ->willReturn($countryTaxRule);

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate(123, $this->constraint);
    }

    public function testValidateWithNumericIdAddsViolationWhenNotFound()
    {
        $this->countryTaxRuleRepository->expects($this->once())
            ->method('findById')
            ->with(999)
            ->willThrowException(new NoEntityFoundException());

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->expects($this->once())
            ->method('setParameter')
            ->with('{{ string }}', 999)
            ->willReturnSelf();
        $violationBuilder->expects($this->once())
            ->method('addViolation');

        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($this->constraint->message)
            ->willReturn($violationBuilder);

        $this->validator->validate(999, $this->constraint);
    }
}
