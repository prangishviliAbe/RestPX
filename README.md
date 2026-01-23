# RestPX - Elementor Add-on

**RestPX** is an Elementor add-on that allows you to seamlessly fetch and display posts from external WordPress sites via the REST API.

## Features

-   **Smart URL Parsing**: Automatically detects:
    -   **Language**: Supports Polylang prefixes (e.g., `/en/`, `/ka/`).
    -   **Categories**: Automatically filters posts when a Category URL is provided (e.g., `/category/news/`).
-   **Elementor Integration**: Dedicated widget with customization options.
-   **Responsive Design**: Beautiful, responsive post cards with featured images.
-   **Performance**: Caches API responses to reduce load times.

## Installation

1.  Upload the plugin files to the `/wp-content/plugins/rest-api-posts` directory, or install the plugin through the WordPress plugins screen.
2.  Activate the plugin through the 'Plugins' screen in WordPress.
3.  Use the **RestPX** widget in Elementor.

## Usage

1.  Drag the **RestPX** widget to your page.
2.  **API URL**: Enter the URL of the external WordPress site.
    -   *Example*: `https://news.iliauni.edu.ge/`
    -   *Specific Category*: `https://news.iliauni.edu.ge/category/office-of-development/`
    -   *Specific Language*: `https://news.iliauni.edu.ge/en/`
3.  **Count**: Set the number of posts to display.
4.  **Language**: (Optional) Javascript language fallback if not present in URL.

## Author

**Abe Prangishvili**
Version: 1.3
