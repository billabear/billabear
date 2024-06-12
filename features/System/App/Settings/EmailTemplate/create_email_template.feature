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

  Scenario: Create email template
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create an email template:
      | Name | subscription_created |
      | Locale | en |
      | Subject | Your subscription has now started! |
      | Template Body | The subscription email body here |
    Then there will be an email template for "subscription_created" with locale "en"

  Scenario: Create email template failure for no template id
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create an email template:
      | Name | subscription_created |
      | Locale | en |
      | Subject | Your subscription has now started! |
      | Template Body | The subscription email body here |
      | Use Emsp Template | true                         |
    Then there will not be an email template for "subscription_created" with locale "en"

  Scenario: Invalid name
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create an email template:
      | Name | subscription_created_no |
      | Locale | en |
      | Subject | Your subscription has now started! |
      | Template Body | The subscription email body here |
    Then I will get an error response
    Then there will not be an email template for "subscription_created_no" with locale "en"

  Scenario: Create email template with template id
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create an email template:
      | Name              | subscription_created |
      | Locale            | en                   |
      | Use Emsp Template | true                 |
      | Template ID       | fake-id-here                 |
    Then there will be an email template for "subscription_created" with locale "en"

  Scenario: Invalid locale
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create an email template:
      | Name | subscription_created |
      | Locale | eke |
      | Subject | Your subscription has now started! |
      | Template Body | The subscription email body here |
    Then I will get an error response
    Then there will not be an email template for "subscription_created" with locale "eke"

  Scenario: Already exits locale
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    Given the following email templates exist:
      | Name                   | Locale | Subject                | Template Body             |
      | subscription_created   | en     | Subscription Created   | A short text for the body |
    When I create an email template:
      | Name | subscription_created |
      | Locale | en |
      | Subject | Your subscription has now started! |
      | Template Body | The subscription email body here |
    Then I will get an error response