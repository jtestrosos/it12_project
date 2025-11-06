import { driver } from 'driver.js';
import 'driver.js/dist/driver.css';

// Make driver available globally
window.driver = driver;

// Tour configurations for different pages and roles
export const tours = {
    // Public - Home/Landing Tour
    home: () => {
        return driver({
            showProgress: true,
            showButtons: ['next', 'previous', 'close'],
            steps: [
                {
                    element: 'h1.fw-bold',
                    popover: {
                        title: 'Welcome to our clinic website',
                        description: 'Explore services and easily book your appointment online.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#btnViewServices',
                    popover: {
                        title: 'View Services',
                        description: 'Browse the healthcare services available to you.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#navPolicy',
                    popover: {
                        title: 'Booking Policy',
                        description: 'Read our guidelines before scheduling an appointment.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#navContact',
                    popover: {
                        title: 'Contact Us',
                        description: 'Need help? Reach us through the contact page.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#btnBookNow',
                    popover: {
                        title: 'Book Appointment Now',
                        description: 'Start here to schedule your appointment.',
                        side: 'bottom',
                        align: 'start'
                    }
                }
            ]
        });
    },
    // Patient - Appointment Booking Tour
    patientBooking: () => {
        return driver({
            showProgress: true,
            showButtons: ['next', 'previous', 'close'],
            steps: [
                {
                    element: '#patient_name',
                    popover: {
                        title: 'Patient Information',
                        description: 'Your name is automatically filled from your account. You can modify it if needed.',
                        side: 'top',
                        align: 'start'
                    }
                },
                {
                    element: '#appointment_date',
                    popover: {
                        title: 'Select Date',
                        description: 'Choose your preferred appointment date. Available dates start from tomorrow.',
                        side: 'top',
                        align: 'start'
                    }
                },
                {
                    element: '#appointment_time',
                    popover: {
                        title: 'Select Time',
                        description: 'Choose your preferred time slot. Available times are from 8:00 AM to 4:00 PM.',
                        side: 'top',
                        align: 'start'
                    }
                },
                {
                    element: '#service_type',
                    popover: {
                        title: 'Service Type',
                        description: 'Select the type of service you need (General Checkup, Prenatal, Immunization, etc.).',
                        side: 'top',
                        align: 'start'
                    }
                },
                {
                    element: '#medical_history',
                    popover: {
                        title: 'Medical History (Optional)',
                        description: 'You can provide any relevant medical history that might be helpful for your appointment.',
                        side: 'top',
                        align: 'start'
                    }
                },
                {
                    element: 'button[type="submit"]',
                    popover: {
                        title: 'Submit Appointment',
                        description: 'Click here to submit your appointment request. You\'ll receive a confirmation once it\'s reviewed.',
                        side: 'top',
                        align: 'end'
                    }
                }
            ]
        });
    },

    // Admin - Dashboard Tour
    adminDashboard: () => {
        return driver({
            showProgress: true,
            showButtons: ['next', 'previous', 'close'],
            steps: [
                {
                    element: '.metric-card',
                    popover: {
                        title: 'Dashboard Overview',
                        description: 'Here you can see key metrics at a glance, including total patients, today\'s appointments, and low stock items.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '.sidebar, nav.sidebar',
                    popover: {
                        title: 'Navigation Menu',
                        description: 'Use the sidebar to navigate between different sections: Dashboard, Patients, Appointments, Inventory, and Reports.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#helpButton',
                    popover: {
                        title: 'Help Button',
                        description: 'Click this button anytime to get a guided tour of the current page.',
                        side: 'bottom',
                        align: 'end'
                    }
                },
                {
                    element: '#themeToggle',
                    popover: {
                        title: 'Theme Toggle',
                        description: 'Switch between light and dark mode for a comfortable viewing experience.',
                        side: 'bottom',
                        align: 'end'
                    }
                }
            ]
        });
    },

    // Admin - Appointments Tour
    adminAppointments: () => {
        return driver({
            showProgress: true,
            showButtons: ['next', 'previous', 'close'],
            steps: [
                {
                    element: '.filter-card',
                    popover: {
                        title: 'Filter Appointments',
                        description: 'Use these filters to search and filter appointments by status, date range, service type, or patient name.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '.nav-tabs',
                    popover: {
                        title: 'Status Tabs',
                        description: 'Quickly filter appointments by status: All, Pending, Confirmed, Completed, or Cancelled.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '.table-responsive, .table',
                    popover: {
                        title: 'Appointments List',
                        description: 'View all appointments here. You can approve, reschedule, or cancel appointments using the action buttons in each row.',
                        side: 'top',
                        align: 'start'
                    }
                }
            ]
        });
    },

    // Admin - Inventory Tour
    adminInventory: () => {
        return driver({
            showProgress: true,
            showButtons: ['next', 'previous', 'close'],
            steps: [
                {
                    element: 'button[data-bs-target="#addItemModal"]',
                    popover: {
                        title: 'Add Inventory Item',
                        description: 'Click here to add new inventory items to the system. Make sure to fill in all required fields including minimum stock levels.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '.table-responsive, .table',
                    popover: {
                        title: 'Inventory List',
                        description: 'View all inventory items here. Items with low stock are highlighted. You can edit items or track transactions by clicking the buttons in each row.',
                        side: 'top',
                        align: 'start'
                    }
                },
                {
                    element: '.table tbody tr:first-child',
                    popover: {
                        title: 'Stock Status Indicators',
                        description: 'Monitor stock status with colored badges in each row: In Stock (green), Low Stock (yellow), Out of Stock (red), or Expired.',
                        side: 'left',
                        align: 'start'
                    }
                }
            ]
        });
    },

    // Patient - Dashboard Tour
    patientDashboard: () => {
        return driver({
            showProgress: true,
            showButtons: ['next', 'previous', 'close'],
            steps: [
                {
                    element: '.metric-card:first-child, .dashboard-card',
                    popover: {
                        title: 'Welcome to Your Dashboard',
                        description: 'Here you can see an overview of your appointments and access key features.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: 'a[href*="book-appointment"], .btn-primary',
                    popover: {
                        title: 'Book Appointment',
                        description: 'Click here to schedule a new appointment with the clinic.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '.table, .appointment-list, .dashboard-card',
                    popover: {
                        title: 'Your Appointments',
                        description: 'View all your appointments here. You can see their status (pending, approved, completed) and details.',
                        side: 'top',
                        align: 'start'
                    }
                }
            ]
        });
    }
};

// Helper function to start a tour
export function startTour(tourName) {
    const tour = tours[tourName];
    if (tour) {
        const driverObj = tour();
        driverObj.drive();
    } else {
        console.warn(`Tour "${tourName}" not found`);
    }
}

// Auto-start tour on first visit (using localStorage)
export function checkAndStartTour(tourName, storageKey) {
    const hasSeenTour = localStorage.getItem(storageKey);
    if (!hasSeenTour) {
        startTour(tourName);
        localStorage.setItem(storageKey, 'true');
    }
}

// Function to reset tour (for testing or user preference)
export function resetTour(storageKey) {
    localStorage.removeItem(storageKey);
}

// Make functions available globally
window.startTour = startTour;
window.checkAndStartTour = checkAndStartTour;
window.resetTour = resetTour;

