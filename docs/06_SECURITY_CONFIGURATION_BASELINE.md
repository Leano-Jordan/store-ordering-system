# SwiftOrder V1.1 Security Configuration Baseline

## Authentication and sessions

- Use secure password hashing and verification.
- Regenerate sessions at authentication boundaries.
- Use secure, HttpOnly, SameSite cookies.
- Require HTTPS in the production deployment mode.
- Enforce session timeout/logout/termination lifecycle.

## Authorization

- Authenticate first; authorize every protected operation server-side.
- Deny access by default.
- Validate the requested resource/action and the user's role before mutation.
- Test direct URL and POST bypass attempts, not only menu visibility.

## Requests

- Require POST for state-changing operations.
- Require valid CSRF tokens.
- Validate type, range, format and business invariants server-side.
- Treat arrays supplied where strings/integers are expected as invalid input rather than calling string functions on them.

## SQL

- Use prepared statements for externally influenced values.
- Keep transaction boundaries explicit.
- Check statement/result failures before dereferencing return objects.

## Output

- Escape stored/user-controlled values in the correct output context.
- URL-encode dynamic URL path/query values and then HTML-attribute encode the final attribute.
- Avoid untrusted values in inline JavaScript/event-handler contexts.

## Uploads

Current source has useful MIME, size and image checks. Before commercial release, move uploads outside webroot or serve them through a controlled retrieval path.

## Headers

Keep the existing baseline security headers. A Content Security Policy should be introduced only as a tested policy compatible with the current inline-script/event-handler surface; adding a nominal CSP that breaks the POS is not a valid fix.

## Privacy

Apply purpose limitation, data minimisation, access control, retention and incident handling consistent with POPIA and the selected commercial/legal model. Professional legal/privacy review remains required.
