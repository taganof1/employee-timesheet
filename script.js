const statusCard = document.querySelector('.status-card');
const employeeInfo = document.querySelector('.employee-info');
const employeeIdSpan = document.getElementById('employeeId');
const employeeNameSpan = document.getElementById('employeeName');
const clockInTimeSpan = document.getElementById('clockInTime');

let lastProcessedTime = null; // track last processed clock-in time

// Welcome screen
function showWelcomeScreen() {
    statusCard.classList.remove('success', 'error');
    statusCard.querySelector('.card-icon').className = 'fas fa-id-card card-icon text-primary';
    statusCard.querySelector('.card-title').textContent = 'Ready to Clock In';
    statusCard.querySelector('.card-text').textContent = 'Please tap your RFID card on the reader';
    employeeInfo.classList.remove('show');
}

// Error state if invalid card is used
function showErrorState(message) {
    statusCard.classList.remove('success');
    statusCard.classList.add('error');
    statusCard.querySelector('.card-icon').className = 'fas fa-exclamation-circle card-icon text-danger';
    statusCard.querySelector('.card-title').textContent = 'Access Denied';
    statusCard.querySelector('.card-text').textContent = message;
    employeeInfo.classList.remove('show');

    // Timeout to welcome screen
    setTimeout(showWelcomeScreen, 3000);
}

// Display employee data on card tap
function updateEmployeeInfo(data) {
    console.log('Received data:', data); // Debug log
    
    // Skip if already checked in
    if (lastProcessedTime && data.clockedInAt === lastProcessedTime) {
        console.log('Skipping already processed clock-in:', lastProcessedTime);
        return;
    }

    // Error handling
    if (data.status === 'error') {
        console.log('Showing error state:', data.message);
        showErrorState(data.message);
        return;
    }

    // Success handling
    if (data.uid) {
        console.log('Processing successful clock-in for UID:', data.uid);
        // Last process time update
        lastProcessedTime = data.clockedInAt;

        statusCard.classList.remove('error');
        statusCard.classList.add('success');
        statusCard.querySelector('.card-icon').className = 'fas fa-check-circle card-icon text-success';
        statusCard.querySelector('.card-title').textContent = 'Successfully Clocked In!';
        statusCard.querySelector('.card-text').textContent = 'Your clock-in has been recorded';

        employeeIdSpan.textContent = data.employeeId;
        employeeNameSpan.textContent = data.employeeName;
        clockInTimeSpan.textContent = new Date(data.clockedInAt).toLocaleString();
        
        employeeInfo.classList.add('show');

        // Return to welcome screen
        setTimeout(showWelcomeScreen, 5000);
    } else {
        console.log('No UID in data:', data);
    }
}

// Check for new clock in events
function pollForUpdates() {
    console.log('Polling for updates...');
    fetch('fetch-clockin-data.php')
        .then(response => {
            console.log('Response status:', response.status); // Debug log
            return response.json();
        })
        .then(data => {
            console.log('Fetched data:', data); // Debug log
            updateEmployeeInfo(data);
        })
        .catch(error => {
            console.error('Error fetching data:', error); // Debug log
        })
        .finally(() => {
            setTimeout(pollForUpdates, 1000); // Poll every second
        });
}

// Start polling when the page loads
document.addEventListener('DOMContentLoaded', () => {
    showWelcomeScreen(); // Show welcome screen initially
    pollForUpdates();
}); 