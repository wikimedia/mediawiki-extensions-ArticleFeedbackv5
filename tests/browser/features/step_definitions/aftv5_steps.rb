#
# This file is subject to the license terms in the LICENSE file found in the
# qa-browsertests top-level directory and at
# https://git.wikimedia.org/blob/qa%2Fbrowsertests/HEAD/LICENSE. No part of
# qa-browsertests, including this file, may be copied, modified, propagated, or
# distributed except according to the terms contained in the LICENSE file.
#
# Copyright 2012-2014 by the Mediawiki developers. See the CREDITS file in the
# qa-browsertests top-level directory and at
# https://git.wikimedia.org/blob/qa%2Fbrowsertests/HEAD/CREDITS
#
Given(/^I am at an AFTv5 page$/) do
  visit AFTv5Page
end

When(/^I click Whats this$/) do
  on(AFTv5Page) do |page|
    page.whats_this_element.when_present.click
  end
end
When(/^I click Yes$/) do
  on(AFTv5Page) do |page|
    page.yes_element.when_present.click
  end
end
When(/^I click Yes and No$/) do
  on(AFTv5Page) do |page|
    page.yes_element.exists?
    page.yes_element.when_present.click
    page.back_to_yesno
    page.no_element.when_present.click
    page.back_to_yesno
  end
end

Then(/^AFTv5 should be there$/) do
  on(AFTv5Page) do |page|
    page.yes_element.when_present.should be_visible
    page.no_element.when_present.should be_visible
    page.whats_this_element.when_present.should be_visible
  end
end
Then(/^After saving I have links to feedback page and See all comments available$/) do
  on(AFTv5Page) do |page|
    page.feedback_page_element.when_present.click
    page.all_comments_element.should be_true
  end
end
Then(/^Comments are shown Relevant and All and Sort By$/) do
  on(AFTv5Page) do |page|
    page.most_relevant.should be_true
    page.sort_by_element.should be_true
  end
end
Then(/^I can always return to AFTv5 input$/) do
  on(AFTv5Page) do |page|
    page.yes_element.should be_true
    page.no_element.should be_true
  end
end
Then(/^I can enter and save text$/) do
  on(AFTv5Page) do |page|
    @input_string = "Automated test did this #{('a' .. 'z').to_a.shuffle[0,10].join}"
    page.input_area_element.send_keys "Hello from #{@input_string}"
    page.post_feedback_element.when_present.click
    page.wait_until(10) do
      page.text.include? "Thanks!"
    end
    page.text.should include "Your post can be viewed on this feedback page."
    #ONLY ANONS GET "CREATE ACCOUNT"/LOG IN MESSAGE
    page.create_account_element.should be_true
    page.log_in_element.should be_true
  end
end
Then(/^I have links to Learn more and View Article$/) do
  on(AFTv5Page) do |page|
    page.learn_more_element.should be_true
    page.view_article_element.should be_true
  end
end
Then(/^I see a floating text window with Learn more link$/) do
  on(AFTv5Page) do |page|
    page.text.should include "Wikipedia would like to hear what you think of this article. Share your feedback with the editors -- and help improve this page"
    page.learn_more_element.should be_true
  end
end
Then(/^I see helpful feedback guide and terms$/) do
  on(AFTv5Page) do |page|
    page.helpful_feedback_element.should be_true
    page.terms_element.should be_true
  end
end
Then(/^When I click to navigate to comments page my saved comment appears$/) do
  on(AFTv5Page) do |page|
    page.wait_until(10) do
      page.all_comments_element.visible?
    end
    page.all_comments_element.when_present.click
    page.wait_until(10) do
      page.text.include? @input_string
    end
  end
end
