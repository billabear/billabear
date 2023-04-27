Feature: Brands Create
  In order to white label for new brands
  As an APP user
  I need to be add new brands

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Create brands
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create an email template:
      | Name | subscription_created |
      | Locale | en |
      | Subject | Your subscription has now started! |
      | Template Body | The subscription email body here |
    Then there will be an email template for "subscription_created" with locale "en"

  Scenario: Create brands
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create an email template:
      | Name | subscription_created |
      | Locale | en |
      | Subject | Your subscription has now started! |
      | Template Body | The subscription email body here |
      | Use Emsp Template | true                         |
    Then there will not be an email template for "subscription_created" with locale "en"