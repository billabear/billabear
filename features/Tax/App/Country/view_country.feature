Feature: View country for tax purposes

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: View Country
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Currency |
      | United Kingdom | GB       | 1770      | GBP      |
    When I view the country for "United Kingdom"
    Then I will see that there is a threshold for the country of 1770
    And I will see the currency is "GBP"
    And I will see the ISO code is "GB"

  Scenario: View Country with tax rules
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Currency |
      | United Kingdom | GB       | 1770      | GBP      |
    And there are the following tax types:
      | Name     |
      | Digital Goods  |
      | Physical |
    And the following country tax rules exist:
      | Country        | Tax Type | Tax Rate | Valid From |
      | United Kingdom | Digital Goods  | 17.5     | -10 days   |
    When I view the country for "United Kingdom"
    Then I should see the tax rule for tax type "Digital Goods" with the tax rate 17.5
