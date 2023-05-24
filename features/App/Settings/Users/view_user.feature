Feature:

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  | Admin |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss | true  |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss | false |
      | Sally Braun | sally.braun@example.org | AF@k3Pass | false |

  Scenario: Create email template
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I view the user management page for "tim.brown@example.org"
    Then I will see the user has the role "ROLE_USER"