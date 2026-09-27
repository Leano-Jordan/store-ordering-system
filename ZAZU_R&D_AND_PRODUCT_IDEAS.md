# ZAZU — R&D AND PRODUCT IDEAS
## Living product-discovery notebook

> Purpose: capture validated observations, product hypotheses, workflow ideas, research findings, and architecture questions while Zazu evolves.
>
> This is an R&D document, not an implementation specification. Ideas must be validated against the current product, real workflows, engineering constraints, and owner decisions before implementation.

---

## 1. Core Product Direction

Zazu should behave like an operational system that adapts to the business rather than a fixed collection of modules.

The system should learn the business configuration from onboarding and then expose the most relevant workflows first.

Core principle:

**Configure the experience, not the underlying truth.**

A catering business, event planner, venue, rental business, decorator, bakery, photographer, mobile food operator, or recurring meal provider may use different terminology and workflows while sharing underlying concepts.

Potential shared domain concepts:

- Client
- Job / Event / Project
- Offering
- Requirement
- Resource
- Schedule
- Task
- Document
- Expense
- Payment
- Supplier
- Staff / Labour
- Stock / Quantity
- Status
- Activity / Audit

Do not force unrelated concepts into one generic database meaning simply to achieve reuse.

---

# 2. Reusable Item / Resource Library

## Product idea

Zazu should maintain reusable libraries so users do not repeatedly type the same things.

Example:

A caterer is configuring an event and needs green pepper.

If green pepper exists:

**Select → set quantity → use**

If it does not exist:

**+ Add green pepper → choose category/unit → Save & use**

The newly created item becomes available for future jobs.

This should work across many domains.

Examples:

- Ingredients
- Food items
- Packaging
- Disposables
- Beverages
- Equipment
- Rentals
- Labour/resources
- Livestock / event resources
- Services
- Other business-specific resources

## Important separation

Avoid one giant undifferentiated inventory table.

Potential hierarchy:

**MASTER LIBRARY**
System-provided reusable starting vocabulary.

↓

**BUSINESS LIBRARY**
Items actually adopted or created by this business.

↓

**JOB REQUIREMENTS**
What a particular job needs.

↓

**ACTUAL STOCK / RESOURCES**
What the business physically has, has purchased, has allocated, has used, or has returned.

The same reusable item may exist in the business library without being stocked.

---

# 3. Packaging and Disposables

Packaging is not a cosmetic detail. It can materially affect job cost and operational planning.

Potential categories:

### Food packaging
- Meal containers
- Foil trays
- Cling film
- Foil
- Takeaway bags
- Boxes
- Portion containers

### Table service
- Paper plates
- Plastic plates
- Reusable plates
- Cups
- Glasses
- Cutlery
- Serving utensils
- Napkins
- Table coverings

### Operational consumables
- Gloves
- Cleaning materials
- Bin bags
- Labels
- Tape
- Food-safe storage materials

Useful attributes may include:

- Material
- Size
- Unit
- Pack quantity
- Cost per pack
- Cost per usable unit
- Reusable vs disposable
- Clean / dirty / damaged state where relevant
- Supplier
- Minimum stock level

For reusable serviceware, quantity and condition matter. Current event-rental products distinguish between available stock, items dispatched, returns, damage, cleaning and repair because those states determine whether an item can actually be used again. [Research: Inventory Mobile; GoodEvent; Operations360]

---

# 4. Hidden Job Costs

Zazu should actively help the user discover costs that are easy to forget.

Potential job-cost categories:

### Direct costs
- Ingredients
- Packaging
- Disposable supplies
- External rentals
- Purchased services
- Event labour
- Delivery labour
- Temporary staff

### Logistics
- Fuel
- Parking
- Tolls
- Loading / unloading
- Vehicle use
- Delivery subcontractors
- Extra trips

### Operations
- Equipment hire
- Equipment cleaning
- Breakage / loss
- Waste
- Spoilage
- Replacements
- Emergency purchases

### Administrative effort
- Enquiry handling
- Quotations
- Menu revisions
- Client communication
- Planning
- Procurement
- Supplier communication
- Invoicing
- Payment follow-up
- Document preparation
- Post-job reconciliation

### Fixed / overhead costs
- Rent
- Utilities
- Insurance
- Telephone / data
- Software
- Office costs
- Equipment depreciation or replacement reserve
- Other recurring business expenses

