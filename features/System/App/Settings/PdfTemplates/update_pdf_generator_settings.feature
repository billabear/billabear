Feature: Update PDF generation settings

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: mpdf
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I set the pdf generator to:
      | Generator | mpdf     |
      | Tmp Dir   | /tmp/dir |
    Then the pdf generator should be "mpdf"
    And the pdf generator tmp dir should be "/tmp/dir"

  Scenario: mpdf - error
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I set the pdf generator to:
      | Generator | mpdf     |
      | Tmp Dir   |  |
    Then there should be an error for "tmp_dir"

  Scenario: docraptor - error
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I set the pdf generator to:
      | Generator | docraptor     |
      | Tmp Dir   |  |
    Then there should be an error for "api_key"

  Scenario: docraptor - Success
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I set the pdf generator to:
      | Generator | docraptor     |
      | Api Key   | api-test |
    Then the pdf generator should be "docraptor"
    And the pdf generator api key should be "api-test"

  Scenario: wkhtmltopdf - error
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I set the pdf generator to:
      | Generator | wkhtmltopdf     |
      | Tmp Dir   |  |
    Then there should be an error for "bin"

  Scenario: wkhtmltopdf - success
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I set the pdf generator to:
      | Generator | wkhtmltopdf     |
      | Bin   | /usr/bin/wkhtmltopdf |
    Then the pdf generator should be "wkhtmltopdf"
    And the pdf generator bin should be "/usr/bin/wkhtmltopdf"