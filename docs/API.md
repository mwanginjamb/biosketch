# BioSketch API (v1)

The BioSketch API gives other systems read access to researcher profiles (biosketches) and lets profile owners manage their own data.

The main integration use case is **pulling a researcher's profile by their staff ID** (employee number, e.g. `90296`). [Quick start](#quick-start-pull-a-researcher-by-staff-id) covers that end to end. The rest of this document is reference material.

- [Quick start: pull a researcher by staff ID](#quick-start-pull-a-researcher-by-staff-id)
- [Authentication](#authentication)
- [Looking up researchers](#looking-up-researchers)
- [Response reference](#response-reference)
- [Errors](#errors)
- [Write endpoints (profile owners)](#write-endpoints-profile-owners)
- [Code examples](#code-examples)
- [Integration checklist](#integration-checklist)

---

## Basics

| | |
|---|---|
| Base URL | `https://<biosketch-host>/apiv1` |
| Format | JSON in and out. Send `Accept: application/json`. Request bodies may be JSON (`Content-Type: application/json`) or form-encoded. |
| Auth | `Authorization: Bearer <token>` on every request except `GET /apiv1` and `POST /apiv1/auth/token` |
| Timestamps | Unix seconds (`created_at`, `updated_at`, `expires_at`) |
| Dates | `YYYY-MM-DD` (grant `start_date` / `end_date`) |

`GET /apiv1` (no auth) returns a short summary of the API. You can use it as a health check.

---

## Quick start: pull a researcher by staff ID

### 1. Get a token (once)

```bash
curl -X POST https://<biosketch-host>/apiv1/auth/token \
  -H "Content-Type: application/json" \
  -d '{"username": "hr-portal-sync", "password": "********", "name": "HR portal sync"}'
```

```json
{
  "token": "pX3...48 characters...",
  "token_type": "Bearer",
  "expires_at": 1766000000
}
```

Store the token in your secret store. It is shown only once and is valid for 30 days by default.

### 2. Fetch the researcher by staff ID

```bash
curl "https://<biosketch-host>/apiv1/researchers?staff_id=90296&expand=education,publications,grants,identifiers,statement,media" \
  -H "Authorization: Bearer <token>" \
  -H "Accept: application/json"
```

### 3. Read the result

The response is always a list. `staff_id` is unique per account, so it holds **zero or one** researcher:

```json
{
  "items": [
    {
      "id": 187,
      "full_name": "Joan Cherono Langat",
      "staff_id": "90296",
      "department": "SSRC",
      "...": "see Response reference",
      "education": [ ... ],
      "publications": [ ... ]
    }
  ],
  "_links": { "self": { "href": "..." } },
  "_meta": { "totalCount": 1, "pageCount": 1, "currentPage": 1, "perPage": 20 }
}
```

If `items` is empty, there is **no published profile** for that staff ID. Either the employee hasn't signed up, hasn't created a profile, or their profile is still a draft. The API doesn't distinguish between these cases.

---

## Authentication

### Integration account

Tokens belong to a BioSketch user account. For a system-to-system integration, ask a BioSketch administrator to create a **dedicated account** for your system (for example `hr-portal-sync`) rather than using a person's login. The account must have completed email verification, because only active accounts can obtain tokens.

Read access to published profiles needs no special role. Roles change what else an account can do:

| Role | API effect |
|---|---|
| `researcher` (every new account) | Create and manage **their own** profile |
| `drd` | Also read **draft** profiles by ID, including their nested collections (read-only) |
| `admin` | Read, edit and delete any profile |

An administrator assigns roles under **RBAC Mgt → User Role Assignment** in the web app, or with `php yii rbac/assign <role> <username>`. A read-only integration needs no extra role. Give it `drd` only if it must see drafts, and never give it `admin`.

### Issue a token

`POST /apiv1/auth/token`

| Field | Required | Description |
|---|---|---|
| `username` | yes | BioSketch username |
| `password` | yes | BioSketch password |
| `name` | no | Label for the token (max 100 chars), e.g. which system uses it |

Responses:

- `201`: `{ "token", "token_type": "Bearer", "expires_at" }`. `expires_at` is a Unix timestamp, or `null` if tokens are configured not to expire.
- `400`: username or password missing.
- `401`: wrong credentials, or the account is not active.
- `429`: more than **10 token requests per 10 minutes** from your IP. Wait and retry.

Each call issues a **new** token; existing tokens stay valid until they expire or are revoked. Request a token once, reuse it until it expires, and don't request one per API call.

### Use a token

Send it on every request:

```
Authorization: Bearer <token>
```

A missing, unknown, expired or revoked token gets `401`. When you see `401` on a token that used to work, issue a new token and retry once.

### Revoke a token

`DELETE /apiv1/auth/token` with the token to revoke in the `Authorization` header. Returns `204`. Use it when rotating credentials or decommissioning an integration.

### Token lifetime

30 days by default. Server operators can change it with the `api.tokenTtl` application param (seconds; `0` = never expires). Only a SHA-256 hash of each token is stored on the server, so a lost token cannot be recovered, only replaced.

---

## Looking up researchers

`GET /apiv1/researchers`

Returns **published** profiles only (profile status *Active*). Drafts never appear in search results, for any caller.

### Filters

| Parameter | Match | Example |
|---|---|---|
| `staff_id` | exact | `?staff_id=90296` |
| `q` | name or research tags (contains), **or** staff ID (exact) | `?q=langat`, `?q=malaria`, `?q=90296` |
| `primary_institution` | contains | `?primary_institution=KEMRI` |
| `department` | contains | `?department=SSRC` |
| `orcid` | exact | `?orcid=0000-0002-1825-0097` |

Filters can be combined (AND).

For staff-ID lookups use `staff_id`. `q` also matches the staff ID, but it's a broader search: `?q=902` could match names or tags containing "902".

### Related data (`expand`)

By default only the profile fields are returned. Add related sections with `expand` (comma-separated):

| `expand` value | Contents |
|---|---|
| `education` | Degrees |
| `publications` | Publications |
| `grants` | Research grants |
| `identifiers` | External researcher IDs (Scopus, etc.) |
| `statement` | Research statement (single object or `null`) |
| `media` | Links and media |

Example: `?staff_id=90296&expand=education,publications,grants`

`expand` also works on `GET /apiv1/researchers/{id}` and `GET /apiv1/researchers/by-username/{username}`.

### Paging and sorting

| Parameter | Default | Notes |
|---|---|---|
| `page` | `1` | |
| `per-page` | `20` | 1–100 |
| `sort` | `full_name` | `full_name`, `primary_institution`; prefix `-` for descending (`sort=-full_name`) |

Paging details are in `_meta` (`totalCount`, `pageCount`, `currentPage`, `perPage`) and `_links` (`self`, `next`, `last`, …).

### Fetch by BioSketch ID

`GET /apiv1/researchers/{id}`. Once you know a researcher's BioSketch `id` (from a search), you can fetch them directly. A draft profile returns `404` unless you own it or have the `drd` or `admin` role.

### Fetch by username

`GET /apiv1/researchers/by-username/{username}` returns the same researcher object as `GET /apiv1/researchers/{id}`, looked up by the profile owner's BioSketch username (usually the part of their institutional email before the `@`, e.g. `jlangat`):

```bash
curl "https://<biosketch-host>/apiv1/researchers/by-username/jlangat?expand=education,publications" \
  -H "Authorization: Bearer <token>"
```

- Same response shape, `expand` support and draft rules as `/researchers/{id}`.
- `404` if the username doesn't exist, the account has no biosketch, or the profile is a draft you may not see.
- Works for any username, including all-digit ones. `{id}` and `{username}` are separate paths, so they never clash.
- The username is also the last part of the profile's web address: `https://<biosketch-host>/researcher/{username}`.

For system integrations, the **staff ID** lookup (`?staff_id=`) remains the recommended identifier. Usernames are convenient but less stable than employee numbers.

### Nested collections

Each related section is also available as its own paged collection:

```
GET /apiv1/researchers/{id}/educations
GET /apiv1/researchers/{id}/publications
GET /apiv1/researchers/{id}/grants
GET /apiv1/researchers/{id}/identifiers
GET /apiv1/researchers/{id}/media
GET /apiv1/researchers/{id}/{collection}/{record_id}
```

These follow the parent profile's visibility. A draft profile's collections return `404` to anyone but its owner, `drd` or `admin`.

---

## Response reference

### Researcher

```json
{
  "id": 187,
  "user_id": 1,
  "title": "Dr.",
  "full_name": "Joan Cherono Langat",
  "primary_institution": "KEMRI",
  "department": "SSRC",
  "role_title": "Senior Research Scientist",
  "email": "jlangat@kemri.go.ke",
  "website": "https://www.kemri.go.ke",
  "location": "Nairobi, Kenya",
  "era_commons_id": null,
  "orcid": "0000-0002-1825-0097",
  "profile_photo": "https://<biosketch-host>/uploads/researchers/profile/researcher_12_aB3dE9.jpg",
  "status": 1,
  "created_at": 1763380000,
  "updated_at": 1763380000,
  "major_breakthrough": "…",
  "patent_filed": "…",
  "research_tags": "malaria, epidemiology",
  "staff_id": "90296"
}
```

| Field | Notes |
|---|---|
| `id` | BioSketch profile ID |
| `user_id` | BioSketch account ID of the profile owner |
| `staff_id` | Employee number, verified against the staff directory when the owner signed up. `null` if the account has no verified staff record. |
| `department` | Department as entered on the profile |
| `status` | `1` = Active (published), `0` = Draft |
| `profile_photo` | Absolute image URL, or `null` |
| `research_tags` | Comma-separated free text |
| `major_breakthrough`, `patent_filed` | Free text, may contain line breaks |

Any field other than `id`, `user_id`, `full_name`, `status` and `staff_id` may be `null`.

### Expanded sections

All nested records include `id`, `researcher_id` and audit fields (`created_at`, `updated_at`, `created_by`, `updated_by`; grants use `update_by`).

**education[]**: `degree`, `institution_name`, `field_of_study`, `graduation_year`, `sort_order`

**publications[]**: `title`, `journal`, `publication_year`, `doi`, `pmid`, `citation_text`, `is_selected` (1 = highlighted by the researcher), `sort_order`, `imported_from`

**grants[]**: `grant_number`, `title`, `funding_agency_id`, `grant_type_id`, `role_id`, `amount`, `currency`, `start_date`, `end_date`, `status_id`, `description`

**identifiers[]**: `identifier_type` (e.g. `Scopus`), `identifier_value`, `verification_status`

**statement** (object or `null`): `statement_type`, `content`

**media[]**: `media_type`, `file_path` (a URL or path), `file_name`, `mime_type`, `file_size`

### Lookup codes

The API returns numeric codes for these fields:

| Field | Values |
|---|---|
| grant `role_id` | `1` Principal Investigator, `2` Co-Principal Investigator, `3` Investigator, `4` Collaborator, `5` Other |
| grant `status_id` | `1` Active, `2` Completed, `3` Pending, `4` Terminated |
| identifier `verification_status` | `0` Unverified, `1` Pending, `2` Verified, `3` Rejected |
| statement `statement_type` | `research_interests`, `current_projects`, `future_plans`, `personal_statement` |
| grant `funding_agency_id`, `grant_type_id` | IDs from the BioSketch funding-agency and grant-type lists; ask the BioSketch team for the current list |

---

## Errors

Errors use standard HTTP status codes and this body:

```json
{
  "name": "Unauthorized",
  "message": "Your request was made with invalid credentials.",
  "code": 0,
  "status": 401
}
```

| Status | Meaning |
|---|---|
| `400` | Malformed request (e.g. missing required parameter or file) |
| `401` | Missing, invalid, expired or revoked token; or wrong credentials at `/auth/token` |
| `403` | Authenticated, but your roles don't allow this (e.g. changing someone else's profile) |
| `404` | Not found, or a draft profile you're not allowed to see |
| `405` | HTTP method not allowed on this endpoint |
| `409` | Creating a second profile for the same account |
| `422` | Validation failed; body is a list of `{ "field": "...", "message": "..." }` |
| `429` | Rate limited (token endpoint); retry later |
| `5xx` | Server error; retry with backoff |

---

## Write endpoints (profile owners)

These are for clients acting **as a researcher** (for example a mobile app), using that researcher's own token. Admin tokens can perform them on any profile. Read-only integrations can skip this section.

| Method & path | Who | Notes |
|---|---|---|
| `POST /apiv1/researchers` | `researcher` role | Creates the caller's profile. The owner is always the token's account, and any `user_id` in the body is ignored. One profile per account; a second returns `409`. `full_name` is required. |
| `PUT` / `PATCH /apiv1/researchers/{id}` | owner, admin | `user_id`, `profile_photo` and audit fields cannot be changed. |
| `DELETE /apiv1/researchers/{id}` | owner, admin | Deletes the profile. Returns `204`. |
| `POST /apiv1/researchers/{id}/photo` | owner, admin | `multipart/form-data` with the image in field `attachment`. JPG/PNG, max 3 MB. |
| `POST /apiv1/researchers/{id}/{collection}` | owner, admin | Add an education/publication/grant/identifier/media record. `researcher_id` is taken from the URL. |
| `PUT` / `PATCH /apiv1/researchers/{id}/{collection}/{record_id}` | owner, admin | Update a record. It cannot be moved to another researcher. |
| `DELETE /apiv1/researchers/{id}/{collection}/{record_id}` | owner, admin | Returns `204`. |

Required fields: education needs `degree` and `institution_name`. Validation errors come back as `422`.

---

## Code examples

### Python (requests)

```python
import os
import requests

BASE = "https://<biosketch-host>/apiv1"
session = requests.Session()
session.headers["Accept"] = "application/json"


def get_token() -> str:
    r = requests.post(f"{BASE}/auth/token", json={
        "username": os.environ["BIOSKETCH_USER"],
        "password": os.environ["BIOSKETCH_PASSWORD"],
        "name": "hr-portal-sync",
    }, timeout=30)
    r.raise_for_status()
    return r.json()["token"]


def researcher_by_staff_id(staff_id: str) -> dict | None:
    params = {"staff_id": staff_id, "expand": "education,publications,grants,identifiers,statement,media"}
    for attempt in range(2):
        r = session.get(f"{BASE}/researchers", params=params, timeout=30)
        if r.status_code == 401 and attempt == 0:   # token expired or revoked: renew once
            session.headers["Authorization"] = f"Bearer {get_token()}"
            continue
        r.raise_for_status()
        items = r.json()["items"]
        return items[0] if items else None   # None: no published profile for this staff ID


session.headers["Authorization"] = f"Bearer {os.environ.get('BIOSKETCH_TOKEN') or get_token()}"
profile = researcher_by_staff_id("90296")
```

### PHP (Yii2 httpclient)

```php
use yii\httpclient\Client;

$client = new Client(['baseUrl' => 'https://<biosketch-host>/apiv1']);

$response = $client->get('researchers', [
    'staff_id' => '90296',
    'expand' => 'education,publications,grants',
], [
    'Authorization' => 'Bearer ' . $token,
    'Accept' => 'application/json',
])->send();

if ($response->statusCode === '401') {
    // token expired or revoked: issue a new one and retry
}

$researcher = $response->isOk ? ($response->data['items'][0] ?? null) : null;
```

### JavaScript (fetch)

```js
const res = await fetch(
  `${BASE}/researchers?staff_id=${encodeURIComponent(staffId)}&expand=education,publications`,
  { headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' } },
);
if (!res.ok) throw new Error(`BioSketch API ${res.status}`);
const { items } = await res.json();
const researcher = items[0] ?? null;
```

Keep tokens on the server side. Don't ship them in browser or mobile code that end users can inspect.

---

## Integration checklist

- [ ] A dedicated, email-verified BioSketch account exists for your system (not a person's login).
- [ ] The token is stored in a secret store, reused until it expires, and renewed automatically on `401`.
- [ ] Lookups use `?staff_id=` (exact), not `q`.
- [ ] An empty `items` list is handled as "no published profile", not as an error.
- [ ] Only the `expand` sections you need are requested.
- [ ] `429` and `5xx` are retried with backoff.
- [ ] Tokens are revoked (`DELETE /apiv1/auth/token`) when an integration is retired or credentials rotate.

### Local development only

On a server running with `YII_ENV=dev`, the token may also be passed as a query parameter (`?access-token=<token>`). This is disabled in production because URLs end up in logs. Always use the `Authorization` header in real integrations.
