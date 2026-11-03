# AptiMedha --- Agent Instructions

## 1. Project Overview

AptiMedha is an MCQ practice and educational content website focused on
aptitude, reasoning, verbal ability, general knowledge, programming,
engineering, and placement preparation.

Build the application according to the client-provided requirements,
reviewed SRS, topic documents, sample MCQ workbook, and design mockups.
Treat these materials as the source of truth.

## 2. Current Technology Stack

-   Laravel 13
-   PHP 8.5
-   Blade templates
-   MySQL
-   Tailwind CSS 4
-   Vite
-   JavaScript
-   Alpine.js where lightweight interactivity is needed

Do not replace the existing stack or recreate the Laravel project
without explicit approval.

## 3. Client Design Theme

Use the color palette taken from the client-provided homepage and MCQ
practice mockups.

  -----------------------------------------------------------------------
  Token                   Hex                     Intended use
  ----------------------- ----------------------- -----------------------
  Primary Navy            `#153B70`               Brand identity, primary
                                                  navigation, headings,
                                                  primary actions

  Secondary Teal          `#087F82`               Secondary actions,
                                                  highlights, links and
                                                  accents

  Page Background         `#F7F9FC`               Main page background

  Surface White           `#FFFFFF`               Cards, panels, forms
                                                  and content surfaces

  Main Text               `#243247`               Body text and primary
                                                  content
  -----------------------------------------------------------------------

### Theme rules

-   Preserve the client palette; do not invent a replacement brand
    theme.
-   Use the same palette consistently across the public website, MCQ
    pages, authentication screens, user dashboard and admin panel.
-   Maintain readable contrast and clear visual hierarchy.
-   Use neutral borders and subtle shadows; avoid excessive gradients
    and decorative effects.
-   Keep the interface clean, professional, accessible and responsive.
-   Do not treat colors outside this palette as client-approved brand
    colors. Status colors (such as success, warning and error) may be
    used only where functionally necessary.

**Source note:** The palette is identified from the client-provided
homepage and MCQ practice mockups. Do not claim that these exact hex
values are specified in the SRS unless the source document explicitly
confirms them.

## 4. Product and Functional Rules

-   The platform is an MCQ practice and educational content website, not
    automatically a timed examination engine.
-   Support public visitors, authenticated users and administrators.
-   Public visitors may browse available learning content and practice
    MCQs.
-   Login is required for restricted actions such as bookmarking,
    participating in discussions and reporting questions.
-   Enforce permissions on the server. Never rely only on hiding buttons
    in the frontend.
-   Reveal the correct answer and explanation after the user selects an
    option, according to the reviewed handover behavior.
-   Keep question pagination and topic/category hierarchy aligned with
    the client requirements.
-   Support individual MCQ management and bulk Excel/CSV import in the
    admin area.
-   Do not introduce out-of-scope features such as payments, paid
    courses, AI personalization, online code execution or a timed exam
    engine unless the client approves them.

## 5. Architecture and Code Quality

-   Use Laravel conventions and keep controllers focused.
-   Use Form Requests or equivalent validation for incoming data.
-   Use policies/middleware for authorization.
-   Use Eloquent relationships and migrations for database structure.
-   Keep reusable Blade components and layouts.
-   Keep frontend assets managed through Vite.
-   Avoid duplicated logic, oversized controllers and hard-coded
    credentials.
-   Never commit `.env`, API secrets, passwords or private keys.
-   Do not overwrite existing configuration or client content without
    checking first.
-   Do not create fake production records or present dummy data as real
    data.

## 6. Security

-   Validate and sanitize user input.
-   Use Laravel's CSRF protection and password hashing.
-   Protect against SQL injection, XSS and unauthorized access.
-   Ensure users cannot access another user's private data by changing
    URL parameters.
-   Protect all admin routes and actions with server-side authorization.
-   Validate uploaded files, file types and sizes before processing
    them.

## 7. Responsive Design and Accessibility

-   Support desktop, tablet and mobile screen sizes.
-   Ensure navigation, forms, MCQ options, tables and admin screens work
    on small screens.
-   Use semantic HTML, keyboard-accessible controls, visible focus
    states and meaningful image alt text.
-   Do not sacrifice readability or usability for animation.

## 8. Working Method --- Mandatory

Work in small, approved phases.

1.  Inspect the existing code before editing.
2.  Explain the intended changes briefly.
3.  Implement only the current approved phase.
4.  Do not jump ahead to unrelated modules.
5.  Run relevant checks, tests and builds after changes.
6.  Fix errors introduced by the implementation.
7.  Report files changed, commands run, test results and remaining
    issues.
8.  Stop and wait for approval before starting the next phase when
    requested.

If a requirement is unclear or source documents conflict, report the
ambiguity and ask for clarification. Do not silently invent a business
rule.

## 9. Project Setup and Environment

-   Preserve the existing project directory and installed dependencies.
-   Do not assume MySQL is installed, running or configured until
    verified.
-   Do not invent database credentials.
-   Do not change `.env` values without approval.
-   Keep local environment-specific settings out of committed source
    files.

## 10. Definition of Done

A task is complete only when:

-   The requested behavior is implemented.
-   Existing functionality has not been unnecessarily broken.
-   Validation and authorization are in place where required.
-   The relevant tests or build checks have been run.
-   No known implementation error is hidden.
-   A concise completion report lists changes, verification results and
    outstanding items.
