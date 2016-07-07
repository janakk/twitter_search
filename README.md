twitter_search
==============

A twitter search application using Underscore and Backbone
This web application  does "Application Only" authorization to get the
search feeds from Twitter Search api.
1) Add your api settings from twitter in the settings.php file
2) TwitterSearch.php class does the behind the scenes communication with the twitter search api.
   It contains the logic to do the authorization as well as get the search results from the api.
3) ajax_search.php receives ajax request for the search query and then massages the result before
   sending the results back.
4) twitter_search.js contains the Backbone Model, View and collection.
5) index.php file is rendered to the user. It contains the underscore template in it.
6) cacert.pem is used by curl to enable ssl communication with twitter.

What is rendered to the user?
1) from_user_name and user screen_name are linked to the users twitter page
2) profile_image
3) tweet_text
4) create date
5) All the links in the tweet text are linked according to the api
5) All the usernames mentioned in the tweet text are appropriately linked.
6) All the hashtags in the tweet text are appropriately linked.
7) The search results are refreshed every 100 seconds for the user. And user can refresh
   the search results simply by clicking the Search button.


