
$(function() {
    // Search Model
    var TwitterSearch = Backbone.Model.extend({});

    // Search Collection
    var TwitterSearchResults = Backbone.Collection.extend({
        model: TwitterSearch,
        url: '/ajax_search.php'
    });

    // Search view
    var TwitterSearchView = Backbone.View.extend({
        el: '#twitter_search_result',
        template: _.template($('#result-item-template').html()),
        render: function() {
            _.each(this.model.models, function(result){
                var att = result.attributes;
                var dt = new Date(att.tweet_date*1000);
                var dt_format = dt.getDate() + "/" + (dt.getMonth()+1)  + "/" + (dt.getFullYear()+1)
                                + " " + dt.getHours() + ":" + dt.getMinutes();
                result.attributes.tweet_date = dt_format;
                var resultTemplate = this.template(result.toJSON());
                $(this.el).append(resultTemplate);
            }, this);
            return this;
        }
    });


    // Bind the search button to set the cookie as well as
    // fetch results from the server
    $(".get-results").bind("click", function(){
        fetchResults($("#search-query").val());

        var d = new Date();
        d.setTime(d.getTime()+(60*24*60*60*1000));
        document.cookie="searchParam="+$("#search-query").val()+"; expires=" + d.toGMTString();
    });
    var refreshInterval = '';

    // Fetches results from the server
    function fetchResults(query) {
        if (query == "") {
            return false;
        }
        var searchResults = new TwitterSearchResults();
        var searchView = new TwitterSearchView({model: searchResults});
        searchResults.reset();
        searchResults.fetch({data: {searchquery : query }, success: function(event){
            $("#twitter_search_result").empty();
            searchView.render();
        }});
    }

    // Returns the value of the cookie
    function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i = 0; i < ca.length; i++) {
            var c = ca[i].trim();
            if (c.indexOf(name)==0) return c.substring(name.length,c.length);
        }
        return "";
    }

    if(getCookie("searchParam") != "") {
        $("#search-query").val(getCookie("searchParam"));
    }

    // Auto refresh the results every 10 seconds
    $(document).ready(function(){
        clearInterval(refreshInterval);
        refreshInterval = setInterval(function(){
            fetchResults(getCookie("searchParam"));
        }, 100000); // Refresh every 100 seconds
    });
});