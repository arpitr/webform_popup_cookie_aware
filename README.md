# Webform Popup Cookie Aware

A flexible Drupal module that displays a popup with a configurable Webform on selected node types. The popup is hidden after submission using a browser cookie, with cookie expiry configurable per content type. The webform is **lazy loaded** via AJAX for performance.

---

## Features

- Popup appears only on selected content types (node bundles).
- Admin can map each content type to a specific Webform and set cookie expiry (in days).
- Popup is hidden after submission using a browser cookie (per webform).
- Webform is lazy loaded via AJAX only when the popup is shown.
- All logic is cache-friendly and works with Drupal 10/11.
- Block output is cached per node type (`node.type` cache context).

---

## Configuration Steps

1. **Enable the module** and ensure the [Webform](https://www.drupal.org/project/webform) module is enabled.
2. **Place the "Webform Popup Cookie Aware Block"** in a visible region (e.g., Content region) via the Block Layout UI.
3. **Configure the module** at `/admin/config/content/webform-popup-cookie-aware`:
   - For each content type, select the Webform to display and set the cookie expiry (in days).
   - Save configuration.
4. **Add the Webform Popup Cookie Aware handler** to each Webform you want to use (under Webform > Handlers > Add handler > "Webform Popup Cookie Aware: Set Cookie").
5. **Test** by visiting a node of a configured content type. The popup should appear if the cookie is not set, and disappear after submission.

---

## Flow Diagram

```mermaid
flowchart TD
    A[User visits a node page] --> B{Is node type configured for popup?}
    B -- No --> Z[No popup shown]
    B -- Yes --> C[Get mapped Webform and cookie expiry]
    C --> D{Is cookie for this webform set?}
    D -- Yes --> Z
    D -- No --> E[Show popup with Webform placeholder]
    E --> F[AJAX loads Webform into popup]
    F --> G{User submits form?}
    G -- No --> F
    G -- Yes --> H[Server sets cookie with configured expiry]
    H --> I[Popup hidden for future visits]
```

---

## Example Configuration Table


| Content Type | Webform | Cookie Expiry (days) |
| ------------ | ------- | -------------------- |
| Article      | Contact | 10                   |
| Basic page   | Demo    | 365                  |

---

## Code Structure

```
webform_popup_cookie_aware/
├── css/
│   └── webform_popup_cookie_aware.css
├── js/
│   └── webform_popup_cookie_aware.js
├── src/
│   ├── Controller/
│   │   └── WebformPopupCookieAwareController.php
│   ├── EventSubscriber/
│   │   └── WebformPopupCookieAwareCookieSubscriber.php
│   ├── Form/
│   │   └── WebformPopupCookieAwareSettingsForm.php
│   ├── Plugin/
│   │   ├── Block/
│   │   │   └── WebformPopupCookieAwareBlock.php
│   │   └── WebformHandler/
│   │       └── WebformPopupCookieAwareHandler.php
├── webform_popup_cookie_aware.info.yml
├── webform_popup_cookie_aware.libraries.yml
├── webform_popup_cookie_aware.routing.yml
├── webform_popup_cookie_aware.services.yml
└── README.md
```

---

## Developer Notes

- The module uses a Webform handler to set the cookie server-side for reliability.
- The popup block uses render arrays for proper form rendering and cacheability.
- Cookie expiry is passed to the frontend and respected by both JS and server logic.
- The webform is lazy loaded via AJAX using a custom controller.
- Block output is cached per node type using the `node.type` cache context.
- Follows Drupal coding standards and best practices.

---

## Contributing

Contributions and suggestions are welcome! Please open issues or pull requests on the project repository.

---
