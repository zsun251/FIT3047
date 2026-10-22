document.addEventListener('DOMContentLoaded', function() {
    // Initialize Flatpickr on all time picker inputs
    const timeInputs = document.querySelectorAll('.time-picker');

    timeInputs.forEach(function(input) {
        flatpickr(input, {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            minuteIncrement: 1,
            allowInput: false,
            clickOpens: true,
            onChange: function(selectedDates, dateStr, instance) {
                console.log('Time selected:', dateStr);
            },
            onClose: function(selectedDates, dateStr, instance) {
                // Optional: validate time
                if (dateStr && !/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/.test(dateStr)) {
                    instance.clear();
                    alert('Please enter a valid time in HH:MM format');
                }
            }
        });

        // Add clear button functionality
        const clearBtn = input.parentElement.querySelector('.clear-time');
        if (clearBtn) {
            clearBtn.addEventListener('click', function(e) {
                e.preventDefault();
                input._flatpickr.clear();
            });
        }
    });

    // Form validation before submit
    const form = document.querySelector('.settings-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Optional: Add any custom validation here
            console.log('Form submitting...');
        });
    }
});
