<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Validator\Constraints\Country;

use BillaBear\Entity\Country;
use BillaBear\Repository\CountryRepositoryInterface;
use BillaBear\Validator\Constraints\Country\CountryExists;
use BillaBear\Validator\Constraints\Country\CountryExistsValidator;
use Parthenon\Common\Exception\NoEntityFoundException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class CountryExistsValidatorTest extends TestCase
{
    public function testValidateWithEmptyValueDoesNothing()
    {
        $countryRepository = $this->createMock(CountryRepositoryInterface::class);
        $countryRepository->expects($this->never())->method('getById');

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $constraint = new CountryExists();
        $validator = new CountryExistsValidator($countryRepository);
        $validator->initialize($context);

        $validator->validate('', $constraint);
    }

    public function testValidateWithNullValueDoesNothing()
    {
        $countryRepository = $this->createMock(CountryRepositoryInterface::class);
        $countryRepository->expects($this->never())->method('getById');

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $constraint = new CountryExists();
        $validator = new CountryExistsValidator($countryRepository);
        $validator->initialize($context);

        $validator->validate(null, $constraint);
    }

    public function testValidateWithValidCountryIdPassesValidation()
    {
        $country = $this->createMock(Country::class);
        $countryRepository = $this->createMock(CountryRepositoryInterface::class);
        $countryRepository->expects($this->once())
            ->method('getById')
            ->with('valid-country-id')
            ->willReturn($country);

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $constraint = new CountryExists();
        $validator = new CountryExistsValidator($countryRepository);
        $validator->initialize($context);

        $validator->validate('valid-country-id', $constraint);
    }

    public function testValidateWithInvalidCountryIdAddsViolation()
    {
        $countryRepository = $this->createMock(CountryRepositoryInterface::class);
        $countryRepository->expects($this->once())
            ->method('getById')
            ->with('invalid-country-id')
            ->willThrowException(new NoEntityFoundException());

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->expects($this->once())
            ->method('setParameter')
            ->with('{{ string }}', 'invalid-country-id')
            ->willReturnSelf();
        $violationBuilder->expects($this->once())
            ->method('addViolation');

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->once())
            ->method('buildViolation')
            ->with('The country "{{ string }}" does not exist.')
            ->willReturn($violationBuilder);

        $constraint = new CountryExists();
        $validator = new CountryExistsValidator($countryRepository);
        $validator->initialize($context);

        $validator->validate('invalid-country-id', $constraint);
    }

    public function testValidateWithCustomConstraintMessage()
    {
        $countryRepository = $this->createMock(CountryRepositoryInterface::class);
        $countryRepository->expects($this->once())
            ->method('getById')
            ->with('invalid-country-id')
            ->willThrowException(new NoEntityFoundException());

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->expects($this->once())
            ->method('setParameter')
            ->with('{{ string }}', 'invalid-country-id')
            ->willReturnSelf();
        $violationBuilder->expects($this->once())
            ->method('addViolation');

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->once())
            ->method('buildViolation')
            ->with('Custom error message')
            ->willReturn($violationBuilder);

        $constraint = new CountryExists();
        $constraint->message = 'Custom error message';
        $validator = new CountryExistsValidator($countryRepository);
        $validator->initialize($context);

        $validator->validate('invalid-country-id', $constraint);
    }
}
