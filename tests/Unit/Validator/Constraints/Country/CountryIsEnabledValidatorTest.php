<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Validator\Constraints\Country;

use BillaBear\Entity\Country;
use BillaBear\Repository\CountryRepositoryInterface;
use BillaBear\Validator\Constraints\Country\CountryIsEnabled;
use BillaBear\Validator\Constraints\Country\CountryIsEnabledValidator;
use Parthenon\Common\Exception\NoEntityFoundException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class CountryIsEnabledValidatorTest extends TestCase
{
    public function testValidateWithEmptyValueDoesNothing()
    {
        $countryRepository = $this->createMock(CountryRepositoryInterface::class);
        $countryRepository->expects($this->never())->method('getByIsoCode');

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $constraint = new CountryIsEnabled();
        $validator = new CountryIsEnabledValidator($countryRepository);
        $validator->initialize($context);

        $validator->validate('', $constraint);
    }

    public function testValidateWithNullValueDoesNothing()
    {
        $countryRepository = $this->createMock(CountryRepositoryInterface::class);
        $countryRepository->expects($this->never())->method('getByIsoCode');

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $constraint = new CountryIsEnabled();
        $validator = new CountryIsEnabledValidator($countryRepository);
        $validator->initialize($context);

        $validator->validate(null, $constraint);
    }

    public function testValidateWithEnabledCountryPassesValidation()
    {
        $country = $this->createMock(Country::class);
        $country->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $countryRepository = $this->createMock(CountryRepositoryInterface::class);
        $countryRepository->expects($this->once())
            ->method('getByIsoCode')
            ->with('US')
            ->willReturn($country);

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $constraint = new CountryIsEnabled();
        $validator = new CountryIsEnabledValidator($countryRepository);
        $validator->initialize($context);

        $validator->validate('US', $constraint);
    }

    public function testValidateWithDisabledCountryAddsViolation()
    {
        $country = $this->createMock(Country::class);
        $country->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);

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
            ->with('The country "{{ string }}" is not enabled')
            ->willReturn($violationBuilder);

        $constraint = new CountryIsEnabled();
        $validator = new CountryIsEnabledValidator($countryRepository);
        $validator->initialize($context);

        $validator->validate('US', $constraint);
    }

    public function testValidateWithNonExistentCountryPassesValidation()
    {
        $countryRepository = $this->createMock(CountryRepositoryInterface::class);
        $countryRepository->expects($this->once())
            ->method('getByIsoCode')
            ->with('XX')
            ->willThrowException(new NoEntityFoundException());

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $constraint = new CountryIsEnabled();
        $validator = new CountryIsEnabledValidator($countryRepository);
        $validator->initialize($context);

        $validator->validate('XX', $constraint);
    }

    public function testValidateWithCustomConstraintMessage()
    {
        $country = $this->createMock(Country::class);
        $country->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);

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
            ->with('Custom disabled message')
            ->willReturn($violationBuilder);

        $constraint = new CountryIsEnabled();
        $constraint->message = 'Custom disabled message';
        $validator = new CountryIsEnabledValidator($countryRepository);
        $validator->initialize($context);

        $validator->validate('US', $constraint);
    }
}
