Feature: Templates View
  In order to edit a template
  As an APP user
  I need to be see the current template data

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
    When I go to the pdf template for "receipt" in brand "default"
    Then I will see the the template content of "A receipt body"