A quotation should not silently assume:

**Selling price - ingredients = profit**

Research repeatedly identifies labour, packaging, transport, rentals, overhead, admin time and waste as material contributors to real catering-job cost.

Research examples:
- HowToBooks ZA: ingredients, packaging, labour, transport, equipment hire, cleaning/utilities, overhead and contingency.
- CaterKit: prep labour, event-day labour, delivery/vehicle, packaging/disposables, rentals, overhead and pre-event admin.
- FoodCostLab: ingredient cost, labour, packaging, delivery, overhead and contingency.

---

# 5. Cost Intelligence

Zazu should distinguish:

**KNOWN COST**
A recorded amount exists.

**ESTIMATED COST**
A planned or estimated amount exists.

**ALLOCATED OVERHEAD**
A share of a fixed business cost has been assigned to the job.

**UNASSIGNED COST**
The user has captured an expense but has not yet linked it to a job/category.

**UNKNOWN / MISSING**
The system knows something may be required but has no reliable amount.

This prevents false precision.

Potential job view:

**Estimated cost:** R...
**Committed cost:** R...
**Actual cost:** R...
**Revenue:** R...
**Current margin:** R...
**Unallocated expenses:** R...
**Known missing costs:** 3

Do not turn estimates into fake accounting truth.

---

# 6. Fixed Costs vs Job Costs

A business may have:

### Fixed recurring costs
- Office/workspace rent
- Kitchen rent
- Storage rent
- Utilities
- Insurance
- Software subscriptions
- Vehicle finance
- Other recurring commitments

### Variable costs
- Ingredients
- Packaging
- Fuel
- Casual labour
- Rentals
- Supplies

### Mixed costs
Costs containing both a recurring base and usage-dependent component.

Zazu should allow fixed costs to exist independently from individual jobs, then optionally allocate a defensible share to jobs for profitability analysis.

The user should be able to switch between:

**JOB PROFIT**
and
**BUSINESS PROFIT**

without confusing the two.

---

# 7. One-Off vs Recurring Work

This is important.

During job setup, the user should choose a work pattern such as:

### One-off
A single event/job.

### Repeating
A repeating service on a schedule.

### Contract / standing service
An ongoing agreement with repeated deliveries/services and periodic billing.

### Template / repeatable job
A reusable operational starting point without automatically creating future jobs.

The system should not force recurring businesses into one-off event logic.

---

# 8. Recurring Catering / Institutional Model

Example:

A school receives meals Monday-Friday.

The business needs:

- Standing agreement
- Customer/site
- Service schedule
- Daily menu
- Daily headcount
- Cut-off time
- Production quantities
- Delivery
- Daily completion
- Exceptions
- Period summary
- Periodic invoice

Current products specifically support recurring meal patterns where customers order or adjust over multiple days and the provider consolidates the period into weekly/monthly invoicing. Catermonkey describes this model for schools, care homes and office meal programmes. Lanemi similarly models standing daily counts, term pauses, service lock times and consolidated monthly invoices.

Potential Zazu concept:

**SERVICE CONTRACT**
↓

**SCHEDULE**
↓

**DAILY OCCURRENCE**
↓

**DAILY REQUIREMENTS**
↓

**DELIVERY / COMPLETION**
↓

**PERIOD CLOSE**
↓

**CONSOLIDATED BILL**

Do not duplicate the same customer, menu, staff, pricing and delivery data for every day.

---

# 9. Church / Club / Weekly Community Catering

Potential pattern:

**Every Sunday**
- Same customer/organisation
- Variable attendance
- Repeating menu or rotating menu
- Repeating staffing
- Repeating delivery/setup
- Periodic invoice or payment

Zazu should support repeating work without making the user recreate the entire job.

Potential interaction:

**Repeat this job**

Then change only:

- Date
- Headcount
- Menu
- Exceptions
- Additional requirements

---

# 10. Requirement Builder

Strong candidate product concept:

## EVENT REQUIREMENTS BUILDER

User starts with:

- Guest count
- Service style
- Menu/package
- Optional requirements
- Existing templates

Zazu produces a draft requirement list.

Example:

**Food**
- Item A — quantity
- Item B — quantity

**Packaging**
- Plates — quantity
- Cups — quantity
- Containers — quantity

**Equipment**
- Tables — quantity
- Serving equipment — quantity

**Staff**
- Role — estimated requirement

