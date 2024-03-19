Feature: Add Country Tax Rule

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Successfully create tax type
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Currency |
      | United Kingdom | GB       | 1770      | GBP      |
      | United States  | US       | 0         | USD      |
      | Germany        | DE       | 0         | EUR      |
    And there are the following tax types:
      | Name     |
      | Digital Goods |
      | Physical |
    When I create a country tax rule with the following data:
      | Country    | United States |
      | Tax Type   | Digital Goods      |
      | Tax Rate   | 15            |
      | Valid From | -3 days       |
    Then there should be a tax rule for "United States" for "Digital Goods" tax type with the tax rate 15

  Scenario: Fails to create when overlapping valid times
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Currency |
      | United Kingdom | GB       | 1770      | GBP      |
      | United States  | US       | 0         | USD      |
      | Germany        | DE       | 0         | EUR      |
    And there are the following tax types:
      | Name     |
      | Digital Goods  |
      | Physical |
    And the following country tax rules exist:
      | Country        | Tax Type      | Tax Rate | Valid From | Valid Until |
      | United Kingdom | Digital Goods | 17.5     | -10 days   | +10 days    |
    When I create a country tax rule with the following data:
      | Country    | United Kingdom |
      | Tax Type   | Digital Goods       |
      | Tax Rate   | 15             |
      | Valid From | -3 days        |
    Then there should be an error for "validFrom"

  Scenario: Fails to create when overlapping valid times
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Currency |
      | United Kingdom | GB       | 1770      | GBP      |
      | United States  | US       | 0         | USD      |
      | Germany        | DE       | 0         | EUR      |
    And there are the following tax types:
      | Name     |
      | Digital Goods  |
      | Physical |
    And the following country tax rules exist:
      | Country        | Tax Type | Tax Rate | Valid From | Valid Until |
      | United Kingdom | Digital Goods  | 17.5     | -10 days   | +10 days    |
    When I create a country tax rule with the following data:
      | Country     | United Kingdom |
      | Tax Type    | Digital Goods        |
      | Tax Rate    | 15             |
      | Valid From  | -13 days       |
      | Valid Until | -3 days        |
    Then there should be an error for "validUntil"

  Scenario: Successfully create tax type and sets valid until on rule with no valid until
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Currency |
      | United Kingdom | GB       | 1770      | GBP      |
      | United States  | US       | 0         | USD      |
      | Germany        | DE       | 0         | EUR      |
    And there are the following tax types:
      | Name     |
      | Digital Goods  |
      | Physical |
    And the following country tax rules exist:
      | Country       | Tax Type | Tax Rate | Valid From |
      | United States | Digital Goods | 17.5     | -10 days   |
    When I create a country tax rule with the following data:
      | Country    | United States |
      | Tax Type   | Digital Goods      |
      | Tax Rate   | 15            |
      | Valid From | -3 days       |
    Then there should be a tax rule for "United States" for "Digital Goods" tax type with the tax rate 17.5 that is valid until "-3 days"

  Scenario: Successfully create tax type and sets but does not set valid until on rule with no valid until
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Currency |
      | United Kingdom | GB       | 1770      | GBP      |
      | United States  | US       | 0         | USD      |
      | Germany        | DE       | 0         | EUR      |
    And there are the following tax types:
      | Name     |
      | Digital Goods |
      | Physical |
    And the following country tax rules exist:
      | Country       | Tax Type | Tax Rate | Valid From |
      | United States | Digital Goods  | 17.5     | -10 days   |
    When I create a country tax rule with the following data:
      | Country     | United States |
      | Tax Type    | Digital Goods       |
      | Tax Rate    | 15            |
      | Valid From  | -23 days      |
      | Valid Until | -11 days      |
    Then there should be a tax rule for "United States" for "Digital Goods" tax type with the tax rate 17.5 that is open ended