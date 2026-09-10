# SwiftOrder Project Memory

## Deployment Architecture Decision — 10 September 2026

### Confirmed V1 direction
SwiftOrder V1 is a locally hosted PHP/MySQL business application. One host device runs the SwiftOrder application and authoritative MySQL database. Other supported devices access that same installation through a local network using a web browser/client interface.

### Device model
- Windows desktop PC: host + SwiftOrder user interface.
- Windows laptop: host + SwiftOrder user interface, or client to another host where applicable.
- Windows tablet: client to the local SwiftOrder host.
- Android tablet: client/browser to the local SwiftOrder host.
- Android/iPhone: client/browser for secondary/mobile operations, not required to host PHP/MySQL.
- Payment terminal: separate payment hardware/peripheral, not the default SwiftOrder host.
- V1 tills are deliberately excluded from this deployment decision for now.

### Core architecture principle
One local installation = one authoritative application/database for that business installation. Multiple client devices must operate against the same database rather than maintaining separate databases and synchronising them.

Conceptual topology:

HOST PC
- PHP
- MySQL
- SwiftOrder application

        ↓ local LAN / Wi-Fi

PC / laptop / tablet / phone browsers

### Offline meaning
Core SwiftOrder operations must continue when internet connectivity is unavailable, provided the local host and local network remain operational. Internet connectivity must not be required for normal local trading. Internet-only integrations remain outside the V1 trading path.

### Important distinction
"Supported on Android/iPhone" means client/browser access to a local SwiftOrder host. It does not currently mean PHP/MySQL are installed natively on the phone/tablet.

### Deployment requirement to define next
The product must eventually provide a practical customer installation process for the host device and a simple way for other devices on the same local network to discover/connect to that installation.

The expected customer experience needs to cover, at minimum:
1. Installing the supported PHP-capable web server/runtime and MySQL-compatible database on the host.
2. Installing/initialising the authoritative SwiftOrder application and database schema.
3. Configuring business settings, secrets and initial administrator.
4. Establishing the host's local network address in a reliable way.
5. Allowing client devices through the required local firewall/network rules without exposing the installation unnecessarily to the public internet.
6. Providing an easy connection/discovery mechanism for client devices, preferably avoiding customers manually hunting for IP addresses where practical.
7. Testing multiple client devices against the same host/database.
8. Providing backup/restore and recovery procedures as part of installation and acceptance.
9. Recording installation identity, software version and licence identity.

### Product architecture implication
The deployment/install mechanism is a product requirement, not merely marketing documentation. The V1 specification already requires local PHP/MySQL deployment, no internet dependency for core trading, a documented installation lifecycle, and backup/restore evidence. Future implementation should turn those requirements into an actual repeatable installer/deployment package rather than expecting customers to manually assemble a development environment.

### Scope discipline
Do not introduce a native Android/iOS application, cloud synchronisation, multi-tenant SaaS, or separate per-device databases merely to solve this V1 deployment problem. First solve reliable local-host + local-client deployment using the existing PHP/MySQL architecture.

### Current status labels
- Local PHP/MySQL host: CONFIRMED by V1.1 specification.
- Multiple browser clients sharing one local installation: ARCHITECTURAL DECISION / TARGET, requiring implementation and acceptance verification.
- Customer-facing installer: MISSING REQUIREMENT DETAIL / TO BE DESIGNED.
- Automatic device discovery: UNDECIDED; investigate simplest reliable V1 mechanism.
- Native mobile app: V1 EXCLUSION unless separately approved.

## User Decision / Communication Preference
The user is the final decision-maker and last line of defence. Explain architecture in ordinary-language terms before using technical terminology. Do not assume technical concepts are understood. Keep discussions bite-sized and deliberate. Do not make the user build or sell a system they cannot personally understand.

## Source Authority
Current GitHub repository is the authoritative source for implementation. The approved SwiftOrder V1.1 specification is the authoritative product target. Engineering memory records decisions and verified/inferred state but must never be treated as evidence by itself.