**Logistics**
- Vehicle
- Delivery
- Setup

**Other**
- User-defined items

User can modify the generated requirements before confirming.

The system must show assumptions and allow correction.

Never silently guess quantities that materially affect cost or safety.

---

# 11. Requirement Reuse / Job Templates

A completed successful job should be reusable.

Example:

**Wedding Buffet — 150 guests**

Save as template.

Next time:

**Create from template**

Then adjust:

- Guest count
- Menu
- Venue
- Date
- Client
- Special requirements

The reusable template should preserve operational structure without copying historical financial facts incorrectly.

Historical prices and actual costs must remain historical.

---

# 12. Change Impact

A major differentiator opportunity.

When a user changes a major job variable, Zazu should identify affected areas.

Example:

**Guest count**
100 → 150

Potential impact:

- Food requirements
- Packaging requirements
- Staffing
- Equipment
- Delivery
- Estimated cost
- Quote value
- Margin
- Production quantities

The system should surface:

**“This change affects 6 areas.”**

The user remains in control of the change.

---

# 13. Operational Readiness

Instead of only showing numbers, Zazu should identify readiness.

Potential state:

### JOB READINESS

**Client**
✓ Confirmed

**Requirements**
✓ Complete

**Supplies**
⚠ 2 shortages

**Staff**
⚠ 1 role unassigned

**Delivery**
✓ Scheduled

**Documents**
✓ Ready

**Payment**
⚠ Balance outstanding

**Overall**
**82% ready**

This is not a scientific score.

It is an operational checklist summary.

Avoid pretending that an arbitrary percentage represents true business health.

---

# 14. Dynamic / Adaptive UI

Goal:

**Users see what matters to them without losing access to the rest of the system.**

On onboarding, enable likely capabilities.

Example:

Caterer selects:

- Catering
- Delivery
- Event service
- Inventory
- Staff

Zazu prioritises those workflows.

A different business may receive a different starting navigation.

### Important design rule

Do not silently delete or permanently hide functionality based on usage.

Instead use:

**Relevant first**
+
**Available when needed**
+
**Customise**

Possible behaviour:

- Relevant modules appear in primary navigation.
- Less-used modules move to secondary navigation.
- Users can pin/unpin areas.
- “Add to workspace” is always available.
- Zazu may suggest reorganising based on repeated usage.
- Critical controls never disappear unpredictably.

Habit learning should influence **priority and arrangement**, not destroy discoverability.

---

# 15. Adaptive Dashboard

The dashboard should not be a fixed collection of widgets.

Potential context:

### Monday morning
**This week**
- 5 jobs
- 2 payments due
- 1 supplier issue
- 3 requirements incomplete

### Event day
**Today**
- Job
- Timeline
- Delivery
- Staff
- Outstanding requirements

### Quiet period
**Business**
- Outstanding invoices
- Expenses
- Upcoming work
- Low-stock items
- Administrative tasks

The visible dashboard should reflect the current operating context.

---

# 16. “Add Anything” Principle

Zazu should make uncommon requirements easy to capture.

A business should not need to wait for a developer because an unusual item does not exist.

Pattern:

**Search**
→ no result
→ **+ Add new**
→ choose appropriate type
→ save
→ immediately use

This should work throughout the product where safe.

Examples:

- New ingredient
- New packaging type
- New service
- New equipment type
- New supplier
- New expense category
- New event requirement
- New custom field where appropriate

---

# 17. Document / Evidence Inbox

Strong opportunity.

Allow users to capture business evidence from the real world.

Potential inputs:

- Photo
- PDF
- Scanned document
- Receipt
- Fuel receipt
- Supplier invoice
- Screenshot
- Email export
- WhatsApp conversation export
- Event brief
- Menu document
- Quote
- Contract
- Delivery note

Concept:

## ZAZU INBOX

User drops evidence into the inbox.

Zazu identifies what it appears to be and asks the user to confirm the intended record.

Examples:

**Receipt detected**
→ supplier/merchant
→ date
→ amount
→ tax information where present
→ expense category
→ optional job link

**Event conversation**
→ possible client
→ event date
→ venue
→ requirements
→ quoted amount
→ unresolved questions

**Supplier document**
→ supplier
→ items
→ quantities
→ prices
→ document date

The system must distinguish:

**EXTRACTED**
from
**CONFIRMED**

Never silently convert OCR/extraction output into financial truth.

