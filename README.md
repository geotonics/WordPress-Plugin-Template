WordPress Plugin Template
=========================

A robust code template for creating a standards-compliant WordPress plugin.



## Why this template?

This template is coded to the Wordpress VIP Standard. It includes many features that you might want to use in your plugin. 

## How do I use it?

You can simply copy the files out of this repo and rename everything as you need it, but to make things easier, there is an online builder at http://geotonics.com/plugintemplate/ that will automatically copy the files from this repo, rename everything in the files according to your new plugin name, and initialise a new git repo in the folder if you choose to do so.

### API functions

As of v3.0 of this template, there are a few libraries built into it that will make a number of common tasks a lot easier. I will expand on these libraries in future versions.

#### Registering a new post type

Using the [post type API](https://github.com/geotonics/WordPress-Plugin-Template/blob/master/includes/lib/class-wordpress-plugin-template-post-type.php) and the wrapper function from the main plugin class you can easily register new post types with one line of code. For exapmle if you wanted to register a `listing` post type then you could do it like this:

`WordPress_Plugin_Template()->register_post_type( 'listing', __( 'Listings', 'wordpress-plugin-template' ), __( 'Listing', 'wordpress-plugin-template' ) );`

*Note that the `WordPress_Plugin_Template()` function name and the `wordpress-plugin-template` text domain will each be unique to your plugin after you have used the cloning script.*

This will register a new post type with all the standard settings. If you would like to modify the post type settings you can use the `{$post_type}_register_args` filter. See [the WordPress codex page](http://codex.wordpress.org/Function_Reference/register_post_type) for all available arguments.

#### Registering a new taxonomy

Using the [taxonomy API](https://github.com/geotonics/WordPress-Plugin-Template/blob/master/includes/lib/class-wordpress-plugin-template-taxonomy.php) and the wrapper function from the main plugin class you can easily register new taxonomies with one line of code. For example if you wanted to register a `location` taxonomy that applies to the `listing` post type then you could do it like this:

`WordPress_Plugin_Template()->register_taxonomy( 'location', __( 'Locations', 'wordpress-plugin-template' ), __( 'Location', 'wordpress-plugin-template' ), 'listing' );`

*Note that the `WordPress_Plugin_Template()` function name and the `wordpress-plugin-template` text domain will each be unique to your plugin after you have used the cloning script.*

This will register a new taxonomy with all the standard settings. If you would like to modify the taxonomy settings you can use the `{$taxonomy}_register_args` filter. See [the WordPress codex page](http://codex.wordpress.org/Function_Reference/register_taxonomy) for all available arguments.

To filter post types by taxonomies, simply use ->add_filter() to the Taxonomy object. An example is included. 
 

## What does this template give me?

This template includes the following features:

+ Plugin headers as required by WordPress & WordPress.org
+ Readme.txt file as required by WordPress.org
+ Main plugin class
+ Full & minified Javascript files
+ Grunt.js support
+ Standard enqueue functions for the dashboard and the frontend
+ A library for easily registering a new post type
+ A library for easily registering a new taxonomy
+ A library for handling common admin functions (including adding meta boxes to any post type, displaying settings fields and display custom fields for posts)
+ A complete and versatile settings class 
+ A complete and versatile class for creating example post types and thier metaboxes
* An example of easily adding a taxonomy filter to a post type. 
* An example of a selecting posts of another post type. In the example, you can select Baby Gizmos from a select menu in the Gizmo metabox. 
+ Full text of the GPLv2 license

## I've got an idea/fix for the template

If you would like to contribute to this template then please fork it and send a pull request. I'll merge the request if it fits into the goals for the template and credit you in the [changelog](https://github.com/geotonics/WordPress-Plugin-Template/blob/master/changelog.txt).

## This template is amazing! How can I ever repay you?

There's no need to credit me in your code for this template, but if you would like to buy me some lunch then you can [donate here](http://geotonics.com/donate).
