Feature: New subscription stats

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And that there are stats for 5 years existing for default brand
    When I view the new subscriptions stats
    Then I should see 12 months of new subscriptions stats
    And I should see the total number of existing subscriptions for the last 12 months
    And I should see the total number of new subscriptions for the last 12 months
    And I should see the total number of upgrades for the last 12 months
    And I should see the total number of downgrades for the last 12 months
    And I should see the total number of cancellations for the last 12 months
    And I should see the total number of reactivations for the last 12 months