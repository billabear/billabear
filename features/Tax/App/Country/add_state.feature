Feature: Add State

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |

  Scenario: Successfully create tax type
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Currency |
      | United States  | US       | 0         | USD      |
    When I create a state with the following data
      | Country    | United States |
      | Name       | Texas         |
      | Code       | TX            |
      | Has Nexus  | true          |
      | Threshold  | 1000          |
    Then there will be a state "Texas" in the country "United States"
