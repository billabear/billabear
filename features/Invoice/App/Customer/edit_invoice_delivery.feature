Feature:

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | DE      | cust_jf9j54d       | Customer Two |

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the following invoice delivery setups exist:
      | Customer                 | Type    | SFTP User | SFTP Password | SFTP Host   | SFTP Port | SFTP Dir | Webhook URL         | Webhook Method |
      | customer.one@example.org | Email   |           |               |             |           |          |                     |                |
      | customer.one@example.org | SFTP    | user      | password      | example.org | 2222      | .        |                     |                |
      | customer.one@example.org | Webhook |           |               |             |           |          | https://example.net | POST           |
      | customer.two@example.org | Webhook |           |               |             |           |          | https://example.org | POST           |
    When I edit the delivery methods for "customer.one@example.org" for "Webhook" with:
      | Type           | Webhook             |
      | Webhook URL    | https://example.com |
      | Webhook Method | POST                |
    Then there should be an invoice delivery for "customer.one@example.org" for type "Webhook" and url "https://example.com"

