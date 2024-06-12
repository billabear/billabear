Feature: API Key create

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Create API Key
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create an API key for the name "New API Key" with the expires "+1 year"
    Then I will get a valid response
    Then there will be an API key with the name "New API Key"

  Scenario: Create API Key fails - expires in past
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create an API key for the name "New API Key" with the expires "-1 year"
    Then I will get an error response
    Then there will not be an API key with the name "New API Key"

  Scenario: Create API Key - name empty
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create an API key for the name "" with the expires "+1 year"
    Then I will get an error response
    Then there will not be an API key with the name "New API Key"

  Scenario: Create API Key - Name already used
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    Given I create an API key for the name "New API Key" with the expires "+1 year"
    When I create an API key for the name "New API Key" with the expires "+1 year"
    Then I will get an error response