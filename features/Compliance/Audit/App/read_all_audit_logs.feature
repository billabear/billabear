Feature: Read all audit logs

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |
    And the follow products exist:
      | Name        | External Reference |
      | Product One | prod_jf9j545       |
    And the following features exist:
      | Name        | Code        | Description     |
      | Feature One | feature_one | A dummy feature |
      | Feature Two | feature_two | A dummy feature |
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price "Price One" with:
      | Name       | Test Plan |
      | Public     | True      |
      | Per Seat   | False     |
      | User Count | 10        |
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    | Billing Type |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One | invoice      |

  Scenario: Read all audit logs
    Given the following audit logs exist:
      | Message        | Created At  |
      | A message here | -30 seconds |
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I go to the audit logs page
    Then I should see the audit log "A message here"