Feature:

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Create email template
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I view the user management page for "tim.brown@example.org"
    Then I will see the user has the role "ROLE_USER"