Feature: List countries for tax purposes

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
    When I view the countries list
    Then I will see the country "United Kingdom" in the list

  Scenario: Show counts
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Currency | Registration Required | Collecting |
      | United Kingdom | GB       | 1770      | GBP      | False                 | False      |
      | United States  | US       | 0         | USD      | True                  | True       |
      | Germany        | DE       | 0         | EUR      | True                  | True       |
      | France         | FR       | 0         | EUR      | False                 | True       |
    When I view the countries list
    Then I will see a total country count of 4
    And I will see a registration required count of 2
    And I will see a collecting count of 3

  Scenario: List countries that need tax registration
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Currency | Registration Required |
      | United Kingdom | GB       | 1770      | GBP      | False                 |
      | United States  | US       | 4000      | USD      | True                  |
      | Germany        | DE       | 1000      | EUR      | True                  |
    When I view the countries list that require tax registration
    Then I will see the country "United States" in the list
    Then I will not see the country "United Kingdom" in the list
