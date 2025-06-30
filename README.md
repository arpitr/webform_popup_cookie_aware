# Webform Popup

A generic Drupal module that displays a popup with a Webform on node pages.
The popup is hidden after submission using a browser cookie, ensuring it does not break page caching.

## Features

- Popup appears only on selected node types.
- Uses any Webform (default: `contact`).
- Popup is hidden after submission (cookie-based).
- All logic is client-side for cache-friendliness.
- Admin UI to select node types and webform.

## How to Use

1. Enable the module and the Webform module.
2. Place the "Webform Popup Block" in a visible region.
3. Configure at `/admin/config/content/webform-popup` which content types and webform to use.
4. The popup will show on selected node types until submitted.

## Contributing

- Fork and submit PRs for improvements!

## Module Structure

webform_popup/
├── webform_popup.info.yml
├── webform_popup.libraries.yml
├── webform_popup.routing.yml
├── src/
│   ├── Plugin/
│   │   └── Block/
│   │       └── WebformPopupBlock.php
│   └── Form/
│       └── WebformPopupSettingsForm.php
├── js/
│   └── webform_popup.js
├── css/
│   └── webform_popup.css
└── README.md
