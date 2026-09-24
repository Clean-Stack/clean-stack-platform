# Clean Stack Platform

A modern web platform for the Clean Stack brand, built with Laravel, Livewire, Flux, and Tailwind CSS.

## Overview

Clean Stack Platform is a full-stack web platform developed for **Clean Stack**, an independent web development brand focused on building clean, modern web experiences.

The platform combines a public-facing website with content management, services, projects, blog publishing, lead management, and an authenticated administration dashboard. It is designed as a real-world portfolio project with an emphasis on maintainable code, clear architecture, practical engineering, and a polished user experience.

This is the first flagship project developed under the **Clean Stack** brand.

## Core Features

* **Public Website**

    * Home, About, Services, Projects, Blog, Contact, and Let's Talk pages
    * Responsive and accessible user interface
    * SEO-focused metadata and structured content

* **Blog**

    * Published-post listing
    * Search and category filtering
    * Featured images
    * SEO metadata, canonical URLs, Open Graph metadata, and structured data

* **Content Management**

    * Manage site pages and their content
    * Manage post categories

* **Services Management**

    * Create, edit, and manage services
    * Control service visibility and presentation on the public website

* **Projects Management**

    * Create, edit, and manage portfolio projects
    * Project images and media management
    * Control project visibility on the public website

* **Lead Management**

    * Capture inquiries through the Let's Talk form
    * Manage leads through the administration dashboard
    * Track lead status through the sales workflow

* **Administration Dashboard**

    * Authenticated administrative area
    * Protected routes and role-based access control
    * Search, filtering, pagination, and load-more interfaces where applicable

## Technology Stack

### Backend

* **PHP 8.5** — Server-side application development
* **Laravel 13** — Application framework, routing, validation, authentication, database access, and application infrastructure
* **Eloquent ORM** — Database modeling and data access

### Frontend

* **Livewire 4** — Server-driven, reactive interfaces and application interactions
* **Blade** — Server-side rendering and view composition
* **Tailwind CSS 4** — Utility-first styling and responsive UI development
* **Alpine.js** — Lightweight client-side interactions where needed

### UI

* **Flux UI / Flux Pro** — UI components and application interface primitives

### Database

* **MySQL** — Relational database management

### Build & Development

* **Vite** — Frontend asset bundling and development workflow
* **Composer** — PHP dependency management
* **npm** — Frontend dependency management

## Architecture & Engineering Highlights

* **Laravel Application Architecture**

    * Built on Laravel's MVC architecture with Eloquent ORM, Blade, routing, validation, authentication, and middleware
    * Clear separation between application logic, presentation, and data access

* **Livewire Single-File Components**

    * Built with Livewire 4 Single-File Components for interactive application interfaces
    * Server-driven interactions without requiring a separate frontend application

* **Reusable UI System**

    * Flux UI and Flux Pro components are used throughout the authenticated dashboard and application interfaces
    * Tailwind CSS provides a consistent utility-based styling system across the application

* **Authentication & Authorization**

    * Protected administrative routes with authenticated access
    * Role-based authorization for administrative functionality
    * Unauthorized users are prevented from accessing protected application areas

* **Content & Publishing Workflows**

    * Structured management of pages, posts, categories, services, projects, and leads
    * Controlled publishing and public visibility for managed content
    * Validation and business rules applied at the application layer

* **Media Management**

    * Dedicated handling of uploaded project and post images
    * Image lifecycle management across relevant content operations

* **SEO & Structured Content**

    * Dynamic SEO metadata for published blog content
    * Canonical URLs
    * Open Graph metadata
    * Twitter Card metadata
    * Article structured data

* **Testing & Quality Assurance**

    * Automated application tests covering core functionality, authorization, validation, database behavior, and critical user workflows
    * Final release verification includes automated tests, asset compilation, route and authorization checks, and manual QA

## Installation

### Requirements

* PHP 8.5+
* Composer
* Node.js and npm
* MySQL
* A local PHP development environment
* A valid **Flux Pro** license for installing and using the project's Pro components

### Setup

Clone the repository and install the project dependencies:

```bash
git clone https://github.com/clean-stack/clean-stack-platform.git
cd clean-stack-platform

composer install
npm install
```

Flux Pro is a private Composer dependency. A valid Flux Pro license and Composer authentication credentials are required to install the Pro package.

Configure the Flux Pro Composer credentials according to the official Flux installation documentation before running `composer install`.

Create the environment file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Configure the database connection in `.env`, then run the migrations and seed the database:

```bash
php artisan migrate --seed
```

Build the frontend assets:

```bash
npm run build
```

Start the local development environment:

```bash
composer run dev
```

### Flux Pro Components

The application uses the following Flux Pro components:

* **Rich Text Editor** — for blog post content editing
* **File Upload** — for post and project image uploads
* **Searchable Select** — for searchable selection interfaces in the administration dashboard

### Testing

Run the automated test suite with:

```bash
php artisan test
```

Build the frontend assets independently with:

```bash
npm run build
```

## Project Status

This project is actively maintained as the flagship portfolio project of the **Clean Stack** brand.

## Author

**Majid Mohammadpanah**
Founder of **Clean Stack**

* GitHub: [@majidmohammadpanah](https://github.com/majidmohammadpanah)
* Telegram: [@majidmohammadpanah](https://t.me/majidmohammadpanah)
* Email: [majidmohammadpanah@gmail.com](mailto:majidmohammadpanah@gmail.com)

## License

This project is proprietary and is not distributed under an open-source license.

The project uses **Flux Pro**, which is distributed under a separate commercial license. A valid Flux Pro license is required to install and use its Pro components.

If you have a valid Flux Pro license, you may use this project in accordance with the applicable Flux Pro license terms. The project itself is not licensed as open source.

For licensing details, please refer to the [Flux Terms of Service and License Agreement](https://fluxui.dev/terms).
