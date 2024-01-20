Feature: Add Country Tax Rule

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Successfully create tax type
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Currency |
      | United Kingdom | GB       | 1770      | GBP      |
      | United States  | US       | 0         | USD      |
      | Germany        | DE       | 0         | EUR      |
    And there are the following tax types:
      | Name     |
      | Digital  |
      | Physical |
    When I create a country tax rule with the following data:
      | Country    | United States |
      | Tax Type   | Digital       |
      | Tax Rate   | 15            |
      | Valid From | -3 days       |
    Then there should be a tax rule for "United States" for "Digital" tax type with the tax rate 15