<?php
/**
 * Created by PhpStorm.
 * User: janak
 * Date: 2014-04-18
 * Time: 12:44 PM
 */

?>
<html lang="en">
<head>
  <title>Twitter Search</title>
  <meta charset="utf-8">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
  <script src="http://documentcloud.github.io/underscore/underscore-min.js"></script>
  <script src="http://documentcloud.github.io/backbone/backbone-min.js"></script>
  <script src="/script/twitter_search.js"></script>
  <link rel="stylesheet" href="/css/twitter_search.css">
</head>
<body>
<div id="twitter_search">
  <header>
    <h1>Twitter Search</h1>
    <input id="search-query" name="search-query" type="text" placeholder="Enter your search query">
    <a href="#" class="get-results">Search</a>
  </header>
</div>
<div id="twitter_search_result"></div>

<script type="text/template" id="result-item-template">
  <div class='result_item'>
    <div class='user-image'>
      <img src="<%= user_image%>"/>
    </div>
    <div class='tweet-text'>
      <div class="user">
        <a href="https://twitter.com/<%= screen_name %>" class="user-name"><%=user_name%></a>
        <a href="https://twitter.com/<%= screen_name %>" class="user-screen-name">@<%=screen_name%></a><br/>
      </div>
      <%= tweet_text%>
      <div class="tweet-date"><%= tweet_date%></div>
    </div>
  </div>
</script>
</body>
</html>