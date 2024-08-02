Feature: Create Invoice Delivery

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a delivery method for "customer.one@example.org" with the following settings:
      | Type          | SFTP            |
      | Format        | PDF             |
      | SFTP User     | user            |
      | SFTP Password | Password        |
      | SFTP Host     | example.org     |
      | SFTP Port     | 22              |
      | SFTP Dir      | /home/directory |
    Then there should be an invoice delivery for "customer.one@example.org" for type "SFTP"

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a delivery method for "customer.one@example.org" with the following settings:
      | Type           | Webhook             |
      | Format         | PDF                 |
      | Webhook URL    | https://example.org |
      | Webhook Method | POST                |
    Then there should be an invoice delivery for "customer.one@example.org" for type "Webhook"


  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a delivery method for "customer.one@example.org" with the following settings:
      | Type           | Webhook     |
      | Format         | PDF                 |
      | Webhook URL    | example.org |
      | Webhook Method | POST        |
    And there should be an error for "webhookUrl"
