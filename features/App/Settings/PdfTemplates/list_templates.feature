Feature: Templates list
  In order to manage templates
  As an APP user
  I need to be see all the templates

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: List templates
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the following pdf templates exist:
      | Name    | Brand   | Content         |
      | receipt | default | A receipt body  |
      | invoice | default | An invoice body |
    When I go to the pdf templates
    Then I will see the pdf template for "invoice"