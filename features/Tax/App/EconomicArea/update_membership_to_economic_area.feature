Feature: update country membership to economic areas

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
    And there are the following economic area memberships:
      | Economic Area          | Country | Joined At  |
      | European Economic Area | Germany | 1959-01-01 |
    When I update "Germany" from the economic area "European Economic Area"
      | Joined At | 1959-01-01 |
      | Left At   | 2359-01-01 |
    Then the membership for "Germany" to "European Economic Area" will be marked as left at as "2359-01-01"
