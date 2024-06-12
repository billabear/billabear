Feature: Tax Settings

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I update the tax system settings to:
      | Tax Customers with Tax Number | false |
    Then the tax settings should be tax customers with tax number is false

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I update the tax system settings to:
      | Tax Customers with Tax Number | true |
    Then the tax settings should be tax customers with tax number is true

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I update the tax system settings to:
      | EU Business Tax Rules | false |
    Then the tax settings for eu business tax rules should be false

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I update the tax system settings to:
      | EU Business Tax Rules | true |
    Then the tax settings for eu business tax rules should be true