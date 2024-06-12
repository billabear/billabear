Feature: Brands Create
  In order to white label for new brands
  As an APP user
  I need to be add new brands

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Create brands
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow brands exist:
      | Name    | Code    | Email               |
      | Example | example | example@example.org |
    When I create a new brand
      | Name            | Example Created     |
      | Code            | example_created     |
      | Email           | example@example.org |
      | Company Name    | New Company         |
      | Street Line One | 4 Example Way       |
      | City            | City                |
      | Region          | Berlin              |
      | Post Code       | 10343               |
      | Country         | DE                  |
    Then there should be a brand with the name "Example Created"

  Scenario: Create brands
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow brands exist:
      | Name    | Code    | Email               |
      | Example | example | example@example.org |
    When I create a new brand
      | Name            | Example Created     |
      | Code            | example_created     |
      | Email           | example@example.org |
      | Company Name    | New Company         |
      | Street Line One | 4 Example Way       |
      | City            | City                |
      | Region          | Berlin              |
      | Post Code       | 10343               |
      | Country         | DE                  |
      | Tax Number      | DE858585            |
    Then there should be a brand with the name "Example Created"
    Then the brand "Example Created" should have the tax number "DE858585"

  Scenario: Create brands - Code already exists
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow brands exist:
      | Name    | Code    | Email               |
      | Example | example | example@example.org |
    When I create a new brand
      | Name            | Example Created     |
      | Code            | example     |
      | Email           | example@example.org |
      | Company Name    | New Company         |
      | Street Line One | 4 Example Way       |
      | City            | City                |
      | Region          | Berlin              |
      | Post Code       | 10343               |
      | Country         | DE                  |
    Then there should not be a brand with the name "Example Created"