# RediSearch

Redisearch implements a search engine on top of Redis. It has lots of advanced features, like exact phrase matching and numeric filtering for text queries, that are nearly not possible or inefficient with mysql search queries.

Here you find a list of RediSearch features included in the plugin:

**Search**: Instantly find the content you’re looking for. The first time.

**Scoring fields differently**: Give different score to different fields. For example higher score to product name and number than its description.

**Fuzzy Search**: Don't worry about visitors misspelling.

**Autosuggest**: Adds a suggestion string to an auto-complete suggestion dictionary.

**Synonyms**: RediSearch supports synonyms, that is searching for synonyms words defined by the synonym data structure.

And even more features will be added in upcoming versions soon.

Some planned features are:

*   WooCommerce support: RediSearch is a perfect choice for E-commerce websites.
*   Binary documents indexing: Searching through binary files like pdf, word, powerpoint and ...
*   Advanced search: Adding advanced search functionality.

### Installation
1. First, you will need to properly install and configure [Redis](https://redis.io/topics/quickstart) and [RediSearch](https://oss.redislabs.com/redisearch/Quick_Start/).
2. Activate the plugin in WordPress.
3. In the RediSearch settings page, input your Redis host and port and do the configuration.
4. In RediSearch dashboard page, click on Index button.
5. Let you visitors enjoy.


### Frequently Asked Questions

#### What is wrong with WordPress native search?

Although mySql is a great database to storing relational data, It acts very poor on search queries and you must forget about some features like fuzzy matching and synonyms.

#### How Redisearch is compared to ElasticSearch?

Yes, ElasticSearch is a great search engine and it has very good performance compared to mySql. But RediSearch has almost 5 to 10 times better performance and also its way easier to create index, sync your data and send query requests.

### Changelog


##### 0.1.1
* User default values if not set in settings

##### 0.1.0
* Initial plugin
