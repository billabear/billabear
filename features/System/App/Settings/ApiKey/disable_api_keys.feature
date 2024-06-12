Feature: API Key list
  In order to keep brands up to date
  As an APP user
  I need to be edit brands

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: View API Key
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow api keys exist:
      | Name      | API Key   | Expires At |
      | Key One   | key-one   | +1 year    |
      | Key Two   | key-two   | +3 years   |
      | Key Three | key-three | -3 years   |
    When I disable the api key "Key One"
    Then then the api key "Key One" is not active
