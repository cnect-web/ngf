@api
Feature: Site is installed
  Check installation
  As a developer
  I want to know if the project is installed

  Scenario: Verify that the website is accessible and the Header is visible.
    Given I am on the homepage
    Then I see the text "No front page content has been created yet."
