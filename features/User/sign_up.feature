Feature: User sign up
  In order to use the site repeatedly and have it remember who I am
  A Customer
  I need to be able to sign up

  Scenario: User already exists
    Given a confirmed user "parthenon.user@example.org" with the password "RealPassword" exists
    Given I have given the field "email" the value "parthenon.user@example.org"
    And I have given the field "password" the value "randomP@ssw0rld!"
    When I try to sign up
    Then there will not be a new user registered

  Scenario: Sign up with a password with code
    Given the invite code "invite-code" exists
    And I have given the field "email" the value "parthenon.user@example.org"
    And I have given the field "password" the value "randomP@ssw0rld!"
    When I try to sign up with the code "invite-code"
    Then there will be a new user registered
    And the invite code "invite-code" will have been used by "parthenon.user@example.org"

  Scenario: Sign up with a password  with code and role
    Given the invite code "invite-code" with role "ROLE_ADMIN" exists
    And I have given the field "email" the value "parthenon.user@example.org"
    And I have given the field "password" the value "randomP@ssw0rld!"
    When I try to sign up with the code "invite-code"
    Then there will be a new user registered
    And the invite code "invite-code" will have been used by "parthenon.user@example.org"
    And the user "parthenon.user@example.org" will have the role "ROLE_ADMIN"
