Feature: User request password reset
  In order to use the system with my personal configuration when I forget my password
  As a user
  I need to be able to request to reset my password

  Scenario: Unconfirmed user
    Given a confirmed user "ExistingUser" with the password "RealPassword" exists
    When I request to reset my password for "ExistingUser"
    Then there will be a new password reset code for "ExistingUser"

  Scenario: Confirmed User
    Given an unconfirmed user "ExistingUser" with the password "RealPassword" exists
    When I request to reset my password for "ExistingUser"
    Then there will be a new password reset code for "ExistingUser"
