Feature: List countries for tax purposes

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Successfully create product
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And that the following countries exist:
      | Name           | ISO Code |
      | United Kingdom | GB       |
      | United States  | US       |
      | Germany        | DE       |
    When I view the countries list
    Then I will see the country "United Kingdom" in the list