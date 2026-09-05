# Security Policy

SwiftOrder takes application security seriously.

Because SwiftOrder is business software that may process operational, financial, customer, staff, authentication, and inventory information, security defects are treated as engineering issues requiring controlled investigation and remediation.

## Supported Versions

Security fixes are provided for the current maintained SwiftOrder release line.

| Version | Security Support   |
| ------- | ------------------ |
| 1.1.x   | :white_check_mark: |
| 1.0.x   | :x:                |
| 0.x     | :x:                |

The supported release line may change as new major or minor releases are established.

Unsupported, experimental, development, or obsolete versions should not be treated as security baselines. Users running unsupported versions should upgrade to the current maintained release where possible.

## Reporting a Vulnerability

**Please do not report security vulnerabilities through public GitHub Issues, pull requests, discussions, or other public channels.**

If you discover a potential security vulnerability in SwiftOrder, use GitHub's **Private Vulnerability Reporting** facility for this repository when it is enabled.

Private reporting allows security information to be provided directly to the maintainers without publicly exposing the vulnerability before it has been investigated and, where appropriate, remediated.

When submitting a report, provide as much of the following information as reasonably possible:

* A clear description of the vulnerability.
* The affected SwiftOrder version.
* The affected file, component, endpoint, feature, or workflow.
* The conditions required to reproduce the issue.
* Reproduction steps or a minimal proof of concept.
* Expected behaviour.
* Observed behaviour.
* Potential security impact.
* Whether authentication or specific user privileges are required.
* Any relevant logs, screenshots, requests, or other technical evidence.
* A suggested remediation, if known.

Please remove or redact passwords, authentication tokens, API keys, customer information, personal information, and other confidential data from reports.

## What Should Be Reported Privately?

Examples include, but are not limited to:

* Authentication bypass.
* Broken access control or privilege escalation.
* SQL injection.
* Cross-site scripting.
* Cross-site request forgery where applicable.
* Insecure direct object references.
* Exposure of confidential business or customer information.
* Session or credential-handling vulnerabilities.
* File-upload vulnerabilities.
* Remote or unintended code execution.
* Security-sensitive API or endpoint weaknesses.
* Vulnerable dependency issues that materially affect SwiftOrder.
* Vulnerabilities that could compromise the integrity, confidentiality, or availability of SwiftOrder data or functionality.

A suspected vulnerability should be reported privately even when its security impact is not yet certain.

## Response Process

Security reports are reviewed privately and assessed according to their technical validity, exploitability, affected functionality, affected versions, and potential impact.

Our target response times are:

| Stage                   | Target                                   |
| ----------------------- | ---------------------------------------- |
| Initial acknowledgement | Within 3 business days                   |
| Initial assessment      | Within 7 business days                   |
| Remediation decision    | Based on severity and reproducibility    |
| Security fix            | Prioritised according to risk and impact |

These are response targets, not contractual guarantees.

Complex vulnerabilities may require additional time for reproduction, source analysis, dependency investigation, impact assessment, remediation, testing, or coordinated disclosure.

Where additional information is required, the reporter may be contacted through the private reporting channel.

## Report Outcomes

A report may receive one of the following outcomes:

**Accepted**
The issue has been confirmed as a security vulnerability affecting SwiftOrder.

**Under Investigation**
The available information is insufficient for a final determination, or additional investigation is required.

**Declined**
The report does not represent a security vulnerability within the scope of this policy.

**Duplicate**
The issue has already been reported or is already being addressed.

**Informational**
The finding does not currently represent a material security risk.

A declined or informational report does not necessarily mean the underlying observation is technically incorrect. It means that, after assessment, it does not meet the project's security-vulnerability criteria.

## Severity Assessment

SwiftOrder considers factors including:

* Exploitability.
* Required authentication or privileges.
* Confidentiality impact.
* Integrity impact.
* Availability impact.
* Scope of affected functionality.
* Number and type of affected users.
* Affected versions.
* Likelihood of real-world exploitation.
* Availability of practical mitigations.
* Potential business impact.

Severity may be reassessed as additional evidence becomes available.

## Responsible Disclosure

