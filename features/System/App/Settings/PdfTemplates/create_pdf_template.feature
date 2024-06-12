Feature: Create PDF Templates

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Create email template
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a pdf template:
      | Type          | invoice                          |
      | Locale        | en                               |
      | Template Body | The subscription email body here |
    Then there will be an pdf template for "invoice" with locale "en"

  Scenario: Create email template failure for no template id
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a pdf template:
      | Type              | invoice                            |
      | Locale            | en                                 |
      | Subject           | Your subscription has now started! |
      | Template Body     | The subscription email body here   |
      | Use Emsp Template | true                               |
    Then there will not be an pdf template for "subscription_created" with locale "en"

  Scenario: Invalid name
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a pdf template:
      | Type          | invoice_two                        |
      | Locale        | en                                 |
      | Subject       | Your subscription has now started! |
      | Template Body | The subscription email body here   |
    Then I will get an error response
    Then there will not be an pdf template for "invoice_two" with locale "en"


  Scenario: Invalid locale
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a pdf template:
      | Type          | invoice                          |
      | Locale        | eke                              |
      | Template Body | The subscription email body here |
    Then I will get an error response
    Then there will not be an email template for "subscription_created" with locale "eke"

  Scenario: Already exits locale
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    Given the following pdf templates exist:
      | Type    | Locale | Template Body             |
      | invoice | en     | A short text for the body |
    When I create a pdf template:
      | Type          | invoice                          |
      | Locale        | en                               |
      | Template Body | The subscription email body here |
    Then I will get an error response
