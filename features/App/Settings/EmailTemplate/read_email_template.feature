Feature: Email Template view
  In order to white label for new brands
  As an APP user
  I need to be list a template

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Create email template
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    Given the following email templates exist:
      | Name                   | Locale | Subject                | Template Body             |
      | subscription_created   | en     | Subscription Created   | A short text for the body |
      | subscription_cancelled | en     | Subscription Cancelled | A longer text for the body. |
    When I go to the email template for "subscription_created" with the locale "en"
    Then I will see that the email template body is "A short text for the body"