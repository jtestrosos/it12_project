@extends('layouts.app')

@section('title', 'UI Style Guide')

@section('content')
<div class="container py-5">
    <div class="mb-5">
        <h1 class="fw-bold">UI Style Guide</h1>
        <p class="text-muted">A collection of reusable components and design elements.</p>
    </div>

    <!-- Buttons -->
    <section class="mb-5">
        <h3 class="mb-4 pb-2 border-bottom">Buttons</h3>
        
        <div class="card card-surface mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Variants</h5>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <x-button variant="primary">Primary</x-button>
                    <x-button variant="secondary">Secondary</x-button>
                    <x-button variant="success">Success</x-button>
                    <x-button variant="danger">Danger</x-button>
                    <x-button variant="warning">Warning</x-button>
                    <x-button variant="info">Info</x-button>
                    <x-button variant="light">Light</x-button>
                    <x-button variant="dark">Dark</x-button>
                    <x-button variant="link">Link</x-button>
                    <x-button variant="ghost">Ghost</x-button>
                </div>

                <h5 class="card-title mb-3">Sizes</h5>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
                    <x-button size="sm">Small</x-button>
                    <x-button size="md">Medium</x-button>
                    <x-button size="lg">Large</x-button>
                </div>

                <h5 class="card-title mb-3">States</h5>
                <div class="d-flex flex-wrap gap-2">
                    <x-button disabled>Disabled</x-button>
                    <x-button loading>Loading</x-button>
                    <x-button icon="fas fa-save">With Icon</x-button>
                    <x-button icon="fas fa-arrow-right" iconPosition="right">Icon Right</x-button>
                </div>
            </div>
        </div>
    </section>

    <!-- Cards -->
    <section class="mb-5">
        <h3 class="mb-4 pb-2 border-bottom">Cards</h3>
        
        <div class="row g-4">
            <div class="col-md-4">
                <x-card title="Card Title" subtitle="Card Subtitle">
                    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                    <x-button size="sm">Go somewhere</x-button>
                </x-card>
            </div>
            <div class="col-md-4">
                <x-card title="Card with Actions" headerActions='<button class="btn btn-sm btn-ghost"><i class="fas fa-ellipsis-v"></i></button>'>
                    <p class="card-text">This card has an action button in the header.</p>
                </x-card>
            </div>
            <div class="col-md-4">
                <x-card title="Card with Footer" footer='<small class="text-muted">Last updated 3 mins ago</small>'>
                    <p class="card-text">This card has a footer slot.</p>
                </x-card>
            </div>
        </div>
    </section>

    <!-- Inputs -->
    <section class="mb-5">
        <h3 class="mb-4 pb-2 border-bottom">Inputs</h3>
        
        <div class="card card-surface">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <x-input name="example1" label="Text Input" placeholder="Enter text..." />
                        <x-input name="example2" label="Required Input" required />
                        <x-input name="example3" label="Input with Icon" icon="fas fa-user" />
                    </div>
                    <div class="col-md-6">
                        <x-input name="example4" label="Password Input" type="password" />
                        <x-input name="example5" label="Disabled Input" value="Cannot change me" disabled />
                        <x-input name="example6" label="Input with Helper" helper="We'll never share your email with anyone else." />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Empty State -->
    <section class="mb-5">
        <h3 class="mb-4 pb-2 border-bottom">Empty State</h3>
        
        <div class="card card-surface">
            <div class="card-body">
                <x-empty-state 
                    title="No Projects Found" 
                    description="Get started by creating a new project." 
                    icon="fa-rocket"
                >
                    <x-slot:action>
                        <x-button variant="primary" icon="fas fa-plus">Create Project</x-button>
                    </x-slot:action>
                </x-empty-state>
            </div>
        </div>
    </section>

    <!-- Modals -->
    <section class="mb-5">
        <h3 class="mb-4 pb-2 border-bottom">Modals</h3>
        
        <div class="card card-surface">
            <div class="card-body">
                <x-button data-bs-toggle="modal" data-bs-target="#exampleModal">Launch Demo Modal</x-button>
            </div>
        </div>
    </section>
</div>

<!-- Demo Modal -->
<x-modal id="exampleModal" title="Modal Title">
    <p>Woohoo, you're reading this text in a modal!</p>
    <x-slot:footer>
        <x-button variant="secondary" data-bs-dismiss="modal">Close</x-button>
        <x-button variant="primary">Save changes</x-button>
    </x-slot:footer>
</x-modal>
@endsection
