Feature: Customer Subscription Update Payment Method APP
  In order to manage a customer's subscriptions payment method
  As an APP user
  I need to be able to update their payment method

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And that there are stats for 5 years existing for default brand
    When I view the overall stats
    Then I will see that there are stats for the default brand
    And I will see there is 30 days of daily stats
    And I will see there is 12 months of monthly stats
    And I will see there is 5 years of yearly stats
    And I will see there is 12 months of monthly revenue stats for "USD"
    And I will see there is 12 months of monthly revenue stats for "EUR"
    And I will see the total number of active subscriptions
    And I will see the total number of active customers
    And I will see the number of outstanding payments
