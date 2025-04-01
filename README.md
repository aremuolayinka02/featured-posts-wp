# RSA Featured Plugin

A WordPress plugin that allows you to manage featured post sections with single post assignments. Seamlessly integrates with Elementor for displaying featured posts in your layouts.

## Features

- Create multiple featured sections
- Assign individual posts to each section
- Elementor integration via Custom Query ID
- Clean and intuitive admin interface
- Automatic query generation for Elementor loops

## Installation

1. Download the plugin zip file
2. Go to WordPress admin → Plugins → Add New
3. Click "Upload Plugin" and select the zip file
4. Click "Install Now" and then "Activate"

## Usage

### Creating Featured Sections

1. In WordPress admin, go to "RSA Featured"
2. Click "Add New"
3. Enter a name for your featured section
4. Select a post from the dropdown menu
5. Click "Publish" to save

### Managing Featured Sections

- View all your featured sections under the "RSA Featured" menu
- Edit a section to change its name or assigned post
- Use the quick-view table to see all assignments at once

### Elementor Integration

1. Create or edit a page with Elementor
2. Add a "Posts" widget or "Loop Grid" widget
3. In the widget settings, go to "Query" section
4. Set "Query Type" to "Custom Query"
5. Enter your section's Query ID (found in the featured section edit screen)

## Query IDs

Each featured section automatically generates a Query ID based on its title. For example:
- Section Title: "Homepage Hero"
- Query ID: `homepage-hero`

You can find the Query ID in:
1. The featured section edit screen
2. The featured sections list table under the "Query ID" column

## Requirements

- WordPress 5.0 or higher
- Optional: Elementor Page Builder for advanced layout integration

## Notes

- Each featured section can only have one assigned post
- Posts must be published to be available for selection
- Query IDs are automatically generated and updated when you change a section's title

## Support

For support or feature requests, please contact the plugin author.

## Screenshots

[Coming Soon]
# featured-posts-wp
