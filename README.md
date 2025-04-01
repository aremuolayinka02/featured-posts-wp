# RSA Featured Posts Plugin

A WordPress plugin that allows you to manage featured post sections with single post assignments.

## Description

RSA Featured Posts Plugin enables you to create and manage featured sections throughout your WordPress site. Each section can be assigned a single post, making it perfect for highlighting specific content in different areas of your website.

## Features

- Create multiple featured sections
- Assign individual posts to each section
- Elementor integration with custom query support
- Role-based access control
- Clean admin interface
- Custom post type management
- Bulk post assignment capabilities

## Installation

1. Upload the `featured-posts` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Featured Sections' in your WordPress admin panel

## Usage

### Creating a Featured Section

1. Navigate to 'Featured Sections' in your WordPress admin menu
2. Click 'Add New'
3. Give your section a title
4. Select a post to feature from the dropdown menu
5. Click 'Publish'

### Elementor Integration

For Elementor users, each featured section automatically creates a custom query that can be used in Elementor's Posts Widget:

1. Add a Posts Widget to your Elementor page
2. In the widget settings, go to 'Query'
3. Select 'Custom Query' under Query Source
4. Enter the Query ID shown in your featured section

The Query ID is automatically generated from your section title and is displayed in:
- The featured section editor
- The featured sections list view

### Role Management

By default, the plugin grants access to:
- Administrators
- Editors

Additional roles can be managed through the plugin settings.

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- Optional: Elementor Page Builder for enhanced functionality

## Changelog

### 1.0.0
- Initial release
- Featured sections management
- Post assignment capability
- Elementor integration
- Role-based access control

## Support

For support questions, bug reports, or feature requests, please use the [plugin's GitHub repository](insert-github-repo-here).

## License

This plugin is licensed under the GPL v2 or later.
