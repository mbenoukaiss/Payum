<h2 align="center">Supporting Payum</h2>

Payum is an MIT-licensed open source project with its ongoing development made possible entirely by the support of community and our customers. If you'd like to join them, please consider:

- [Become a sponsor](https://www.patreon.com/makasim)
- [Become our client](http://forma-pro.com/)

---

# Payum vs Omnipay

The short answer is Payum provides the same functionality as Omnipay plus some extra features.

Payum works best when you combine a payment model with a convert action. The model must not be only a [Payum’s one](https://github.com/Payum/Payum/blob/master/src/Payum/Core/Model/Payment.php), I encourage you to use your own or one from a [ecommerce platform](https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Payment/Model/Payment.php). The idea is simple: you send a request to Payum to capture your model. In the action you [convert the payment model to a gateway specific format](https://github.com/Payum/Payum/blob/master/src/Payum/Paypal/ExpressCheckout/Nvp/Action/ConvertPaymentAction.php), most probably an array. The beauty of this approach is that your code is never changed, and looks like this:

    $gateway->execute(new Capture($payment));

All gateway differences are hidden inside a gateway. Of course Payum supports a gateway-specific format, or a Payum’s Payment model as well. In case of Omnipay you cannot simply replace a stripe gateway to a paypal one, because they behave differently and what is more important they require different data. Stripe require a credit card to be provided where Paypal does not care about it but instead wants return and cancel urls to be set. You have to reflect these differences in your code, is this abstraction? By the way Payum generates cancel and return urls for you and they are secure ones (we talk about it later).

Sometimes you have to get more details about the payment transaction, or a payer, or more info about an error. Payum provides you access to all data that takes part in a communication between your code and a payment gateway. The data format is a payment specific, so if you familiar with [Paypal protocol (for example)](https://developer.paypal.com/docs/classic/express-checkout/gs_expresscheckout/) it would be easy for you to understand what is going on there. Another good example is Klarna Checkout. It returns shipping\billing addresses, gender and date of birth. With Payum you can easily get these out of payment and use for your needs.

Payum gives you better status handling. Omnipay provides only two statuses success and failed, but it is not enough. For example Paypal sometimes returns pending status because of [multi currency issue](http://stackoverflow.com/questions/19864511/paypal-sandbox-pending-multicurrency). In this case omnipay says the payment has failed, but in fact it has not. Or a user can cancel the payment at Paypal side, Omnipay will tell you it failed but it is not really true. If you need a status which is not provided by Payum by default, you can easily add it. Do you already have payment statuses, maybe your [ecommerce platform provide](https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/PayumBundle/Payum/Request/GetStatus.php#L24) them and you want to reuse, no problem Payum could be adjust to use them.

Sometimes users want to cheat on you or pay less for the stuff. As a developer you have to think about it and take care of your data. What do you expose to a user? Could it be used the wrong way? You can not rely on an amount given in the url, until you validate it. For example Paypal sends you a push notification to the notify url you previously sent to them. Payum generates for you such an url and when the notification comes back it validates it. You get unique, secured urls out of the box. The payment internally associated with that url, and once you remove\invalidate the url user not able to access the payment behind it. Omnipay does not provide anything to help you solve security issues. There is one good side effect with these secured urls. The secured urls invalidated\deleted once they not needed. This is good, for example user clicks “Back” button in a browser. He would not be able to do a second payment because the purchase url no longer exists, instead he will see a 404 error.

It is not good practice to store credit cards on your server, is it? There is no excuse for storing them accidentally, or just for few seconds. Payum has a [sensitive value](https://github.com/Payum/Payum/blob/master/src/Payum/Core/Security/SensitiveValue.php) object which ensure that nothing is saved accidentally. You still able to store it, but while doing so you are on your own.

Omnipay supports only gateways, that do a redirect to gateway side or require a credit card. But there are bunch of other gateways, and they act differently. For example Klarna Checkout [require a snippet (iframe) to be rendered](https://developers.klarna.com/en/se+php/kco-v2/checkout/2-embed-the-checkout), Stripe.Js [requires their javascript to be executed](https://stripe.com/docs/stripe.js?) on a purchase page. Stripe Checkout [renders its own popup](https://stripe.com/docs/checkout). Payum [supports them all](supported-gateways.md), and as we talked at the beginning you can switch from one gateway to another without changes in your code. Payum does not natively support a gateway you need? I do not think reimplementing every gateway in house is a good idea. That’s why a [bridge for Omnipay gateways exists](index.md#omnipay-bridge-external). It allows you to use Omnipay gateways the Payum way.

Payum tries to [standardise the payment flow](get-it-started.md). There are three steps prepare, capture\authorise and done. The first one is called “prepare” and at this step you have to prepare the payment, calculate total prices, taxes, get user or shipping information and so on. Once you are done you can redirect user to a capture\authorise step, From here user could be redirected to a gateway side or asked for a credit card, or something else. It depends on what gateway you chose. At “done” step you have to get the payment status and act according to it. Omnipay only partly solves this task.

With a gateway factories you can easily overwrite\replace any parts of the gateway functionally, or add a custom actions, extensions or apis.

Payum has official extensions for most modern frameworks such as [Symfony](https://github.com/Payum/PayumBundle). [Laravel](https://github.com/Payum/PayumLaravelPackage), [Silex](https://github.com/Payum/PayumSilexProvider), [Yii](https://github.com/Payum/PayumYiiExtension),  [Laminas](https://github.com/Payum/PayumModule).

At the end lets compare the corner stone interfaces from Payum and Omnipay.

* Payum [GatewayInterface](https://github.com/Payum/Payum/blob/master/src/Payum/Core/GatewayInterface.php) vs Omnipay [GatewayInterface](https://github.com/thephpleague/omnipay-common/blob/master/src/Common/GatewayInterface.php)
* Payum [GatewayFactoryInterface](https://github.com/Payum/Payum/blob/master/src/Payum/Core/GatewayFactoryInterface.php) vs Omnipay [GatewayFactory](https://github.com/thephpleague/omnipay-common/blob/master/src/Common/GatewayFactory.php) ups there is not an interface.

Back to [index](index.md).
