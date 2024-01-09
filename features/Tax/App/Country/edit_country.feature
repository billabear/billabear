Feature: Edit country for tax purposes

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: List countries
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Currency |
      | United Kingdom | GB       | 1770      | GBP      |
      | United States  | US       | 0         | USD      |
      | Germany        | DE       | 0         | EUR      |
    When I goto the edit country for "United Kingdom"
    Then I will see that there is a threshold for the country of 1770
    And I will see the currency is "GBP"
    And I will see the ISO code is "GB"