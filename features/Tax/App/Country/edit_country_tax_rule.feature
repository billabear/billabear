Feature: Edit country tax rule

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Update
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Currency |
      | United Kingdom | GB       | 1770      | GBP      |
      | United States  | US       | 0         | USD      |
      | Germany        | DE       | 0         | EUR      |
    And there are the following tax types:
      | Name     |
      | Digital Goods |
      | Physical |
    And the following country tax rules exist:
      | Country       | Tax Type | Tax Rate | Valid From |
      | United States | Digital Goods  | 17.5     | -10 days   |
    When I update the country tax rule for "United States" with tax type "Digital Goods" and tax rate "17.5" with the values:
      | Country     | United States |
      | Tax Type    | Digital Goods       |
      | Tax Rate    | 15            |
      | Valid From  | -23 days      |
      | Valid Until | -11 days      |
    Then there should be a tax rule for "United States" for "Digital Goods" tax type with the tax rate 15
    And there should not be a tax rule for "United States" for "Digital Goods" tax type with the tax rate 17.5