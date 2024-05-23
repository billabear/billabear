Feature: Add country to economic areas

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Successfully create economic area
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And that the following economic areas exist:
      | Name                   | Threshold | Currency |
      | European Economic Area | 1000000   | GBP      |
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Currency |
      | United Kingdom | GB       | 1770      | GBP      |
      | United States  | US       | 0         | USD      |
      | Germany        | DE       | 0         | EUR      |
    When I create an economic area membership with the following data:
      | Economic Area | European Economic Area |
      | Country       | Germany                |
      | Joined At     | 1958-01-01             |
    Then the country "Germany" is a member of "European Economic Area"
    And the country "United Kingdom" is not a member of "European Economic Area"
