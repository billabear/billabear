Feature: API Key auth

  Scenario: Valid
    Given the follow api keys exist:
      | Name      | API Key   | Expires At |
      | Key One   | key-one   | +1 year    |
      | Key Two   | key-two   | +3 years   |
      | Key Three | key-three | -3 years   |
    When I use the API key "key-one"
    Then I will get a valid response

  Scenario: View API Key
    Given the follow api keys exist:
      | Name      | API Key   | Expires At |
      | Key One   | key-one   | +1 year    |
      | Key Two   | key-two   | +3 years   |
      | Key Three | key-three | -3 years   |
    When I use the API key "key-four"
    Then I will get an unauthorised error response

  Scenario: View API Key
    Given the follow api keys exist:
      | Name      | API Key   | Expires At |
      | Key One   | key-one   | +1 year    |
      | Key Two   | key-two   | +3 years   |
      | Key Three | key-three | -3 years   |
    When I use the API key "key-three"
    Then I will get an unauthorised error response