SwiftOrder supports coordinated and responsible disclosure.

Security researchers are asked to provide maintainers with a reasonable opportunity to investigate, reproduce, and remediate a confirmed vulnerability before publicly disclosing technical details.

Security researchers should not:

* Publicly disclose an unpatched vulnerability.
* Publish exploit code for an unresolved SwiftOrder vulnerability.
* Access, modify, copy, or delete data belonging to other users.
* Obtain credentials, tokens, personal information, or other sensitive information beyond what is necessary to demonstrate the vulnerability.
* Intentionally disrupt SwiftOrder systems or production environments.
* Perform destructive testing.
* Introduce malware or persistent access.
* Conduct high-volume automated testing that could materially affect availability.
* Social-engineer users or maintainers.
* Attempt to compromise third-party systems through SwiftOrder.

Testing should be limited to systems and data for which the researcher has explicit authorization.

Good-faith security research conducted within these boundaries is welcomed.

## Security Remediation

When a vulnerability is confirmed, maintainers may:

1. Reproduce and validate the vulnerability.
2. Determine affected versions and security impact.
3. Identify appropriate remediation.
4. Develop and review the security fix.
5. Test the remediation against the affected workflow.
6. Release the fix where appropriate.
7. Notify affected users or maintainers where reasonably necessary.
8. Publish an appropriate security advisory after remediation.

Security-sensitive changes should be reviewed and verified before release.

Where appropriate, vulnerabilities may be handled through GitHub Security Advisories rather than public issue tracking.

## Disclosure Timing

Security disclosure will normally be coordinated around the availability of an appropriate remediation.

SwiftOrder may delay public disclosure where reasonably necessary to:

* Complete investigation.
* Develop or validate a security fix.
* Coordinate affected releases.
* Allow users reasonable time to apply a security update.
* Prevent unnecessary exploitation of an unresolved vulnerability.

Where a vulnerability requires coordinated disclosure with a reporter or affected dependency maintainers, reasonable efforts will be made to establish an appropriate disclosure timeline.

## Third-Party Dependencies

SwiftOrder relies on third-party software and dependencies.

A vulnerability in a dependency should be reported privately when it materially affects SwiftOrder, its users, or the security of the application.

Dependency vulnerabilities may be addressed through:

* Dependency upgrades.
* Configuration changes.
* Temporary mitigations.
* Removal or replacement of the affected dependency.
* Application-level remediation where appropriate.

Dependency security does not automatically imply that SwiftOrder itself is vulnerable. Impact will be assessed in the context of the way the dependency is used.

## Security Advisories

Confirmed vulnerabilities may be documented through GitHub Security Advisories where appropriate.

A published advisory may include:

* A description of the vulnerability.
* Affected versions.
* Fixed versions.
* Severity information.
* Impact.
* Remediation guidance.
* Appropriate references.
* Reporter credit where agreed.

GitHub supports repository security advisories for privately discussing, fixing, and subsequently publishing vulnerability information.

## Researcher Recognition

SwiftOrder may acknowledge security researchers who provide valid vulnerability reports.

Recognition may be included in a security advisory, release documentation, or other appropriate security record, subject to the researcher's preference and applicable privacy considerations.

Researchers may request that their identity not be publicly disclosed.

SwiftOrder does not currently operate a paid bug bounty programme.

## Scope

This policy applies to security vulnerabilities in the maintained SwiftOrder application and repository code.

Issues unrelated to security should be reported through the project's normal issue or support channels.

This policy does not grant authorization to test systems, accounts, infrastructure, or data that you do not own or have explicit permission to assess.

## Security Philosophy

SwiftOrder treats security as part of the software engineering lifecycle rather than as a final release checklist.

Security work includes protecting:

* Authentication and sessions.
* Authorization and role boundaries.
* Business and customer data.
* Database operations.
* File handling.
* Application endpoints.
* Financial and transactional integrity.
* Third-party dependencies.
* Operational availability.

Security fixes are prioritised according to actual risk and impact rather than simply the number of reported issues.

## Policy Review

This policy is reviewed as the SwiftOrder architecture, release model, security controls, and supported versions evolve.

**Last reviewed:** September 2026
