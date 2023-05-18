<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Install\Steps;

use App\Entity\Customer;
use App\Entity\EmailTemplate;
use App\Entity\Template;
use App\Repository\BrandSettingsRepositoryInterface;
use App\Repository\EmailTemplateRepositoryInterface;
use App\Repository\TemplateRepositoryInterface;

class TemplateStep
{
    public function __construct(
        private TemplateRepositoryInterface $templateRepository,
        private EmailTemplateRepositoryInterface $emailTemplateRepository,
        private BrandSettingsRepositoryInterface $brandSettingsRepository,
    ) {
    }

    public function install()
    {
        $brand = $this->brandSettingsRepository->getByCode(Customer::DEFAULT_BRAND);

        $template = new Template();
        $template->setBrand(Customer::DEFAULT_BRAND);
        $template->setName(Template::NAME_INVOICE);
        $template->setContent('<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title></title>
{% verbatim %}
        
        <style>
            .invoice-box {
                max-width: 800px;
                margin: auto;
                padding: 30px;
                border: 1px solid #eee;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
                font-size: 16px;
                line-height: 24px;
                font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif;
                color: #555;
            }

            .invoice-box table {
                width: 100%;
                line-height: inherit;
                text-align: left;
            }

            .invoice-box table td {
                padding: 5px;
                vertical-align: top;
            }

            .invoice-box table tr td:nth-child(2) {
                text-align: right;
            }

            .invoice-box table tr.top table td {
                padding-bottom: 20px;
            }

            .invoice-box table tr.top table td.title {
                font-size: 45px;
                line-height: 45px;
                color: #333;
            }

            .invoice-box table tr.information table td {
                padding-bottom: 40px;
            }

            .invoice-box table tr.heading td {
                background: #eee;
                border-bottom: 1px solid #ddd;
                font-weight: bold;
            }

            .invoice-box table tr.details td {
                padding-bottom: 20px;
            }

            .invoice-box table tr.item td {
                border-bottom: 1px solid #eee;
            }

            .invoice-box table tr.item.last td {
                border-bottom: none;
            }

            .invoice-box table tr.total td:nth-child(2) {
                border-top: 2px solid #eee;
                font-weight: bold;
            }

            @media only screen and (max-width: 600px) {
                .invoice-box table tr.top table td {
                    width: 100%;
                    display: block;
                    text-align: center;
                }

                .invoice-box table tr.information table td {
                    width: 100%;
                    display: block;
                    text-align: center;
                }
            }

            /** RTL **/
            .invoice-box.rtl {
                direction: rtl;
                font-family: Tahoma, \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif;
            }

            .invoice-box.rtl table {
                text-align: right;
            }

            .invoice-box.rtl table tr td:nth-child(2) {
                text-align: left;
            }
        </style>
{% endverbatim %}
    </head>

    <body>
        <div class="invoice-box">
            <table cellpadding="0" cellspacing="0">
                <tr class="top">
                    <td colspan="2">
                        <strong>RECEIPT</strong>
                    </td>
                </tr>
                <tr class="top">
                    <td colspan="2">
                        <table>
                            <tr>
                                <td class="title">
                                    {{ brand.name }}
                                </td>

                                <td>
                                    Receipt #: {{ receipt.id }}<br />
                                    Created: {{ receipt.createdAt.format(\'Y-m-d\') }} <br />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="information">
                    <td colspan="2">
                        <table>
                            <tr>
                                <td>
                                    {{ receipt.billerAddress.companyName }}<br />
                                    {{ receipt.billerAddress.streetLineOne }}<br />
                                    {{ receipt.billerAddress.streetLineTwo }}<br />
                                    {{ receipt.billerAddress.region }}<br />
                                    {{ receipt.billerAddress.city }}<br />
                                    {{ receipt.billerAddress.country }}<br />
                                    {{ receipt.billerAddress.postcode }}
                                </td>

                                <td>
                                    {{ receipt.payeeAddress.companyName }}<br />
                                    {{ receipt.payeeAddress.streetLineOne }}<br />
                                    {{ receipt.payeeAddress.streetLineTwo }}<br />
                                    {{ receipt.payeeAddress.region }}<br />
                                    {{ receipt.payeeAddress.city }}<br />
                                    {{ receipt.payeeAddress.country }}<br />
                                    {{ receipt.payeeAddress.postcode }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="heading">
                    <td>Item</td>

                    <td>Price</td>
                </tr>

                {% for line in receipt.lines %}
                <tr class="item">
                    <td>{{ line.description }}</td>

                    <td>{{ line.totalMoney }}</td>
                </tr>
                {% endfor %}

                <tr class="total">
                    <td></td>

                    <td>Total: {{ receipt.totalMoney }}</td>
                </tr>
            </table>
        </div>
    </body>
</html>

{# MIT License

Copyright (c) 2021 Sparksuite

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE. #}
');
        $this->templateRepository->save($template);

        $template = new Template();
        $template->setBrand(Customer::DEFAULT_BRAND);
        $template->setName(Template::NAME_RECEIPT);
        $template->setContent('<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title></title>
{% verbatim %}
        
        <style>
            .invoice-box {
                max-width: 800px;
                margin: auto;
                padding: 30px;
                border: 1px solid #eee;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
                font-size: 16px;
                line-height: 24px;
                font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif;
                color: #555;
            }

            .invoice-box table {
                width: 100%;
                line-height: inherit;
                text-align: left;
            }

            .invoice-box table td {
                padding: 5px;
                vertical-align: top;
            }

            .invoice-box table tr td:nth-child(2) {
                text-align: right;
            }

            .invoice-box table tr.top table td {
                padding-bottom: 20px;
            }

            .invoice-box table tr.top table td.title {
                font-size: 45px;
                line-height: 45px;
                color: #333;
            }

            .invoice-box table tr.information table td {
                padding-bottom: 40px;
            }

            .invoice-box table tr.heading td {
                background: #eee;
                border-bottom: 1px solid #ddd;
                font-weight: bold;
            }

            .invoice-box table tr.details td {
                padding-bottom: 20px;
            }

            .invoice-box table tr.item td {
                border-bottom: 1px solid #eee;
            }

            .invoice-box table tr.item.last td {
                border-bottom: none;
            }

            .invoice-box table tr.total td:nth-child(2) {
                border-top: 2px solid #eee;
                font-weight: bold;
            }

            @media only screen and (max-width: 600px) {
                .invoice-box table tr.top table td {
                    width: 100%;
                    display: block;
                    text-align: center;
                }

                .invoice-box table tr.information table td {
                    width: 100%;
                    display: block;
                    text-align: center;
                }
            }

            /** RTL **/
            .invoice-box.rtl {
                direction: rtl;
                font-family: Tahoma, \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif;
            }

            .invoice-box.rtl table {
                text-align: right;
            }

            .invoice-box.rtl table tr td:nth-child(2) {
                text-align: left;
            }
        </style>
{% endverbatim %}
    </head>

    <body>
        <div class="invoice-box">
            <table cellpadding="0" cellspacing="0">
                <tr class="top">
                    <td colspan="2">
                        <strong>RECEIPT</strong>
                    </td>
                </tr>
                <tr class="top">
                    <td colspan="2">
                        <table>
                            <tr>
                                <td class="title">
                                    {{ brand.name }}
                                </td>

                                <td>
                                    Receipt #: {{ receipt.id }}<br />
                                    Created: {{ receipt.createdAt.format(\'Y-m-d\') }} <br />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="information">
                    <td colspan="2">
                        <table>
                            <tr>
                                <td>
                                    {{ receipt.billerAddress.companyName }}<br />
                                    {{ receipt.billerAddress.streetLineOne }}<br />
                                    {{ receipt.billerAddress.streetLineTwo }}<br />
                                    {{ receipt.billerAddress.region }}<br />
                                    {{ receipt.billerAddress.city }}<br />
                                    {{ receipt.billerAddress.country }}<br />
                                    {{ receipt.billerAddress.postcode }}
                                </td>

                                <td>
                                    {{ receipt.payeeAddress.companyName }}<br />
                                    {{ receipt.payeeAddress.streetLineOne }}<br />
                                    {{ receipt.payeeAddress.streetLineTwo }}<br />
                                    {{ receipt.payeeAddress.region }}<br />
                                    {{ receipt.payeeAddress.city }}<br />
                                    {{ receipt.payeeAddress.country }}<br />
                                    {{ receipt.payeeAddress.postcode }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="heading">
                    <td>Item</td>

                    <td>Price</td>
                </tr>

                {% for line in receipt.lines %}
                <tr class="item">
                    <td>{{ line.description }}</td>

                    <td>{{ line.totalMoney }}</td>
                </tr>
                {% endfor %}

                <tr class="total">
                    <td></td>

                    <td>Total: {{ receipt.totalMoney }}</td>
                </tr>
            </table>
        </div>
    </body>
</html>

{# MIT License

Copyright (c) 2021 Sparksuite

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE. #}
');

        $this->templateRepository->save($template);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_SUBSCRIPTION_CREATED);
        $emailTemplate->setSubject('Subscription Created');
        $emailTemplate->setTemplateBody($this->getEmailTemplate('<p>Your subscription for plan <strong>{{ subscription.plan_name }}</strong> has been started</p>
        
        {% if subscription.has_trial %}
            <p>You have a trial which will last {{ subscription.trial_length  }} after which you will be charged {{ subscription.amount }}</p>
        {% endif %}
      
        <p>If you have any questions just reach out and we\'ll be happy to answer them!</p>'));
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_PAYMENT_SUCCEEDED);
        $emailTemplate->setSubject('Payment Received');
        $emailTemplate->setTemplateBody($this->getEmailTemplate('Thanks for your payment. Here is the receipt.'));
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_PAYMENT_FAILED);
        $emailTemplate->setSubject('Payment Failed');
        $emailTemplate->setTemplateBody($this->getEmailTemplate('Thanks for your payment. Here is the receipt.'));
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_PAYMENT_FAILURE_WARNING);
        $emailTemplate->setSubject('Payment Failed');
        $emailTemplate->setTemplateBody($this->getEmailTemplate('Thanks for your payment. Here is the receipt. We\'ll try and charge you later.'));
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_SUBSCRIPTION_CANCELLED);
        $emailTemplate->setSubject('Subscription Cancelled');
        $emailTemplate->setTemplateBody($this->getEmailTemplate('<p>Your subscription for plan <strong>{{ subscription.plan_name }}</strong> has been cancelled</p>
        <p>You will stop being able to use the system at {{ subscription.finishes_at }}</p>
      
        <p>If you have any questions just reach out and we\'ll be happy to answer them!</p>'));
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_SUBSCRIPTION_PAUSED);
        $emailTemplate->setSubject('Subscription Paused');
        $emailTemplate->setTemplateBody($this->getEmailTemplate('Your subscription has been paused'));
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_PAYMENT_METHOD_NO_VALID_METHODS);
        $emailTemplate->setSubject('You have no valid payment methods');
        $emailTemplate->setTemplateBody($this->getEmailTemplate('There are no valid payment methods attached to your account. Please add one.'));
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_PAYMENT_METHOD_EXPIRY_WARNING);
        $emailTemplate->setSubject('Payment Method Expiring Soon');
        $emailTemplate->setTemplateBody($this->getEmailTemplate('Your payment method is expiring soon. Please add one before it expires.'));
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_PAYMENT_METHOD_DAY_BEFORE_WARNING);
        $emailTemplate->setSubject('Payment Method Expiring Soon');
        $emailTemplate->setTemplateBody($this->getEmailTemplate('Your subscription is due to be renewed. Your default payment method is due to expire. Update your payment method to ensure uninterrupted access.'));
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_PAYMENT_METHOD_DAY_BEFORE_NOT_VALID_WARNING);
        $emailTemplate->setSubject('Payment Method Expiring Soon');
        $emailTemplate->setTemplateBody($this->getEmailTemplate('Your subscription is due to be renewed. Your default payment method has expired. Update your payment method to ensure uninterrupted access.'));
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);
    }

    private function getEmailTemplate(string $content): string
    {
        return '<html>
    <head>
      <title></title>
    </head>
    <body style="background: rgb(254,234,0);
background: radial-gradient(circle, rgba(254,234,0,1) 0%, rgba(246,156,0,1) 100%);; color: black;">
    
    <div style="padding-top: 40px;">
      <div style="margin:auto; background-color: white; max-width: 700px; padding: 50px; border-radius: 15px; margin-top: 40px; ">
        <h1 style="text-align:center;"><img src="https://ha-static-data.s3.eu-central-1.amazonaws.com/github-readme-logo.png" alt="{{ brand.name  }}" /></h1>
        
        '.$content.'
      </div>

      </div>


    </body>
  </html>';
    }
}
