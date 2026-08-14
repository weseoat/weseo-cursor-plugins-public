# REST API Connection Test

Full verification of the WordPress REST API connection: list all reachable content types and options pages. All calls run over the shell with HTTP basic auth from the repo-root `.env` (`WSO_SITE_URL`, `WSO_BRIDGE_USER`, `WSO_BRIDGE_APP_PASSWORD`). Talk to the user in German; keep commands and outputs as they are.

## Prepare The Auth Header

Load the `.env` and build the basic-auth header once per session:

**PowerShell (Windows):**

```powershell
Get-Content .env | Where-Object { $_ -match '^\s*WSO_' } | ForEach-Object {
    $name, $value = $_ -split '=', 2
    Set-Item -Path "Env:$($name.Trim())" -Value $value.Trim()
}
$pair    = "$($env:WSO_BRIDGE_USER):$($env:WSO_BRIDGE_APP_PASSWORD)"
$token   = [Convert]::ToBase64String([Text.Encoding]::UTF8.GetBytes($pair))
$headers = @{ Authorization = "Basic $token" }
```

**curl (macOS/Linux):** `set -a; source .env; set +a` and per call
`--user "$WSO_BRIDGE_USER:$WSO_BRIDGE_APP_PASSWORD"`.

Never print the loaded secret values; only report whether the variables are set.

## Step 1: Check The Connection

```powershell
Invoke-RestMethod -Uri "$($env:WSO_SITE_URL)/wp-json/wp/v2/types" -Headers $headers
```

If the call fails, give the user a clear error message with likely causes:

- Credentials wrong (check `.env`: `WSO_BRIDGE_USER`, `WSO_BRIDGE_APP_PASSWORD`)
- `WSO_SITE_URL` wrong (base URL without `/wp-json`, no trailing double slash)
- HTTPS not active (required for application passwords)
- The user is not an administrator

On success, confirm the connection and show the connected domain (`WSO_SITE_URL`).

## Step 2: List Post Types

List all registered post types as a table from `/wp-json/wp/v2/types`, including both standard post types (post, page, attachment) and custom post types:

| Name | Post type (slug) | Hierarchical |
| ---- | ---------------- | ------------ |

Post types missing here despite existing in the admin usually lack `show_in_rest` — route back to the REST exposure step of `setup-local-project`.

## Step 3: Sample Content Per Post Type

Fetch the first 3 entries for each public post type using the `rest_base` from `/wp-json/wp/v2/types`:

```powershell
Invoke-RestMethod -Uri "$($env:WSO_SITE_URL)/wp-json/wp/v2/{rest_base}?per_page=3" -Headers $headers
```

Format per post type:

| Name | ID  | Link | Status |
| ---- | --- | ---- | ------ |

The Link column is the WordPress admin edit link (`{WSO_SITE_URL}/wp-admin/post.php?post={ID}&action=edit`). Skip post types without REST support or whose fetch fails (for example `attachment`).

## Step 4: Check ACF Options Pages

The options pages run over the project-specific endpoints (`wso/v1/options/{slug}`, see `reference/acf-options-rest-endpoints.php`). Common slugs:

- `wso-website-settings` - company data (name, phone, e-mail, address, social media)
- `wso-logo-settings` - logo configuration
- `wso-animation-settings` - animation settings
- `404-options` - 404 error page
- `acf-menue` - burger menu settings
- `acf-img-switch` - image switch (default/alternative)

Fetch each options page individually and show a compact overview of the contained fields/values:

```powershell
Invoke-RestMethod -Uri "$($env:WSO_SITE_URL)/wp-json/wso/v1/options/wso-website-settings" -Headers $headers
```

If a slug returns 404, skip it and note that the options page is not (yet) available over REST — the endpoint install from the REST exposure step of `setup-local-project` may still be pending, or the project does not use that page.

## Step 5: Summary

Report compactly:

- **Connection status:** connected / error
- **Website:** the domain (`WSO_SITE_URL`)
- **Post type count:** X (Y of them custom)
- **Content count:** total entries found per type
- **ACF options pages:** available / not available (with the list of reachable slugs)
