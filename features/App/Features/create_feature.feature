Feature: Feature Creation
  In order to charge customers for features
  As an APP user
  I need to create a feature

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Create a feature
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a feature via the APP with the info:
      | Name | Feature One |
      | Code | feature_one |
      | Description | A dummy feature |
    Then there should be a feature with the code "feature_one"
    And there should be a feature with the name "Feature One"

  Scenario: Fail to create feature with same code
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    Given the following features exist:
      | Name        | Code        | Description     |
      | Feature One | feature_one | A dummy feature |
    When I create a feature via the APP with the info:
      | Name | Feature One |
      | Code | feature_one |
      | Description | A dummy feature |
    Then there should be an error for "code"