Feature: Update Brand

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: View brands
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow brands exist:
      | Name    | Code    | Email               |
      | Example | example | example@example.org |
    When I go to update the brand "Example" with:
      | Name  | Example 2 |
      | Email | example@example.org |
      | Company Name | New Company  |
      | Street Line One | 4 Example Way |
      | City            | City          |
      | Region          | Berlin        |
      | Post Code       | 10343         |
      | Country | DE  |
    Then there should be a brand with the name "Example 2"
    Then there should not be a brand with the name "Example"

  Scenario: View brands
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow brands exist:
      | Name    | Code    | Email               |
      | Example | example | example@example.org |
    When I go to update the brand "Example" with:
      | Name  | Example 2 |
      | Email | example@example.org |
      | Company Name | New Company  |
      | Street Line One | 4 Example Way |
      | City            | City          |
      | Region          | Berlin        |
      | Post Code       | 10343         |
      | Country | DE  |
      | Tax Number | DE3489484 |
    Then the brand "Example 2" should have the tax number "DE3489484"