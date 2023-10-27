# Post Type Registration from JSON

A simple plugin to register post types from a JSON file, and allows a template to be 
registered for each post-type using the Gutenberg/HTML syntax. 

## Installation
Download and install this as a WP Plugin. 

## Usage
Create a JSON file in the root of your theme called `post-types.json`.
A schema for this file exists called `post-type.schema.json` in the root of this plugin.

You can also create a template-file for each post-type in the `post-type-templates` directory in your theme. 

## Example
Example of `wp-content\themes\{theme_name}\post-types.json` 

```json
{
	"post_types": {
		"example": {
			"labels": {
				"name": "Examples",
				"singular_name": "Example"
			},
			"menu_icon": "dashicons-star",
			"taxonomies": [ "category", "post_tag" ]
		}
	}
}
```
This will register a post-type called `example` with the labels `Examples` and `Example` in the admin menu.
The args for the post-type are defined in [register_post_type()](https://developer.wordpress.org/reference/functions/register_post_type/).

Example of `wp-content\themes\{theme_name}\post-type-templates\example.html` 

```html
<!-- wp:paragraph {align:"left"} -->
<p>Example Template</p>
<!-- /wp:paragraph -->
```

This will register the default template for when a new post of type `example` is created.

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.