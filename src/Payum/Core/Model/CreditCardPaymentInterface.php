<?php
namespace Payum\Core\Model;

/**
 * Experimental. Anything could be changed in this model at any moment
 */
interface CreditCardPaymentInterface
{
    public function getCreditCard(): ?CreditCardInterface;
}
