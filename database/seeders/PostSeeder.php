<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()
            ->where('email', 'info@cleanstack.ir')
            ->firstOrFail();

        $categories = Category::query()
            ->whereIn('slug', [
                'web-development',
                'laravel',
                'livewire',
                'alpine-js',
                'clean-code',
                'performance',
            ])
            ->pluck('id', 'slug');

        $posts = [
            [
                'category' => 'laravel',
                'title' => 'Why Laravel Is a Practical Choice for Business Web Applications',
                'slug' => 'why-laravel-is-a-practical-choice-for-business-web-applications',
                'excerpt' => 'A look at why Laravel works well for building maintainable, secure, and business-focused web applications.',
                'content' => <<<'HTML'
<p>Choosing a framework for a business application is not only about how quickly the first page can be built. The more important question is whether the application can remain understandable and maintainable as its requirements grow.</p>

<p>Laravel provides a structured foundation for building modern PHP applications. Routing, validation, authentication, database access, queues, caching, mail, and application configuration are all part of a consistent ecosystem.</p>

<h2>Structure matters as the application grows</h2>

<p>A business application usually starts with a small number of requirements. Over time, it may gain administration tools, user accounts, forms, reports, integrations, and more complex workflows. Laravel gives developers conventions that help keep these parts organized.</p>

<p>The goal should not be to use every Laravel feature simply because it exists. A good Laravel application uses the framework where it provides real value and keeps the rest of the code as simple as possible.</p>

<h2>A practical foundation</h2>

<p>For business-focused projects, the combination of Laravel, Eloquent, Blade, Livewire, and a well-structured database can provide everything needed for a reliable modern application without unnecessary frontend complexity.</p>
HTML,
                'published_at' => '2026-09-08 10:00:00',
            ],

            [
                'category' => 'laravel',
                'title' => 'Building Maintainable Laravel Applications',
                'slug' => 'building-maintainable-laravel-applications',
                'excerpt' => 'Maintainability starts with clear responsibilities, predictable structure, and avoiding complexity that the project does not need.',
                'content' => <<<'HTML'
<p>Maintainability is one of the most important qualities of a business application. A project that works today but becomes difficult to understand after six months can quickly become expensive to change.</p>

<h2>Keep responsibilities clear</h2>

<p>Routes should describe application entry points. Models should represent data and relationships. Components should manage their own interface behavior. Services can be introduced when a piece of business logic genuinely needs its own boundary.</p>

<p>This does not mean every piece of code needs its own class. Over-abstraction can make a small application harder to understand instead of making it cleaner.</p>

<h2>Prefer predictable code</h2>

<p>Consistent naming, sensible file locations, small methods, meaningful validation rules, and straightforward database relationships make a project easier to work on.</p>

<p>Clean architecture is not about creating the largest architecture possible. It is about creating enough structure to keep the application understandable as it grows.</p>
HTML,
                'published_at' => '2026-09-09 10:00:00',
            ],

            [
                'category' => 'laravel',
                'title' => 'Structuring Business Logic in Laravel',
                'slug' => 'structuring-business-logic-in-laravel',
                'excerpt' => 'A practical approach to deciding where application and business logic should live in a Laravel project.',
                'content' => <<<'HTML'
<p>One of the recurring decisions in Laravel development is where a piece of application logic should live. There is rarely a single answer that works for every project.</p>

<h2>Start with the simplest suitable place</h2>

<p>Simple operations can often remain inside a Livewire component or controller. Validation belongs close to the operation that needs it, while relationships and data behavior can naturally live on Eloquent models.</p>

<p>When a workflow becomes larger or needs to be reused, extracting it into a dedicated service can make the responsibility clearer.</p>

<h2>Avoid premature architecture</h2>

<p>Creating services, repositories, managers, and interfaces for every operation can introduce more concepts than the application actually needs.</p>

<p>A practical Laravel application should evolve its architecture as its complexity grows. The objective is clarity, not abstraction for its own sake.</p>
HTML,
                'published_at' => '2026-09-10 10:00:00',
            ],

            [
                'category' => 'livewire',
                'title' => 'When Livewire Makes Sense for Modern Interfaces',
                'slug' => 'when-livewire-makes-sense-for-modern-interfaces',
                'excerpt' => 'Livewire can provide rich application interfaces while keeping much of the application logic close to Laravel.',
                'content' => <<<'HTML'
<p>Modern websites often need more than static pages. Forms, dashboards, filters, search, pagination, validation, and interactive workflows can quickly introduce frontend complexity.</p>

<p>Livewire provides another approach. Instead of moving every interaction into a separate JavaScript application, developers can keep much of the interface behavior connected to the Laravel application.</p>

<h2>Server-driven interaction</h2>

<p>Livewire is particularly useful for business applications where forms and data-driven interfaces are important. A component can validate input, query Eloquent models, update state, and render the result without requiring a large client-side framework.</p>

<h2>Use the right tool for the job</h2>

<p>Livewire does not replace JavaScript. Some interactions are naturally client-side, and Alpine.js can complement Livewire for those cases.</p>

<p>The useful part is having the freedom to keep frontend complexity proportional to the actual needs of the application.</p>
HTML,
                'published_at' => '2026-09-11 10:00:00',
            ],

            [
                'category' => 'livewire',
                'title' => 'Building Interactive Forms with Livewire',
                'slug' => 'building-interactive-forms-with-livewire',
                'excerpt' => 'Livewire makes it possible to build validated, interactive Laravel forms while keeping application behavior close to the server.',
                'content' => <<<'HTML'
<p>Forms are one of the most common interactive parts of a business website. Contact forms, lead forms, administration forms, and content management interfaces all need validation and reliable state handling.</p>

<h2>Validation stays close to the component</h2>

<p>With Livewire, form state can be represented directly by component properties and validated when an action is submitted. This keeps the validation rules and the operation that uses them easy to find.</p>

<p>The server remains responsible for important validation and persistence. Client-side behavior can then be used to improve the experience without treating the browser as the source of truth.</p>

<h2>Keep the user experience simple</h2>

<p>Loading states, validation messages, disabled buttons, and success feedback can make a form feel responsive without requiring a large JavaScript layer.</p>

<p>The result is an interface that remains interactive while keeping the underlying application architecture relatively simple.</p>
HTML,
                'published_at' => '2026-09-12 10:00:00',
            ],

            [
                'category' => 'livewire',
                'title' => 'Flux and Livewire: Building Consistent Laravel Interfaces',
                'slug' => 'flux-and-livewire-building-consistent-laravel-interfaces',
                'excerpt' => 'Combining Livewire with Flux can provide reusable interface patterns for dashboards and business applications.',
                'content' => <<<'HTML'
<p>A good business application needs more than functional backend code. The interface also needs consistent forms, navigation, dialogs, tables, notifications, and responsive behavior.</p>

<p>Livewire handles much of the application interaction, while Flux provides a collection of interface components designed to work naturally within the Laravel and Livewire ecosystem.</p>

<h2>Consistency reduces interface complexity</h2>

<p>Reusable components help keep the visual language of an application consistent. Instead of creating every button, form control, modal, and navigation element from scratch, developers can build the application around established interface patterns.</p>

<h2>Design and architecture together</h2>

<p>The benefit is not simply visual. Consistent components can also make templates easier to understand and maintain.</p>

<p>For a business platform, this combination provides a practical way to build rich administration interfaces without introducing an entirely separate frontend application.</p>
HTML,
                'published_at' => '2026-09-13 10:00:00',
            ],

            [
                'category' => 'livewire',
                'title' => 'Using Flux Pro to Build Better Laravel Admin Interfaces',
                'slug' => 'using-flux-pro-to-build-better-laravel-admin-interfaces',
                'excerpt' => 'A consistent component system can make complex Laravel administration interfaces easier to build and maintain.',
                'content' => <<<'HTML'
<p>Administration panels contain many interface patterns: navigation, forms, filters, tables, dialogs, confirmation actions, notifications, and responsive layouts.</p>

<p>Flux Pro provides a set of reusable components that can help Laravel and Livewire applications maintain consistency across these interfaces.</p>

<h2>Components instead of one-off markup</h2>

<p>Using established components reduces the amount of custom interface code that has to be maintained. It also makes repeated patterns easier to recognize when moving between different sections of an application.</p>

<h2>The important part is still the application</h2>

<p>A component library should not dictate unnecessary architecture. The goal is to use the interface system to support the application's workflows while keeping the underlying Laravel and Livewire code clear.</p>

<p>For business dashboards, that balance can make a significant difference in development speed and long-term maintainability.</p>
HTML,
                'published_at' => '2026-09-19 10:00:00',
            ],

            [
                'category' => 'alpine-js',
                'title' => 'Using Alpine.js for Lightweight Frontend Interactions',
                'slug' => 'using-alpine-js-for-lightweight-frontend-interactions',
                'excerpt' => 'Alpine.js is useful for small client-side interactions that do not require a full JavaScript application.',
                'content' => <<<'HTML'
<p>Not every frontend interaction needs a large JavaScript framework. Many interfaces only need small amounts of client-side behavior such as opening a menu, toggling a panel, controlling a modal, or managing temporary UI state.</p>

<h2>Small behavior, small tool</h2>

<p>Alpine.js provides a lightweight way to add this type of behavior directly to HTML. Its declarative approach makes simple interactions easy to keep close to the markup they control.</p>

<h2>Alpine.js and Livewire</h2>

<p>Alpine.js can work particularly well alongside Livewire. Livewire can handle server-side state, validation, database operations, and application logic, while Alpine.js can handle small browser-side interactions.</p>

<p>This separation can keep the frontend simple. Instead of introducing a large client-side architecture for a few interactions, the project can use the smallest tool that solves the actual problem.</p>
HTML,
                'published_at' => '2026-09-14 10:00:00',
            ],

            [
                'category' => 'web-development',
                'title' => 'What Makes a Business Website Useful',
                'slug' => 'what-makes-a-business-website-useful',
                'excerpt' => 'A useful business website should communicate clearly, support real users, and create a straightforward path toward action.',
                'content' => <<<'HTML'
<p>A business website should do more than look modern. Its primary job is to help visitors understand the business, its services, and what they can do next.</p>

<h2>Clarity comes first</h2>

<p>Visitors should quickly understand what the business does and who it serves. Clear headings, useful content, straightforward navigation, and focused calls to action are often more valuable than decorative complexity.</p>

<h2>Technology supports the business</h2>

<p>The technology behind the website should serve these goals. Responsive design, performance, accessibility, security, and maintainable code all contribute to a better product.</p>

<p>A good website is therefore a combination of communication, design, engineering, and business thinking.</p>
HTML,
                'published_at' => '2026-09-15 10:00:00',
            ],

            [
                'category' => 'web-development',
                'title' => 'From Business Requirement to Web Product',
                'slug' => 'from-business-requirement-to-web-product',
                'excerpt' => 'Turning a business requirement into a useful web product starts with understanding the problem before choosing the technology.',
                'content' => <<<'HTML'
<p>Many web projects begin with a list of requested features. A stronger starting point is understanding the business problem those features are supposed to solve.</p>

<h2>Understand the workflow</h2>

<p>Before implementation, it helps to identify who will use the system, what information they need, which actions they perform, and where the current process creates friction.</p>

<h2>Build only what provides value</h2>

<p>Once the workflow is clear, the application can be designed around the necessary functionality. This usually produces a simpler and more focused product than starting with a long list of technical features.</p>

<p>Technology choices should support the resulting product rather than become the product themselves.</p>
HTML,
                'published_at' => '2026-09-16 10:00:00',
            ],

            [
                'category' => 'clean-code',
                'title' => 'What Clean Code Means in a Real Web Project',
                'slug' => 'what-clean-code-means-in-a-real-web-project',
                'excerpt' => 'Clean code is not about perfect code; it is about making software easier to understand, change, and maintain.',
                'content' => <<<'HTML'
<p>The phrase clean code can mean different things to different developers. In a real project, its most practical meaning is code that another developer can understand and safely change.</p>

<h2>Names matter</h2>

<p>Meaningful names reduce the amount of context a developer needs to keep in their head. A method should communicate what it does, and a variable should communicate what it represents.</p>

<h2>Small and focused responsibilities</h2>

<p>When a component or method starts doing too many unrelated things, understanding and testing it becomes harder. Separating responsibilities where there is a real boundary can improve clarity.</p>

<p>Clean code is ultimately about reducing unnecessary mental overhead. The best solution is often the one that communicates its intent without requiring a long explanation.</p>
HTML,
                'published_at' => '2026-09-17 10:00:00',
            ],

            [
                'category' => 'performance',
                'title' => 'Practical Website Performance Improvements',
                'slug' => 'practical-website-performance-improvements',
                'excerpt' => 'Website performance is often improved by addressing a small number of practical bottlenecks instead of adding unnecessary complexity.',
                'content' => <<<'HTML'
<p>Performance work is most useful when it starts with measurement and a clear understanding of the actual bottleneck.</p>

<h2>Start with the basics</h2>

<p>Large images, unnecessary JavaScript, inefficient queries, excessive requests, and poorly optimized assets can all affect the experience users have when loading a website.</p>

<h2>Performance should remain maintainable</h2>

<p>A performance improvement that makes the application significantly harder to maintain is not always a good trade-off. The goal should be to remove real bottlenecks while keeping the code understandable.</p>

<p>Efficient images, sensible asset delivery, optimized database queries, caching where appropriate, and a focused frontend can provide meaningful improvements without turning performance into an unnecessary engineering project.</p>
HTML,
                'published_at' => '2026-09-18 10:00:00',
            ],

            [
                'category' => 'laravel',
                'title' => 'Exploring AI Features in Laravel Applications',
                'slug' => 'exploring-ai-features-in-laravel-applications',
                'excerpt' => 'AI can become a useful part of a Laravel application when it is connected to a real workflow instead of being added simply for novelty.',
                'content' => <<<'HTML'
<p>Artificial intelligence is becoming part of many software products, but adding an AI feature should start with a real problem rather than the technology itself.</p>

<h2>AI inside a Laravel application</h2>

<p>Laravel can provide the surrounding application architecture while an AI service handles tasks such as text generation, summarization, classification, extraction, or assistance with business content.</p>

<p>The Laravel application remains responsible for authentication, authorization, validation, database persistence, business rules, and the user experience around the AI feature.</p>

<h2>Useful AI features are workflow features</h2>

<p>For example, an administration panel could use AI to help draft content, summarize customer messages, categorize incoming information, or assist with repetitive internal tasks.</p>

<p>The important question is not whether a project can use AI. The better question is whether AI solves a meaningful problem for the people using the application.</p>
HTML,
                'published_at' => '2026-09-20 10:00:00',
            ],

            [
                'category' => 'laravel',
                'title' => 'Building AI-Assisted Workflows with Laravel',
                'slug' => 'building-ai-assisted-workflows-with-laravel',
                'excerpt' => 'Laravel provides a strong foundation for connecting AI capabilities to authenticated, database-driven business workflows.',
                'content' => <<<'HTML'
<p>AI becomes more useful when it is connected to the existing data and workflows of an application. A standalone chatbot can be interesting, but a well-designed AI feature can become part of a much larger business process.</p>

<h2>Laravel as the application layer</h2>

<p>Laravel can handle the parts of the system that require predictable application behavior: authentication, permissions, validation, database access, queues, logging, and storing the results of AI operations.</p>

<p>This separation is important because AI output is not always deterministic. The application should still enforce the rules that protect business data and user permissions.</p>

<h2>Human control still matters</h2>

<p>AI-assisted workflows should generally make it easy for users to review, edit, approve, or reject generated results when accuracy matters.</p>

<p>The strongest AI features are often not fully autonomous. They are tools that reduce repetitive work while keeping important decisions in the hands of the people using the application.</p>
HTML,
                'published_at' => '2026-09-21 10:00:00',
            ],
        ];

        foreach ($posts as $post) {
            Post::query()->updateOrCreate(
                ['slug' => $post['slug']],
                [
                    'user_id' => $user->id,
                    'category_id' => $categories[$post['category']],
                    'title' => $post['title'],
                    'excerpt' => $post['excerpt'],
                    'content' => $post['content'],
                    'featured_image' => null,
                    'status' => 'published',
                    'published_at' => $post['published_at'],
                ],
            );
        }
    }
}

