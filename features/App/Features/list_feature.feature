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


  Scenario: Fail to create feature with same code
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    Given the following features exist:
      | Name          | Code          | Description     |
      | Feature One   | feature_one   | A dummy feature |
      | Feature Two   | feature_two   | A dummy feature |
      | Feature Three | feature_three | A dummy feature |
    When I get the list of features via the APP
    Then I should see in the site response with only 3 result in the data set