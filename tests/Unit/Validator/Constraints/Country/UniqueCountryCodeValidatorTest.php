<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Validator\Constraints\Country;

use BillaBear\Entity\Country;
use BillaBear\Repository\CountryRepositoryInterface;
use BillaBear\Validator\Constraints\Country\UniqueCountryCode;
use BillaBear\Validator\Constraints\Country\UniqueCountryCodeValidator;
use Parthenon\Common\Exception\NoEntityFoundException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class UniqueCountryCodeValidatorTest extends TestCase
{
    public function testValidateWithEmptyValueDoesNothing()
    {
        $countryRepository = $this->createMock(CountryRepositoryInterface::class);
        $countryRepository->expects($this->never())->method('getByIsoCode');

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $constraint = new UniqueCountryCode();
        $validator = new UniqueCountryCodeValidator($countryRepository);
        $validator->initialize($context);

        $validator->validate('', $constraint);
    }

    public function testValidateWithNullValueDoesNothing()
    {
        $countryRepository = $this->createMock(CountryRepositoryInterface::class);
        $countryRepository->expects($this->never())->method('getByIsoCode');

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $constraint = new UniqueCountryCode();
        $validator = new UniqueCountryCodeValidator($countryRepository);
        $validator->initialize($context);

        $validator->validate(null, $constraint);
    }

    public function testValidateWithUniqueCountryCodePassesValidation()
    {
        $countryRepository = $this->createMock(CountryRepositoryInterface::class);
        $countryRepository->expects($this->once())
            ->method('getByIsoCode')
            ->with('XX')
            ->willThrowException(new NoEntityFoundException());

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $constraint = new UniqueCountryCode();
        $validator = new UniqueCountryCodeValidator($countryRepository);
        $validator->initialize($context);

        $validator->validate('XX', $constraint);
    }

    public function testValidateWithExistingCountryCodeAddsViolation()
    {
        $country = $this->createMock(Country::class);
        $countryRepository = $this->createMock(CountryRepositoryInterface::class);
        $countryRepository->expects($this->once())
            ->method('getByIsoCode')
            ->with('US')
            ->willReturn($country);

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->expects($this->once())
            ->method('setParameter')
            ->with('{{ string }}', 'US')
            ->willReturnSelf();
        $violationBuilder->expects($this->once())
            ->method('addViolation');

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->once())
            ->method('buildViolation')
            ->with('The code "{{ string }}" already exists.')
            ->willReturn($violationBuilder);

        $constraint = new UniqueCountryCode();
        $validator = new UniqueCountryCodeValidator($countryRepository);
        $validator->initialize($context);

        $validator->validate('US', $constraint);
    }

    public function testValidateWithCustomConstraintMessage()
    {
        $country = $this->createMock(Country::class);
        $countryRepository = $this->createMock(CountryRepositoryInterface::class);
        $countryRepository->expects($this->once())
            ->method('getByIsoCode')
            ->with('US')
            ->willReturn($country);

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->expects($this->once())
            ->method('setParameter')
            ->with('{{ string }}', 'US')
            ->willReturnSelf();
        $violationBuilder->expects($this->once())
            ->method('addViolation');

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->once())
            ->method('buildViolation')
            ->with('Custom uniqueness message')
            ->willReturn($violationBuilder);

        $constraint = new UniqueCountryCode();
        $constraint->message = 'Custom uniqueness message';
        $validator = new UniqueCountryCodeValidator($countryRepository);
        $validator->initialize($context);

        $validator->validate('US', $constraint);
    }
}
