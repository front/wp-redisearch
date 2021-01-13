=== RediSearch ===
Contributors: foadyousefi
Author URI: https://7km.co
Plugin URI: https://github.com/7kmCo/wp-redisearch
Donate link: https://www.paypal.me/foadyousefi
Tags: search, redisearch, redis, fuzzy, aggregation, searching, autosuggest, suggest, advanced search, woocommerce
Requires at least: 5.0
Tested up to: 5.6
Stable tag: 0.3.2
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Flexible search engine for WordPress with very high performance.

== Description ==

Redisearch implements a search engine on top of Redis. It has lots of advanced features, like exact phrase matching and numeric filtering for text queries, that are nearly not possible or inefficient with mysql search queries.


== IMPORTANT ==
__The latest version of this plugin supports RediSearch version 2.0 (or higher) which runs on Redis 6.0 (or higher). If you have version prior to 2.0, please use this plugins 0.2.7.__

Here you find a list of RediSearch features included in the plugin:

__Search__: Instantly find the content you’re looking for. The first time.

__Scoring fields differently__: Give different score to different fields. For example higher score to product name and number than its description.

__Fuzzy Search__: Don't worry about visitors misspelling.

__Autosuggest__: Adds a suggestion string to an auto-complete suggestion dictionary.

__Synonyms__: RediSearch supports synonyms, that is searching for synonyms words defined by the synonym data structure.

### Existing features

* WooCommerce: Index and search through most of existing products meta data.
* Document: Index content of binary files such as pdf, word, excel and powerpoint.
* Synonym: Adding synonym groups is simple. Just add each comma separated group on a new line in synonym settings and done.
* Live search (aka autosuggest): Search as you type regardless of misspelling.

== Installation ==
1. First, you will need to properly [install and configure](https://redis.io/topics/quickstart) Redis and [RediSearch](https://oss.redislabs.com/redisearch/Quick_Start/).
2. Activate the plugin in WordPress.
3. In the RediSearch settings page, input your Redis host and port and do the configuration.
4. In RediSearch dashboard page, click on Index button.
5. Let you visitors enjoy.

Optionaly, you can pass settings in your wp-config.php file like following. If you are using [Redis Object Cache](https://wordpress.org/plugins/redis-cache/) plugin, these settings may already exist.

define('WP_REDIS_HOST', '127.0.0.1');
define('WP_REDIS_PORT', '6379');
define('WP_REDIS_PASSWORD', 'your-password');
define('WP_REDIS_INDEX_NAME', 'indexName');

== Frequently Asked Questions ==

= What is wrong with WordPress native search? =

Although mySql is a great database to storing relational data, It acts very poor on search queries and you must forget about some features like fuzzy matching and synonyms.

= How Redisearch is compared to ElasticSearch? =

Yes, ElasticSearch is a great search engine and it has very good performance compared to mySql. But RediSearch has almost 5 to 10 times better performance and also its way easier to create index, sync your data and send query requests.

== Changelog ==

= 0.3.2 =
* FIXED: Fix issue while creating/updating/deleting posts

= 0.3.1 =
* FIXED: Fix an issue with live search

= 0.3.0 =
* Updated: Implemented RediSearch version 2.0

= 0.2.7 =
* FIXED: Fix some bugs.

= 0.2.6 =
* FIXED: Fix a bug preventing saving og feature settings.

= 0.2.5 =
* Added: Get index name option from wp-config
* Added: Option for disabling stop words
* Added: Adding a comma separated and user defined list of stop words
* Fixed: Make search fields parent elements position to 'relative' so auto suggestion will appear in correct place

= 0.2.4 =
* Fix: Fix admin js and css files enqueue directory name case issue

= 0.2.3 =
* Added: Added password option.
* Added: Ability to set redis server configurations in wp-config.php file.

= 0.2.2 =
* Added: Document feature for indexing binary file contents
* Added: Filter hook 'wp_redisearch_indexable_post_status' to manipulate indexable post status
* Added: Filter hook 'wp_redisearch_before_admin_wp_query' Applies to main query args. This is mainly for showing number of indexable posts
* Added: Filter hook 'wp_redisearch_after_admin_wp_query' Applies after main query and recieves args and the $query object. This is mainly for showing number of indexable posts
* Added: Filter hook 'wp_redisearch_before_index_wp_query' Applies to main query args. This hook is for manipulating arguments for indexing process
* Added: Filter hook 'wp_redisearch_after_index_wp_query' Applies after main query and recieves args and the $query object. This hook is for manipulating $query object used for indexing posts

= 0.2.1 =
* Added: WooCommerce support added as Feature
* Fixed: Return option values if empty string stores in database
* Fixed: Fix incorrect link to settings page
* Fixed: Fix harcoded index name in WP-CLI INFO command
* Added: filter hook 'wp_redisearch_indexable_temrs' to manipulate indexable terms list
* Added: filter hook 'wp_redisearch_indexable_post_types' to manipulate indexable post types

= 0.2.0 =
* Added: WP-CLI support
* Added: Register and activating of Features
* Added: filter hook 'wp_redisearch_indexable_meta_keys' to add extra meta keys to the index
* Added: filter hook 'wp_redisearch_indexable_meta_schema' to manipulate type of post meta fields (default is text)
* Added: action hook 'wp_redisearch_after_post_indexed' fires after posts indexed from the main index command
* Added: action hook 'wp_redisearch_after_post_published' fires after a post have been published
* Added: action hook 'wp_redisearch_after_post_deleted' fires after a post have been deleted
* Added: action hook 'wp_redisearch_after_index_created' fires after main index created
* Added: action hook 'wp_redisearch_settings_indexing_fields' fires after settings fields inside indexing options page
* Fixed: Fix indexing posts on publish/update

= 0.1.1 =
* Use default value for settings if not set in settings

= 0.1.0 =
* Initial plugin
