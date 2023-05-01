Feature:

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Create email template
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I update the user management page for "tim.brown@example.org" with:
      | Email | tim.brown@example.com |
      | Roles | ROLE_USER,ROLE_ADMIN   |
    Then there will be a user with the email "tim.brown@example.com"
    And the user with the email "tim.brown@example.com" has the role "ROLE_ADMIN"