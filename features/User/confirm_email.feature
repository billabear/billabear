Feature: User confirm email
  In order to use the system with my personal configuration
  As a user
  I need to be able to confirm I gave a valid email during sign up

  Scenario: User confirms correct code
    Given an unconfirmed user "ExistingUser" with the password "RealPassword" and the confirmation code "random-code" exists
    When I confirm the code "random-code"
    Then the user "ExistingUser" will be confirmed

  Scenario: User confirms correct code
    Given an unconfirmed user "ExistingUser" with the password "RealPassword" and the confirmation code "random-code" exists
    When I confirm the code "random-code-two"
    Then the user "ExistingUser" will not be confirmed
