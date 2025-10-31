// Admin page functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Handle booking status updates
    document.querySelectorAll('.update-status').forEach(button => {
        button.addEventListener('click', function(e) {
            const bookingId = this.dataset.bookingId;
            const newStatus = this.dataset.status;
            updateBookingStatus(bookingId, newStatus);
        });
    });

    // Handle booking deletion
    document.querySelectorAll('.delete-booking').forEach(button => {
        button.addEventListener('click', function(e) {
            if (confirm('Are you sure you want to delete this booking?')) {
                const bookingId = this.dataset.bookingId;
                deleteBooking(bookingId);
            }
        });
    });
});

function updateBookingStatus(bookingId, status) {
    // Send AJAX request to update booking status
    fetch('?page=admin&action=update-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `booking_id=${bookingId}&status=${status}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI to reflect new status
            const statusElement = document.querySelector(`#booking-${bookingId} .booking-status`);
            if (statusElement) {
                statusElement.textContent = status;
                statusElement.className = `booking-status status-${status.toLowerCase()}`;
            }
            // Show success message
            showAlert('success', 'Booking status updated successfully');
        } else {
            showAlert('error', 'Failed to update booking status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while updating the booking');
    });
}

function deleteBooking(bookingId) {
    fetch('?page=admin&action=delete-booking', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `booking_id=${bookingId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the booking element from the UI
            const bookingElement = document.querySelector(`#booking-${bookingId}`);
            if (bookingElement) {
                bookingElement.remove();
            }
            showAlert('success', 'Booking deleted successfully');
        } else {
            showAlert('error', 'Failed to delete booking');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while deleting the booking');
    });
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    const container = document.querySelector('.admin-dashboard');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}