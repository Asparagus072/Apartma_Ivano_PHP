document.addEventListener("DOMContentLoaded", function() {
    const disabledDates = window.disabledDates || [];

    // Initialize check-in calendar
    const checkin = flatpickr("#checkin", {
        minDate: "today",
        dateFormat: "Y-m-d",
        disable: disabledDates,
        onChange: function(selectedDates) {
            if (selectedDates.length > 0) {
                // Set minDate for checkout to be the day after check-in
                const checkoutMin = new Date(selectedDates[0].getTime() + 86400000);
                flatpickr("#checkout", {
                    minDate: checkoutMin,
                    dateFormat: "Y-m-d",
                    disable: disabledDates
                });
            }
        }
    });

    // Initialize checkout calendar
    flatpickr("#checkout", {
        minDate: "today",
        dateFormat: "Y-m-d",
        disable: disabledDates
    });
});