A South African receipt-capture product currently demonstrates a very low-friction WhatsApp-to-expense pattern: photo in, merchant/date/amount extracted, then filed into an expense workflow. This validates the usability pattern, not any particular Zazu implementation.

---

# 18. Evidence → Record Linking

Every captured document should be linkable to:

- Customer
- Job
- Supplier
- Expense
- Purchase
- Payment
- Contract
- Requirement

Potential relationship:

**Document**
→ supports
→ **Business Record**

This is useful when someone later asks:

“Why does this expense exist?”

The source document is immediately available.

---

# 19. Admin Simplification

The product should aim to eliminate repetitive clerical work.

Examples:

**One customer record**
reused across enquiries, quotes, jobs, invoices and documents.

**One job record**
reused across planning, requirements, operations, billing and closeout.

**One supplier**
reused across purchases, receipts and expense history.

**One requirement**
can flow into planning, purchasing and operational preparation.

Principle:

**Enter once → reuse everywhere.**

Research from current catering platforms repeatedly emphasises avoiding re-keying between enquiry, quote, booking, kitchen/operations, billing and reporting.

---

# 20. Expense Capture

Potential expense flow:

**Capture**
→ **Extract**
→ **Review**
→ **Categorise**
→ **Link**
→ **Approve**
→ **Record**

Possible capture sources:

- Camera
- File upload
- Drag/drop
- PDF
- Imported statement/document
- Future connector integrations

The user should be able to say:

**“This belongs to the Johnson Wedding.”**

without navigating through five accounting screens.

---

# 21. Expense Allocation

A single purchase may be:

- Entirely business overhead
- Entirely job-specific
- Partially job-specific
- Shared across multiple jobs

Zazu should support explicit allocation rather than forcing a false binary.

Example:

**Fuel receipt R700**

Allocate:
- Job A: R250
- Job B: R150
- General business: R300

This becomes especially useful for delivery-heavy businesses.

---

# 22. Resource Behaviour Types

Resources may behave differently.

Potential behaviour types:

### Consumable
Used and gone.

Example:
food ingredient, disposable packaging.

### Reusable quantity
Returns to stock but can become unavailable while deployed or dirty.

Example:
plates, glasses, equipment.

### Serialized / individually tracked
Individual asset identity matters.

Example:
high-value equipment.

### Service
No physical stock.

Example:
DJ, photographer, security, cleaning.

### Labour
Time/capacity rather than stock.

### External resource
Purchased or subcontracted for the job.

### Regulated / controlled category
Requires configurable compliance and business rules appropriate to jurisdiction.

Do not force all resource behaviours into one generic stock mutation.

---

# 23. Potential Business Modes

Registration could identify initial operating mode using several dimensions rather than one industry label.

### What do you mainly provide?

- Food
- Services
- Venue/space
- Rentals
- Custom work
- Labour
- Products
- Recurring meals
- Other

### How is it delivered?

- Pickup
- Delivery
- On-site service
- Venue-based
- Remote/digital
- Mixed

### How is the work normally booked?

- One-off
- Appointment
- Event/date
- Recurring schedule
- Contract
- Walk-in
- Mixed

This creates a better basis for configuring the initial product experience.

---

# 24. Architecture Principle

The frontend should not contain separate hard-coded business systems for every category.

Potential architecture direction:

**CORE DOMAIN**
Common business objects and rules.

+

**CAPABILITY CONFIGURATION**
What this business does.

+

**WORKFLOW CONFIGURATION**
How this business normally works.

+

**PRESENTATION CONFIGURATION**
What the user sees first.

The same underlying product can therefore express different workflows.

Do not prematurely build a generic meta-platform.

First prove repeated patterns.

---

# 25. Research Signals

### Catering costing
Current catering costing guidance repeatedly identifies ingredients, packaging, labour, transport, rentals, overhead and administrative time as meaningful cost categories.

Sources:
- https://howtobooksza.com/how-to-start-a-catering-business-in-south-africa-from-your-first-booking-to-a-profitable-operation/
- https://caterkit.app/blog/what-a-2500-catering-job-actually-costs
- https://getfoodcost.com/

### Catering operations
Current catering software commonly connects enquiry, quote, booking, operational preparation, documents and invoicing rather than treating them as unrelated workflows.

Sources:
- https://eventsync.co.za/solutions/catering
- https://www.puree.app/
- https://www.thecatercore.com/
- https://cateringtracker.com/features/

