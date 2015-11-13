Feature: Play the game
  The application should accept input from the user in the format "A5" to signify a square to target,
  and feedback to the user whether the shot was success, miss, and additionally report on the sinking of any vessels.

  Background:
    Given there is a 10 x 10 sized board
    And there is a 3 length vertical ship with its first coordinate A5
    And there is a 4 length horizontal ship with its first coordinate F4
    And I am on homepage
    Then I should see "Enter coordinates"

  Scenario: Hit
    When I shoot to A5
    Then I should see "hit a5"

  Scenario: Miss
    When I shoot to A6
    Then I should see "miss"

  Scenario: Sunk
    Given the following shots
      | coordinate |
      | A5         |
      | B5         |
      | C5         |
    Then I should see "sunk"

  Scenario: Win with no miss
    Given the following shots
      | coordinate |
      | A5         |
      | B5         |
      | C5         |
      | F4         |
      | F5         |
      | F6         |
      | F7         |
    Then I should see "Well done! You completed the game in 7 shots"

  Scenario: Win with a miss
    Given the following shots
      | coordinate |
      | A1         |
      | A5         |
      | B5         |
      | C5         |
      | F4         |
      | F5         |
      | F6         |
      | F7         |
    Then I should see "Well done! You completed the game in 8 shots"

  Scenario: Enter show
    When I fill in "coordinate" with "show"
    And I press "submit"
    Then I should see "showing ships"

  Scenario: Enter reset
    When I fill in "coordinate" with "reset"
    And I press "submit"
    Then I should see "game restarted"
