# Craft Uploads Proxy plugin for Craft CMS 4.x and 5.x

Uploads Proxy is a convenient solution for fetching production uploads (images) to a development server on demand. Installed on a live Craft CMS site, it allows you to avoid manually downloading images when setting up a development environment. Instead of downloading the images, you simply add the production domain to your .env file, and the plugin will automatically proxy pull the images into your development site. This saves time and disk space by fetching only the images needed, without the hassle of managing them manually.",

## Requirements

This plugin requires Craft CMS 4.0.0 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require adamgoredev/uploads-proxy

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Stage File Proxy.

## Craft Uploads Proxy Overview
When working with multiple developers on a project, transferring uploaded files between different development environments can become tedious and time-consuming. This plugin simplifies the process by allowing you to maintain just one remote environment, such as a staging server. Any local development environment can then automatically retrieve the required files, keeping everything up-to-date.

For production updates, the live website can serve as the source for all assets. The local environments will only pull the specific files they need, making this especially beneficial for large websites with extensive file libraries.

## Configuring Uploads Proxy

Add the following line to your local .env file: Be sure to include a trailing slash

    UPLOADS_PROXY_REMOTE="http://remote.site.url/"

Optionally add the following line if your images don't live in the "uploads" folder

    UPLOADS_PROXY_BASE_FOLDER="custom/subfolder"


## Using Uploads Proxy

The plugin operates automatically, checking the local file base for a file. If not found, it downloads it from the remote source. This process occurs before any other actions, making it compatible with Image Transform and plugins like Imager X.