### Recurring meal contracts
Recurring meal providers commonly model standing schedules, daily quantities, cut-off times and consolidated weekly/monthly billing.

Sources:
- https://catermonkey.com/en/features/menus-general/
- https://catermonkey.com/en/features/menus-invoicing/
- https://lanemi.com/use-cases/institutional-catering/

### Equipment / reusable event stock
Current event-rental systems distinguish quantity, reservations, dispatch, return, condition, cleaning, maintenance and availability.

Sources:
- https://www.inventorymobile.com/industries/event-rental-inventory-app
- https://www.goodevent.com/industries/catering-equipment-rental-software
- https://www.operations360.live/equipment-rental-inventory-software/
- https://www.renttix.com/en-gb/articles/catering-equipment-hire-software-guide

### Receipt / evidence capture
Current South African products demonstrate low-friction receipt capture from WhatsApp/photo input into expense records.

Source:
- https://www.snapaslip.co.za/how-it-works

---

# 26. Product Hypotheses to Validate

These are hypotheses, not decisions.

1. A reusable business library will materially reduce repetitive setup.
2. A requirements builder can become a major differentiator if it is transparent and editable.
3. Separating job requirements from actual stock/resources will prevent many conceptual problems.
4. Hidden-cost visibility may be more commercially valuable than additional dashboards.
5. Recurring work deserves a first-class model rather than repeated copied jobs.
6. An evidence inbox could remove substantial admin friction.
7. Adaptive UI should reorganise around relevance and habit while preserving discoverability.
8. One connected job record can become the central operational object for event-oriented businesses.
9. A configurable core may support several business types without turning Zazu into a generic ERP.
10. Good defaults plus an always-available “Add new” path can let the product grow with each business.

---

# 27. Things Not to Assume

Do not assume:

- Every caterer uses recipes.
- Every event has formal contracts.
- Every business needs inventory.
- Every resource needs quantity tracking.
- Every recurring customer invoices monthly.
- Every business allocates overhead to individual jobs.
- Every business wants the same dashboard.
- Every user wants automation.
- Every extracted document is correct.
- Every industry needs the same workflow.
- A feature is valuable merely because competitors have it.

Validate these against real users/workflows.

---

# 28. Current Strategic Questions

Keep these open until evidence resolves them:

### Q1
What is the minimum common domain model that supports several business types without becoming a generic ERP?

### Q2
How much configuration can safely happen at registration without overwhelming a new user?

### Q3
Which requirements can be generated reliably from templates, and which must remain explicit user input?

### Q4
How should Zazu distinguish estimates, commitments and actuals?

### Q5
What is the simplest evidence-ingestion workflow that provides real admin value without creating false financial records?

### Q6
How should adaptive navigation work without making the application unpredictable?

### Q7
Which business modes are genuinely worth supporting first?

---

# 29. Engineering Guardrails

Jarvis must:

- Inspect current source before implementation.
- Treat the current repository as the implementation source of truth.
- Distinguish product idea from confirmed requirement.
- Preserve working behaviour unless evidence requires change.
- Avoid speculative architecture.
- Avoid generic abstractions that have not earned themselves through repetition.
- Trace database, UI and workflow dependencies before changing shared concepts.
- Protect financial, inventory, authorization and audit integrity.
- Verify migration impact before changing foundational entities.
- Prefer reversible changes.
- Record unknowns instead of inventing business rules.
- Keep the user-facing workflow simpler than the underlying system.
- Never expose system complexity merely because the system contains it.

---

# 30. Immediate R&D Direction

Before building new modules:

**Map the current Zazu implementation against:**

**Business setup**
→ **Capabilities**
→ **Offerings**
→ **Job**
→ **Requirements**
→ **Resources**
→ **Operations**
→ **Expenses**
→ **Documents**
→ **Money**
→ **Completion**

For each stage identify:

**Already exists**
**Partially exists**
**Missing**
**Duplicated**
**Architecturally risky**
**Potentially reusable**

The immediate goal is not more features.

The immediate goal is discovering the strongest coherent operating model that the current product can evolve into safely.

---

## Document Status

**Type:** R&D / Product Discovery Notebook  
**Authority:** Founder / Owner decisions + verified product evidence  
**Implementation status:** Ideas only unless separately promoted into the engineering task system  
**Last research update:** September 2026

