<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Exception\NoSubscriptionException;
use Parthenon\Common\Address;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity]
#[ORM\Table(name: 'customers')]
class Customer implements CustomerInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\Embedded(class: Subscription::class)]
    #[Ignore]
    private ?Subscription $subscription;

    #[ORM\Embedded(class: Address::class)]
    #[Ignore]
    private ?Address $billingAddress;

    #[ORM\Column(type: 'string', nullable: true)]
    #[SerializedName('reference')]
    private ?string $reference;

    #[ORM\Column(type: 'string')]
    #[SerializedName('external_reference')]
    private string $externalCustomerReference;

    #[ORM\Column(type: 'string')]
    #[SerializedName('email')]
    private string $billingEmail;

    public function hasSubscription(): bool
    {
        if (isset($this->subscription) && !empty($this->subscription->getPlanName())) {
            return true;
        }

        return false;
    }

    #[Ignore]
    public function hasActiveSubscription(): bool
    {
        if (isset($this->subscription) && $this->subscription->isActive()) {
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getSubscription(): Subscription
    {
        if (!isset($this->subscription)) {
            throw new NoSubscriptionException();
        }

        return $this->subscription;
    }

    public function setSubscription(?Subscription $subscription): void
    {
        $this->subscription = $subscription;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): void
    {
        $this->reference = $reference;
    }

    public function getExternalCustomerReference(): string
    {
        return $this->externalCustomerReference;
    }

    /**
     * @param string $externalCustomerReference
     */
    public function setExternalCustomerReference($externalCustomerReference)
    {
        $this->externalCustomerReference = $externalCustomerReference;
    }

    #[Ignore]
    public function hasExternalsCustomerReference(): bool
    {
        return isset($this->externalCustomerReference);
    }

    public function getBillingEmail(): string
    {
        return $this->billingEmail;
    }

    public function setBillingEmail(string $billingEmail): void
    {
        $this->billingEmail = $billingEmail;
    }

    public function setBillingAddress(Address $address)
    {
        $this->billingAddress = $address;
    }

    public function getBillingAddress(): Address
    {
        if (!isset($this->billingAddress)) {
            throw new \Exception('No billing address');
        }

        return $this->billingAddress;
    }

    public function hasBillingAddress(): bool
    {
        return isset($this->billingAddress);
    }

    public function getCountry(): string
    {
        return $this->billingAddress->getCountry();
    }

    #[Ignore]
    public function getDisplayName(): string
    {
        return $this->billingEmail;
    }
}
